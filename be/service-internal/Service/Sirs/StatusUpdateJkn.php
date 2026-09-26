<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\Models\LoginJknR;
use Integrasi\Service\Sirs\Models\Lookup;
use Integrasi\Service\Sirs\Models\PendaftaranOl;
use Integrasi\Service\Sirs\Models\Pendaftaran;
use Integrasi\Service\Sirs\Models\Reseptur;
use Integrasi\Service\Sirs\Models\KonfigSystem;
use Integrasi\Service\Sirs\Models\LookupTransaksi;
use Doco\models\antrian\AntrianjknV;
use Integrasi\Service\Sirs\Models\BpjsJkn;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoHelpers;
use Doco\Services\ApiBPJSLZString;

class StatusUpdateJkn extends \Integrasi\Contracts\DocoImplement
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
    static protected $pendaftaranol_ids;
    static protected $pendaftaran_id;
    static protected $task_ids;
    static protected $isOnline = true;
    static protected $tanpaResep = false;
    static protected $konfigAutoCreateTaskId = false;
    static protected $isSerahkanObat = false;
    static protected $konfigFlowTaskId = false;
    

    public function execute()
    {
        $this->getKonfigAutoCreateTaskId();
        $this->getKonfigFlowTaskId();
        $update_from = isset($this->result['update_from']) ? $this->result['update_from'] : $this->update_from;
        if(self::$konfigAutoCreateTaskId && $update_from == DocoConstants::CETAK_ETIKET){
            $message = 'Task ID 6 Tidak Dikirim karena konfigurasi.';
            return $this->setResponse($message);
        }
        $kodebooking = isset($this->result['kodebooking']) ? $this->result['kodebooking'] : null;
        $pendaftaranol_id = isset($this->result['pendaftaranol_id']) ? $this->result['pendaftaranol_id'] : null;
        $pendaftaran_id = isset($this->result['pendaftaran_id']) ? $this->result['pendaftaran_id'] : null;
        $is_created_by_dokter = isset($this->result['data']['is_created_by_dokter']) && $this->result['data']['is_created_by_dokter'] ? true : false;
        
        // handle farmasi response
        if(isset($this->result['data'])){
            $pendaftaran_id = isset($this->result['data']['pendaftaran_id']) ? $this->result['data']['pendaftaran_id'] : $pendaftaran_id;
        }

        $date = date('Y-m-d H:i:s');
        $waktu = DocoHelpers::generateTimeStamp($date);
        $waktu = isset($this->waktu) ? $this->waktu : $waktu;
        $data_ol = [];
        $task_id = isset($this->result['taskid']) ? $this->result['taskid'] : $this->taskid;
        $isPendingTask = (int)$this->isPendingTask == 1 ? true : false;
        $keterangan = '';

        if(!empty($pendaftaranol_id)){
            $data_ol = PendaftaranOl::find()->where(['pendaftaranol_id'=>$pendaftaranol_id])
                        ->one();
        } else if (!empty($pendaftaran_id)){
            $data_ol = PendaftaranOl::find()->where(['pendaftaran_id'=>$pendaftaran_id])
                        ->one();
        } else if (!empty($kodebooking)){
            $data_ol = PendaftaranOl::find()->where(['no_pendaftaranol'=>$kodebooking])
                        ->one();
        }

        $jenisReservasi = $data_ol['jenis_reservasi'];
        if(empty($data_ol)){
            if($pendaftaran_id != null){
                self::$isOnline = false;
                $data_ol = (new \yii\db\Query())
                ->select('*')
                ->from('antrianjkn_v')
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'tipe' => 'offline',
                    ])
                ->one();
            }

            if(empty($data_ol)){
                $response = [
                    'message'=> 'Pasien tidak ditemukan'
                ];
                return $this->setResponse($response);
            }
        }

        $is_pasienbaru=0;
        if(isset($data_ol['status_pasien']) && in_array($data_ol['status_pasien'], [DocoConstants::VAR_PAS_L, DocoConstants::JKN_PASIEN_LAMA])){
            $is_pasienbaru = 0;
        }else{
            $is_pasienbaru = 1;
        }

        // if ((int)$task_id == DocoConstants::TASK_SELESAI_POLI && $pendaftaran_id) {
        //     $task_id = $this->setTaskPulang($pendaftaran_id);
        // }
        $this->checkResep($pendaftaran_id);
        
        $kodebooking = isset($data_ol['no_pendaftaranol']) ? $data_ol['no_pendaftaranol'] : null;
        if (!self::$isOnline) $kodebooking = isset($data_ol['kodebooking']) ? $data_ol['kodebooking'] : $kodebooking;
        self::$pendaftaranol_ids = isset($data_ol['pendaftaranol_id']) ? $data_ol['pendaftaranol_id'] : null;
        self::$pendaftaran_id = isset($data_ol['pendaftaran_id']) ? $data_ol['pendaftaran_id'] : null;
        if(self::$konfigAutoCreateTaskId && $update_from == DocoConstants::PEMULANGAN){
            $task_id = ['5','6'];
        }else if($update_from == DocoConstants::SERAHKAN_OBAT){
            $isCetakEtiket = $this->getLogJknR($pendaftaran_id);
            if(!$isCetakEtiket){
                $task_id = ['6','7'];
                self::$isSerahkanObat = true;
            }
        }else if($update_from == DocoConstants::CETAK_ETIKET){
            $isCetakEtiket = $this->getLogJknR($pendaftaran_id);
            if($isCetakEtiket){
                $message = 'Task ID 6 Sudah Dikirim.';
                return $this->setResponse($message);
            }
        }
        self::$task_ids = isset($task_id) ? $task_id : null;

        $response = [];
        if (is_array($task_id)) {
            $waktuTambahan = 0;
            $counter = 1;
            // if (self::$tanpaResep) {
            //     $waktuTambahan = 10;
            //     array_pop($task_id);
            // } // dicomment karena mengurangi task id *RPP-730
            foreach ($task_id as $value) {
                $_val = (int) $value;
                if($is_pasienbaru == 0 && in_array($_val, [1, 2])){
                    continue;
                }

                if (in_array($value, [4, 5, 6, 7])) {
                    continue;
                }
                
                if (self::$konfigFlowTaskId) {
                    if ((int) $_val == 3 && $update_from != DocoConstants::SAVE_SOAP_PELAYANAN) {
                        $response[] = ['message' => 'Task 3 akan update ketika save SOAP karena sekarang sedang menyalakan konfig flow task id'];
                        continue;
                    } else if ((int) $_val == 3 && $update_from == DocoConstants::SAVE_SOAP_PELAYANAN && !$is_created_by_dokter) {
                        $response[] = ['message' => 'Task 3 akan update ketika save SOAP oleh dokter karena sekarang sedang menyalakan konfig flow task id'];
                        continue;
                    } else if ((int) $_val == 4 && $update_from != DocoConstants::SAVE_RESUMEMEDIS_PELAYANAN) {
                        $response[] = ['message' => 'Task 4 akan update ketika save RESUME MEDIS karena sekarang sedang menyalakan konfig flow task id'];
                        continue;
                    }
                } else {
                    if ((int) $_val == 3 && $update_from == DocoConstants::SAVE_SOAP_PELAYANAN) {
                        $response[] = ['message' => 'Task 3 akan update ketika save pendaftaran karena tidak menyalakan konfig flow task id'];
                        continue;
                    } else if ((int) $_val == 4 && $update_from == DocoConstants::SAVE_RESUMEMEDIS_PELAYANAN) {
                        $response[] = ['message' => 'Task 4 akan update ketika periksa pasien karena tidak menyalakan konfig flow task id'];
                        continue;
                    }
                }
                
                if(($isPendingTask && $counter == 2) || (self::$isSerahkanObat && $counter == 1) || count($task_id) > 1){
                    $waktuTambahan = $waktuTambahan + 300;
                }
                self::$task_ids = $value;
                if ($waktuTambahan != 0) {
                    if (self::$tanpaResep) {
                        $additionalTime = " -" . $waktuTambahan . " seconds";
                    } else if(self::$isSerahkanObat){
                        $additionalTime = " -" . $waktuTambahan . " seconds";
                    } else {
                        $additionalTime = " +" . $waktuTambahan . " seconds";
                    } 
                } else {
                    $additionalTime = "";
                }
                if(($isPendingTask && $counter == 2) || (self::$isSerahkanObat && $counter == 1)){
                    $newDate = date('Y-m-d H:i:s', strtotime($date . $additionalTime));
                }else{
                    $newDate = date('Y-m-d H:i:s');
                }
                $waktu = DocoHelpers::generateTimeStamp($newDate);

                if ($value == DocoConstants::TASK_SELESAI_POLI) {
                    if(!self::$tanpaResep){
                        $jenisResep = $this->checkJenisResep($pendaftaran_id);
                        $data = [
                            'kodebooking' => $kodebooking,
                            'taskid' => $value,
                            'waktu' => $waktu,
                            'jenisresep' => $jenisResep,
                        ];
                    }else{
                        $data = [
                            'kodebooking' => $kodebooking,
                            'taskid' => $value,
                            'waktu' => $waktu
                        ];    
                    }
                }else{
                    if (self::$konfigFlowTaskId && $update_from == DocoConstants::SAVE_PENDAFTARAN && in_array($value, [1, 2])) {
                        $created_date_pendaftaran = Pendaftaran::find()->select('created_date')->where(['pendaftaran_id' => $pendaftaran_id])->scalar();
                        if ($value == 1) { // task id 1
                            $waktu = DocoHelpers::generateTimeStamp(date('Y-m-d H:i:s', strtotime('-15 seconds', strtotime($created_date_pendaftaran))));
                        } else { // task id 2
                            $waktu = DocoHelpers::generateTimeStamp(date('Y-m-d H:i:s', strtotime('-10 seconds', strtotime($created_date_pendaftaran))));
                        }
                    } else if (!self::$konfigFlowTaskId && $update_from == DocoConstants::SAVE_PENDAFTARAN && in_array($value, [1, 2, 3])) {
                        $created_date_pendaftaran = Pendaftaran::find()->select('created_date')->where(['pendaftaran_id' => $pendaftaran_id])->scalar();
                        if ($value == 1) { // task id 1
                            $waktu = DocoHelpers::generateTimeStamp(date('Y-m-d H:i:s', strtotime('-15 seconds', strtotime($created_date_pendaftaran))));
                        } else if ($value == 2) { // task id 2
                            $waktu = DocoHelpers::generateTimeStamp(date('Y-m-d H:i:s', strtotime('-10 seconds', strtotime($created_date_pendaftaran))));
                        } else {
                            $waktu = DocoHelpers::generateTimeStamp(date('Y-m-d H:i:s', strtotime($created_date_pendaftaran)));
                        }
                    }

                    $data = [
                        'kodebooking' => $kodebooking,
                        'taskid' => $value,
                        'waktu' => $waktu
                    ];
                }
                
                $result = (new BpjsJkn)->updateAntrianJkn($data);
                $response[] = $result;
                $this->updateAntrianPasien($data_ol, $value);
                $this->setLogs($result, $data);

                if (self::$tanpaResep) {
                    $waktuTambahan = $waktuTambahan - 5;
                } else {
                    $waktuTambahan = $waktuTambahan + 5;
                }
                $counter++;
            }

            if($jenisReservasi == DocoConstants::JENIS_RESERVASI_JKN){
                if (in_array('2', $task_id)){
                    $date = date('Y-m-d H:i:s');
                    Yii::$app->db->createCommand("
                        UPDATE antrianjkn_r SET is_checkin = true, tgl_checkin = :date, last_modified_date = :date 
                        WHERE is_checkin = false and pendaftaranol_id IN (:pendaftaranol_id)
                    ")
                    ->bindValue(':date',$date)
                    ->bindValue(':pendaftaranol_id',$pendaftaranol_id)
                    ->execute();
                    Yii::$app->db->createCommand("
                        UPDATE pendaftaranol_t SET is_checkin = true, tgl_checkin = :date, last_modified_date = :date 
                        WHERE is_checkin = false and pendaftaranol_id IN (:pendaftaranol_id)
                    ")
                    ->bindValue(':date',$date)
                    ->bindValue(':pendaftaranol_id',$pendaftaranol_id)
                    ->execute();
                };
            }
            
        } else {
            if($is_pasienbaru == 0 && in_array($task_id, [1, 2])){
                return;
            }

            if(in_array($task_id, [4, 5, 6, 7])){
                return;
            }

            if ((int) $task_id == DocoConstants::TASK_BATAL_JKN) {
                if ($update_from == DocoConstants::BATAL_RESERVASI_POLI) {
                    return;
                }
                if (self::$isOnline && 
                    ($data_ol['status_daftar_ol'] != DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK
                    && $data_ol['status_daftar_ol'] != DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI)) {
                    $response = [
                        'message'=> 'Pasien bukan batal reservasi'
                    ];
                    return $this->setResponse($response);
                }
                
                $dataBatal = [
                    'kodebooking' => $kodebooking,
                    'keterangan' => $keterangan
                ];
                $responseBatal = (new BpjsJkn)->batalAntrianJkn($dataBatal);
            }
            
            if (self::$konfigFlowTaskId) {
                if ((int) $task_id == 3 && $update_from != DocoConstants::SAVE_SOAP_PELAYANAN) {
                    $response = ['message' => 'Task 3 akan update ketika save SOAP karena sekarang sedang menyalakan konfig flow task id'];
                    return $this->setResponse($response);
                } else if ((int) $task_id == 3 && $update_from == DocoConstants::SAVE_SOAP_PELAYANAN && !$is_created_by_dokter) {
                    $response = ['message' => 'Task 3 akan update ketika save SOAP oleh dokter karena sekarang sedang menyalakan konfig flow task id'];
                    return $this->setResponse($response);
                } else if ((int) $task_id == 4 && $update_from != DocoConstants::SAVE_RESUMEMEDIS_PELAYANAN) {
                    $response = ['message' => 'Task 4 akan update ketika save RESUME MEDIS karena sekarang sedang menyalakan konfig flow task id'];
                    return $this->setResponse($response);
                }
            } else {
                if ((int) $task_id == 3 && $update_from == DocoConstants::SAVE_SOAP_PELAYANAN) {
                    $response = ['message' => 'Task 3 akan update ketika save pendaftaran karena tidak menyalakan konfig flow task id'];
                    return $this->setResponse($response);
                } else if ((int) $task_id == 4 && $update_from == DocoConstants::SAVE_RESUMEMEDIS_PELAYANAN) {
                    $response = ['message' => 'Task 4 akan update ketika periksa pasien karena tidak menyalakan konfig flow task id'];
                    return $this->setResponse($response);
                }
            }

            $data = [
                'kodebooking' => $kodebooking,
                'taskid' => $task_id,
                'waktu' => $waktu
            ];
            
            // $response = $this->updateAntrian($data);
            $response = (new BpjsJkn)->updateAntrianJkn($data);
            $this->updateAntrianPasien($data_ol, $task_id);
            $this->setLogs($response, $data);
        }
        
        return $this->setResponse(@$response);
    }
    
    protected function updateAntrian($data)
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
        $param = 'antrean/updatewaktu';
        $action = 'POST';
        
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        
        return self::out($get_curl);
	}

    public function setResponse($response)
    {
        return json_encode([
            'service' => 'Sirs-UpdateStatusJkn',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    public function setLogs($response, $data)
    {
        $userIdentity = $this->user_identity;
        
        $model = new LoginJknR;
        $model->pendaftaranol_id = self::$pendaftaranol_ids;
        $model->pendaftaran_id = self::$pendaftaran_id;
        $model->state = self::$task_ids;
        $model->created_date = date('Y-m-d H:i:s');
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

    /**
     * @method update status antrian pasien di tabel antrian_t
     * 
     * @param array $data_ol
     * @param int $taskId
     * @return boolean
     */
    protected function updateAntrianPasien($data_ol, $taskId)
    {
        $antrianId = $data_ol['antrian_id'];
        $penOlId = $data_ol['pendaftaranol_id'];
        $penId = self::$pendaftaran_id;

        if (!empty($antrianId)) {
            switch ((int) $taskId) {
                case (int) DocoConstants::STATUS_DONE_ADMISI:
                    $status_antrian = DocoConstants::STATUS_ANTRIAN_POLI;
                    break;
                case (int) DocoConstants::STATUS_TUNGGU_POLI:
                    $status_antrian = DocoConstants::STATUS_PERIKSA;
                    break;
                case (int) DocoConstants::STATUS_PULANG_POLI:
                    $status_antrian = DocoConstants::STATUS_PULANG;
                    break;
                
                default:
                    $status_antrian = 0;
                    break;
            }

            Yii::$app->db->createCommand("
                UPDATE antrian_t SET status_antrian = ({$status_antrian}) WHERE antrian_id = {$antrianId}
            ")->execute();

            $queryPulang = "UPDATE antrianjkn_r SET is_selesai_periksa = true WHERE pendaftaranol_id = " . $penOlId;
            $queryCheckin = "UPDATE antrianjkn_r SET is_checkin = true WHERE pendaftaranol_id IN (" . $penOlId . ")";
            if (!self::$isOnline)  {
                $queryPulang = "UPDATE antrianjkn_r SET is_selesai_periksa = true WHERE pendaftaran_id = " . $penId;
                $queryCheckin = "UPDATE antrianjkn_r SET is_checkin = true WHERE pendaftaran_id = " . $penId;
            }

            if($taskId == DocoConstants::STATUS_PULANG_POLI) {
                Yii::$app->db->createCommand($queryPulang)->execute();
            }

            if ($taskId == DocoConstants::STATUS_CHECKIN) {
                Yii::$app->db->createCommand($queryCheckin)->execute();
            }
        }

        return true;
    }

    protected function setTaskPulang($pendaftaran_id)
    {
        $result = [];
        $dataResep = Reseptur::find()->select(['reseptur_id'])
        ->where(['pendaftaran_id' => $pendaftaran_id])
        ->asArray()->all();

        if (empty($dataResep)) {
            self::$tanpaResep = true;
            $result = [
                DocoConstants::TASK_SELESAI_POLI,
                DocoConstants::TASK_TUNGGU_FARMASI,
                DocoConstants::TASK_SELESAI_FARMASI,
            ];
        } else {
            $result = DocoConstants::TASK_SELESAI_POLI;
        }

        return $result;
    }

    protected function checkResep($pendaftaran_id)
    {
        $dataResep = Reseptur::find()->select(['reseptur_id'])
        ->where(['pendaftaran_id' => $pendaftaran_id])
        ->asArray()->all();

        if (empty($dataResep)) {
            self::$tanpaResep = true;
        }
    }

    protected function checkJenisResep($pendaftaran_id)
    {
        $dataResep = (new \yii\db\Query())
        ->select('*')
        ->from('reseptur_t')
        ->where(['pendaftaran_id' => $pendaftaran_id])
        ->orderBy(['reseptur_id' => SORT_DESC])
        ->one();
        $reseptur_id = $dataResep['reseptur_id'];
        $jenisResep = Yii::$app->db->createCommand("
            SELECT
            CASE
            WHEN prescribe_v.racikan_id =1 THEN 'Racikan'
            ELSE 'Non Racikan'
            END AS jenis_resep
            from prescribe_v 
            WHERE prescribe_v.pendaftaran_id = :pendaftaran_id
            AND prescribe_v.reseptur_id = :reseptur_id
        ")->bindValue(':pendaftaran_id', $pendaftaran_id)->bindValue(':reseptur_id', $reseptur_id)->queryOne();
        return $jenisResep['jenis_resep'];
    }

    protected function getKonfigAutoCreateTaskId()
    {
        $konfig = KonfigSystem::find()->select(['auto_create_task_id'])->asArray()->one();
        self::$konfigAutoCreateTaskId = $konfig['auto_create_task_id'];
    }

    protected function getLogJknR($pendaftaran_id)
    {
        $result = false;
        $dataLog = LoginJknR::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
            'state' => '6'
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
    
    protected function getKonfigFlowTaskId()
    {
        $konfig = LookupTransaksi::find()->select(['additional_value'])->where(['kode_transaksi' => DocoConstants::KONFIG_FLOW_TASKID])->asArray()->one();
        if ($konfig && ($konfig['additional_value'] != false || $konfig['additional_value'] != 'false')) {
            self::$konfigFlowTaskId = $konfig['additional_value'] 
                && strtolower($konfig['additional_value']) != 'false' 
                && $konfig['additional_value'] != '' 
                ? true 
                : false;
        }
    }
}