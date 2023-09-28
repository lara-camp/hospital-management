<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Traits\HttpResponses;
use App\UseCases\Department\FetchDepartmentAction;
use App\UseCases\Department\DeleteDepartmentAction;
use App\UseCases\Department\StoreDepartmentAction;
use App\UseCases\Department\UpdateDepartmentAction;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use HttpResponses;
    public function departments ()
    {
        $result = (new FetchDepartmentAction)();
        return response()->json([
            'data' => DepartmentResource::collection($result['data']),
            'meta' => $result['meta'],
        ]);
    }

    public function create(Request $request)
    {
        (new StoreDepartmentAction)($request->all());
        return $this->success('Successfully inserted.', null, 201);
    }

    public function update(Request $request , Department $department)
    {
        $request->validate([
            'name' => 'required|min:2|max:100'
        ]);
        $update = (new UpdateDepartmentAction)($request->all(),$department);
        return $this->success('Successfully updated.', $update);
    }

    public function delete(Department $department)
    {
        (new DeleteDepartmentAction)($department);
        return $this->success('Successfully deleted.');
    }

    public function searchHospitalByDepartment(Request $request)
    {
        $department = $request->department;
        $hospitals = Department::where('name', 'LIKE', '%'. $department .'%')->first();

        return $this->success('Fetched hospitals by department.', ['hospitals' => $hospitals->doctors]);
    }
}
