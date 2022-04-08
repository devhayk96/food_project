<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkHoursCollection;
use App\Http\Resources\WorkHoursResource;
use App\Models\WorkHours;
use Illuminate\Http\Request;

class WorkHoursController extends Controller
{
    /**
     * Validation rule preparation
     **/
    protected array $constrains = [
        'shop_id' => 'required|exists:poshub_shops,id',
        'day' => 'required|integer|min:0|max:6',
        'type' => 'required|string|in:' . WorkHours::TYPE_OPENING . ',' . WorkHours::TYPE_DELIVERY,
        'opening_hour' => 'required|string|date_format:H:i',
        'closing_hour' => 'required|string|date_format:H:i',
        'is_open' => 'required|boolean'
    ];

    /**
     * Display the list of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return WorkHoursCollection::collection(WorkHours::paginate(1000));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return $this->executeShow($id, WorkHours::class, WorkHoursResource::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(
            WorkHours::class,
            WorkHoursResource::class,
            $request,
            $this->constrains
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request)
    {
        return $this->executeSimpleUpdate(
            WorkHours::class,
            WorkHoursResource::class,
            $request,
            $this->constrains
        );
    }
}
