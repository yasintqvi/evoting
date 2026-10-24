<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SurveyExport implements WithMultipleSheets
{
    public function __construct(
        private string $groupTitle,
        private string $eventTitle,
        private string $surveyTitle,
        private bool $isAnonymous,
        private bool $isWeighted,
        private int $responseCount,
        private string $generatedAt,
        private Collection $stats
    ) {}

    public function sheets(): array
    {
        $sheets = [
            new SurveySummarySheet(
                $this->groupTitle,
                $this->eventTitle,
                $this->surveyTitle,
                $this->isAnonymous,
                $this->isWeighted,
                $this->stats->count(),
                $this->responseCount,
                $this->generatedAt
            ),
        ];

        $index = 1;
        foreach ($this->stats as $items) {
            $questionTitle = $items->first()->question_title ?? '—';
            $sheets[] = new SurveyQuestionSheet($questionTitle, $items->values(), $this->isWeighted, $index);
            $index++;
        }

        return $sheets;
    }
}
