<?php

namespace App\Exports;

// use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\{FromView, ShouldAutoSize};
// use DOMDocument;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Report implements WithMultipleSheets
{
    use Exportable;

    public function __construct($labels, $dataset){
        $this->labels = $labels;
        $this->dataset = $dataset;
    }

    public function sheets(): array{
        $sheets = [];
        $ids = array_keys($this->dataset);

        // dd($this->labels, $this->dataset, $ids);
        foreach($ids as $id){
            array_push($sheets, new DeviceReport($this->labels, $this->dataset[$id]));
        }

        return $sheets;
    }

    // public function view(): View
    // {
    //     return view('reports.exports.report', [
    //         'labels' => $this->labels,
    //         'dataset' => $this->dataset
    //     ]);
    // }
}