<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Client;

class ExportExcelLaporanPersalinan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $totalJenis = $this->totalJenis;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data kunjungan',
                'progress' => 80
            ]),
        ]);
        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str .'-'. $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str .'-'. $x);
        }

        $start   = date('Y-m-d');
        $end     = date('Y-m-d');

        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl_lahir_bayi'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_lahir_bayi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_lahir_bayi']);
            }
        }

        $header['Periode Tanggal Lahir'] = date('d-m-Y', strtotime($start))." - ".  date('d-m-Y', strtotime($end));

        if(!empty($totalJenis)) {
            foreach($totalJenis as $k => $v) {
                $header['Total Jenis Pemeriksaan '.$v['jenis_persalinan_nama']] = $v['jumlah']; 
            }
        }

        $custHeader = [
            [
                [
                    'label' => 'No',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'Nama Bayi',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'Tanggal Lahir',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'No Rekam Medik Bayi',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'Berat (gram)',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'Nama Ibu',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'No Rekam Medik Ibu',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'Dokter Penanggung Jawab',
                    'rowspan'=>2,
                ],
                [
                    'label' => 'Jenis Persalinan',
                    'rowspan'=>2,
                ],
            ]
        ];

        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('Laporan Persalinan', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save($path);

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90
            ]),
        ]);


        return json_encode([
            'service' => 'Sirs-ExportExcelLaporanPersalinan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}