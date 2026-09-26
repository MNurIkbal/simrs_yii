<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use Yii;
use GuzzleHttp\Client;
use Integrasi\Components\DocoPrint;

class CetakPdfCpptRajalTask extends ReportTask
{
    const CHANNEL = 'export-pdf:';

    protected $request;
    protected $kodeDoc;
    protected $token;
    protected $xOwner;
    protected $uniqueStr;
    protected $fileName;

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => self::CHANNEL.$this->uniqueStr,
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
            'channel' => self::CHANNEL . $this->uniqueStr,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang menyiapkan file PDF.',
                'progress' => 80
            ]),
        ]);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => self::CHANNEL . $this->uniqueStr,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam PDF.',
                'progress' => 85
            ]),
        ]);

        $this->cetakPdf();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => self::CHANNEL . $this->uniqueStr,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses import PDF berhasil.',
                'progress' => 90
            ]),
        ]);

        $this->uploadFile();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => self::CHANNEL . $this->uniqueStr,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil.',
                'progress' => 100,
                'filename' => $this->uniqueStr,
                'jenis_laporan' => $this->kodeDoc,
            ]),
        ]);
    }

    private function getDataAttributes()
    {
        $client = $this->setUrlRajal();
        try {
			$response = $client->post('cppt/cetak-list-cppt-rajal', [
				'query' => $this->request,
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

    private function setUrlRajal()
	{
        $params = Yii::$app->params['iniFile'];
        $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        return new Client([
            'base_uri' => $urlBackend . 'rajal/v1/',
            'headers' => $header
        ]);
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
        return new Client([
            'base_uri' => $urlBackend . 'penjaminasuransi/v1/',
            'headers' => $header
        ]);
	}

    private function cetakPdf()
    {
        ini_set('memory_limit', '512M');
		ini_set('memory_limit', '-1');
		$multiple = false;
        $dir = dirname(dirname(dirname(__DIR__)));
        $rootPath = 'web/'.'uploads';
		$dokumenName = $this->uniqueStr;
        if($this->fileName != null || $this->fileName != '') {
			$dokumenName = $this->fileName;
		}
		$path = $dir.'/'.$rootPath.'/'.$dokumenName;

		$attributes = $this->getDataAttributes();
		$attributes = !empty($attributes['response']) ? $attributes['response'] : [];
		$print = new DocoPrint($this->kodeDoc);
        $print->attributes = $attributes;
		
		$print->Output($multiple, $path);
    }

    private function uploadFile()
	{
        $is_ftp =  filter_var($this->is_ftp, FILTER_VALIDATE_BOOLEAN);
		if($is_ftp) {
			$client = $this->setUrlPenjamin();
		} else {
			$client = $this->setUrlRajal();
		}
        $dir = dirname(dirname(dirname(__DIR__)));
        $rootPath = 'web/'.'uploads';
		$dokumenName = $this->uniqueStr;
        if($this->fileName != null || $this->fileName != '') {
			$dokumenName = $this->fileName;
		}
		$path = $dir.'/'.$rootPath.'/'.$dokumenName;
		$ext = '.pdf';
		$pathContents = $path.$ext;
		try {
            if($is_ftp) {
				$params = [
					'filePath' => $pathContents,
					'pendaftaran_id' => $this->pendaftaran_id
				];
				$response = $client->get('inf-pasien-ranap-bpjs/ftp-send-file?'.http_build_query($params));
			} else {
                $response = $client->post('cppt/send-file', [
                    'query' => [
                        'filePath' => $this->uniqueStr
                    ],
                    'multipart' => [
                        [
                            'name' => 'file',
                            'contents' => file_get_contents($pathContents),
                            'filename' => 'CPPT Rajal.pdf'
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
}
