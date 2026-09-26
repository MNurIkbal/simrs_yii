<?php

namespace app\extensions\master\JadwalDokter;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\modules\master\models\JadwalDokterForm;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoConstants;
use app\components\DocoHelpers;
use app\modules\master\processes\JadwalDokterIndexProcess;

class JadwalDokterSty extends JadwalDokterIndexProcess
{
    protected function processFlow($controller)
    {
        $title = "Jadwal dokter";
        
        $modelJadwalDokter = new JadwalDokterForm;
        
        $instalasiList = [DocoConstants::INSTALASI_ID_RJ, DocoConstants::INSTALASI_ID_RD, DocoConstants::INSTALASI_ID_RI];
        $instalasiList = (count($instalasiList) > 0 ) ? array_merge($instalasiList, DocoConstants::INSTALASI_ID_PENUNJANG) : [];
        $arr = ['list_instalasi'=>['actionListInstalasi', $instalasiList]];
        $response = Yii::$app->docoRest->master->post('allow/loop-aksi', ['form_params'=>$arr]);
        $body = json_decode($response->getBody(), True);
        $listInstalasi = (count($body['response']['list_instalasi']['data']) > 0) ? ArrayHelper::map($body['response']['list_instalasi']['data'], 'instalasi_id', 'instalasi_nama') : [];
        $listHari = $this->listHari();
        $listJam = DocoHelpers::listJam(0,24);

        $response = Yii::$app->docoRest->master->get('ruangan/list-ruangan?instalasi_id=1');
        $body = json_decode($response->getBody(), True);
        $listRuangan = $body['response'];

        return $controller->render('@app/extensions/master/views/jadwal-dokter/indexSty', get_defined_vars());
    }
}