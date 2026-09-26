<?php
/**
 * @author: arief saputra
 * @description: master untuk CRUD Layar Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
// use app\modules\v1\models\Layarantrian;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class LookupController extends \Doco\components\DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\Lookup';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-lookup-by-type"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('layarantrian')) {
                $result->andFilterWhere(['ILIKE', 't.layarantrian_nama', $indexing]);
            }

            // $status = $request->post('is_active');
            // $status = $status ? true : false;

            // $result->andWhere(['t.is_active' => $status]);

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->all(),
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

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new Layarantrian;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'LayarantrianForm');
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
        return $this->getData($id)->one();
        // return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $returnData = (new \yii\db\Query())
                        ->select([
                                't.layarantrian_id',
                                't.layarantrian_nama',
                                't.layarantrian_judul',
                                't.jenisantrian_id',
                                't.konfigantrian_id',
                                't.layarantrian_latarbelakang',
                                't.layarantrian_maxitem',
                                't.layarantrian_itemhigh',
                                't.layarantrian_itemwidth',
                                't.layarantrian_intrefresh',
                                't.is_active',
                                't.is_deleted',
                                'lookupjenis.lookup_name as jenis'])->from('layarantrian_m t')
                        ->join('JOIN', 'lookup_m lookupjenis','lookupjenis.lookup_id = t.jenisantrian_id')
                        ->orderBy([ 't.jenisantrian_id' => SORT_ASC ]);

        if ($id) {
            $returnData->where(['t.layarantrian_id' => $id]);
        }

        return $returnData;
    }

    public function actionListLookupByType($param = null) {
        $data = Lookup::find();

        if($param) {
            $data->where(['lookup_type' => $param]);
        }

        $items = ArrayHelper::map($data->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    // DEPRECATED by arief
    /*public function actionListTypeScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'jenis_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    public function actionListFunctionScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'fungsi_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }*/
}