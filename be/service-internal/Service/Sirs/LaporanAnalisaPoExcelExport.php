<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\LaporanAnalisaPurchaseOrderView;
use Integrasi\Components\DocoHelpers;

class LaporanAnalisaPoExcelExport extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $model = new LaporanAnalisaPurchaseOrderView;
        $cache = Yii::$app->cache;

        $filter = $this->filter;
        $advanced_filter = isset($filter['advanced-filter']) ? $filter['advanced-filter'] : [];

        $countData = $cache->get($this->unique_str . '-total-data');
        $countIndex = $cache->get($this->unique_str . '-total-index');

        $rows = [];
        for ($x = 0; $x < $countIndex; $x++) {
            $tempRows = $cache->get($this->unique_str . '-data' . $x);
            if (!empty($tempRows)) {
                for ($y = 0; $y < count($tempRows); $y++) {
                    $rows[] = $tempRows[$y];
                }
                $cache->delete($this->unique_str .'-data'. $x);
            }
        }

        $profil = $this->getRsProfil();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Parsing data hasil pencarian ke excel',
                'progress' => 80
            ]),
        ]);

        $path = 'uploads/'. $this->unique_str .'.xlsx';
        $header = $model->setHeaderExcel($this->setUpHeader($advanced_filter));
        $custHeader = $this->custHeader();
        $footer = [];

        $filePath = DocoHelpers::exportExcel('Laporan Analisa Purchase Order', $rows, $header, $custHeader, $footer, [], true);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Membuat file Excel',
                'progress' => 90
            ]),
        ]);

        $filePath->save($path);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'File excel berhasil dibuat, memulai proses upload',
                'progress' => 95
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-LaporanAnalisaPoExcelExport',
            'payload' => $this->attributes,
            'response'  => $this->unique_str,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    protected function setUpHeader($advFilter)
    {
        $reportHeader = [];
        foreach($advFilter as $key => $value) {
            if (!empty($value)) {
                $reportHeader[$key] = $value;
            }
        }
        return $reportHeader;
    }

    protected function custHeader()
    {
        return [
            "skipIncrement" => false,
            "titleStyle" => [
                "fontSize" => 11,
                "alignment" => "left"
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'I',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'J',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'K',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'O',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'U',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'W',
                    'formatCode' => 'number2'
                ],
                [
                    'selectColumn' => 'X',
                    'formatCode' => 'number2'
                ],
                [
                    'selectColumn' => 'Y',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'AB',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'AD',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'L',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'M',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'V',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'Z',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'P',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'S',
                    'formatCode' => 'number2'
                ],
                [
                    'selectColumn' => 'T',
                    'formatCode' => 'number2'
                ],
                [
                    'selectColumn' => 'H',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'Q',
                    'formatCode' => 'datetime'
                ],
                [
                    'selectColumn' => 'R',
                    'formatCode' => 'number2'
                ],
            ],
        ];
    }

    private function getRsProfil()
    {
        return Yii::$app->db->createCommand("
            SELECT nokode_rumahsakit, nama_rumahsakit FROM profilrumahsakit_m
        ")->queryOne();
    }
}
