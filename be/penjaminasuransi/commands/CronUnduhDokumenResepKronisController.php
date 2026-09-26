<?php

namespace app\commands;

use Doco\rabbitmq\RabbitBgProcess;
use Yii;
use yii\console\Controller;

class CronUnduhDokumenResepKronisController extends Controller
{
    protected $keyConfig = 'authentication';
    protected $params;
    protected $baseConfig;
    protected $docoRest;

    CONST DEFAULT_CHUNK = 15;

    public $start_date;
    public $end_date;
    public $chunk_size;


    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->params = Yii::$app->params['iniFile'];
        $this->baseConfig = isset($this->params[$this->keyConfig]) ? $this->params[$this->keyConfig] : [];
        $this->docoRest = Yii::$app->docoRest;
    }

    public function options($actionID)
    {
        return ['start_date', 'end_date', 'chunk_size'];
    }
    
    public function optionAliases()
    {
        return ['s' => 'start_date', 'e' => 'end_date', 'c' => 'chunk_size'];
    }

    public function actionIndex()
    {
        Yii::error(json_encode([
            "message" => "CRON 'UNDUH DOKUMEN RESEP KRONIS' MULAI BERJALAN"
        ]));

        if (!$this->setAndValidatePayload()) {
            return false;
        }
        
        $bearer = 'Bearer '.$this->getToken();

        $this->docoRest->penjaminasuransi->get('inf-pasien-ranap-bpjs/unduh-dokumen-resep-kronis', [
            'headers' => [
                'Authorization' => $bearer,
                'XOwner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
            ],
            'query' => [
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'chunk' => $this->chunk_size,
            ]
        ]);
        Yii::error(json_encode([
            "message" => "CRON 'UNDUH DOKUMEN RESEP KRONIS' SUDAH BERJALAN"
        ]));

        return true;
    }

    private function getToken()
    {
        $response = $this->docoRest->dcms->post('auth/get-token',[
           'form_params' => [
              'username' => $this->baseConfig['username'],
              'password' => $this->baseConfig['password']
           ]
        ]);
        $response = json_decode($response->getBody(), true);
        return isset($response['response']['access_token']) ? $response['response']['access_token'] : null;
    }

    private function setAndValidatePayload()
    {
        if ((!empty($this->start_date) && !strtotime($this->start_date)) || (!empty($this->end_date) && !strtotime($this->end_date))) {
            Yii::error([
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Parameter tanggal tidak sesuai'
            ]);
        }

        /* backdate range */
        if ((empty($this->start_date) || $this->start_date == 'null')  && empty($this->end_date)) {
            $this->start_date = date('Y-m-d', strtotime('-1 day'));
            $this->end_date = date('Y-m-d', strtotime('-31 day'));
        } elseif (!empty($this->start_date) && empty($this->end_date)) {
            $this->start_date = date('Y-m-d', strtotime($this->start_date));
            $this->end_date = date('Y-m-d', strtotime('-30 day', strtotime($this->start_date)));
        } elseif (empty($this->start_date) && !empty($this->end_date)) {
            Yii::error([
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Jika parameter end date diisi, maka start date wajib diisi'
            ]);
            return false;
        } else {
            $this->start_date = date('Y-m-d', strtotime($this->start_date));
            $this->end_date = date('Y-m-d', strtotime($this->end_date));
        }
        $this->chunk_size = !empty($this->chunk_size) ? $this->chunk_size : self::DEFAULT_CHUNK;
        return true;
    }

}