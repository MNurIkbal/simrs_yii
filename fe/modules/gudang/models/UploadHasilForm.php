<?php

namespace Doco\gudang\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadHasilForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $upload_file;

    public function rules()
    {
        return [
            [['upload_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, pdf, xlsx, xls', 'message' => \Yii::t('fe', '')],
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