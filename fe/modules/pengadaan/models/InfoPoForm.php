<?php

namespace app\modules\pengadaan\models;

use Yii;
use app\components\DocoHelpers;

class InfoPoForm extends \yii\base\Model
{

    public $nomor;
    public $tanggal_po;
    public $transaksi_id;
    public $asal_transaksi;
    public $supplier_id;
    public $supplier_nama;
    public $ruangan_nama;
    public $instalasi_nama;
    public $tgl_rencanaterima;
    public $diorder_oleh;
    public $peg_mengetahui_id;
    public $peg_mengetahui;
    public $peg_menyetujui_id;
    public $peg_menyetujui;
    public $payterm_id;
    public $pajak_id;
    public $no_transaksi;
    public $is_validasi;
    public $diorder_oleh_nama;
    public $tgl_rekomendasi;
    public $catatan1;
    public $catatan2;
    public $status_penerimaan;
    public $stat_penerimaan;
    public $peg_mengubah;
    public $tgl_perubahan;
    public $po_cito; 
    public $po_admin; 
    public $po_consigment;

    public $list_data;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'nomor',
                'tanggal_po',
                'transaksi_id',
                'asal_transaksi',
                'supplier_id',
                'supplier_nama',
                'ruangan_nama',
                'instalasi_nama',
                'tgl_rencanaterima',
                'diorder_oleh',
                'peg_mengetahui_id',
                'peg_mengetahui',
                'peg_menyetujui_id',
                'peg_menyetujui',
                'payterm_id',
                'is_validasi',
                'pajak_id',
                'no_transaksi',
                'diorder_oleh_nama',
                'tgl_rekomendasi',
                'catatan1',
                'catatan2',
                'list_data',
                'status_penerimaan',
                'stat_penerimaan',
                'peg_mengubah',
                'tgl_perubahan',
                'po_cito', 
                'po_admin', 
                'po_consigment',
            ],'safe'],
            [[
                'pajak_id',
                'payterm_id',
                'list_data',
                'supplier_id'
            ],'required', 'message' => '{attribute} tidak boleh kosong'],
            [['list_data'],'checkValid']
        ];
    }

    public function checkValid()
    {
        $listData = json_decode($this->list_data,true);
        foreach ($listData as $key => $value) {
           if (empty($value['qty'])) {
                DocoHelpers::multipleParseError($this,'Qty tidak boleh bernilai 0','qty',$key);
           }
           if (empty($value['satuan_id'])) {
                DocoHelpers::multipleParseError($this,'Satuan tidak boleh kosong','konversi',$key);
           }
           if (empty($value['harga'])) {
                DocoHelpers::multipleParseError($this,'Harga tidak boleh bernilai 0','harga',$key);
           }
        }
    }

    public function attributeLabels()
    {
        return [
            'tgl_rencanaterima' => 'Tgl Rencana Terima',
            'supplier_id' => 'Supplier',
            'payterm_id' => 'Payment Term',
            'pajak_id' => 'Tarif Pajak'
        ];
    }
}
