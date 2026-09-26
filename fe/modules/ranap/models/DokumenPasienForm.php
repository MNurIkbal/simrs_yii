<?php

namespace app\modules\ranap\models;

use Yii;
use yii\web\UploadedFile;

class DokumenPasienForm extends \yii\base\Model
{
	public $attachment;
    public $dokumen_id;
    public $filename;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $path;

	public function rules()
    {
        return [
            [['attachment'], 'file', 'skipOnEmpty' => false,
            // 'size' =>'20000000' 
            // 'extensions' => 'xlsx'
        ],
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
        $uploadPath = $root_path . '/uploads/dokumenpasien';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
    	$inputFileName = $this->attachment->baseName.'.'.$this->attachment->extension;
        $this->attachment->saveAs($uploadPath.'/'.$inputFileName);
	    
	    return $this->attachment;
	       
    }
}