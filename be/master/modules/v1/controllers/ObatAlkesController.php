<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-05 11:02:53
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-21 10:48:17
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use app\modules\v1\models\ObatAlkesView;
use app\modules\v1\models\ObatAlkesDetailView;
use app\modules\v1\models\TambahStokOpnameObatFn;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\IntObatView;
use app\modules\v1\models\RuteObat;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\Services\Vendors\OdooService;

class ObatAlkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ObatAlkes';

    public $messageBroker = [
        'create' => [
            'services' => [
                'Odoo' => [
                    'Obat' => [
                        'last_insert' => true
                    ]
                ],
                'Sirs' => [
                    'ObatAkunting' => [
                        'last_insert' => true,
                        'state' => 'create'
                    ]
                ]
            ]
        ],
        'edit' => [
            'services' => [
                'Odoo' => [
                    'Obat' => [
                        'query_params' => ['id'],
                    ]
                ],
                'Sirs' => [
                    'ObatAkunting' => [
                        'query_params' => ['id'],
                        'state' => 'edit'
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'Obat' => [
                        'query_params' => ['id'],
                    ]
                ],
                'Sirs' => [
                    'ObatAkunting' => [
                        'query_params' => ['id'],
                        'state' => 'delete'
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Sirs' => [
                    'ObatAkunting' => [
                        'query_params' => ['id'],
                        'state' => 'edit'
                    ]
                ]
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["delete"] = ["POST", "GET"];
        $verbs["edit"] = ["PUT"];
        $verbs["view"] = ["GET"];
        $verbs["create"] = ["POST"];
        $verbs["get-stok"] = ["GET"];
        $verbs["get-list-obat-new-so"] = ["GET"];
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

    public function actionIndex()
    {
        $cacheDuration = 60 * 60;

        try {
            $request = Yii::$app->request;
//            $model = new ObatAlkesView;

            $dep = new \yii\caching\DbDependency();
            $dep->sql = "SELECT MAX(last_modified_date) FROM obatalkes_m WHERE obatalkes_m.is_deleted = false";

//            $query = ObatAlkesView::getDb()->cache(function($db) {
//                return ObatAlkesView::find();
//            }, $cacheDuration, $dep);

            $query = Yii::$app->db->cache(function () {
                return (new Query())
                ->select(new Expression("obatalkes_m.obatalkes_id, 
                obatalkes_m.obatalkes_nama, 
                jenisobatalkes_m.jenisobatalkes_id, 
                jenisobatalkes_m.jenisobatalkes_nama, 
                obatalkes_m.ven, fgetnamalookup(obatalkes_m.ven) AS ven_name,
                obatalkes_m.groupinacbg_id, obatalkes_m.lead_time, obatalkes_m.avg_usage, 
                obatalkes_m.min_order, obatalkes_m.max_order, obatalkes_m.nilai_ro, 
                fgetpersenmargin(obatalkes_m.harganetto) AS margin, konfigfarmasi_k.persenppn AS ppn, 
                konfigfarmasi_k.persen_diskon AS disc, obatalkes_m.hargaterakhir AS hn_last, 
                0 AS hn_last_margin, 0 AS hn_last_diskon, 0 AS hn_last_margin_diskon, 0 AS hn_last_ppn, 0 AS hargajual_last, 
                0 AS hn_max_margin, 0 AS hn_max_diskon, 0 AS hn_max_margin_diskon, 0 AS hn_max_ppn, 0 AS hargajual_max, 0 AS hn_min_margin, 0 AS hn_min_diskon, 
                0 AS hn_min_margin_diskon, 0 AS hn_min_ppn, 0 AS hargajual_min, 0 AS hn_avg_margin, 0 AS hn_avg_diskon, 0 AS hn_avg_margin_diskon, 0 AS hn_avg_ppn, 
                0 AS hargajual_avg, 0 AS hargaygdipakai, 0 AS selisih, 0 AS hn_margin, 0 AS hn_diskon, 0 AS hn_ppn,
                obatalkes_m.hargaminimum AS hn_min,
                obatalkes_m.hargamaksimum AS hn_max,
                obatalkes_m.hargaratarata AS hn_avg, 
                obatalkes_m.harganetto AS harganetto_ygdipakai,
                CASE 
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MAX'::text THEN obatalkes_m.hargamaksimum 
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MIN'::text THEN obatalkes_m.hargaminimum 
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'AVG'::text THEN obatalkes_m.hargaratarata 
                    ELSE obatalkes_m.hargaterakhir 
                END AS harga_sugesstion,
                obatalkes_m.satuankecil_id, satuan_kecil.satuanunit_nama AS satuankecil_nama, jenisobatalkes_m.group_jenisobat, obatalkes_m.obatalkes_kode, 
                obatalkes_m.satuanbesar_id, satuan_besar.satuanunit_nama AS satuanbesar_nama, obatalkes_m.is_active, obatalkes_m.is_deleted"))
                ->from('obatalkes_m')
                ->join('JOIN', 'jenisobatalkes_m', 'obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id')
                ->join('JOIN', 'konfigfarmasi_k', 'konfigfarmasi_k.is_deleted = false')
                ->join('LEFT JOIN', 'satuanunit_m satuan_kecil', 'obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id')
                ->join('LEFT JOIN', 'satuanunit_m satuan_besar', 'obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id')
                ->where(['obatalkes_m.is_deleted' => false]);
            }, $cacheDuration, $dep);

            $advancedFilters = $request->get('advanced-filter', []);
            $orderBy = $request->get('order');
//            Yii::error($advancedFilters);
//            Yii::error($request->get());
            if(isset($advancedFilters)){
                if (isset($advancedFilters['ven']) ) {
                    $query->andFilterWhere(['obatalkes_m.ven' =>  $advancedFilters['ven']]);
                }
                if (isset($advancedFilters['obatalkes_nama']) ) {
                    $query->andFilterWhere(['ilike', 'obatalkes_m.obatalkes_nama', $advancedFilters['obatalkes_nama']]);
                }
                if (isset($advancedFilters['obatalkes_kode']) ) {
                    $query->andFilterWhere(['ilike', 'obatalkes_m.obatalkes_kode', $advancedFilters['obatalkes_kode']]);
                }
                if (isset($advancedFilters['jenisobatalkes_id']) ) {
                    $query->andFilterWhere(['obatalkes_m.jenisobatalkes_id' => $advancedFilters['jenisobatalkes_id']]);
                }
            }
            if(isset($orderBy)) {
                $query->orderBy("obatalkes_m.".$orderBy);
            }
//            $query = DocoRestActiveFilter::advancedFilter($model, $query);
//            $raw = $query;
//            Yii::error($raw->createCommand()->sql);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }
    public function actionView($id)
    {
        $data = ObatAlkesDetailView::find()->where(['obatalkes_id'=>$id])->one();
        $default_ruteobat_id = DocoConstansId::actionGetId('default_ruteobat_id_is_oral');
        return [
            'data' => $data,
            'default_ruteobat_id' => $default_ruteobat_id
        ];
    }
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = new ObatAlkes;
            $model->attributes = $post;
            $model->hargajual = 0;
            // manufaktur
            if(!empty($post['manufacture_ids'])) {
                $listManufaktur = [];
                $manufactures = $post['manufacture_ids'];
                foreach ($manufactures as $key => $value) {
                    $Id = explode("_", $value);
                    $listManufaktur[] = [
                        'id' => $Id[0],
                        'text' => $Id[1]
                    ];
                }
                $model->manufacture_ids = json_encode($listManufaktur);
            }
            // supplier
            if(!empty($post['supplier_ids'])) {
                $listSupplier = [];
                $suppliers = $post['supplier_ids'];
                foreach ($suppliers as $key => $value) {
                    $Id = explode("_", $value);
                    $listSupplier[] = [
                        'id' => $Id[0],
                        'text' => $Id[1]
                    ];
                }
                $model->supplier_ids = json_encode($listSupplier);
            }
            // rute obat
            if (!empty($post['ruteobat_id'])) {
                $ruteobat = RuteObat::find()->where(['ruteobat_id' => $post['ruteobat_id']])->one();
                $model->ruteobat_id = $ruteobat->ruteobat_id;
                $model->is_oral = $ruteobat->is_oral;
            }
            // $model->kemasan_sedang = $post['kemasan_sedang'];
            $model->kemasan_besar = $post['kemasan_besar'];
            $model->nilai_ro = ($post['avg_usage'] * $post['lead_time']) + $post['minimalstok'];
            if(empty($post['satuanbesar_id'])){
                $model->satuanbesar_id = $post['satuankecil_id'];
                $model->kemasan_besar = 1;
            }
            if($model->validate() && $model->save()){
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                $oa_id = $model->obatalkes_id;
                $find = SatuanKonversi::find()->where(['obatalkes_id'=>$oa_id, 'satuanbesar_id'=>$model->satuanbesar_id,'satuankecil_id'=>$model->satuankecil_id])->asArray()->all();                
                try {
                    if(isset($model->satuanbesar_id) && $model->satuanbesar_id != $model->satuankecil_id) {
                        $datakonversi[0] = [
                            'satuanbesar_id'=>$model->satuanbesar_id,
                            'satuankecil_id'=>$model->satuankecil_id,
                            'nilai_konversi'=>$model->kemasan_besar,
                            'obatalkes_id'=>$oa_id,
                        ];
                    }
                    
                    if(!empty($model->satuansedang_id)) {
                        $datakonversi[1] = [
                            'satuanbesar_id'=>$model->satuansedang_id,
                            'satuankecil_id'=>$model->satuankecil_id,
                            // 'nilai_konversi'=>$model->kemasan_sedang,
                            'obatalkes_id'=>$oa_id,
                        ];
                    }
                    
                    $datakonversi[2] = [
                        'satuanbesar_id'=>$model->satuankecil_id,
                        'satuankecil_id'=>$model->satuankecil_id,
                        'nilai_konversi'=>1,
                        'obatalkes_id'=>$oa_id,
                    ];
                    // return $datakonversi;
                    SatuanKonversi::batchInsert($datakonversi);
                } catch (\yii\db\Exception $e) {
                    throw new \Exception("Data gagal diinputkan!");
                }
                
                /*
                ** change to message broker
                $dataObat = IntObatView::find(true)->where(['sync_id_api'=>'OBT'.$model->getPrimaryKey(),'keterangan_rekap'=>'INSERT'])->asArray()->one();
                (new OdooService)->createObat(['obat'=>$dataObat],function($data,$result)use($dataObat){
                    $idObat = ArrayHelper::getValue($dataObat,'id',0);
                    $uid = ArrayHelper::getValue($result,'ProcessUID',0);
                    Yii::$app->db->createCommand("
                        UPDATE obatalkes_r SET is_sending = true , id_sync_sercon = '{$uid}' WHERE id = '{$idObat}' AND keterangan_rekap = 'INSERT'
                    ")->execute();
                });
                */

                $transaction->commit();
                return true;
            }else{
                return ['meta-status'=>422, 'data'=>$model->getErrors()];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
        
    }

    public function actionEdit($id) {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = ObatAlkes::findOne($id);
        $satuankecil_id = $model->satuankecil_id;
        $satuanbesar_id = $model->satuanbesar_id;
         // manufaktur
        if(!empty($post['manufacture_ids'])) {
            $listManufaktur = [];
            $manufactures = $post['manufacture_ids'];
            foreach ($manufactures as $key => $value) {
                $Id = explode("_", $value);
                $listManufaktur[] = [
                    'id' => $Id[0],
                    'text' => $Id[1]
                ];
            }
            $model->manufacture_ids = json_encode($listManufaktur);
        }
        // supplier
        if(!empty($post['supplier_ids'])) {
            $listSupplier = [];
            $suppliers = $post['supplier_ids'];
            foreach ($suppliers as $key => $value) {
                $Id = explode("_", $value);
                $listSupplier[] = [
                    'id' => $Id[0],
                    'text' => $Id[1]
                ];
            }
            $model->supplier_ids = json_encode($listSupplier);
        }

        // rute obat
        if (!empty($post['ruteobat_id'])) {
            $ruteobat = RuteObat::find()->where(['ruteobat_id' => $post['ruteobat_id']])->one();
            $model->ruteobat_id = $ruteobat->ruteobat_id;
            $model->is_oral = $ruteobat->is_oral;
        }

        $model->attributes = $post;
        if(empty($post['satuanbesar_id']) && empty($satuanbesar_id)){
            $model->satuanbesar_id = $satuankecil_id;
            $model->kemasan_besar = 1;
        }
        $model->hargajual = 0;
        $model->nilai_ro = ($post['avg_usage'] * $post['lead_time']) + $post['minimalstok'];
        $model->satuankecil_id = $satuankecil_id;
        if($model->save()){
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
            $this->responseJson(200, DocoMessages::SUC_MESSAGE_UPDATED);
        } else {
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE, $model->errors);
        }
    }

    public function actionDelete($id){
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            $model_obatalkes = ObatAlkes::findOne($id);
            // $model_satuankonversi = SatuanKonversi::deleteAll('obatalkes_id = '.$id);
            $model_satuankonversi = SatuanKonversi::find()->where('obatalkes_id = '.$id)->all();
            foreach ($model_satuankonversi as $model) {
                $model->delete();
            }
            // $model_obatalkes->delete();
            if($model_obatalkes->delete() ){
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');

                /*
                ** change to message broker
                $dataObat = IntObatView::find(true)->where(['sync_id_api'=>'OBT'.$model_obatalkes->getPrimaryKey(),'keterangan_rekap'=>'UPDATE','is_sending'=>FALSE])->asArray()->one();
                (new OdooService)->editObat(['obat'=>$dataObat],function($data,$result)use($dataObat){
                    $idObat = ArrayHelper::getValue($dataObat,'id',0);
                    $uid = ArrayHelper::getValue($result,'ProcessUID',0);
                    Yii::$app->db->createCommand("
                        UPDATE obatalkes_r SET is_sending = true , id_sync_sercon = '{$uid}' WHERE id = '{$idObat}' AND keterangan_rekap = 'UPDATE'
                    ")->execute();
                });
                */
                
                $transaction->commit();
                return ['message' => 'success'];
            } else {
                return [
                    'status' => 422,
                    'data' => $model->errors
                ];
            }
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    public function dataProvider()
    {
        $model = new ObatAlkesView;
        $request = Yii::$app->request->get('advanced-filter');
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionDataObat()
    {
        $request = Yii::$app->request;
        $term = $request->post('term');
        $model = ObatAlkes::find();
        $model->select(['obatalkes_nama']);
        if($term){
            $model->where(['ILIKE','LOWER(obatalkes_nama)',$term]);
        }
        $model->groupBy(['obatalkes_nama']);
        return $model->asArray()->all();
    }
    public function actionDataSupplier()
    {
        $request = Yii::$app->request;
        $term = $request->post('term');
        $model = Supplier::find();
        $model->select(['supplier_id', 'supplier_nama']);
        if($term){
            $model->where(['ILIKE','LOWER(supplier_nama)',$term]);
        }
        return $model->asArray()->all();
    }

    public function actionCheckTransaction()
    {
        $request = Yii::$app->request;
        $obatalkes_id = $request->get('id');
        $check = StokObatAlkes::find()
            ->where([
                'IN', 'obatalkes_id', $obatalkes_id
            ])->all();

        if (empty($check)) {
            return [
                'status' => 200,
                'title' => 'Proses Berhasil !',
                'text' => 'Data belum ada Transaksi'
            ];
        } 
        return [
            'status' => 422,
            'title' => 'Proses Gagal !',
            'text' => 'Data sudah ada Transaksi'
        ];
    }

    public function actionUpdate() {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('id');
            $is_active = $request->post('is_active', 1);

            //check stock obat
            $model_stock = StokObatAlkes::find()->where(['obatalkes_id' => $obatalkes_id])->orderBy('stokobatalkes_id DESC')->one();
            $model = ObatAlkes::find()->where(['obatalkes_id' => $obatalkes_id])->one();
            if($model !== null) {
//                Yii::error($model_stock->stok_tersedia);
//                Yii::error($obatalkes_id);
//                Yii::error(Yii::$app->getUser()->getId());
                $model->is_active = $is_active;
                $model->last_modified_by = Yii::$app->getUser()->getId();
                $model->last_modified_date = date("Y-m-d H:i:s");
                if($is_active == 0) {//jika nonaktifkan
                    if($model_stock !== null){
                        if($model_stock->stok_tersedia == 0) {//hanya jika stock obat kosong
                            if($model->save(false)){
                                return ['status' => 200, 'message' => 'Data berhasil disimpan.'];
                            }else{
                                return ['status'=> 422, 'data'=>$model->errors, 'message' => 'Terjadi Kesalahan pada Server.'];
                            }
                        } else {
                            return ['status'=> 422, 'message' => 'Stock Obat Tidak Kosong.'];
                        }
                    }else{
                        if($model->save(false)){
                            return ['status' => 200, 'message' => 'Data berhasil disimpan.'];
                        }else{
                            return ['status'=> 422, 'data'=>$model->errors, 'message' => 'Terjadi Kesalahan pada Server.'];
                        }
                    }
                } else {//jika aktifkan
                    if($model->save(false)){
                        return ['status' => 200, 'message' => 'Data berhasil disimpan.'];
                    }else{
                        return ['status'=> 422, 'data'=>$model->errors, 'message' => 'Terjadi Kesalahan pada Server.'];
                    }
                }
            }
            return ['status'=> 422, 'message' => 'Obat Alkes Tidak Ditemukan.'];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetListObatNewSo() {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $ruangan_id = $request->get('ruangan_id', null);

        $modelObat = new TambahStokOpnameObatFn(['extParam' => [(int)$ruangan_id]]);
        $queryObat = $modelObat::find()
        ->select([
            'obatalkes_kode', 'obatalkes_nama', 'satuankeci as satuan_kecil','obatalkes_id','stok_sistem',
            'satuanbesar as satuan_besar', 'rakobat_nama','laci','uom','stok_saatini','stok_in','stok_out'
        ]);
        if (!empty($term)) {
            $queryObat->where(['like', 'LOWER(obatalkes_nama)', strtolower($term)])
            ->orWhere(['LIKE', 'LOWER(obatalkes_kode)', strtolower($term)]);
        }
        $queryObat->orderBy(['obatalkes_nama' => SORT_ASC]);

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $queryObat->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetStok() {
        $request = Yii::$app->request;
        $obatalkes_id = $request->get('obatalkes_id', null);
        $ruanganId = $request->get('ruangan_id', null);
        if(empty($obatalkes_id)) throw new \Exception("obatalkes_id tidak boleh kosong");

        $model = new TambahStokOpnameObatFn(['extParam' => [(int)$ruanganId]]);
        $model = $model::find()->where(['obatalkes_id' => $obatalkes_id])->one();
        return $model ? $model : ['obatalkes_id' => null,'qty_stok' => 0];
    }

    public function actionGetObat(){
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $model = new ObatAlkesDetailView;
        $modelObat = $model::find()->select(['obatalkes_nama','obatalkes_kode','obatalkes_id','satuankecil_id','satuan_kecil','satuanbesar_id','satuan_2 as satuan_besar']);

        if (!empty($term)) {
            $modelObat->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($term)])
            ->orWhere(['LIKE', 'LOWER(obatalkes_kode)', strtolower($term)]);
        }
        $modelObat->orderBy(['obatalkes_nama' => SORT_ASC]);

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $modelObat->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetDataSelect2()
    {
        $request = Yii::$app->request;
        $payload = $request->get('payload', []);

        $page = (int) ArrayHelper::getValue($payload, 'page', 1);
        $limit = (int) ArrayHelper::getValue($payload, 'limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $term = ArrayHelper::getValue($payload, 'term');
        $jenisObatAlkesId = ArrayHelper::getValue($payload, 'jenisobatalkes_id');
        $penjaminId = (int) ArrayHelper::getValue($payload, 'penjamin_id');
        $instalasiId = (int) ArrayHelper::getValue($payload, 'instalasi_id');
        $ruangan_id = ArrayHelper::getValue($payload, 'ruangan_id');

        $condition = $jenisObatAlkesId ? "o.jenisobatalkes_id = :jenisobatalkes_id AND" : "";
        $termCondition = $term ? "o.obatalkes_nama ILIKE :term AND" : "";
        $joinRuanganId = $ruangan_id ? "JOIN stokobatalkes_r sr on sr.obatalkes_id = o.obatalkes_id and sr.is_deleted is false" : ""; 
        $termRuanganId = $ruangan_id ? "sr.ruangan_id = :ruangan_id AND" : "";

        $sql = "
            WITH cek_akses AS ( SELECT COUNT ( * ) AS jumlah FROM restriction_akses_obat_mp
                WHERE penjamin_id = :penjamin_id AND instalasi_id = :instalasi_id AND is_active = TRUE AND is_deleted = FALSE ) SELECT
            o.obatalkes_id AS ID,
            o.obatalkes_nama AS TEXT
            FROM
            obatalkes_m o
            $joinRuanganId
            WHERE
            o.is_active = true and o.is_deleted = false AND
            $termCondition
            $condition
            $termRuanganId
            (
                o.obatalkes_id NOT IN ( SELECT DISTINCT rl.obatalkes_id FROM restriction_list_obat_mp rl WHERE rl.is_active = TRUE AND rl.is_deleted = FALSE )
                OR o.obatalkes_id IN (
                SELECT
                rl.obatalkes_id
                FROM
                restriction_list_obat_mp rl
                JOIN restriction_akses_obat_mp ra ON ra.restriction_obat_id = rl.restriction_obat_id
                WHERE
                ra.penjamin_id = :penjamin_id
                AND ra.instalasi_id = :instalasi_id
                AND ra.is_active = TRUE
                AND ra.is_deleted = FALSE
                AND rl.is_active = TRUE
                AND rl.is_deleted = FALSE
                )
            ) order by o.obatalkes_nama asc LIMIT :limit OFFSET :offset";

        $cmd = Yii::$app->db->createCommand($sql)
            ->bindValue(':penjamin_id', $penjaminId, \PDO::PARAM_INT)
            ->bindValue(':instalasi_id', $instalasiId, \PDO::PARAM_INT)
            ->bindValue(':ruangan_id', $ruangan_id, \PDO::PARAM_INT)
            ->bindValue(':limit', $limit + 1, \PDO::PARAM_INT)
            ->bindValue(':offset', ($page - 1) * $limit, \PDO::PARAM_INT);

        if ($jenisObatAlkesId) {
            $cmd->bindValue(':jenisobatalkes_id', $jenisObatAlkesId, \PDO::PARAM_INT);
        }

        if ($term) {
            $cmd->bindValue(':term', "%$term%");
        }

        $result = $cmd->queryAll();

        if (!empty($payload['is_others']) && !empty($payload['non_racikan'])) {
            $exists = array_filter($result, function($row) {
                return $row['id'] == 0;
            });
            if (empty($exists)) {
                $result[] = ['id' => '0', 'text' => 'OTHERS'];
            }
        }

        return $result;
    }
}
