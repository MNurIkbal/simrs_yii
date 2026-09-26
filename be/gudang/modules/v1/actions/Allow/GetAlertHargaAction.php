<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\HargaNettoObatView;
use app\modules\v1\models\KonfigFarmasi;
use Doco\components\DocoConstants;

class GetAlertHargaAction extends Action {
    protected function beforeRun()
    {
        try {
            $result = KonfigFarmasi::find()->joinWith([
                'editedBy' => function($query){
                    $query->select([
                        'loginpemakai_k.loginpemakai_id',
                        'loginpemakai_k.pegawai_id',
                    ])->joinWith([
                        'pegawai' => function($query){
                            $query->select([
                                'pegawai_m.pegawai_id',
                                'pegawai_m.nama_pegawai'
                            ]);
                        }
                    ]);
                },
                'penjamin' => function($query){
                    $query->select([
                        'penjamin_v.carabayar_nama',
                        'penjamin_v.penjamin_nama'
                    ]);
                }
            ]);

            $konfig = $result->asArray()->one();
            return isset($konfig['hargaygdigunakan']) && $konfig['hargaygdigunakan'] == DocoConstants::HARGA_FIX_RATE ? false : true;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function run() {
        try {
        	$request = Yii::$app->request;
        	$transaksi_id = $request->get('id', null);
        	$tipe = $request->get('tipe', null);

            $model = new HargaNettoObatView;
            $query = $model::find()->where([
            	'transaksi_id' => $transaksi_id,
            	'tipe' => $tipe
            ])->andWhere('harga_netto_sekarang <> harga_disarankan');

            return $query->asArray()->all();
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
