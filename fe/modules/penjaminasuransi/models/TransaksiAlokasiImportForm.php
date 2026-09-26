<?php

namespace Doco\penjaminasuransi\models;

use app\components\DocoConstants;
use yii\web\UploadedFile;

class TransaksiAlokasiImportForm extends \yii\base\Model
{
    public $upload_file;

    public function rules()
    {
        return [
            [
                ['upload_file'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => 'xls,xlsx',
                'checkExtensionByMimeType' => false,
                'message' => ''
            ],
        ];
    }

    public function upload()
    {
        $path = \Yii::getAlias('@webroot');
        if ($this->validate()) {
            // $this->upload_file->saveAs($path . '/uploads/' . $this->upload_file->baseName . '.' . $this->upload_file->extension);
            return true;
        } else {
            return false;
        }
    }
}
