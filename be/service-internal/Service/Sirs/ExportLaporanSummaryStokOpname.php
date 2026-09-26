<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanSumStokOpnameView;
use Integrasi\Service\Sirs\Models\KonfigFarmasi;

class ExportLaporanSummaryStokOpname extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $row = [];
        $title = 'Laporan Summary SO';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data',
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

        $implementasi = KonfigFarmasi::find()->select(['is_tgl_implementasi_sesuai_verif'])->asArray()->one();

        $model = new LaporanSumStokOpnameView;

        if(isset($filter['advanced-filter'])) {
            $header = $model->setHeaderExcel($filter['advanced-filter']);
        } else {
            $header = [];
        }
        $custHeader = [
            [
                [
                    'label' => 'No',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Store',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No. Formulir SO',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Formulir SO',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Validasi SO',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Divalidasi Oleh',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Jenis Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Kode Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Satuan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Konversi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Weighted Average',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'System Stock Qty',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Physical Stock Qty',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Variance Qty',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Opening Total Batch Cost',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Ending Total Batch Cost',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Selisih Batch Cost',
                    'rowspan' => 2,
                ],
            ]
        ];

        $mappingData = $model->mappingDataExcel($row);
        $options = [
            "skipIncrement" => true,
            "customFormatCode" => [
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'date'
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode' => 'date'
                ],
            ],
            // "customHeader" => $custHeader,
        ];

        if(!$implementasi['is_tgl_implementasi_sesuai_verif']){
            $custHeader[0] = array_merge(array_slice($custHeader[0], 0,5),array(array('label' => 'Tanggal Implementasi','rowspan' => 2)), array_slice($custHeader[0], 5));
            array_push($options["customFormatCode"],['selectColumn' => 'F','formatCode' => 'date']);
        }

        $options["customHeader"] = $custHeader;

        $path = 'uploads/'. $this->unique_str .'.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data Summary Stock Opname',
                'progress' => 85
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel($title, $row, $header, $options, [], [], true);
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
            'service' => 'Sirs-ExportLaporanSummaryStokOpname',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
