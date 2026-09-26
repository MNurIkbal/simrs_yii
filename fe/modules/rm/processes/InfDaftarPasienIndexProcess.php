<?php
/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of Sirs
 * Powered by Sirs
 */

namespace app\modules\rm\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class InfDaftarPasienIndexProcess extends \app\components\DocoBaseProcessExtension
{
    protected $title = 'Informasi Daftar Pasien';

    protected function processFlow($controller)
    {

    	$api    = Yii::$app->docoRest->rm->get('lap-rekapitulasi-pelayanan/generate-api');
    	$api    = json_decode($api->getBody(), true);
    	$dokter = $api['response']['dokter'];
		return $controller->render('index', [
            'title' => $this->title,
            'dokter' => $dokter,
            'reportEngine' => Yii::$app->report->enabled
        ]);
    }
}