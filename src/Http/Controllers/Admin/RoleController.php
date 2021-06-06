<?php

namespace Porteiro\Http\Controllers\Admin;

use Porteiro\Models\Role;
// use Facilitador\Services\FacilitadorService;
use Illuminate\Http\Request;
// use Support\Http\Controllers\RiCa\Manager\RepositoryController;
// use Support\Services\ModelService;
// use Support\Services\RepositoryService;
use Pedreiro\CrudController;

class RoleController extends Controller
{
    use CrudController;

    public function __construct(Role $model)
    {
        $this->model = $model;

        // Configure Controller
        $this->withPagination = 15;

        // Call Parent
        parent::__construct();
    }

    // @todo Antigo removi, agora usa Pedreiro
    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index(Request $request)
    // {
    //     $facilitadorService = app(FacilitadorService::class);
    //     $repositoryService = new RepositoryService(
    //         new ModelService(
    //             Role::class
    //         )
    //     );
    //     $baseControl = new RepositoryController($facilitadorService, $repositoryService);

    //     return $baseControl->index($request);

    //     // $roles = Role::all();

    //     // return view('porteiro::admin.roles.index', compact('roles'));
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create(Request $request)
    // {
    //     return view('porteiro::admin.roles.create');
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param \Illuminate\Http\Request $request
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     Role::create(['name' => $request->name]);

    //     return redirect('roles');
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param int $id
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit(Request $request, $id)
    // {
    //     $role = Role::findOrFail($id);

    //     return view('porteiro::admin.roles.edit', compact('role'));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param \Illuminate\Http\Request $request
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request)
    // {
    //     $role = Role::findOrFail($request->role_id);

    //     $role->name = $request->name;

    //     $role->update();

    //     return redirect('roles');
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param int $id
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(Request $request, $id)
    // {
    //     $role = Role::findOrFail($id);

    //     $role->delete();

    //     return redirect('roles');
    // }
}
