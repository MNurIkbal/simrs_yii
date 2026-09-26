<?php

namespace app\modules\penatajasa\models;

use Yii;

class TindakanForm extends \yii\base\Model
{
    const TINDAKAN = 'tindakan';
    const PAKET = 'paket';
    const AKOMODASI = 'akomodasi';
    const PULANG = 'Pulang';
    public $tanggal_tindakan;
    public $instalasi_id;
    public $ruangan_id;
    public $kelas_pelayanan;
    public $jenis_pelayanan;
    public $dokter_pj;
    public $perawat;
    public $tindakan_pelayanan_id;
    public $paket_pelayanan_id;
    public $qty;
    public $is_cyto;
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $pasienadmisi_id;
    public $pasien_id;
    public $harga_satuan;
    public $total;
    public $daftartindakan_id;
    public $tipepaket_id;
    public $tglpasienpulang;
    public $tglPendaftaran;
    public $status_periksa;

    public $instalasi_nama;
    public $ruangan_nama;
    public $dokterdpjp_nama;
    public $tindakan_nama;
    public $harga_tariftindakan;
    public $persencyto_tindakan;
    public $obatalkes_id;
    public $satuan_id;
    public $ditagihkan;
    public $qty_konversi;
    public $carabayar_id;
    public $penjamin_id;
    public $harga_netto;
    public $is_penyulit;
    public $qty_obat; 
    public $is_ditagihkan; 
    public $stok_obat; 
    public $depo_id; 
    public $tindakan_obat_id;
    public $kamarruangan_id;
    public $kamartempattidur_id;
    public $akomodasi_pelayanan_id;
    public $is_half;
    public $penjamin_tindakan_id;
    public $harga_satuan_origin;
    public $uid;
    public $alasan_edit_harga;
    public $remarks;

    public function rules()
    {
        return [
            [[
                'tanggal_tindakan',
                'jenis_pelayanan',
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan',
                'dokter_pj',
                'perawat',
                'tindakan_pelayanan_id',
                'paket_pelayanan_id',
                'qty',
                'harga_satuan',
                'total',
                'is_cyto',
                'pendaftaran_id',
                'no_pendaftaran',
                'pasienadmisi_id',
                'pasien_id',
                'daftartindakan_id',
                'tipepaket_id',
                'tglpasienpulang',
                'tglPendaftaran',
                'status_periksa',
                'obatalkes_id',
                'satuan_id',
                'ditagihkan',
                'qty_konversi', 
                'carabayar_id',
                'penjamin_id',
                'harga_netto',
                'is_penyulit',
                'is_ditagihkan',
                'qty_obat',
                'stok_obat',
                'depo_id',
                'tindakan_obat_id',
                'kamarruangan_id',
                'kamartempattidur_id',
                'akomodasi_pelayanan_id',
                'is_half',
                'penjamin_tindakan_id',
                'harga_satuan_origin',
                'uid',
                'alasan_edit_harga',
                'remarks'
            ],'safe'],
            [[
                'tanggal_tindakan',
                'jenis_pelayanan',
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan',
                'qty',
            ],'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['daftartindakan_id','dokter_pj'], 'required','on' => 'tindakan'],
            [['tipepaket_id','dokter_pj'], 'required','on' => 'paket'],
            [['obatalkes_id', 'qty', 'satuan_id'], 'required','on' => 'bmhp'],
            [['kamarruangan_id', 'kamartempattidur_id'], 'required','on' => 'akomodasi'],
            [['qty'], 'integer', 'min' => 1, 'message' => '{attribute} harus minimal 1'],
            [['tanggal_tindakan'], 'validateDate', 'on' => 'Pulang'],
            [['harga_satuan', 'harga_satuan_origin', 'total'], 'validateHargaSatuan']
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dokter_pj' => 'Dokter',
            'perawat' => 'Perawat',
            'instalasi_id' => 'Instalasi',
            'ruangan_id' => 'Ruangan',
            'kelas_pelayanan' => 'Kelas Pelayanan',
            'tindakan_pelayanan_id' => 'Tindakan',
            'paket_pelayanan_id' => 'Paket',
            'harga_satuan' => 'Harga Satuan',
            'total' => 'Sub Total',
            'obatalkes_id' => 'Obat BMHP',
            'satuan_id' => 'Satuan',
            'qty' => 'Jumlah',
            'daftartindakan_id' => 'Tindakan/Paket/Akomodasi',
            'kamarruangan_id' => 'Kamar',
            'kamartempattidur_id' => 'No Tempat Tidur',
            'akomodasi_pelayanan_id' => 'Akomodasi',
            'is_half' => 'Akomodasi 0,5 hari',
            'remarks' => 'Keterangan'
        ];
    }

    public function validateDate()
    {
        if (strtotime($this->tglPendaftaran) > strtotime($this->tanggal_tindakan)) {
            $this->addError('tanggal_tindakan','Tanggal Tindakan tidak boleh kecil dari tanggal pendaftaran');
        }
        if (strtotime($this->tanggal_tindakan) > strtotime(date('Y-m-d H:i:s'))) {
            $this->addError('tanggal_tindakan','Tanggal Tindakan tidak boleh kecil dari hari ini');
        }
        if ((!empty($this->tglpasienpulang)) && (strtotime($this->tanggal_tindakan) > strtotime($this->tglpasienpulang))) {
            $this->addError('tanggal_tindakan','Tanggal Tindakan tidak boleh kecil dari tanggal pasien pulang');
        }
    }

    public function validateHargaSatuan()
    {
        if($this->harga_satuan == 0) {
            $this->addError('harga_satuan', 'Tindakan/Paket tidak dapat disimpan karena harga 0');
        }
        if($this->total == 0 && $this->scenario != 'bmhp') {
            $this->addError('total', 'Tindakan/Paket tidak dapat disimpan karena Sub Total 0');
        }
    }

}