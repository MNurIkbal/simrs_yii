<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\models\Pegawai;

class CetakKwitansiKramat extends \Doco\processes\CetakKwitansiProcess
{
    // protected $dokTercetak = 'kwitansi-adhy';
    protected function setAttributes($data)
    {
        $listPembayaran = $this->detailNonTunai();
        $totalTerbayar = 0;
        $namaJenisKwitansi = '';
        $txtListPembayaran = '';
        $diterimaDari = $this->diterima_dari;
        if (empty($diterimaDari)) {
            $diterimaDari = $this->nama_pasien;
        }
        $diterimaLabel = $diterimaDari;
        $multiPayer = $this->getDataMultiPayer();
        $diskonDokter = $this->getDataDiskon();
        if($data == self::KWITANSI_LENGKAP) {
            $noKwitansi = $this->no_pembayaran;
            $dijaminPayerUtama = ArrayHelper::getValue($multiPayer, 'dijaminPayerUtama', 0);
            $dijaminSubPayer = ArrayHelper::getValue($multiPayer, 'dijaminSubPayer', 0);
            $bayarPasien = ($this->total_tunai + $this->total_nontunai) - $this->total_kembalian;
            $totalTerbayar = $dijaminPayerUtama + $dijaminSubPayer + $bayarPasien - $this->total_discount + $diskonDokter;
            if($totalTerbayar == 0) {
                $totalTerbayar = $this->total_dibayar_pasien;
            }
            else {
                $totalTerbayar += ($this->total_dibayar_pasien < $this->sisa_uangmuka) ? $this->total_dibayar_pasien : $this->sisa_uangmuka;
            }
        }
        elseif($data == self::KWITANSI_PASIEN) {
            $bayarPasien = ($this->total_tunai + $this->total_nontunai) - $this->total_kembalian;
            $dijaminPayerUtama = ArrayHelper::getValue($multiPayer, 'dijaminPayerUtama', 0);
            $totalDiscount = $dijaminPayerUtama > 0 ? 0 : $this->total_discount;
            $totalTerbayar = $bayarPasien < 0 ? 0 : $bayarPasien - $totalDiscount + $diskonDokter;
            if($totalTerbayar == 0) {
                $totalTerbayar = $this->total_dibayar_pasien;
            }
            else {
                $totalTerbayar += ($this->total_dibayar_pasien < $this->sisa_uangmuka) ? $this->total_dibayar_pasien : $this->sisa_uangmuka;
            }
            $namaJenisKwitansi = 'PASIEN';
            $txtListPembayaran = implode(", ", $listPembayaran);
            $noKwitansi = $this->no_invoicepasien;
        }
        elseif($data == self::KWITANSI_PENJAMIN) {
            $totalTerbayar = ArrayHelper::getValue($multiPayer, 'dijaminPayerUtama', 0);
            $totalTerbayar = $totalTerbayar > 0 ? $totalTerbayar - $this->total_discount : 0;
            $diterimaLabel = ArrayHelper::getValue($multiPayer, 'namaPayerUtama', '-');
            $noKwitansi = ArrayHelper::getValue($multiPayer, 'noKwitansiPayerUtama', '-');
            $payerUtama = ArrayHelper::getValue($multiPayer, 'payerUtama', []);
            $groupCaraBayarId = ArrayHelper::getValue($payerUtama, 'groupcarabayar_id');
            if($groupCaraBayarId == DocoConstants::GROUP_UMUM) {
                $totalTerbayar = ArrayHelper::getValue($payerUtama, 'tarif_dijamin', 0);
            }
            $namaJenisKwitansi = 'PENJAMIN';
            $txtListPembayaran = '';
        }
        else {
            $totalTerbayar = ArrayHelper::getValue($multiPayer, 'dijaminSubPayer', 0);
            $diterimaLabel = ArrayHelper::getValue($multiPayer, 'namaSubPayer', '-');
            $noKwitansi = ArrayHelper::getValue($multiPayer, 'noKwitansiSubPayer', '-');
            $namaJenisKwitansi = 'PENJAMIN';
            $txtListPembayaran = '';
        }
        
        $jumlahDiterima = DocoHelpers::formatNumber(ceil($totalTerbayar));
        $terbilang = DocoHelpers::Terbilang(ceil($totalTerbayar)). ' Rupiah';
        if($totalTerbayar == 0) {
            $terbilang = 'Nol Rupiah';
        }
        $helper = new DocoHelpers;
        $profileRs = $this->getProfileRs();
        $kota = isset($profileRs['kota']) ? $profileRs['kota'] : '';
        $tgl_pembayaran = !empty($this->tgl_pembayaran) ? date('d-m-Y H:i:s', strtotime($this->tgl_pembayaran)) : '';
        $tgl_pembayaran2 = !empty($this->tgl_pembayaran) ? $helper->convertDate($this->tgl_pembayaran) : '';
        $userId = Yii::$app->jwt->user->pegawai_id;
        $pegawai = Pegawai::findOne($userId);
        $namaKasir = isset($this->kasir) ? $this->kasir : '-';
        $namaPegawai = ArrayHelper::getValue($pegawai, 'nama_pegawai');
        $noPendaftaran = $this->no_pendaftaran;
        $noRekamMedik = $this->no_rekam_medik;
        $keterangan = $this->keterangan;
        $attributes = [
            '#no_kwitansi#' => $noKwitansi,
            '#nama_pasien#' => $this->nama_pasien,
            '#diterima_dari#' => strtoupper($diterimaLabel),
            '#keterangan#' => $keterangan,
            '#tgl_pembayaran#'=> $tgl_pembayaran,
            '#tgl_pembayaran2#'=> $tgl_pembayaran2,
            '#jumlah_diterima#'=> $jumlahDiterima,
            '#tgl_pembayaran#'=> !empty($this->tgl_pembayaran) ? date('d-m-Y H:i:s', strtotime($this->tgl_pembayaran)) : '',
            '#jumlah_diterima#'=> $jumlahDiterima,
            '#kasir#' => $namaKasir,
            '#terbilang#' => $terbilang,
            '#jenis_kwitansi#' => $namaJenisKwitansi,
            '#no_reg#' => $noPendaftaran,
            '#no_mr#' => $noRekamMedik,
            '#list_jenis_pembayaran#' => $txtListPembayaran,
            '#dokter#' => $this->dokter,
            '#tanggal_sekarang#' => date('d M Y', strtotime($this->tgl_pembayaran)),
            '#alamatRs#' => $kota,
            '#printed_by#' => $namaPegawai,
			'#tanggal_cetak#' => date('d M Y H:i:s'),
        ];

        if(!empty($this->dokPath)) {
            $attributes = array_merge($attributes, [
                '#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
                    'no_mr' => $noRekamMedik,
                    'no_reg' => $noPendaftaran,
                    'no_kwt' => $noKwitansi,
                    'diterima_dari' => strtoupper($diterimaLabel),
                    'keterangan' => $keterangan,
                    'terbilang' => $terbilang,
                    'jumlah_diterima'=> $jumlahDiterima,
                    'kasir' => $namaKasir,
                ]),
            ]);
        }
        return $attributes;
    }

	protected function processFlow()
    {
      $this->populateData();
      $this->getBuktiMasuk();
      $this->getHeader();
      $this->cetak();
    }
}