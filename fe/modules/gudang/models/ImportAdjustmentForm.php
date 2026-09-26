<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\models;

use Yii;
use yii\web\UploadedFile;

class ImportAdjustmentForm extends \yii\base\Model
{

    public $attachment;
    public $ruangan_adjusmen_id;
    public $ruangan_id;
    public $jenis_adjusmen;
    public $tgl_adjusmen;

    public function rules()
    {
        return [
            [['ruangan_adjusmen_id', 'jenis_adjusmen', 'tgl_adjusmen', 'attachment'], 'safe']
        ];
    }

    public function upload()
    {
        if ( !($this->attachment instanceof UploadedFile) ) {
            $this->addError('attachment', 'Bukan File');
            return "bukan file";
        }

        if (!$this->validate()) {
            $this->addError('attachment', 'File yang anda masukkan salah');
            return "file salah";
        }

        $root_path = \Yii::getAlias("@webroot");
        $upload_path = $root_path. "/uploads/excel";
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }


        $filename = $this->attachment->baseName. "." .$this->attachment->extension;
        $this->attachment->saveAs($upload_path. "/" .$filename);

        $spreadsheet_reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet_reader->setReadDataOnly(true);

        $spreadsheet = $spreadsheet_reader->load($upload_path. "/" .$filename);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $header = [
            'no',
            'obatalkes_kode',
            'obatalkes_nama',
            'tglkadaluarsa',
            'satuan_nama',
            'qty',
            'harganetto'
        ];

        unset($data[0]);

        $mapping = [];
        foreach ($data as $row) {
            $newRow = [];
            if (is_array($row)) {
                foreach ($row as $index => $column) {
                    $label = $header[$index];
                    $newRow[$label] = trim($column);
                }
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($newRow['tglkadaluarsa']);
                $newRow['tglkadaluarsa']  = date('Y-m-d', $date);
                $mapping[] = $newRow;
            }
        }

        $this->attachment = $mapping;

        return $mapping;
    }
}