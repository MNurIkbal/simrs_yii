<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\base\View;
use app\components\DocoHelpers;
use app\components\DocoConstants;

class LogEditAction extends Action {
    public function run($id,$type_po) {
        try {
            $request = Yii::$app->request;
            $title = \Yii::t('fe', 'Log Perubahan');
            $transaksi_id = DocoHelpers::decrypt($id);
            return $this->controller->renderAjax('modal-log-perubahan', get_defined_vars());
        } catch (RequestException $e) {
           return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
