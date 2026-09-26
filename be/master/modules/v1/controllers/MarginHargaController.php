<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\GroupMargin;
use app\modules\v1\models\KonfigMargin;
use app\modules\v1\models\KonfigMarginDetail;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\KonfigMarginView;
use app\modules\v1\models\MarginKhusus;
use app\modules\v1\models\MarginKhususDetail;
use app\modules\v1\models\MarginKhususView;
use app\modules\v1\cache\Cache;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class MarginHargaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KonfigMargin';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new GroupMargin;

            $query = $model->find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['groupmargin_nama' => SORT_ASC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = null;
            $request = Yii::$app->request;
            $header = $request->post('header');
            $dataJson = $request->post('detail',"{}");
            $data = json_decode($dataJson,true);

            // Insert Ke teransakasi margin detail
            $model = new KonfigMargin;
            $model->attributes = $header;
            $model->tgl_berlaku = date('Y-m-d', strtotime($header['tgl_berlaku']));
            if ($model->validate() && $model->save()) {
                $dataInsert = [];
                $idParent = $model->konfigmargin_id;
                if(!empty($data)) {
                    foreach ($data as $key => $value) {
                        $dataInsert[] = [
                            'konfigmargin_id' => $idParent,
                            'harga_min' => $value['harga_min'],
                            'harga_max' => $value['harga_max'],
                            'margin' => str_replace(',', '.', $value['margin']),
                        ];
                    }
                }

                // Insert Ke teransakasi margin detail
                KonfigMarginDetail::batchInsert($dataInsert);
                $transaction->commit();
                return [
                    'message' => 'sukses',
                ];
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'KonfigMarginForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateStatus()
    {
        try {
            $request = Yii::$app->request;
            $model = KonfigMargin::findOne($request->get('id'));
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KonfigMarginForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            else{
                throw new \Exception('Data Tidak Di Temukan');
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateData()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = null;
            $request = Yii::$app->request;
            $header = $request->post('header');
            $dataJson = $request->post('detail',"{}");

            $data = json_decode($dataJson, true);

            $id = $request->get('id');
            // Insert Ke teransakasi margin detail
            $model = KonfigMargin::findOne($id);
            $model->attributes = $header;
            $model->tgl_berlaku = date('Y-m-d', strtotime($header['tgl_berlaku']));
            if ($model->validate() && $model->save()) {
                $dataInsert = [];
                $idParent = $model->konfigmargin_id;
                KonfigMarginDetail::deleteAll(['konfigmargin_id' => $idParent]);
                if(!empty($data)) {
                    foreach ($data as $key => $value) {
                        $dataInsert[] = [
                            'konfigmargin_id' => $idParent,
                            'harga_min' => $value['harga_min'],
                            'harga_max' => $value['harga_max'],
                            'margin' => str_replace(',', '.', $value['margin']),
                        ];
                    }
                }

                KonfigMarginDetail::batchInsert($dataInsert);
                $transaction->commit();
                return [
                    'message' => 'sukses',
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete($id)
    {

        (new KonfigMargin)->delete($id);
        (new KonfigMarginDetail)->delete([
            'konfigmargin_id' => $id
        ]);
        return [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
    }

    public function actionCheckTransaction($id)
    {
        $konfigMarginDetail = KonfigMarginDetail::find()
            ->where(['konfigmargin_id' => $id])->all();

        $konfigmargindetail_id = [];
        foreach ($konfigMarginDetail as $key => $value) {
            $konfigmargindetail_id[] = $value['konfigmargindetail_id'];
        }

        $check = ObatAlkesPasien::find()
            ->where([
                'IN', 'konfigmargindetail_id', $konfigmargindetail_id
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $title = 'Margin Harga';
        try{

            $perda_margin = '';
            $model = new KonfigMargin;
            $queries = $model->find(false);
            $queries = DocoRestActiveFilter::advancedFilter($model, $queries);
            $queries->orderby(['tgl_berlaku' => SORT_DESC]);
            // $queries->joinWith(['konfigMarginDetail']);
            $queries->joinWith([
                'konfigMarginDetail' => function ($query) {
                   $query->select(['konfigmargindetail_k.konfigmargindetail_id',
                                    'konfigmargindetail_k.konfigmargin_id',
                                    'konfigmargindetail_k.harga_min',
                                    'konfigmargindetail_k.harga_max',
                                    'konfigmargindetail_k.margin',
                                    ]);
                   $query->orderby(['harga_min' => SORT_ASC]);
                }
            ]);
            $resultQry = $queries->asArray()->all();
            $resData = [];
            foreach ($resultQry as $key => $value) {

                $resDetail = [];
                if( count($value['konfigMarginDetail']) == 0  ){
                    $resData[] = [
                                     'perda_margin' => $value['perda_margin'],
                                     'tgl_berlaku'  => $value['tgl_berlaku'],
                                     'harga_min'    => 0,
                                     'harga_max'    => 0,
                                     'margin'       => 0,
                                ];
                }else{
                    foreach ($value['konfigMarginDetail'] as $k => $v) {
                        $resData[] = [
                                         'perda_margin' => $value['perda_margin'],
                                         'tgl_berlaku'  => $value['tgl_berlaku'],
                                         'harga_min'    => $v['harga_min'],
                                         'harga_max'    => $v['harga_max'],
                                         'margin'       => $v['margin'],
                                    ];
                    }
                }
            }

            // $model = new KonfigMarginView;
            $query = $model::find();
            if(isset($_GET['advanced-filter'])) {
                $advancedFilters = $_GET['advanced-filter'];
                if(isset($advancedFilters['perda_margin'])) {
                    $perda_margin = $advancedFilters['perda_margin'];
                    $query->andWhere(['ILIKE', 'perda_margin', $perda_margin]);
                }
            }

            $header = [Yii::t('app', 'Nama') => $perda_margin];
            $result = [];
            foreach ($resData as $key => $value) {
                $newValue = [];
                $newValue[\Yii::t('app', 'Nama')] = $value['perda_margin'];
                $newValue[\Yii::t('app', 'Mulai Berlaku')] = date('d-M-Y', strtotime($value['tgl_berlaku']));
                $newValue[\Yii::t('app', 'Harga Min (Rp.)')] = $value['harga_min'];
                $newValue[\Yii::t('app', 'Harga Max (Rp.)')] = $value['harga_max'];
                $newValue[\Yii::t('app', 'Margin (%)')] = $value['margin'];
                $result[$key] = $newValue;
            }

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Nama' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetAttributes($id)
    {
        $header = KonfigMargin::find()->where([
            'konfigmargin_id' => $id
        ])->one();
        $detail = KonfigMarginDetail::find()->where([
            'konfigmargin_id' => $id
        ])->all();
        $data = GroupMargin::find()
                    ->andWhere([
                        'is_deleted' => false,
                        'is_active' => true
                    ])
                    ->orderBy(['groupmargin_id' => SORT_ASC])
                    ->asArray()->all();
        $items = ArrayHelper::map($data, 'groupmargin_id', 'groupmargin_nama');
        return [
            'header' => $header,
            'detail' => $detail,
            'group_margin' => $items,
        ];
    }

    //-------------------------- group margin -------------------------------------
    public function actionGroupMargin()
    {
        try {
            $request = Yii::$app->request;
            $model = new GroupMargin;

            $query = $model->find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            // $query->orderby(['groupmargin_nama' => SORT_ASC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGroupMarginCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = null;
            $request = Yii::$app->request;
            $header = $request->post('header');

            // Insert Ke teransakasi margin detail
            $model = new GroupMargin;
            $model->groupmargin_nama = $header['groupmargin_nama'];
            $model->groupmargin_kode = $header['groupmargin_kode'];
            $model->is_discount = $header['is_discount'];
            if ($model->validate() && $model->save()) {
                $transaction->commit();
                return [
                    'message' => 'sukses',
                ];
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'GroupMarginForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGroupMarginDelete($id)
    {
        $konfigMargin = KonfigMargin::find()
            ->where(['groupmargin_id' => $id])->count();

        if ($konfigMargin == 0) {
            $delete = (new GroupMargin)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        }
        return [
            'status' => 422,
            'title' => 'Proses Gagal !',
            'text' => 'Data sudah di gunakan'
        ];
    }

    //-------------------------- margin harga obat ---------------------------------
    public function actionMarginHargaObat()
    {
        try {
            $request = Yii::$app->request;
            $_GET['expand'] = $request->get('expand', 'active_margin, groupmargin_m');
            $model = new KonfigMargin;

            $query = $model->find()->joinWith(['groupmargin' => function($query){
                $query->from('groupmargin_m');
            }]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDetailMarginHargaObat()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = new KonfigMarginDetail;
        $query = $model::find()->where(['konfigmargin_id' => $id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListGroupMargin($id = null)
    {
        $data = GroupMargin::find()->where(['is_deleted' => false
                                            ,'is_active'=>true])
                                ->orderBy(['groupmargin_id' => SORT_ASC])
                                ->asArray()->all();
        $items = ArrayHelper::map($data, 'groupmargin_id', 'groupmargin_nama');
        $kelasPelayanan = Cache::getKelasPelayanan();
        $jenisObat = Cache::getJenisObatAlkes();

        $header = $detail = [];
        if (!empty($id)) {
            $header = KonfigMargin::find()->where([
                'konfigmargin_id' => $id
            ])->asArray()->one();

            $detail = KonfigMarginDetail::find()->where([
                'konfigmargin_id' => $id
            ])->asArray()->all();
        }

        return [
            'group_margin' => $items,
            'kelas_pelayanan' => $kelasPelayanan,
            'header' => $header,
            'detail' => $detail,
            'jenis_obat' => $jenisObat
        ];

    }

    //-------------------------- margin khusus ---------------------------------
    public function actionCreateMarginKhusus() {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = null;
            $request = Yii::$app->request;
            $header = $request->post('header');
            $dataJson = $request->post('detail',"{}");
            $data = json_decode($dataJson, true);

            $model = new MarginKhusus;
            $model->nama = $header['nama'];
            $model->perda = $header['perda'];
            $model->mulai_berlaku = date('Y-m-d', strtotime($header['mulai_berlaku']));
            if ($model->validate() && $model->save()) {
                $dataInsert = [];
                $idParent = $model->marginkhusus_id;
                if(!empty($data)) {
                    foreach ($data as $key => $value) {
                        $dataInsert[] = [
                            'marginkhusus_id' => $idParent,
                            'jenisobat_id' => $value['jenisobat_id'],
                            'margin' => str_replace(',', '.', $value['margin']),
                        ];
                    }
                }

                // Insert Ke transaksi margin khusus detail
                MarginKhususDetail::batchInsert($dataInsert);
                $transaction->commit();
                return [
                    'message' => 'sukses',
                ];
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'MarginKhususForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionMarginKhusus()
    {
        try {
            $request = Yii::$app->request;
            $model = new MarginKhusus;

            $_GET['expand'] = $request->get('expand', 'active_margin');
            $query = $model->find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionMarginKhususDetail($id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'jenisobatalkes_m');
        $model = new MarginKhususDetail;
        $query = $model->find()
                        ->joinWith(['jenisobatalkes' => function($query){
                                $query->from('jenisobatalkes_m');
                        }])->where(['marginkhusus_id' => $id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
