<?php
/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoPrint;

class CetakLaporanPasienSudahBayar extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
	{
		ini_set('memory_limit', '-1');
		$multiple = false;
        $cacheFiles = Yii::$app->cacheFiles;
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Lap Pasien Sudah Bayar',
                'progress' => 80
			]),
		]);

		$attributes = $cacheFiles->get($this->unique_str);
		$attributes = !empty($attributes['response']) ? $attributes['response'] : [];
		$print = new DocoPrint($this->kode_doc);
        $print->attributes = $attributes;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Sedang mengimport data ke dalam PDF',
				 'progress' => 85
			 ]),
		]);
		
		$print->Output($multiple, $path);
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Proses import PDF berhasil',
				 'progress' => 90
			 ]),
		]);
		return json_encode([
            'service' => 'Sirs-CetakLaporanPasienSudahBayar',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
	}
}
