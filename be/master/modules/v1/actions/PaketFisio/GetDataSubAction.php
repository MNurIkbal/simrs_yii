<?php

namespace app\modules\v1\actions\PaketFisio;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Exception as DBException;
use app\modules\v1\models\ServiceCategory;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DaftarPaketFisioDetail;
use app\modules\v1\models\DaftarPaketFisioDetailV;

class GetDataSubAction extends BaseCurrentAction
{
    public function run()
    {
        $helpers = new DocoHelpers;
        $request = Yii::$app->request;
        $daftarPaketFisioId = $request->get('daftarpaketfisio_id');
        try {
            $model = new DaftarPaketFisioDetailV;
            $query = $model::find()->asArray();
            if ($daftarPaketFisioId) {
                $query->andWhere(['daftarpaketfisio_id' => $daftarPaketFisioId]);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return $this->controller->activeDataProvider($query);
        } catch (DBException $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        }
    }
}
