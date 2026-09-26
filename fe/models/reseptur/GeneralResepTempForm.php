<?php

namespace app\models\reseptur;

use Yii;

class GeneralResepTempForm extends \yii\base\Model
{

    public $dokter_id;
    public $nama_template;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reseptemp_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_template'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nama_template' => 'Nama Template',
        ];
    }
}