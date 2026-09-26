<?php

namespace app\modules\v1\actions\PaketFisio;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use yii\db\Exception as DBException;
use app\modules\v1\models\DaftarPaketFisioV;
use app\modules\v1\actions\PaketFisio\GetDetailPaketAction;
use app\modules\v1\Exceptions\PaketFisio\BaseCurrentException;

class EditDataAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request->get();
        $id = ArrayHelper::getValue($request, 'id');
        $helpers = new DocoHelpers;
        try {
            $model = new DaftarPaketFisioV;
            $query = $model::find()->findByDaftarPaketFisioId($id)->one();
            $daftarPaketFisioId = ArrayHelper::getValue($query, 'daftarpaketfisio_id');
            if (empty($daftarPaketFisioId)) {
                return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => 'Daftar Paket Fisioterapi ID Tidak Ditemukan'
                ]);
            }
            $listTindakanPaket = (new GetDetailPaketAction(1, $this))->run($daftarPaketFisioId);
            return [
                'listDataPaket' => $query,
                'listDetailTindakanPaket' => $listTindakanPaket
            ];
        } catch (BaseCurrentException $e) {
            return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => $e->getMessage()
            ]);
        } catch (DBException $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        }
    }
}
