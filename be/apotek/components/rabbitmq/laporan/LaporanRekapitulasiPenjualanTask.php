<?php

namespace app\components\rabbitmq\laporan;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\rabbitmq\task\ReportTask;
use GuzzleHttp\Client;

class LaporanRekapitulasiPenjualanTask extends ReportTask
{
    protected $list_data = [];
    protected $list_tmp_data = [];
    protected $headerFilter = [];
    protected $title;
    protected $sendToUrl;
    protected $base_uri;
    protected $getDataUrl;

    protected function prosesGetData()
    {
        $this->list_data = $this->getDataAttibutes();

        $no = 1;
        foreach ($this->list_data as $value) {  
            $tmp[1] = $no;
            $tmp[2] = $value['Tanggal'];
            $tmp[3] = $value['Kode Obat'];
            $tmp[4] = $value['Nama Obat'];
            $tmp[5] = $value['Qty'];
            $tmp[6] = $value['Satuan'];
            $tmp[7] = $value['Harga(Rp)'];
            $tmp[8] = $value['Total Harga(Rp)'];
            
            $this->list_tmp_data[] = $tmp;
            $no++;
        }

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                 'status' => 'finish', 
                 'messageProcess' => 'Berhasil menyiapkan data.',
                 'progress' => 50
             ]),
         ]);
    }

    protected function prosesExport()
    {
        $this->generateToExcel();
    }

    protected function generateToExcel() 
    {
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
				'messageProcess' => 'Sedang mengekstrak data',
				'progress' => 80
			]),
		]);

		$footer = [];
        $header = $this->headerFilter;

		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
				'messageProcess' => 'Sedang mengimport data ke dalam excel',
				'progress' => 85
			]),
		]);

        $custHeader = $this->getColumns();
        $title = $this->title;
        $path = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $filePath = DocoHelpers::exportExcel($title, $this->list_tmp_data, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save($path);

		Yii::$app->redis->executeCommand('PUBLISH', [
		'channel' => 'export-excel:'.$this->unique_str,
		'message' => json_encode([
				'status' => 'finish', 
				'messageProcess' => 'Proses import excel berhasil',
				'progress' => 100,
                'filename' => $this->unique_str,
			]),
		]);
    }

    private function getDataAttibutes()
    {
        $client = $this->setEndPoint();
        try {
            $response = $client->get($this->getDataUrl, [
                'query' => $this->filter
            ]);
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);

            $row = [];
            foreach ($response as $detail) {
                $newRow = [
                    // 'No' => $detail['no'],
                    'Tanggal' => $detail['tgl_pelayanan'],
                    'Kode Obat' => $detail['kode_obat'],
                    'Nama Obat' => $detail['nama_obat'],
                    'Qty' => $detail['qty'],
                    'Satuan' => $detail['satuan_kecil'],
                    'Harga(Rp)' => $detail['harga_netto'],
                    'Total Harga(Rp)' => $detail['total'],
                ];
                $row[] = $newRow;
            }

            return $row;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return $response->getBody();
            }
        }
    }

    private function setEndPoint()
	{
        $url_backend = Yii::$app->params['url_backend'];
        $header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => $url_backend."apotek/v1/",
			'headers' => $header
		]);
		return $client;
	}

    private function getColumns()
    {
      $column = [
        [
            [
               'label' => 'No',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal',
               'rowspan' => 2,
            ],
            [
               'label' => 'Kode Obat',
               'rowspan' => 2,
            ],
            [
                'label' => 'Nama Obat',
                'rowspan' => 2,
            ],
            [
                'label' => 'Qty',
                'rowspan' => 2,
            ],
            [
                'label' => 'Satuan',
                'rowspan' => 2,
            ],
            [
                'label' => 'Harga(Rp)',
                'rowspan' => 2,
            ],
            [
                'label' => 'Total Harga(Rp)',
                'rowspan' => 2,
            ],
        ]
      ];
      return $column;
    }
}