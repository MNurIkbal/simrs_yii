<?php 
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Doco\components\DocoHelpers;

class ReservasiPoliklinikDataPasienDownload extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
        $filter = $this->filter;
        $tipe = ArrayHelper::getValue($filter, 'tipe', 1);
        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getDataAttibutes($filter);
        if($tipe == 1) {
            $row = $tmpCache = [];
            $no = 1;
            $prefix = 0;
            foreach ($attributes as $value) {
                $tmp[1] = $no;

                $col = 2;
                foreach (json_decode('['.$this->columns.']') as $column) {
                    if ($column->data == 'antrian') {
                        $antrian = ArrayHelper::getValue($value, 'no_antrian', '');
                        $antrian_dokter = ArrayHelper::getValue($value, 'antrian_dokter', '');

                        if(!empty($antrian_dokter)) {
                            $antrian = $antrian_dokter;
                        }

                        $tmp[$col] = $antrian;
                    } else if ($column->data == 'nama_pasien') {
                        $nama_pasien = ArrayHelper::getValue($value, 'nama_pasien', '');
                        $nama_pasien_ol = ArrayHelper::getValue($value, 'nama_pasien_ol', '');

                        if(!empty($nama_pasien_ol)) {
                            $nama_pasien = $nama_pasien_ol;
                        }

                        $tmp[$col] = $nama_pasien;
                    } else if ($column->data == 'no_telepon_pasien') {
                        $no_telepon_pasien = ArrayHelper::getValue($value, 'no_telepon_pasien', '');
                        $no_telepon_pasien_ol = ArrayHelper::getValue($value, 'no_telepon_pasien_ol', '');

                        if(!empty($no_telepon_pasien_ol)) {
                            $no_telepon_pasien = $no_telepon_pasien_ol;
                        }

                        $tmp[$col] = $no_telepon_pasien;
                    } else if($column->data == 'tgl_kunjungan') {
                        $tgl_kunjungan = ArrayHelper::getValue($value, 'tgl_kunjungan', '');
                        if(!empty($tgl_kunjungan)) {
                            $tgl_kunjungan = DocoHelpers::convertDate( date('d-M-Y', strtotime($tgl_kunjungan)) );
                        }
                        $jam_kunjungan = ArrayHelper::getValue($value, 'jam_kunjungan', '');

                        $tmp[$col] = $tgl_kunjungan . ' ' . $jam_kunjungan;
                    } else if($column->data == 'is_checkin') {
                        $tmp[$col] = isset($value['is_checkin']) && $value['is_checkin'] ? 'Sudah Check-in' : 'Belum Check-in';
                    } else {
                        $tmp[$col] = ArrayHelper::getValue($value, $column->data);
                    }

                    $col++;
                }

                $row[] = $tmp;
                $tmpCache[] = $tmp;
                if (($no%50) == 0) {
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-excel:'.$this->unique_str,
                        'message' => json_encode(['unique_process' => $this->unique_str]),
                    ]);
                    $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
                    $prefix++;
                    $tmpCache = [];
                }
                $no++;
            }
            $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
        } else {
            $cacheFiles->set($this->unique_str, $attributes);
        }

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        
        return json_encode([
            'service' => 'Sirs-ReservasiPoliklinikDataPasienDownload',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
   }

    private function getDataAttibutes($filter)
    {
        $client = $this->setEndPoint();
        $params = $this->params;
        $getDataUrl = ArrayHelper::getValue($params, 'getDataUrl');
        try {
            $response = $client->get($getDataUrl, [
                'query' => $filter
            ]);
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            return $response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if($e->hasResponse()) {
                $response = $e->getResponse();
                return $response->getBody();
            }
        }
    }

    private function setEndPoint()
	{
		$params = $this->params;
        $baseUri = ArrayHelper::getValue($params, 'base_uri');
        $header = [
                'Authorization' => $this->token,
                'user-agent' => 'cli',
                'X-Owner' => $this->xOwner,
            ];
            $client =  new Client([
                'base_uri' => $baseUri,
                'headers' => $header
            ]);

		return $client;
	}
}
