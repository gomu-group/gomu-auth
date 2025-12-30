<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Gomu\Auth\Models\Employee;
use Gomu\Auth\Http\Resources\EmployeeResource;

final class EmployeeController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Employee::with(['user', 'department', 'jobLevel', 'jobPosition']);

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Search by name or NIP
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate($request->get('per_page', 15));

        return EmployeeResource::collection($employees);
    }

    public function store(Request $request): JsonResource
    {
        $validated = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id', 'unique:employees'],
            'nip' => ['required', 'string', 'unique:employees'],
            'nik' => ['nullable', 'string', 'max:25', 'unique:employees'],
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['nullable', 'in:M,F'],
            'date_of_birth' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'personal_email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'district' => ['nullable', 'string', 'max:100'],
            'village' => ['nullable', 'string', 'max:100'],
            'join_date' => ['required', 'date'],
            'termination_date' => ['nullable', 'date'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,id'],
            'job_level_id' => ['nullable', 'uuid', 'exists:job_levels,id'],
            'job_position_id' => ['nullable', 'uuid', 'exists:job_positions,id'],
        ]);

        $employee = Employee::create($validated);

        return EmployeeResource::make($employee->load(['user', 'department', 'jobLevel', 'jobPosition']));
    }

    public function show($employeeId): JsonResource
    {
        $employee = Employee::findOrFail($employeeId);
        return EmployeeResource::make($employee->load(['user', 'department', 'jobLevel', 'jobPosition']));
    }

    public function update(Request $request, $employeeId): JsonResource
    {
        $employee = Employee::findOrFail($employeeId);

        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'unique:employees,nip,' . $employee->id],
            'nik' => ['nullable', 'string', 'max:25', 'unique:employees,nik,' . $employee->id],
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['nullable', 'in:M,F'],
            'date_of_birth' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'personal_email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'district' => ['nullable', 'string', 'max:100'],
            'village' => ['nullable', 'string', 'max:100'],
            'join_date' => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,id'],
            'job_level_id' => ['nullable', 'uuid', 'exists:job_levels,id'],
            'job_position_id' => ['nullable', 'uuid', 'exists:job_positions,id'],
        ]);

        $employee->update($validated);

        return EmployeeResource::make($employee->fresh(['user', 'department', 'jobLevel', 'jobPosition']));
    }

    public function destroy($employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}