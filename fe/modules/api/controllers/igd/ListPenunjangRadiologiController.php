<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\api\controllers\igd;

use Yii;
use app\components\DocoController;
use app\components\Services\Igd\ListPenunjangService;

class ListPenunjangRadiologiController extends DocoController
{
    protected $allowAction = ['*'];

    public function actionIndex()
    {
        $resultService = (new ListPenunjangService)->execute();
        if (!empty($resultService['noRm'])) {
            return $this->renderAjax("_modal_radiologi_rm", $resultService);
        } else {
            return $this->renderAjax('_modal_radiologi', $resultService);
        }
    }    
}