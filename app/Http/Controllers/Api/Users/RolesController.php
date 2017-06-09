<?php

namespace App\Http\Controllers\Api\Users;

use App\Entities\Role;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Transformers\Users\RoleTransformer;

class RolesController extends Controller
{
    use Helpers;

    /**
     * @var
     */
    protected $model;

    /**
     * UsersController constructor.
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
        $this->middleware('permission:List roles')->only('index');
        $this->middleware('permission:List roles')->only('show');
        $this->middleware('permission:Create roles')->only('store');
        $this->middleware('permission:Update roles')->only('update');
        $this->middleware('permission:Delete roles')->only('destroy');
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $paginator = $this->model->with('permissions')
            ->paginate($request->get('limit', config('app.pagination_limit')));
        return $this->response->paginator($paginator, new RoleTransformer());
    }


    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $role = $this->model->with('permissions')->byUuid($id)->firstOrFail();
        return $this->response->item($role, new RoleTransformer());
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $role = $this->model->create($request->all());
        return $this->response->created(url('api/roles/'.$role->uuid));
    }


    /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function update(Request $request, $uuid)
    {
        $role = $this->model->byUuid($uuid)->firstOrFail();
        $rules = [
            'name' => 'required'
        ];
        $this->validate($request, $rules);
        $role->update($request->except('_token'));
        return $this->response->item($role->fresh(), new RoleTransformer());
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return mixed
     */
    public function destroy(Request $request, $uuid)
    {
        $user = $this->model->byUuid($uuid)->firstOrFail();
        $user->delete();
        return $this->response->noContent();
    }
}