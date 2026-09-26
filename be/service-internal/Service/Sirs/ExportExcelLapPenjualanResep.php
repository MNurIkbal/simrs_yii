<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Lookup;

class ExportExcelLapPenjualanResep extends \Integrasi\Contracts\DocoImplement
{
    const BMHP = 'Penjualan BMHP';

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
                'messageProcess' => 'Sedang mengekstrak data Penjualan Resep',
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
        $start   = date('d M Y');
        $end     = date('d M Y');
        $jenisPenjualan = $caraBayar = $penjamin = $noRm = $namaPasien = $noResep = $psikotropika = $jenis_resep = $narkotika = $namaObat = $ruangan = $dokter = $jenisobatalkes_nama  = '-';

        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgltransaksi'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgltransaksi']);
                if(count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgltransaksi']);
            }
            if(isset($filter['advanced-filter']['jenispenjualan'])) {
                $jenisPenjualan = $filter['advanced-filter']['jenispenjualan'];
                if (is_string($jenisPenjualan)) {
                    $jenisPenjualan = self::BMHP;
                } else {
                    $modelLookup = Lookup::findOne($jenisPenjualan);
                    $jenisPenjualan = ($modelLookup) ? $modelLookup['lookup_name'] : '-';
                }
            }
            if(isset($filter['advanced-filter']['nama_obat'])) {
                $namaObat = $filter['advanced-filter']['nama_obat'];
            }
            if(isset($filter['advanced-filter']['carabayar_nama'])) {
                $caraBayar = $filter['advanced-filter']['carabayar_nama'];
                $modelCaraBayar = CaraBayar::findOne($caraBayar);
                $caraBayar = ($modelCaraBayar) ? $modelCaraBayar['carabayar_nama'] : '-';
            }
            if(isset($filter['advanced-filter']['penjamin_nama'])) {
                $penjamin = $filter['advanced-filter']['penjamin_nama'];
            }
            if(isset($filter['advanced-filter']['nama_dokter'])) {
                $dokter = $filter['advanced-filter']['nama_dokter'];
            }
            if(isset($filter['advanced-filter']['ruangan_nama'])) {
                $ruangan = $filter['advanced-filter']['ruangan_nama'];
            }
            if(isset($filter['advanced-filter']['jenis_resep'])) {
                $jenis_resep = $filter['advanced-filter']['jenis_resep'];
            }
            if(isset($filter['advanced-filter']['is_psycothropica'])) {
                $psikotropika = $filter['advanced-filter']['is_psycothropica'];
                if($psikotropika == 'false'){
                    $psikotropika = 'Tidak';
                }if($psikotropika == 'true'){
                    $psikotropika = 'Ya';
                }
            }
            if(isset($filter['advanced-filter']['is_narcotic'])) {
                $narkotika = $filter['advanced-filter']['is_narcotic'];
                if($narkotika == 'false'){
                    $narkotika = 'Tidak';
                }if($narkotika == 'true'){
                    $narkotika = 'Ya';
                }
            }
            if(isset($filter['advanced-filter']['no_rekammedik'])) {
                $noRm = $filter['advanced-filter']['no_rekammedik'];
            }
            if(isset($filter['advanced-filter']['nama_pasien'])) {
                $namaPasien = $filter['advanced-filter']['nama_pasien'];
            }
            if(isset($filter['advanced-filter']['noresep'])) {
                $noResep = $filter['advanced-filter']['noresep'];
            }
            if(isset($filter['advanced-filter']['jenisobatalkes_nama'])) {
                $jenisobatalkes_nama = $filter['advanced-filter']['jenisobatalkes_nama'];
            }
        }

        $header = [
            'Tanggal Penjualan' => $start.' s/d '.$end,
            'Jenis Penjualan' => $jenis_resep,
            'Nama Obat' => $namaObat,
            'Jenis Obat' => $jenisobatalkes_nama,
            'Cara Bayar' => $caraBayar,
            'Penjamin' => $penjamin,
            'Psikotropika' => $psikotropika,
            'Narkotika' => $narkotika,
            'No Rekam Medik' => $noRm,
            'Nama Pasien' => $namaPasien,
            'Nomor Resep' => $noResep,
            'Nama Dokter' => $dokter,
            'Ruangan' => $ruangan,
        ];
        $custHeader = $this->custHeader();
        $customFormatCode = $this->customFormatCode();
        $path = 'uploads/'. $this->unique_str .'.xlsx';
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('Laporan Penjualan Obat Alkes', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            'customFormatCode' => $customFormatCode
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
            'service' => 'Sirs-ExportExcelLapPenjualanResep',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader() 
    {
        return [
            [
                [
                    'label'=>'No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Transaksi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Pendaftaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Penjualan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Resep',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Dokter',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Rekam Medik',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Pasien',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Lahir',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'R-Ke',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat',
                    'rowspan'=>2, //14
                ],
                [
                    'label'=>'Jumlah Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Tagihan (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara Bayar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Penjamin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Formularium',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Psikotropika',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Narkotika',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Supplier',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Principle',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'User',
                    'rowspan'=>2,
                ],
            ]
        ];
    }

    private function customFormatCode()
    {
        return [
            ['selectColumn' => 'B', 'formatCode' => 'date'],
            ['selectColumn' => 'C', 'formatCode' => 'general'],
            ['selectColumn' => 'D', 'formatCode' => 'general'],
            ['selectColumn' => 'E', 'formatCode' => 'general'],
            ['selectColumn' => 'F', 'formatCode' => 'general'],
            ['selectColumn' => 'G', 'formatCode' => 'general'],
            ['selectColumn' => 'H', 'formatCode' => 'general'],
            ['selectColumn' => 'I', 'formatCode' => 'general'],
            ['selectColumn' => 'J', 'formatCode' => 'date'],
            ['selectColumn' => 'K', 'formatCode' => 'general'],
            ['selectColumn' => 'L', 'formatCode' => 'general'],
            ['selectColumn' => 'M', 'formatCode' => 'general'],
            ['selectColumn' => 'N', 'formatCode' => 'number'],
            ['selectColumn' => 'O', 'formatCode' => 'general'], //14
            ['selectColumn' => 'P', 'formatCode' => 'general'],
            ['selectColumn' => 'Q', 'formatCode' => 'general'],
            ['selectColumn' => 'R', 'formatCode' => 'general'],
            ['selectColumn' => 'S', 'formatCode' => 'general'],
            ['selectColumn' => 'T', 'formatCode' => 'general'],
            ['selectColumn' => 'U', 'formatCode' => 'general'],
            ['selectColumn' => 'V', 'formatCode' => 'general'],
        ];
    }
}
