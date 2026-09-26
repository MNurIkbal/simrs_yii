<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @last modified by: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @date : 2020-09-21 10:41:00
 */

namespace app\modules\v1\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\KontrakSupplier;
use app\modules\v1\models\KontrakSupplierDetail;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\SatuanKonversiView;
use Doco\components\DocoRestActiveFilter;

class UpdateAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $header = $request->post('header');
        $details = $request->post('details');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $new_ids = [];

        try {
            $model = KontrakSupplier::find()
                        ->where(['kontraksupplier_id' => $header['kontraksupplier_id']])
                        ->one();

            if(is_null($header))
                throw new \Exception("Data dengan id:{$header['kontraksupplier_id']} tidak ditemukan", 1);

            foreach ($details as $key => $value) {
                if(isset($value['kontraksupplierdetail_id'])) {
                    $new_ids[] = $value['kontraksupplierdetail_id'];
                }
            }

            $rows = KontrakSupplierDetail::find()
                        ->where(['not in', 'kontraksupplierdetail_id', $new_ids])
                        ->andWhere(['kontraksupplier_id' => $header['kontraksupplier_id']])
                        ->all();

            foreach ($rows as $row) {
                $row->delete();
            }

            $model->attributes = $header;
            if(!$model->save()) throw new \Exception("Error Processing Request", 1);

            $arr_obatalkes_id = array_column($details, 'obatalkes_id');
            $arr_obatalkes = ObatAlkes::find()->where([
                'IN', 'obatalkes_id', $arr_obatalkes_id
            ])->asArray()->all();
            foreach ($arr_obatalkes as $key => $value) {
                $obatalkes[$value['obatalkes_id']] = [
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'obatalkes_kode' => $value['obatalkes_kode']
                ];
            }
            foreach ($details as $key => $value) {
                if(!isset($value['kontraksupplierdetail_id'])) {
                    $satuankonversi = SatuanKonversiView::find()->where([
                        'obatalkes_id' => $value['obatalkes_id'],
                        'satuanbesar_id' => $value['uom_id']
                    ])->one();
                    $model_detail = new KontrakSupplierDetail;
                    $model_detail->kontraksupplier_id = $header['kontraksupplier_id'];
                    $model_detail->obatalkes_id = $value['obatalkes_id'];
                    $model_detail->nama_obat = $obatalkes[$value['obatalkes_id']]['obatalkes_nama'];
                    $model_detail->kode_obat = $obatalkes[$value['obatalkes_id']]['obatalkes_kode'];
                    $model_detail->satuankecil_id = $satuankonversi['satuankecil_id'];
                    $model_detail->satuankonv1_id = $satuankonversi['satuanbesar_id'];
                    // Dipisah untuk kebutuhan updateTime partial
                    // Insert Harga Qty dsb.
                    $model_detail->qty_min = $value['qty_minimum'];
                    $model_detail->harga = $value['harga_order'];
                    $model_detail->pengurang = $value['pengurang'];
                    $model_detail->penambah = $value['penambah'];
                    $model_detail->total_harga = $value['total_harga'];
                    if(!$model_detail->save()) throw new \Exception("Error Processing Request", 1);
                } else {
                    $model_detail = KontrakSupplierDetail::find()
                        ->where(['kontraksupplierdetail_id' => $value['kontraksupplierdetail_id']])
                        ->one();
                    // Insert Harga Qty dsb. (only edited)
                    $isEdited = ArrayHelper::getValue($value, 'is_edited');
                    if ($isEdited) {
                        $model_detail->qty_min = $value['qty_minimum'];
                        $model_detail->harga = $value['harga_order'];
                        $model_detail->pengurang = $value['pengurang'];
                        $model_detail->penambah = $value['penambah'];
                        $model_detail->total_harga = $value['total_harga'];
                        if(!$model_detail->save()) throw new \Exception("Error Processing Request", 1);
                    }
                }
            }

            $transaction->commit();

            return [
                'message' => 'Data berhasil di update'
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
