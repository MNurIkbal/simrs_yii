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
use app\modules\v1\models\LaporanPendapatanRuanganView;
use app\modules\v1\models\LaporanSensusHarianRjV;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstansId;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanPendapatanRuanganTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $totalPerPage;
    protected $countData;
    protected $manual_excel;
    protected $filter;
    protected $list_data = [];


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
        $model = new LaporanPendapatanRuanganView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $advancedFilter = ArrayHelper::getValue($request,'advanced-filter');
        $instalasi_id = ArrayHelper::getValue($request, 'instalasi_id', DocoConstants::INSTALASI_KASIR);
        $ruanganId = ArrayHelper::getValue($request, 'ruangan_id');
        if ($advancedFilter) {
            $advancedFilter = $request['advanced-filter'];
            if (!empty($advancedFilter['tgl_pendaftaran'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_pendaftaran']);
            }
            
            if ($instalasi_id == DocoConstants::INSTALASI_KASIR) {
                if (isset($advancedFilter['instalasi_ruangan'])) {
                    $instalasi_ruangan = $advancedFilter['instalasi_ruangan'];
                    $query->andWhere(['ruangan_id' => $instalasi_ruangan]);
                }
            }else {
                $query->andWhere(['instalasi_id' => $instalasi_id]);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');
        $filter = $this->filter;

        $row = [];
        $no = 1;
        $totalRs = $totalJp = 0;
        $carabayar_nama = $penjamin_nama = $ruangan_nama = '-';

        $data = $this->getDataAttributes();
        $no = 1;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
                $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
                $caraBayarPenjaminNama = "$caraBayarNama / $penjaminNama";
                $row[$key]['No']  = $no;
                $row[$key]['Tanggal Pendaftaran']  = !empty($value['tgl_pendaftaran']) ? date('d-M-Y', strtotime($value['tgl_pendaftaran'])) : '';
                $row[$key]['No Pendaftaran']  = !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
                $row[$key]['No Rekam Medik']  = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
                $row[$key]['Nama Pasien']  = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '';
                $row[$key]['Nama Pemeriksaan']  = !empty($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : '';
                $row[$key]['Cara Bayar / Penjamin']  = $caraBayarPenjaminNama;
                $row[$key]['Dokter Pengirim/Perujuk']  = !empty($value['nama_pegawai']) ? $value['nama_pegawai'] : '';
                $row[$key]['Nama Kelas Pelayanan']  = !empty($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '';
                $row[$key]['Total (Rp.)']  = !empty($value['total']) ? $value['total'] : 0;

                $totalRs += $value['jasa_rumahsakit'];
                $totalJp += $value['jasa_layanan'];
                $no++;
            }
        }

        $start   = date('Y-m-d');
        $end     = date('Y-m-d');
        $db = Yii::$app->db;
        if (isset($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];
            if (isset($advancedFilter['tgl_pendaftaran'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_pendaftaran']);
            }
            if (isset($advancedFilter['carabayar_id'])) {
                $carabayar_id = $advancedFilter['carabayar_id'];
                $data = $db->createCommand("
                    SELECT carabayar_nama
                    FROM carabayar_m
                    WHERE carabayar_id = {$carabayar_id}
                ")->queryOne();
                $carabayar_nama = $data['carabayar_nama'];
            }
            if (isset($advancedFilter['penjamin_id'])) {
                $penjamin_id = $advancedFilter['penjamin_id'];
                $data = $db->createCommand("
                    SELECT penjamin_nama
                    FROM penjamin_m
                    WHERE penjamin_id = {$penjamin_id}
                ")->queryOne();
                $penjamin_nama = $data['penjamin_nama'];
            }

            if (isset($advancedFilter['instalasi_ruangan'])) {
                $ruangan_id = $advancedFilter['instalasi_ruangan'];
                $data = $db->createCommand("
                    SELECT ruangan_nama, instalasi_nama
                    FROM ruangan_v
                    WHERE ruangan_id = {$ruangan_id}
                ")->queryOne();
                $ruangan_nama = $data['instalasi_nama'] . ' - ' . $data['ruangan_nama'];
            }
        }

        $header = [
            'Tanggal Pendaftaran' => date('d M Y', strtotime($start)) . ' Sampai Dengan ' . date('d M Y', strtotime($end)),
            'No Pendaftaran' => isset($filter['advanced-filter']['no_pendaftaran']) ? $filter['advanced-filter']['no_pendaftaran'] : '-',
            'No Rekam Medik' => isset($filter['advanced-filter']['no_rekam_medik']) ? $filter['advanced-filter']['no_rekam_medik'] : '-',
            'Nama Pasien' => isset($filter['advanced-filter']['nama_pasien']) ? $filter['advanced-filter']['nama_pasien'] : '-',
            'Cara Bayar' => $carabayar_nama,
            'Penjamin' => $penjamin_nama,
            'Dokter' => isset($filter['advanced-filter']['nama_pegawai']) ? $filter['advanced-filter']['nama_pegawai'] : '-',
            'Kelas Pelayanan' => isset($filter['advanced-filter']['kelaspelayanan_nama']) ? $filter['advanced-filter']['kelaspelayanan_nama'] : '-',
            'Ruangan' => $ruangan_nama,
        ];

        $footer = [
            'title' => ['TOTAL', 7],
            'data' => [
                'Rumah Sakit' => $totalRs ? $totalRs : '0',
                'Jasa Pelayanan' => $totalJp,
                'Total' => $totalRs + $totalJp,
            ]
        ];

        $path = 'web/'.'uploads/'. $this->unique_str . '.xlsx';

        $staticHeader = [
            [
                ['label' => 'No', 'rowspan' => 2],
                ['label' => 'Tanggal Pendaftaran', 'rowspan' => 2],
                ['label' => 'No Pendaftaran', 'rowspan' => 2],
                ['label' => 'No Rekam Medik', 'rowspan' => 2],
                ['label' => 'Nama Pasien', 'rowspan' => 2],
                ['label' => 'Nama Pemeriksaan', 'rowspan' => 2],
                ['label' => 'Cara Bayar / Penjamin', 'rowspan' => 2],
                ['label' => 'Dokter Pengirim / Perujuk', 'rowspan' => 2],
                ['label' => 'Nama Kelas Pelayanan', 'rowspan' => 2],
                ['label' => 'Total', 'rowspan' => 2],
            ],
        ];
        $nameHeaderSkipped = [
            'No',
            'Tanggal Pendaftaran',
            'No Pendaftaran',
            'No Rekam Medik',
            'Nama Pasien',
            'Nama Pemeriksaan',
            'Cara Bayar / Penjamin',
            'Dokter Pengirim / Perujuk',
            'Nama Kelas Pelayanan',
            'Total'
        ];

        $footerInfo = [];

        $filePath = DocoHelpers::exportExcel('Laporan Pendapatan Ruangan', $row, $header, [
            "skipIncrement" => true,
            "skipHeader" => true,
            "nameHeaderSkipped" => $nameHeaderSkipped,
            "customHeader" => $staticHeader,
        ], $footer, $footerInfo, true);
        $filePath->save($path);

        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        try {
            $response = $client->post('laporan-pendapatan-ruangan/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str . '.xlsx'),
                        'filename' => 'Laporan Pendapatan Ruangan.xlsx'
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

    private function setUrl()
    {
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];

        $client =  new Client([
            'base_uri' => "http://localhost:8858/rm/v1/",
            'headers' => $header
        ]);

        return $client;
    }
}
