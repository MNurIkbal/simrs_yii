<?php
namespace app\commands;

use app\modules\v1\controllers\SinkronisasiRajalController as Sinkron;
use Doco\components\DocoActiveController;
use yii\console\Controller;

class SyncRajalController extends Controller
{
    public function actionIndex()
    {
        $return = Sinkron::actionSinkronRajal(true);
        print_r($return);
    }
}
