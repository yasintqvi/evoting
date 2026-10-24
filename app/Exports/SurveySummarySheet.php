<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveySummarySheet implements FromArray, WithStyles, WithTitle
{
    public function __construct(
        private string $groupTitle,
        private string $eventTitle,
        private string $surveyTitle,
        private bool $isAnonymous,
        private bool $isWeighted,
        private int $questionCount,
        private int $responseCount,
        private string $generatedAt
    ) {}

    public function array(): array
    {
        return [
            ['عنوان گروه', $this->groupTitle],
            ['عنوان رویداد', $this->eventTitle],
            ['عنوان نظرسنجی', $this->surveyTitle],
            ['ناشناس بودن', $this->isAnonymous ? 'بله' : 'خیر'],
            ['تأثیر تعداد سهام', $this->isWeighted ? 'مؤثر است' : 'مؤثر نیست'],
            ['تعداد سؤالات', $this->questionCount],
            ['تعداد پاسخ‌دهندگان', $this->responseCount],
            ['تاریخ تهیه گزارش', $this->generatedAt],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A' => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'خلاصه';
    }
}
