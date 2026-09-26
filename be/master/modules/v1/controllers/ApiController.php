<?php

namespace app\modules\v1\controllers;

use Yii;

use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\models\bpjs\BpjsAplicare;

class ApiController extends DocoActiveController
{
    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Model class
     */
    public $modelClass = '';

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi custom verbs
     * @return array $results
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi custom actions
     * @return array $results
     */
    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Function untuk get list master
     * @return array $results
     */
    public function actionGetListMaster()
    {
        $results = array();
        $request = Yii::$app->request->get();

        if (!empty($request)) {
            foreach ($request as $key => $value) {
                $condition = null;
                $order = null;

                $isJson = $this->isJson($value);

                if ($isJson) {
                    $data = json_decode($value);
                    $class = "app\modules\\v1\models\\" . $data[0];
                    $condition = $data[1];
                    $order = $data[2];
                } else {
                    $class = "app\modules\\v1\models\\" . $value;
                }

                $model = new $class;
                $q = $model->find();

                if ($condition) {
                    $q->andWhere($condition);
                }

                if ($order) {
                    $q->orderBy([$order => SORT_ASC]);
                }

                $q->andWhere([
                    'is_active' => true,
                    'is_deleted' => false
                ]);
                $results[$key] = $q->asArray()->all();
            }
        }

        return $results;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk cek variabel apakah json atau bukan
     * @param array $string
     * @return array boolean
     */
    private function isJson($string) {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function actionReferensiKamarAplicare()
    {
        return (new BpjsAplicare)->referensiKamarAplicare();
    }

    public function actionReadAplicare()
    {
        $start = Yii::$app->request->get('start');
        $limit = Yii::$app->request->get('limit');
        return (new BpjsAplicare)->readAplicare($start, $limit);
    }
}