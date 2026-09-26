<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Edc;
use Doco\components\DocoHelpers;

class EdcController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Edc';
    const EDC_KODE = 'edclist_kode';
    const EDC_NAMA = 'edclist_namamesin';
    const STATUS = 'status';
    const MESSAGE = 'message';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new Edc;
        $query = $model::find()
            ->select(['edclist_m.edclist_id', 'edclist_m.edclist_kode', 
                'edclist_m.edclist_namamesin', 'edclist_m.edclist_bank', 
                'edclist_m.is_active', 'bank_m.nama_bank'])
            ->joinWith('bank')
            ->orderBy('edclist_m.edclist_namamesin');
        
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter[self::EDC_KODE])) {
                $edclist_kode = $advancedFilter[self::EDC_KODE];
                $query->andWhere(['ILIKE', self::EDC_KODE, $edclist_kode]);
            }
            if(isset($advancedFilter[self::EDC_NAMA])) {
                $edclist_namamesin = $advancedFilter[self::EDC_NAMA];
                $query->andWhere(['ILIKE', self::EDC_NAMA, $edclist_namamesin]);
            }
            if(isset($advancedFilter['nama_bank'])) {
                $nama_bank = $advancedFilter['nama_bank'];
                $query->andWhere(['edclist_bank' => $nama_bank]);
            }
            if(isset($advancedFilter['is_active'])) {
                $is_active = $advancedFilter['is_active'];
                $query->andWhere(['edclist_m.is_active' => $is_active]);
            }
        }

        return [
            'data' => $query->asArray()->all(),
            'count' => $query->count()
        ];
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new Edc;
            $post = $request->post();
            $model->attributes = $post;
            if ($model->validate() && $model->save()) {
                $status = [
                    self::STATUS => 200,
                    'title' => 'Proses Berhasil',
                    'text' => 'Data Berhasil Tersimpan'
                ];
            }else{
                $errors = DocoHelpers::parseError($model->errors,'EdcForm');
                $status = [
                    'data' => $errors,
                    self::STATUS => 422
                ];
            }       
            return $status;     
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [self::MESSAGE => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [self::MESSAGE => $e->getMessage()];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Edc::findOne($id);
            if($request->post() && !empty($model) ){
                $post = $request->post();
                $model->attributes = $post;
                if($model->validate() && $model->save()){
                    $status = [self::MESSAGE => 'Data Berhasil di simpan'];
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'EdcForm');
                    $status = [
                        'data' => $errors,
                        self::STATUS => 422
                    ];
                }
            }else{
                $status = [
                    self::STATUS => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Data Tidak Di Temukan'
                ];                        
            }     
            return $status;     
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [self::MESSAGE => $e->getMessage()];
        } catch (\Exception $e) {
           \Yii::$app->response->statusCode = 500;
            return [self::MESSAGE => $e->getMessage()];
        }
    }
}