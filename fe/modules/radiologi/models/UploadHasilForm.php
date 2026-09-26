<?php

namespace Doco\radiologi\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadHasilForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $upload_file;
    public $pegawairad_id;
    public $pasien_id;
    public $pasienmasukpenunjang_id;
    public $pendaftaran_id;
    public $samplelab_id;

    public function rules()
    {
        return [
            [['pegawairad_id'], 'required'],
            [['upload_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, pdf, docx'],
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
    
    public function attributeLabels()
    {
        return [
            'pegawairad_id' => \Yii::t('fe', 'Petugas radiologi'),
        ];
    }
}