<?php

/**
 * ! @author : Budi (budi@sirs.co.id)
 * ? A product of PT. Docotel Teknologi
 * ? Powered by Sirs
 * updated at 05052022 by dede herdiana
 * --- code ini sudag tidak dipakai lagi ---
 */

namespace Doco\processes;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\models\kasir\InfoPasienRiView;
use Doco\models\kasir\CetakKwitansiBkm;
use Doco\models\kasir\InfoDataPendaftaran;
use Doco\models\kasir\JenisNonTunai;
use Doco\models\ProfilRsView;

class CetakKwitansiAdhyProcess extends \Doco\components\DocoBaseProcessExtension
{
	
	// const KWITANSI_LENGKAP = 1;
    // const KWITANSI_PASIEN = 2;
    // const KWITANSI_PENJAMIN = 3;
    // const KWITANSI_SUBPAYER = 4;

	/** Sementara prevent multipayer */
	const KWITANSI_LENGKAP = 1;
    const KWITANSI_PASIEN = 3;
    const KWITANSI_PENJAMIN = 2;
    const KWITANSI_SUBPAYER = 4;
    const KEY_PAYER_UTAMA = 'payer_utama';
    const KEY_SUBPAYER = 'sub_payer';
    const KEY_PENJAMIN = 'penjamin';

    protected $dokTercetak = 'kwitansi-adhy';
    protected $dokPath = '';
    protected $id;
    protected $invoice_id;
    protected $pendaftaran_id;
    protected $pdf_id;
    protected $jenis_kwitansi;
    protected $keterangan;
    protected $diterima_dari;
    protected $total_tunai;
    protected $total_nontunai;
    protected $total_tagihan;
    protected $biaya_administrasi;
    protected $total_bayar;
    protected $discount;
    protected $resep_id;
    protected $nama_pasien;
    protected $instalasi_nama;
    protected $dokter;
    protected $penjamin_nama;
    protected $no_pendaftaran;
    protected $no_rekam_medik;
    protected $no_kwitansi;
    protected $tgl_pembayaran;
    protected $kasir;
    protected $penjaminId;
    protected $dataBuktiMasuk = [];
    protected $no_pembayaran;
    protected $no_invoicepasien;
    protected $detailPenjamin;
    protected $total_kembalian;
    protected $total_dijamin;

    protected function populateData()
    {
        $request = $this->_requestData;
        $id = $request->get('id', null);
        $invoice_id = $request->get('pembayaran_id', null);
        $pdf_id = $request->get('pdf_id', null);
        $keterangan = $request->get('keterangan', null);
        $diterima_dari = $request->get('diterima_dari', null);
        $jenis_kwitansi = $request->get('jenis_kwitansi', null);
        $penjaminId = $request->get('penjamin_id', null);
        
        $this->id = $id;
        $this->invoice_id = $invoice_id;
        $this->pdf_id = $pdf_id;
        $this->keterangan = $keterangan;
        $this->diterima_dari = $diterima_dari;
        $this->penjaminId = $penjaminId;
        $detailPenjamin = $this->detailPenjamin();
        $this->detailPenjamin = $detailPenjamin;

        if(!empty($jenis_kwitansi)) {
            $jenis_kwitansi = explode(',', $jenis_kwitansi);
            if(in_array(self::KWITANSI_PENJAMIN, $jenis_kwitansi)) {
				if(empty($penjaminId) && isset($detailPenjamin[self::KEY_SUBPAYER])) {
					array_push($jenis_kwitansi, self::KWITANSI_SUBPAYER);
				}
            }
        }
        $this->jenis_kwitansi = $jenis_kwitansi;
        if (!$this->invoice_id) {
            throw new \yii\web\HttpException(500, "Pembayaran ID tidak ditemukan.");
        }
    }

    protected function getBuktiMasuk()
    {
        $header = $this->getInvoiceSudahBayar();
        $this->total_tunai = isset($header['total_tunai']) ? $header['total_tunai'] : 0;
        $this->total_nontunai = isset($header['total_nontunai']) ? $header['total_nontunai'] : 0;
        $this->total_kembalian = isset($header['total_kembalian']) ? $header['total_kembalian'] : 0;
        $this->dokter = isset($header['dokter']) ? $header['dokter'] : '-';
        $this->no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '';
        $this->pendaftaran_id = isset($header['pendaftaran_id']) ? $header['pendaftaran_id'] : null;
        $this->resep_id = isset($header['penjualanresep_id']) ? $header['penjualanresep_id'] : null;
        $this->nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '';
        $this->instalasi_nama = isset($header['instalasi_nama']) ? $header['instalasi_nama'] : '';
        $this->total_tagihan = isset($header['total_tagihan']) ? $header['total_tagihan'] : 0;
        $this->biaya_administrasi = isset($header['biaya_administrasi']) ? $header['biaya_administrasi'] : 0;
        $this->tgl_pembayaran = isset($header['tgl_pembayaran']) ? date('d-m-Y H:i:s', strtotime($header['tgl_pembayaran'])) : '-';
        $this->kasir = isset($header['kasir']) ? $header['kasir'] : '-';
        $this->no_pembayaran = isset($header['no_pembayaran']) ? $header['no_pembayaran'] : '-';
        $this->no_invoicepasien = isset($header['no_invoicepasien']) ? $header['no_invoicepasien'] : '-';
        $this->discount = isset($header['discount']) ? $header['discount'] : 0;
        $this->total_bayar = ($this->total_tagihan + $this->biaya_administrasi)  - $this->discount;
        $this->total_dijamin = isset($header['total_dijamin']) ? $header['total_dijamin'] : 0;
        $this->penggunaan_uangmuka = isset($header['penggunaan_uangmuka']) ? $header['penggunaan_uangmuka'] : 0;

    }

    protected function getInvoiceSudahBayar()
    {
        return Yii::$app->db->createCommand("
            SELECT 
            infopasiensudahbayar_v.*,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.penggunaan_uangmuka,
            (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) as discount,
            pegawai_m.nama_pegawai AS kasir
            FROM infopasiensudahbayar_v 
            JOIN pembayaran_t ON pembayaran_t.pembayaran_id = infopasiensudahbayar_v.pembayaran_id
            JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayaran_t.created_by
            LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
            WHERE infopasiensudahbayar_v.pembayaran_id = $this->invoice_id"
        )->queryOne();
    }

    protected function getHeader()
    {
        $penjamin_nama = $no_rekam_medik = '-';
        if($this->pendaftaran_id) {
            $pasienadmisi = InfoPasienRiView::find()->where(['pendaftaran_id' => $this->pendaftaran_id])->one();
            if($pasienadmisi) {
                $penjamin_nama = $pasienadmisi->penjamin_nama;
                $no_rekam_medik = !empty($pasienadmisi->no_rekam_medik) ? $pasienadmisi->no_rekam_medik : null;
            }
            else {
                $pendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $this->pendaftaran_id])->one();
                $penjamin_nama = $pendaftaran->penjamin_nama;
                $no_rekam_medik = !empty($pendaftaran->no_rekam_medik) ? $pendaftaran->no_rekam_medik : null;
            }
        } else if ($this->resep_id) {
            $pendaftaran = InfoDataPendaftaran::find()->where(['penjualanresep_id' => $this->resep_id])->one();
            $penjamin_nama = $pendaftaran->penjamin_nama;
        }

        $this->penjamin_nama = $penjamin_nama;
        $this->no_rekam_medik = $no_rekam_medik;
        if(!$this->keterangan) {
            $this->keterangan = 'PEMERIKSAAN  '. strtoupper($this->instalasi_nama). ' a/n : ' . strtoupper($this->nama_pasien) . ' RM : ' . $this->no_rekam_medik;
            if ($this->resep_id) {
                $this->keterangan = 'PEMBELIAN OBAT ALKES';
            }
        }
    }

    protected function getDataMultiPayer()
    {
        $detailPenjamin = $this->detailPenjamin;
        $key = empty($this->penjaminId) ? self::KEY_PAYER_UTAMA : self::KEY_PENJAMIN;
        $payerUtama = isset($detailPenjamin[$key]) ? $detailPenjamin[$key] : [];
        $namaPayerUtama = isset($payerUtama['penjamin_nama']) ? $payerUtama['penjamin_nama'] : '-';
        $AdminPayerUtama = isset($payerUtama['biaya_administrasi']) ? $payerUtama['biaya_administrasi'] : 0;
        $noKwitansiPayerUtama = isset($payerUtama['no_pembayaran']) ? $payerUtama['no_pembayaran'] : '-';
        $sub_payer = isset($detailPenjamin[self::KEY_SUBPAYER]) ? $detailPenjamin[self::KEY_SUBPAYER] : [];
        $dijaminPayerUtama = isset($payerUtama['tarif_dijamin']) ? $payerUtama['tarif_dijamin'] : 0;
        $namaSubPayer = isset($sub_payer['penjamin_nama']) ? $sub_payer['penjamin_nama'] : '-';
        $AdminSubPayer = isset($sub_payer['biaya_administrasi']) ? $sub_payer['biaya_administrasi'] : 0;
        $dijaminSubPayer = isset($sub_payer['tarif_dijamin']) ? $sub_payer['tarif_dijamin'] : 0;
        $noKwitansiSubPayer = isset($sub_payer['no_pembayaran']) ? $sub_payer['no_pembayaran'] : '-';

        $dijaminPayerUtama = $dijaminPayerUtama + $AdminPayerUtama;
        $dijaminSubPayer =  $dijaminSubPayer + $AdminSubPayer;
        return [
            'namaPayerUtama' => $namaPayerUtama,
            'noKwitansiPayerUtama' => $noKwitansiPayerUtama,
            'dijaminPayerUtama' => $dijaminPayerUtama,
            'namaSubPayer' => $namaSubPayer,
            'dijaminSubPayer' => $dijaminSubPayer,
            'noKwitansiSubPayer' => $noKwitansiSubPayer,
        ];
    }

    protected function detailNonTunai()
    {
        $listPembayaran = [];
        if ($this->total_tunai > 0) {
            $listPembayaran[] = 'TUNAI';
        }
        if (!empty($this->invoice_id) && !empty($this->total_nontunai)) {
            $metodePembayaran = Yii::$app->db->createCommand("
                SELECT jenisnontunai_id FROM pembayaranmetode_t 
                WHERE pembayaran_id = {$this->invoice_id} 
                AND jenisnontunai_id IS NOT NULL
            ")->queryAll();

            if (!empty($metodePembayaran)) {
                $listJenis = [];
                foreach ($metodePembayaran as $value) {
                    $listJenis[] = $value['jenisnontunai_id'];
                }

                $qJenisNonTunai = JenisNonTunai::find()
                    ->select(['nama'])
                    ->andWhere(['jenisnontunai_id' => $listJenis])
                    ->asArray()
                    ->all();

                foreach ($qJenisNonTunai as $value) {
                    $listPembayaran[] = strtoupper($value['nama']);
                }
            }
        }
        return $listPembayaran;
    }

    protected function detailPenjamin()
    {
        $whereCond = '';
        $penjaminId = $this->penjaminId;
        if(!empty($penjaminId)) {
            $whereCond = ' AND invoice.penjamin_pelayanan_id = '.$penjaminId.'';
        }
        $data = Yii::$app->db->createCommand("
            SELECT DISTINCT 
            invoice.penjamin_pelayanan_id AS penjamin_id, 
            invoice.penjamin_pelayanan AS penjamin_nama, 
            SUM(invoice.tarif_dijamin) AS tarif_dijamin,
            invoice.biaya_administrasi AS biaya_administrasi,
            CASE WHEN invoice.penjamin_tinpelayanan_id = invoice.penjamin_pelayanan_id THEN TRUE
                ELSE FALSE END AS payer_utama,pembayaranpelayanan_t.no_pembayaran
            FROM invoicesudahbayardetail_v invoice
            JOIN pembayaranpelayanan_t ON invoice.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
            WHERE invoice.pembayaran_id = $this->invoice_id {$whereCond}
            GROUP BY penjamin_pelayanan_id,invoice.biaya_administrasi,penjamin_pelayanan,penjamin_tinpelayanan_id,no_pembayaran
        ")->queryAll();

        $listPenjamin = [];
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                if(!empty($penjaminId)) {
                    $listPenjamin[self::KEY_PENJAMIN] = $value;
                }
                else {
                    if($value['payer_utama']) {
                        $listPenjamin[self::KEY_PAYER_UTAMA] = $value;
                    }
                    else {
                        $listPenjamin[self::KEY_SUBPAYER] = $value;
                    }
                }
            }
        }
        return $listPenjamin;
    }

    protected function setAttributes($data)
    {
        $listPembayaran = $this->detailNonTunai();
        $total_terbayar = 0;
        $nama_jenis_kwitansi = '';
        $txtListPembayaran = '';
        $diterima_dari = $this->diterima_dari;
        if (empty($diterima_dari)) {
            $diterima_dari = $this->nama_pasien;
        }
        $diterimaLabel = $diterima_dari;
        $multiPayer = $this->getDataMultiPayer();
        if($data == self::KWITANSI_LENGKAP) {
            $total_terbayar = $this->total_bayar;
            $no_kwitansi = $this->no_pembayaran;
        }
        elseif($data == self::KWITANSI_PASIEN) {
            $bayarPasien = ($this->total_tunai + $this->total_nontunai + $this->penggunaan_uangmuka) - $this->total_kembalian;
            $total_terbayar = $bayarPasien < 0 ? 0 : $bayarPasien;
            $nama_jenis_kwitansi = 'PASIEN';
            $txtListPembayaran = implode(", ", $listPembayaran);
            $no_kwitansi = $this->no_pembayaran;
        }
        elseif($data == self::KWITANSI_PENJAMIN) {
            $total_terbayar = isset($multiPayer['dijaminPayerUtama']) ? $multiPayer['dijaminPayerUtama'] : 0;
            $diterimaLabel = isset($multiPayer['namaPayerUtama']) ? $multiPayer['namaPayerUtama'] : '-';

            $nama_jenis_kwitansi = 'PENJAMIN';
            $txtListPembayaran = '';
            $no_kwitansi = isset($multiPayer['noKwitansiPayerUtama']) ? $multiPayer['noKwitansiPayerUtama'] : '-';
        }
        else {
            $total_terbayar = isset($multiPayer['dijaminSubPayer']) ? $multiPayer['dijaminSubPayer'] : '-';
            $diterimaLabel = isset($multiPayer['namaSubPayer']) ? $multiPayer['namaSubPayer'] : '-';
            $nama_jenis_kwitansi = 'PENJAMIN';
            $txtListPembayaran = '';
            $no_kwitansi = isset($multiPayer['noKwitansiSubPayer']) ? $multiPayer['noKwitansiSubPayer'] : '-';
        }

        $jumlah_diterima = DocoHelpers::formatNumber($total_terbayar);
        $terbilang = DocoHelpers::Terbilang($total_terbayar). ' Rupiah';
        if($total_terbayar == 0) {
            $terbilang = 'Nol Rupiah';
        }

		$profilRs = $this->getProfileRs();
		$kota = !empty($profilRs['kota']) ? $profilRs['kota'] : '';

        $attributes = [
            '#no_kwitansi#' 			=> $no_kwitansi,
            '#nama_pasien#' 			=> $this->nama_pasien,
            '#diterima_dari#' 			=> strtoupper($diterimaLabel),
            '#keterangan#' 				=> $this->keterangan,
            '#tgl_pembayaran#'			=> !empty($this->tgl_pembayaran) ? date('d-m-Y H:i:s', strtotime($this->tgl_pembayaran)) : '',
            '#jumlah_diterima#'			=> $jumlah_diterima,
            '#kasir#' 					=> isset($this->kasir) ? $this->kasir : '-',
            '#terbilang#' 				=> $terbilang,
            '#jenis_kwitansi#' 			=> $nama_jenis_kwitansi,
            '#no_reg#' 					=> $this->no_pendaftaran,
            '#no_mr#' 					=> $this->no_rekam_medik,
            '#list_jenis_pembayaran#' 	=> $txtListPembayaran,
            '#dokter#'					=> $this->dokter,
            '#tanggal_sekarang#' 		=> $kota.', '.date('d M Y', strtotime($this->tgl_pembayaran)),
			'#kota#'					=> $kota,
			'#tanggal_pembayaran#'		=> date('d M Y', strtotime($this->tgl_pembayaran)),
			'#tanggal_cetak#'			=> date('d M Y H:i:s'),
			'#instalasi_nama#'			=> $this->instalasi_nama,
        ];

        return $attributes;
    }

    protected function cetak()
    {
        $print = new DocoPrint($this->dokTercetak);
        if (!empty($this->jenis_kwitansi)) {
            $countData = count($this->jenis_kwitansi);
            foreach ($this->jenis_kwitansi as $key => $value) {
                $attributes = $this->setAttributes($value);
                $print->attributes = $attributes;
                $break = (($key + 1) == $countData) ? false : true;
                $print->generateHtml($break);
            }
        }
        $print->Output(true);
    }

	protected function getProfileRs()
	{
		$profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
				return ProfilRsView::find()->asArray()->one();
		});

		$kota   = '-';
		$namaRs = '-';
		if (!empty($profilRs['nama_rumahsakit'])) {
			$namaRs = $profilRs['nama_rumahsakit'];
		}

		if (!empty($profilRs['kota'])) {
			if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
					$pattern = "KOTA ADM. ";
			}
			elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
					$pattern = "KAB. ADM. ";
			}
			elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
					$pattern = "KAB. ";
			}
			elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
					$pattern = "KOTA ";
			}

			$kota = str_replace($pattern,"", $profilRs['kota']);
		}
		
		return [
			'namaRs' => $namaRs,
			'kota'   => $kota,
		];
	}

	protected function processFlow()
  	{
    	$this->populateData();
        $this->getBuktiMasuk();
        $this->getHeader();
        $this->cetak();
  	}
}