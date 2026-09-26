<?php
namespace app\modules\pendaftaran\models;

use Yii;
use app\components\DocoBaseModel;

class KetersediaanKamarForm extends DocoBaseModel
{
    public $pendaftaran_id;
    public $pasien_id;
    public $ruangan_id;
    public $kelaspelayanan_id;
    public $kamarruangan_id;
    public $kamartempattidur_id;

    public function rules()
    {
        return [
            [[
                'pendaftaran_id', 
                'pasien_id', 
                'ruangan_id', 
                'kelaspelayanan_id', 
                'kamarruangan_id',
                'kamartempattidur_id',
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => \Yii::t('fe', 'Pendaftaran ID'),
            'pasien_id' => \Yii::t('fe', 'Pasien ID'),
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'kelaspelayanan_id' => \Yii::t('fe', 'Kelas Pelayanan'),
            'kamarruangan_id' => \Yii::t('fe', 'Kamar Ruangan'),
            'kamartempattidur_id' => \Yii::t('fe', 'Kamar Tempat Tidur'),
        ];
    }
}
