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
use app\modules\v1\models\LaporanHasilSoView;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use Doco\models\Ruangan;
use Doco\Repositories\KonfigRepositories;
use Integrasi\Components\DocoRestActiveFilter;
use yii\db\Expression;

class LaporanHasilStokOpnameTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $unique_str;
    protected $totalPerPage;
    protected $countData;
    protected $filter;
    protected $manual_excel;
    protected $list_data = [];

    /** proses get Data */
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
                 'filename' => $this->unique_str,
             ]),
         ]);
    }

    private function getDataAttributes()
    {
        try {
            $data = $this->generateData()->asArray()->all();
            $no = 1;
            $prefix = 0;
            foreach ($data as $value) 
            {
                $tmp[1]  = $no;
                $tmp[2]  = isset($value['tgl_form_so']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_form_so']))) : '-';
                $tmp[3]  = isset($value['tgl_validasi_so']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_validasi_so']))) : '-';
                $tmp[4]  = isset($value['tgl_implementasi']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date('d M Y H:i:s', strtotime($value['tgl_implementasi']))) : '-';
                $tmp[5]  = !empty($value['validasi_by']) ? $value['validasi_by'] : '-';
                $tmp[6]  = !empty($value['no_form_so']) ? $value['no_form_so'] : '-';
                $tmp[7]  = !empty($value['instalasi_ruangan']) ? $value['instalasi_ruangan'] : '-';
                $tmp[8]  = !empty($value['jenis_obatalkes']) ? $value['jenis_obatalkes'] : '-';
                $tmp[9]  = !empty($value['kode_obat']) ? $value['kode_obat'] : '-';
                $tmp[10]  = !empty($value['nama_obat']) ? $value['nama_obat'] : '-';
                $tmp[11]  = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '-';
                $tmp[12]  = !empty($value['weighted_avg']) ? $value['weighted_avg'] : '0';
                $tmp[13]  = !empty($value['stok_sistem']) ? $value['stok_sistem'] : '0';
                $tmp[14]  = !empty($value['stok_fisik']) ? $value['stok_fisik'] : '0';
                $tmp[15]  = !empty($value['selisih']) ? $value['selisih'] : '0';
                $tmp[16]  = !empty($value['total_harga_sistem']) ? $value['total_harga_sistem'] : '0';
                $tmp[17]  = !empty($value['weighted_avg'] * $value['stok_fisik']) ? $value['weighted_avg'] * $value['stok_fisik'] : '0';
                $tmp[18]  = !empty($value['total_harga_selisi']) ? $value['total_harga_selisi'] : '0';

                $tmpCache[] = $tmp;

                $prefix++;
                $no++;

            }
            return $tmpCache;
        } catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
		}
    }

    private function generateData()
    {
        try {
            $request = $this->filter;

            $date = date('Y-m-d');
            $model = new LaporanHasilSoView;
            $query = $model::find(true);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            if(isset($request['advanced-filter'])) {
                if(isset($request['advanced-filter']['tgl_form_so'])) {
                    $explode = explode(" - ", $request['advanced-filter']['tgl_form_so']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($request['advanced-filter']['tgl_form_so']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if(isset($request['advanced-filter']['no_form_so'])){
                    $form = $request['advanced-filter']['no_form_so'];
                    $query->andWhere(['ILIKE', 'no_form_so', $form]);
                }

                if(isset($request['advanced-filter']['instalasi_ruangan'])) {
                    $ruangan_id = $request['advanced-filter']['instalasi_ruangan'];
                    $query->andWhere(['ruangan_id' => $ruangan_id]);
                    unset($request['advanced-filter']['instalasi_ruangan']);
                }
            }
            $query->andWhere(['between', 'tgl_form_so', $start, $end]);
        
            return DocoRestActiveFilter::advancedFilter($model,$query,$request);
        } catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
		}
    }

    private function generateExcel(){
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $filter = $this->filter;

        $row = [];
        $no = 1;

        $data = $this->getDataAttributes();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $row[] = $value;
            }
        }

        $konfig = KonfigRepositories::getKonfigFarmasi();
        $basePrice = $konfig['base_price_so'];

        $model = new LaporanHasilSoView;
        $start = date('d M Y 00:00:00');
        $end = date('d M Y 23:59:59');
        $query = $model::find()->select([
            new Expression("SUM(total_harga_selisi) AS  total_selisih"),
            new Expression("SUM(weighted_avg*stok_fisik) AS  total_fisik"),
            new Expression("SUM(total_harga_sistem) AS  total_sistem")
        ]);
        
        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('d M Y 00:00:00', strtotime($explode[0]));
                    $end = date('d M Y 23:59:59', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_form_so']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($filter['advanced-filter']['no_form_so'])){
                $form = $filter['advanced-filter']['no_form_so'];
                $query->andWhere(['ILIKE', 'no_form_so', $form]);
            }

            if(isset($filter['advanced-filter']['instalasi_ruangan'])) {
                $ruangan_id = $filter['advanced-filter']['instalasi_ruangan'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            }
        }
        
        $query->andWhere(['between', 'tgl_form_so', $start, $end]);
        $data = $query->asArray()->one();

        $totalSelisih = DocoHelpers::formatNumber($data['total_selisih']);
        $totalFisik = DocoHelpers::formatNumber($data['total_fisik']);

        $db = Yii::$app->db;
        $form = $instalasi_ruangan = '';
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_form_so']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_form_so']);
            }

            if (isset($filter['advanced-filter']['no_form_so'])) {
                $form = $filter['advanced-filter']['no_form_so'];
                unset($filter['advanced-filter']['no_form_so']);
            }
            
            if(isset($filter['advanced-filter']['instalasi_ruangan'])){
                $instalasi = $filter['advanced-filter']['instalasi_ruangan'];
                $ruangan = Ruangan::find()->where(['ruangan_id' => $instalasi])->select(['ruangan_nama'])->one();
                $instalasi_ruangan = $ruangan['ruangan_nama'];
                unset($filter['advanced-filter']['instalasi_ruangan']);
            }
        }
        
        $header = array(
            'Tanggal Form SO' => $start . ' s/d ' . $end,
            'No.Form SO' => $form,
            'Instalasi - Ruangan' => $instalasi_ruangan,
        );

        $footer = [
            'title' => ['TOTAL HARGA (Rp.)', 14],
            'data' => [
                '16' => $data['total_sistem'],
                '17' => $data['total_fisik'],
                '18' => $data['total_selisih'],
            ]
        ];

        $custHeader = $this->custHeader();
        if($basePrice == 0){
            $custHeader[0] = array_merge(array_slice($custHeader[0], 0,11),array(array('label' => 'Weighted Average (Rp.)','rowspan' => 2)), array_slice($custHeader[0], 11));
        }else{
            $custHeader[0] = array_merge(array_slice($custHeader[0], 0,11),array(array('label' => 'HNA','rowspan' => 2)), array_slice($custHeader[0], 11));
        }

        $path = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        
        $filePath = DocoHelpers::exportExcel('LAPORAN HASIL STOK OPNAME', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'B', 'formatCode' => 'datetime'],
                ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                ['selectColumn' => 'D', 'formatCode' => 'datetime'],
                ['selectColumn' => 'K', 'formatCode' => 'number'],
                ['selectColumn' => 'L', 'formatCode' => 'number'],
                ['selectColumn' => 'M', 'formatCode' => 'number'],
                ['selectColumn' => 'N', 'formatCode' => 'number'],
                ['selectColumn' => 'O', 'formatCode' => 'number'],
                ['selectColumn' => 'P', 'formatCode' => 'number'],
                ['selectColumn' => 'Q', 'formatCode' => 'number'],
                ['selectColumn' => 'R', 'formatCode' => 'number']
            ],
        ], $footer, [], true);
        $filePath->save($path);
        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function custHeader() 
    {
        return [
            [
                [
                    'label'=>'No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Form SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Validasi SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Implementasi SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Divalidasi oleh',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No. Form SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Instalasi - Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat Alkes',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Item',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Item',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Sistem',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Fisik',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Selisih',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga Sistem (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga Fisik (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga Selisih (Rp.)',
                    'rowspan'=>2,
                ],
            ]
        ];
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        try {
            $response = $client->post('laporan-hasil-stok-opname/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str .'.xlsx'),
                        'filename' => 'laporan-hasil-stok-opname.xlsx'
                    ],
                ]
            ]);
            return json_decode($response->getBody(),true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if($e->hasResponse()) {
                $response = $e->getResponse();
                return $response->getBody();
            }
        }
    }

    private function setUrl()
    {
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];

        $client =  new Client([
            'base_uri' => "http://localhost:8858/apotek/v1/",
            'headers' => $header
        ]);

        return $client;
    }

}