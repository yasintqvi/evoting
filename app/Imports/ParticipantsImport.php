<?php

namespace App\Imports;

use App\Models\Election;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantsImport implements ToCollection, WithHeadingRow
{
    protected $groupId;

    protected $election;

    protected $participants = [];

    public function __construct($groupId, $electionId)
    {
        $this->groupId = $groupId;
        $this->election = Election::findOrFail($electionId);
    }

    public function collection(Collection $rows)
    {
        $totalNormalStock = 0;
        $totalPreferedStock = 0;
        $sumTotal = 0;
        $this->participants = [];
        $uniquePhones = [];

        foreach ($rows as $row) {
            if ($row->filter()->isEmpty()) {
                continue;
            }

            $row = $row->toArray();

            if (!isset($row['phone']) || empty($row['phone'])) {
                throw new \Exception('تکمیل گزینه تلفن الزامی است.');
            }

            $phone = trim($row['phone']);

            if (in_array($phone, $uniquePhones)) {
                throw new \Exception("شماره تلفن '$phone' در فایل اکسل تکراری است.");
            }

            $uniquePhones[] = $phone;

            $validator = Validator::make($row, [
                'phone' => [
                    'required',
                    'exists:users,phone',
                    function ($attribute, $value, $fail) {
                        if (
                            Participant::where('event_id', $this->election->event_id)->whereHas('user', function ($query) use ($value) {
                                $query->where('phone', $value);
                            })->exists()
                        ) {
                            $fail('این شماره قبلاً در این انتخابات ثبت شده است.');
                        }
                    },
                ],
                'normal_stock_count' => ['required', 'integer', 'min:0'],
                'prefered_stock_count' => ['required', 'integer', 'min:0'],
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $user = User::where('phone', $phone)->first();

            if ($user) {
                $this->participants[] = [
                    'user_id' => $user->id,
                    'event_id' => $this->election->event_id,
                    'normal_stock_count' => $row['normal_stock_count'],
                    'prefered_stock_count' => $row['prefered_stock_count'],
                ];

                $totalNormalStock += $row['normal_stock_count'];
                $totalPreferedStock += $row['prefered_stock_count'];
                $sumTotal = $totalNormalStock + $totalPreferedStock;
            }
        }
        if ($sumTotal > $this->election->normal_stock_count + $this->election->prefered_stock_count) {
            throw new \Exception('تعداد سهام عادی وارد شده با تعداد کل سهام عادی برابر نیست.');
        }

        if ($sumTotal < $this->election->normal_stock_count + $this->election->prefered_stock_count) {
            throw new \Exception('تعداد سهام عادی وارد شده با تعداد کل سهام عادی برابر نیست.');
        }

        if ($totalNormalStock > $this->election->normal_stock_count) {
            throw new \Exception("تعداد سهام عادی وارد شده ($totalNormalStock) بیشتر از سهام کل ({$this->election->normal_stock_count}) است.");
        }

        if ($totalPreferedStock > $this->election->prefered_stock_count) {
            throw new \Exception("تعداد سهام ممتاز وارد شده ($totalPreferedStock) بیشتر از سهام کل ({$this->election->prefered_stock_count}) است.");
        }

        if (count($this->participants) < 3) {
            throw new \Exception('حداقل ۳ گروه‌کننده باید ثبت شوند. تعداد ثبت‌شده: ' . count($this->participants));
        }

        foreach ($this->participants as $participant) {
            Participant::create($participant);
        }
    }
}
