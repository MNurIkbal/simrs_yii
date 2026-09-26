<?php

namespace Extensions\pendaftaran;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\Services\Cache;

class PrintLabelPasienUcup extends \Doco\processes\PrintLabelPasienProcess
{

    protected function processFlow()
    {
        $this->populateData();
        $kunjungan = $this->getDataPasien();
        $this->setTemplete();
        $kunjungan['nama_pasien'] =isset($kunjungan['nama_pasien']) ? DocoHelpers::cutSentence($kunjungan['nama_pasien'], 20) : null; 
        if ($this->jenis == 'rajal') {
            $print = new DocoPrint();
            $print->attributes = [
                '#data#' => Yii::$app->controller->renderPartial($this->loc, [
                    'data' => $kunjungan,
                    'jumlah_data' => $this->jumlah,
                    'jenis' => $this->jenis,
                    'ktp' => $this->ktp,
                    self::JK => Cache::getLookupByType(self::JK)
                ])
            ];
        } else {

            $intJml = (int) $this->jumlah;
            $jml = $intJml % 2;
            if (empty($jml) && !empty($intJml)) {
                $intJml = $intJml / 2;
            } 

            if ($this->jenis == 'ranap') {
                $print = new DocoPrint('cetak-label-pasien-multiple');

                $namaDepan = '';
                if (!empty($kunjungan['nama_depan']) && is_string($kunjungan['nama_depan'])) {
                    $namaDepan = $kunjungan['nama_depan'];
                } else if (!empty($kunjungan['namadepan']) && is_string($kunjungan['namadepan'])) {
                    $namaDepan = $kunjungan['namadepan'];
                }

                for ($x = 1; $x <= $intJml; $x++) {
                    $print->attributes = [
                        '#nama_depan#' => strtoupper($namaDepan),
                        '#nama_pasien#' => isset($kunjungan['nama_pasien']) 
                            ? DocoHelpers::cutSentence(strtoupper($kunjungan['nama_pasien']),50) : '-' ,
                        '#no_rekam_medik#' => isset($kunjungan['no_rekam_medik']) ? $kunjungan['no_rekam_medik'] : '-',
                        '#tgl_lahir#' => !empty($kunjungan['tanggal_lahir']) ? date('d-m-Y', strtotime($kunjungan['tanggal_lahir'])) : '-' ,
                    ];

                    $print->generateHtml($x == $intJml ? false : true);
                }
            } else {
                $print = new DocoPrint('cetak-label-pasien-multiple-igd');

                $namaDepan = '';
                if (!empty($kunjungan['nama_depan']) && is_string($kunjungan['nama_depan'])) {
                    $namaDepan = $kunjungan['nama_depan'];
                } else if (!empty($kunjungan['namadepan']) && is_string($kunjungan['namadepan'])) {
                    $namaDepan = $kunjungan['namadepan'];
                }

                for ($x = 1; $x <= $intJml; $x++) {
                    $print->attributes = [
                        '#nama_depan#' => strtoupper($namaDepan),
                        '#nama_pasien#' => isset($kunjungan['nama_pasien']) 
                            ? DocoHelpers::cutSentence(strtoupper($kunjungan['nama_pasien']),50) : '-' ,
                        '#no_rekam_medik#' => isset($kunjungan['no_rekam_medik']) ? $kunjungan['no_rekam_medik'] : '-',
                        '#tgl_lahir#' => !empty($kunjungan['tanggal_lahir']) ? date('d-m-Y', strtotime($kunjungan['tanggal_lahir'])) : '-' ,
                    ];

                    $print->generateHtml($x == $intJml ? false : true);
                }
                // $this->loc = 'print_label_pasien_igd_sty';
                // $print = new DocoPrint();
                // $print->attributes = [
                //     '#data#' => Yii::$app->controller->renderPartial($this->loc, [
                //         'data' => $kunjungan,
                //         'jumlah_data' => $intJml,
                //         'jenis' => $this->jenis,
                //         'ktp' => $this->ktp,
                //         self::JK => Cache::getLookupByType(self::JK)
                //     ])
                // ];
            }
        }

        if ($this->html) {
            return $print->OutputHtml();
        } else {
            $print->Output(true);
        }
    }
}