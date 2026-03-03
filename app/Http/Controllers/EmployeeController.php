<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Services\Employee\EmployeeService;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}
    
    public function index()
    {
        return EmployeeResource::collection($this->employeeService->list());
    }

    public function store(EmployeeRequest $request)
    {
        $employee = $this->employeeService->create($request->validated());

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id)
    {
        return new EmployeeResource($this->employeeService->find($id));
    }

    public function update(EmployeeRequest $request, string $id)
    {
        $employee = $this->employeeService->update($id, $request->validated());
        return new EmployeeResource($employee);
    }

    public function destroy(string $id)
    {
        $this->employeeService->delete($id);
        return response()->noContent();
    }
}
