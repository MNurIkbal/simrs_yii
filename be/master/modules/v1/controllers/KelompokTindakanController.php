<?php
/**
 * @author: arief saputra
 * @description: master untuk CRUD Kelompok Tindakan
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\KelompokTindakan;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;

class KelompokTindakanController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KelompokTindakan';
    /**
     * Untuk Kebutuhan Integerasi Odoo
     * @var array
     */
    public $messageBroker = [
        'create' => [
            'services' => [
                'Odoo' => [
                    'KelompokTindakan' => [
                        'last_insert' => true
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'KelompokTindakan' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'KelompokTindakan' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ],
    ];
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE","POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
         unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new KelompokTindakan;
        $query = $model::find();
        
        if(!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            if(isset($advancedFilter['status'])) {
                $is_active = $advancedFilter['status'];
                $query->andWhere(['is_active' => $is_active]);
                
            }
        }

        // $get = $request->get();
        // $result = $this->getData();
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        // $query = DocoRestActiveFilter::advancedFilter(new KelompokTindakan, $result);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new KelompokTindakan;
            if ($request->post()) {
                $model->attributes = $request->post('KelompokTindakanForm');
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokTindakanForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = KelompokTindakan::findOne($id);
            if ($request->post()) {
                $post = $request->post('KelompokTindakanForm');
                $model->attributes = $post;
                $model->is_active = $post['is_active'];
                $model->catatan = $post['catatan'];
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di ubah',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokTindakanForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }

            throw new Exception("Data Tidak Di Temukan");

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id)
    {
        return $this->getData(['kelompoktindakan_id'=>$id])->asArray()->one();
    }

    public function actionDetailTindakanKelompok($id)
    {
        $model = new DaftarTindakan;
        $query = $model::find();

        $query->where(['kelompoktindakan_id' => $id]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getData($filter = null)
    {
        $returnData = KelompokTindakan::find();

        if($filter) {
            $returnData->where($filter);
        }

        return $returnData;
    }

    protected $_title = "Kelompok Tindakan";
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new KelompokTindakan;
        $query = $model::find();
        $header = $footer = [];

        if(!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            if(isset($advancedFilter['kelompoktindakan_kode'])){
                $header['Kode Kelompok'] = $advancedFilter['kelompoktindakan_kode'];
            }
            if(isset($advancedFilter['kelompoktindakan_nama'])){
                $header['Nama Kelompok'] = $advancedFilter['kelompoktindakan_nama'];
            }
            if(isset($advancedFilter['kelompoktindakan_namalainnya'])){
                $header['Nama Lain Kelompok'] = $advancedFilter['kelompoktindakan_namalainnya'];
            }
            if(isset($advancedFilter['catatan'])){
                $header['Catatan'] = $advancedFilter['catatan'];
            }
            if(isset($advancedFilter['status'])) {
                $is_active = $advancedFilter['status'];
                $query->andWhere(['is_active' => $is_active]);
                $header['Status'] = ($is_active) ? 'Aktif' : 'Tidak Aktif';
            }
        }

        $query->orderBy($request->get('order'));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        
        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Kode Kelompok')] = $value['kelompoktindakan_kode'];
            $newValue[\Yii::t('app', 'Nama Kelompok')] = $value['kelompoktindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['kelompoktindakan_namalainnya'];
            $newValue[\Yii::t('app', 'Cyto')] = $value['kelompoktindakan_persencyto'];
            $newValue[\Yii::t('app', 'Diskon')] = $value['kelompoktindakan_persendiskon'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif';
            $newValue[\Yii::t('app', 'Catatan')] = $value['catatan'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die();
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tanggal# => tanggal sekarang
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Data Master Kelompok Tindakan';
        $get = $request->get();
        $model = new KelompokTindakan;
        $query = $model::find();
        if(!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            if(isset($advancedFilter['status'])) {
                $is_active = $advancedFilter['status'];
                $query->andWhere(['is_active' => $is_active]);
                
            }
        }
        
        $query->orderBy($request->get('order'));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y'),
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $dataProvider->getModels(),
                'title' => $title,
            ]),
        ];

        $print->Output();
    }

    public function actionDelete() 
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $model = KelompokTindakan::findOne($id);
            if ($model) {
                $model->is_deleted = true;
                $model->deleted_date = date('Y-m-d H:i:s');
                if($model->save(false)) {
                    DaftarTindakan::updateAll([
                        'kelompoktindakan_id' => null,
                    ], 'kelompoktindakan_id = '.$id.'');

                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Tindakan Berhasil',
                    ];
                }
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['text'] = "Gagal Menghapus Data";
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

    public function actionDeleteTindakan($id = null) 
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        
        try {
            $model = DaftarTindakan::findOne($request->post('id'));
            $model->kelompoktindakan_id = null;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Tindakan Berhasil',
                ];
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['text'] = "Gagal Menghapus Data";
                $result['text'] = $model->getErrors();
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
}