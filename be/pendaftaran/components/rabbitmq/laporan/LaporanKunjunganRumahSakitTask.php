<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use app\modules\v1\models\LaporanKunjunganRumahSakitView;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\models\PegawaiView;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class LaporanKunjunganRumahSakitTask extends ReportTask
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
        $title = 'Laporan Pendaftaran Kunjungan Rumah Sakit';
        $result = [];
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $model = new LaporanKunjunganRumahSakitView();
        $query = $model::find();
        $advancedFilters = ArrayHelper::getValue($request, 'advanced-filter');
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($advancedFilters)){
            if (isset($advancedFilters['tgl_pendaftaran_awal'])
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $start = $advancedFilters['tgl_pendaftaran_awal'];
                $end = $advancedFilters['tgl_pendaftaran_akhir'];
            }

            if (isset($advancedFilters['ruangan_id'])) {
                $arr_ruangan_id = explode(',', $advancedFilters['ruangan_id']);
                $query->andWhere(['in', 'ruangan_id', $arr_ruangan_id]);
            }

            if (isset($advancedFilters['no_pendaftaran'])) {
                $query->andWhere(['no_pendaftaran' => $advancedFilters['no_pendaftaran']]);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end])
                ->orderby(['tgl_pendaftaran'=> SORT_DESC]);
        $tgl_awal = '';
        $tgl_akhir = '';
        $no = 0 ;
        $statusTitipan = '-';
        foreach ($query->asArray()->all() as $key => $value) {
            $no ++;
            $statusTitipan = '-';
            $data['instalasi_nama']    = $value['instalasi_nama'];
            $data['jenis_pasien']      = $value['jenis_pendaftaran'];
            $data['ruangan_nama']      = $value['ruangan_nama'];
            $data['no_pendaftaran']    = $value['no_pendaftaran'];
            $data['tgl_pendaftaran']   =   date('d-F-Y H:i:s',strtotime($value['tgl_pendaftaran']));;
            $data['no_rekam_medik']    = $value['no_rekam_medik'];
            $data['nama_pasien']       = (($value['namadepan']) ? $value['namadepan'] . " " : '') . $value['nama_pasien'];
            $data['status_pasien']       = $value['status_pasien'];
            $data['alamat_pasien']     = $value['alamat_pasien'];
            $data['jeniskelamin']      = $value['jeniskelamin'];
            $data['umur']              = $value['umur'];
            $data['jeniskasuspenyakit_nama']   = $value['jeniskasuspenyakit_nama'];
            if($value['instalasi_id'] == 2 || $value['instalasi_id'] == 3){
                if($value['carabayar_id'] == 6){
                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                } else if (!empty($value['is_pasientitipan_pk'])) {
                    if($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false){
                        $statusTitipan =  $value['kelas_ditagihkan_nama'];
                    }
                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                } else if (empty($value['is_pasientitipan_pk'])) {
                    if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                        $statusTitipan =  $value['kelas_ditagihkan_nama'];
                    }
                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                }
            }
            $data['kelaspelayanan_nama / Kelas_tagihan']       = $value['kelaspelayanan_nama'];
            $result[] = $data;
        }

        $header = ['Periode'=> date('d F Y H:i:s', strtotime($start)) . ' - '.date('d F Y H:i:s', strtotime($end))];
        $path = 'web/'.'uploads/'. $this->unique_str . '.xlsx';
        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Rumah Sakit', $result, $header, [], [], [], true);
        $filePath->save($path);
        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrlPendaftaran();
        try {
            $response = $client->post('lap-kunjungan-rumah-sakit/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str . '.xlsx'),
                        'filename' => 'Laporan Kunjungan Rumah Sakit.xlsx'
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
