<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\PengadaanComponent;
use app\modules\v1\models\KontrakSupplier;
use app\modules\v1\models\KontrakSupplierDetail;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\SatuanKonversiView;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class SaveAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $header = $request->post('header');
        $list_obat = $request->post('list_obat');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $model = new KontrakSupplier;
            $model->attributes = $header;
            if(!$model->save()) throw new \Exception("Error Processing Request", 1);

            $arr_obatalkes_id = array_column($list_obat, 'obatalkes_id');
            $arr_obatalkes = ObatAlkes::find()->where([
                'IN', 'obatalkes_id', $arr_obatalkes_id
            ])->asArray()->all();
            $obatalkes = [];

            foreach ($arr_obatalkes as $key => $value) {
                $obatalkes[$value['obatalkes_id']] = [
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'obatalkes_kode' => $value['obatalkes_kode']
                ];
            }

            foreach ($list_obat as $key => $value) {
                $satuankonversi = SatuanKonversiView::find()->where([
                    'obatalkes_id' => $value['obatalkes_id'],
                    'satuanbesar_id' => $value['uom_id']
                ])->one();

                $detail[] = [
                    'kontraksupplier_id' => $model->kontraksupplier_id,
                    'obatalkes_id'  => $value['obatalkes_id'],
                    'nama_obat'     => @$obatalkes[$value['obatalkes_id']]['obatalkes_nama'],
                    'kode_obat'     => @$obatalkes[$value['obatalkes_id']]['obatalkes_kode'],
                    'qty_min'       => $value['qty_minimum'],
                    'satuankecil_id'=> $satuankonversi['satuankecil_id'],
                    'satuankonv1_id'=> $satuankonversi['satuanbesar_id'],
                    'harga'         => $value['harga_order'],
                    'pengurang'     => $value['pengurang'],
                    'penambah'      => $value['penambah'],
                    'total_harga'   => $value['total_harga']
                ];
            }

            KontrakSupplierDetail::batchInsert($detail);
            $transaction->commit();

            return [
                'message' => 'OK',
                'data' => [
                    'id' => $model->getPrimaryKey(),
                    'no_kontrak_supplier' => $model->kontraksupplier_no
                ]
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
