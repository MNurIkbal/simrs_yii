<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pasien;

class PasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pasien';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    // public function actionIndex()
    // {
    //     $model = new Pasien;
    //     $query = $model::find();
    //     $query = DocoRestActiveFilter::advancedFilter($model, $query);
    //     return new ActiveDataProvider([
    //         'query' => $query,
    //     ]);
    // }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('no_rekam_medik')) {
                $result->andFilterWhere(['ILIKE', 'no_rekam_medik', $indexing]);
            }

            // $layarantrian_id = $request->post('layarantrian_id');
            // if($layarantrian_id) {
            //     $result->andWhere(['antrian_t.layarantrian_id' => $layarantrian_id]);
            // }

            // $loket_id = $request->post('loket_id');
            // if($loket_id) {
            //     $result->andWhere(['antrian_t.loket_id' => $loket_id]);
            // }

            // $panggil_flag = $request->post('panggil_flag');
            // if($panggil_flag !== null) {
            //     $panggil_flag = $panggil_flag === '0' ? false : true;
            //     $result->andWhere(['antrian_t.panggil_flag' => $panggil_flag]);
            // }


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

    public function actionGetPasien()
    {
        $model = new Pasien;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getData($filter = null)
    {
        $returnData = Pasien::find()
                ->select([
                    'pasien_id',
                    'no_rekam_medik',
                    'tgl_rekam_medik',
                    'jenisidentitas',
                    'no_identitas_pasien',
                    'namadepan',
                    'nama_pasien',
                    'nama_bin',
                    'jeniskelamin',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'kelompokumur_id'
                ]);
        if ($filter) {
            if(is_array($filter))
            {
                foreach ($filter as $key => $value) {
                    $returnData->andWhere([$key => $value]);
                }
            }
        }

        return $returnData;
    }

}
