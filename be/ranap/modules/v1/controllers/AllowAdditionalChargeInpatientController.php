<?php 
/**
 * @author: [Ardi Pratama][ardi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\businessLogic\AdditionalChargeInpatient;

class AllowAdditionalChargeInpatientController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        return [];
    }

    public function actionIndex()
    {
    	$request = Yii::$app->request;
    	$pasienadmisi_id = $request->get('pasienadmisi_id',null);
    	return AdditionalChargeInpatient::process($pasienadmisi_id);
    }
}