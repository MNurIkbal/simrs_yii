<?php

namespace app\modules\v1\models;

use Yii;
use yii\base\Model;

class UploadForm extends Model
{
    public $file;
    public function rules()
    {
        return [
            [
                ['file'], 'file',
                'skipOnEmpty' => false,
            ]
        ];
    }
}
