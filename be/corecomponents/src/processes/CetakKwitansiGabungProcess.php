<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\models\kasir\InfoPasienRiView;
use Doco\models\kasir\InfoDataPendaftaran;
use Doco\models\kasir\JenisNonTunai;
use app\modules\v1\cache\Cache;

class CetakKwitansiGabungProcess extends \Doco\components\DocoBaseProcessExtension
{
	const KWITANSI_LENGKAP = 1;
    const KWITANSI_PASIEN = 2;
    const KWITANSI_PENJAMIN = 3;
    const KWITANSI_SUBPAYER = 4;
    const KEY_PAYER_UTAMA = 'payer_utama';
    const KEY_SUBPAYER = 'sub_payer';
    const KEY_PENJAMIN = 'penjamin';

    protected $dokTercetak = 'kwitansi-gabung';
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
    protected $instalasi_nama_ri;
    protected $headerGabung;
    protected $dataPayer;
    protected $total_dijamin;
    protected $listNewPayerId;
    protected $listPayer;
    protected $arrPayer = [];
    protected $cara_bayar_umum = DocoConstants::GROUP_UMUM;

    protected function populateData()
    {
        $request = $this->_requestData;
        $id = $request->get('id', null);
        $pdf_id = $request->get('pdf_id', null);
        $keterangan = $request->get('keterangan', null);
        $diterima_dari = $request->get('diterima_dari', null);
        $jenis_kwitansi = $request->get('jenis_kwitansi', 1);
        $penjaminId = $request->get('penjamin_id', null);
        
        $this->id = $id;
        $get_pembayaran = $this->getPembayaran();
        $this->invoice_id = !empty($get_pembayaran['pembayaranId']) ? $get_pembayaran['pembayaranId'] : null;
        $this->arrPayer = !empty($get_pembayaran['arrPayer']) ? $get_pembayaran['arrPayer'] : []; //inital penjamin
        $this->listNewPayerId = !empty($get_pembayaran['listNewPayerId']) ? $get_pembayaran['listNewPayerId'] : []; //inital penjamin
        
        $this->getBuktiMasuk();
        $this->pdf_id = $pdf_id;
        $this->keterangan = $keterangan;
        $this->diterima_dari = $diterima_dari;
        $this->penjaminId = $penjaminId;
        $this->headerGabung = $this->getHeaderGabung();
        $headerGabung = $this->headerGabung;
        $this->pendaftaran_id = !empty($headerGabung['pendaftaran_id_cetak']) ? $headerGabung['pendaftaran_id_cetak'] :null;
        
        $dataPayer = $this->getPayer();
        $this->dataPayer = $dataPayer;
        $listNewPayerId = !empty($dataPayer['listNewPayerId']) ? $dataPayer['listNewPayerId'] : [];
        $this->listNewPayerId = $listNewPayerId;
        $arrPayer = !empty($dataPayer['arrPayer']) ? $dataPayer['arrPayer'] : [];
        $this->arrPayer = $arrPayer;

        if(!empty($jenis_kwitansi)) {
            $jenis_kwitansi = explode(',', $jenis_kwitansi);
            if(in_array(self::KWITANSI_PENJAMIN, $jenis_kwitansi)) {
                foreach($listNewPayerId as $val){
                    array_push($jenis_kwitansi, $val);
                }
                /** blok hapus array jenis penjamin supaya tidak double */
                $k = array_search(self::KWITANSI_PENJAMIN,$jenis_kwitansi);
                unset($jenis_kwitansi[$k]);
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
        $this->total_dijamin = isset($header['total_dijamin']) ? $header['total_dijamin'] : 0;
        $this->dokter = isset($header['dokter']) ? $header['dokter'] : '-';
        $this->no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '';
        $this->resep_id = isset($header['penjualanresep_id']) ? $header['penjualanresep_id'] : null;
        $this->nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '';
        $this->instalasi_nama = isset($header['instalasi_nama']) ? $header['instalasi_nama'] : '';
        $this->instalasi_nama_ri = isset($header['instalasi_nama_ri']) ? $header['instalasi_nama_ri'] : '';
        $this->total_tagihan = isset($header['total_tagihan']) ? $header['total_tagihan'] : 0;
        $this->biaya_administrasi = isset($header['biaya_administrasi']) ? $header['biaya_administrasi'] : 0;
        $this->tgl_pembayaran = isset($header['tgl_pembayaran']) ? date('d-m-Y H:i:s', strtotime($header['tgl_pembayaran'])) : '-';
        $this->kasir = isset($header['kasir']) ? $header['kasir'] : '-';
        $this->no_pembayaran = isset($header['no_pembayaran']) ? $header['no_pembayaran'] : '-';
        $this->no_invoicepasien = isset($header['no_invoicepasien']) ? $header['no_invoicepasien'] : '-';
        $this->discount = isset($header['discount']) ? $header['discount'] : 0;
        $this->total_bayar = ($this->total_tagihan + $this->biaya_administrasi)  - $this->discount;
        $this->total_pembulatan = isset($header['total_pembulatan']) ? $header['total_pembulatan'] : 0;
    }

    protected function getInvoiceSudahBayar()
    {
        $pembayaranId = $this->invoice_id;
        $detail = [];
        if(!empty($pembayaranId)) {
            $detail = Yii::$app->db->createCommand("
                SELECT 
                SUM(infopasiensudahbayar_v.total_tagihan) AS total_tagihan,
                SUM(infopasiensudahbayar_v.biaya_administrasi) AS biaya_administrasi,
                SUM(pembayaran_t.total_tunai) AS total_tunai,
                SUM(pembayaran_t.total_nontunai) AS total_nontunai,
                SUM(pembayaran_t.total_kembalian) AS total_kembalian,
                SUM(pembayaran_t.total_pembulatan) AS total_pembulatan,
                SUM(pembayaran_t.total_dijamin) AS total_dijamin,
                SUM(pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) as discount
                FROM infopasiensudahbayar_v 
                JOIN pembayaran_t ON pembayaran_t.pembayaran_id = infopasiensudahbayar_v.pembayaran_id
                JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayaran_t.created_by
                JOIN pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
                LEFT JOIN instalasi_m ON infopasiensudahbayar_v.instalasi_id1 = instalasi_m.instalasi_id
                WHERE infopasiensudahbayar_v.pembayaran_id IN $pembayaranId"
            )->queryOne();
        }

        return $detail;
    }

    protected function getHeader()
    {
        $penjamin_nama = $no_rekam_medik = $nama_pasien = '-';
        if($this->pendaftaran_id) {
            $pendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $this->pendaftaran_id])->one();
            $is_admisi = !empty($pendaftaran['pasienadmisi_id']) ? true : false; 
            $instalasi_nama = !empty($pendaftaran['instalasi_nama']) ? $pendaftaran['instalasi_nama'] : false; 
            $this->instalasi_nama = $instalasi_nama;
            $this->dokter = isset($pendaftaran['nama_dok_rj_rd']) ? $pendaftaran['nama_dok_rj_rd'] : '-';

            if($is_admisi) {
                $pasienadmisi = InfoPasienRiView::find()->where(['pendaftaran_id' => $this->pendaftaran_id])->one();
                $penjamin_nama = !empty($pasienadmisi->penjamin_nama) ? $pasienadmisi->penjamin_nama : '';
                $no_rekam_medik = !empty($pasienadmisi->no_rekam_medik) ? $pasienadmisi->no_rekam_medik : null;
                $nama_pasien = $pasienadmisi->nama_depan . ' ' . $pasienadmisi->nama_pasien;
                $this->dokter = isset($pendaftaran['nama_dok_ri']) ? $pendaftaran['nama_dok_ri'] : '-';
            }
            else {
                $penjamin_nama = $pendaftaran->penjamin_nama;
                $no_rekam_medik = !empty($pendaftaran->no_rekam_medik) ? $pendaftaran->no_rekam_medik : null;
                $nama_pasien = $pendaftaran->nama_pasien;
            }
        } else if ($this->resep_id) {
            $pendaftaran = InfoDataPendaftaran::find()->where(['penjualanresep_id' => $this->resep_id])->one();
            $penjamin_nama = $pendaftaran->penjamin_nama;
            $nama_pasien = $pendaftaran->nama_pasien;
            $instalasi_nama = !empty($pendaftaran['instalasi_nama']) ? $pendaftaran['instalasi_nama'] : false; 
            $this->instalasi_nama = $instalasi_nama;
        }

        $this->nama_pasien = $nama_pasien;
        $this->penjamin_nama = $penjamin_nama;
        $this->no_rekam_medik = $no_rekam_medik;
        if(!$this->keterangan) {
            $this->keterangan = 'BIAYA PEMERIKSAAN DI '. strtoupper($this->instalasi_nama) . ' PASIEN ' . strtoupper($this->nama_pasien);
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
        $pembayaranId = $this->invoice_id;
        if (!empty($pembayaranId) && !empty($this->id) && !empty($this->total_nontunai)) {
            $metodePembayaran = Yii::$app->db->createCommand("
                SELECT jenisnontunai_id FROM pembayaranmetode_t 
                WHERE pembayaran_id IN {$pembayaranId} 
                AND jenisnontunai_id IS NOT NULL
            ")->queryAll();

            if (!empty($metodePembayaran)) {
                $listJenis = [];
                foreach ($metodePembayaran as $value) {
                    $listJenis[] = !empty($value['jenisnontunai_id']) ? $value['jenisnontunai_id'] : null;
                }

                if (!empty($listJenis)){
                    $qJenisNonTunai = JenisNonTunai::find()
                        ->select(['nama'])
                        ->andWhere(['jenisnontunai_id' => $listJenis])
                        ->asArray()
                        ->all();
    
                    foreach ($qJenisNonTunai as $value) {
                        $listPembayaran[] = !empty( $value['nama'] ) ? strtoupper($value['nama']) : '';
                    }
                }
            }
        }
        return $listPembayaran;
    }

    protected function detailPenjamin()
    {
        $pembayaranId = $this->invoice_id;
        $whereCond = '';
        $penjaminId = $this->penjaminId;
        if(!empty($penjaminId)) {
            $whereCond = ' AND invoice.penjamin_pelayanan_id = '.$penjaminId.'';
        }

        $listPenjamin = [];
        if(!empty($pembayaranId)){
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
                WHERE invoice.pembayaran_id IN $pembayaranId {$whereCond}
                GROUP BY penjamin_pelayanan_id,invoice.biaya_administrasi,penjamin_pelayanan,penjamin_tinpelayanan_id,no_pembayaran
            ")->queryAll();

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

        }
        return $listPenjamin;
    }

    protected function setAttributes($data = [])
    {
        $headerGabung = $this->headerGabung;
        $no_pembayaran = !empty($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-';
        $no_pendaftaran = !empty($headerGabung['no_pendaftaran']) ? $headerGabung['no_pendaftaran'] : $this->no_pendaftaran;
        $kasir = !empty($headerGabung['kasir']) ? $headerGabung['kasir'] : $this->kasir;
        $penjamin = !empty($headerGabung['penjamin']) ? $headerGabung['penjamin'] : '-' ;
        $billDate = !empty($headerGabung['tgl_invoicegabung']) ? $headerGabung['tgl_invoicegabung'] : null;
        $tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? $headerGabung['tgl_invoicegabung_cetak'] : $billDate ;
        $total_terbayar = 0;
        $nama_jenis_kwitansi = '';
        $txtListPembayaran = '';
        $diterima_dari = $this->diterima_dari;
        if (empty($diterima_dari)) {
            $diterima_dari = $this->nama_pasien;
        }
        $diterimaLabel = $diterima_dari;
        // $multiPayer = $this->getDataMultiPayer();
        $total_terbayar = $this->total_bayar + $this->total_pembulatan;
        $no_kwitansi = $no_pembayaran;
        if($data == self::KWITANSI_LENGKAP) {
            $total_terbayar = $this->total_bayar + $this->total_pembulatan;
        }
        elseif($data == self::KWITANSI_PASIEN) {
            $bayarPasien = ($this->total_tunai + $this->total_nontunai) - $this->total_kembalian;
            $total_terbayar = $bayarPasien < 0 ? 0 : $bayarPasien;
            $nama_jenis_kwitansi = 'PASIEN';
            $listPembayaran = $this->detailNonTunai();
            $txtListPembayaran = implode(", ", $listPembayaran);
        }
        elseif(in_array($data, $this->listNewPayerId)) {
            $total_terbayar = isset($this->arrPayer[$data]['total_dijamin']) ? $this->arrPayer[$data]['total_dijamin'] : 0;
            $diterimaLabel = isset($this->arrPayer[$data]['penjamin_nama']) ? $this->arrPayer[$data]['penjamin_nama'] : '-';
            $nama_jenis_kwitansi = 'PENJAMIN';
            $txtListPembayaran = '';
        }

        $pembulatan_total = $this->getPembulatan($total_terbayar);
        $total_terbayar = isset($pembulatan_total['total']) ? (int) $pembulatan_total['total'] : (int) $total_terbayar;

        $jumlah_diterima = DocoHelpers::formatNumber($total_terbayar);
        $terbilang = DocoHelpers::Terbilang($total_terbayar). ' Rupiah';
        if($total_terbayar == 0) {
            $terbilang = 'Nol Rupiah';
        }
        $attributes = [
            '#no_kwitansi#' => $no_kwitansi,
            '#nama_pasien#' => $this->nama_pasien,
            '#diterima_dari#' => strtoupper($diterimaLabel),
            '#keterangan#' => $this->keterangan,
            '#tgl_pembayaran#'=> !empty($billDate) ? date('d-m-Y', strtotime($billDate)) : '-',
            '#jumlah_diterima#'=> $jumlah_diterima,
            '#kasir#' => $kasir,
            '#terbilang#' => $terbilang,
            '#jenis_kwitansi#' => $nama_jenis_kwitansi,
            '#no_reg#' => $no_pendaftaran,
            '#no_mr#' => $this->no_rekam_medik,
            '#list_jenis_pembayaran#' => $txtListPembayaran,
            '#dokter#' => $this->dokter,
            '#tanggal_sekarang#' => !empty($billDate) ? date('d M Y', strtotime($billDate)) : '-',
            '#penjamin#' => $penjamin,
            '#tgl_invoicegabung_cetak#' => !empty($tgl_invoicegabung_cetak) ? date('d-m-Y', strtotime($tgl_invoicegabung_cetak)) : '-',
        ];

        return $attributes;
    }

    protected function cetak()
    {
        $print = new DocoPrint($this->dokTercetak);
        if (!empty($this->jenis_kwitansi)) {
            $countData = count($this->jenis_kwitansi);
            $i = 0;
            foreach ($this->jenis_kwitansi as $key => $value) {
                $attributes = $this->setAttributes($value);
                $print->attributes = $attributes;
                $break = (($i + 1) == $countData) ? false : true;
                $print->generateHtml($break);
                $i++;
            }
        }
        $print->Output(true);
    }

    protected function getPembayaran()
    {
        $invoicegabung_id = $this->id;
        $db = Yii::$app->db;
        $pembayaranId = $arrPayer = $data = $listNewPayerId = [];
        if(!empty($invoicegabung_id)){
            $sql = "SELECT 
                        invoicegabung_t.invoicegabung_id, 
                        invoicegabungdetail_t.pembayaran_id,
                        pembayaran_t.pasienadmisi_id,
                        COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) penjamin_id,
                        penjamin_m.penjamin_nama,
                        carabayar_m.groupcarabayar_id
                    FROM invoicegabung_t 
                    INNER JOIN invoicegabungdetail_t ON invoicegabungdetail_t.invoicegabung_id = invoicegabung_t.invoicegabung_id
                    INNER JOIN pembayaran_t ON pembayaran_t.pembayaran_id = invoicegabungdetail_t.pembayaran_id
                    LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pembayaran_t.pasienadmisi_id
                    INNER JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = invoicegabungdetail_t.pendaftaran_id
                    LEFT JOIN penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id
                    LEFT JOIN carabayar_m ON carabayar_m.carabayar_id = penjamin_m.carabayar_id
                    WHERE invoicegabung_t.invoicegabung_id = {$invoicegabung_id}";
            
            $pembayaran = $db->createCommand($sql)->queryAll();
            if(!empty($pembayaran)) {
                foreach ($pembayaran as $value) {
                    $pembayaranId[] = isset($value['pembayaran_id']) ? $value['pembayaran_id'] : null;
                    $penjamin_id = !empty($value['penjamin_id']) ? $value['penjamin_id'] : null;
                    $penjamin_id = $this->setPrefixPenjaminId($penjamin_id);
                    $penjamin_nama = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
                    $grup_cara_bayar = !empty($value['groupcarabayar_id']) ? $value['groupcarabayar_id'] : null;
                    if($grup_cara_bayar != $this->cara_bayar_umum){
                        $arrPayer[$penjamin_id] = [
                            'penjamin_nama' => $penjamin_nama,
                            'total_dijamin' => 0,
                        ];
                        
                        $listNewPayerId[] = $penjamin_id;
                        $listNewPayerId = array_unique($listNewPayerId);
                    }
                }
            }
            $pembayaranId = "(" . implode(",", $pembayaranId) . ")";
        }
		
        return [
            'pembayaranId' => $pembayaranId,
            'arrPayer' => $arrPayer,
            'listNewPayerId' => $listNewPayerId,
        ];
    }

    protected function getHeaderGabung()
    {
        $invoicegabung_id = $this->id;
        $data = [];
        if(!empty($invoicegabung_id)){
            $data = Yii::$app->db->createCommand("SELECT * FROM infoinvoicegabung_v WHERE invoicegabung_id = {$invoicegabung_id}")->queryOne();
        }
        return $data;
    }

    protected function getPayer()
	{
		$listPayer = $listMetode = $listNewPayer = $listNewPayerId = [];
		$isPayer = false;
		$payer = '';
		$total_dijamin = $this->total_dijamin;
        $pembayaranId = $this->invoice_id;
        $arrPayer = $this->arrPayer; 
        $listNewPayerId = $this->listNewPayerId; 
        $headerGabung = $this->headerGabung;
        $penjamin_id_cetak = !empty($headerGabung['penjamin_id_cetak']) ? $headerGabung['penjamin_id_cetak'] : 0;
        $penjamin_id_cetak = $this->setPrefixPenjaminId($penjamin_id_cetak);
        if(!empty($pembayaranId )){

            if ($total_dijamin > 0) {
                $isPayer = true;
                $listPayer = Yii::$app->db->createCommand("
                    SELECT 
                    penjamin_nama,
                    penjamin_id,
                    SUM(total_dijamin) AS total_dijamin
                    FROM pembayaranpenjamin_t
                    WHERE pembayaran_id IN {$pembayaranId}
                    GROUP BY penjamin_nama, penjamin_id
                ")->queryAll();
    
                if (!empty($listPayer)) {
                    $grand_total_dijamin = 0;
                    foreach ($listPayer as $value) {
                        $penjamin_id = !empty($value['penjamin_id']) ? $value['penjamin_id'] : '';
                        $penjamin_id = $this->setPrefixPenjaminId($penjamin_id);
                        $total_dijamin = !empty($value['total_dijamin']) ? $value['total_dijamin'] : '';
                        $penjaminNama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
                        $listNewPayer[] = $penjaminNama;
                        $grand_total_dijamin += $total_dijamin;

                        /** Untuk handling penjamin yang bukan umum, arr payer ini sudah dikondisiin grup cara bayar sblmnya di get pembayaran 
                         * jadi kalo yang ga ke set di get pembayaran akan di skip */
                        if(isset($arrPayer[$penjamin_id])){
                            $arrPayer[$penjamin_id] = [
                                'penjamin_nama' => $penjaminNama,
                                'total_dijamin' => $total_dijamin,
                            ];
                            $listNewPayerId[] = $penjamin_id;
                            $listNewPayerId = array_unique($listNewPayerId);
                        }
                    
                        /** Update 2022 - 04 - 04 
                         * ketika digabung, maka semua penjamin digabungkan ke penjamin_id_cetak
                         * code sebelumnya (tampilkan dan pisahkan kwitansi sebanyak penjaminnya ) di keep supaya kalo ada kebutuhan bisa digunakan kembali
                         * code dibawah ini untuk mengubah list array semua penjamin menjadi gabungan penjamin dengan key penjamin id tercetak
                        */
                        if($penjamin_id != $penjamin_id_cetak){
                            unset($arrPayer[$penjamin_id]);

                            /** blok hapus array penjamin_id agar hanya muncul penjamin_id cetak */
                            $k = array_search($penjamin_id,$listNewPayerId);
                            unset($listNewPayerId[$k]);
                        } 

                    }
                    $payer = implode(", ", $listNewPayer);

                    if(isset($arrPayer[$penjamin_id_cetak])){
                        $arrPayer[$penjamin_id_cetak]['total_dijamin'] = $grand_total_dijamin;
                        $listNewPayerId[] = $penjamin_id_cetak;
                        $listNewPayerId = array_unique($listNewPayerId);
                    }

                }
            }
    
            if (!empty($pembayaran['total_nontunai'])) {
                $listMetode = Yii::$app->db->createCommand("
                    SELECT 
                        metode_bayar,
                        total_dibayar,
                        no_kartu
                    FROM pembayaranmetode_t
                    WHERE pembayaran_id IN {$pembayaranId}
                ")->queryAll();
            }
        }

		return [
			'payer' => $payer,
			'isPayer' => $isPayer,
			'listPayer' => $listPayer,
			'listMetode' => $listMetode,
			'listNewPayerId' => $listNewPayerId,
            'arrPayer' => $arrPayer,
		];
	}

    protected function getPembulatan($nominal = 0){
        $confSistem = Cache::getKonfigSistem();

        /** Blok Pembukatan */
        $isPembulatan = isset($confSistem['is_pembulatankeatas']) ? $confSistem['is_pembulatankeatas'] : null;
        $satuanPembulatan = !empty($confSistem['satuanpembulatan']) ? $confSistem['satuanpembulatan'] : 0;
        $pembulatanNominal = DocoHelpers::pembulatan($nominal > 1 ? round($nominal,2) : 0, $isPembulatan, $satuanPembulatan);
        return $pembulatanNominal;

    }

    protected function setPrefixPenjaminId($id){
        return 'penjamin-'.$id;
    }

	protected function processFlow()
  	{
    	$this->populateData();
        $this->getHeader();
        $this->cetak();
  	}
}