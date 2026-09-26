<?php

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Contracts\DocoImplement;
use Integrasi\Components\DocoHelpers;

class ListInformasiKartuStokExportExcel extends DocoImplement
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
        
        $namaObatAlkes = $cache->get($this->unique_str . '-nama-obat-alkes');
        $stokAwal = $cache->get($this->unique_str . '-stok-awal');

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
        $header = $this->setUpHeader($advanced_filter, $namaObatAlkes, $stokAwal);
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
            'service' => 'Sirs-ListInformasiKartuStokExportExcel',
            'payload' => $this->attributes,
            'response'  => $this->unique_str,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    protected function setUpHeader($advFilter, $namaObatAlkes, $stokAwal)
    {
        $range_tanggal = $advFilter['tanggal_transaksi'];
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        if(!is_null($range_tanggal)){
            $range_explode = explode(' - ', $range_tanggal);
            if (count($range_explode) == 2) {
                $start_date = date('Y-m-d',strtotime($range_explode[0]));
                $end_date = date('Y-m-d',strtotime($range_explode[1]));
            }
        }
        return [
            'Tanggal Transaksi' => date('d-M-Y',strtotime($start_date)) . ' s/d ' . date('d-M-Y',strtotime($end_date)),
            'Nama Obat/Alkes' => $namaObatAlkes,
            'Stok Awal' => $stokAwal
        ];
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
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'H',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'I',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'J',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'K',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'L',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'M',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'N',
                    'formatCode' => 'general',
                ]
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