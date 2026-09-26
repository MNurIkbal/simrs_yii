<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\models\kasir\InfoPasienRiView;
use Doco\models\kasir\InfoDataPendaftaran;
use Doco\models\kasir\JenisNonTunai;
use Doco\models\ProfilRsView;
use app\modules\v1\cache\Cache;
use Doco\components\DocoConstants;
use Doco\models\Pegawai;
use Doco\components\DocoHtml;
use yii\helpers\ArrayHelper;

class CetakKwitansiProcess extends \Doco\components\DocoBaseProcessExtension
{
	const KWITANSI_LENGKAP = 1;
    const KWITANSI_PASIEN = 2;
    const KWITANSI_PENJAMIN = 3;
    const KWITANSI_SUBPAYER = 4;
    const KEY_PAYER_UTAMA = 'payer_utama';
    const KEY_SUBPAYER = 'sub_payer';
    const KEY_PENJAMIN = 'penjamin';

    protected $dokTercetak = 'kwitansi-inf-pas-sudah-bayar';
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
    protected $total_dibayar_pasien;
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
    protected $nama_pegawai;
    protected $total_lengkap;
    protected $pembulatan;

    protected function populateData()
    {
        $request = $this->_requestData;
        $id = $request->get('id', null);
        $invoice_id = $request->get('pembayaran_id', null);
        $nama_pegawai = $this->_requestData->get('nama_pegawai', null);
        $pdf_id = $request->get('pdf_id', null);
        $keterangan = $request->get('keterangan', null);
        $diterima_dari = $request->get('diterima_dari', null);
        $jenis_kwitansi = $request->get('jenis_kwitansi', null);
        $penjaminId = $request->get('penjamin_id', null);
        
        $this->id = $id;
        $this->invoice_id = $invoice_id;
        $this->detailPenjamin = $this->detailPenjamin();
        $this->pdf_id = $pdf_id;
        $this->keterangan = $keterangan;
        $this->diterima_dari = $diterima_dari;
        $this->penjaminId = $penjaminId;
        $this->nama_pegawai = $nama_pegawai;

        if(!empty($jenis_kwitansi)) {
            $jenis_kwitansi = explode(',', $jenis_kwitansi);
            if(in_array(self::KWITANSI_PENJAMIN, $jenis_kwitansi)) {
                if(empty($penjaminId) && isset($this->detailPenjamin[self::KEY_SUBPAYER])) {
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
        $this->total_tunai = ArrayHelper::getValue($header, 'total_tunai', 0);
        $this->sisa_uangmuka = ArrayHelper::getValue($header, 'sisa_uangmuka', 0);
        $this->total_nontunai = ArrayHelper::getValue($header, 'total_nontunai', 0);
        $this->total_kembalian = ArrayHelper::getValue($header, 'total_kembalian', 0);
        $this->total_tagihan = ArrayHelper::getValue($header, 'total_tagihan', 0);
        $this->discount = ArrayHelper::getValue($header, 'discount', 0);
        $this->biaya_administrasi = ArrayHelper::getValue($header, 'biaya_administrasi', 0);
        $this->dokter = ArrayHelper::getValue($header, 'dokter', '-');
        $this->no_pendaftaran = ArrayHelper::getValue($header, 'no_pendaftaran');
        $this->pendaftaran_id = ArrayHelper::getValue($header, 'pendaftaran_id');
        $this->resep_id = ArrayHelper::getValue($header, 'penjualanresep_id');
        $this->nama_pasien = ArrayHelper::getValue($header, 'nama_pasien');
        $this->instalasi_nama = ArrayHelper::getValue($header, 'instalasi_nama') ;
        $this->instalasi_nama_ri = ArrayHelper::getValue($header, 'instalasi_nama_ri');
        $this->pembulatan = ArrayHelper::getValue($header, 'pembulatan');
        $this->tgl_pembayaran = ArrayHelper::getValue($header, 'tgl_pembayaran', '-');
        $this->kasir = ArrayHelper::getValue($header, 'kasir', '-');
        $this->no_pembayaran = ArrayHelper::getValue($header, 'no_pembayaran', '-');
        $this->no_invoicepasien = ArrayHelper::getValue($header, 'no_invoicepasien', '-');
        $this->total_discount = ArrayHelper::getValue($header, 'total_discount', '0');
        $this->total_bayar = ($this->total_tagihan + $this->biaya_administrasi)  - $this->discount - $this->sisa_uangmuka;
        $this->total_dibayar_pasien = ArrayHelper::getValue($header, 'total_dibayar', 0);
    }

    protected function getInvoiceSudahBayar()
    {
        return Yii::$app->db->createCommand("
            SELECT 
            infopasiensudahbayar_v.no_pendaftaran,
            infopasiensudahbayar_v.pendaftaran_id,
            infopasiensudahbayar_v.penjualanresep_id,
            infopasiensudahbayar_v.nama_pasien,
            infopasiensudahbayar_v.instalasi_nama,
            infopasiensudahbayar_v.total_tagihan,
            infopasiensudahbayar_v.tgl_pembayaran,
            infopasiensudahbayar_v.pegawai_rd_rj AS dokter,
            infopasiensudahbayar_v.no_pembayaran,
            infopasiensudahbayar_v.no_pembayaran_header,
            infopasiensudahbayar_v.no_invoicepasien, 
            infopasiensudahbayar_v.biaya_administrasi,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_discount,
            pembayaran_t.sisa_uangmuka,
            pembayaran_t.total_dibayar,
            (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) as discount,
            pegawai_m.nama_pegawai AS kasir,
            instalasi_m.instalasi_nama AS instalasi_nama_ri,
            (pembayaran_t.pembulatan + pembayaran_t.total_pembulatan) as pembulatan
            FROM infopasiensudahbayar_v 
            JOIN pembayaran_t ON pembayaran_t.pembayaran_id = infopasiensudahbayar_v.pembayaran_id
            JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayaran_t.created_by
            JOIN pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
            LEFT JOIN instalasi_m ON infopasiensudahbayar_v.instalasi_id1 = instalasi_m.instalasi_id
            WHERE infopasiensudahbayar_v.pembayaran_id = $this->invoice_id "
        )->queryOne();
    }

    protected function getHeader()
    {
        $penjamin_nama = $no_rekam_medik = $nama_pasien = '-';
        if($this->pendaftaran_id) {
            $pasienadmisi = InfoPasienRiView::find()->where(['pendaftaran_id' => $this->pendaftaran_id])->one();
            if($pasienadmisi) {
                $penjamin_nama = $pasienadmisi->penjamin_nama;
                $no_rekam_medik = !empty($pasienadmisi->no_rekam_medik) ? $pasienadmisi->no_rekam_medik : null;
                $nama_pasien = $pasienadmisi->nama_depan . ' ' . $pasienadmisi->nama_pasien;
                $this->instalasi_nama = $this->instalasi_nama_ri;
            }
            else {
                $pendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $this->pendaftaran_id])->one();
                $penjamin_nama = $pendaftaran->penjamin_nama;
                $no_rekam_medik = !empty($pendaftaran->no_rekam_medik) ? $pendaftaran->no_rekam_medik : null;
                $nama_pasien = $pendaftaran->nama_pasien;
            }
        } else if ($this->resep_id) {
            $pendaftaran = InfoDataPendaftaran::find()->where(['penjualanresep_id' => $this->resep_id])->one();
            $penjamin_nama = $pendaftaran->penjamin_nama;
            $nama_pasien = $pendaftaran->nama_pasien;
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
        $biayaAdmin = $this->getBiayaAdmin();
        $adminPayerUtama = ArrayHelper::getValue($biayaAdmin, 'adminPayerUtama', 0);
        $adminSubPayer = ArrayHelper::getValue($biayaAdmin, 'adminSubPayer', 0);
        $pembulatanPayerUtama = ArrayHelper::getValue($biayaAdmin, 'pembulatanPayerUtama', 0);
        $pembulatanSubPayer = ArrayHelper::getValue($biayaAdmin, 'pembulatanSubPayer', 0);
        $payerUtama = [];
        $namaPayerUtama = $noKwitansiPayerUtama = $namaSubPayer = $noKwitansiSubPayer = "";
        $dijaminSubPayer = $dijaminPayerUtama = 0;
        if(isset($detailPenjamin[self::KEY_PAYER_UTAMA]) || isset($detailPenjamin[self::KEY_SUBPAYER])) {
            $payerUtama =  ArrayHelper::getValue($detailPenjamin, self::KEY_PAYER_UTAMA, []);
            $namaPayerUtama = ArrayHelper::getValue($payerUtama, 'penjamin_nama', '-');
            $noKwitansiPayerUtama = ArrayHelper::getValue($payerUtama, 'no_pembayaran', '-');
            $dijaminPayerUtama = ArrayHelper::getValue($payerUtama, 'tarif_dijamin', 0);
            $dijaminPayerUtama = round($dijaminPayerUtama, 2);
            //$dijaminPayerUtama = round($dijaminPayerUtama, 2) + round($adminPayerUtama, 2) + round($pembulatanPayerUtama, 2);

            $subPayer = ArrayHelper::getValue($detailPenjamin, self::KEY_SUBPAYER, []);
            $namaSubPayer = ArrayHelper::getValue($subPayer, 'penjamin_nama', '-');
            $noKwitansiSubPayer = ArrayHelper::getValue($subPayer, 'no_pembayaran', '-');
            $dijaminSubPayer = ArrayHelper::getValue($subPayer, 'tarif_dijamin', 0);
            $dijaminSubPayer = round($dijaminSubPayer, 2) + round($pembulatanSubPayer, 2);
            //$dijaminSubPayer =  round($dijaminSubPayer, 2) + round($adminSubPayer, 2) + round($pembulatanSubPayer, 2);
        }
        else {
            $detailPenjaminUtama = ArrayHelper::getValue($detailPenjamin, 'penjamin', []);
            $isPenjaminUtama = ArrayHelper::getValue($detailPenjamin, 'is_penjaminutama');
            $penjaminNama = ArrayHelper::getValue($detailPenjamin, 'penjamin_nama');
            $tarifDijamin = ArrayHelper::getValue($detailPenjamin, 'tarif_dijamin', 0);
            $noPembayaran = ArrayHelper::getValue($detailPenjamin, 'no_pembayaran', '-');
            
            if($isPenjaminUtama) {
                $payerUtama = $detailPenjaminUtama;
                $namaPayerUtama = $penjaminNama;
                $noKwitansiPayerUtama = $noPembayaran;
                $dijaminPayerUtama = round($tarifDijamin, 2);
                //$dijaminPayerUtama = round($tarifDijamin, 2) + round($adminPayerUtama, 2) + round($pembulatanPayerUtama, 2);
            }
            else {
                $subPayer = $detailPenjaminUtama;
                $namaPayerUtama = $penjaminNama;
                $noKwitansiPayerUtama = $noPembayaran;
                $dijaminPayerUtama = round($tarifDijamin, 2) + round($pembulatanSubPayer, 2);
                //$dijaminPayerUtama = round($tarifDijamin, 2) + round($adminSubPayer, 2) + round($pembulatanSubPayer, 2);
            }
        }

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
                    $listJenis[] = ArrayHelper::getValue($value, 'jenisnontunai_id');
                }

                $qJenisNonTunai = JenisNonTunai::find()
                    ->select(['nama'])
                    ->andWhere(['jenisnontunai_id' => $listJenis])
                    ->asArray()
                    ->all();

                foreach ($qJenisNonTunai as $value) {
                    $listPembayaran[] = strtoupper(ArrayHelper::getValue($value, 'nama'));
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
            $whereCond = ' AND pembayaranpelayanan_t.penjamin_id = '.$penjaminId.'';
        }
        
        /*
        $data = Yii::$app->db->createCommand("
            SELECT DISTINCT 
            invoice.penjamin_pelayanan_id AS penjamin_id, 
            carabayar_m.carabayar_nama,
            carabayar_m.groupcarabayar_id,
            invoice.penjamin_pelayanan AS penjamin_nama, 
            SUM(invoice.tarif_dijamin) AS tarif_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.is_penjaminutama
            FROM invoicesudahbayardetail_v invoice
            JOIN pembayaranpelayanan_t ON invoice.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
            JOIN carabayar_m ON carabayar_m.carabayar_id = invoice.carabayar_pelayanan_id
            WHERE invoice.pembayaran_id = $this->invoice_id {$whereCond} AND invoice.is_diskon = false
            GROUP BY penjamin_pelayanan_id,carabayar_m.carabayar_nama,carabayar_m.groupcarabayar_id,
            invoice.penjamin_pelayanan,no_pembayaran,pembayaranpelayanan_t.is_penjaminutama
        ");
        */

        $data = Yii::$app->db->createCommand("
            SELECT DISTINCT 
                penjamin_m.penjamin_id AS penjamin_id, 
                carabayar_m.carabayar_nama,
                carabayar_m.groupcarabayar_id,
                penjamin_m.penjamin_nama AS penjamin_nama, 
                invoice.total_dijamin AS tarif_dijamin,
                pembayaranpelayanan_t.no_pembayaran,
                pembayaranpelayanan_t.is_penjaminutama
            FROM pembayaran_t invoice
            JOIN pembayaranpelayanan_t ON invoice.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
            JOIN carabayar_m ON carabayar_m.carabayar_id = pembayaranpelayanan_t.carabayar_id
            JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
            WHERE invoice.pembayaran_id = $this->invoice_id {$whereCond}
            GROUP BY penjamin_m.penjamin_id,carabayar_m.carabayar_nama, carabayar_m.groupcarabayar_id, pembayaranpelayanan_t.is_penjaminutama, pembayaranpelayanan_t.no_pembayaran, invoice.total_dijamin
        ");

        $data = $data->queryAll();

        $listPenjamin = [];
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                $isPayerUtama = ArrayHelper::getValue($value, 'is_penjaminutama');
                if(!empty($penjaminId)) {
                    $listPenjamin[self::KEY_PENJAMIN] = $value;
                }
                else {
                    if($isPayerUtama) {
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
            /* $totalTerbayar = $this->total_tagihan + $this->biaya_administrasi + $this->pembulatan; */
            $totalTerbayar = $dijaminPayerUtama + ($this->total_tunai + $this->total_nontunai);
        }
        elseif($data == self::KWITANSI_PASIEN) {
            $bayarPasien = ($this->total_tunai + $this->total_nontunai) - $this->total_kembalian;
            $dijaminPayerUtama = ArrayHelper::getValue($multiPayer, 'dijaminPayerUtama', 0);
            $totalDiscount = $dijaminPayerUtama > 0 ? 0 : $this->total_discount;
            //$totalTerbayar = $bayarPasien < 0 ? 0 : $bayarPasien - $totalDiscount + $diskonDokter;
            $namaJenisKwitansi = 'PASIEN';
            $txtListPembayaran = implode(", ", $listPembayaran);
            $noKwitansi = $this->no_invoicepasien;
            $totalTerbayar = ($this->total_tunai + $this->total_nontunai);
        }
        elseif($data == self::KWITANSI_PENJAMIN) {
            $totalTerbayar = ArrayHelper::getValue($multiPayer, 'dijaminPayerUtama', 0);
            //$totalTerbayar = $totalTerbayar > 0 ? $totalTerbayar - $this->total_discount : 0;
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
        
        $jumlahDiterima = DocoHelpers::formatNumber($totalTerbayar,true,false,0);
        $terbilang = DocoHelpers::Terbilang($totalTerbayar). ' Rupiah';
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
        $kota = '-';
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
          elseif($match = preg_match("/Kota /i", $profilRs['kota'])) {
              $pattern = "Kota ";
          }

          $kota = str_replace($pattern,"", $profilRs['kota']);
        }
          
        return [
          'namaRs' => $namaRs,
          'kota' => $kota,
          'alamat' => $profilRs['alamatlokasi_rumahsakit'],
          'no_telp' => $profilRs['no_telp_profilrs'],
        ];
    }

    protected function getPembulatan($nominal = 0)
    {
        $confSistem = Cache::getKonfigSistem();

        /** Blok Pembukatan */
        $isPembulatan = ArrayHelper::getValue($confSistem, 'is_pembulatankeatas', false);
        $satuanPembulatan = ArrayHelper::getValue($confSistem, 'satuanpembulatan', 0);
        return (new DocoHelpers)->pembulatan($nominal > 1 ? round($nominal,2) : 0, $isPembulatan, $satuanPembulatan);
    }

    protected function getListPembulatan()
    {
        $pembayaran = Yii::$app->db->createCommand("SELECT additional_data FROM pembayaran_t WHERE pembayaran_id = {$this->invoice_id}")->queryOne();
        $pembayaranPelayanan = Yii::$app->db->createCommand("SELECT penjamin_id, pembulatan FROM pembayaranpelayanan_t WHERE pembayaran_id = {$this->invoice_id}")->queryAll();
        $listPembulatanPayer = [];
        if(!empty($pembayaranPelayanan)) {
            foreach ($pembayaranPelayanan as $key => $value) {
                $payerId = ArrayHelper::getValue($value, 'penjamin_id');
                $pembulatan = ArrayHelper::getValue($value, 'pembulatan');
                $listPembulatanPayer[$payerId] = $pembulatan;
            }
        }
        $additionalData = ArrayHelper::getValue($pembayaran, 'additional_data', []);
        if(!empty($additionalData)) {
            $additionalData = json_decode($additionalData, true);
        }

        return [
            'listPembulatanPayer' => $listPembulatanPayer,
            'additionalData' => $additionalData,
        ];
    }

    protected function getBiayaAdmin()
    {
        $dataPembulatanPayer = $this->getListPembulatan();
        $listPembulatanPayer = ArrayHelper::getValue($dataPembulatanPayer, 'listPembulatanPayer', []);
        $additionalData = ArrayHelper::getValue($dataPembulatanPayer, 'additionalData', []);
        $payerUtama = $this->getPayerUtama();
        $penjaminId = null;
        $biayaAdmin = $adminPayerUtama = $adminSubPayer = $pembulatanPayerUtama = $pembulatanSubPayer = 0;
        $admAsuransi = ArrayHelper::getValue($additionalData, 'adm_asuransi', []);
        $detailPenjamin = $this->detailPenjamin;
        if(!empty($admAsuransi)) {
            $dijaminAsuransi = ArrayHelper::getValue($admAsuransi, 'dijamin', 0);
            $defaultPenjamin = ArrayHelper::getValue($admAsuransi, 'defaultPenjamin', []);
            $defaultPenjaminId = ArrayHelper::getValue($defaultPenjamin, 'id');
            if(!empty($this->penjaminId)) {
                $penjaminId = $this->penjaminId;
                if($defaultPenjaminId == $penjaminId) {
                    $penjaminId = $defaultPenjaminId;
                    if($dijaminAsuransi > 0){
                        $biayaAdmin = $dijaminAsuransi;
                        if($penjaminId == $defaultPenjaminId) {
                            if($payerUtama == $penjaminId) {
                                $adminPayerUtama = $biayaAdmin;
                                if(isset($listPembulatanPayer[$penjaminId])) {
                                    $pembulatanPayerUtama = $listPembulatanPayer[$penjaminId];
                                }
                            }   
                        }
                    }                                  
                }
                else {
                    $adminSubPayer = $biayaAdmin;
                    if(isset($listPembulatanPayer[$penjaminId])) {
                        $pembulatanSubPayer = $listPembulatanPayer[$penjaminId];
                    }
                }
            }
            else {
                if(isset($detailPenjamin[self::KEY_PAYER_UTAMA])) {
                    if(isset($detailPenjamin[self::KEY_PAYER_UTAMA]['penjamin_id'])) {
                        $penjaminId = $detailPenjamin[self::KEY_PAYER_UTAMA]['penjamin_id'];
                        if($defaultPenjaminId == $penjaminId) {
                            $penjaminId = $defaultPenjaminId;
                            if($dijaminAsuransi > 0){
                                $biayaAdmin = $dijaminAsuransi;
                                if($penjaminId == $defaultPenjaminId) {
                                    $adminPayerUtama = $biayaAdmin;
                                }
                            }
                        }
                        if(isset($listPembulatanPayer[$penjaminId])) {
                            $pembulatanPayerUtama = $listPembulatanPayer[$penjaminId];
                        }
                    }
                }
                if(isset($detailPenjamin[self::KEY_SUBPAYER])) {
                    if(isset($detailPenjamin[self::KEY_SUBPAYER]['penjamin_id'])) {
                        $penjaminId = $detailPenjamin[self::KEY_SUBPAYER]['penjamin_id'];
                        if($defaultPenjaminId == $penjaminId) {
                            $penjaminId = $defaultPenjaminId;
                            if($dijaminAsuransi > 0){
                                $biayaAdmin = $dijaminAsuransi;
                                if($penjaminId == $defaultPenjaminId) {
                                    $adminSubPayer = $biayaAdmin;
                                }
                            }                                  
                        }
                        if(isset($listPembulatanPayer[$penjaminId])) {
                            $pembulatanSubPayer = $listPembulatanPayer[$penjaminId];
                        }
                    }
                }
            }
        }
        else {
            if(!empty($this->penjaminId)) {
                $penjaminId = $this->penjaminId;
                if(isset($listPembulatanPayer[$penjaminId])) {
                    if($payerUtama == $penjaminId) {
                        $pembulatanPayerUtama = $listPembulatanPayer[$penjaminId];
                    }
                    else {
                        $pembulatanSubPayer = $listPembulatanPayer[$penjaminId];
                    }
                }
            }
            else {
                if(isset($detailPenjamin[self::KEY_PAYER_UTAMA])) {
                    if(isset($detailPenjamin[self::KEY_PAYER_UTAMA]['penjamin_id'])) {
                        $penjaminId = $detailPenjamin[self::KEY_PAYER_UTAMA]['penjamin_id'];
                        if(isset($listPembulatanPayer[$penjaminId])) {
                            $pembulatanPayerUtama = $listPembulatanPayer[$penjaminId];
                        }
                    }
                }
                if(isset($detailPenjamin[self::KEY_SUBPAYER])) {
                    if(isset($detailPenjamin[self::KEY_SUBPAYER]['penjamin_id'])) {
                        $penjaminId = $detailPenjamin[self::KEY_SUBPAYER]['penjamin_id'];
                        if(isset($listPembulatanPayer[$penjaminId])) {
                            $pembulatanSubPayer = $listPembulatanPayer[$penjaminId];
                        }
                    }
                }
            }
        }
        return [
            'adminPayerUtama' => $adminPayerUtama,
            'adminSubPayer' => $adminSubPayer,
            'pembulatanPayerUtama' => $pembulatanPayerUtama,
            'pembulatanSubPayer' => $pembulatanSubPayer,
        ];
    }

    protected function getPayerUtama()
    {
        $detailPenjamin = $this->detailPenjamin;
        $penjaminId = null;
        if(!empty($detailPenjamin)) {
            foreach ($detailPenjamin as $key => $value) {
                $isPayerUtama = ArrayHelper::getValue($value, 'is_penjaminutama', false);
                if($isPayerUtama) {
                    $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');
                }
            }
        }
        return $penjaminId;
    }

    protected function getDataDiskon()
    {
        $pembayaran = Yii::$app->db->createCommand("SELECT additional_data FROM pembayaran_t WHERE pembayaran_id = {$this->invoice_id}")->queryOne();
        $additionalData = ArrayHelper::getValue($pembayaran, 'additional_data', []);
        $additionalData = json_decode($additionalData, true);
        $diskonDokter = ArrayHelper::getValue($additionalData, 'pembayaran_diskon', []);
        $totalDiskonDokter = 0;
        if(!empty($diskonDokter)) {
            foreach ($diskonDokter as $key => $value) {
                $totalDiscount = ArrayHelper::getValue($value, 'total_diskon', 0);
                $totalDiskonDokter += $totalDiscount;
            }
        }
        return $totalDiskonDokter;
    }

	protected function processFlow()
  	{   
        $this->populateData();
    	$this->getBuktiMasuk();
        $this->getHeader();
        $this->cetak();
  	}
}
