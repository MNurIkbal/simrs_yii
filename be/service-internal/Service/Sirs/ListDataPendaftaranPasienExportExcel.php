<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Components\DocoHelpers;

class ListDataPendaftaranPasienExportExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $data = $cacheFiles->get($this->unique_str .'-data');
        $filter = $cacheFiles->get($this->unique_str .'-filter');

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Ekstrak data hasil pencarian ...',
                'progress' => 20
            ]),
        ]);

        $path = 'uploads/'. $this->unique_str .'.xlsx';
        $profil = $this->getProfil();
        $header = $this->setUpHeader($data, $filter);
        $custHeader = $this->custHeader($data);
        $footer = [];

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Eksport data ke Excel ...',
                'progress' => 50
            ]),
        ]);

        $filePath = DocoHelpers::exportExcel('Laporan Data Pasien', $data, $header,[
            'skipIncrement' => false,
            // 'customHeader' => $custHeader,
            'customFormatCode' => [
                [
                    'selectColumn' => 'G',
                    'formatCode' => 'numberncs'
                ]
            ]
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
            'service' => 'Sirs-ListDataPendaftaranPasienExportExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    protected function setUpHeader($data, $filter)
    {
        $header = [];
        $advancedFilters = isset($filter['advanced-filter']) ? $filter['advanced-filter'] : [];
        if (isset($advancedFilters['no_rekam_medik'])) {
            $header['No Rekam Medik'] = $advancedFilters['no_rekam_medik'];
        }
        if (isset($advancedFilters['nama_pasien'])) {
            $header['Nama Pasien / No. Handphone'] = $advancedFilters['nama_pasien'];
        }
        if (isset($advancedFilters['alamat_pasien'])) {
            $header['Alamat Pasien'] = $advancedFilters['alamat_pasien'];
        }
        if (isset($advancedFilters['jenis_kelamin'])) {
            $header['Jenis Kelamin'] = $data[0]['jenis_kelamin'];
        }
        if (isset($advancedFilters['propinsi_nama'])) {
            $header['Provinsi'] = $data[0]['propinsi_nama'];
        }
        if (isset($advancedFilters['kabupaten_nama'])) {
            $header['Kabupaten'] = $data[0]['kabupaten_nama'];
        }
        if (isset($advancedFilters['kecamatan_nama'])) {
            $header['Kecamatan'] = $data[0]['kecamatan_nama'];
        }
        return $header;
    }

    protected function custHeader($data)
    {
        return [
            [
                [
                    'label'=>'No',
					'rowspan'=>2,
                ],
                [
                    'label'=>'No Rekam Medik',
					'rowspan'=>2,
                ],
                [
                    'label'=>'Tgl. Rekam Medik',
					'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Kelamin',
					'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal lahir',
					'rowspan'=>2,
                ],
                [
                    'label'=>'NIK',
					'rowspan'=>2,
                ],
                [
                    'label'=>'Alamat pasien',
					'rowspan'=>2,
                ],
                [
                    'label'=>'Propinsi nama',
					'rowspan'=>2,
                ],
                [
                    'label'=>'Kabupaten nama',
					'rowspan'=>2,
                ],
                [
                    'label'=>'Kecamatan nama',
					'rowspan'=>2,
                ],
                [
                    'label' => 'No Handphone',
                    'rowspan' => 2,
                ],
            ],
            []
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
