<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Database\Eloquent\Collection;

class EmployeeService
{
    public function __construct(
        private readonly EmployeeRepository $employees
    ) {}

    public function list(): Collection
    {
        return $this->employees->all();
    }

    public function create(array $data): Employee
    {
        return $this->employees->create($data);
    }

    public function find(string $id): Employee
    {
        return $this->employees->findOrFail($id);
    }

    public function update(string $id, array $data): Employee
    {
        $employee = $this->find($id);

        return $this->employees->update($employee, $data);
    }

    public function delete(string $id): void
    {
        $employee = $this->find($id);
        $this->employees->delete($employee);
    }
}