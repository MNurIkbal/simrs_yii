<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportTarifTindakan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage;
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = $footer = [];
        $no = 1;
        $db = Yii::$app->db;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengekstrak data master tarif tindakan',
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

        $searchJenisTindakan = $searchNamaTindakan = $searchKelasPelayanan = $searchCaraBayar = $searchPenjamin = $searchPerdaSK = $searchStatus = '';

        if (isset($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];
            if (!empty($advancedFilter['jenis_tindakan_paket'])) {
                $searchJenisTindakan = $advancedFilter['jenis_tindakan_paket'];
            }

            if (!empty($advancedFilter['nama_tindakan_paket'])) {
                $searchNamaTindakan = $advancedFilter['nama_tindakan_paket'];
            }

            if (!empty($advancedFilter['is_active'])) {
                $searchStatus = $advancedFilter['is_active'];
            }

            if (!empty($advancedFilter['kelaspelayanan_nama'])) {
                $searchKelasPelayanan = $advancedFilter['kelaspelayanan_nama'];
            }

            if (isset($advancedFilter['carabayar_id'])) {
                $carabayar_id = $advancedFilter['carabayar_id'];
                $data = $db->createCommand("
                    SELECT carabayar_nama
                    FROM carabayar_m
                    WHERE carabayar_id = {$carabayar_id}
                ")->queryOne();
                $searchCaraBayar = $data['carabayar_nama'];
            }

            if (isset($advancedFilter['penjamin_id'])) {
                $penjamin_id = $advancedFilter['penjamin_id'];
                $data = $db->createCommand("
                    SELECT penjamin_nama
                    FROM penjamin_m
                    WHERE penjamin_id = {$penjamin_id}
                ")->queryOne();
                $searchPenjamin = $data['penjamin_nama'];
            }
        }


        $header = [
            'Tanggal Unduh' => date('d-M-Y H:i:s'),
            'Jenis Paket/Tindakan' => $searchJenisTindakan,
            'Nama Paket/Tindakan' => $searchNamaTindakan,
            'Kelas Pelayanan' => $searchKelasPelayanan,
            'Cara Bayar' => $searchCaraBayar,
            'Penjamin' => $searchPenjamin,
            'Perda/SK' => $searchPerdaSK,
            'Status' => $searchStatus,
        ];
        $custHeader = $this->custHeader();
        $path = 'uploads/' . $this->unique_str . '.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel('Laporan Tarif Tindakan', $row, $header, [
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save($path);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90
            ]),
        ]);


        return json_encode([
            'service' => 'Sirs-ExportTarifTindakan',
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
                    'label' => 'Jenis Tindakan / Paket',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Nama Tindakan / Paket',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Kelas Pelayanan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Cara bayar',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Penjamin',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Perda / Sk',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Persen Cyto (%)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Persen Diskon (%)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Harga (Rp)',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Status',
                    'rowspan' => 2,
                ]
            ]
        ];
    }
}
