<?php

namespace Integrasi\Service\Sirs;

use GuzzleHttp\Client;
use Yii;
use Integrasi\Components\DocoPrint;
use yii\helpers\ArrayHelper;

class CetakRincian extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $kode_report = 'new-rincian-po';
        $dir = dirname(dirname(__DIR__));
        $rootPath = 'uploads';
        $filePath = $dir . '/' . $rootPath . '/' . $this->unique_str;
        if (!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data ke dalam Zip',
                'progress' => 80
            ]),
        ]);
        
        $attributes = $cacheFiles->get($this->unique_str);
        if (empty($attributes)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'failed',
                    'messageProcess' => 'Proses ekstrak data ke dalam Zip gagal',
                ]),
            ]);
        } else {
            foreach ($attributes as $key => $value) {
                $no_transaksi = isset($value['no_transaksi']) ? $value['no_transaksi'] : '-';
                $type_po = isset($value['type_po']) ? $value['type_po'] : '-';
                $path = $filePath . '/Cetak Rincian' . $no_transaksi . '.pdf';
                Yii::$app->report->renderDesigner($kode_report, $no_transaksi, $type_po, $path);
            }
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'finish',
                    'messageProcess' => 'mengextract Pdf ke dalam Zip',
                    'progress' => 85
                ]),
            ]);

            /**
             * * ! convert folder ke dalam zip
             * */
            $fileName = $filePath . '.zip';
            if (file_exists($filePath)) {
                $zip = new \ZipArchive();
                if ($zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                    foreach (glob($filePath . '/*') as $file) {
                        $zip->addFile($file, basename($file));
                    }
                    $zip->close();
                    header('Content-disposition: attachment; filename=files.zip');
                    header('Content-type: application/zip');
                    readfile($fileName);
                }
            }
            
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'finish',
                    'messageProcess' => 'Proses import File berhasil',
                    'progress' => 90
                ]),
            ]);
        }
        $result = json_encode([
            'service' => 'Sirs-CetakRincian',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
        return $result;
    }

}