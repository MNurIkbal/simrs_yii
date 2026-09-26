<?php

namespace app\modules\v1\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\PengajuanKlaimView;

class CetakPengajuanAction extends BaseCurrentAction
{
    private function getPengajuanKlaim($pengajuanKlaimId)
    {
        $pengajuanKlaimData = PengajuanKlaim::find()
            ->findByPengajuanKlaimId($pengajuanKlaimId)
            ->asArray()
            ->one();
        $headerNoPengajuan = ArrayHelper::getValue($pengajuanKlaimData, 'no_pengajuanklaim');
        $headerCatatan = ArrayHelper::getValue($pengajuanKlaimData, 'catatan');
        $headerTglPengajuan = ArrayHelper::getValue($pengajuanKlaimData, 'tgl_pengajuanklaim');
        $headerTglPengajuan = date('d M Y H:i:s', strtotime($headerTglPengajuan));
        $headerTglJatuhTempo = ArrayHelper::getValue($pengajuanKlaimData, 'tgl_jatuhtempo');
        $headerTglJatuhTempo = date('d M Y', strtotime($headerTglJatuhTempo));
        $headerTotalPengajuan = 0;
        $pengajuanKlaimViewDatas = PengajuanKlaimView::find()
            ->findByPengajuanKlaimId($pengajuanKlaimId)
            ->asArray()
            ->all();
        foreach ($pengajuanKlaimViewDatas as $key => $value) {
            $tanggalMasuk = ArrayHelper::getValue($value, 'tgl_pendaftaran');
            $tanggalKeluar = ArrayHelper::getValue($value, 'tglpasienpulang');
            $totalTagihan = ArrayHelper::getValue($value, 'total_tagihan');
            $totalSudahBayar = ArrayHelper::getValue($value, 'total_sdh_bayar');
            $totalSisaTagihan = ArrayHelper::getValue($value, 'total_sisa_tagihan');
            $totalAsuransi = ArrayHelper::getValue($value, 'total_asuransi');
            $totalDiscount = ArrayHelper::getValue($value, 'total_discountpembayaran');
            $textTotalTagihan = "Rp." . DocoHelpers::formatNumber($totalTagihan, '0');
            $textTotalSudahBayar = "Rp." . DocoHelpers::formatNumber($totalSudahBayar, '0');
            $textTotalSisaTagihan = "Rp." . DocoHelpers::formatNumber($totalSisaTagihan, '0');
            $textTotalAsuransi = "Rp." . DocoHelpers::formatNumber($totalAsuransi, '0');
            $textTotalDiscount = "Rp." . DocoHelpers::formatNumber($totalDiscount, '0');
            $tanggalMasuk = date('d M Y', strtotime($tanggalMasuk));
            $tanggalKeluar = date('d M Y', strtotime($tanggalKeluar));
            $headerTotalPengajuan += $totalAsuransi;
            ArrayHelper::setValue($pengajuanKlaimViewDatas, "$key.text_total_tagihan", $textTotalTagihan);
            ArrayHelper::setValue($pengajuanKlaimViewDatas, "$key.text_total_sdh_bayar", $textTotalSudahBayar);
            ArrayHelper::setValue($pengajuanKlaimViewDatas, "$key.text_total_sisa_tagihan", $textTotalSisaTagihan);
            ArrayHelper::setValue($pengajuanKlaimViewDatas, "$key.text_total_asuransi", $textTotalAsuransi);
            ArrayHelper::setValue($pengajuanKlaimViewDatas, "$key.text_total_discountpembayaran", $textTotalDiscount);
            ArrayHelper::setValue($pengajuanKlaimViewDatas, "$key.tgl_pendaftaran", $tanggalMasuk);
            ArrayHelper::setValue($pengajuanKlaimViewDatas, "$key.tglpasienpulang", $tanggalKeluar);
        }
        $headerTotalPengajuan = "Rp.". DocoHelpers::formatNumber($headerTotalPengajuan, 0);
        $headerCaraBayar = ArrayHelper::getValue($pengajuanKlaimViewDatas, '0.carabayar_nama');
        $headerPenjamin = ArrayHelper::getValue($pengajuanKlaimViewDatas, '0.penjamin_nama');
        $headerTanggalPelayananDari = ArrayHelper::getValue($pengajuanKlaimData, 'tgl_keluardari');
        $headerTanggalPelayananSampai = ArrayHelper::getValue($pengajuanKlaimData, 'tgl_keluarsampai');
        $headerTanggalKeluarAwal = date('d M Y', strtotime($headerTanggalPelayananDari));
        $headerTanggalKeluarSampai = date('d M Y', strtotime($headerTanggalPelayananSampai));
        $tanggalKeluarRange = "$headerTanggalKeluarAwal s/d $headerTanggalKeluarSampai";
        $header = [
            'tanggal_keluar_range' => $tanggalKeluarRange,
            'no_pengajuan' => $headerNoPengajuan,
            'total_pengajuan' => $headerTotalPengajuan,
            'cara_bayar' => $headerCaraBayar,
            'catatan' => $headerCatatan,
            'tanggal_pengajuan' => $headerTglPengajuan,
            'tanggal_jatuh_tempo' => $headerTglJatuhTempo,
            'penjamin' => $headerPenjamin,
        ];
        return [
            'header' => $header,
            'details' => $pengajuanKlaimViewDatas
        ];
    }

    private function getPegawaiKabag()
    {
        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai = PegawaiView::find()
            ->findByJabatanId($lookupTransaksi->kode_id)
            ->findLastUpdated()
            ->orderByLastUpdated()
            ->one();
        return $pegawai;
    }

    public function run()
    {
        $request = Yii::$app->request;
        $pengajuanKlaimId = $request->get('pengajuanklaim_id');
        $pegawaiData = $this->getPegawaiKabag();
        $pengajuanKlaimData = $this->getPengajuanKlaim($pengajuanKlaimId);
        $headerData = ArrayHelper::getValue($pengajuanKlaimData, 'header');
        $contentDatas = ArrayHelper::getValue($pengajuanKlaimData, 'details');
        $namaPegawai = ArrayHelper::getValue($pegawaiData, 'nama_pegawai');
        $namaJabatan = ArrayHelper::getValue($pegawaiData, 'jabatan_nama');
        $nipPegawai = ArrayHelper::getValue($pegawaiData, 'nomorindukpegawai');
        $print = new DocoPrint();
        $print->shrink_tables_to_fit  = 1;
        $print->attributes = [
            '#tanggal_keluar_range#' => ArrayHelper::getValue($headerData, 'tanggal_keluar_range'),
            '#no_pengajuan#' => ArrayHelper::getValue($headerData, 'no_pengajuan'),
            '#total_pengajuan#' => ArrayHelper::getValue($headerData, 'total_pengajuan'),
            '#cara_bayar#' => ArrayHelper::getValue($headerData, 'cara_bayar'),
            '#catatan#' => ArrayHelper::getValue($headerData, 'catatan'),
            '#tanggal_pengajuan#' => ArrayHelper::getValue($headerData, 'tanggal_pengajuan'),
            '#tanggal_jatuh_tempo#' => ArrayHelper::getValue($headerData, 'tanggal_jatuh_tempo'),
            '#penjamin#' => ArrayHelper::getValue($headerData, 'penjamin'),
            '#datatable#' => $this->controller->renderPartial('cetak-pengajuan', [
                'datas' => $contentDatas,
            ]),
            '#tanggal#'   => date('d M Y H:i:s'),
            '#jabatan#'   => $namaJabatan,
            '#pegawai#'   => $namaPegawai,
            '#nip#'       => $nipPegawai
        ];
        $print->Output();
    }
}
