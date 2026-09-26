<?php

namespace app\commands;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use app\modules\v1\models\SyKunjunganPasien;
use Yii;
use yii\console\Controller;

class CronUnduhDokumenEklaimController extends Controller
{
    protected $keyConfig = 'authentication';
    protected $params;
    protected $baseConfig;
    protected $docoRest;

    CONST DEFAULT_CHUNK = 50;

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

    /* -------------------- main function  --------------------------------*/
    public function actionIndex()
    {  
        Yii::error(json_encode([
            "message" => "CRON 'UNDUH DOKUMEN EKLAIM' MULAI BERJALAN"
        ]));

        if (!$this->setAndValidatePayload()) {
            return false;
        }
        
        $bearer = 'Bearer '.$this->getToken();
        
        $kunjungan_ids_chuncked = $this->getDataKunjunganIdsChuncked();

        foreach ($kunjungan_ids_chuncked as $key => $kunjungan_ids) {
            $responseCron = $this->docoRest->penjaminasuransi->post('inf-pasien-ranap-bpjs/unduh-dokumen', [
                'headers' => [
                    'Authorization' => $bearer,
                    'XOwner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
                ],
                'form_params' => [
                    'kunjungan_id' => $kunjungan_ids,
                ]
            ]);
        }

        Yii::error(json_encode([
            'return' => count($kunjungan_ids_chuncked). ' Data Chunk',
            "message" => "CRON 'UNDUH DOKUMEN EKLAIM' SUDAH BERJALAN"
        ]));

        return true;
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
        } else if (!empty($this->start_date) && empty($this->end_date)) {
            $this->start_date = date('Y-m-d', strtotime($this->start_date));
            $this->end_date = date('Y-m-d', strtotime('-30 day', strtotime($this->start_date)));
        } else if (empty($this->start_date) && !empty($this->end_date)) {
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

    /* backdate range */
    private function getDataKunjunganIdsChuncked() 
    {
        $konfig_unduh_dokumen = DocoConstansId::actionGetadditional('validasi_button_unduh', true);
        $kunjungan_ids  = SyKunjunganPasien::find()->select(['kunjungan_id'])
                ->andWhere(['or', ['<>', 'status_unduh_dokumen', DocoConstants::SELESAI_UNDUH_DOKUMN], ['is', 'status_unduh_dokumen', null]])
                ->andWhere(['between', new \yii\db\Expression('(tgl_pendaftaran::date)'), $this->end_date, $this->start_date])
                ->andWhere(['is not', 'nosep', null])
                ->limit(150);

        if (!empty($konfig_unduh_dokumen)) {
            $kunjungan_ids = $kunjungan_ids->andWhere(['IN', 'status_kunjungan', $konfig_unduh_dokumen]);
        }

        $kunjungan_ids = $kunjungan_ids->column();

        return array_chunk($kunjungan_ids, $this->chunk_size);
    }
}
