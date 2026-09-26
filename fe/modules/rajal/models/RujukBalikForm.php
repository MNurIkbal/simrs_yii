<?php

namespace app\modules\rajal\models;

use Yii;
use app\components\DocoConstants;

class RujukBalikForm extends\yii\base\Model
{
    public $rujukbalik_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $tgl_rujukbalik;
    public $alamat;
    public $email;
    public $kode_dpjp;
    public $nama_dokter;
    public $saran;
    public $diagnosa;
    public $parent_id;
    public $data_reseptur;
    public $no_srb;
    public $no_rekam_medik;
    public $nama_pasien;
    public $nosep;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alamat', 'email', 'kode_dpjp', 'nama_dokter', 'saran'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'tgl_rujukbalik', 'alamat', 'email', 'kode_dpjp', 'nama_dokter', 'saran', 'diagnosa', 'parent_id', 'data_reseptur'], 'default', 'value' => null],
            [['rujukbalik_id', 'pendaftaran_id', 'pasienadmisi_id', 'parent_id',], 'integer'],
            [['data_reseptur'], 'checkDataReseptur'],
            [['pendaftaran_id', 'no_srb', 'pasienadmisi_id', 'tgl_rujukbalik',  'diagnosa', 'parent_id', 'data_reseptur', 'no_rekam_medik', 'nama_pasien', 'nosep'], 'safe'],
            [['tgl_rujukbalik', 'alamat', 'email', 'kode_dpjp', 'nama_dokter', 'saran', 'diagnosa', 'no_srb'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rujukbalik_id' => 'ID',
            'pendaftaran_id' => 'Pendaftaran',
            'pasienadmisi_id' => 'Pasien Admisi',
            'tgl_rujukbalik' => 'Tanggal',
            'alamat' => 'Alamat',
            'email' => 'Email',
            'kode_dpjp' => 'Kode DPJP',
            'nama_dokter' => 'Dokter',
            'saran' => 'Saran',
            'diagnosa' => 'Diagnosa Keluar RS',
            'parent_id' => 'Parent',
            'data_reseptur' => 'Reseptur',
            'no_srb' => 'No SRB',
            'no_rekam_medik' => 'No. Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'nosep' => 'Nomor SEP',
        ];
    }

    public function checkDataReseptur() {
        Yii::error($this->data_reseptur);
        if(is_array($this->data_reseptur)) {
            foreach ($this->data_reseptur as $reseptur) {
                if(
                    !isset($reseptur['kode_bpjs']) || !isset($reseptur['obatalkes_nama']) || !isset($reseptur['signa']) ||
                    !isset($reseptur['qty_signa']) || !isset($reseptur['iterasi_signa']) || !isset($reseptur['qty_reseptur']) || 
                    empty($reseptur['kode_bpjs']) || empty($reseptur['obatalkes_nama']) || empty($reseptur['signa']) ||
                    empty($reseptur['qty_signa']) || empty($reseptur['iterasi_signa']) || empty($reseptur['qty_reseptur'])
                ) {
                    $this->addError('data_reseptur', 'Data-data Reseptur tidak boleh kosong');
                    return;
                }
            }
        }
    }
}
