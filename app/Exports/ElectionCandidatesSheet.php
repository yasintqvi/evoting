<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ElectionCandidatesSheet implements FromArray, WithCharts, WithHeadings, WithStyles, WithTitle
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
            $nationalCode = $this->maskNationalCode($candidate->user->nationalcode ?? '');
            $total = (int) ($item['total_votes'] ?? 0);
            $percent = $this->totalVotes > 0 ? round(($total * 100.0) / $this->totalVotes, 2) : 0.0;
            return [$fullName, $nationalCode, $total, $percent];
        })->toArray();
    }

    private function maskNationalCode(string $nationalCode): string
    {
        return mb_strlen($nationalCode) === 10
            ? mb_substr($nationalCode, 0, 3).'***'.mb_substr($nationalCode, 6, 4)
            : '**********';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function charts(): Chart|array
    {
        $rowCount = $this->candidateVotes->count();
        $lastRow = $rowCount + 1;

        if ($rowCount === 0) {
            return [];
        }

        $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'نتایج کاندیداها'!\$A\$2:\$A\${$lastRow}", null, $rowCount)];
        $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'نتایج کاندیداها'!\$C\$2:\$C\${$lastRow}", null, $rowCount)];
        $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'نتایج کاندیداها'!\$A\$2:\$A\${$lastRow}", null, $rowCount)];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values) - 1),
            $labels,
            $categories,
            $values
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

        $chart = new Chart(
            'candidate-votes-chart',
            new Title('مقایسه آرای کاندیداها'),
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('F2');
        $chart->setBottomRightPosition('N20');

        return $chart;
    }

    public function title(): string
    {
        return 'نتایج کاندیداها';
    }
}
