<?php 

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\api\controllers\ranap;

use Yii;
use app\components\DocoController;
use app\components\Services\Ranap\CetakCpptService;

class CetakCpptController extends DocoController
{
    protected $allowAction = ['*'];

    /**
     * @method actionIndex
     */
    public function actionIndex()
    {
        $id              = Yii::$app->request->get('id');
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id');
        return (new CetakCpptService)->execute($id, $pasienadmisi_id);
    }
}

?>