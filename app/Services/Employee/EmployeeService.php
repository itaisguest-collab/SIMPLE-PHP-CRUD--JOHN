<?php

namespace App\Services\Employee;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class EmployeeService implements EmployeeServiceInterface
{
    public function list(): Collection
    {
        return Employee::all();
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function find(string $id): Employee
    {
        return Employee::findOrFail($id);
    }

    public function update(string $id, array $data): Employee
    {
        $employee = $this->find($id);
        $employee->update($data);
        return $employee;
    }

    public function delete(string $id): bool
    {
        $employee = $this->find($id);
        return $employee->delete();
    }
}