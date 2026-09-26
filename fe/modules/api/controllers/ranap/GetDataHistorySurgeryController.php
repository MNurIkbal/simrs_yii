<?php 

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\api\controllers\ranap;

use app\components\DocoController;
use app\components\Services\Ranap\GetDataHistorySurgeryService;

class GetDataHistorySurgeryController extends DocoController 
{
    protected $allowAction = ['*'];

    /**
     * @method actionIndex
     */
    public function actionIndex()
    {
        return (new GetDataHistorySurgeryService)->execute();
    }
}

?>