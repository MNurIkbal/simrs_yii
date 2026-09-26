<?php
namespace app\modules\pengadaan\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    public $upload_file;

    public function rules()
    {
        return [
            [
                ['upload_file'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => 'xls, xlsx',
                'message' => ''
            ],
        ];
    }
    
    public function upload()
    {
        $path = \Yii::getAlias('@webroot');
        if ($this->validate()) {
            $this->upload_file->saveAs($path. '/media/kontrak-supplier/' . $this->upload_file->baseName . '.' . $this->upload_file->extension);
            return true;
        } else {
            return false;
        }
    }
}