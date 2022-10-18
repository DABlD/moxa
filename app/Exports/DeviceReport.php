<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\{FromView, WithCharts, WithEvents, ShouldAutoSize};
use Maatwebsite\Excel\Events\{AfterSheet};

use PhpOffice\PhpSpreadsheet\Chart\{Chart, DataSeries, DataSeriesValues, Layout, Legend, PlotArea, Title};

class DeviceReport implements FromView, WithCharts, WithEvents, ShouldAutoSize
{
    public function __construct($labels, $dataset){
        $this->labels = $labels;
        $this->dataset = $dataset;
    }

    public function view(): View
    {
        return view('reports.exports.report', [
            'labels' => $this->labels,
            'dataset' => $this->dataset,
        ]);
    }

    public function charts()
    {
        $label      = [new DataSeriesValues('String', $this->dataset['label'] . '!V21', null, 1)];
        $categories = [new DataSeriesValues('String', $this->dataset['label'] . '!U21:U' . 21 + sizeof($this->labels), null, 4)];
        $values     = [new DataSeriesValues('Number', $this->dataset['label'] . '!V21:V' . 21 + sizeof($this->labels), null, 4)];

        $series = new DataSeries(DataSeries::TYPE_LINECHART, DataSeries::GROUPING_STANDARD,
            range(0, \count($values) - 1), $label, $categories, $values);
        $plot   = new PlotArea(null, [$series]);

        $legend = new Legend();
        $chart  = new Chart('chart name', new Title($this->dataset['label']), $legend, $plot);

        $chart->setTopLeftPosition('A1');
        $chart->setBottomRightPosition('L20');

        return $chart;
    }

    public function registerEvents(): array{
        $labels = $this->labels;
        $dataset = $this->dataset;

        return [
            AfterSheet::class => function(AfterSheet $event) use($dataset, $labels) {
                $event->sheet->getDelegate()->setTitle($dataset['label'], false);

                $event->sheet->getDelegate()->getStyle('A21:G' . 21 + sizeof($this->labels))->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]
                ]);
            }
        ];
    }
}