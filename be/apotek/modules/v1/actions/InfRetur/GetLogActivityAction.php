<?php

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use app\components\DocoHelpers;
use app\modules\v1\models\LogActivityR;
use app\modules\v1\models\PasienReturV;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class GetLogActivityAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LogActivityR;
            $query = $model::find();
            if(isset($_GET['advanced-filter']['transaksi_id'])){
                $query->andWhere(['=','transaksi_id', $_GET['advanced-filter']['transaksi_id']]);
                unset($_GET['advanced-filter']['transaksi_id']);
            }
            if(isset($_GET['advanced-filter']['tipe'])){
                $query->andWhere(['=','tipe', $_GET['advanced-filter']['tipe']]);
                unset($_GET['advanced-filter']['tipe']);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query
            ]);
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }
}
