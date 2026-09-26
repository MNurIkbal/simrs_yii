<?php

namespace app\modules\v1\actions\PaketFisio;

use Yii;
use Doco\components\DocoHelpers;
use yii\db\Exception as DBException;
use app\modules\v1\models\DaftarPaketFisioV;

class GetDataDetailAction extends BaseCurrentAction
{
    public function run()
    {
        $helpers = new DocoHelpers;
        $request = Yii::$app->request;
        $daftarPaketFisio = $request->get('id');
        $daftarPaketFisio = $helpers->decrypt($daftarPaketFisio);
        try {
            $model = new DaftarPaketFisioV;
            $query = $model::find()->asArray()->one();
            return $helpers->response($query);
        } catch (DBException $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        }
    }
}
