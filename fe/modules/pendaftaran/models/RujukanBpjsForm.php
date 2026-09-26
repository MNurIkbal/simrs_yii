<?php

namespace app\modules\pendaftaran\models;

use Yii;

class RujukanBpjsForm extends \app\components\DocoBaseModel
{
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $bpjs_id;
    public $diagnosa_id;
    public $tanggal_rujukan;
    public $tanggal_rencana_kunjungan;
    public $no_rujukan;
    public $rujukan;
    public $spesialis;
    public $catatan_rujukan;
    public $jenis_pelayanan_bpjs;
    public $dirujukke;
    public $dirujukke_nama;
    public $poli_rujukan;
    public $diagnosa_rujukan;
    public $nosep;
    public $kode_ppkrujukan;
    public $kode_ppkrujuk;
    public $kode_spesialis;
    public $diagnosa_rujukan_nama;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spesialis','kode_spesialis'], 'required','on' => 'penuh'],
            [['diagnosa_rujukan', 'catatan_rujukan','tanggal_rencana_kunjungan'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'bpjs_id', 'jenis_pelayanan_bpjs'], 'integer'],
            [['diagnosa_id', 'tanggal_rujukan', 'no_rujukan', 'rujukan', 'spesialis', 'catatan_rujukan', 'dirujukke', 'dirujukke_nama', 'poli_rujukan', 'kode_ppkrujukan', 'nosep', 'diagnosa_rujukan', 'tanggal_rencana_kunjungan', 'kode_ppkrujuk', 'kode_spesialis','diagnosa_rujukan_nama'], 'safe'],
            [['tanggal_rencana_kunjungan', 'kode_ppkrujuk', 'kode_spesialis'], 'default', 'value' => null],
            [['catatan_rujukan'], 'checkMinLength']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'pasienadmisi_id' => Yii::t('fe', 'Pasien Admisi'),
            'bpjs_id' => Yii::t('fe', 'BPJS'),
            'diagnosa_rujukan' => Yii::t('fe', 'Diagnosa Rujukan'),
            'tanggal_rujukan' => Yii::t('fe', 'Tanggal Rujukan'),
            'tanggal_rencana_kunjungan' => Yii::t('fe', 'Tanggal Rencana Kunjungan'),
            'no_rujukan' => Yii::t('fe', 'No. Rujukan'),
            'rujukan' => Yii::t('fe', 'Tipe Rujukan'),
            'spesialis' => Yii::t('fe', 'Spesialis'),
            'catatan_rujukan' => Yii::t('fe', 'Catatan Rujukan'),
            'jenis_pelayanan_bpjs' => Yii::t('fe', 'Jenis Pelayanan'),
            'dirujukke' => Yii::t('fe', 'Di Rujuk Ke'),
            'nosep' => Yii::t('fe', 'No. SEP'),
            'kode_ppkrujuk' => Yii::t('fe', 'PPK Rujuk'),
            'kode_spesialis' => Yii::t('fe', 'Kode Spesialis/SubSpesialis'),
        ];
    }

    public function checkMinLength()
    {
        if (strlen($this->catatan_rujukan) < 5) {
            $this->addError('catatan_rujukan', 'Catatan Tidak Boleh Kurang dari 5 karakter (Minimal 5 alpahnumeric)');
        }
    }
}
