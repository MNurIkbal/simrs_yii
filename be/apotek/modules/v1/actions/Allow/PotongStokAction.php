<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */


namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use SirsCore\features\FeatureTindakanBmhp;
use Doco\exceptions\ValidationException;
use app\modules\v1\businessLogic\ValidasiStok;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\KonfigFarmasi;

class PotongStokAction extends Action {
	public function run() {
        $payload = Yii::$app->request->post('detail_obat', []);
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $potongStok = FeatureTindakanBmhp::stokObatAlkes($payload, false);
        ValidasiStok::serahkan($payload);

        $konfig = KonfigFarmasi::find()->one();
        $transaksi_obat_minus = $konfig->is_transaksiobat_0;

        if(is_array($potongStok) && !$transaksi_obat_minus) {
            $obat = ObatAlkes::findOne($potongStok[0]);
            $obat_nama = ArrayHelper::getValue($obat,'obatalkes_nama');
            $transaction->rollBack();
            return $this->controller->responseJson(422, 'Gagal potong stok obat', [
                'status' => 422,
                'message' => 'Gagal potong stok obat',
                'text' => "Stok obat {$obat_nama} tidak mencukupi",
                'list_obat_tidak_cukup' => $potongStok
            ]);
        }

        if(!$potongStok && !$transaksi_obat_minus){
            $transaction->rollBack();
            return $this->controller->responseJson(422, 'Proses pemotongan stok gagal');
        }
        
        $transaction->commit();
        return $this->controller->responseJson(200, 'Proses potong stok berhasil');
    }
}
