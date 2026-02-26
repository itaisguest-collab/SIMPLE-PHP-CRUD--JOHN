<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'gender'        => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
        ];
    }
}