<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupCollection;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Resources\GroupResource;

class GroupController extends Controller
{
    /**
     * Store Validation rule preparation
     **/
    protected array $createConstrains = [
        'code' => 'required|unique:poshub_groups|min:1|max:32',
        'name' => 'required|min:3|max:256',
        'description' => 'nullable|sometimes|max:4096'
    ];

    /**
     * Group List.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
        return GroupCollection::collection(Group::paginate(1000));
    }

    /**
     * Group details for a particular ID.
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function show($id)
    {
        return $this->executeShow($id, Group::class, GroupResource::class);
    }

    /**
     * Group store.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        return $this->executeSimpleStore(Group::class, GroupResource::class, $request, $this->createConstrains);
    }
}
