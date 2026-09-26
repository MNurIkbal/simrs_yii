<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Resep Form
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\gudang\models;

use Yii;

class KonfigForm extends \yii\base\Model
{
    public $pembulatan_harga;
    public $harga_digunakan;
    public $biaya_admin;
    public $layarantrian_latarbelakang;
    public $metode_antrian;
    public $tanggal_berlaku;
    public $persen_ppn;
    public $persen_pph;
    public $persen_margin;
    public $persen_diskon;
    public $formula_penjualan;
    public $pesan_struk;
    public $pesan_etiket;
    public $embalase_racikan;
    public $embalase_nonracikan;
    public $last_update;
    public $update_by;
    public $update_by_id;
    public $is_verifpemesanan;
    public $is_verifpenerimaan;
    public $is_verifstokopname;
    public $penjaminkaryawan_id;
    public $po_expired;
    public $is_bypassworklist;
    public $max_dataso;
    public $harga_donasi;
    public $batal_pesan_by;
    public $is_large_unit_pr;
    public $is_pesanstokobat_0;
    public $is_fulfilledso;
    public $is_returnstock;
    public $auto_validasi_po_manual;
    public $is_disabledfulfilled_so;
    public $is_tgl_implementasi_sesuai_verif;
    public $is_others;
    public $is_freetext;
    public $get_stok_rs;
    public $use_ppn;
    public $use_discount;
    public $enable_split_kronis;
    public $hari_resep_kronis;

    /**
     * @inheritdoc
     */
    // public static function tableName()
    // {
    //     return 'layarantrian_m';
    // }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['pembulatan_harga', 'harga_digunakan', 'biaya_admin'], 'required', 'on' => 'poli'],
            [['pembulatan_harga', 'harga_digunakan', 'pesan_etiket'], 'required', 'on' => 'loket'],
            [['layarantrian_latarbelakang'], 'file', 'extensions'=>'jpg, png, gif'],
            [['stok','is_freetext','is_others', 'instalasi_tujuan', 'ruangan_tujuan', "harga_digunakan", "metode_antrian", "persen_ppn", "persen_pph", "pesan_struk", "pesan_etiket", "last_update", "update_by", "update_by_id", "persen_diskon", 'embalase_racikan', 'embalase_nonracikan','is_verifpemesanan','is_verifpenerimaan','is_verifstokopname','penjaminkaryawan_id', 'po_expired', 'max_dataso', 'is_bypassworklist','is_large_unit_pr','is_fulfilledso','is_pesanstokobat_0','is_returnstock','auto_validasi_po_manual','is_disabledfulfilled_so','is_tgl_implementasi_sesuai_verif','harga_donasi','batal_pesan_by', 'get_stok_rs', 'use_ppn', 'use_discount', 'enable_split_kronis', 'hari_resep_kronis'], 'safe'],
            [['harga_digunakan','metode_antrian'], 'required'],
            [['harga_digunakan','metode_antrian', 'embalase_racikan', 'embalase_nonracikan', 'po_expired','harga_donasi'], 'default', "value" => 0],
            [['embalase_racikan', 'embalase_nonracikan'], 'number', "min" => 0],
        ];
    }



    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'pembulatan_harga' => \Yii::t('fe', 'Pembulan harga'),
            'harga_digunakan' => \Yii::t('fe', 'Harga yang digunakan'),
            'metode_antrian' => \Yii::t('fe', 'Metode Distribusi Stok'),
            'pesan_struk' => \Yii::t('fe', 'Pesan di struk'),
            'biaya_admin' => \Yii::t('fe', 'Biaya administrasi'),
            'pesan_etiket' => \Yii::t('fe', 'Pesan di e-tiket'),
            'layarantrian_latarbelakang' => \Yii::t('fe', 'Latar belakang'),
            'qty' => 'Qty',
            'tanggal_berlaku' =>  \Yii::t('fe', 'Tanggal berlaku'),
            'persen_ppn' =>  \Yii::t('fe', 'Persen PPn'),
            'persen_pph' =>  \Yii::t('fe', 'Persen PPh'),
            'persen_margin' =>  \Yii::t('fe', 'Persen margin'),
            'persen_diskon' =>  \Yii::t('fe', 'Persen diskon'),
            'embalase_racikan' =>  \Yii::t('fe', 'Racikan'),
            'embalase_nonracikan' =>  \Yii::t('fe', 'Non Racikan'),
            'formula_penjualan' =>  \Yii::t('fe', 'Formula penjualan'),
            'last_update' =>  \Yii::t('fe', 'Tanggal Terakhir Diubah'),
            'update_by' =>  \Yii::t('fe', 'Diubah Terakhir Oleh'),
            'update_by_id' =>  \Yii::t('fe', ''),
            'is_verifpemesanan' =>  \Yii::t('fe', 'Verifikasi Pemesanan'),
            'is_verifpenerimaan' =>  \Yii::t('fe', 'Verifikasi Penerimaan'),
            'is_verifstokopname' =>  \Yii::t('fe', 'Verifikasi Stok Opname'),
            'is_bypassworklist' =>  \Yii::t('fe', 'Lewati Proses Worklist'),
            'is_large_unit_pr' =>  \Yii::t('fe', 'Menggunakan Satuan Besar '),    
            'is_fulfilledso' =>  \Yii::t('fe', 'Field SO Wajib Diisi'),
            'is_returnstock' =>  \Yii::t('fe', 'Obat Dikembalikan Jika Resep Batal '),
            'auto_validasi_po_manual' =>  \Yii::t('fe', 'Validasi Filled Aktif '),
            'is_disabledfulfilled_so' =>  \Yii::t('fe', 'Disabled Filled tanpa Perubahan '),
            'is_tgl_implementasi_sesuai_verif' =>  \Yii::t('fe', 'Implementasi Tanggal Sesuai verivikasi '),
            'penjaminkaryawan_id' =>  \Yii::t('fe', 'Penjamin Karyawan'),
            'po_expired' =>  \Yii::t('fe', 'PO Expired'),
            'is_pesanstokobat_0' =>  \Yii::t('fe', 'Pesan Stok Obat '),
            'is_freetext' =>  \Yii::t('fe', 'Free Text '),
            'is_others' =>  \Yii::t('fe', 'Other '),
            'is_returnstock' =>  \Yii::t('fe', 'Stok Kembali Jika Resep Dibatalkan'),
            'auto_validasi_po_manual' =>  \Yii::t('fe', 'Auto Validasi'),
            'is_disabledfulfilled_so' =>  \Yii::t('fe', 'Disable Field SO Tanpa Perubahan'),
            'is_tgl_implementasi_sesuai_verif' =>  \Yii::t('fe', 'Implementasi Tanggal Sesuai Verifikasi'),
            'penjaminkaryawan_id' =>  \Yii::t('fe', 'Penjamin Karyawan'),
            'po_expired' =>  \Yii::t('fe', 'PO Expired'),
            'is_pesanstokobat_0' =>  \Yii::t('fe', 'Pesan Obat Berdasarkan Master Obat Alkes'),
            'is_freetext' =>  \Yii::t('fe', 'Racikan Freetext'),
            'is_others' =>  \Yii::t('fe', 'Non Racikan Others'),
            'max_dataso' =>  \Yii::t('fe', 'Maksimal Data SO'),
            'harga_donasi' =>  \Yii::t('fe', 'Harga Item Donasi'),
            'batal_pesan_by' =>  \Yii::t('fe', 'Batal Pesan Dilakukan Oleh '),
            'get_stok_rs' =>  \Yii::t('fe', 'Get Stok RS Pencarian Obat di Pelayanan'),
            'use_ppn' =>  \Yii::t('fe', 'PPN'),
            'use_discount' =>  \Yii::t('fe', 'Discount'),
        ];
    }
}
