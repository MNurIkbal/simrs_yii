<?php

namespace app\components\Traits\Pelayanan;

use Yii;
/**
 * This is the model class for table "soaprj_t".
 *
 * @property string $kegiatan_perawat

 */

class ObservasiEwsForm extends \yii\base\Model
{
    public $tanggal_ews;
    public $jenis_ews;
    public $jenis_ews_nama;
    public $pegawai_id;
    public $pegawai_nama;
    public $is_ttv;

    public $nafas;
    public $sistolik;
    public $diastolik;
    public $spo2;
    public $nadi;
    public $suhu;
    public $kesadaran;
    public $nyeri;
    public $pengeluaran;
    public $penggunaan_oksigen;
    public $protein_urine;
    public $perilaku;
    public $sistem_kardiovaskuler;
    public $sistem_respirasi;
    public $list_skor;
    public $total_skor;

    public $pendaftaran_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tanggal_ews','jenis_ews', 'pegawai_id', 'pendaftaran_id'], 'required'],
            [[
                'tanggal_ews',
                'jenis_ews',
                'jenis_ews_nama',
                'pegawai_id',
                'pegawai_nama',
                'is_ttv',
                'nafas',
                'sistolik',
                'diastolik',
                'spo2',
                'nadi',
                'suhu',
                'kesadaran',
                'nyeri',
                'pengeluaran',
                'penggunaan_oksigen',
                'protein_urine',
                'perilaku',
                'sistem_kardiovaskuler',
                'sistem_respirasi',
                'total_skor',
                'list_skor',
                'pendaftaran_id',
            ], 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tanggal_ews' => Yii::t("fe", "Tanggal & Waktu"),
            'pegawai_id' => Yii::t("fe", "Petugas"),
            'nafas' => Yii::t("fe", "Frekuensi Nafas"),
            'nadi' => Yii::t("fe", "Denyut Nadi"),
            'spo2' => Yii::t("fe", "SPO2"),
            'pengeluaran' => Yii::t("fe", "Pengeluaran/Lochea"),
            'penggunaan_oksigen' => Yii::t("fe", "Penggunaan O2"),
            'jenis_ews_nama' => Yii::t("fe", "Jenis EWS"),
            'pegawai_nama' => Yii::t("fe", "Petugas"),
        ];
    }
}
