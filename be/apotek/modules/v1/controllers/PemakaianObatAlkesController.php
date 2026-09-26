<?php

/**
** @author yaya
**/
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoPrint;
use app\modules\v1\models\PemakaianObat;
use app\modules\v1\models\PemakaianObatDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\InformasiPemakaianObatalkesView;
use app\modules\v1\models\InfoPemakaianObatAlkesDetailView;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use Doco\components\DocoHelpers;

class PemakaianObatAlkesController extends DocoActiveController
{
    public $modelClass = '';
    const FEFO = 'FEFO';
    const FIFO = 'FIFO';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new PemakaianObat;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSave()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $dataJson = $request->post('data',"{}");
            $jwt = Yii::$app->jwt;
            $id_pegawai = !empty($jwt->user->pegawai_id) ? $jwt->user->pegawai_id : null;
            $data = json_decode($dataJson,true);
            // Insert Ke teransakasi pemakaian obat

            $ruangan_id = $request->post('ruangan_id');
            $obatalkes_ids_not_in_stock = LogicStokObatAlkes::validateStock($ruangan_id, $data);
            if (count($obatalkes_ids_not_in_stock) > 0) {
                // return $this->responseJson(422, 'Terdapat stok obat yang tidak mencukupi', ['obatalkes_ids' => $obatalkes_ids_not_in_stock]);
                return [
                    'data' => ['obatalkes_ids_not_in_stock' => $obatalkes_ids_not_in_stock],
                    'message' => 'Terdapat stok obat yang tidak mencukupi',
                    'status' => 422
                ];

            }

            $tanggalPemakaian = $request->post("tanggal_pemakaian", date('d-m-Y H:i:s'));
            $model = new PemakaianObat;
            $model->pegawai_id = $id_pegawai;
            $model->ruangan_id = $request->post('ruangan_id');
            $model->tglpemakaianobat = date('Y-m-d H:i:s', strtotime($tanggalPemakaian));
            $model->nopemakaian_obat = 'by_trigered';
            $model->untukkeperluan_obat = 'belum tersedia';
            if ($model->validate() && $model->save()) {
                $dataInsert = [];
                $idParent = $model->pemakaianobat_id;
                foreach ($data as $key => $value) {
                    $dataInsert[] = [
                        'satuankecil_id' => $value['satuankecil_id'],
                        'satuanbesar_id' => $value['satuanbesar_id'],
                        'pemakaianobat_id' => $idParent,
                        'obatalkes_id' => $key,
                        'qty_satuanpakai' => $value['permintaan'],
                        'harga_satuanpakai' => $value['harga_netto'],
                        'harganetto_satuanpakai' => $value['harga_netto'],
                        'ket_obatpakai' => $value['ket_obatpakai'],
                        'jumlah_input' => $value['jumlah_input'],
                    ];
                }
                PemakaianObatDetail::batchInsert($dataInsert);
                // End Insert ke pemakaianobatalkes_t
                $tanggalBerlaku = date('Y-m-d');
                // Mencari Metode
                $konfig = $connection->createCommand("
                    SELECT metodeantrian FROM konfigfarmasi_k
                    WHERE tglberlaku >= '{$tanggalBerlaku}'
                    AND konfigfarmasi_aktif = true
                    AND is_active = true
                ")->queryOne();
                // Select ulang ke Detail pemakaian berdasarkan id yang di insert yang di atas
                $detailTrans = $connection->createCommand("
                    SELECT * FROM pemakaianobatdetail_t 
                    WHERE pemakaianobat_id = {$idParent}
                ")->queryAll();
                // Mencari Metode dengan nilai default FEFO
                $currentMetode = LogicStokObatAlkes::FEFO;
                if ($konfig) {
                    $currentMetode = isset($konfig['metodeantrian']) 
                                        ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                }

                // Execute By Condition
                if ($currentMetode === LogicStokObatAlkes::FEFO) {
                   $methode = LogicStokObatAlkes::methodeFEFO($detailTrans,$tanggalPemakaian);
                } else {
                   $methode = LogicStokObatAlkes::methodeFIFO($detailTrans,$tanggalPemakaian);
                }
                $transaction->commit();
                return $model->pemakaianobat_id;
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
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

    /**
    * @controller actionCetak
    * @attribute #table_data# => table
    * @attribute #tgl_pemakaian# => tanggal pemakaian
    * @attribute #no_pemakaian# => nomor pemakaian
    **/
    public function actionCetak()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $getHeader = InformasiPemakaianObatalkesView::find()->where(['pemakaianobat_id'=>$id])->one();
            $getDetail = InfoPemakaianObatAlkesDetailView::find()->where(['pemakaianobat_id'=>$id])->asArray()->all();
            $print = new DocoPrint();
            $print->attributes = [
                '#table_data#' => $this->renderPartial('cetak',['data'=>$getDetail]),
                '#tgl_pemakaian#' => isset($getHeader['tglpemakaianobat']) ?  date('d M Y H:i:s', strtotime($getHeader['tglpemakaianobat'])) : '' ,
                '#no_pemakaian#' => isset($getHeader['nopemakaian_obat']) ?  $getHeader['nopemakaian_obat'] : '' ,
                '#ruangan#' => isset($getHeader['ruangan_nama']) ? strtoupper($getHeader['ruangan_nama']) : ''
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
