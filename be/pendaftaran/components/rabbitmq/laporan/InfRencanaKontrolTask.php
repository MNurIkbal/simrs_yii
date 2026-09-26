<?php

namespace app\components\rabbitmq\laporan;

use app\modules\v1\models\InfRencanaKontrol;
use Doco\rabbitmq\task\ReportTask;
use Doco\components\DocoHelpers;
use Yii;
use GuzzleHttp\Client;

class InfRencanaKontrolTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $customData;
    protected $footer;
    protected $title;
    protected $totalPerPage;
    protected $countData;

    protected $list_data = [];

    public static $look_exclude = [402,628];
    const STATUS_PERIKSA = 'status_periksa_id';

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
    }

    /** proses export */
    protected function prosesExport()
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang menyiapkan file excel.',
                'progress' => 80
            ]),
        ]);

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel.',
                'progress' => 85
            ]),
        ]);

        $this->generateExcel();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                 'status' => 'finish', 
                 'messageProcess' => 'Proses import excel berhasil.',
                 'progress' => 90
             ]),
         ]);
 
         $this->uploadFile();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses berhasil.',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);
    }
    
    private function generateExcel()
    {
        $result = $this->getDataLaporanExcel();
        $newResult = [];
        $no = 1;
        if (! empty($result)) {
            foreach ($result as $key => $value) {
                $newData['Tanggal Pendaftaran'] = $value['tgl_pendaftaran'];
                $newData['Tanggal Rencana Kontrol'] = $value['tgl_jadwal'];
                $newData['No Pendaftaran'] = $value['no_pendaftaran'];
                $newData['No Rekam Medik'] = $value['no_rekam_medik'];
                $newData['Nama Pasien'] = $value['nama_pasien'];
                $newData['No Telepon'] = $value['no_telepon_pasien'];
                $newData['Dokter Asal'] = $value['doktermengkonsul'];
                $newData['Dokter Tujuan'] = $value['nama_pegawai'];
                $newData['Poliklinik Asal'] = $value['ruangan_asal'];
                $newData['Poliklinik Tujuan'] = $value['ruangan_nama'];
                $newData['Cara Bayar'] = $value['carabayar_nama'];
                $newData['Penjamin'] = $value['penjamin_nama'];

                $newResult[] = $newData;
            }
        }

        $path = 'web/'.'uploads/'. $this->unique_str . '.xlsx';
        $filePath = DocoHelpers::exportExcel('Informasi Rencana Kontrol Pasien', $newResult, [], [], [], [], true);
        $filePath->save($path);
        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getDataLaporanExcel()
    {
        $getData = $this->filter;
        $advancedFilters = [];
        if(isset($getData['advanced-filter'])) {
            $advancedFilters = $getData;
        }

        $model = new InfRencanaKontrol;
        $query = $model::find();
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:00');
        
        if (isset($advancedFilters['advanced-filter'])) {
            if (isset($advancedFilters['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $advancedFilters['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilters['advanced-filter']['tgl_pendaftaran']);
            }
            if (isset($advancedFilters['advanced-filter']['tgl_jadwal'])) {
                $explode = explode(" - ", $advancedFilters['advanced-filter']['tgl_jadwal']);
                if (count($explode) == 2) {
                    $startJadwal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endJadwal = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    $query->andWhere(['between', 'tgl_jadwal', $startJadwal, $endJadwal]);
                }
                unset($advancedFilters['advanced-filter']['tgl_jadwal']);
            }
            if (isset($advancedFilters['advanced-filter']['asalpoliklinikkonsul_id'])) {
                $query->andWhere(['asalpoliklinikkonsul_id' => (int) $advancedFilters['advanced-filter']['asalpoliklinikkonsul_id']]);
                unset($advancedFilters['advanced-filter']['asalpoliklinikkonsul_id']);
            }
            if (isset($advancedFilters['advanced-filter']['ruangan_id'])) {
                $query->andWhere(['ruangan_id' => (int) $advancedFilters['advanced-filter']['ruangan_id']]);
                unset($advancedFilters['advanced-filter']['ruangan_id']);
            }
            if (isset($advancedFilters['advanced-filter']['approval'])) {
                $query->andWhere(['status_approve' => (int) $advancedFilters['advanced-filter']['approval']]);
                unset($advancedFilters['advanced-filter']['approval']);
            }
            if (isset($advancedFilters['advanced-filter']['transaksi_konsul'])) {
                $query->andWhere(['transaksi_konsul' => (int) $advancedFilters['advanced-filter']['transaksi_konsul']]);
                unset($advancedFilters['advanced-filter']['transaksi_konsul']);
            }
            if (isset($advancedFilters['advanced-filter']['no_pendaftaran'])) {
                $query->andWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($advancedFilters['advanced-filter']['no_pendaftaran'])]);
                unset($advancedFilters['advanced-filter']['no_pendaftaran']);
            }
            if (isset($advancedFilters['advanced-filter']['nama_pasien'])) {
                $query->andWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($advancedFilters['advanced-filter']['nama_pasien'])]);
                unset($advancedFilters['advanced-filter']['nama_pasien']);
            }
            if (isset($advancedFilters['advanced-filter']['no_rekam_medik'])) {
                $query->andWhere(['ILIKE', 'no_rekam_medik', $advancedFilters['advanced-filter']['no_rekam_medik']]);
                unset($advancedFilters['advanced-filter']['no_rekam_medik']);
            }
            if (isset($advancedFilters['advanced-filter']['doktermengkonsul_id'])) {
                $query->andWhere(['doktermengkonsul_id' => $advancedFilters['advanced-filter']['doktermengkonsul_id']]);
                unset($advancedFilters['advanced-filter']['doktermengkonsul_id']);
            }
            if (isset($advancedFilters['advanced-filter']['pegawai_id'])) {
                $query->andWhere(['pegawai_id' => $advancedFilters['advanced-filter']['pegawai_id']]);
                unset($advancedFilters['advanced-filter']['pegawai_id']);
            }
        }

        Yii::error(json_encode([
            'start' => $start,
            'end' => $end,
            'advancedFilters' => $advancedFilters,
            'getData' => $getData,
            'getData-filter' => isset($getData['advanced-filter']) ? $getData['advanced-filter'] : null
        ]));
        $query->andWhere(['between', new \yii\db\Expression('(tgl_pendaftaran::date)'), $start, $end]);
        $query->orderBy('tgl_pendaftaran', SORT_ASC);
        $model = $query->asArray()->all();

        return $model;
    }

    private function uploadFile()
    {
        $client = $this->setUrlPendaftaran();
        try {
            $response = $client->post('/inf-rencana-kontroler/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str . '.xlsx'),
                        'filename' => 'Informasi Rencana Kontrol.xlsx'
                    ],
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            Yii::error(json_encode($response));
            return $response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return $response->getBody();
            }
        }
    }

    private function setUrlPendaftaran()
	{
        $params = Yii::$app->params['iniFile'];
        $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        $client =  new Client([
            'base_uri' => $urlBackend . 'pendaftaran/v1/',
            'headers' => $header
        ]);

		return $client;
	}
    
}
