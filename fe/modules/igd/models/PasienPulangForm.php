<?php
//author: ijal

namespace app\modules\igd\models;

use Yii;

class PasienPulangForm extends \yii\base\Model
{
    public $pasienpulang_id;
    public $pendaftaran_id;
    public $pasien_id;
    public $ruanganakhir_id;
    public $carakeluar_id;
    public $tglpasienpulang;
    public $kondisikeluar_id;
    public $tgl_meninggal;
    public $tgl_pendaftaran;
    public $dpjp_id;
    public $tempattidurtujuan_id;
    public $catatan_lain;
    public $status_jenazah;
    public $tgl_kremasi;
    public $nama_pemeriksa_jenazah;
    public $kualifikasi_pemeriksa;
    public $waktu_pemeriksaan_jenazah;
    public $dasar_diagnosis;
    public $kelompok_kematian;
    public $tempat_kematian;
    public $penyebab_langsung;
    public $penyebab_antara;
    public $penyebab_dasar;
    public $kondisi_lain;
    public $penyebab_utama_bayi;
    public $penyebab_utama_ibu;
    public $penyebab_lain_bayi;
    public $penyebab_lain_ibu;
    public $pihak_menerima;
    public $hubungan_penerima;
    public $infeksi;
    public $kamarruangan_jenis;
    public $dokterspesialis_id;
    public $catatan_tindakan;
    public $no_surat_kematian;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id', 'pasien_id', 'carakeluar_id',
                    'ruanganakhir_id', 'tglpasienpulang', 'kondisikeluar_id',
                ],
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            ['tglpasienpulang', 'validateDate'],
            [
                [
                    'tgl_meninggal',
                    'pasienpulang_id',
                    'tgl_pendaftaran',
                    'dpjp_id',
                    'tempattidurtujuan_id',
                    'catatan_lain',
                    'status_jenazah',
                    'infeksi',
                    'waktu_pemeriksaan_jenazah',
                    'dokterspesialis_id',
                    'catatan_tindakan',
                    'no_surat_kematian',
                ],
                'safe'
            ],
            [
                'dpjp_id' , 'required', 'when' => function($model) {
                    return $model->carakeluar_id == '5';
                }
            ],
            [['tgl_meninggal', 'no_surat_kematian'], 'required', 'when' => function ($model) {
                return $model->carakeluar_id == '4';
            }, 'whenClient' => "function (attribute, value) {
                return $('#pasienpulangform-carakeluar_id').val() == '4';
            }"],
        ];
    }

    public function validateDate()
    {
        if (strtotime($this->tgl_pendaftaran) > strtotime($this->tglpasienpulang)) {
            $this->addError('tglpasienpulang','Tanggal pulang tidak boleh kecil dari tanggal pendaftaran');
        }
        if (strtotime($this->tglpasienpulang) > strtotime(date('Y-m-d H:i:s'))) {
            $this->addError('tglpasienpulang','Tanggal pulang tidak boleh besar dari hari ini');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'carakeluar_id' => Yii::t('fe', 'Cara keluar'),
            'tglpasienpulang' => Yii::t('fe', 'Tanggal keluar / pindah ruangan'),
            'kondisikeluar_id' => Yii::t('fe', 'Kondisi pasien'),
            'tgl_meninggal' => Yii::t('fe', 'tanggal / jam meninggal'),
            'pasienpulang_id' => Yii::t('fe', 'ID pasien pulang'),
            'dpjp_id' => 'Dokter DPJP',
            'kamarruangan_jenis' => 'Jenis Kamar Tujuan',
            'tempattidurtujuan_id' => 'Kamar Ruangan Tujuan',
            'catatan_lain' => 'Catatan Lain',
            'status_jenazah' => 'Status Jenazah',
            'infeksi' => 'Infeksi',
            'dokterspesialis_id' => 'Dokter Tujuan',
            'catatan_tindakan' => 'Pemeriksaan / Pertolongan yang sudah / harus diberikan',
            'no_surat_kematian' => Yii::t('fe', 'Nomor surat kematian'),
        ];
    }
}
