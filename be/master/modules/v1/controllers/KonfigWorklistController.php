<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\base\Exception;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\KonfigAntrian;
use app\modules\v1\models\KonfigantrianV;
use app\modules\v1\models\Layarantrian;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\LookupTransaksi;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\models\KonfigWorklist;

class KonfigWorklistController extends \Doco\components\DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\KonfigWorklist';
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["create"] = ["POST"];
        $verbs["get-attribute-options"] = ["GET"];
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
            $model = new LookupTransaksi;
            $ruangan = DocoConstants::WORKLIST_KONFIG_FILTER_RUANGAN;
            $urutan = DocoConstants::WORKLIST_KONFIG_URUTAN_PERIKSA;
            $query = $model->find()->andWhere(['in', 'kode_transaksi',[$ruangan, $urutan]]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionChangeStatus(){
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $id = $request->get('id', null);
            $model = LookupTransaksi::findOne(['kode_transaksi' => $id]);
            if (empty($model)) {
                return [
                    'title' => 'Proses Update Gagal !',
                    'text' => 'Konfig tidak ditemukan',
                    'status' => 422
                ];
            }
            $post = $request->post('additional_value');
            $value = 'false';
            if ($post == '1'){
                $value = 'true';
            }
            $model->additional_value = $value;
            if ($model->save()) {
                $transaction->commit();
                return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                    'additional' => [
                        'kode_transaksi' => $id,
                    ]
                ]);
            } else {
                $transaction->rollBack();
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }

        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
