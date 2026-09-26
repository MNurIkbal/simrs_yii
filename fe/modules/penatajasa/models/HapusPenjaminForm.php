<?php
namespace app\modules\penatajasa\models;

use Yii;

class HapusPenjaminForm extends \yii\base\Model
{
    
    public $pendaftaranpenjamin_id;
    public $alasan;
    public function rules()
    {
        return [
            [[
                'alasan',
            ],'required','message'=>'{attribute} Tidak boleh kosong'],
            [[
                'pendaftaranpenjamin_id',
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
            'pendaftaranpenjamin_id' => 'Pendaftaran ID',
            'alasan' => 'Alasan',
        ];
    }
}