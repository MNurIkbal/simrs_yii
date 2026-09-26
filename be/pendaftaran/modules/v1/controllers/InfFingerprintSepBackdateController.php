<?php

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\components\BpjsController;
use app\modules\v1\models\Bpjs;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoMessages;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class InfFingerprintSepBackdateController extends BpjsController
{
    public $modelClass = 'app\modules\v1\models\Bpjs';
    const LOOKUP_TYPE = 'jenis_rencana';
    const RENCANA_RAWAT_INAP = 1126;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["approval"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);


        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $filterParam = $request->get('advancedFilter');
            $bulan = date('n');
            $tahun = date('Y');
            if (isset($filterParam['tglsep'])) {
                if ($filterParam['tglsep']) {
                    $bulan = date('n', strtotime($filterParam['tglsep']));
                    $tahun = date('Y', strtotime($filterParam['tglsep']));
                }
            }
            $data = $model->listPersetujuanSep($bulan, $tahun);

            return $data;
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

    public function actionApproval()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new Bpjs;
        $result = $model->approvalPengajuanSep($post);

        return $result;
    }
}
