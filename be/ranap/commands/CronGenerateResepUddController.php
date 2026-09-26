<?php 

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoPasienRiView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
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

class CronGenerateResepUddController extends Controller
{
	public function actionIndex($admisi_id = NULL) {
		echo json_encode(CronUddComponent::startGenerate($admisi_id));
	}
}

