<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoTerimaMutasiObatView;
use app\modules\v1\models\InfoTerimaMutasiObatDetailView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\TerimaMutasiObat;
use app\modules\v1\models\TerimaMutasiObatDetail;
use app\modules\v1\models\StokObatAlkesT;

class InfPenerimaanController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoTerimaMutasiObatView';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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

    public function actionObatAlkes()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoTerimaMutasiObatView;
            $query = $model::find()->where(['ruanganasal_id' => $request->get('ruangan_id', null)]);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tglterima'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglterima']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglterima']);
                }
                if(isset($_GET['advanced-filter']['instalasi_pengirim'])){
                    $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_pengirim'];
                    unset($_GET['advanced-filter']['instalasi_pengirim']);
                }

                if(isset($_GET['advanced-filter']['ruangan_pengirim'])){
                    $_GET['advanced-filter']['ruanganasal_id'] = $_GET['advanced-filter']['ruangan_pengirim'];
                    unset($_GET['advanced-filter']['ruangan_pengirim']);
                }
            }
            
            $query->andWhere(['between', 'tglterima', $start, $end]);
            
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

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

    public function actionGenerateApi()
    {
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
        ]);

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();

        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
        ];
    }

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $model = InfoTerimaMutasiObatView::find();
        if ($id) {
            $model->where(['terimamutasiobat_id' => $id]);
        }

        return $model;
    }

    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoTerimaMutasiObatDetailView;
            $query = $model::find()->where(['terimamutasiobat_id' => $request->get('id', null)]);

            return [
                'data' => $query->asArray()->all(),
                'count' => $query->count()
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

    public function actionDelete($id)
    {
        try {
            $result = (new TerimaMutasiObat)->delete($id);
            $result = (new TerimaMutasiObatDetail)->find()->where(['terimamutasiobat_id' => $id])->one();
            if($result) {
                $result->delete();
                (new StokObatAlkesT)->find()->where(['terimamutasidetail_id' => $result->terimamutasidetail_id])->delete();
            }
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}