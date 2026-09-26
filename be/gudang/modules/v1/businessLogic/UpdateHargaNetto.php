<?php

namespace app\modules\v1\businessLogic;

/**
** @author anggoro
**/

use Yii;

use app\modules\v1\models\ObatAlkes;

class UpdateHargaNetto
{
    const KET_PENERIMAANMANUAL = '624';
    const KET_ADJUSTMENOBAT = '625';
    const KET_MANUAL = '626';
    const KET_PENERIMAANSUPP = '627';

    public function UpdateHargaObat($data, $transaksi = null)
    {
        $model = new ObatAlkes;
        if ($transaksi == "adjustment") {
            $ket_ubah_harga = UpdateHargaNetto::KET_ADJUSTMENOBAT;
        }
        elseif ($transaksi == "penerimaan_manual") {
            $ket_ubah_harga = UpdateHargaNetto::KET_PENERIMAANMANUAL;
        }
        elseif ($transaksi == "penerimaaan_po") {
            $ket_ubah_harga = UpdateHargaNetto::KET_PENERIMAANSUPP;
        }
        else{
            return false;
        }
        foreach ($data["list_obat"] as $obatbaru) {
            if (isset($obatbaru["obatalkes_id"]) && isset($obatbaru["harga_sugesstion"]) && isset($ket_ubah_harga)) {
                $obat = $model->find()->where([
                    "obatalkes_id" => $obatbaru["obatalkes_id"]
                ])->one();
                $obat->harganetto = (float) $obatbaru["harga_sugesstion"];
                $obat->ket_ubah_harga = $ket_ubah_harga;
                $obat->save();
            }
        }
        return true;
    }
}