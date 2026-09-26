<?php

namespace app\modules\gudang\models;

use Yii;
use app\components\DocoHelpers;

class InfoPoForm extends \yii\base\Model
{

    public $nomor;
    public $diterima_oleh;
    public $peg_penerima_nama;
    public $peg_mengubah;
    public $tgl_perubahan;
    public $no_suratjalan;
    public $tgl_suratjalan;
    public $no_faktur;
    public $no_faktur_sementara;
    public $no_penerimaan;
    public $tgl_penerimaan;
    public $ruanganpenerima_id;
    public $peg_mengetahui;
    public $peg_menyetujui;
    public $catatan;
    public $supplier_nama;
    public $no_transaksi;
    public $supplier_id;
    public $mengetahui;
    public $menyetujui;
    public $is_verifikasi;
    public $stat_penerimaan;
    public $status_penerimaan;

    public $list_data = [];

    public $file_upload;

    public $total_discount;
	public $sub_total;
	public $ppn_nilai;
	public $total;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'nomor',
                'no_penerimaan',
                'tgl_penerimaan',
                'list_data',
                'no_suratjalan',
                'diterima_oleh',
                'peg_penerima_nama',
                'peg_mengubah',
                'tgl_perubahan',
                'tgl_suratjalan',
                'no_faktur',
                'no_faktur_sementara',
                'ruanganpenerima_id',
                'peg_mengetahui',
                'peg_menyetujui',
                'file_upload',
                'catatan',
                'supplier_nama',
                'no_transaksi',
                'supplier_id',
                'mengetahui',
                'menyetujui',
                'is_verifikasi',
                'stat_penerimaan',
                'status_penerimaan',
                'total_discount',
                'sub_total',
                'ppn_nilai',
                'total',
            ],'safe'],
            [[
                'list_data',
                'tgl_suratjalan',
                'diterima_oleh',
            ],'required'],
            [['list_data'],'validData']
        ];
    }

    public function validData($params, $attributes)
    {
        if (!empty($this->list_data)) {
            $data = json_decode($this->list_data,true);
            if (!is_array($data)) return true;
            $validData = 0;
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $row => $attr) {
                        $rowKey = explode("-",$row)[1];

                        if (is_null($rowKey)) {
                            if (!empty($attr['qty_diterima'])) $validData++;
                            continue;
                        }

                        if (!empty($attr['qty_diterima'])) {
                            $validData++;
                        }

                        if (empty($attr['tgl_kadaluarsa']) && !empty($attr['is_kadaluarsa']) && !empty($attr['qty_diterima'])) {
                            $this->addError("{$key}date-{$row}","Tanggal kadaluarsa harus disi");
                        }

                        if (empty($attr['qty_diterima']) && !empty($attr['is_kadaluarsa']) && !empty($attr['tgl_kadaluarsa'])) {
                            $this->addError("{$key}qty-{$row}","Qty harus disi");
                        }

                        if (empty($attr['no_batch']) && !empty($attr['qty_diterima']) && !empty($attr['tgl_kadaluarsa']) ) {
                            $this->addError("{$key}no_batch-{$row}","No. Batch harus diisi");
                        }
                    }
                }
            }

            if ($validData == 0) {
                $this->addError("data_valid","Minimal 1 penerimaan Barang / Obat dan tidak boleh kosong");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_suratjalan' => 'Tanggal Surat Jalan',
            'no_suratjalan' => 'Nomor Surat Jalan',
            'peg_mengetahui' => 'Pegawai mengetahui',
            'peg_menyetujui' => 'Pegawai menyetujui',
            'no_faktur' => 'Nomor Faktur',
            'no_faktur_sementara' => 'Nomor Faktur Sementara',
        ];
    }

}
