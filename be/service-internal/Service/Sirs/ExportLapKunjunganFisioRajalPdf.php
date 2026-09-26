<?php

/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoPrint;
use yii\helpers\ArrayHelper;

class ExportLapKunjunganFisioRajalPdf extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
        ini_set("pcre.backtrack_limit", "500000000");
        $multiple = false;
        $cacheFiles = Yii::$app->cacheFiles;
        $dir = dirname(dirname(__DIR__));
        $rootPath = 'uploads';
        $path = $dir . '/' . $rootPath . '/' . $this->unique_str;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data laporan kunjungan fisio rajal!',
                'progress' => 80
            ]),
        ]);
        $data = $cacheFiles->get($this->unique_str);
        $attributes = ArrayHelper::getValue($data, 'attributes');
        $kodeDoc = ArrayHelper::getValue($data, 'kode_doc');

        if (empty($kodeDoc) || empty($attributes)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'failed',
                    'messageProcess' => 'Proses import PDF Gagal',
                    'progress' => 0
                ]),
            ]);
        } else {
            $print = new DocoPrint($kodeDoc);
            $print->attributes = $attributes;
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'finish',
                    'messageProcess' => 'Sedang mengimport data ke dalam PDF',
                    'progress' => 85
                ]),
            ]);

            $print->Output($multiple, $path);
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'finish',
                    'messageProcess' => 'Proses import PDF berhasil',
                    'progress' => 90
                ]),
            ]);
        }
        return json_encode([
            'service' => 'Sirs-ExportLapKunjunganFisioRajalPdf',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
