<?php 

namespace app\components\Services;

use Yii;
use app\components\Services\BaseService;
use yii\helpers\ArrayHelper;

class BpjsAplicaresService extends BaseService
{
    protected $_restMasterService;
    protected $_restPendaftaran;

    public function __construct()
    {
        $this->_restMasterService = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function getReferensiKamarAplicare()
    {
        $response = $this->guzzleExec($this->_restPendaftaran,[
            'url' => 'allow-bpjs/referensi-kamar-aplicare',
            'method' => 'GET',
            'payload' => [],
        ]);
        $response = ArrayHelper::getValue($response, 'response', []);
        
        return ArrayHelper::getValue($response, 'list', []);
    }
}