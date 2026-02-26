<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\Employee\EmployeeServiceInterface;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\EmployeeRequest;
class EmployeeController extends Controller
{
    private EmployeeServiceInterface $employeeService;

    public function __construct(EmployeeServiceInterface $employeeService)
    {
        $this->employeeService = $employeeService;
    }
    
    public function index()
    {
          return response()->json($this->employeeService->list());
    }

    public function store(EmployeeRequest $request)
    {
       
        Log::info('Employee store payload', $request->all());


        try {
            $employee = $this->employeeService->create($request->validated());
            return response()->json($employee, 201);
        } catch (\Throwable $e) {
            Log::error('Employee create failed: '.$e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Create failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        return response()->json($this->employeeService->find($id));
    }

    public function update(EmployeeRequest $request, string $id)
    {
        $employee = $this->employeeService->update($id, $request->validated());
        return response()->json($employee);
    }

    public function destroy(string $id)
    {
        $this->employeeService->delete($id);
        return response()->json(null, 204);
    }
}
