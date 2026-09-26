<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\MorbiditasRajalHeaderView;

class ExportMorbiditasRajal extends \Integrasi\Contracts\DocoImplement
{
    const _LAKI = '_LAKI';
    const _PEREMPUAN = '_PEREMPUAN';

	public function execute()
    {
        ini_set('memory_limit', '-1');
		set_time_limit(0);
        
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Pasien Morbiditas Rawat Jalan',
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
        // if(isset($filter['advanced-filter'])) {
        //     if(isset($filter['advanced-filter']['tglmasukpenunjang'])) {
        //         $explode = explode(" - ", $filter['advanced-filter']['tglmasukpenunjang']);
        //         if(count($explode) == 2) {
        //             $start = date('Y-m-d', strtotime($explode[0]));
        //             $end = date('Y-m-d', strtotime($explode[1]));
        //         }
        //         unset($filter['advanced-filter']['tglmasukpenunjang']);
        //     }
        // }

        // $header = [
        //     'Tanggal Pendaftaran' => $start.' Sampai Dengan '.$end,
        // ];
        $bulan = ArrayHelper::getValue($filter,'bulan');
        $tahun = ArrayHelper::getValue($filter,'tahun');
        $textBulan = $bulan && isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : '';
        $profil = $this->getProfil();
        $header = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
            'Bulan' => $textBulan,
            'Tahun' => $tahun,
            'Jumlah Diagnosa' => count($row),
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
        
        $filePath = DocoHelpers::exportExcel('Laporan RL4.b Morbiditas Rawat Jalan', $row, $header,[
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
            'service' => 'Sirs-ExportMorbiditasRajal',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader() 
    {
        $dataDetailDiagnosa = $dataDetailJenisKelamin = $listUmur = $tmp = $tmpDataHeader = $listGolongan = $data = $header = $columns= [];
        $staticHeaderAwal = [
            'no' => 'no',
            'no_dtd' => 'no_dtd',
            'note_dtd' => 'note_dtd',
            'diagnosa_nama' => 'diagnosa_nama',
        ];
        $staticHeaderAkhir = [
            'lk' => 'lk',
            'pr' => 'pr',
            'jml_kasus_baru' => 'jml_kasus_baru',
            'jml_kunjungan' => 'jml_kunjungan',
            'diagnosa_id' => 'diagnosa_id',
        ];

        $modelheader = new MorbiditasRajalHeaderView;
        $queryheader = $modelheader::find();
        $header = $queryheader->where(['kolom' => 'vertikal'])
        ->orderBy([
            'dtd_noterperinci' => SORT_ASC,
            'diagnosa_kode' => SORT_ASC,
        ])->all();

        $qryUmur = "
            SELECT 
                golonganumur_id,
                golonganumur_nama,
                golonganumur_namalainnya
            FROM rl4_a_morbiditasrawatinapexcel_v
            WHERE kolom='horizontal'
            ORDER BY golonganumur_id
        ";
        $listUmur = Yii::$app->db->createCommand("{$qryUmur} ")->queryAll();

        foreach($header as $k => $v) {
            $diagId = $v['diagnosa_id'];
            $dtdNo = $v['no_dtd'];
            $dtdNote = $v['dtd_noterperinci'];
            $diagNama = $v['diagnosa_nama'];

            foreach($listUmur as $w => $x) {
                $golUmurId = $x['golonganumur_id'];
                $golUmurNama = str_replace(' ', '_', $x['golonganumur_namalainnya']);

                if(!isset($listGolongan[$golUmurNama.self::_LAKI])){
                    $listGolongan[$golUmurNama.self::_LAKI] = null;
                } 

                if(!isset($listGolongan[$golUmurNama.self::_PEREMPUAN])){
                    $listGolongan[$golUmurNama.self::_PEREMPUAN] = null;
                }

                if(!isset($tmpDataHeader[$x['golonganumur_namalainnya']])){
                    $tmpDataHeader[$x['golonganumur_namalainnya']] =  $x['golonganumur_namalainnya'];
                }

            }
        }

        $header = array_merge($staticHeaderAwal, $listGolongan);
        $header = array_merge($header, $staticHeaderAkhir);

        foreach($header as $k => $v) {
            $visible = true;
            $search = false;
            $title = '';
            $nameLaki = substr($k, -5);
            $nameCewe = substr($k, -10);

            switch ($k) {
                case 'no_dtd':
                    $title = 'No. DTD';
                    break;
                case 'note_dtd':
                    $title = 'No. Daftar terperinci';
                    break;
                case 'diagnosa_nama':
                    $title = 'Golongan sebab penyakit';
                    break;
                case 'lk':
                    $title = 'LK';
                    break;
                case 'pr':
                    $title = 'PR';
                    break;
                case 'jml_kasus_baru':
                    $title = 'Jumlah Pasien Keluar Hidup (23 + 24)';
                    break;
                case 'jml_kunjungan':
                    $title = 'Jumlah Pasien Keluar mati';
                    break;
                default:
                    $title =  $k;
                    break;
            }

            if($nameLaki == self::_LAKI) {
                $title = 'L';
            }

            if($nameCewe == self::_PEREMPUAN) {
                $title = 'P';
            }

            $columns[] =[
                'title' => ucfirst($title),
                'data' => $k,
                'searchable' => $search,
                'orderable' => false,
                'visible' => $visible,
            ];
        }

        $dataColumns = $columns;
        $data_header = $tmpDataHeader;
        $countGol = count($data_header);
        //
        $staticFirstRow = [
            [
                'label' => 'No',
                'rowspan' => 3
            ],
            [
                'label' => 'No. DTD',
                'rowspan' => 3
            ],
            [
                'label' => 'No. Daftar terperinci',
                'rowspan' => 3
            ],
            [
                'label' => 'Golongan sebab penyakit',
                'rowspan' => 3
            ],
            [
                'label' => 'Jumlah Pasien Hidup dan Mati menurut Golongan Umur & Jenis Kelamin',
                'colspan' => ($countGol * 2)
            ],
            [
                'label' => 'Pasien Keluar (Hidup & Mati) Menurut Jenis Kelamin',
                'colspan' => 2
            ],
            [
                'label' => 'Jumlah Kasus Baru (23 + 24)',
                'rowspan' => 3
            ],
            [
                'label' => 'Jumlah Kunjungan',
                'rowspan' => 3
            ],
        ];

        $staticSecondRow = [
            [
                'label' => 'LK',
                'rowspan' => 2
            ],[
                'label' => 'PR',
                'rowspan' => 2
            ],
        ];
        $loopCheckHeader = 0;
        foreach($data_header as $v) {
            if($loopCheckHeader == 0) {
                $listGol[] = [
                    'label' => $v,
                    'startfrom' => 5,
                    'colspan' => 2
                ];
            } else {
                $listGol[] = [
                    'label' => $v,
                    'colspan' => 2
                ];
            }
            $loopCheckHeader++;
        }

        $loopCheckGol = 0;
        foreach($dataColumns as $k => $v) {
            if ($k >= 4 && $k < (($countGol * 2) + 4) ) {
                if($loopCheckGol == 0) {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'startfrom' => 5,
                        'data' => $v['data']
                    ];
                } else {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'data' => $v['data']
                    ];
                }
                $loopCheckGol++;
            }
        }
        $listGol = array_merge($listGol, $staticSecondRow);
        return [
            $staticFirstRow,
            $listGol,
            $jkGolUmur
        ];
        return [
            [
                [
                    'label'=>'No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Pendaftaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Pendaftaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Pasien',
                    'rowspan'=>2,
                ]
            ]
        ];
    }

    private function getProfil()
    {
        $profil = Yii::$app->db->createCommand("
            SELECT nokode_rumahsakit, nama_rumahsakit FROM profilrumahsakit_m
        ")->queryOne();

        return $profil;
    }
}
