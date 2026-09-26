<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Integrasi\Service\Sirs\Models\LoginJknR;
use Integrasi\Service\Sirs\Models\Lookup;
use Integrasi\Service\Sirs\Models\BpjsJkn;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoHelpers;
use Doco\Services\ApiBPJSLZString;
use Integrasi\Service\Sirs\Models\KonfigSystem;

class AddAntrianFarmasiJkn extends \Integrasi\Contracts\DocoImplement
{
    static protected $cons_ids;
    static protected $secret_keys;
    static protected $version = 1.0;
    public $url;
    public $cons_id;
    public $secret_key;
    public $ppkPelayanan;
    public $user_key;
    static protected $user_keys;
    static protected $pendaftaran_id;
    static protected $konfigAutoCreateTaskId = false;

    public function execute()
    {
        $this->getKonfigAutoCreateTaskId();
        /** collect pendaftaran_id and reseptur_id with index response */
        $pendaftaran_id = isset($this->result['response']['data']['pendaftaran_id']) ? $this->result['response']['data']['pendaftaran_id'] : null;
        $reseptur_id = isset($this->result['response']['data']['reseptur_id']) ? $this->result['response']['data']['reseptur_id'] : null;
        
        /** collect pendaftaran_id and reseptur_id without index response */
        if(empty($pendaftaran_id)){
            $pendaftaran_id = isset($this->result['data']['pendaftaran_id']) ? $this->result['data']['pendaftaran_id'] : null;
        }
        if(empty($reseptur_id)){
            $reseptur_id = isset($this->result['data']['reseptur_id']) ? $this->result['data']['reseptur_id'] : null;
        }
        $cppt_id = isset($this->result['cppt_id']) ? DocoHelpers::decrypt($this->result['cppt_id']) : null;
        $dataJkn = [];
        $date = date('Y-m-d H:i:s');
        $waktu = DocoHelpers::generateTimeStamp($date);
        $waktu = isset($this->waktu) ? $this->waktu : $waktu;
        
        if(!empty($cppt_id)){
            $dataCppt = (new \yii\db\Query())
            ->select('pendaftaran_id')
            ->from('cppt_t')
            ->where([
                'cppt_id' => $cppt_id,
                ])
            ->one();
            $pendaftaran_id = $dataCppt['pendaftaran_id'];
            
            $dataReseptur = (new \yii\db\Query())
            ->select('reseptur_id')
            ->from('reseptur_t')
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                ])
            ->one();
            $reseptur_id =  $dataReseptur['reseptur_id'];
        }

        /** Validation */
        if(empty($pendaftaran_id)){
            $response = [
                'message'=> 'Pendaftaran ID tidak boleh null'
            ];
            return $this->setResponse($response);
        }

        if(empty($dataJkn)){
            if($pendaftaran_id != null){
                
                $dataReservasi = (new \yii\db\Query())
                ->select('pendaftaran_id')
                ->from('pendaftaranol_t')
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    ])
                ->andWhere(['<>','status_daftar_ol',566])
                ->andWhere(['is_active'=>'t'])
                ->andWhere(['is_deleted'=>'f'])
                ->one();

                $tipe = 'offline';
                if(!empty($dataReservasi)){
                    $tipe = 'online';
                }

                $dataJkn = (new \yii\db\Query())
                ->select('*')
                ->from('antrianjkn_v')
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'tipe' => $tipe,
                    ])
                ->one();
            }

            if(empty($dataJkn)){
                $response = [
                    'message'=> 'Pasien tidak ditemukan'
                ];
                return $this->setResponse($response);
            }
        }

        $kodebooking = isset($dataJkn['kodebooking']) ? $dataJkn['kodebooking'] : null;
        self::$pendaftaran_id = isset($dataJkn['pendaftaran_id']) ? $dataJkn['pendaftaran_id'] : null;
        if(!empty($pendaftaran_id)){
            // $isPulang = $this->getLogJknR(self::$pendaftaran_id);
            // if(!$isPulang){
            //     $data = [
            //         'kodebooking' => $kodebooking,
            //         'taskid' => '5',
            //         'waktu' => $waktu
            //     ];
            //     $response = (new BpjsJkn)->updateAntrianJkn($data);
            //     $this->setLogs($response, $data, '5');
            // }
        }
        $jenisResep = $this->checkJenisResep($reseptur_id);
        $noAntrian = $this->getNoAntrianFarmasi($reseptur_id);

        $data = [
            'kodebooking' => $kodebooking,
            'jenisresep' => $jenisResep,
            'nomorantrean' => $noAntrian,
            'keterangan' => $jenisResep
        ];
        $response = (new BpjsJkn)->addAntrianFarmasi($data);
        $this->setLogs($response, $data);

        if(self::$konfigAutoCreateTaskId){
            /** task id 6 = waktu task_id 5 + 5 menit  */
            // $newDate = date('Y-m-d H:i:s', strtotime($date . '+300 seconds'));
            // $waktu = DocoHelpers::generateTimeStamp($newDate);
            // $data = [
            //     'kodebooking' => $kodebooking,
            //     'taskid' => '6',
            //     'waktu' => $waktu
            // ];
            // $response = (new BpjsJkn)->updateAntrianJkn($data);
            // $this->setLogs($response, $data, '6');
        }

        return $this->setResponse($response);
    }

    public function setResponse($response)
    {
        return json_encode([
            'service' => 'Sirs-AddAntrianFarmasiJkn',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    public function setLogs($response, $data, $taskid = null)
    {
        $userIdentity = $this->user_identity;
        
        $model = new LoginJknR;
        $model->pendaftaran_id = self::$pendaftaran_id;
        $model->created_date = date('Y-m-d H:i:s');
        $model->state = $taskid;
        $model->created_by = isset($userIdentity['uid']) ? $userIdentity['uid'] : null;
        $model->payload = isset($data) ? json_encode($data) : null;
        $model->sync_respon = isset($response) ? json_encode($response) : null;
    
        $model->save();
    }

    protected static function getTimestamp()
    {
        // Computes the timestamp
        return DocoHelpers::generateTimeStamp(date('Y-m-d H:i:s'));
    }

    protected static function getSignature()
    {
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac(
            'sha256', 
            self::$cons_ids . "&" . self::getTimestamp(), 
            self::$secret_keys, 
            true
        );
        return base64_encode($signature);
    }

    protected static function getHeader($form_url_encoded = false)
    {
        if ($form_url_encoded)
            $conten_type = "Content-Type: application/x-www-form-urlencoded";
        else
            $conten_type = "Content-Type: application/json";

        return [
            "X-cons-id:" . self::$cons_ids,
            "X-timestamp:" . self::getTimestamp(),
            "X-signature:" . self::getSignature(),
            "user_key:" . self::$user_keys,
            $conten_type
        ];
    }

    protected static function out($data)
    {
        $outPut = $data && is_string($data) && json_decode($data) ? json_decode($data, true) : [];

        if (!empty($outPut['response']) && self::$version >= 1.1) {
            $response = $outPut['response'];
            $keyEncrypt = self::$cons_ids . self::$secret_keys . self::getTimestamp();
            $outPut['response'] = (new ApiBPJSLZString)->decryptWithDecompress($keyEncrypt,$response);
        }
        return $outPut;

    }

    protected static function curl($url, $data, $header, $action = '')
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if (!empty($action)) {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $action);
            }
        }

        $res = curl_exec($curl);

        $curl_get_info = curl_getinfo($curl);
        $get_response_time = !empty($curl_get_info['total_time']) ? $curl_get_info['total_time'] : 0;

        if (!$res) {
            $errorArray = [
                'metaData'=>[
                    "code"=>500,
                    "message"=>curl_error($curl) ? : "Problem with bridging",
                ]
            ];
            $res = json_encode($errorArray);
            // die('Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl));
        }
        curl_close($curl);

        return $res;
    }

    protected function addAntrianFarmasi($data)
	{
        $cache = Yii::$app->cache;
        $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        if (!$cache_bpjs) {
            $lookup = Lookup::find()->where(['lookup_type'=>DocoConstants::LOOKUP_BPJS])
                ->asArray()
                ->all();
            $cache->set(DocoConstants::LOOKUP_BPJS, $lookup);
            $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        }

        if ($cache_bpjs) {
            foreach ($cache_bpjs as $each) {
                if ($each['lookup_name'] == 'secret_key') {
                    $this->secret_key = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'cons_id') {
                    $this->cons_id = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'url_jkn') {
                    $this->url = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'ppkPelayanan') {
                    $this->ppkPelayanan = $each['lookup_value'];
                }
                
                if ($each['lookup_name'] == 'version') {
                    self::$version = $each['lookup_value'];
                }

                if ($each['lookup_name'] == 'user_key') {
                    $this->user_key = $each['lookup_value'];
                }
            }
        }

       
        self::$cons_ids = $this->cons_id;
        self::$secret_keys = $this->secret_key;
        self::$user_keys = $this->user_key;

        
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'antrean/farmasi/add';
        $action = 'POST';
        
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        
        return self::out($get_curl);
	}

    protected function checkJenisResep($reseptur_id)
    {
        $jenisResep = Yii::$app->db->createCommand("
            SELECT
                fgetstatusracikan(:reseptur_id)
            as status_racikan
        ")->bindValue(':reseptur_id', $reseptur_id)->queryOne();
        return $jenisResep['status_racikan'];
    }

    protected function getNoAntrianFarmasi($reseptur_id)
    {
        $dataReseptur = (new \yii\db\Query())
        ->select('antrian_id')
        ->from('reseptur_t')
        ->where(['reseptur_id' => $reseptur_id])
        ->orderBy(['reseptur_id' => SORT_DESC])
        ->one();
        $dataAntrian = (new \yii\db\Query())
        ->select('no_antrian')
        ->from('antrian_t')
        ->where(['antrian_id' => $dataReseptur['antrian_id']])
        ->one();
        return $dataAntrian['no_antrian'];
    }

    protected function getLogJknR($pendaftaran_id)
    {
        $result = false;
        $dataLog = LoginJknR::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
            'state' => '5'
        ])->one();

        if (!empty($dataLog)) {
            $sync_respon = json_decode($dataLog['sync_respon'], true);
            if(!empty($sync_respon)){
                $code = ArrayHelper::getValue($sync_respon, 'metadata.code');
                if($code === 200){
                    $result = true;
                }
            }
        }
        return $result;
    }

    protected function getKonfigAutoCreateTaskId()
    {
        $konfig = KonfigSystem::find()->select(['auto_create_task_id'])->asArray()->one();
        self::$konfigAutoCreateTaskId = $konfig['auto_create_task_id'];
    }
}