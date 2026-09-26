<?php

namespace app\modules\gudang\models;

use Yii;
use app\components\DocoHelpers;

class ReturFormObat extends \yii\base\Model
{

    public $no_penerimaan;
    public $no_faktur;
    public $supplier_nama;
    public $returpenerimaanobat_id;
    public $penerimaanobat_id;
    public $no_returpenerimaanobat;
    public $tgl_retur;
    public $pegawairetur_id;
    public $alasan_retur;

    public $detail_retur = [];
    public function rules()
    {
        return [
            [[
                "no_penerimaan",
                "no_faktur",
                "supplier_nama",
                "returpenerimaanobat_id",
                "penerimaanobat_id",
                "no_returpenerimaanobat",
                "tgl_retur",
                "pegawairetur_id",
                "alasan_retur",
                "pegawai_retur",
                "supplier_id",
                "detail_retur",
            ],'safe'],
            [["detail_retur", "alasan_retur"], 'required'],
            [['detail_retur'], 'validasiDetail'],
            // [[
            //     'list_data',
            //     'no_suratjalan',
            //     'tgl_suratjalan',
            //     'diterima_oleh',
            // ],'required'],
        ];
    }

    public function validasiDetail($params, $attributes)
    {
        if (!empty($this->detail_retur)) {
            $data = json_decode($this->detail_retur, true);

            if (!is_array($data)) { return true; }

            $dataValid = 0;
            foreach ($data as $key => $attr) {
                if(empty($attr["qty_retur"]))
                {
                    DocoHelpers::multipleParseError($this, "Qty Retur harus diisi", "qty_retur", $attr["penerimaanobatdetail_id"]);
                }else{
                    $dataValid++;
                }
            }
        }
    }

    public function attributeLabels()
    {
        return [
           "no_penerimaan" => "No. Penerimaan" ,
           "no_faktur" => "No. Faktur" ,
           "supplier_nama" => "Nama Supplier" ,
           "no_returpenerimaanobat" => "No. Retur Penerimaan" ,
           "tgl_retur" => "Tanggal Retur" ,
           "alasan_retur" => "Alasan Retur" ,
           "pegawairetur_id" => "Pegawai Retur" ,
           "returpenerimaanobat_id" => "" ,
           "detail_retur" => "" ,
        ];
    }

}
