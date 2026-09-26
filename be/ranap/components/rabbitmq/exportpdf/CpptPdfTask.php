<?php

namespace app\components\rabbitmq\exportpdf;

use Doco\rabbitmq\task\ReportTask;
use Yii;
use GuzzleHttp\Client;
use Integrasi\Components\DocoPrint;

class CpptPdfTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $request;
    protected $totalPerPage;
    protected $kode_doc;
    protected $type;
    protected $token;
    protected $xOwner;
    protected $unique_str;
    protected $fileName;
    protected $countData;
    protected $manual_excel;
    protected $list_data = [];

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttributes();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
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
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang menyiapkan file PDF.',
                'progress' => 80
            ]),
        ]);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam PDF.',
                'progress' => 85
            ]),
        ]);

        $this->cetakPdf();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses import PDF berhasil.',
                'progress' => 90
            ]),
        ]);

        $this->uploadFile();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil.',
                'progress' => 100,
                'filename' => $this->unique_str,
                'jenis_laporan' => $this->kode_doc,
            ]),
        ]);
    }

    private function getDataAttributes()
    {
        $client = $this->setUrlRanap();
        try {
			$response = $client->post('cppt/data-pdf-cppt-ranap', [
				'query' => ['params' => $this->request],
                'multipart' => []
            ]);
            return json_decode($response->getBody(), true);
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
    }

    private function cetakPdf()
    {
        ini_set('memory_limit', '512M');
		ini_set('memory_limit', '-1');
		$multiple = false;
        $dir = dirname(dirname(dirname(__DIR__)));
        $rootPath = 'web/'.'uploads';
		$dokumenName = $this->unique_str;
        if($this->fileName != null || $this->fileName != '') {
			$dokumenName = $this->fileName;
		}
		$path = $dir.'/'.$rootPath.'/'.$dokumenName;

		$attributes = $this->getDataAttributes();
		$attributes = !empty($attributes['response']) ? $attributes['response'] : [];
		$print = new DocoPrint($this->kode_doc);
        $print->attributes = $attributes;
		
		$print->Output($multiple, $path);
    }

    private function uploadFile()
	{
        
        $is_ftp =  filter_var($this->is_ftp, FILTER_VALIDATE_BOOLEAN);
		if($is_ftp) {
			$client = $this->setUrlPenjamin();
		} else {
			$client = $this->setUrlRanap();
		}
        $dir = dirname(dirname(dirname(__DIR__)));
        $rootPath = 'web/'.'uploads';
		$dokumenName = $this->unique_str;
        if($this->fileName != null || $this->fileName != '') {
			$dokumenName = $this->fileName;
		}  
		$path = $dir.'/'.$rootPath.'/'.$dokumenName;
		$ext = '.pdf';
		$pathContents = $path.$ext;
		try {
			// Kondisi upload ftp disini.
			if($is_ftp) {
				$params = [
					'filePath' => $pathContents,
					'pendaftaran_id' => $this->pendaftaran_id
				];
				$response = $client->get('inf-pasien-ranap-bpjs/ftp-send-file?'.http_build_query($params));
			} else {
				$response = $client->post('cppt/send-file', [
                    'query' => [
                        'filePath' => $this->unique_str
                    ],
					'multipart' => [
						[
							'name' => 'file',
							'contents' => file_get_contents($pathContents),
                            'filename' => 'CPPT Ranap.pdf'
						],
					]
				]);
			}
            return json_decode($response->getBody(), true);
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
        
	}

    private function setUrlRanap()
	{
        $params = Yii::$app->params['iniFile'];
        $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        $client =  new Client([
            'base_uri' => $urlBackend . 'ranap/v1/',
            'headers' => $header
        ]);

		return $client;
	}

    private function setUrlPenjamin()
	{
        $params = Yii::$app->params['iniFile'];
        $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        $client =  new Client([
            'base_uri' => $urlBackend . 'penjaminasuransi/v1/',
            'headers' => $header
        ]);

		return $client;
	}
}
