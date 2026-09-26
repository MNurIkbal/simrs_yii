<?php 

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoPasienRiView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\models\InfoStokObatAlkesFnr;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\CpptView;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\PindahKamar;
use Doco\Services\ResepUddService;

use app\components\CronUddComponent;

class CronResepUddController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoResepturView';

	public function verbs()
	{
	    $verbs = parent::verbs();
	    return $verbs;
	}

	public function actions()
	{
	    $actions = parent::actions();
	    unset($actions['index']);
	    return $actions;
	}

	public function behaviors()
	{
	    $behaviors = parent::behaviors();

	    $behaviors['authenticator'] = [
	        'class' => DocoJwtHttpBearerAuth::className(),
	        'except' => [
	            'index'
	        ],
	    ];

	    $behaviors['access'] = [
	        'class' => DocoAccessRule::className(),
	        'except' => [
	            'index'
	        ],
	    ];

	    return $behaviors;
	}

	public function actionIndex($admisi_id = NULL) {
		return CronUddComponent::startGenerate($admisi_id);
	}
}

