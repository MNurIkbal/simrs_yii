<?php

namespace app\components\rabbitmq\excel;

use Doco\rabbitmq\task\ReportTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use app\modules\v1\models\InfoPermintaanMakanView;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Integrasi\Components\DocoRestActiveFilter;

class InfPermintaanMakanTask extends ReportTask
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

    const STATUS_PERIKSA = 'status_periksa_id';
    const DEFAULT_DATA_SHOW = [0, 1, 2];
    const DEFAULT_HIDE_DATA = [];
    const DEFAULT_ROWSPAN = 2;

    const DEFAULT_LIST_COLUMN = [
        '0' => ['title' => 'Tanggal Permintaan', 'data' => 'tgl_permintaanmakan', 'custom' => true], // 0
        '1' => ['title' => 'No. Permintaan', 'data' => 'no_permintaanmakan'], // 1
        '2' => ['title' => 'Pembayaran', 'data' => 'is_ditagihkan', 'custom' => true], // 2
        '3' => ['title' => 'Catatan Diet', 'data' => 'catatan_diet'], // 3
        '4' => ['title' => 'Ruangan/Kamar', 'data' => 'ruangan_nama', 'custom' => true], // 4
        '5' => ['title' => 'No. Pendaftaran', 'data' => 'no_pendaftaran'], // 5
        '6' => ['title' => 'No. Rekam Medik', 'data' => 'no_rekam_medik'], // 6
        '7' => ['title' => 'Nama Pasien', 'data' => 'nama_pasien'], // 7
        '8' => ['title' => 'Jenis Kelamin', 'data' => 'jenis_kelamin'], // 8
        '9' => ['title' => 'Tanggal Lahir', 'data' => 'tanggal_lahir', 'custom' => true], // 9
        '10' => ['title' => 'Jenis Diet', 'data' => 'jenisdiet_nama'], // 10
        '11' => ['title' => 'Diagnosa', 'data' => 'diagnosa'], // 11
        '12' => ['title' => 'Alergi', 'data' => 'riwayat_alergi'], // 12
        '13' => ['title' => 'Penjamin', 'data' => 'penjamin_nama'], // 13
        '14' => ['title' => 'Cara Bayar', 'data' => 'carabayar_nama'], // 14
        '15' => ['title' => 'Status', 'data' => 'status_permintaan'], // 15
        '16' => ['title' => 'DPJP', 'data' => 'dok_dpjp'], // 16
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

        $model = new InfoPermintaanMakanView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_permintaanmakan']) && $request['advanced-filter']['tgl_permintaanmakan'] != '') {
                $explode = explode(" - ", $request['advanced-filter']['tgl_permintaanmakan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));

                }
                unset($request['advanced-filter']['tgl_permintaanmakan']);
            }
            
            if(isset($request['advanced-filter']['is_ditagihkan']) && $request['advanced-filter']['is_ditagihkan'] != '') {
                if($request['advanced-filter']['is_ditagihkan'] == 'ditagihkan'){
                    $query = $query->andWhere(['=', 'is_ditagihkan', TRUE]);
                }else{
                    $query = $query->andWhere(['or', ['is_ditagihkan' => FALSE], ['is_ditagihkan' => NULL]]);

                }
                unset($request['advanced-filter']['is_ditagihkan']);
            }

        }
        $query->andWhere(['between', new \yii\db\Expression('(tgl_permintaanmakan::date)'), $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $request);
        return $query;
    }

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');
        $filter = $this->filter;

        $row = $this->generateExcelDataRow();

        $excel_konfig = $this->generateExcelKonfig();

        $path = 'web/'.'uploads/'. $this->unique_str . '.xlsx';

        $filePath = DocoHelpers::exportExcel('Informasi Permintaan Makan', $row, $excel_konfig['header'], [
            // "skipIncrement" => true,
            // "skipHeader" => true,
            // "nameHeaderSkipped" => $excel_konfig['nameHeaderSkipped'],
            // "customHeader" => $excel_konfig['staticHeader'],
        ], $excel_konfig['footer'], $excel_konfig['footerInfo'], true);
        $filePath->save($path);

        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function generateExcelKonfig()
    {
        $header = [];

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
                        }else if ($column['title'] == 'Pembayaran') {
                            $row[$key][$column['title']] = ($value['is_ditagihkan'] != null && $value['is_ditagihkan'] != '') ? 'Ditagihkan' : 'Tidak Ditagihkan';
                        } else if ($column['title'] == 'Tanggal Permintaan') {
                            $row[$key][$column['title']] = !empty($value[$column['data']]) ? date('d-M-Y H:i:s', strtotime($value[$column['data']])) : '';
                        } else if ($column['title'] == 'Tanggal Lahir') {
                            $row[$key][$column['title']] = !empty($value[$column['data']]) ? date('d-M-Y', strtotime($value[$column['data']])) : '';
                        } else if ($column['title'] == 'Ruangan/Kamar') {
                            $row[$key][$column['title']] = $this->getRuanganKamar($value);
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
        $client = $this->setUrlGizi();
        try {
            $response = $client->post('inf-permintaan-makan/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str . '.xlsx'),
                        'filename' => 'Informasi Permintaan Makan.xlsx'
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

    private function setUrlGizi()
	{
        $params = Yii::$app->params['iniFile'];
        $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        $client =  new Client([
            'base_uri' => $urlBackend . 'gizi/v1/',
            'headers' => $header
        ]);

		return $client;
	}

    private function getRuanganKamar($data)
    {
        return ArrayHelper::getValue($data, 'ruangan_nama', '').'/'.ArrayHelper::getValue($data, 'kamarruangan_nokamar').' - '.ArrayHelper::getValue($data, 'no_tempattidur');
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