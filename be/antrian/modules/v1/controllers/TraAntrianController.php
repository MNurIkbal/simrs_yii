<?php
/**
 * @author: arief saputra
 * @description: master untuk CRUD Layar Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Loket;
use app\modules\v1\models\Carabayar;
use app\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

class TraAntrianController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Loket';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-carabayar"] = ["POST", "GET"];
        $verbs["list-antrian-by-layar"] = ["POST", "GET"];
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
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('antrian')) {
                $result->andFilterWhere(['ILIKE', 'loket_nama', $indexing]);
            }

            $layarantrian_id = $request->post('layarantrian_id');
            if($layarantrian_id) {
                $result->andWhere(['antrian_t.layarantrian_id' => $layarantrian_id]);
            }

            $loket_id = $request->post('loket_id');
            if($loket_id) {
                $result->andWhere(['antrian_t.loket_id' => $loket_id]);
            }

            $panggil_flag = $request->post('panggil_flag');
            if($panggil_flag !== null) {
                $panggil_flag = $panggil_flag === '0' ? false : true;
                $result->andWhere(['antrian_t.panggil_flag' => $panggil_flag]);
            }


            // $status = $request->post('is_active');
            // if($status === null) {
            //     $status = $status ? true : false;
            //     $result->andWhere(['antrian_t.is_active' => $status]);
            // }

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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

    public function actionListAntrianByLayar($id, $status = false)
    {
        $arr_where = [];

        if($id) {
            $arr_where['antrian_t.layarantrian_id' ] = $id;
        }

        if($status) {
            $arr_where['antrian_t.panggil_flag'] = $status;
        }

        return $this->getData($arr_where)->asArray()->all();
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Loket::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'LoketForm');
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

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new Loket;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'LoketForm');
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

    public function actionDelete($id)
    {
        throw new HttpException(404, 'The requested Item could not be found.');
    }

    public function actionView($id)
    {
        return $this->getData(['antrian_t.antrian_id' => $id])->asArray()->one();
    }

    private function getData($filter = null)
    {
        $antrian = Antrian::find()
                        ->select([
                                'antrian_t.antrian_id',
                                'antrian_t.ruangan_id',
                                'antrian_t.carabayar_id',
                                'antrian_t.pendaftaran_id',
                                'antrian_t.layarantrian_id',
                                'antrian_t.loket_id',
                                'antrian_t.tgl_antrian',
                                'antrian_t.no_antrian',
                                'antrian_t.status_pasien',
                                'antrian_t.carabayar_loket',
                                'antrian_t.panggil_flag',
                                'antrian_t.is_active'
                        ]);
        if ($filter) {
            if(is_array($filter)) 
            {
                foreach ($filter as $key => $value) {
                    $antrian->andWhere([$key => $value]);
                }
            }

            // else {
            //     $antrian->where(['antrian_id'=>$filter]);
            // }
            
        }

        return $antrian;
    }

    public function actionListCarabayar() {
        $items = ArrayHelper::map(Carabayar::find()->where(['is_active' => true])->all(), 'carabayar_id', 'carabayar_nama');

        return $items;
    }

}