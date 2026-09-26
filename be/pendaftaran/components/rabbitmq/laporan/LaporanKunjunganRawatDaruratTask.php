<?php

namespace app\components\rabbitmq\laporan;

use app\modules\v1\models\LapKunjunganRawatDarurat;
use Doco\rabbitmq\task\ReportTask;
use Doco\components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class LaporanKunjunganRawatDaruratTask extends ReportTask
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
        $request = $this->filter;
        $model = new LapKunjunganRawatDarurat;
        $query = $model::find();
        $result = $header = $footer = $toggle = [];
        $advancedFilters = ArrayHelper::getValue($request, 'advanced-filter');
        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        }
        if(isset($advancedFilters['toggle'])){
            $toggle = $advancedFilters['toggle'];
            unset($request['advanced-filter']['toggle']);
        }
        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $request);
        $key = [
            '4' => [
                'title' => 'Umur',
                'row' => 'umur'
            ],
            '5' => [
                'title' => 'Golongan Umur',
                'row' => 'golonganumur_nama'
            ],
            '6' => [
                'title' => 'Jenis Kelamin',
                'row' => 'jenis_kelamin'
            ],
            '7' => [
                'title' => 'Agama',
                'row' => 'agama'
            ],
            '8' => [
                'title' => 'Status Perkawinan',
                'row' => 'status_perkawinan'
            ],
            '9' => [
                'title' => 'Pekerjaan',
                'row' => 'pekerjaan_nama'
            ],
            '10' => [
                'title' => 'Kota/Kab',
                'row' => 'kabupaten_nama'
            ],
            '11' => [
                'title' => 'Status Kunjungan',
                'row' => 'kunjungan'
            ],
            '12' => [
                'title' => 'Jenis Kasus Penyakit',
                'row' => 'jeniskasuspenyakit_nama'
            ],
            '13' => [
                'title' => 'Ruangan',
                'row' => 'ruangan_nama'
            ],
            '16' => [
                'title' => 'Cara Bayar / Penjamin',
                'row' => 'carabayar_penjamin',
            ],
            '17' => [
                'title' => 'Dokter IGD',
                'row' => 'nama_pegawai'
            ],
            '18' => [
                'title' => 'Kelas Pelayanan',
                'row' => 'kelaspelayanan_nama'
            ],
            '19' => [
                'title' => 'Status Pulang / Kondisi',
                'row' => 'kondisipulang'
            ],
            '20' => [
                'title' => 'Status Pemeriksaan',
                'row' => 'status_periksa'
            ],
            '21' => [
                'title' => 'No SEP',
                'row' => 'nosep'
            ],
        ];
        $data = $query->asArray()->all();
        if($toggle){
            $toggle = explode(',', $toggle);
            foreach ($toggle as $k => $v) {
                $keyheader[$v] = $key[$v];
            }
        }else{
            $keyheader = $key;
        }
        foreach ($data as $index => $value) {
            $newdata = [];
            $newdata['Tanggal Pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran']);
            $newdata['Info Kunjungan'] = $value['no_pendaftaran']. ' - '.$value['no_rekam_medik']. ' - ' . (($value['namadepan']) ? $value['namadepan'] . " " : '') .$value['nama_pasien'];
            $newdata['Status Pasien'] = !empty($value['status_pasien']) ? $value['status_pasien'] : '-';
            foreach ($keyheader as $k => $v) {
                if ($v['row'] == 'carabayar_penjamin') {
                    $newdata[$v['title']] = $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'];
                } else {
                    $newdata[$v['title']] = isset($value[$v['row']]) ? $value[$v['row']] : null; // Safely access the value
                }
            }
            $result[] = $newdata;
        }
        $path = 'web/'.'uploads/'. $this->unique_str . '.xlsx';
        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Rawat Darurat', $result, $header, [], $footer, [], true);
        $filePath->save($path);
        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrlPendaftaran();
        try {
            $response = $client->post('lap-kunjungan-rawat-darurat/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str . '.xlsx'),
                        'filename' => 'Laporan Kunjungan Rawat Darurat.xlsx'
                    ],
                ]
            ]);
            return json_decode($response->getBody(), true);
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
