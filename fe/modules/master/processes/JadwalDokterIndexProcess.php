<?php

namespace app\modules\master\processes;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\modules\master\models\JadwalDokterForm;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoConstants;
use app\components\DocoHelpers;

class JadwalDokterIndexProcess extends \app\components\DocoBaseProcessExtension
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

        return $controller->render('index', get_defined_vars());
    }

    public function listHari() 
    {
        try{

            $request = Yii::$app->docoRest->master->get('allow/list-hari-lookup');
            $response = json_decode($request->getBody(),TRUE);
            $results = $response['response']['data'];
            $results = ArrayHelper::map($results,'hari_id','hari_nama');

            return $results;
        } catch(\Exception $e) {
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }

    }
}