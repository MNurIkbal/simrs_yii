<?php

namespace Doco\laboratorium\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $upload_file;
    public $hasilpemeriksaanlab_id;
    public $pasien_id;
    public $pasienmasukpenunjang_id;
    public $pendaftaran_id;
    public $samplelab_id;

    public function rules()
    {
        return [
            [['upload_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, pdf, doc, docx', 'message' => \Yii::t('fe', '')],
        ];
    }
    
    public function upload()
    {
        $path = \Yii::getAlias('@webroot');
        if ($this->validate()) {
            $this->upload_file->saveAs($path. '/media/input-hasil-lab/' . $this->upload_file->baseName . '.' . $this->upload_file->extension);
            return true;
        } else {
            return false;
        }
    }
}