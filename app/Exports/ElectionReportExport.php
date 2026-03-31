<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ElectionReportExport implements WithMultipleSheets
{
    public function __construct(
        private string $groupTitle,
        private string $eventTitle,
        private string $electionTitle,
        private int $totalVotes,
        private int $totalParticipants,
        private int $totalCandidates,
        private Collection $candidateVotes
    ) {}

    public function sheets(): array
    {
        return [
            new ElectionSummarySheet(
                $this->groupTitle,
                $this->eventTitle,
                $this->electionTitle,
                $this->totalVotes,
                $this->totalParticipants,
                $this->totalCandidates
            ),
            new ElectionCandidatesSheet($this->candidateVotes, $this->totalVotes),
        ];
    }
}
