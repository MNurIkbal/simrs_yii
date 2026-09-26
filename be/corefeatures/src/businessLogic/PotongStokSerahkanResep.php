<?php

namespace SirsCore\businessLogic;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use SirsCore\features\FeatureTindakanBmhp;
use Doco\exceptions\ValidationException;
use app\modules\v1\businessLogic\ValidasiStok;
use app\modules\v1\models\ObatAlkes;
use Doco\Traits\ControllerHelperTrait;

class PotongStokSerahkanResep
{
    use ControllerHelperTrait;

    public function potongStokSerahkanObat($payloadStok) {
        $payload = $payloadStok;
        $potongStok = FeatureTindakanBmhp::stokObatAlkes($payload, false);
        ValidasiStok::serahkan($payload);

        $konfig = Yii::$app->db->createCommand("SELECT is_transaksiobat_0 FROM konfigfarmasi_k")->queryOne();
        $transaksi_obat_minus = isset($konfig['is_transaksiobat_0']) ? $konfig['is_transaksiobat_0'] : false;

        if(is_array($potongStok) && !$transaksi_obat_minus) {
            $obat = ObatAlkes::findOne($potongStok[0]);
            $obat_nama = isset($obat['obatalkes_nama']) ? $obat['obatalkes_nama'] : null;
            return $this->responseJson(422, 'Gagal potong stok obat', [
                'status' => 422,
                'message' => 'Gagal potong stok obat',
                'text' => "Stok obat {$obat_nama} tidak mencukupi",
                'list_obat_tidak_cukup' => $potongStok
            ]);
        }

        if(!$potongStok && !$transaksi_obat_minus) {
            return $this->responseJson(422, 'Proses pemotongan stok gagal');
        }
        return $this->responseJson(200, 'Proses potong stok berhasil.');
    }
}
