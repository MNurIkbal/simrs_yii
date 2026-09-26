<?php 

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\api\controllers\ranap;

use Yii;
use app\components\DocoController;
use app\components\Services\Ranap\HistorySurgeryOrdersService;

class HistorySurgeryOrdersController extends DocoController 
{
    protected $allowAction = ['*'];

    /**
     * @method actionIndex
     */
    public function actionIndex()
    {
        $noRm          = Yii::$app->request->get('norm');
        $resultService = (new HistorySurgeryOrdersService)->execute($noRm);
        return $this->renderAjax('/ranap/cppt/penunjang/_modal_riwayat_bedah', $resultService);
    }
}

?>