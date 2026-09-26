<?php
/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoPrint;

class CetakDetailRincian extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
	{
		ini_set('pcre.backtrack_limit', 100000000);
		ini_set('memory_limit', '-1');
		$multiple = false;
        $cacheFiles = Yii::$app->cacheFiles;
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data detail rincian',
                'progress' => 80
			]),
		]);

		$attributes = $cacheFiles->get($this->unique_str);
		$kode_doc = !empty($attributes['kode_doc']) ? $attributes['kode_doc'] : 'cetak-detail-rincian';
		$attributes = !empty($attributes['attributes']) ? $attributes['attributes'] : [];
		$print = new DocoPrint($kode_doc);
        $print->attributes = $attributes;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Sedang mengimport data ke dalam PDF',
				 'progress' => 85
			 ]),
		]);
		
		$print->Output($multiple, $path);
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Proses import PDF berhasil',
				 'progress' => 90
			 ]),
		]);
		return json_encode([
            'service' => 'Sirs-CetakDetailRincian',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
	}
}
