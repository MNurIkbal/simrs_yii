<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\InfoPurchaseRequisition;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\InfoSatuanKonversi;
use Doco\components\DocoRestActiveFilter;

class GetDataObatAction extends Action {
    public function run() {
        $cacheDuration = 60*10;
        $ruangan_id = Yii::$app->request->get('ruangan_id',null);
        try{
            $data_obat_ruangan = Yii::$app->db->cache(function($db)use($ruangan_id){
                $query = "
                        SELECT
                            stokobatalkes_r.obatalkes_id,
                            obatalkes_m.obatalkes_nama,
                            obatalkes_m.obatalkes_namalain,
                            stokobatalkes_r.qty_dipesan,
                            stokobatalkes_r.qty_tersedia,
                            stokobatalkes_r.qty_sisa
                        FROM
                            stokobatalkes_r
                        JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
                        JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
                        WHERE stokobatalkes_r.ruangan_id = {$ruangan_id}
                        ORDER BY obatalkes_m.obatalkes_nama ASC";
                return $db->createCommand($query)->queryAll();
            }, $cacheDuration);

            return [
                'data'=>[
                    'obat_ruangan' => $data_obat_ruangan
                ]
            ];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage(),
                'data' =>[
                    'penjamin' => [],
                    'signa' => [],
                    'obat_ruangan' => []
                ]
            ];
        }
    }
}