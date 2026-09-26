<?php
namespace Integrasi\Service\Sirs\Igd\LapPasienIgd;

use Yii;
use Integrasi\Components\DocoHelpers;

class ExportExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data',
                'progress' => 80
            ]),
        ]);
        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str . '-' . $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str . '-' . $x);
        }

        $start   = date('Y-m-d');
        $end     = date('Y-m-d');

        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
        }

        $header = [
            'PERIODE' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
        ];

        $path = 'uploads/' . $this->unique_str . '.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('LAPORAN PASIEN RAWAT DARURAT', $row, $header, [
            "skipIncrement" => true,
            // 'customHeader' => [],
        ], $footer, [], true);
        $filePath->save($path);

        // $filePath = DocoHelpers::exportExcel('Laporan Daftar Pasien Rawat Darurat', $result, [], array(
        //     "subTitle" => $header,
        //     "uploadPath" => "./uploads",
        // ),[],[],true);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90,
            ]),
        ]);


        return json_encode([
            'service' => 'Sirs-LapPasienIgd-Export-Excel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
