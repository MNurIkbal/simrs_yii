<?php
namespace app\modules\penatajasa\models;

use Yii;

class HapusTindakanForm extends \yii\base\Model
{
    
    public $pendaftaran_id;
    public $alasan;
    public function rules()
    {
        return [
            [[
                'alasan',
            ],'required','message'=>'{attribute} Tidak boleh kosong'],
            [[
                'pendaftaran_id',
                'alasan',
            ],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'alasan' => 'Alasan',
        ];
    }
}