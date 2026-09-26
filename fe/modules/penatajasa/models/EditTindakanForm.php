<?php
namespace app\modules\penatajasa\models;

use Yii;

class EditTindakanForm extends \yii\base\Model
{
    
    public $pendaftaran_id;
    public $alasan_edit;
    public $tanggal_tindakan;
    public $tanggal_asal;
    public function rules()
    {
        return [
            [[
                'alasan_edit',
                'tanggal_tindakan',
            ],'required','message'=>'{attribute} Tidak boleh kosong'],
            [[
                'pendaftaran_id',
                'alasan_edit',
                'tanggal_asal',
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
            'alasan_edit' => 'Alasan Edit',
            'tanggal_tindakan' => 'Tanggal Tindakan',
            'tanggal_asal' => 'Tanggal Asal',
        ];
    }
}
