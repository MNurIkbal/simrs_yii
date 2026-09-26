<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoHelpers;

class ListPasienRawatInapExportExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
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

        $path = 'uploads/'. $this->unique_str . $this->ext;
        $header = $this->setUpHeader($advanced_filter);
        $custHeader = $this->custHeader();
        $footer = [];

        $filePath = DocoHelpers::exportExcel('Informasi Pasien Rawat Inap', $rows, $header, $custHeader, $footer, [], true);
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
            'service' => 'Sirs-ListPasienRawatInapExportExcel',
            'payload' => $this->attributes,
            'response'  => $this->unique_str,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    protected function setUpHeader($advFilter)
    {
        $start = $end = date('d F Y');
        $priode = [
            'Periode' => date("F Y", strtotime($start))
        ];
        if (!empty($advFilter)) {
            if (isset($advFilter['tgl_admisi'])) {
                $explodePr = explode(" - ", $advFilter['tgl_admisi']);
                if (count($explodePr) == 2) {
                    $start = date('d F Y', strtotime($explodePr[0]));
                    $end = date('d F Y', strtotime($explodePr[1]));
                }
            }
        }

        if ($start != $end) {
            $priode = [
                'Periode' => date("d F Y", strtotime($start)) . ' - ' . date("d F Y", strtotime($end))
            ];
        }
        return $priode;
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
            //     [
            //         'selectColumn' => 'B',
            //         'formatCode' => 'datetime'
            //     ],
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