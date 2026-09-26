<?php

namespace app\components\Traits\Pelayanan;

use Yii;
use yii\helpers\Html;

class SbarForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $sbar_id;
    public $tgl_sbar;
    public $dokter_tujuan_id;
    public $situasi;
    public $is_ttv;
    public $respirasi;
    public $sistol;
    public $diastol;
    public $spo2;
    public $nadi;
    public $suhu;
    public $tinggi_badan;
    public $berat_badan;
    public $lingkar_kepala;
    public $asesmen;
    public $rekomendasi;
    public $loginpemakai_id;
    public $tgl_pendaftaran;
    public $tglpasienpulang;

    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function rules()
    {
        return [
            [['tgl_sbar','situasi', 'dokter_tujuan_id', 'asesmen', 'rekomendasi', 'pendaftaran_id'], 'required'],
            ['tgl_sbar', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [
                'tgl_sbar',
                'compare',
                'compareAttribute' => 'tgl_pendaftaran',
                'operator' => '>=',
                'type' => 'datetime',
                'message' => 'Tanggal dan Waktu Input SBAR di luar periode kunjungan pasien.',
            ],
            [
            'tgl_sbar',
                'compare',
                'compareAttribute' => 'tglpasienpulang',
                'operator' => '<=',
                'type' => 'datetime',
                'message' => 'Tanggal dan Waktu Input SBAR di luar periode kunjungan pasien.',
                'when' => function ($model) {
                    return !empty($model->tglpasienpulang);
                },
                'whenClient' => "function (attribute, value) {
                    return $('#" . Html::getInputId($this, 'tglpasienpulang') . "').val() !== '';
                }",
            ],
            [[
                'tgl_sbar',
                'situasi',
                'asesmen',
                'dokter_tujuan_id',
                'rekomendasi',
                'is_ttv',
                'respirasi',
                'sistol',
                'diastol',
                'spo2',
                'nadi',
                'suhu',
                'tinggi_badan',
                'berat_badan',
                'lingkar_kepala',
                'pendaftaran_id',
                'sbar_id',
                'loginpemakai_id',
                'tgl_pendaftaran',
                'tglpasienpulang'
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_sbar' => Yii::t("fe", "Tanggal & Waktu"),
            'dokter_tujuan_id' => Yii::t("fe", "Dokter Tujuan"),
            'respirasi' => Yii::t("fe", "Frekuensi Nafas"),
            'nadi' => Yii::t("fe", "Denyut Nadi"),
            'spo2' => Yii::t("fe", "SPO2"),
            'situasi' => Yii::t("fe", "Situation"),
            'asesmen' => Yii::t("fe", "Assesment"),
            'rekomendasi' => Yii::t("fe", "Recommendation"),
        ];
    }

    public function beforeValidate()
    {
        if (!empty($this->tgl_pendaftaran)) {
            $this->tgl_pendaftaran = date(self::DATETIME_FORMAT, strtotime($this->tgl_pendaftaran));
        }
        if (!empty($this->tglpasienpulang)) {
            $this->tglpasienpulang = date(self::DATETIME_FORMAT, strtotime($this->tglpasienpulang));
        }
        if (!empty($this->tgl_sbar)) {
            $this->tgl_sbar = date(self::DATETIME_FORMAT, strtotime($this->tgl_sbar));
        }
        
        return parent::beforeValidate();
    }
}
