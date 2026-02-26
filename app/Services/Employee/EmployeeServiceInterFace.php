<?php

namespace App\Services\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeServiceInterface
{
    public function list(): Collection;
    public function create(array $data): Employee;
    public function find(string $id): Employee;
    public function update(string $id, array $data): Employee;
    public function delete(string $id): bool;
}


