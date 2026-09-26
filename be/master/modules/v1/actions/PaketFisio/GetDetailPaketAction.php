<?php

namespace app\modules\v1\actions\PaketFisio;

use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use yii\db\Exception as DBException;
use app\modules\v1\models\DaftarPaketFisioDetailV;

class GetDetailPaketAction extends BaseCurrentAction
{
    public function run($daftarPaketFisioId)
    {
        $helpers = new DocoHelpers;
        if (empty($daftarPaketFisioId)) {
            return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => 'Daftar Paket Fisioterapi ID Tidak Ditemukan'
            ]);
        }
        try {
            $model = new DaftarPaketFisioDetailV;
            $query = $model::find()->findByDaftarPaketFisioId($daftarPaketFisioId)->all();
            return $query;
        } catch (DBException $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        }
    }
}
