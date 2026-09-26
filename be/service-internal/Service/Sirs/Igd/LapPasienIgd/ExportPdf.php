<?php 

namespace Integrasi\Service\Sirs\Igd\LapPasienIgd;

use Yii;
use Integrasi\Components\DocoPrint;
use yii\helpers\ArrayHelper;

class ExportPdf extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $multiple = false;
        $cacheFiles = Yii::$app->cacheFiles;
        $dir = dirname(dirname(dirname(dirname(__DIR__))));
        $rootPath = 'uploads';
        $path = $dir . '/' . $rootPath . '/' . $this->unique_str;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data laporan!',
                'progress' => 80
            ]),
        ]);

        $data = $cacheFiles->get($this->unique_str);
        $attributes = ArrayHelper::getValue($data, 'attributes');
        if (empty($attributes)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'failed',
                    'messageProcess' => 'Proses import PDF Gagal',
                    'progress' => 0
                ]),
            ]);
        } else {
            $print = new DocoPrint('lap-igd');
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
            'service' => 'Sirs-LapPasienIgd',
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $data
        ]);
    }
}
