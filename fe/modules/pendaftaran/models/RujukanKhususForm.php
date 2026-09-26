<?php

namespace app\modules\pendaftaran\models;

use Yii;

class RujukanKhususForm extends \app\components\DocoBaseModel
{
    public $no_rujukan;
    public $type_diagnosa;
    public $procedure;
    public $procedure_nama;
    public $diagnosa_rujukan;
    public $diagnosa_rujukan_nama;
    public $data_diagnosa;
    public $data_procedure;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['diagnosa_rujukan', 'type_diagnosa', 'procedure', 'data_diagnosa', 'data_procedure'], 'required', 'on' => 'default'],
            [[ 'no_rujukan', 'data_diagnosa', 'data_procedure'], 'required', 'on' => 'create_form'],
            [['diagnosa_rujukan', 'diagnosa_rujukan_nama', 'type_diagnosa', 'procedure', 'procedure_nama', 'no_rujukan'], 'string'],
            [['diagnosa_rujukan', 'type_diagnosa', 'procedure', 'no_rujukan', 'data_diagnosa', 'data_procedure'], 'safe'],
            [['diagnosa_rujukan', 'type_diagnosa', 'procedure', 'no_rujukan'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'diagnosa_rujukan' => Yii::t('fe', 'Diagnosa Rujukan'),
            'type_diagnosa'  => Yii::t('fe', 'Type Diagnosa'), 
            'procedure'  => Yii::t('fe', 'Procedure'),
            'no_rujukan' => Yii::t('fe', 'No. Rujukan'),
        ];
    }

    public function checkMinLength()
    {
        if (strlen($this->catatan_rujukan) < 5) {
            $this->addError('catatan_rujukan', 'Catatan Tidak Boleh Kurang dari 5 karakter (Minimal 5 alpahnumeric)');
        }
    }
}
