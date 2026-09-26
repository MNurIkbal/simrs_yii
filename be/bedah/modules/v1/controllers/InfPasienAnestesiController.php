<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-08-07 10:27:19
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 10:49:24
 */

namespace app\modules\v1\controllers;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Anestesi;
use app\modules\v1\models\AnestesiDetail;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\InfoPasienOperasiView;
use app\modules\v1\models\AnestesiDetailView;
use app\modules\v1\traits\PreAnestheticTrait as TraitsPreAnestheticTrait;
use app\modules\v1\traits\PostOperativeAnestesiTrait;
use app\modules\v1\traits\IntraOperativeAnestesiTrait;
use app\modules\v1\traits\AnestesiTrait;
use app\modules\v1\traits\BeforeLeavingAnestesiTrait;

class InfPasienAnestesiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienOperasiView';
    
    use TraitsPreAnestheticTrait;
    use PostOperativeAnestesiTrait;
    use IntraOperativeAnestesiTrait;
    use AnestesiTrait;
    use BeforeLeavingAnestesiTrait;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["get-pack-data"] = ["GET"];
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
        $model = new InfoPasienOperasiView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_operasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_operasi']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_operasi']);
            }
        }

        $query->andWhere(['between', 'tgl_operasi', $start, $end]);
        $query->andWhere(['status_periksa' => DocoConstants::BLM_OPERASI]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetPackData()
    {
        $lookup = $this->getLookup();
        $list_sts_op = $lookup->select(['lookup_id', 'lookup_name'])
            ->where([
              'lookup_type' => DocoConstants::STRING_ST_OP['lookup_type'],
              'lookup_kode' => DocoConstants::STRING_ST_OP['lookup_kode'],
            ])->asArray()->all();

        return [
            'list_sts_op' => $list_sts_op
        ];
    }

    public function actionGetPackAnestesi()
    {
        $request = Yii::$app->request;
        $pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id');
        $dataAnestesi = $dataAnestesiDet = [];

        $dataAnestesi = Anestesi::find()
            ->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])
            ->asArray()->one();

        if (!empty($dataAnestesi)) {
            $anestesiId = $dataAnestesi['anestesi_id'];
            $dataAnestesiDet = AnestesiDetailView::find()
                ->where(['anestesi_id' => $anestesiId])
                ->asArray()->all();
        }

        $lookupList = $this->getLookupKeperawatan()
            ->select(['lookupkeperawatan_id', 'lookup_name', 'lookup_type'])
            ->where(['IN', 'lookup_type', DocoConstants::ANES_LOOK_TYPE])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->asArray()->all();
        $dataPack = $this->mapLookupByType($lookupList);

        return [
            'dataAnestesi' => $dataAnestesi,
            'dataAnestesiDet' => $dataAnestesiDet,
            'dataPack' => $dataPack,
        ];
    }

    private function getLookup()
    {
        return Lookup::find();
    }

    private function getLookupKeperawatan()
    {
        return LookupKeperawatan::find();
    }

    private function mapLookupByType($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$value['lookup_type']][] = $value;
        }
        return $result;
    }
}
