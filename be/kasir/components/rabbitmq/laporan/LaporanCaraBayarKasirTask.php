<?php

namespace app\components\rabbitmq\laporan;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\rabbitmq\task\ReportTask;
use GuzzleHttp\Client;

class LaporanCaraBayarKasirTask extends ReportTask
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
            $tmp[2] = $value['Tanggal Pembayaran'];
            $tmp[3] = $value['No Pembayaran'];
            $tmp[4] = $value['No Pendaftaran'];
            $tmp[5] = $value['No Rekam Medis'];
            $tmp[6] = $value['Nama Pasien'];
            $tmp[7] = $value['Cara Bayar'];
            $tmp[8] = $value['Penjamin'];
            
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
            "customHeader" => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'B', 'formatCode' => 'shortdate'],
            ],
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
                    'Tanggal Pembayaran' => date('j M Y', strtotime($detail['tgl_pembayaran'])),
                    'No Pembayaran' => $detail['no_pembayaran'],
                    'No Pendaftaran' => $detail['no_pendaftaran'],
                    'No Rekam Medis' => $detail['no_rekam_medik'],
                    'Nama Pasien' => $detail['nama_pasien'],
                    'Cara Bayar' => $detail['carabayar_nama'],
                    'Penjamin' => $detail['penjamin_nama'],
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
			'base_uri' => $url_backend."kasir/v1/",
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
               'label' => 'Tanggal Pembayaran',
               'rowspan' => 2,
            ],
            [
               'label' => 'No Pembayaran',
               'rowspan' => 2,
            ],
            [
                'label' => 'No Pendaftaran',
                'rowspan' => 2,
            ],
            [
                'label' => 'No Rekam Medis',
                'rowspan' => 2,
            ],
            [
                'label' => 'Nama Pasien',
                'rowspan' => 2,
            ],
            [
                'label' => 'Cara Bayar',
                'rowspan' => 2,
            ],
            [
                'label' => 'Penjamin',
                'rowspan' => 2,
            ],
        ]
      ];
      return $column;
    }
}
