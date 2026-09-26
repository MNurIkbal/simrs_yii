<?php

/**
 * @author : Asri Nurul M
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfProduksiObat;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoProduksiObatAlkesDetailView;
use app\modules\v1\models\KonfigFarmasi;
use Doco\components\DocoConstants;

class GetAlertHargaAction extends Action {
    public function run() {
        try {
            $result = KonfigFarmasi::find()->asArray()->one();
            $data = isset($konfig['hargaygdigunakan']) && $konfig['hargaygdigunakan'] == DocoConstants::HARGA_FIX_RATE ? false : true;
            $hasil = [];
            if($data){
                $request = Yii::$app->request;
                $transaksi_id = $request->get('id', null);
                $tipe = $request->get('tipe', null);

                $model = new InfoProduksiObatAlkesDetailView;
                $query = $model::find()->where([
                    'produksiobatalkes_id' => $transaksi_id
                ])->andWhere('harganetto_awal <> harganetto_baru');

                $hasil = $query->asArray()->all();
            }
            return $hasil;
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
