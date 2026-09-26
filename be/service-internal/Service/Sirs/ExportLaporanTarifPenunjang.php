<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Lookup;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\Models\InfoTarifPenunjangView;

class ExportLaporanTarifPenunjang extends \Integrasi\Contracts\DocoImplement
{
    public function execute() {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = $footer = [];
        $no = 1;

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data Laporan Tarif Penunjang',
                'progress' => 80 
            ]),
        ]);

        for($i = 0; $i < $totalPerPage; $i++) {
            $data = $cacheFiles->get($this->unique_str . '-' . $i);

            if(!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }

            $cacheFiles->delete($this->unique_str . '-' . $i);
        }


        $header = [];

        $custheader = $this->custHeader();
        $custFormat = $this->customFormatCode();
        $path = 'uploads/'.$this->unique_str.'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ])
        ]);

        $filePath = DocoHelpers::exportExcel('Laporan Tarif Penunjang', $row, $header, [
            'skipIncrement' => true,
            'customHeader' => $custheader,
            'customFormatCode' => $custFormat
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
            'service' => 'Sirs-ExportLaporanTarifPenunjang',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    private function custHeader() {
        return [
            [
                [
                    'label' => 'No',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Ruangan',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Penjamin',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Kelompok Pemeriksaan',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Jenis Pemeriksaan',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Nama Pemeriksaan',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Kelas Pelayanan',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Tarif Total',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Cyto Tindakan (%)',
                    'rowspan' => 2
                ],
                [
                    'label' => 'Diskon Tindakan (%)',
                    'rowspan' => 2
                ]
            ]
        ];
    }

    private function customFormatCode() {
        return [
            ['selectColumn' => 'B', 'general'],
            ['selectColumn' => 'C', 'general'],
            ['selectColumn' => 'D', 'general'],
            ['selectColumn' => 'E', 'general'],
            ['selectColumn' => 'F', 'general'],
            ['selectColumn' => 'G', 'general'],
            ['selectColumn' => 'H', 'number'],
            ['selectColumn' => 'I', 'number'],
            ['selectColumn' => 'J', 'number']
        ];
    }
}