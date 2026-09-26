<?php

namespace app\components\rabbitmq\excel;

use Doco\rabbitmq\task\ReportTask;
use app\modules\v1\models\LaporanMutasiObatView;
use Doco\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoSpout;
use Doco\components\DocoConstants;
use Integrasi\Components\DocoRestActiveFilter;

use app\modules\v1\models\Ruangan;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\JenisObatAlkes;

class LaporanMutasiObatAlkesExcelTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $customData;
    protected $footer;
    protected $title;
    protected $totalPerPage;

    protected $list_data = [];

    const DEFAULT_DATA_SHOW = [];
    const DEFAULT_HIDE_DATA = [];
    const DEFAULT_ROWSPAN = 2;

    const DEFAULT_LIST_COLUMN = [
        '0' => ['title' => 'No', 'data' => null, 'custom' => true], // 0
        '1' => ['title' => 'Tanggal Pemesanan', 'data' => 'tgl_pemesanan', 'custom' => true], // 1
        '2' => ['title' => 'Nomor Pemesanan', 'data' => 'no_pemesanan'], // 2
        '3' => ['title' => 'User Pemesanan', 'data' => 'pegawai_pemesan'], // 3
        '4' => ['title' => 'Kode Obat', 'data' => 'kode_obat'], // 4
        '5' => ['title' => 'Nama Obat', 'data' => 'nama_obat'], // 5
        '6' => ['title' => 'Jenis Obat', 'data' => 'jenisobatalkes_nama'], // 6
        '7' => ['title' => 'Qty Pesan', 'data' => 'qty_pesan'], // 7
        '8' => ['title' => 'UoM Pesan', 'data' => 'uom_input'], // 8
        '9' => ['title' => 'Qty Terima', 'data' => 'qty_terima'], // 9
        '10' => ['title' => 'UoM Terima', 'data' => 'uom_konversi'], // 10
        '11' => ['title' => 'Tanggal Pengiriman', 'data' => 'tgl_pengiriman', 'custom' => true], // 11
        '12' => ['title' => 'Nomor Pengiriman', 'data' => 'no_pengiriman'], // 12
        '13' => ['title' => 'Ruangan Pengirim', 'data' => 'ruangan_pengirim'], // 13
        '14' => ['title' => 'User Pengirim', 'data' => 'pegawai_pengirim'], // 14
        '15' => ['title' => 'Tanggal Penerimaan', 'data' => 'tgl_penerimaan', 'custom' => true], // 15
        '16' => ['title' => 'Nomor Penerimaan', 'data' => 'no_penerimaan'], // 16
        '17' => ['title' => 'Ruangan Penerima', 'data' => 'ruangan_penerima'], // 17
        '18' => ['title' => 'User Penerima', 'data' => 'pegawai_penerima'], // 18
        '19' => ['title' => 'Catatan Penerima', 'data' => 'catatan_penerima'], // 19
        '20' => ['title' => 'Harga Satuan', 'data' => 'harga_satuan'], // 20
        '21' => ['title' => 'Total Harga (Rp)', 'data' => 'harga_total'], // 21
    ];

    /** proses get Data */
    protected function prosesGetData()
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Mengambil dan menyiapkan data.',
				 'progress' => 20
			 ]),
        ]);
       
        $this->list_data = $this->getDataAttributes();
       
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 40
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
				 'messageProcess' => 'Sedang menyiapkan data, kedalam excel',
				 'progress' => 40
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

    private function getDataLaporanExcel()
    {
        $model = new LaporanMutasiObatView;
        $query = $model::find();

        $filter = $this->queryAdvancedFilter($this->filter);

        $query = $this->filterQuery($filter, $query);

        $query = DocoRestActiveFilter::advancedFilter($model, $query, $filter);

        $query->orderBy([
            'tgl_pemesanan' => SORT_ASC,
            'no_pemesanan' => SORT_ASC,
            'nama_obat' => SORT_ASC
        ]);

        return $query;
    }

    private function getDataAttributes()
    {
        $data = $this->getDataLaporanExcel()->asArray()->all();
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmpCache[] = $value;
            $no++;
        }

        return $tmpCache;
    }

    private function uploadFile()
    {
        $client = $this->setUrlGudang();
        try {
            $response = $client->post('laporan-mutasi-obat-alkes/drop-file', [
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

    private function setUrlGudang()
	{
        $params = Yii::$app->params['iniFile'];
        $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        $client =  new Client([
            'base_uri' => $urlBackend . 'gudang/v1/',
            'headers' => $header
        ]);

		return $client;
	}

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');

        $row = $this->generateExcelDataRow();

        $excel_konfig = $this->generateExcelKonfig();

        $path = 'web/'.'uploads/'. $this->unique_str . '.xlsx';

        $filePath = DocoHelpers::exportExcel('Laporan Mutasi Obat Alkes', $row, $excel_konfig['header'], [
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
                        } else if (in_array($column['title'], ['Tanggal Pemesanan', 'Tanggal Pengiriman', 'Tanggal Penerimaan'])) {
                            $row[$key][$column['title']] = !empty($value[$column['data']]) && $value[$column['data']] != '-'  ? date('d/m/Y', strtotime($value[$column['data']])) : '';
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

        private function generateExcelKonfig()
    {
        $header = [
            'Tanggal Pengiriman' => $this->getTglPengirimanFilter(),
            'Ruangan Pengirim' => $this->getRuanganPengirimFilter(),
            'Ruangan Penerima' => $this->getRuanganPenerimaFilter(),
            'Jenis Obat' => $this->getJenisObatAlkesFilter(),
            'Nama Obat' => $this->getObatAlkesFilter(),
        ];

        $staticHeader = [array_map(function($val) {
                return ['label' => $val['title'], 'rowspan' => self::DEFAULT_ROWSPAN];
        }, $this->showed_data)];

        $nameHeaderSkipped = array_column($this->showed_data, 'title');

        $footer = [];
        $footerInfo = [];

        return compact('header', 'staticHeader', 'nameHeaderSkipped', 'footer', 'footerInfo');
    }

    private function queryAdvancedFilter($filter)
    {
        if(isset($filter['advanced-filter']['tgl_pengiriman'])) {
            $explode = explode(' - ', $filter['advanced-filter']['tgl_pengiriman']);

            $filter['advanced-filter']['tgl_pengiriman_awal'] = date('Y-m-d H:i:s', strtotime($explode[0] . '00:00:00'));
            $filter['advanced-filter']['tgl_pengiriman_akhir'] = date('Y-m-d H:i:s', strtotime($explode[1] . '23:59:59'));

            unset($filter['advanced-filter']['tgl_pengiriman']);
        }

        if(isset($filter['advanced-filter']['ruangan_pengirim'])) {
            $filter['advanced-filter']['ruanganpengirim_id'] = $filter['advanced-filter']['ruangan_pengirim'];

            unset($filter['advanced-filter']['ruangan_pengirim']);
        }

        if(isset($filter['advanced-filter']['ruangan_penerima'])) {
            $filter['advanced-filter']['ruanganpenerima_id'] = $filter['advanced-filter']['ruangan_penerima'];

            unset($filter['advanced-filter']['ruangan_penerima']);
        }

        if(isset($filter['advanced-filter']['jenisobatalkes_nama'])) {
            $filter['advanced-filter']['jenisobatalkes_id'] = $filter['advanced-filter']['jenisobatalkes_nama'];

            unset($filter['advanced-filter']['jenisobatalkes_nama']);
        }

        if(isset($filter['advanced-filter']['nama_obat'])) {
            $filter['advanced-filter']['obatalkes_id'] = $filter['advanced-filter']['nama_obat'];

            unset($filter['advanced-filter']['nama_obat']);
        }

        return $filter;
    }

    private function filterQuery($advancedFilter, $query)
    {
        if (isset($advancedFilter['advanced-filter']['tgl_pengiriman_awal']) && isset($advancedFilter['advanced-filter']['tgl_pengiriman_akhir'])) {
            $query->andWhere(
                ['between', 'tgl_pengiriman', $advancedFilter['advanced-filter']['tgl_pengiriman_awal'], $advancedFilter['advanced-filter']['tgl_pengiriman_akhir']]
            );
        }

        return $query;
    }

    private function getTglPengirimanFilter()
    {
        $start   = date('Y-m-d');
        $end     = date('Y-m-d');
        if (isset($this->filter['advanced-filter']['tgl_pengiriman_awal'])) {
            $start = $this->filter['advanced-filter']['tgl_pengiriman_awal'];
        }

        if (isset($this->filter['advanced-filter']['tgl_pengiriman_akhir'])) {
            $end = $this->filter['advanced-filter']['tgl_pengiriman_akhir'];
        }
        return date('d M Y', strtotime($start)) . ' Sampai Dengan ' . date('d M Y', strtotime($end));
    }

    private function getRuanganPengirimFilter()
    {
        $ruangan_pengirim = '-';
        if (isset($this->filter['advanced-filter']['ruanganpengirim_id'])) {
            $ruangan_pengirim = Ruangan::find()->select('ruangan_nama')->where(['ruangan_id' => $this->filter['advanced-filter']['ruanganpengirim_id']])->scalar();
        }
        return $ruangan_pengirim;
    }

    private function getRuanganPenerimaFilter()
    {
        $ruangan_penerima = '-';
        if (isset($this->filter['advanced-filter']['ruanganpenerima_id'])) {
            $ruangan_penerima = Ruangan::find()->select('ruangan_nama')->where(['ruangan_id' => $this->filter['advanced-filter']['ruanganpenerima_id']])->scalar();
        }
        return $ruangan_penerima;
    }

    private function getJenisObatAlkesFilter()
    {
        $jenisobat = '-';
        if (isset($this->filter['advanced-filter']['jenisobatalkes_id'])) {
            $jenisobat = JenisObatAlkes::find()->select('jenisobatalkes_nama')->where(['jenisobatalkes_id' => $this->filter['advanced-filter']['jenisobatalkes_id']])->scalar();
        }
        return $jenisobat;
    }

    private function getObatAlkesFilter()
    {
        $nama_obat = '-';
        if (isset($this->filter['advanced-filter']['obatalkes_id'])) {
            $nama_obat = ObatAlkes::find()->select('obatalkes_nama')->where(['obatalkes_id' => $this->filter['advanced-filter']['obatalkes_id']])->scalar();
        }
        return $nama_obat;
    }

}