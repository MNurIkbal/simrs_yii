<?php
//author: Ardi Pratama

namespace app\modules\igd\models;

use Yii;
use app\components\DocoConstants;

class InstruksiPenunjangForm extends \yii\base\Model
{
    // kebutuhan order penunjang
    public $pendaftaran_id;
    public $pasien_id;
    public $cppt_id;
    public $instalasi_id;
    public $ruangan_id;
    public $pegawai_id;
    public $tgl_kirimpasien; // tanggal permintaan
    public $catatan_dokterpengirim;
    public $id_tindakan; // array

    public $no_rujukan;
    public $pegawai_nama;

    public $jenispemeriksaanlab_id;
    public $jenispemeriksaanrad_id;
    public $jenispemeriksaanrehabmedik_id;

    // untuk kepentingan validasi jadwal operasi
    public $has_jadwal;
    public $pasienadmisi_id;
    public $is_puasa;
    public $ruangan;

    public $pemakaian_implant;
    public $sewa_vendor;
    public $sewa_alat_rs;
    public $jenis_operasi_cito;
    public $jenis_operasi_elektif;
    public $jenis_operasi_odc;

    public $is_rujukan;

    public $diagnosa_utama;
    public $diagnosa_utama_text;
    public $diagnosa_penyerta;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'instalasi_id', 'ruangan_id',  'tgl_kirimpasien', 'pegawai_id', 'catatan_dokterpengirim'
                ],
                'required',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [
                [
                    'has_jadwal'
                ],
                'required',
                 'when' => function($model) {
                    return $model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH;
                }
            ],
            [
                [
                    'pendaftaran_id', 'pasien_id', 'cppt_id',
                    'catatan_dokterpengirim', 'no_rujukan', 'jenispemeriksaanlab_id',
                    'jenispemeriksaanrad_id', 'jenispemeriksaanrehabmedik_id',
                    'pegawai_id', 'id_tindakan', 'has_jadwal', 'pasienadmisi_id', 'is_puasa', 'ruangan',
                    'pemakaian_implant', 'sewa_vendor', 'sewa_alat_rs', 'jenis_operasi_cito', 'jenis_operasi_elektif', 'jenis_operasi_odc', 'is_rujukan',
                    'diagnosa_utama', 'diagnosa_utama_text', 'diagnosa_penyerta',
                ],
                'safe'
            ],
            // [
            //     ['has_jadwal'],
            //     'validateJadwalOperasi'
            // ],
        ];
    }

    public function validateJadwalOperasi($attribute, $params)
    {
        if ($this->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH) {
            if (!$this->has_jadwal) {
                $this->addError($attribute, "Jadwal Operasi tidak boleh kosong");
                return false;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => Yii::t('fe', 'Unit penunjang'),
            'ruangan_id' => Yii::t('fe', 'Ruangan tujuan'),
            'catatan_dokterpengirim' => Yii::t('fe', 'Keterangan Klinis'),
            'tgl_kirimpasien' => Yii::t('fe', 'Tanggal permintaan'),
            'pegawai_nama' => Yii::t('fe', 'Dokter perujuk'),
            'has_jadwal' => Yii::t('fe', 'Jadwal operasi'),
            'is_puasa' => Yii::t('fe', 'Reminder Puasa'),
            'pemakaian_implant' => Yii::t('fe', 'Rencana Pemakaian Implant'),
            'sewa_vendor' => Yii::t('fe', 'Rencana Sewa Alat Vendor'),
            'sewa_alat_rs' => Yii::t('fe', 'Rencana Pemakaian Alat RS'),
            'jenis_operasi_cito' => Yii::t('fe', 'Jenis Prosedur / Operasi CITO'),
            'jenis_operasi_elektif' => Yii::t('fe', 'Jenis Prosedur / Operasi Elektif'),
            'jenis_operasi_odc' => Yii::t('fe', 'Jenis Prosedur / Operasi ODC'),
            'is_rujukan' => Yii::t('fe', 'Rujukan'),
            'diagnosa_utama_text' => Yii::t('fe', 'Diagnosa Utama'),
        ];
    }
}
