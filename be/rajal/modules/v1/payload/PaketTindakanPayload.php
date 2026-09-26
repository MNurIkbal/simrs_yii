<?php
namespace app\modules\v1\payload;

use Yii;

class PaketTindakanPayload extends \Doco\components\DocoBaseModel
{

    const PAKET = 'paket';
    const TINDAKAN = 'tindakan';

    public $no_pendaftaran;
    public $tgl_transaksi;
    public $instalasi_id;
    public $ruangan_id;
    public $kelas_pelayanan_id;
    public $penjamin_id;
    public $dokter_id;
    public $perawat_id;
    public $tipepaket_id;
    public $daftartindakan_id;
    public $is_cyto;
    public $qty;
    public $qty_konversi;
    public $tindakanpelayanan_id;
    public $satuankecil_id;
    public $obatalkes_id;
    public $pendaftaran_id;
    public $ditagihkan;
    public $tglpasienpulang;
    public $tglPendaftaran;
    public $status_periksa;
    public $tgl_tindakan;
    public $pasienmasukpenunjang_id;

    public function rules()
    {
         return [
            [['ditagihkan'], 'boolean'],
            [[
                'tgl_transaksi'
            ], 'date', 'format' => 'yyyy-MM-dd H:i:s'],
            [[
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan_id',
                'dokter_id',
                'perawat_id',
                'tipepaket_id',
                'daftartindakan_id',
                'penjamin_id',
                'qty',
                'qty_konversi',
                'tindakanpelayanan_id',
                'satuankecil_id',
                'obatalkes_id',
                'pendaftaran_id',
                'pasienmasukpenunjang_id'
            ], 'integer', 'min' => 0],
            [[
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan_id',
                'penjamin_id',
                'dokter_id',
                'tipepaket_id',
                'qty',
            ], 'required', 'on' => self::PAKET],
            [[
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan_id',
                'penjamin_id',
                'dokter_id',
                'daftartindakan_id',
                'qty',
            ], 'required', 'on' => self::TINDAKAN],
            [[
                'no_pendaftaran',
                'tgl_transaksi',
                'instalasi_id',
                'ruangan_id',
                'kelas_pelayanan_id',
                'penjamin_id',
                'dokter_id',
                'perawat_id',
                'tipepaket_id',
                'daftartindakan_id',
                'is_cyto',
                'qty',
                'qty_konversi',
                'tindakanpelayanan_id',
                'satuankecil_id',
                'obatalkes_id',
                'pendaftaran_id',
                'ditagihkan',
                'tglpasienpulang',
                'tglPendaftaran',
                'status_periksa',
                'pasienmasukpenunjang_id'
            ], 'safe'],
            [['tgl_tindakan'], 'validateDate'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'no_pendaftaran' => 'Nomor Pendaftaran',
            'tgl_transaksi' => 'Tanggal Pendaftaran',
            'instalasi_id' => 'Instalasi',
            'ruangan_id' => 'Ruangan',
            'kelas_pelayanan_id' => 'Kelas Pelayanan',
            'penjamin_id' => 'Penjamin',
            'dokter_id' => 'Dokter',
            'perawat_id' => 'Perawat',
            'tipepaket_id' => 'Paket',
            'daftartindakan_id' => 'Tindakan',
            'pasienmasukpenunjang_id' => 'Pasien Masuk Penunjang ID',
            'is_cyto' => 'Cyto',
            'qty' => 'Qty',
            'qty_konversi' => 'Total Konversi',
            'satuankecil_id' => 'Satuan Transaksi',
            'obatalkes_id' => 'Obat Alkes'
        ];
    }

    public function validateDate()
    {
        if (strtotime($this->tglPendaftaran) > strtotime($this->tgl_tindakan)) {
            $this->addError('tgl_tindakan','Tanggal Tindakan tidak boleh kecil dari tanggal pendaftaran');
        }
        if (strtotime($this->tgl_tindakan) > strtotime(date('Y-m-d H:i:s'))) {
            $this->addError('tgl_tindakan','Tanggal Tindakan tidak boleh kecil dari hari ini');
        }
        if ((!empty($this->tglpasienpulang)) && (strtotime($this->tgl_tindakan) > strtotime($this->tglpasienpulang))) {
            $this->addError('tgl_tindakan','Tanggal Tindakan tidak boleh kecil dari tanggal pasien pulang');
        }
    }
}
