<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Barang;
use app\modules\v1\models\SubKelompokBarang;
use app\modules\v1\models\KelompokBarang;
use app\modules\v1\models\KelompokBarangView;
use Doco\components\DocoPrint;

class KelompokBarangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\KelompokBarang';

    public $messageBroker = [
        'create' => [
            'services' => [
                'Odoo' => [
                    'KelompokBarang' => [
                        'last_insert' => true
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'KelompokBarang' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'KelompokBarang' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE", "POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new KelompokBarangView;
            $query = $model->find();

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

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

    public function actionView($id)
    {
        $query = KelompokBarang::findOne($id);

        return $query;
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $cekBarang = Barang::find()->where(['kelompokbarang_id' => $id])->count();
            $cekSubKelompok = SubKelompokBarang::find()->where(['kelompokbarang_id' => $id])->count();
            if($cekBarang > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Kelompok Barang, data sudah digunakan di master lain.";
            }
            elseif($cekSubKelompok > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Kelompok Barang, data sudah digunakan di master lain.";
            }
            else {
                $model = KelompokBarang::findOne($id);
                if ($model) {
                    $model->is_deleted = true;
                    $model->deleted_date = date('Y-m-d H:i:s');
                    $model->save();
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Kelompok Barang Berhasil',
                    ];
                } else {
                    $transaction->rollBack();
                    $result['status'] = 422;
                    $result['text'] = "Gagal Menghapus Data";
                }
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new KelompokBarang;
        $query = $model::find();
        $namaRs = $request->get('namaRs');
        $title = 'Kelompok Barang '.$namaRs;

        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['kelompokbarang_nama'])) {
                $kelompokbarang_nama = $advancedFilters['kelompokbarang_nama'];
                $query->andWhere(['ILIKE', 'kelompokbarang_nama', $kelompokbarang_nama]);
            }
            if(isset($advancedFilters['kelompokbarang_kode'])) {
                $kelompokbarang_kode = $advancedFilters['kelompokbarang_kode'];
                $query->andWhere(['ILIKE', 'kelompokbarang_kode', $kelompokbarang_kode]);
            }
            if(isset($advancedFilters['is_active'])) {
                $is_active = ($advancedFilters['is_active'] == 1) ? true : false;
                $query->andWhere(['is_active' => $is_active]);
            }
        }

        $header = [];
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Kelompok Barang')] = $value['kelompokbarang_nama'];
            $newValue[\Yii::t('app', 'Kode Kelompok Barang')] = $value['kelompokbarang_kode'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif' ;
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));

        // return $filePath;
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #title# => title
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $namaRs = $get['namaRs'];
        $title = 'Kelompok Barang '.$namaRs;
        $model = new KelompokBarang;
        $query = $model::find();

        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['kelompokbarang_nama'])) {
                $kelompokbarang_nama = $advancedFilters['kelompokbarang_nama'];
                $query->andWhere(['ILIKE', 'kelompokbarang_nama', $kelompokbarang_nama]);
            }
            if(isset($advancedFilters['kelompokbarang_kode'])) {
                $kelompokbarang_kode = $advancedFilters['kelompokbarang_kode'];
                $query->andWhere(['ILIKE', 'kelompokbarang_kode', $kelompokbarang_kode]);
            }
            if(isset($advancedFilters['is_active'])) {
                $is_active = ($advancedFilters['is_active'] == 1) ? true : false;
                $query->andWhere(['is_active' => $is_active]);
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];
        $print->Output();
    }
}