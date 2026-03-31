<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ElectionSummarySheet implements FromArray, WithTitle
{
    public function __construct(
        private string $groupTitle,
        private string $eventTitle,
        private string $electionTitle,
        private int $totalVotes,
        private int $totalParticipants,
        private int $totalCandidates
    ) {}

    public function array(): array
    {
        return [
            ['عنوان گروه', $this->groupTitle],
            ['عنوان رویداد', $this->eventTitle],
            ['عنوان انتخابات', $this->electionTitle],
            ['تعداد کاندیدا', $this->totalCandidates],
            ['تعداد رأی‌دهندگان', $this->totalParticipants],
            ['کل رأی‌ها', $this->totalVotes],
        ];
    }

    public function title(): string
    {
        return 'خلاصه';
    }
}
