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

class SurveyQuestionSheet implements FromArray, WithCharts, WithHeadings, WithStyles, WithTitle
{
    private string $sheetTitle;

    public function __construct(
        private string $questionTitle,
        private Collection $options,
        private bool $isWeighted,
        private int $index
    ) {
        $safeTitle = preg_replace('/[\[\]\*\/\\\\\?:]/u', ' ', $this->questionTitle) ?: 'سوال';
        $safeTitle = trim(mb_substr($safeTitle, 0, 22));
        $this->sheetTitle = 'س'.$this->index.' - '.$safeTitle;
        $this->sheetTitle = mb_substr($this->sheetTitle, 0, 31);
    }

    public function headings(): array
    {
        return [
            [$this->questionTitle, '', ''],
            ['گزینه', $this->isWeighted ? 'وزن سهام' : 'تعداد پاسخ', 'درصد'],
        ];
    }

    public function array(): array
    {
        return $this->options->map(function ($row) {
            $label = $row->option_title ?? 'پاسخ متنی';
            $value = $this->isWeighted ? round((float) ($row->count ?? 0), 2) : (int) ($row->count ?? 0);
            $percent = (float) ($row->percent ?? 0);

            return [$label, $value, $percent];
        })->toArray();
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->setRightToLeft(true);
        $sheet->mergeCells('A1:C1');

        return [
            1 => ['font' => ['bold' => true, 'size' => 13]],
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function charts(): Chart|array
    {
        $rowCount = $this->options->count();

        if ($rowCount === 0) {
            return [];
        }

        // ردیف ۱ عنوان سؤال، ردیف ۲ سرستون‌ها، داده از ردیف ۳ شروع می‌شود.
        $firstDataRow = 3;
        $lastDataRow = $firstDataRow + $rowCount - 1;
        $sheetRef = "'{$this->sheetTitle}'";

        $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$sheetRef}!\$B\$2", null, 1)];
        $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "{$sheetRef}!\$B\${$firstDataRow}:\$B\${$lastDataRow}", null, $rowCount)];
        $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "{$sheetRef}!\$A\${$firstDataRow}:\$A\${$lastDataRow}", null, $rowCount)];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($values) - 1),
            $labels,
            $categories,
            $values
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

        $chart = new Chart(
            'question-'.$this->index.'-chart',
            new Title(mb_substr($this->questionTitle, 0, 60)),
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('E2');
        $chart->setBottomRightPosition('M20');

        return $chart;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }
}
