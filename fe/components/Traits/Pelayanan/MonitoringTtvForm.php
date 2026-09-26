<?php

namespace app\components\Traits\Pelayanan;

use Yii;
/**
 * This is the model class for table "soaprj_t".
 *
 * @property string $kegiatan_perawat

 */

class MonitoringTtvForm extends \yii\base\Model
{
    public $vitalsign_id;
    public $tanggal_ttv;
    public $jenisttv;
    public $jenisttv_id;
    public $tingkatkesadaran;
    public $tingkatkesadaran_id;
    public $sistol;
    public $diastol;
    public $tinggi_badan;
    public $berat_badan;
    public $nadi;
    public $spo2;
    public $respirasi;
    public $suhu;
    public $gcs_e;
    public $gcs_v;
    public $gcs_m;
    public $pendaftaran_id;
    public $sumberttv_id;
    public $sumberttv;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tanggal_ttv','jenisttv', 'pendaftaran_id', 'tingkatkesadaran_id', 'jenisttv_id'], 'required'],
            [[
                'vitalsign_id',
                'sumberttv_id',
                'sumberttv',
                'jenisttv',
                'jenisttv_id',
                'tingkatkesadaran_id',
                'tingkatkesadaran',
                'tanggal_ttv', 
                'pendaftaran_id', 
                'sistol', 
                'diastol',  
                'tinggi_badan',
                'berat_badan', 
                'nadi', 
                'spo2', 
                'respirasi', 
                'suhu', 
                'gcs_e', 
                'gcs_v', 
                'gcs_m', 
            ], 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tanggal_ttv' => Yii::t("fe", "Tanggal & Waktu"),
            'jenisttv_id' => Yii::t("fe", "Jenis TTV"),
            'tingkatkesadaran_id' => Yii::t("fe", "Tingkat Kesadaran"),
            'nadi' => Yii::t("fe", "Denyut Nadi"),
            'spo2' => Yii::t("fe", "SpO2"),
            'respirasi' => Yii::t("fe", "Frekuensi Nafas"),
            'gcs_e' => Yii::t("fe", "GCS E"),
            'gcs_v' => Yii::t("fe", "GCS V"),
            'gcs_m' => Yii::t("fe", "GCS M"),
        ];
    }
}
