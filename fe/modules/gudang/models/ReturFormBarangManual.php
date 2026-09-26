<?php

namespace app\modules\gudang\models;

use Yii;
use app\components\DocoHelpers;

class ReturFormBarangManual extends \yii\base\Model
{

    public $alasan_retur;
    public $pegawairetur_id;
    public $tgl_retur;
    public $ruanganpenerima_id;

    public $detail_retur = [];
    public function rules()
    {
        return [
            [[
                "pegawairetur_id",
                "tgl_retur",
                "ruanganpenerima_id",
            ],'safe'],
            [[
                "detail_retur",
                "alasan_retur",
            ], 'required'],
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
                    DocoHelpers::multipleParseError($this, "Qty retur tidak boleh kurang dari 1", "qty_retur", $attr["penerimaansuppdetail_id"]);
                }else{
                    $dataValid++;
                }
            }
        }
    }

    public function attributeLabels()
    {
        return [
           "alasan_retur" => "Alasan Retur" ,
           "detail_retur" => "" ,
        ];
    }

}
