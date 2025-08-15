<?php

namespace App\Excel;

use App\Helpers\AppHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PembelianExcel extends ReportExcel
{
    private $spreadsheet;
    private $periode;
    private $data;
    private $title;

    public function __construct($periode, $data, $title)
    {
        $this->periode = $periode;
        $this->data = $data;
        $this->title = $title;
        $this->view();
    }

    public function view()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);

        $sheet->setCellValue('A1', $this->title);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1:H3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:H1')->getFont()
            ->setBold(true)
            ->setSize(15);
        $row = 3;

        $sheet->setCellValue('A' . $row, 'NO');
        $sheet->setCellValue('B' . $row, 'KODE');
        $sheet->setCellValue('C' . $row, 'TANGGAL');
        $sheet->setCellValue('D' . $row, 'VENDOR');
        $sheet->setCellValue('E' . $row, 'TOTAL');
        $sheet->setCellValue('F' . $row, 'PPN');
        $sheet->setCellValue('G' . $row, 'GRAND TOTAL');
        $sheet->setCellValue('H' . $row, 'PEMBAYARAN');

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(10);

        $sheet->getStyle('A' . $row . ':' . 'H' . $row)->applyFromArray($this->fontBold);
        $sheet->getStyle('A' . $row . ':' . 'H' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('cccccc');
        $sheet->getStyle('A' . $row . ':' . 'H' . $row)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $row++;
        $no = 1;
        foreach ($this->data as $key => $value) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $value->kode);
            $sheet->setCellValue('C' . $row, AppHelper::tanggal_indo($value->tanggal));
            $sheet->setCellValue('D' . $row, $value->vendor->nama);

            $sheet->setCellValue('E' . $row, $value->total);
            $sheet->getCell('E' . $row)->getCalculatedValue();
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('\Rp\. #,##0');

            $sheet->setCellValue('F' . $row, $value->ppn);
            $sheet->getCell('F' . $row)->getCalculatedValue();
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('\Rp\. #,##0');

            $sheet->setCellValue('G' . $row, $value->grand_total);
            $sheet->getCell('G' . $row)->getCalculatedValue();
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('\Rp\. #,##0');

            $sheet->setCellValue('H' . $row, $value->pembayaran);

            $sheet->getStyle('A' . $row . ':' . 'H' . $row)
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;
        }

        $this->spreadsheet = $spreadsheet;
    }

    public function download($filename = "")
    {
        $writer = new Xlsx($this->spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        $writer->save("php://output");
    }
}
