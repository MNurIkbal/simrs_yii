<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Service\Sirs\Models\PegawaiMasterView;
use Integrasi\Service\Sirs\Models\CaraBayar;
use Integrasi\Service\Sirs\Models\Penjamin;
use Integrasi\Service\Sirs\Models\Ruangan;
use Integrasi\Components\DocoHelpers;

class ExportLaporanRekapJasaDokter extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;
        $db = Yii::$app->db;
        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Rekap Jasa Dokter',
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
        $startPulang = $endPulang = ""; $status_jasa = "-"; $nama_pegawai = []; $ruangan_nama = []; $carabayar_nama = []; $penjamin_nama = []; $kelas_pelayanan = []; $pelayanan = []; $status_bayar = [];
        if(isset($filter['advanced-filter'])) {
            $advancedFilter = $filter['advanced-filter'];
            if(isset($advancedFilter['tgl_tindakan'])) {
                $explode = explode(" - ", $advancedFilter['tgl_tindakan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_tindakan']);
            }
            if(isset($advancedFilter['tgl_pasienpulang']) && !empty($advancedFilter['tgl_pasienpulang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pasienpulang']);
                if(count($explode) == 2) {
                    $startPulang = date('Y-m-d', strtotime($explode[0]));
                    $endPulang = date('Y-m-d', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_pasienpulang']);
            }
            if (isset($advancedFilter['dokterpenanggungjawab_id']) && !empty($advancedFilter['dokterpenanggungjawab_id'])) {
                $pegawai = PegawaiMasterView::find()->where(['pegawai_id' => $advancedFilter['dokterpenanggungjawab_id']])->all();
                if(!empty($pegawai)) {
                    foreach($pegawai as $k => $v) {
                        $nama_pegawai[] = strtoupper($v->nama_pegawai);
                    }
                    $nama_pegawai = implode(", ", $nama_pegawai);
                }
            }

            if (isset($advancedFilter['status_bayar_id']) && !empty($advancedFilter['status_bayar_id'])) {
                $status_bayar_id = implode(",", $advancedFilter['status_bayar_id']);
                $dataLookup = $db->createCommand("SELECT lookup_name FROM lookup_m WHERE lookup_id IN ({$status_bayar_id})")->queryAll();
                if(!empty($dataLookup)) {
                    foreach($dataLookup as $k => $v) {
                        $status_bayar[] = strtoupper($v["lookup_name"]);
                    }
                    $status_bayar = implode(", ", $status_bayar);
                }
            }

            if (isset($advancedFilter['komponentarif_nama']) && !empty($advancedFilter['komponentarif_nama'])) {
                $komponentarif_nama = $advancedFilter['komponentarif_nama'];
            } else {
                $komponentarif_nama = '-';
            }

            if (isset($advancedFilter['daftartindakan_nama']) && !empty($advancedFilter['daftartindakan_nama'])) {
                $daftartindakan_nama = $advancedFilter['daftartindakan_nama'];
            } else {
                $daftartindakan_nama = '-';
            }

            if (isset($advancedFilter['jenis_transaksi']) && !empty($advancedFilter['jenis_transaksi'])) {
                $jenis_transaksi = $advancedFilter['jenis_transaksi'];
            } else {
                $jenis_transaksi = '-';
            }

            if (isset($advancedFilter['carabayar_id']) && !empty($advancedFilter['carabayar_id'])) {
                $dataCaraBayar = CaraBayar::find()->where(['carabayar_id' => $advancedFilter['carabayar_id']])->all();
                if(!empty($dataCaraBayar)) {
                    foreach($dataCaraBayar as $k => $v) {
                        $carabayar_nama[] = strtoupper($v->carabayar_nama);
                    }
                    $carabayar_nama = implode(", ", $carabayar_nama);
                }
            }

            if(isset($advancedFilter['penjamin_id']) && !empty($advancedFilter['penjamin_id'])) {
                $dataPenjamin = Penjamin::find()->where(['penjamin_id' => $advancedFilter['penjamin_id']])->all();
                if(!empty($dataPenjamin)) {
                    foreach($dataPenjamin as $k => $v) {
                        $penjamin_nama[] = strtoupper($v->penjamin_nama);
                    }
                    $penjamin_nama = implode(", ", $penjamin_nama);
                }
            }

            if (isset($advancedFilter['ruangan_id']) && !empty($advancedFilter['ruangan_id'])) {
                $dataRuangan = Ruangan::find()->where(['ruangan_id' => $advancedFilter['ruangan_id']])->all();
                if(!empty($dataRuangan)) {
                    foreach($dataRuangan as $k => $v) {
                        $ruangan_nama[] = strtoupper($v->ruangan_nama);
                    }
                    $ruangan_nama = implode(", ", $ruangan_nama);
                }
            }

            if (isset($advancedFilter['jenis_transaksi']) && !empty($advancedFilter['jenis_transaksi'])) {
                $jenis_transaksi = $advancedFilter['jenis_transaksi'];
            } else {
                $jenis_transaksi = '-';
            }

            if (isset($advancedFilter['komponentarif_nama']) && !empty($advancedFilter['komponentarif_nama'])) {
                $komponentarif_nama = $advancedFilter['komponentarif_nama'];
            } else {
                $komponentarif_nama = '-';
            }

            if (isset($advancedFilter['daftartindakan_nama']) && !empty($advancedFilter['daftartindakan_nama'])) {
                $daftartindakan_nama = $advancedFilter['daftartindakan_nama'];
            } else {
                $daftartindakan_nama = '-';
            }

            if (isset($advancedFilter['kelaspelayanan_id']) && !empty($advancedFilter['kelaspelayanan_id'])) {
                $kelaspelayanan_id = implode(",", $advancedFilter['kelaspelayanan_id']);
                $dataKelas = $db->createCommand("SELECT kelaspelayanan_nama FROM  kelaspelayanan_m WHERE kelaspelayanan_id IN ({$kelaspelayanan_id})")->queryAll();
                if(!empty($dataKelas)) {
                    foreach($dataKelas as $k => $v) {
                        $kelas_pelayanan[] = strtoupper($v["kelaspelayanan_nama"]);
                    }
                    $kelas_pelayanan = implode(", ", $kelas_pelayanan);
                }
            }

            if (isset($advancedFilter['pelayanan']) && !empty($advancedFilter['pelayanan'])) {
                $pelayanan = !empty($advancedFilter['pelayanan']) ? implode(", ", $advancedFilter['pelayanan']) : [];
            }

            if (isset($advancedFilter['tgl_flag']) && !empty($advancedFilter['tgl_flag'])) {
                $tgl_flag = $advancedFilter['tgl_flag'];
            } else {
                $tgl_flag = '-';
            }

            if (isset($advancedFilter['flag_jasdok']) && !empty($advancedFilter['flag_jasdok'])) {
                $flag_jasdok = $advancedFilter['flag_jasdok'];
                if($flag_jasdok == 1) {
                    $status_jasa = "Lunas";
                } else if($flag_jasdok == 2) {
                    $status_jasa = "Belum Lunas";
                }
            }
        }

        $periodePulang = !empty ($startPulang) && !empty($endPulang) ? date('d M Y', strtotime($startPulang))." - ".date('d M Y', strtotime($endPulang)) : '-';
        $header = array(
            Yii::t("app", "Tanggal Transaksi") => ((date('d M Y',strtotime($start))." - ".date('d M Y', strtotime($end)))),
            Yii::t("app", "Tanggal Pulang") => $periodePulang,
            Yii::t("app", "Tanggal Bayar Jasa Dokter") => $tgl_flag,
            Yii::t("app", "Nama Dokter")      => !empty($nama_pegawai) ? $nama_pegawai : "-",
            Yii::t("app", "Status Billing")   => !empty($status_bayar) ? $status_bayar : "-",
            Yii::t("app", "Status Bayar Jasa Dokter")   => strtoupper($status_jasa),
            Yii::t("app", "Tindakan")         => strtoupper($daftartindakan_nama),
            Yii::t("app", "Nama Jasa")        => strtoupper($komponentarif_nama),
            Yii::t("app", "Ruangan")          => !empty($ruangan_nama) ? $ruangan_nama : "-",
            Yii::t("app", "Jenis Transaksi")  => strtoupper($jenis_transaksi),
            Yii::t("app", "Cara Bayar")       => !empty($carabayar_nama) ? $carabayar_nama : "-",
            Yii::t("app", "Penjamin")         => !empty($penjamin_nama) ? $penjamin_nama : "-",
            Yii::t("app", "Kelas Pelayanan")  => !empty($kelas_pelayanan) ? $kelas_pelayanan : "-",
            Yii::t("app", "Pelayanan")        => !empty($pelayanan) ? $pelayanan : "-",
        );
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
        
        $filePath = DocoHelpers::exportExcel('Laporan Rekapitulasi Jasa Dokter', $row, $header,[
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
            'service' => 'Sirs-ExportLaporanRekapJasaDokter',
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
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Transaksi',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Pulang',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tanggal Bayar Jasa Dokter',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Nama Dokter',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Status Billing',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Status Bayar Jasa Dokter',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'No Pendaftaran',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'No Rekam Medik',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Nama Pasien',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Ruangan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tindakan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Nama Jasa',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Tarif (Rp.)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Jasa (Rp.)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Bruto (Rp.)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'DPP (Rp.)',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Keterangan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Jenis Transaksi',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Cara Bayar',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Penjamin',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Kelas Pelayanan',
                        'rowspan' => 2
                    ],
                    [
                        'label'=>'Pelayanan',
                        'rowspan' => 2
                    ]
                ]
            ];
    }
}
