<?php

namespace app\modules\kasir\models;

use Yii;

class GabungBillingForm extends \yii\base\Model
{
    public $no_rekam_medik;
    public $no_pendaftaran;
    public $no_pendaftaran_tujuan;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_rekam_medik', 'no_pendaftaran', 'no_pendaftaran_tujuan'], 'required'],
            [[
                'no_rekam_medik', 'no_pendaftaran', 'no_pendaftaran_tujuan'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_rekam_medik' => 'Nama Pasien/No.RM',
            'no_pendaftaran' => 'No Pendaftaran Yang Akan di Gabung',
            'no_pendaftaran_tujuan' => 'No Pendaftaran Tujuan',
        ];
    }
}
