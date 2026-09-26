<?php
/**
 * @var created by ijal
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;

class ReferensiController extends DocoController
{

    protected $_restPendaftaran;
    protected $allowAction = ['norm'];

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }
    
    public function actionNorm($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $response = $this->_restPendaftaran->post('allow/get-pasien-rajal', [
            'form_params' => ['keyword'=>$q]
        ]);
        $response = json_decode($response->getBody(), TRUE);
        
        $results = [];
        if ($response['metadata']['status'] == 200) {
            $list = $response['response'];
            foreach ($list as $key => $each) {
                $no_and_nama = $each['no_rekam_medik'] . " - " . $each['nama_pasien'];
                $results[] = ['id'=>$each['pasien_id'], 'text'=>$no_and_nama];
            }
        }

        return ['results' => $results];
    }

}