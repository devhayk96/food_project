<?php

namespace App\Http\Controllers;

use App\Models\AccountSettings;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'form' => AccountSettings::orderBy('id','desc')->get()->first()
        ],Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $accountSettings = AccountSettings::firstOrNew();
            $accountSettings->background_color = $request->input('background_color');
            $accountSettings->button_color = $request->input('button_color');
            $accountSettings->icon_color = $request->input('icon_color');
            $accountSettings->text_color = $request->input('text_color');
            $accountSettings->header_background_color = $request->input('header_background_color');
            $accountSettings->tabs_header_color = $request->input('tabs_header_color');
            $accountSettings->link_color = $request->input('link_color');
            $accountSettings->logo_width = $request->input('logo_width');
            $accountSettings->logo_height = $request->input('logo_height');
            $accountSettings->editor_data = $request->input('editor_data');
            $accountSettings->piggy_checkbox = $request->input('piggy_checkbox');
            $accountSettings->piggy_secret_token = $request->input('piggy_secret_token');
            $accountSettings->piggy_client_id = $request->input('piggy_client_id');
            $accountSettings->piggy_shop_id = $request->input('piggy_shop_id');

            if( $request->hasFile('background_image') ){
                $backgroundImage = $request->file('background_image');
                $path = 'account-background' .'.'. $backgroundImage->extension();
                $backgroundImage->move(public_path('images/uploads'), $path);
                $accountSettings->background_image = '/images/uploads/'.$path;
            }else{
                $accountSettings->background_image = $request->input('background_image');
            }

            if( $request->hasFile('logo') ){
                $logo = $request->file('logo');
                $data = getimagesize($logo);
                $width = $data[0];
                $height = $data[1];

                $accountSettings->logo_original_width = $width;
                $accountSettings->logo_original_height = $height;

                $path = 'account-logo' .'.'. $logo->extension();
                $logo->move(public_path('images/uploads'), $path);
                $accountSettings->logo = '/images/uploads/'.$path;
            }else{
                $accountSettings->logo_original_width = '';
                $accountSettings->logo_original_height = '';
                $accountSettings->logo = $request->input('logo');
            }

            $accountSettings->save();

            return response()->json([
                'message' => 'Settings successfully updated!',
                'accountSetting' => $accountSettings
            ],Response::HTTP_OK);
        }catch (Exception $exception){
            return response()->json([
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function imageUpload(Request $request)
    {
        $accountSettings = AccountSettings::firstOrNew();
        if( $request->file() ){
            $image = $request->file()['file'];
            $path = 'content_image' .'.'. $image->extension();
            $image->move(public_path('images/uploads'), $path);
            $accountSettings->content_image = '/images/uploads/'.$path;
        }else{
            $accountSettings->content_image = $request->input('');
        }
        $accountSettings->save();

    }
}
