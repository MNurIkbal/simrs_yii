<?php

namespace app\modules\kasir\models;

use Yii;

class InvoiceGabungDetailForm extends \yii\base\Model
{
    public $pasien_id;
    public $no_rekam_medik;
    public $nama_pasien;
    public $pendaftaran_id;
    public $no_pendaftaran;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'nama_pasien', 'no_rekam_medik', 'no_pendaftaran', 'pendaftaran_id', 'pasien_id'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'No Rekam Medik',
            'pendaftaran_id' => 'Nomor Pendaftaran',
        ];
    }
}
