<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Employee::query()->latest()->paginate($perPage);
    }

    public function all(): Collection
    {
        return Employee::query()->latest()->get();
    }

    public function findOrFail(string $id): Employee
    {
        return Employee::query()->findOrFail($id);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->refresh();
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}