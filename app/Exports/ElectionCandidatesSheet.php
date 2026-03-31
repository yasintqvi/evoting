<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ElectionCandidatesSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private Collection $candidateVotes,
        private int $totalVotes
    ) {}

    public function headings(): array
    {
        return ['نام کاندید', 'کد ملی', 'کل رأی‌ها', 'درصد رأی'];
    }

    public function array(): array
    {
        return $this->candidateVotes->map(function ($item) {
            $candidate = $item['candidate'];
            $fullName = $candidate->user->full_name ?? '';
            $nationalCode = $candidate->user->national_code ?? '';
            $total = (int) ($item['total_votes'] ?? 0);
            $percent = $this->totalVotes > 0 ? round(($total * 100.0) / $this->totalVotes, 2) : 0.0;
            return [$fullName, $nationalCode, $total, $percent];
        })->toArray();
    }

    public function title(): string
    {
        return 'نتایج کاندیداها';
    }
}
