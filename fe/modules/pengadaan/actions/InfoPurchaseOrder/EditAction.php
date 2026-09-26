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

class EditAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $title = \Yii::t('fe', 'Alasan Edit');
            return $this->controller->renderAjax('modal-alasan-edit', get_defined_vars());
        } catch (RequestException $e) {
           return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
