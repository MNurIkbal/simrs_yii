<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\models;

use Yii;
use yii\web\UploadedFile;

class ImportObatForm extends \yii\base\Model
{
	public $attachment;

	public function rules()
    {
        return [
            [['attachment'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx'],
        ];
    }

	public function upload()
    {
    	if (!($this->attachment instanceof UploadedFile)){
            $this->addError('attachment', 'Bukan Instance File');
            return false;
    	}
        if (!$this->validate()) {
            $this->addError('attachment', 'Validasi Gagal');
            return false;
        }
        $root_path = \Yii::getAlias('@webroot');
        $uploadPath = $root_path . '/uploads/excel';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
    	$inputFileName = $this->attachment->baseName.'.'.$this->attachment->extension;
        $this->attachment->saveAs($uploadPath.'/'.$inputFileName);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($uploadPath.'/'.$inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        $this->attachment = $sheetData;
        return $this->attachment;
	       
    }
}