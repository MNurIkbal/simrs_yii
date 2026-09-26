<?php

namespace app\modules\v1\actions\InfProduksiObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\ObatAlkes;

class SaveUpdateHargaAction extends Action {
    public function run()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new ObatAlkes;
        foreach ($post as $obatbaru) {
            if (isset($obatbaru["obatalkes_id"]) && isset($obatbaru["harga_sugesstion"])) {
                $obat = $model->find()->where([
                    "obatalkes_id" => $obatbaru["obatalkes_id"]
                ])->one();
                $obat->harganetto = (float) $obatbaru["harga_sugesstion"];
                $obat->ket_ubah_harga = DocoConstants::PRODUKSI_OBAT;
                $obat->save();
            }
        }
        
        return [
            "status" => 200,
            "title" => "Proses Berhasil",
            "text" => "Proses penyimpanan harga berhasil"
        ];
    }
}