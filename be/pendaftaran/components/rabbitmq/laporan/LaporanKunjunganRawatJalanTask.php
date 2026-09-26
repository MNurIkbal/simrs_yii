<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use app\modules\v1\models\KonfigLaporan;
use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanKunjunganRawatJalanTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $totalPerPage;
    protected $countData;
    protected $manual_excel;
    protected $filter;
    protected $list_data = [];
    protected $showed_data = [];

    public static $look_exclude = [402,628];
    const STATUS_PERIKSA = 'status_periksa_id';
    const DEFAULT_DATA_SHOW = [0, 1, 2, 3];
    const DEFAULT_HIDE_DATA = [15, 16, 25];
    const DEFAULT_ROWSPAN = 2;

    const DEFAULT_LIST_COLUMN = [
        '0' => ['title' => 'No', 'data' => null, 'custom' => true], // 0
        '1' => ['title' => 'Tanggal Pendaftaran', 'data' => 'tgl_pendaftaran', 'custom' => true], // 1
        '2' => ['title' => 'Info Kunjungan', 'data' => null, 'custom' => true], // 2
        '3' => ['title' => 'Status Pasien', 'data' => 'status_pasien'], // 10
        '4' => ['title' => 'NIK', 'data' => 'no_induk_kependudukan'], // 3
        '5' => ['title' => 'Jenis Kelamin', 'data' => 'jenis_kelamin'], // 4
        '6' => ['title' => 'Umur', 'data' => 'umur'], // 5
        '7' => ['title' => 'Golongan Umur', 'data' => 'golonganumur_nama'], // 6
        '8' => ['title' => 'Agama', 'data' => 'agama'], // 7
        '9' => ['title' => 'Status Perkawinan', 'data' => 'statusperkawinan'], // 8
        '10' => ['title' => 'Pekerjaan', 'data' => 'pekerjaan_nama'], // 9
        '11' => ['title' => 'Kota/Kab', 'data' => 'kabupaten_nama'], // 10
        '12' => ['title' => 'Status Kunjungan', 'data' => 'kunjungan'], // 11
        '13' => ['title' => 'Jenis Kasus Penyakit', 'data' => 'jeniskasuspenyakit_nama'], // 12
        '14' => ['title' => 'Cara Bayar / Penjamin', 'data' => null, 'custom' => true], // 13
        '15' => ['title' => 'Cara Bayar', 'data' => null], // 14
        '16' => ['title' => 'Penjamin', 'data' => null], // 15
        '17' => ['title' => 'Rujukan', 'data' => 'nama_perujuk'], // 16
        '18' => ['title' => 'Ruangan', 'data' => 'ruangan_nama'], // 17
        '19' => ['title' => 'Dokter', 'data' => 'nama_pegawai'], // 18
        '20' => ['title' => 'Kelas Pelayanan', 'data' => 'kelaspelayanan_nama'], // 19
        '21' => ['title' => 'No. SEP', 'data' => 'nosep'], // 20
        '22' => ['title' => 'Status Pulang / Kondisi', 'data' => 'status_pulang', 'custom' => true], // 21
        '23' => ['title' => 'Status Pemeriksaan', 'data' => 'status_periksa'], // 22
        '24' => ['title' => 'Status Skrining', 'data' => 'status_skrining'], // 23
        '25' => ['title' => 'Jenis Pencarian', 'data' => null], // 24
    ];

    // @override
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttributes();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
    }

    // @override
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

        $this->initiateColumn();

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

    private function getDataAttributes()
    {
        $data = $this->generateData()->asArray()->all();
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmpCache[] = $value;
            $no++;
        }

        return $tmpCache;
    }

    private function generateData()
    {
        $request = $this->filter;

        $model = new LaporanKunjunganRawatJalanView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($request['advanced-filter'])) {
            if (isset($request['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
            }

            if(isset($request['advanced-filter']['no_induk_kependudukan'])) {
                $no_induk_kependudukan = $request['advanced-filter']['no_induk_kependudukan'];
                $query->andWhere(['no_induk_kependudukan' => $no_induk_kependudukan]);
                unset($request['advanced-filter']['no_induk_kependudukan']);
            }

            if(isset($request['advanced-filter']['nosep'])) {
                $nosep = $request['advanced-filter']['nosep'];
                $query->andWhere(['nosep' => $nosep]);
                unset($request['advanced-filter']['nosep']);
            }

            if(isset($request['advanced-filter']['status_periksa'])) {
                $status_periksa_id = $request['advanced-filter']['status_periksa'];
                $query->andWhere(['status_periksa_id' => $status_periksa_id]);
                unset($request['advanced-filter']['status_periksa']);
            }

            if(isset($request['advanced-filter']['status_skrining'])) {
                $status_skrining = $request['advanced-filter']['status_skrining'];
                $query->andWhere(['status_skrining' => $status_skrining]);
                unset($request['advanced-filter']['status_periksa']);
            }

            if(isset($request['advanced-filter']['carakeluar_nama'])) {
                $carakeluar_id = $request['advanced-filter']['carakeluar_nama'];
                $query->andWhere(['carakeluar_id' => $carakeluar_id]);
                unset($request['advanced-filter']['carakeluar_nama']);
            }

            if(isset($request['advanced-filter']['toggle'])){
                $toggle = $request['advanced-filter']['toggle'];
                unset($request['advanced-filter']['toggle']);
            }
        }

        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query, $request);
        $query->orderBy(['tgl_pendaftaran'=> SORT_DESC]);
        return $query;
    }

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');
        $filter = $this->filter;

        $row = $this->generateExcelDataRow();

        $excel_konfig = $this->generateExcelKonfig();

        $path = 'web/'.'uploads/'. $this->unique_str . '.xlsx';

        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Rawat Jalan', $row, $excel_konfig['header'], [
            "skipIncrement" => true,
            "skipHeader" => true,
            "nameHeaderSkipped" => $excel_konfig['nameHeaderSkipped'],
            "customHeader" => $excel_konfig['staticHeader'],
        ], $excel_konfig['footer'], $excel_konfig['footerInfo'], true);
        $filePath->save($path);

        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function generateExcelKonfig()
    {
        $header = [
            'Tanggal Pendaftaran' => $this->getTglPendaftaranFilter(),
            'NIK' => isset($this->filter['advanced-filter']['no_induk_kependudukan']) ? $this->filter['advanced-filter']['no_induk_kependudukan'] : '-',
            'Cara Bayar' => $this->getCaraBayarNamaFilter(),
            'Penjamin' => $this->getPenjaminNamaFilter(),
            'Ruangan' => $this->getRuanganNamafilter(),
            'Dokter' => $this->getPegawaiNamaFilter(),
            'No. SEP' => isset($this->filter['advanced-filter']['nosep']) ? $this->filter['advanced-filter']['nosep'] : '-',
            'Status Pulang' => $this->getCaraKeluarNamaFilter(),
            'Status Periksa' => $this->getStatusPeriksaNamaFilter(),
            'Status Skrining' => isset($this->filter['advanced-filter']['status_skrining']) ? $this->filter['advanced-filter']['status_skrining'] : '-',
        ];

        $staticHeader = [array_map(function($val) {
                return ['label' => $val['title'], 'rowspan' => self::DEFAULT_ROWSPAN];
        }, $this->showed_data)];

        $nameHeaderSkipped = array_column($this->showed_data, 'title');

        $footer = [];
        $footerInfo = [];

        return compact('header', 'staticHeader', 'nameHeaderSkipped', 'footer', 'footerInfo');
    }

    private function generateExcelDataRow()
    {
        $row = [];
        $data = $this->getDataAttributes();
        $no = 1;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                foreach ($this->showed_data as $key1 => $column) {
                    if (isset($column['custom']) && $column['custom'] == true) {
                        if ($column['title'] == 'No') {
                            $row[$key][$column['title']] = $no++;
                        }else if ($column['title'] == 'Info Kunjungan') {
                            $row[$key][$column['title']] = $this->getInfoKunjunganData($value);
                        } else if ($column['title'] == 'Tanggal Pendaftaran') {
                            $row[$key][$column['title']] = !empty($value[$column['data']]) ? date('d-M-Y', strtotime($value[$column['data']])) : '';
                        } else if ($column['title'] == 'Cara Bayar / Penjamin') {
                            $row[$key][$column['title']] = $this->getCarabayarPenjamin($value);
                        } else if ($column['title'] == 'Status Pulang / Kondisi') {
                            $row[$key][$column['title']] = $this->getStatusPulang($value);
                        } else {
                            $row[$key][$column['title']] = '';    
                        }
                    } else {
                        $row[$key][$column['title']] = $column['data'] != null ? ArrayHelper::getValue($value, $column['data'], '') : '';
                    }
                }
            }
        }
        return $row;
    }

    private function uploadFile()
    {
        $client = $this->setUrlPendaftaran();
        try {
            $response = $client->post('lap-kunjungan-rawat-jalan/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str . '.xlsx'),
                        'filename' => 'Laporan Kunjungan Rawat Jalan.xlsx'
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


    private function getInfoKunjunganData($data)
    {
        return ArrayHelper::getValue($data, 'no_pendaftaran'). ' - '.ArrayHelper::getValue($data, 'no_rekam_medik'). ' - ' . ((isset($data['namadepan']) && !empty($data['namadepan'])) ? $data['namadepan'] . " " : '') . ArrayHelper::getValue($data, 'nama_pasien');
    }

    private function getCarabayarPenjamin($data)
    {
        return ArrayHelper::getValue($data, 'carabayar_nama', ''). ' / '.ArrayHelper::getValue($data, 'penjamin_nama', '');
    }
    
    private function getStatusPulang($data)
    {
        if($data['carakeluar_nama'] != '' && $data['kondisikeluar_nama']){
            return $data['carakeluar_nama'] . ' / ' . $data['kondisikeluar_nama'];
        }elseif(($data['carakeluar_nama'] != '') || ($data['kondisikeluar_nama'] != '')){
            if($data['carakeluar_nama'] != ''){
                return $data['carakeluar_nama'];
            }else{
                return $data['kondisikeluar_nama'];
            }
        }else{
            return '';
        } 
    }

    private function getTglPendaftaranFilter()
    {   
        $start   = date('Y-m-d');
        $end     = date('Y-m-d');
        if (isset($this->filter['advanced-filter']['tgl_pendaftaran'])) {
            $explode = explode(" - ", $this->filter['advanced-filter']['tgl_pendaftaran']);
            if (count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));
            }
        }
        return date('d M Y', strtotime($start)) . ' Sampai Dengan ' . date('d M Y', strtotime($end));
    }

    private function getCaraBayarNamaFilter() 
    {
        if (isset($this->filter['advanced-filter']['carabayar_id'])) {
            $carabayar_id = $this->filter['advanced-filter']['carabayar_id'];
            $data = Yii::$app->db->createCommand("
                SELECT carabayar_nama
                FROM carabayar_m
                WHERE carabayar_id = {$carabayar_id}
            ")->queryOne();
            return $data['carabayar_nama'];
        }
        return '-';
    }

    private function getPenjaminNamaFilter()
    {
        if (isset($this->filter['advanced-filter']['penjamin_id'])) {
            $penjamin_id = $this->filter['advanced-filter']['penjamin_id'];
            $data = Yii::$app->db->createCommand("
                SELECT penjamin_nama
                FROM penjamin_m
                WHERE penjamin_id = {$penjamin_id}
            ")->queryOne();
            return $data['penjamin_nama'];
        }
        return '-';
    }

    private function getRuanganNamafilter()
    {
        if (isset($this->filter['advanced-filter']['ruangan_id'])) {
            $ruangan_id = $this->filter['advanced-filter']['ruangan_id'];
            $data = Yii::$app->db->createCommand("
                SELECT ruangan_nama
                FROM ruangan_m
                WHERE ruangan_id = {$ruangan_id}
            ")->queryOne();
            return $data['ruangan_nama'];
        }
        return '-';
    }

    private function getPegawaiNamaFilter()
    {
        if (isset($this->filter['advanced-filter']['pegawai_id'])) {
            $pegawai_id = $this->filter['advanced-filter']['pegawai_id'];
            $data = Yii::$app->db->createCommand("
                SELECT nama_pegawai
                FROM pegawai_m
                WHERE pegawai_id = {$pegawai_id}
            ")->queryOne();
            return $data['nama_pegawai'];
        }
        return '-';
    }

    private function getCaraKeluarNamaFilter()
    {
        if (isset($this->filter['advanced-filter']['carakeluar_nama'])) {
            $carakeluar_id = $this->filter['advanced-filter']['carakeluar_nama'];
            $data = Yii::$app->db->createCommand("
                SELECT carakeluar_nama
                FROM carakeluar_m
                WHERE carakeluar_id = {$carakeluar_id}
            ")->queryOne();
            return $data['carakeluar_nama'];
        }
        return '-';
    }
    
    private function getStatusPeriksaNamaFilter()
    {
        if (isset($this->filter['advanced-filter']['status_periksa'])) {
            $status_periksa_id = $this->filter['advanced-filter']['status_periksa'];
            $data = Yii::$app->db->createCommand("
                SELECT lookup_value as status_periksa_nama
                FROM lookup_m
                WHERE lookup_id = {$status_periksa_id} and lookup_type = 'status_periksa'
            ")->queryOne();
            return $data['status_periksa_nama'];
        }
        return '-';
    }

    private function initiateColumn()
    {
        $this->setColumnShowed();
        $this->setColumnHide();
    }

    private function setColumnShowed()
    {
        $filter_showed = isset($this->filter['advanced-filter']['toggle']) ? $this->filter['advanced-filter']['toggle'] : [];
        if (!empty($filter_showed)) {
            $filter_showed = array_merge(self::DEFAULT_DATA_SHOW, explode(',', $filter_showed));
            foreach ($filter_showed as $key => $value) {
                $this->showed_data[$value] = self::DEFAULT_LIST_COLUMN[$value];
            }
        } else {
            $this->showed_data = self::DEFAULT_LIST_COLUMN;
        }
    }

    private function setColumnHide()
    {
        foreach (self::DEFAULT_HIDE_DATA as $key => $value) {
            if (isset($this->showed_data[$value])) {
                unset($this->showed_data[$value]);
            }
        }
    }
}