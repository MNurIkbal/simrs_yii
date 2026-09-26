<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Pasien;
use Integrasi\Service\Sirs\Models\ObatAlkes;

class ExportRiwayatObatPaien extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
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
                'messageProcess' => 'Sedang mengekstrak data Riwayat Obat Pasien',
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

        $start = date('d M Y');
        $end = date('d M Y');
        $ruangan = $jenis_adjustment = $jenis_obatalkes = "";
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_transaksi'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_transaksi']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_transaksi']);
            }

            if(isset($filter['advanced-filter']['pasien_id'])){
                $pasien_id = $filter['advanced-filter']['pasien_id'];
                $data = Pasien::find()->where(['pasien_id' => $pasien_id])->select(['nama_pasien'])->one();
                $pasien_nama = $data['nama_pasien'];
            }

            if(isset($filter['advanced-filter']['obatalkes_id'])){
                $obatalkes_id = $filter['advanced-filter']['obatalkes_id'];
                $data = ObatAlkes::find()->where(['obatalkes_id' => $obatalkes_id])->select(['obatalkes_namalain'])->one();
                $jenis_obatalkes = $data['obatalkes_namalain'];
            }
        }

        $header = [
            'Tanggal' => $start . ' s/d ' . $end,
            'Nama Pasien' => $pasien_nama,
            'Nama Obat Alkes' => $jenis_obatalkes
        ];

        $custHeader = $this->custHeader();
        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        
        $filePath = DocoHelpers::exportExcel('RIWAYAT OBAT PASIEN', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'B', 'formatCode' => 'datetime']
            ],
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
            'service' => 'Sirs-ExportRiwayatObatPaien',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader() 
    {
        return [
            [
                [
                    'label' => 'No',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Transaksi',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No.Resep',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Obat Alkes',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Signa',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Qty',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Satuan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Instalasi Ruangan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Cara Bayar-Penjamin',
                    'rowspan' => 2,
                ],
            ]
        ];
    }
}
