<?php

namespace App\Exports;

use PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class PDFExport
{
    public function __construct($data, $filename, $type){
        $this->data = $data;
        $this->filename = $filename;
        $this->type = $type;
    }

    public function billing(){
        $pdf = PDF::loadView('billings.email', ['billing' => $this->data, 'width' => "100%"]);
        $pdf->setPaper('a4', 'Portrait');
        return $pdf->download($this->filename . '.pdf');
    }

    public function report(){
        $settings = Setting::pluck('value', 'name');

        // CREATE TEMP IF NOT EXISTS;
        $path = "uploads/temp";
        is_dir($path) ? true : mkdir($path);

        $oMerger = PDFMerger::init();

        $path = "uploads/temp/$this->filename.pdf";
        $pdf = PDF::loadView('exports.' . $this->type, ['data' => $this->data, 'settings' => $settings]);
        $pdf->setPaper('a4', 'Portrait');
        $pdf->setWarnings(false)->save($path);
        $oMerger->addPDF($path);

        if(is_array($this->data->file)){
            $files = json_decode($this->data->file);
            foreach ($files as $file) {
                $oMerger->addPDF(public_path($file));
            }
        }
        // else{
        //     $oMerger->addPDF(public_path($this->data->file));
        // }

        $oMerger->merge();
        $oMerger->setFileName($this->filename . '.pdf');
        $oMerger->download();
    }

    public function invoice(){
        $settings = Setting::pluck('value', 'name');

        // CREATE TEMP IF NOT EXISTS;
        // $path = "uploads/invoice ";
        // is_dir($path) ? true : mkdir($path);

        // $path = "uploads/invoice/$this->filename.pdf";
        $pdf = PDF::loadView('exports.' . $this->type, ['data' => $this->data, 'settings' => $settings]);
        $pdf->setPaper('a4', 'Portrait');
        return $pdf->download($this->filename . '.pdf');
    }
}