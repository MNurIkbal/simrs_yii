<?php

/**
 * @Author: Rizqi Fitrianto
 * @edited : Yaya
 * @Date:   2018-02-26 11:15:13
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-08 11:53:08
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\models\InformasiPemakaianObatalkesView;
use app\modules\v1\models\InformasiPemakaianObatalkesDetailView;
use app\modules\v1\models\PemakaianObat;
use app\modules\v1\models\PemakaianObatDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\controllers\AllowController;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;

class InfPemakaianObatalkesController extends DocoActiveController
{
    public $modelClass = InformasiPemakaianObatalkesView::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex() {
        $model = new InformasiPemakaianObatalkesView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpemakaianobat'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemakaianobat']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemakaianobat']);
            }
        }

        $query->andWhere(['between', 'tglpemakaianobat', $start, $end]);  
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    
    /**
    ** @var $id integer [pemakaianobat_id]
    **/
    public function actionHapusPemakaian($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;

        $data = [
            'is_deleted'=> true, 
            'deleted_date'=> date('Y-m-d H:i:s'), 
            'last_modified_date'=> date('Y-m-d H:i:s'), 
            'deleted_by'=>$pegawai_id
        ];

        // Hapus pemakaianobat_t
        $pemakaian = (new PemakaianObat)->delete($id);

        // Hapus Detailnya
        // Sebelum delete tampung id_detailnya dulu
        $list = PemakaianObatDetail::find()->where([
                'pemakaianobat_id' => $id
        ])->all();
        
        $listOfDetail = [];
        foreach ($list as $value) {
            $listOfDetail[] = $value->pemakaianobatdetail_id;
        }

        $detail = (new PemakaianObatDetail)->delete([
            'pemakaianobat_id' => $id
        ]);

        $stokOa = StokObatAlkes::find()->where([
            'pemakaianobatdetail_id' => $listOfDetail
        ])->all();

        $listOfStokAsal = [];
        foreach ($stokOa as $key => $value) {
            if (!in_array($value->stokobatalkesasal_id, $listOfStokAsal)) {
                $listOfStokAsal[] = $value->stokobatalkesasal_id;
            }
        }

        $delStokOa = (new StokObatAlkes)->delete([
            'pemakaianobatdetail_id' => $listOfDetail
        ]);

        if ($listOfStokAsal) {
            $inCondition = "(" . implode(",", $listOfStokAsal) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokobatalkes_t SET stokoa_aktif = true, is_active = true
                WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition})
            ")->execute();
        }

        return "success";
    }

    public function actionSave($id)
    {
        // $detailTrans = Yii::$app->db->createCommand("
        //     SELECT * FROM pemakaianobatdetail_t 
        //     WHERE pemakaianobat_id = {$id}
        // ")->queryAll();
        // var_dump($detailTrans);
        // die();
        $dataParent = PemakaianObat::find()->with([
            'detail' => function ($query) {

            }
        ])->where([
            'pemakaianobat_id' => $id
        ])->asArray()->one();
        $request = Yii::$app->request;
        // return $dataParent;
        if (!empty($dataParent['detail'])) {
            try {
                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();
                $dataSebelum = [];
                foreach ($dataParent['detail'] as $value) {
                    $dataSebelum[$value['obatalkes_id']] = $value;
                }
                $dataJson = $request->post('data',"{}");
                $jwt = Yii::$app->jwt;
                $id_pegawai = !empty($jwt->user->pegawai_id) ? $jwt->user->pegawai_id : null;
                $data = json_decode($dataJson,true);
                // Insert Ke teransakasi pemakaian obat
                $tanggalPemakaian = $request->post("tanggal_pemakaian",date('d-m-Y'));
                $dataUpdate = $dataInsert = [];
                $expectId = [];
                foreach ($data as $key => $value) {
                    if (isset($dataSebelum[$key])) {
                        $row = $dataSebelum[$key];
                        $qtyKecil = $row['qty_satuanpakai'] + $value['permintaan'];
                        $qtyBesar = $row['jumlah_input'] + $value['jumlah_input'];
                        $row['qty_satuanpakai'] = $value['permintaan'];
                        $row['jumlah_input'] = $value['jumlah_input'];
                        $dataUpdate[] = $row;
                        $expectId[] = $row['pemakaianobatdetail_id'];
                        Yii::$app->db->createCommand("
                            UPDATE pemakaianobatdetail_t SET qty_satuanpakai = {$qtyKecil}, jumlah_input = {$qtyBesar} 
                            WHERE pemakaianobatdetail_id = {$row['pemakaianobatdetail_id']}
                        ")->execute();
                    } else {
                        $dataInsert[] = [
                            'satuankecil_id' => $value['satuankecil_id'],
                            'satuanbesar_id' => $value['satuanbesar_id'],
                            'pemakaianobat_id' => $idParent,
                            'obatalkes_id' => $id,
                            'qty_satuanpakai' => $value['permintaan'],
                            'harga_satuanpakai' => $value['harga_netto'],
                            'harganetto_satuanpakai' => $value['harga_netto'],
                            'ket_obatpakai' => 'alkes',
                            'jumlah_input' => $value['jumlah_input'],
                        ];
                    }
                }
                if ($dataInsert) {
                    PemakaianObatDetail::batchInsert($dataInsert);
                }
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
                $notCondition = "";
                if ($expectId) {
                    $notCondition = 'AND pemakaianobatdetail_id NOT IN (' . implode(",", $expectId) . ')';
                }
                $detailTrans = $connection->createCommand("
                    SELECT * FROM pemakaianobatdetail_t 
                    WHERE pemakaianobat_id = {$id} {$notCondition}
                ")->queryAll();
                $detailTrans = array_merge($detailTrans,$dataUpdate);
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
        \Yii::$app->response->statusCode = 200;
        return ['message' => 'Tidak ada data yang di eksekusi'];
    }

    public function actionGetDetailPemakaian($id)
    {

        $model = new InformasiPemakaianObatalkesDetailView;
        $query = $model::find()->where([
            'pemakaianobat_id' => $id
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionBeforePrint($id)
    {
        $header = InformasiPemakaianObatalkesView::find()->where([
            'pemakaianobat_id' => $id
        ])->one();

        return $header;
    }

    /**
    * @controller actionPrint
    * @attribute #table_pemakaian# => Untuk Menampilkan Tabel pemakaian obat alkes
    * @attribute #no_pemakaian# => untuk menampilkan nomor pemakaian
    * @attribute #tgl_pemakaian# => untuk menampilkan Tanggal pemakaian
    * @attribute #ruangan_pemakaian# => untuk menampilkan Ruangan pemakaian
    **/

    public function actionPrint($id)
    {
        $request = Yii::$app->request;
        $print = new DocoPrint();

        $header = InformasiPemakaianObatalkesView::find()->where([
            'pemakaianobat_id' => $id
        ])->asArray()->one();

        $query = InformasiPemakaianObatalkesDetailView::find()->where([
            'pemakaianobat_id' => $id
        ])->asArray()->all();

        $tanggal = !empty($header['tglpemakaianobat']) ? date('d M Y', strtotime($header['tglpemakaianobat'])) : '';
        $no_pemakaianbarang = !empty($header['nopemakaian_obat']) ? $header['nopemakaian_obat'] : '';
        $ruangan_pemakai = !empty($header['ruangan_nama']) ? $header['ruangan_nama'] : '';

        $print->attributes = [
            '#table_pemakaian#' => $this->renderPartial('index',[
                'detail' => $query
            ]),
            '#no_pemakaian#' => $no_pemakaianbarang,
            '#tgl_pemakaian#' => $tanggal,
            '#ruangan_pemakaian#' => strtoupper($ruangan_pemakai),
        ];
        $print->Output();
        \Yii::$app->response->statusCode = 500;
        return ['message' => 'Tidak Ada Data yang harus di cetak'];
    }

    public function actionView($id)
    {
        $cacheKonvert = AllowController::actionSetCacheKonvertSatuan();
        $pemakaiObat = InformasiPemakaianObatalkesView::find()->where([
            'pemakaianobat_id' => $id
        ])->one();
        
        return [
            'pemakaian_obat' => $pemakaiObat,
            'cache_satuan' => $cacheKonvert,
        ];
    }
}
