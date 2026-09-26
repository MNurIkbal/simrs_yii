<?php

/**
* @author yaya
*
**/

namespace Doco\penjaminasuransi\models;

use Yii;

class SuratKeteranganDokterForm extends \yii\base\Model
{

    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $is_doc;
    public $upload_doc;
    public $tgl_gejala;
    public $tgl_konsul;
    public $gejala_penyakit;
    public $diag_utama;
    public $diag_tambahan;
    public $faktor_penyebab;
    public $tgl_diagnosa;
    public $terapi_tindakan;
    public $jenis_operasi;
    public $dokbedah_id;
    public $hasil_penunjang;
    public $sebebdiagnosa_id;
    public $is_kecelakaaan;
    public $tgl_kecelakaan;
    public $sebab_kecelakaan;
    public $is_diag_sama;
    public $tgl_diag_sama;
    public $diag_sama;
    public $nama_rs;
    public $nama_dokter_rs;
    public $is_rujukan;
    public $dokter_rujukan;
    public $alamat;
    public $instalasi_id;
    public $is_konsultasi;
    public $tgl_konsultasi;
    public $diag_konsultasi;
    public $nam_rs_konsul;
    public $nama_dr_konsul;

    public function rules()
    {
        return [
            [[
                'gejala_penyakit',
                'diag_utama',
                'diag_tambahan',
                'faktor_penyebab',
                'tgl_gejala',
                'tgl_konsul'
            ],'required'],
            ['is_konsultasi', 'required', 'message' => 'Harus diisi'],
            ['is_diag_sama', 'required', 'message' => 'Harus diisi'],
            [[
                'pendaftaran_id',
                'pasienadmisi_id',
                'pasien_id',
                'is_doc',
                'upload_doc',
                'tgl_gejala',
                'tgl_konsul',
                'gejala_penyakit',
                'diag_utama',
                'diag_tambahan',
                'faktor_penyebab',
                'tgl_diagnosa',
                'terapi_tindakan',
                'jenis_operasi',
                'dokbedah_id',
                'hasil_penunjang',
                'sebebdiagnosa_id',
                'is_kecelakaaan',
                'tgl_kecelakaan',
                'sebab_kecelakaan',
                'is_diag_sama',
                'tgl_diag_sama',
                'diag_sama',
                'nama_rs',
                'nama_dokter_rs',
                'is_rujukan',
                'dokter_rujukan',
                'alamat',
                'instalasi_id',
                'is_konsultasi',
                'tgl_konsultasi',
                'diag_konsultasi',
                'nam_rs_konsul',
                'nama_dr_konsul',
            ],'safe'],
            [['is_diag_sama'], 'checkDiagSama'],
            [['is_konsultasi'], 'checkKonsul'],
            [['is_rujukan'],'checkRujukan', 'on' => 'default'],
            [['is_kecelakaaan'],'checkKecelakaan', 'on' => 'default'],
            [['terapi_tindakan'],'checkTerapi', 'on' => 'default'],
        ];
    }

    public function checkTerapi($attributes, $params)
    {
        if (!empty($this->terapi_tindakan)) {
            if (!isset($this->jenis_operasi)) {
                $this->addError('jenis_operasi', 'Jenis operasi Harus diisi');
            }

            // if (!isset($this->dokbedah_id)) {
            //     $this->addError('dokbedah_id', 'Dokter operasi Harus diisi');
            // }
        }
    }

    public function checkKecelakaan($attributes, $params)
    {
        if (!empty($this->is_kecelakaaan)) {
            if (empty($this->tgl_kecelakaan)) {
                $this->addError('tgl_kecelakaan', 'Tanggal kecelakaan Harus diisi');
            }

            if (empty($this->sebab_kecelakaan)) {
                $this->addError('sebab_kecelakaan', 'Sebab kecelakaan Harus diisi');
            }
        }
    }

    public function checkRujukan($attributes, $params)
    {
        if (!empty($this->is_rujukan)) {
            if (empty($this->dokter_rujukan)) {
                $this->addError('dokter_rujukan', 'Dokter Harus diisi');
            }

            if (empty($this->alamat)) {
                $this->addError('alamat', 'Alamat Harus diisi');
            }
        }
    }

    public function checkKonsul($attributes, $params)
    {
        if (!empty($this->is_konsultasi)) {
            if (empty($this->tgl_konsultasi)) {
                $this->addError('tgl_konsultasi', 'Tanggal Harus diisi');
            }

            if (empty($this->diag_konsultasi)) {
                $this->addError('diag_konsultasi', 'Diagnosa Harus diisi');
            }

            if (empty($this->nam_rs_konsul)) {
                $this->addError('nam_rs_konsul', 'Nama Rumah Sakit Harus diisi');
            }

            if (empty($this->nama_dr_konsul)) {
                $this->addError('nama_dr_konsul', 'Dokter Harus diisi');
            }
        }
    }

    public function checkDiagSama($attributes, $params)
    {
        if (!empty($this->is_diag_sama)) {
            if (empty($this->tgl_diag_sama)) {
                $this->addError('tgl_diag_sama', 'Tanggal Harus diisi');
            }

            if (empty($this->diag_sama)) {
                $this->addError('diag_sama', 'Diagnosa Harus diisi');
            }

            if (empty($this->nama_rs)) {
                $this->addError('nama_rs', 'Nama Rumah Sakit Harus diisi');
            }

            if (empty($this->nama_dokter_rs)) {
                $this->addError('nama_dokter_rs', 'Dokter Harus diisi');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tgl_gejala' => Yii::t('fe','Tanggal Gejala/keluhan pertama kali yang diketahui pasien'),
            'tgl_konsul' => Yii::t('fe','Tanggal pertama kali konsultasi penyakit yang diderita'),
            'instalasi_id' => Yii::t('fe','Jenis Layanan'),
            'gejala_penyakit' => Yii::t('fe','Gejala Penyakit'),
            'diag_utama' => Yii::t('fe','Diagnosa Utama'),
            'diag_tambahan' => Yii::t('fe','Diagnosa Tambahan'),
            'faktor_penyebab' => Yii::t('fe','Faktor Penyebab'),
            'tgl_diagnosa' => Yii::t('fe','Tanggal Diagnosa ditegakan'),
            'terapi_tindakan' => Yii::t('fe','Terapi/Tindakan bedah yang diberikan'),
            'jenis_operasi' => Yii::t('fe','Jenis Operasi'),
            'dokbedah_id' => Yii::t('fe','Nama Dokter Bedah'),
            'hasil_penunjang' => Yii::t('fe','Hasil Pemeriksaan Lab/Radiologi'),
            'sebebdiagnosa_id' => Yii::t('fe','Penyebab/Hubungan Diagnosa'),
            'is_kecelakaaan' => Yii::t('fe','Perawatan karena kecelakaan'),
            'tgl_kecelakaan' => Yii::t('fe','Tanggal kecelakaan'),
            'sebab_kecelakaan' => Yii::t('fe','Penyebab kecelakaan'),
            'is_diag_sama' => Yii::t('fe','Apakah pasien pernah konsultasi/Dirawat sebelumnya dengan diagnosa yang sama'),
            'tgl_diag_sama' => Yii::t('fe','Tanggal'),
            'diag_sama' => Yii::t('fe','Diagnosa'),
            'nama_rs' => Yii::t('fe','Nama Rumah Sakit'),
            'nama_dokter_rs' => Yii::t('fe','Dokter'),
            'is_rujukan' => Yii::t('fe','Kasus Rujukan Pengirim'),
            'dokter_rujukan' => Yii::t('fe','Dokter'),
            'alamat' => Yii::t('fe','Alamat'),
            'is_konsultasi' => Yii::t('fe','Apakah pasien pernah konsultasi/Dirawat sebelumnya'),
            'tgl_konsultasi' => Yii::t('fe','Tanggal'),
            'diag_konsultasi' => Yii::t('fe','Diagnosa'),
            'nam_rs_konsul' => Yii::t('fe','Nama Rumah Sakit'),
            'nama_dr_konsul' => Yii::t('fe','Dokter'),
        ];
    }
}