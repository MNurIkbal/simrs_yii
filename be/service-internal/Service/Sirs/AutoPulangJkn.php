<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;
use Integrasi\Service\Sirs\Models\LoginJknR;
use Integrasi\Service\Sirs\Models\Lookup;
use Integrasi\Service\Sirs\Models\PendaftaranOl;
use Integrasi\Service\Sirs\Models\Reseptur;
use Doco\models\antrian\AntrianjknV;
use Integrasi\Service\Sirs\Models\BpjsJkn;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoHelpers;
use Integrasi\ApiBPJSLZString;

class AutoPulangJkn extends \Integrasi\Service\Sirs\StatusUpdateJkn
{  
    protected const TIPE_OFFLINE = 'offline';

    public function execute()
    {
        $task_id = [
            DocoConstants::TASK_SELESAI_POLI,
            DocoConstants::TASK_TUNGGU_FARMASI,
            DocoConstants::TASK_SELESAI_FARMASI,
        ];
        $waktuTambahan = 0;
        $tgl_pendaftaran =  date('Y-m-d 23:59:59', strtotime('-1 days'));
        
        $data = AntrianjknV::find()->where([
            'is_selesai_periksa' => false,
            'pasienpulang_id' => NULL,
        ])->andWhere([
            'NOT', ['tgl_pendaftaran' => NULL]
        ])->andWhere([
            '<=', 'tgl_pendaftaran', $tgl_pendaftaran
        ])->asArray()->all();

        if(empty($data)){
            $response = [
                'message'=> 'Tidak ada pasien yang belum pulang'
            ];
            return $this->setResponse($response);
        }

        foreach ($data as $key => $value) {
            $waktuTambahan = $value['estimasidilayani'];
            $date = $value['tanggal_periksa'];
            $kodebooking = $value['kodebooking'];
            $timezone = date_default_timezone_get();
            self::$pendaftaran_id = $value['pendaftaran_id'];
            self::$pendaftaranol_ids = $value['pendaftaranol_id'];
            if ($value['tipe'] == self::TIPE_OFFLINE) {
                self::$isOnline = false;
            }

            foreach ($task_id as $v) {
                date_default_timezone_set($timezone);
                self::$task_ids = $v;
                $additionalTime = " +" . $waktuTambahan . " minutes";
                $newDate = date('Y-m-d H:i:s', strtotime($date . $additionalTime));
                $waktu = DocoHelpers::generateTimeStamp($newDate);
    
                $data = [
                    'kodebooking' => $kodebooking,
                    'taskid' => $v,
                    'waktu' => $waktu
                ];
                
                $result = $this->updateAntrian($data);
                $result['data'] = $data;
                $response[] = $result;
                $this->updateAntrianPasien($value, $v);
                $this->setLogs($result, $data);

                $waktuTambahan = $waktuTambahan + $value['estimasidilayani'];
            }
        }
        
        return $this->setResponse($response);
    }

    public function setResponse($response)
    {
        return json_encode([
            'service' => 'Sirs-AutoPulangJkn',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }
}