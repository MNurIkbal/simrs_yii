<?php

namespace Doco\kasir\models;

use Yii;

class TransaksiPelayananForm extends \yii\base\Model
{
    
    public $pendaftaran_id;
    public $instalasi_id;
    public $ruangan_id;
    public $pasien_id;
    public $penjamin_id;
    public $carabayar_id;
    public $kelaspelayanan_id;
    public $pasienpulang_id;
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $no_rekam_medik;
    public $nama_pasien;
    public $no_mobile_pasien;
    public $instalasi_nama;
    public $ruangan_nama;
    public $carabayar_nama;
    public $penjamin_nama;
    public $kelaspelayanan_nama;
    public $tglpasienpulang;
    public $type_antrian;
    public $status_pasien;
    public $pasienmasukpenunjang_id;

    public $detail_tagihan;

    public $tanggal_pembayaran;
    public $jumlah_uangmuka;
    public $subsidi_asuransi;
    public $total_tagihan;
    public $biaya_administrasi;
    public $uang_diterima;
    public $pembulatan;
    public $uang_kembalian;
    public $pengguna_uang_muka;

    // E Collection
    public $is_ecollect;
    public $nama_pemilik;
    public $nomor_rekening;

    public $ruangan_pelakhir_id;

    public $tagihan_pasien;
    public $pasienadmisi_id;
    public $condition;

    // add conditional
    public $car_id;
    public $pen_id;

    public function rules()
    {
        return [
            [[
                'pengguna_uang_muka',
                'subsidi_asuransi',
                'total_tagihan',
                'biaya_administrasi',
                'uang_diterima',
                'jumlah_uangmuka',
                'detail_tagihan'
            ],'required'],
            [[
                'pendaftaran_id',
                'instalasi_id',
                'ruangan_id',
                'pasien_id',
                'penjamin_id',
                'carabayar_id',
                'kelaspelayanan_id',
                'pasienpulang_id',
                'no_pendaftaran',
                'tgl_pendaftaran',
                'no_rekam_medik',
                'nama_pasien',
                'no_mobile_pasien',
                'instalasi_nama',
                'ruangan_nama',
                'carabayar_nama',
                'penjamin_nama',
                'kelaspelayanan_nama',
                'tglpasienpulang',
                'detail_tagihan',
                'is_ecollect',
                'nama_pemilik',
                'nomor_rekening',
                'ruangan_pelakhir_id',
                'tanggal_pembayaran',
                'type_antrian',
                'status_pasien',
                'tagihan_pasien',
                'pasienmasukpenunjang_id',
                'pasienadmisi_id',
                'condition',
                'car_id',
                'pen_id',
                'pembulatan',
                'biaya_administrasi',
                'jumlah_uangmuka',
                'pengguna_uang_muka',
            ],'safe'],
            [['detail_tagihan'],'checkTagihan'],
            [['pengguna_uang_muka'],'checkUangMuka']
        ];
    }

    public function checkUangMuka($attribute, $param)
    {
        if ($this->pengguna_uang_muka > $this->jumlah_uangmuka) {
            $this->addError('pengguna_uang_muka',Yii::t('fe','Pengguna Uang Muka terlalu besar'));
        }
    }

    public function checkTagihan($attribute, $param)
    {
        $input = Yii::$app->request;
        if (is_array($this->detail_tagihan)) {
            foreach ($this->detail_tagihan as $jenis => $items) {
                foreach ($items as $key => $item) {
                    $item = json_decode($item,true);
                    if (empty($item['penjamin_pelayanan_id']) || empty($item['carabayar_pelayanan_id'])) {
                        $this->addError('detail_tagihan',Yii::t('fe','Detail transaksi tidak boleh kosong'));
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'instalasi_id' => 'Instalasi ID',
            'jumlah_uangmuka' => 'Uang Muka',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'penjamin_id' => 'Penjamin ID',
            'carabayar_id' => 'Carabayar ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'tglpasienpulang' => 'Tglpasienpulang',
            'pengguna_uang_muka' => 'Penggunaan Uang Muka',
            'tagihan_pasien' => 'Ditagihkan ke pasien',
        ];
    }
}