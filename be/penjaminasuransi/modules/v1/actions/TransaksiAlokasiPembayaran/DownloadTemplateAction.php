<?php

namespace app\modules\v1\actions\TransaksiAlokasiPembayaran;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPengajuanKlaimDetail;

class DownloadTemplateAction extends BaseCurrentAction
{
    private function getDataExcel($pengajuanKlaimId)
    {
        $model = new InfoPengajuanKlaimDetail;
        $query = $model::find()->where([
            'pengajuanklaim_id' => $pengajuanKlaimId
        ])->orderBy([
            'no_pembayaran' => SORT_DESC
        ]);
        $datas = $query->asArray()->all();
        $details = null;
        $header = [];
        $counterNumber = 1;
        foreach ($datas as $key => $value) {
            if (empty($header)) {
                ArrayHelper::setValue($header, 'no_pengajuanklaim', $value['no_pengajuanklaim']);
            }
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
            $instalasi = ArrayHelper::getValue($value, 'instalasi_nama');
            $ruangan = ArrayHelper::getValue($value, 'ruangan_nama');
            $dataPasien = $namaPasien . "\n" . $noPendaftaran;
            $instalasiRuangan = $instalasi . "\n" . $ruangan;
            $newValue['no'] = $counterNumber;
            $newValue['data_pasien'] = $dataPasien;
            $newValue['no_invoice'] = $value['no_pembayaran'];
            $newValue['no_sep'] = $value['nosep'];
            $newValue['tanggal_masuk'] = !empty($value['tgl_pendaftaran']) ? date('d M Y', strtotime($value['tgl_pendaftaran'])) : '';
            $newValue['tanggal_keluar'] = !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '';
            $newValue['instalasi_ruangan'] = $instalasiRuangan;
            $newValue['total_tagihan'] = $value['total_tagihan'];
            $newValue['jumlah_telahbayar'] = $value['jumlah_telahbayar'];
            $newValue['jumlah_piutang'] = $value['jumlah_piutang'];
            $newValue['jumlah_bayar'] = $value['jumlah_bayar'];
            $newValue['label_bayar'] = '';
            $newValue['jumlah_sisapiutang'] = (int) $value['jumlah_piutang'] - $value['jumlah_bayar'];
            $details[] = $newValue;
            $counterNumber++;
        }
        return [
            'details' => $details,
            'header' => $header
        ];
    }

    protected function run()
    {
        $pengajuanKlaimId = Yii::$app->request->get('pengajuanklaim_id');
        if (!$pengajuanKlaimId) {
            return [
                'status' => 422,
                'messages' => 'pengajuanklaim_id must be set !'
            ];
        }
        $resultExcel = $this->getDataExcel($pengajuanKlaimId);
        $resultHeader = $resultExcel['header'];
        $resultDetails = $resultExcel['details'];
        $noPengajuanKlaim = ArrayHelper::getValue($resultHeader, 'no_pengajuanklaim');
        $header = [];
        $footer = [];
        $customFormatCode = [
            [
                'selectColumn' => 'B',
                'formatCode'   => 'general',
                'alignment'    => [
                    'wrapText' => true
                ]
            ],
            [
                'selectColumn' => 'G',
                'formatCode'   => 'general',
                'alignment'    => [
                    'wrapText' => true
                ]
            ],
            [
                'selectColumn' => 'L',
                'formatCode'   => 'general'
            ],
        ];
        $custHeader = [
            [
                [
                    'label' => 'No.',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Data Pasien',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No Invoice',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'No SEP',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Masuk',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Tanggal Keluar',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Instalasi Dan Ruangan',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Jumlah Pembayaran Dan Tagihan - Tagihan',
                    'colspan' => 6,
                ],
            ],
            [
                [
                    'label' => 'Tagihan (Rp.)',
                    'startfrom' => 8
                ],
                [
                    'label' => 'Jumlah Pasien Bayar (Rp.)',
                ],
                [
                    'label' => 'Piutang (Rp.)',
                ],
                [
                    'label' => 'Piutang (Telah Dibayarkan) (Rp.)',
                ],
                [
                    'label' => 'Jumlah Bayar (Rp.)',
                ],
                [
                    'label' => 'Sisa Tagihan (Rp.)',
                ],
            ]
        ];
        $header = [
            'No Pengajuan Klaim' => $noPengajuanKlaim,
        ];
        $filePath = DocoHelpers::exportExcel('ALOKASI PEMBAYARAN', $resultDetails, $header,  [
            'skipIncrement' => true,
            'skipHeader'    => true,
            'customFormatCode' => $customFormatCode,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save('php://output');
        die;
    }
}
