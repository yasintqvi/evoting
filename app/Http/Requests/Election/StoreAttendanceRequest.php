<?php

namespace App\Http\Requests\Election;

use App\DTOs\Election\CreateAttendanceDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attendance.*' => ['required', 'array'],
            'attendance.*.status' => ['nullable', 'in:0,1'],
            'attendance.*.attorney_id' => ['nullable', 'numeric'],
        ];
    }

    public function toDto(): CreateAttendanceDto
    {
        $attendanceData = $this->validated('attendance');

        $statuses = [];
        $attorneys = [];

        foreach ($attendanceData ?? [] as $participantId => $data) {

            $statuses[] = [
                'participant_id' => $participantId,
                'status' => (bool) isset($data['status']) ? $data['status'] : false,
            ];

            // if (!empty($data['attorney_id'])) {
            $attorneys[] = [
                'participant_id' => $participantId,
                'attorney_id' => $data['attorney_id'],
            ];
            // }
        }

        return new CreateAttendanceDto($statuses, $attorneys);
    }
}
