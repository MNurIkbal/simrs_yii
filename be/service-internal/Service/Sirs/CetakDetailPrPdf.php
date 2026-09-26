<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Service\Sirs;

use Integrasi\Components\DocoConstants;
use Yii;
use Integrasi\Components\DocoPrint;

class CetakDetailPrPdf extends \Integrasi\Contracts\DocoImplement {
    public function execute() {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $multiple = false;
        $cacheFiles = Yii::$app->cacheFiles;
        $dir = dirname(dirname(__DIR__));
        $rootPath = 'uploads';
        $path = $dir . '/' . $rootPath . '/' . $this->unique_str;

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data pdf',
                'progress' => 80
            ]),
        ]);
        $data = $cacheFiles->get($this->unique_str);
        $attributes = isset($data['attributes']) ? $data['attributes'] : null;


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
            $print = new DocoPrint(strtolower($this->type) == DocoConstants::JENIS_OBAT ? 'test-detail-pr-obat' : 'test-detail-pr');
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
            'service' => 'Sirs-CetakDetailPrPdf',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
