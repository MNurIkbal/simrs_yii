<?php
/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoPrint;

class CetakDetailInvoiceGabung extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
	{
		ini_set('memory_limit', '-1');
        ini_set("pcre.backtrack_limit", "2000000");
		$multiple = false;
        $cacheFiles = Yii::$app->cacheFiles;
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
                'messageProcess' => 'Sedang Mengekstrak Data Detail Invoice',
                'progress' => 80
			]),
		]);

		$data = $cacheFiles->get($this->unique_str);
		$attributes = isset($data['attributes']) ? $data['attributes'] : null;
		$kode_doc = isset($data['kode_doc']) ? $data['kode_doc'] : null;
		if(empty($kode_doc) || empty($attributes)) {
			Yii::$app->redis->executeCommand('PUBLISH', [
				'channel' => 'invoice:'.$this->unique_str,
				'message' => json_encode([
					 'status' => 'failed', 
					 'messageProcess' => 'Proses import PDF Gagal',
					 'progress' => 0
				 ]),
			]);
		}
		else {
			$print = new DocoPrint($kode_doc);
			$print->attributes = $attributes;
			Yii::$app->redis->executeCommand('PUBLISH', [
				'channel' => 'invoice:'.$this->unique_str,
				'message' => json_encode([
					'status' => 'finish', 
					'messageProcess' => 'Sedang Mengimport Data ke Dalam PDF',
					'progress' => 85
				]),
			]);
			
			$print->Output($multiple, $path);
			Yii::$app->redis->executeCommand('PUBLISH', [
				'channel' => 'invoice:'.$this->unique_str,
				'message' => json_encode([
					'status' => 'finish', 
					'messageProcess' => 'Proses Import PDF Berhasil',
					'progress' => 90
				]),
			]);
		}
		return json_encode([
            'service' => 'Sirs-CetakDetailInvoiceGabung',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
	}
}
