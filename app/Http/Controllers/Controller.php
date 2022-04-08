<?php

namespace App\Http\Controllers;

use App\Locale\PoshubLocale;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected Request $currentRequest;

    protected User $currentUser;

    protected array $errorMessages = [];

    protected PoshubLocale $locale;

    public function __construct()
    {
        $this->locale = new PoshubLocale();
    }

    protected function executeShow($id, $modelName, $resourceName)
    {
        if (empty($id)) {
            return response()->json(['error' => 'Id is not defined'], 400);
        }

        $entity = $modelName::find($id);

        if (empty($entity)) {
            return response()->json(['error' => 'Id not found: ' . $id], 403);
        }

        return new $resourceName($entity);
    }

    protected function validateRequest(Request $request, array $rules, $messages = array()): ?array
    {
        try {
            $this->currentUser = $request->user();
            return $request->validate($rules, $messages);
        } catch (ValidationException $exception) {
            $this->errorMessages = $exception->errors();
        }
        return null;
    }

    protected function decorateCreateArrayWithUser(array $create): array
    {
        return array_merge(
            $create,
            [
                'created_by_id' => $this->currentUser->id,
                'updated_by_id' => $this->currentUser->id
            ]
        );
    }

    protected function decorateUpdateArrayWithUser(array $create): array
    {
        return array_merge(
            $create,
            ['updated_by_id' => $this->currentUser->id]
        );
    }

    protected function createAndReturn(string $modelName, string $resourceName, array $values)
    {
        try {
            $values = $this->decorateCreateArrayWithUser($values);
            $entity = $modelName::create($values);
            return new $resourceName($entity);
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    protected function takeDataWherePrefix(array &$values, string $prefix): array
    {
        $carry = [];
        foreach ($values as $key => $value) {
            if (str_contains($key, $prefix)) {
                $newKey = str_replace($prefix, "", $key);
                $carry[$newKey] = $value;
                unset($values[$key]);
            }
        }
        return $carry;
    }

    protected function executeSimpleStore(
        string $modelName,
        string $resourceName,
        Request $request,
        array $rules,
        array $messages = []
    ): JsonResponse
    {
        $values = $this->validateRequest($request, $rules, $messages);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 422);
        }
//        need to check this function
         return new JsonResponse($this->createAndReturn($modelName, $resourceName, $values));
//        return new $this->createAndReturn($modelName, $resourceName, $values);
    }

    protected function executeSimpleUpdate(
        string $modelName,
        string $resourceName,
        Request $request,
        array $rules
    ): JsonResponse
    {
        $values = $this->validateRequest($request, $rules);

        if (is_null($values)) {
            return response()->json(['errors' => $this->errorMessages], 400);
        }

        try {
            $model = $modelName::findOrFail($values['id']);
            unset($values['id']);
            foreach ($values as $key => $value) {
                $model->$key = $value;
            }
            $model->updated_by_id = $this->currentUser->id;
            $model->save();
            $model->refresh();

            return new JsonResponse( new $resourceName($model));
        } catch (\Throwable $exception) {
            return response()->json(['errors' => $exception->getMessage()], 500);
        }
    }


    protected function storeSyncRequestData(Request $request, $fileName)
    {
        $fileData = Storage::disk('public')->exists($fileName) ? json_decode(Storage::disk('public')->get($fileName), true) : [];
        $insertData = [
            'referer' => $request->header('referer'),
            'url' => $request->url(),
            'date' => now(),
            'parameters' => json_encode($request->all())
        ];
        array_push($fileData, $insertData);
        Storage::disk('public')->put($fileName, json_encode($fileData, JSON_PRETTY_PRINT), null);
    }
}
