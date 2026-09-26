<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use Doco\models\kasir\InfoPasienSudahBayarView;
use Doco\models\kasir\InvoiceSudahBayarView;
use Doco\models\kasir\InvoiceSudahBayarDetailView;
use Doco\models\kasir\Pembayaran;
use Doco\models\kasir\PembayaranPenjamin;
use Doco\models\kasir\InfoPasienRiView;
use Doco\models\kasir\PasienView;
use Doco\models\ProfilRsView;
use Doco\models\Pendaftaran;
use Doco\models\KonfigSystem;
use Doco\models\kasir\CetakKwitansiBkm;
use Doco\models\kasir\InfoDataPendaftaran;
use Doco\models\kasir\JenisNonTunai;

class CetakKwitansiMhbgProcess extends \Doco\components\DocoBaseProcessExtension
{
	protected function cetak()
	{
		$request = $this->_requestData;
        $pdf_id = $this->_requestData->get('pdf_id', null);
        $id = $this->_requestData->get('id', null);
        $pembayaran_id = $this->_requestData->get('pembayaran_id', null);
        $jenis_kwitansi = $this->_requestData->get('jenis_kwitansi', null);
        if(!empty($jenis_kwitansi)) {
            $jenis_kwitansi = explode(',', $jenis_kwitansi);
        }

        $keterangan = $this->_requestData->get('keterangan', null);
        $diterima_dari = $this->_requestData->get('diterima_dari', null);

        if (!$pembayaran_id) {
        	throw new ValidationException(500, $this->_error, [
              'text' => "Pembayaran ID tidak ditemukan."
          	]);
        }

        $model = new CetakKwitansiBkm;
        $query = $model::find();
        $data = $query->where(['pembayaran_id' => $pembayaran_id]);
        
        if(!empty($pdf_id)) {
            $data->andWhere(['pendaftaran_id' => $pdf_id]);
        }

        $data = $data->one();
        $pendaftaran_id = !empty($data) ? $data->pendaftaran_id : null;
        $resep_id = !empty($data->penjualanresep_id) ? $data->penjualanresep_id : null;
        $total_tunai = !empty($data->total_tunai) ? $data->total_tunai : null;
        $total_nontunai = !empty($data->total_nontunai) ? $data->total_nontunai : null;
        $total_tagihan = !empty($data->total_tagihan) ? $data->total_tagihan : null;
        $dokter = !empty($data->dokter) ? $data->dokter : '-';
        
        //cek pasien RD rujuk ranap atau bukan
        $carabayar_id = null;
        $penjamin_id = null;
        $penjamin_nama = null;
        $no_pendaftaran = '-';
        $no_rekam_medik = '-';
        if($pendaftaran_id) {
            $pasienadmisi = InfoPasienRiView::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            if($pasienadmisi) {
                $penjamin_nama = $pasienadmisi->penjamin_nama;
                $no_rekam_medik = !empty($pasienadmisi->no_rekam_medik) ? $pasienadmisi->no_rekam_medik : null;
            } else {
                $pendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
                $penjamin_nama = $pendaftaran->penjamin_nama;
                $no_rekam_medik = !empty($pendaftaran->no_rekam_medik) ? $pendaftaran->no_rekam_medik : null;
            }
            $no_pendaftaran = isset($data->no_pendaftaran) ? $data->no_pendaftaran : '';
        } else if ($resep_id) {
            $pendaftaran = InfoDataPendaftaran::find()->where(['penjualanresep_id' => $resep_id])->one();
            $penjamin_nama = $pendaftaran->penjamin_nama;
        }
        
        $nama_pasien = isset($data->nama_pasien) ? $data->nama_pasien : '';
        $instalasi_nama = isset($data->instalasi_nama) ? $data->instalasi_nama : '';
        if(!$keterangan) {
            $keterangan = 'BIAYA PEMERIKSAAN DI '. strtoupper($instalasi_nama) . ' PASIEN ' . strtoupper($nama_pasien);
            if ($resep_id) {
                $keterangan = 'PEMBELIAN OBAT ALKES';
            }
        }

        if (!empty($jenis_kwitansi)) {
            $countData = count($jenis_kwitansi);
            $print = new DocoPrint('kwitansi-mhbg');
            $pembayaran = Pembayaran::findOne($pembayaran_id);
            $listPembayaran = [];
            if ($total_tunai > 0) {
                 $listPembayaran[] = 'TUNAI';
            }

            if (!empty($pembayaran_id) && !empty($total_nontunai)) {
                $metodePem = Yii::$app->db->createCommand("
                    SELECT jenisnontunai_id FROM pembayaranmetode_t 
                    WHERE pembayaran_id = {$pembayaran_id} 
                    AND jenisnontunai_id IS NOT NULL
                ")->queryAll();
                if (!empty($metodePem)) {
                    $listJenis = [];
                    foreach ($metodePem as $value) {
                        $listJenis[] = $value['jenisnontunai_id'];
                    }

                    $qJenisNonTunai = JenisNonTunai::find()->select([
                        'nama'
                    ])->andWhere([
                        'jenisnontunai_id' => $listJenis
                    ])->asArray()->all();

                    foreach ($qJenisNonTunai as $value) {
                        $listPembayaran[] = strtoupper($value['nama']);
                    }
                }
            }

            $total_terbayar = 0;
            $nama_jenis_kwitansi = '';
            if (empty($diterima_dari)) {
                $diterima_dari = $nama_pasien;
            }
            $txtListPem = '';
            foreach ($jenis_kwitansi as $key => $value) {
                $total_dijamin = ($pembayaran->total_dijamin > $total_tagihan) 
                        ? $total_tagihan : $pembayaran->total_dijamin;

                $diterimaLabel = $diterima_dari;
                if ($value == 1) {
                    $total_terbayar = $total_tagihan;
                } elseif($value == 2) {
                    $diterimaLabel = $penjamin_nama;
                    $total_terbayar = $total_dijamin;
                    $nama_jenis_kwitansi = 'PENJAMIN';
                } else {
                    $bayarPasien = $total_tunai + $total_nontunai;
                    $total_terbayar = $bayarPasien < 0 ? 0 : $bayarPasien;
                    $nama_jenis_kwitansi = 'PASIEN';
                    $txtListPem = implode(", ", $listPembayaran);
                }

                $jumlah_diterima = DocoHelpers::formatNumber($total_terbayar);
                $terbilang = DocoHelpers::Terbilang($total_terbayar). ' Rupiah';
                if($total_terbayar == 0) {
                    $terbilang = 'Nol Rupiah';
                }

                $print->attributes = [
                    '#no_kwitansi#'=>isset($data->no_kwitansi) ? $data->no_kwitansi : '',
                    '#nama_pasien#' => $nama_pasien,
                    '#diterima_dari#' => strtoupper($diterimaLabel),
                    '#keterangan#' => $keterangan,
                    '#tgl_pembayaran#'=> !empty($data->tgl_pembayaran) ? date('d-m-Y H:i:s', strtotime($data->tgl_pembayaran)) : '',
                    '#jumlah_diterima#'=> $jumlah_diterima,
                    '#kasir#' => isset($data->kasir) ? $data->kasir : '',
                    '#terbilang#' => $terbilang,
                    '#jenis_kwitansi#' => $nama_jenis_kwitansi,
                    '#no_reg#' => $no_pendaftaran,
                    '#no_mr#' => $no_rekam_medik,
                    '#list_jenis_pembayaran#' => $txtListPem,
                    '#dokter#' => $dokter,
                    '#tanggal_sekarang#' => date('d M Y', strtotime($data->tgl_pembayaran)),
                    '#datatable#' => Yii::$app->controller->renderPartial('kwitansi-mhbg', [
                        'no_mr' => $no_rekam_medik,
                        'no_reg' => $no_pendaftaran,
                        'no_kwt' => isset($data->no_kwitansi) ? $data->no_kwitansi : '',
                        'diterima_dari' => strtoupper($diterimaLabel),
                        'keterangan' => $keterangan,
                        'terbilang' => $terbilang,
                        'jumlah_diterima'=> $jumlah_diterima,
                        'kasir' => isset($data->kasir) ? $data->kasir : '',
                    ]),
                ];
                $break = (($key + 1) == $countData) ? false : true;
                $print->generateHtml($break);
            }
        }
        
        $print->Output(true);
	}

	protected function processFlow()
  	{
    	$this->cetak();
  	}
}