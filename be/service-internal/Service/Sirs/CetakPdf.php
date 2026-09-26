<?php
/**
 * * @author Dede <dede.herdiana@sirs.co.id>
 * reusable export pdf
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoPrint;

class CetakPdf extends \Integrasi\Contracts\DocoImplement
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
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data.',
                'progress' => 80
			]),
		]);

		$attributes = $cacheFiles->get($this->unique_str);
		$print = new DocoPrint($this->kode_doc);
        $print->attributes = $attributes;
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Sedang mengexport data ke format PDF',
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
            'service' => 'Sirs-CetakPdf',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
	}
}
