<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\TerimaBayarKlaimDetail;
use app\modules\v1\models\TerimaBayarKlaim;
use app\modules\v1\models\InfoPengajuanKlaim;
use app\modules\v1\models\InfoPengajuanKlaimDetail;
use app\modules\v1\models\PembayaranAlokasi;
use app\modules\v1\models\PembayaranAlokasiDetail;
use yii\helpers\ArrayHelper;

class SelectOptionsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\SelectOptions';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        return $actions;
    }

    public function actionListPengajuan()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        $page = $request->get('page', 1);
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $additionalPayload = $request->get('additionalPayload');
        $start = ArrayHelper::getValue($additionalPayload, 'start_date');
        $end = ArrayHelper::getValue($additionalPayload, 'end_date');
        $model = PengajuanKlaim::find();
        $model->andWhere(['status_pengajuanklaim' => [
            DocoConstants::PROSES_PEMBAYARAN,
            DocoConstants::BATAL_PEMBAYARAN
        ]]);
        if (!empty($start) && !empty($end)) {
            $start = date('Y-m-d', strtotime($start));
            $end = date('Y-m-d', strtotime($end));
            $model->andWhere(['BETWEEN', 'tgl_pengajuanklaim', $start . ' 00:00:00', $end . ' 23:59:59']);
        }
        if (!empty($term)) {
            $model->andWhere(['ILIKE', 'no_pengajuanklaim', $term]);
        }
        $model->orderBy(['no_pengajuanklaim' => SORT_DESC]);
        $datas = $model->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
        return $datas;
    }

    public function actionListPembayaran()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        $page = $request->get('page', 1);
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $additionalPayload = $request->get('additionalPayload');
        $pengajuanKlaimId = ArrayHelper::getValue($additionalPayload, 'pengajuanklaim_id');
        $model = TerimaBayarKlaimDetail::find()->joinWith([
            'parent' => function ($data) {
                $data->select([
                    'terimabayarklaim_t.terimabayarklaim_id',
                    'terimabayarklaim_t.no_terimabayarklaim',
                    'terimabayarklaim_t.tgl_terimabayarklaim',
                ]);
            }
        ]);
        $model->andWhere(['!=', 'terimabayarklaimdetail_t.is_alokasi', true]);
        if (!empty($term)) {
            $model->andWhere(['ILIKE', 'terimabayarklaim_t.no_terimabayarklaim', $term]);
        }
        if (!empty($pengajuanKlaimId)) {
            $model->andWhere(['terimabayarklaimdetail_t.pengajuanklaim_id' => $pengajuanKlaimId]);
        }
        $model->orderBy(['terimabayarklaim_id' => SORT_DESC]);
        $datas = $model->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
        return $datas;
    }
}
