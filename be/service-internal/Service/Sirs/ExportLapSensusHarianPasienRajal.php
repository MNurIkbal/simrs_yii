<?php

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Lookup;

class ExportLapSensusHarianPasienRajal extends \Integrasi\Contracts\DocoImplement
{
    const JK = 'jenis_kelamin';
    const ADOA = 'adoa';
    const ADOAD = 'adoad';
    const ADOAPP = 'adoapp';
    const TGL_PENDAFTARAN = 'tgl_pendaftaran';
    const JENIS_PENDAFTARAN = 'jenis_pendaftaran';
    const IS_HEADER = 'is_header';

    public function execute()
    {
        ini_set('memory_limit', '-1');
		set_time_limit(0);
        
        $row = [];
        $footer = [];

        $cacheFiles = Yii::$app->cacheFiles;
        $data = $cacheFiles->get($this->unique_str .'-data');
        $dataHeader = $cacheFiles->get($this->unique_str .'-header');

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Ekstrak data hasil pencarian ...',
                'progress' => 20
            ]),
        ]);

        foreach($data['data'] as $idx => $columns) {
            $cols = []; $ctr = 0;
            foreach($dataHeader['header'] as $key => $label) {
                if (in_array($label, [self::TGL_PENDAFTARAN, self::JENIS_PENDAFTARAN, self::IS_HEADER])) {
                    continue;
                } else {
                    $cols[$ctr+1] = $columns[$label];
                    $ctr++;
                }
            }
            $row[] = $cols;
        }

        $profil = $this->getProfil();
        $header = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
        ];
        $custHeader = $this->custHeader($data, $dataHeader);
        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Eksport data ke Excel ...',
                'progress' => 40
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel('Laporan Sensus Harian Pasien Rawat Jalan', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save($path);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Menyiapkan file untuk didownload ...',
                'progress' => 80
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-ExportLapSensusHarianPasienRajal',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function custHeader($dataHeader)
    {
        $jk = $this->getLookupByType(self::JK)->all();
        $countCarabayar = count($dataHeader['carabayar']);
        $colspanCounter = $countCarabayar * 2;
        $coljumlahpasien = ($colspanCounter * 2) + 2;
        $staticFirstRow = [
            [
                'label' => 'NO',
                'rowspan' => 4
            ],
            [
                'label' => 'DEPARTMENT',
                'rowspan' => 4
            ],
            [
                'label' => 'JUMLAH PASIEN',
                'colspan' => $coljumlahpasien
            ],
            [
                'label' => 'JUMLAH',
                'colspan' => 2
            ],
            [
                'label' => strtoupper(self::ADOA),
                'rowspan' => 4
            ],
            [
                'label' => strtoupper(self::ADOAD),
                'rowspan' => 4
            ],
            [
                'label' => strtoupper(self::ADOAPP),
                'rowspan' => 4
            ],
        ];

        $staticSecondRow = [
            [
                'label' => 'BARU',
                'colspan' => $colspanCounter,
                'startfrom' => 3
            ],
            [
                'label' => 'JUMLAH BARU',
                'rowspan' => 3,
            ],
            [
                'label' => 'LAMA',
                'colspan' => $colspanCounter,
            ],
            [
                'label' => 'JUMLAH LAMA',
                'rowspan' => 3,
            ],
            [
                'label' => 'KUNJUNGAN',
                'rowspan' => 3,
            ],
            [
                'label' => 'HP',
                'rowspan' => 3,
            ],
        ];

        foreach($dataHeader['carabayar'] as $k => $v) {
            $tmpCabarBaru[] = [
                'label' => $k,
                'colspan' => 2,
            ];
            $tmpCabarLama[] = [
                'label' => $k,
                'colspan' => 2,
            ];
        }
        $tmpCabarBaru[0]['startfrom'] = 3;
        $tmpCabarLama[0]['startfrom'] = 2;
        $listCaraBayar = array_merge($tmpCabarBaru, $tmpCabarLama);

        $listJk = [];
        for ($i=0; $i < $colspanCounter; $i++) { 
            $jkCodeL = $jk[0]['lookup_kode'];
            $jkCodeP = $jk[1]['lookup_kode'];
            $tmpJkL = [
                'label' => $jkCodeL,
            ];
            $tmpJkP = [
                'label' => $jkCodeP,
            ];

            if($i == 0) {
                $tmpJkL['startfrom'] = 3;
            }

            if($i == $countCarabayar) {
                $tmpJkL['startfrom'] = 2;
            }
            array_push($listJk, $tmpJkL);
            array_push($listJk, $tmpJkP);
        }

        return [
            $staticFirstRow,
            $staticSecondRow,
            $listCaraBayar,
            $listJk
        ];
    }

    public function getLookupByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->andWhere(['lookup_type' => $type]);
            $result->andWhere(['is_active' => TRUE]);
        }
        $result->orderBy(['lookup_urutan' => SORT_ASC]);
        return $result;
    }

    private function getProfil()
    {
        $profil = Yii::$app->db->createCommand("
            SELECT nokode_rumahsakit, nama_rumahsakit FROM profilrumahsakit_m
        ")->queryOne();

        return $profil;
    }
}
