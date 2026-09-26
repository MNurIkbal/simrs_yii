<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\MigrasiAdjustment;

use Yii;
use yii\base\Action;
use app\modules\v1\models\ObatAlkes;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TemplateAdjustmentOaAction extends Action {
    public function run() {
        $spreadsheet = new Spreadsheet();
        $rowArray = ['No', 'Kode Obat', 'Nama Obat', 'Tanggal Kadaluarsa', 'Satuan', 'Qty'];
        $styleArray = [
            'font' => [
                'bold' => true,
            ]
        ];
        $spreadsheet->getActiveSheet()->fromArray($rowArray, NULL, 'A1');
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        die;
    }
}