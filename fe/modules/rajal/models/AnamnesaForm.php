<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 18:00:01
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-22 11:53:44
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class AnamnesaForm extends \yii\base\Model
{
    public $anamnesa_id;
    public $pendaftaran_id;
    public $pasien_id;
    public $triase_id;
    public $pasienadmisi_id;
    public $pegawaidokter_id;
    public $pegawaiperawat_id;
    public $pegawaitriase_id;
    public $tgl_anamnesis;
    public $keluhan_utama;
    public $keluhan_tambahan;
    public $riwayat_penyakitterdahulu;
    public $riwayat_penyakitkeluarga;
    public $lama_sakit;
    public $pengobatan_ygsudahdilakukan;
    public $riwayat_alergiobat;
    public $riwayat_kelahiran;
    public $riwayat_makanan;
    public $riwayat_imunisasi;
    public $keterangan_anamesa;
    public $riwayat_perjalananpasien;
    public $status_merokok;
    public $jmlrokok_btgperhari;
    public $riwayat_imunisasiblm;
    public $riwayat_obatygsering;
    public $keb_olahraga;
    public $keb_jnsolahraga;
    public $keb_frekuensi_kaliminggu;
    public $keb_konsumsialkohol;
    public $keb_minumkopi;
    public $riwayat_kecelakaan;
    public $riwayat_operasi;
    public $keb_konsumsidrug;
    public $is_nyeri;
    public $is_resikojatuh;
    public $skala_nyeri;
    public $lokasi_nyeri;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'pegawaidokter_id', 'pegawaiperawat_id', 'keluhan_utama', 'tgl_anamnesis'], 'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['pendaftaran_id', 'pasien_id', 'triase_id', 'pasienadmisi_id', 'pegawaidokter_id', 'pegawaiperawat_id', 'pegawaitriase_id', 'jmlrokok_btgperhari', 'keb_frekuensi_kaliminggu'], 'default', 'value' => null],
            [['anamnesa_id' ,'pendaftaran_id', 'pasien_id', 'triase_id', 'pasienadmisi_id', 'pegawaidokter_id', 'pegawaiperawat_id', 'pegawaitriase_id', 'jmlrokok_btgperhari', 'keb_frekuensi_kaliminggu' ], 'integer'],
            [['tgl_anamnesis', 'is_nyeri','is_resikojatuh','lokasi_nyeri','skala_nyeri'], 'safe'],
            [['keluhan_utama', 'keluhan_tambahan', 'keterangan_anamesa', 'riwayat_perjalananpasien', 'riwayat_kecelakaan', 'riwayat_operasi'], 'string'],
            [['status_merokok'], 'boolean'],
            [['riwayat_penyakitterdahulu', 'riwayat_penyakitkeluarga', 'keb_jnsolahraga'], 'string', 'max' => 200],
            // [['pengobatan_ygsudahdilakukan', 'riwayat_alergiobat', 'riwayat_kelahiran', 'riwayat_makanan'], 'string', 'max' => 100],
            [['riwayat_penyakitterdahulu', 'riwayat_penyakitkeluarga', 'keb_jnsolahraga', 'pengobatan_ygsudahdilakukan', 'riwayat_alergiobat', 'riwayat_kelahiran', 'riwayat_makanan'], 'safe'],
            [['lama_sakit'], 'string', 'max' => 20],
            [['riwayat_imunisasiblm', 'riwayat_obatygsering'], 'string', 'max' => 500],
            [['keb_olahraga', 'keb_konsumsialkohol', 'keb_minumkopi', 'keb_konsumsidrug'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'anamnesa_id' => Yii::t('fe', 'anamnesa_id'),
            'pendaftaran_id' => Yii::t('fe', 'pendaftaran_id'),
            'pasien_id' => Yii::t('fe', 'pasien_id'),
            'triase_id' => Yii::t('fe', 'triase_id'),
            'pasienadmisi_id' => Yii::t('fe', 'pasienadmisi_id'),
            'pegawaidokter_id' => Yii::t('fe', 'Dokter'),
            'pegawaiperawat_id' => Yii::t('fe', 'Perawat'),
            'pegawaitriase_id' => Yii::t('fe', 'pegawaitriase_id'),
            'tgl_anamnesis' => Yii::t('fe', 'Tanggal Asesmen'),
            'keluhan_utama' => Yii::t('fe', 'Keluhan Utama'),
            'keluhan_tambahan' => Yii::t('fe', 'Keluhan Tambahan'),
            'riwayat_penyakitterdahulu' => Yii::t('fe', 'Riwayat Penyakit Terdahulu'),
            'riwayat_penyakitkeluarga' => Yii::t('fe', 'Riwayat Penyakit Keluarga'),
            'lama_sakit' => Yii::t('fe', 'Lama Sakit'),
            'pengobatan_ygsudahdilakukan' => Yii::t('fe', 'pengobatan_ygsudahdilakukan'),
            'riwayat_alergiobat' => Yii::t('fe', 'Riwayat Alergi'),
            'riwayat_kelahiran' => Yii::t('fe', 'Riwayat Kelahiran'),
            'riwayat_makanan' => Yii::t('fe', 'Riwayat Makanan'),
            'riwayat_imunisasi' => Yii::t('fe', 'Riwayat Imunisasi'),
            'keterangan_anamesa' => Yii::t('fe', 'Keterangan Anamnesa'),
            'riwayat_perjalananpasien' => Yii::t('fe', 'Riwayat Perjalanan Penyakit Pasien'),
            'status_merokok' => Yii::t('fe', 'Status Merokok'),
            'jmlrokok_btgperhari' => Yii::t('fe', 'Jumlah Batang Rokok'),
            'riwayat_imunisasiblm' => Yii::t('fe', 'Riwayat Imunisasi'),
            'riwayat_obatygsering' => Yii::t('fe', 'Obat yang sudah diberikan'),
            'keb_olahraga' => Yii::t('fe', 'keb_olahraga'),
            'keb_jnsolahraga' => Yii::t('fe', 'keb_jnsolahraga'),
            'keb_frekuensi_kaliminggu' => Yii::t('fe', 'keb_frekuensi_kaliminggu'),
            'keb_konsumsialkohol' => Yii::t('fe', 'keb_konsumsialkohol'),
            'keb_minumkopi' => Yii::t('fe', 'keb_minumkopi'),
            'riwayat_kecelakaan' => Yii::t('fe', 'riwayat_kecelakaan'),
            'riwayat_operasi' => Yii::t('fe', 'riwayat_operasi'),
            'keb_konsumsidrug' => Yii::t('fe', 'keb_konsumsidrug'),            
            'is_resikojatuh' => Yii::t('fe', 'Resiko Jatuh'),
            'is_nyeri' => Yii::t('fe', 'Nyeri'),
            'skala_nyeri' => Yii::t('fe', 'Skala Nyeri'),
            'lokasi_nyeri' => Yii::t('fe', 'Lokasi'),
        ];
    }
}