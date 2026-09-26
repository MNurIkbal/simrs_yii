<?php

namespace app\modules\gudang\models;

use Yii;
use app\components\DocoHelpers;

class ReturFormBarang extends \yii\base\Model
{

    public $no_penerimaan;
    public $no_faktur;
    public $supplier_nama;
    public $returpenerimaanbarang_id;
    public $penerimaanbarang_id;
    public $no_returpenerimaanbarang;
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
                "returpenerimaanbarang_id",
                "penerimaanbarang_id",
                "no_returpenerimaanbarang",
                "tgl_retur",
                "pegawairetur_id",
                "alasan_retur",
                "pegawai_retur",
                "supplier_id",
                "detail_retur",
            ],'safe'],
            [["detail_retur"], 'required'],
            [['detail_retur'], 'validasiDetail'],
        ];
    }

    public function validasiDetail($params, $attributes)
    {
        if (!empty($this->detail_retur)) {
            $data = json_decode($this->detail_retur, true);

            $dataValid = 0;
            foreach ($data as $key => $attr) {
                if(empty($attr["qty_retur"]))
                {
                    DocoHelpers::multipleParseError($this, "Qty retur tidak boleh kurang dari 1", "qty_retur", $attr["penerimaanbarangdetail_id"]);
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
           "no_returpenerimaanbarang" => "No. Retur Penerimaan" ,
           "tgl_retur" => "Tanggal Retur" ,
           "alasan_retur" => "Alasan Retur" ,
           "pegawairetur_id" => "Pegawai Retur" ,
           "returpenerimaanbarang_id" => "" ,
           "detail_retur" => "" ,
        ];
    }

}
