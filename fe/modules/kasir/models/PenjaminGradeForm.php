<?php

namespace app\modules\kasir\models;

use Yii;
use app\components\DocoBaseModel;

class PenjaminGradeForm extends DocoBaseModel
{
    public $grade;

    protected $xssProtected = [
        'grade',
    ];


    public function rules()
    {
        return [
            [['grade'], 'required'],
            [['grade'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penjamingrade_id' => 'Grade Penjamin ID',
            'penjamin_id' => 'Penjamin ID',
            'grade' => 'Grade',
        ];
    }
}
