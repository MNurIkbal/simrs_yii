<?php

/**
* @author yaya
*/

namespace Doco\kasir\models;

use Yii;

class TagihanPasienForm extends \yii\base\Model
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
    public $alamat_pasien;
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
    public $penjualanresep_id;
    public $jasa;
    public $administrasi; #apotek dll

    public $detail_tagihan;
    public $tmpTagihan;
    public $kontrakPenjamin;

    public $tanggal_pembayaran;
    public $jumlah_uangmuka;
    public $subsidi_asuransi;
    public $total_tagihan;
    public $biaya_administrasi;
    public $uang_diterima;
    public $pembulatan;
    public $uang_kembalian;
    public $pengguna_uang_muka;
    public $diskon_dokter;
    public $data_diskon;

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
    public $tanggal_lahir;
    public $total_dijamin;
    public $total_balance_rs;
    public $total_uang_muka;
    public $total_dibayar;
    public $total_nontunai;
    public $total_sisa_piutang;
    public $total_piutang;
    public $hak_kelas;
    public $data_penjamin;
    public $data_metode_pembayaran;
    public $no_kartu;
    public $total_diskon;
    public $catatan;
    public $persen;
    public $persen_chk;
    public $group_carabayar;
    public $adm_asuransi;
    public $tagihan_dijamin;
    public $plafon_payer;
    public $plafon_subpayer;
    public $penjamin_id_main;
    public $penjamin_id_sub;
    public $excess_pasien;
    public $limit_penjamin;
    public $nominaldiskon_edit_tagihan;

    /**tindakan visit dokter */
    public $tindakan_visitdokter;


    /**
     * @var Float [Sisa tagihan penunjang]
     */
    public $sisa_penunjang;

    /**
     * @var Float [Sisa tagihan Karcis]
     */
    public $sisa_karcis;

    /**
     * @var Float [Sisa tagihan Obat Alkes]
     */
    public $sisa_obat;

    /**
     * @var String [Flag untuk menentukan tipe pembayaran]
     */
    public $kelompok;
    public $sisa_tagihanlain;
    public $no_sep;
    public $data_diskon_dokter;
    public function rules()
    {
        return [
            [[
                'subsidi_asuransi',
                'total_tagihan',
                'biaya_administrasi',
                'jumlah_uangmuka',
                // 'detail_tagihan',
                'total_dibayar'
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
                'alamat_pasien',
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
                'penjualanresep_id',
                'jasa',
                'administrasi',
                'total_dijamin', 
                'total_balance_rs',
                'total_uang_muka',
                'total_dibayar',
                'total_sisa_piutang',
                'tanggal_lahir', 
                'hak_kelas',
                'total_tagihan',
                'data_penjamin',
                'data_metode_pembayaran',
                'no_kartu',
                'total_piutang',
                'jumlah_uangmuka',
                'total_nontunai',
                'diskon_dokter',
                'data_diskon',
                'total_diskon',
                'catatan',
                'persen',
                'persen_chk',
                'sisa_penunjang',
                'sisa_karcis',
                'sisa_obat',
                'kelompok',
                'sisa_tagihanlain',
                'group_carabayar',
                'no_sep',
                'tagihan_dijamin',
                'plafon_payer',
                'plafon_subpayer',
                'penjamin_id_main',
                'penjamin_id_sub',
                'excess_pasien',
                'limit_penjamin'
            ],'safe'],
            // [['detail_tagihan'],'checkTagihan'],
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

    // validasi ketika detail tagihan masih ada yang berwarna merah atau tindakan tidak ada pada cara bayar baru
    public function checkIsValid($attribute, $param)
    {
        $input = Yii::$app->request;
        if (is_array($this->detail_tagihan)) {
            foreach ($this->detail_tagihan as $jenis => $isValid) {
                foreach ($isValid as $key => $valid) {
                    $valid = json_decode($valid,true);
                    if (($valid['is_valid'] == false) && ($valid['is_valid'] !== null)) {
                        $this->addError('detail_tagihan',Yii::t('fe','Ada detail transaksi dengan tindakan yang tidak ada di cara bayar baru (baris berwarna merah)'));
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
            'diskon_dokter' => 'Jasa Dokter',
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
            'total_diskon' => 'Diskon Total',
            'catatan' => 'Catatan',
            'persen' => 'Persen',
            'persen_chk' => 'Persen',
        ];
    }
}