<?php

/**
 * ? @author : Budi (budi@docotel.com)
 * * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Extensions\kasir\InvoicePayerBelumBayar;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use Doco\models\kasir\PasienView;
use Doco\models\kasir\InvoiceObatView;
use Doco\models\kasir\PembayaranPelayanan;
use Doco\models\ProfilRsView;
use Doco\models\KonfigSystem;
use Doco\models\KonfigTarif;
use Doco\models\Pegawai;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\DaftarTindakan;

class CetakDetailInvoiceProcess extends \Doco\components\DocoBaseProcessExtension
{
   const INVOICE_LENGKAP = 1;
   const INVOICE_PASIEN = 2;
   const INVOICE_PENJAMIN = 3;
   const DEFAULT_BIAYA_ADMIN = 'Admin Fee';
   const LAYANAN_TINDAKAN = 'tindakan';
   const LAYANAN_OBAT = 'obat';
   const TUNAI = 'Tunai';
   const NON_TUNAI = 'Non Tunai';
   const PENJAMIN = 'Penjamin';

   public $dokTercetak = 'detail-invoice-pembayaran-ri';
   public $dokPath = 'invoice-detail-ranap';
   public $pisahBill = false;

   protected $id;
   protected $invoice_id;
   protected $jenis_invoice;
   protected $kelompok;
   protected $nama_pegawai;
   protected $isObat = false;
   protected $pasienadmisi_id;
   protected $instalasi_id;
   protected $no_rekam_medik;
   protected $model = [];
   protected $header = [];
   protected $detail = [];
   protected $pembayaran = [];
   protected $dataPayer = [];
   protected $detailPasien = [];
   protected $dataTindakan = [];
   protected $arrDokter = [];
   protected $dataRoomRent = [];
   protected $dataRs = []; 
   protected $konfigSystem = [];
   protected $tindakan_keperawatan = [];
   protected $groupBill = [];
   protected $grandTotal;
   protected $totalDijamin;
   protected $additionalTindakan = true;

   protected function populateData()
   {
      $request = $this->_requestData;
      $kelompok = $request->get('kelompok');
      $id = $request->get('id');
      $invoice_id = $request->get('invoice_id');
      $jenis_invoice = $request->get('jenis_invoice', 1);
      $this->nama_pegawai = $request->get('nama_pegawai', null);
      $this->penjaminId = $request->get('penjamin_id', null);
      $this->id = $id;
      $this->invoice_id = $invoice_id;
      $this->jenis_invoice = $jenis_invoice;
      $this->kelompok = $kelompok;
      
      $model = $this->getPendaftaran();
      $this->model = $model;

      $pasienadmisi_id = ArrayHelper::getValue($model, 'pasienadmisi_id');
      $instalasi_id = ArrayHelper::getValue($model, 'instalasi_id');
      $this->pasienadmisi_id = $pasienadmisi_id;
      $this->instalasi_id = $instalasi_id;
      if(empty($this->model) || $this->kelompok == DocoConstants::PASIEN_ALKES) {
         $this->isObat = true;
      }

      $header = $this->getHeader();
      $this->header = $header;
      $dataRoomRent = !empty($pasienadmisi_id) ? $this->getDataRoomRent() : [];
      $this->dataRoomRent = $dataRoomRent;

      $pembayaran = $this->getPembayaran();
      $this->pembayaran = $pembayaran;

      $dataPayer = $this->getPayer();
      $this->dataPayer = $dataPayer;

      $this->dataRs = $this->getProfileRs();
      $this->konfigSystem = $this->getKonfigSistem();
      $this->konfigTarif = $this->getKonfigTarif();

      $constantID = new DocoConstansId;
      $tindakan_keperawatan = $constantID->ActionGetAdditional('tindakan_keperawatan');
      $tindakan_keperawatan = json_decode($tindakan_keperawatan, true);
      $this->tindakan_keperawatan = $tindakan_keperawatan;
   }

   protected function detailPasien()
   {
      $header = $this->header;
      $noRekamMedik = ArrayHelper::getValue($header, 'no_rekam_medik');
      if(empty($noRekamMedik)) {
         return [];
      }

      $result = PasienView::find()->select([
         'alamat_pasien','propinsi_nama', 'kabupaten_nama',
         'kecamatan_nama', 'kelurahan_nama', 'warganegara'
      ])->where([
         'no_rekam_medik' => $noRekamMedik
      ])->one();

      return [
         'kecamatan_nama' => ArrayHelper::getValue($result, 'kecamatan_nama'),
         'kelurahan_nama' => ArrayHelper::getValue($result, 'kelurahan_nama'),
         'kabupaten_nama' => ArrayHelper::getValue($result, 'kabupaten_nama'),
         'propinsi_nama' => ArrayHelper::getValue($result, 'propinsi_nama'),
         'warganegara' => ArrayHelper::getValue($result, 'warganegara'),
      ];
   }

   protected function getDetailTindakan()
   {
      $whereClause = '';
      $withPenjaminId = '';
      if(!empty($this->penjaminId)) {
         $withPenjaminId = ' AND penjamin_pelayanan_id = '.$this->penjaminId.'';
      }
      if($this->jenis_invoice == self::INVOICE_PENJAMIN) {
         $whereClause = 'AND tarif_dijamin != 0 '.$withPenjaminId.' ';
      }
      elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
         $whereClause = 'AND tarif_dibayarkan != 0';
      }
      
      $query = "
      SELECT 
      kelompoktindakan_nama AS kelompok, 
      tindakan_obat_nama AS tindakan_obat, 
      tgl_pelayanan,
      pelayanan_id, 
      dokter_tindakan AS dokter, 
      qty, 
      tarif_satuan AS harga_satuan, 
      sub_total AS tarif, 
      is_obat, 
      is_konsultasi, 
      tarif_diskon, 
      tarif_cyto AS tarifcyto_tindakan, 
      ruangan_pelayanan AS ruangan,
      CASE WHEN tarif_cyto > 0 THEN true ELSE false END AS cyto_tindakan,
      kelompoktindakan_id,
      tarif_dijamin,
      tarif_dibayarkan,
      tarifpenyulit_tindakan,
      pendaftaran_id,
      jenis_racikan,
      tindakan_obat_kode,
      kelaspelayanan_nama as kelas,
      kamarruangan_nokamar as kamar,
      penjamin_pelayanan_id AS penjamin_id,
      is_diskon
      FROM invoicesudahbayardetail_v 
      WHERE pembayaran_id = {$this->invoice_id} AND sub_total != 0
      {$whereClause} ";

      $instalasiRanapId = DocoConstants::VAR_I_RANAP;
      // $instalasiRajalId = DocoConstants::VAR_I_RJ;
      // $instalasiBedahId = DocoConstants::INSTALASI_BEDAH;
      $tindakanNonAkomodasi = Yii::$app->db->createCommand("{$query} AND is_akomodasi = FALSE ORDER BY tgl_pelayanan, tindakan_obat ASC, harga_satuan DESC")->queryAll();
      // $tindakanAkomodasiBedah = Yii::$app->db->createCommand("{$query} AND is_akomodasi = TRUE AND instalasi_id IN ({$instalasiRajalId},{$instalasiBedahId}) ORDER BY tgl_pelayanan, tindakan_obat ASC, harga_satuan DESC")->queryAll();
      $tindakanAkomodasiBedah = Yii::$app->db->createCommand("{$query} AND is_akomodasi = TRUE AND instalasi_id != {$instalasiRanapId} ORDER BY tgl_pelayanan, tindakan_obat ASC, harga_satuan DESC")->queryAll();
      return array_merge($tindakanNonAkomodasi, $tindakanAkomodasiBedah);
   }

   protected function getBillNo()
   {
      $pembayaran = $this->pembayaran;
      $billNo = ArrayHelper::getValue($pembayaran, 'no_pembayaran');
      $jenisInvoice = $this->jenis_invoice;
      $pembayaranPelayanan = PembayaranPelayanan::find()->where([
         'pembayaran_id' => $this->invoice_id,
      ]);

      if($jenisInvoice != self::INVOICE_LENGKAP) {
         if($jenisInvoice == self::INVOICE_PASIEN) {
            $billNo = ArrayHelper::getValue($pembayaran, 'no_invoicepasien');
         }
         else {
            if(!empty($this->penjaminId)) {
               $data = $pembayaranPelayanan->andWhere(['penjamin_id' => $this->penjaminId])->one();
               if($data) {
                  $billNo =  ArrayHelper::getValue($data, 'no_pembayaran');
               }
            }
         }
      }

      if(!$billNo) {
         $data = $pembayaranPelayanan->one();
         $billNo = ArrayHelper::getValue($data, 'no_pembayaran');
      }
      return $billNo;
   }

   protected function getNoBuktiBayar()
   {
      if(empty($this->invoice_id)) {
         return null;
      }

      $result = Yii::$app->db->createCommand("SELECT nobuktibayar FROM tandabuktibayar_t WHERE pembayaran_id = {$this->invoice_id}")->queryOne();
      return ArrayHelper::getValue($result, 'nobuktibayar', '-');
   }

   protected function setAttrPrint()
   {
      $dataRs = $this->dataRs;
      $dataPayer = $this->dataPayer;
      $pasienadmisi_id = $this->pasienadmisi_id;
      $header = $this->header;
      $pembayaran = $this->pembayaran;
      $detailPasien = $this->detailPasien();
      $noBed = $noKamar = '';
      $billNo = $this->getBillNo();

      $kabupatenNama = ArrayHelper::getValue($detailPasien, 'kabupaten_nama');
      $propinsiNama = ArrayHelper::getValue($detailPasien, 'propinsi_nama');
      $wargaNegara = ArrayHelper::getValue($detailPasien, 'warganegara');
      $tglPembayaran = ArrayHelper::getValue($header, 'tgl_pembayaran');
      $kelasPelayananNama = ArrayHelper::getValue($header, 'kelaspelayanan_nama');
      $tglPendaftaran = ArrayHelper::getValue($header, 'tgl_pendaftaran');
      $tglPasienPulang = ArrayHelper::getValue($header, 'tgl_pasienpulang');
      $tanggalLahir = ArrayHelper::getValue($header, 'tanggal_lahir');

      if(!$this->isObat) {
         $ruanganNama = ArrayHelper::getValue($header, 'r_pendaftaran');
         $dokterPendaftaran = ArrayHelper::getValue($header, 'dok_pendaftaran');

         if(!empty($pasienadmisi_id)) {
            $ruanganNama = ArrayHelper::getValue($header, 'r_ranap');
            $kelasPelayananNama = ArrayHelper::getValue($header, 'kelas_admisi');
            $kelasDitagihkanNama = ArrayHelper::getValue($header, 'kelas_ditagihkan_nama');
            $dokterPendaftaran = ArrayHelper::getValue($header, 'dok_ranap');
            $tglPasienPulang = ArrayHelper::getValue($header, 'tgl_stopakomodasi');
            $tglPendaftaran = ArrayHelper::getValue($header, 'tgl_admisi');
            $noKamar = ArrayHelper::getValue($header, 'kamarruangan_nokamar');
            $noBed = ArrayHelper::getValue($header, 'no_tempattidur');
            if(!empty($kelasDitagihkanNama)){
               $kelasPelayananNama = $kelasDitagihkanNama;
            }
   
            $historyPindahKamar = $this->getHistoryPindahKamar($this->id);
            if(!empty($historyPindahKamar)) {
               $kelasPelayananNama = ArrayHelper::getValue($historyPindahKamar, 'kelaspelayanan_nama');
               $kelasDitagihkanNama = ArrayHelper::getValue($historyPindahKamar, 'kelas_ditagihkan_nama_pk');
               $ruanganNama = ArrayHelper::getValue($historyPindahKamar, 'ruangan_nama');
               $ruanganPindah = ArrayHelper::getValue($historyPindahKamar, 'ruangan_pindah');
               if(!empty($kelasDitagihkanNama)) {
                  $kelasPelayananNama = $kelasDitagihkanNama;
               }
               if(!empty($ruanganPindah)) {
                  $ruanganNama = $ruanganPindah;
               }
            }
         }
      }
      else {
         $ruanganNama = ArrayHelper::getValue($header, 'ruangan_nama');
         $tanggalLahir = ArrayHelper::getValue($header, 'tgl_lahir');
         $dokterPendaftaran = ArrayHelper::getValue($header, 'dok_resep');
      }
      
      $namaKasir = ArrayHelper::getValue($pembayaran, 'kasir');
      return [
         '#printed_by#' => $this->nama_pegawai,
         '#printed_date#' => date('d/M/Y H:i'),
         '#no_pendaftaran#' => ArrayHelper::getValue($header, 'no_pendaftaran', '-'),
         '#visit_no#' => ArrayHelper::getValue($header, 'no_pendaftaran', '-'),
         '#no_rekam_medik#' => ArrayHelper::getValue($header, 'no_rekam_medik'),
         '#mr_no#' => ArrayHelper::getValue($header, 'no_rekam_medik'),
         '#nama_pasien#' => ArrayHelper::getValue($header, 'nama_pasien'),
         '#gender#' => ArrayHelper::getValue($header, 'jenis_kelamin'),
         '#age#' => !empty($tanggalLahir) ? $this->getUmur($tanggalLahir) : '-',
         '#bill_date#' => date('d/M/Y H:i', strtotime($tglPembayaran)),
         '#invoice_date#' => date('d/M/Y H:i', strtotime($tglPembayaran)),
         '#bill_no#' => $billNo,
         '#invoice_no#' => $billNo,
         '#no_resep#' => ArrayHelper::getValue($header, 'no_resep'),
         '#nobuktibayar#' => $this->getNoBuktiBayar(),
         '#bill_time#' => '',
         '#address#' => ArrayHelper::getValue($header, 'alamat'),
         '#address1#' => ArrayHelper::getValue($header, 'alamat'),
         '#address2#' => ArrayHelper::getValue($detailPasien, 'kelurahan_nama'),
         '#address3#' => ArrayHelper::getValue($detailPasien, 'kecamatan_nama'),
         '#address4#' => $kabupatenNama.' '.$propinsiNama.' '.$wargaNegara,
         '#ward#' => !empty($ruanganNama) ? $ruanganNama : '-',
         '#remarks#' => ArrayHelper::getValue($header, 'komponen', '-'),
         '#kelaspelayanan_nama#' => !empty($kelasPelayananNama) ? $kelasPelayananNama : '-',
         '#bed_type#' => !empty($noKamar) ? $noKamar : '-',
         '#bed_no#' => !empty($noBed) ? $noBed : '-',
         '#primary_doctor#' => !empty($dokterPendaftaran) ? $dokterPendaftaran : '-',
         '#admission#' => !empty($tglPendaftaran) ? date('d/M/Y H:i', strtotime($tglPendaftaran)) : '-',
         '#discharge_date#' => !empty($tglPasienPulang) ? date('d/M/Y H:i', strtotime($tglPasienPulang)) : '-',
         '#tgl_masuk#' => !empty($tglPendaftaran) ? date('d/M/Y H:i', strtotime($tglPendaftaran)) : '-',
         '#tgl_keluar#' => !empty($tglPasienPulang) ? date('d/M/Y H:i', strtotime($tglPasienPulang)) : '-',
         '#dokter#' => $dokterPendaftaran,
         '#payer#' => ($this->jenis_invoice != self::INVOICE_PASIEN) ? ArrayHelper::getValue($dataPayer, 'payer') : '',
         '#main_payer#' => ArrayHelper::getValue($header, 'penjamin_nama'),
         '#lokasi#' => ArrayHelper::getValue($dataRs, 'kota') . ', ' .date('d M Y', strtotime($tglPembayaran)),
         '#rs_name#' => ArrayHelper::getValue($dataRs, 'namaRs'),
         '#title#' => !empty($pasienadmisi_id) ? 'PERINCIAN BIAYA RAWAT INAP' : 'PERINCIAN BIAYA',
         '#label_tanggal#' => ($pasienadmisi_id) ? 'Tanggal Perawatan' : 'Tanggal Masuk/Keluar',
         '#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, $this->getRenderTable()),
         '#kasir#' => $namaKasir
      ];
   }

   protected function getRenderTable($shown = true)
   {
      $header = $this->header;
      $konfigSystem = $this->konfigSystem;
      $pembayaran = $this->pembayaran;
      $dataPayer = $this->dataPayer;
      $pasienadmisi_id = $this->pasienadmisi_id;

      $noBed = $noKamar = '';
      $tglPembayaran = ArrayHelper::getValue($header, 'tgl_pembayaran');
      $tglPasienPulang = ArrayHelper::getValue($header, 'tgl_pasienpulang');
      $tglPendaftaran = ArrayHelper::getValue($header, 'tgl_pendaftaran');
      $isPembulatanKeAtas = ArrayHelper::getValue($konfigSystem, 'is_pembulatankeatas', false);
      $satuanPembulatan = ArrayHelper::getValue($konfigSystem, 'satuanpembulatan', 0);
      $dataTagihan = $this->getDataTagihan($shown);
      $additionalPembayaran = ArrayHelper::getValue($pembayaran, 'additional_data', []);
      if(!empty($additionalPembayaran)) {
         $additionalPembayaran = json_decode($additionalPembayaran, true);
      }

      if(!empty($pasienadmisi_id)) {
         $tglPasienPulang = ArrayHelper::getValue($header, 'tgl_stopakomodasi');
         $tglPendaftaran = ArrayHelper::getValue($header, 'tgl_admisi');
         $noKamar = ArrayHelper::getValue($header, 'kamarruangan_nokamar');
         $noBed = ArrayHelper::getValue($header, 'no_tempattidur');
      }

      $listCaraBayar = $this->getListCaraBayar();
      $billNo = $this->getBillNo();
      return [
         'invoice_lengkap' => self::INVOICE_LENGKAP,
         'invoice_pasien' => self::INVOICE_PASIEN,
         'invoice_penjamin' => self::INVOICE_PENJAMIN,
         'tindakan_keperawatan' => $this->tindakan_keperawatan,
         'namaPasien' => ArrayHelper::getValue($header, 'nama_pasien'),
         'dataRoomRent' => $shown ? $this->dataRoomRent : [],
         'tgl_admisi' => $tglPendaftaran,
         'tgl_stopakomodasi' => $tglPasienPulang,
         'bed_type' => $noKamar,
         'bed_no' => $noBed,
         'tgl_pembayaran' => $tglPembayaran,
         'dataTindakan' => $this->dataTindakan,
         'isPayer' => ArrayHelper::getValue($dataPayer, 'isPayer', false),
         'listPayer' => ArrayHelper::getValue($dataPayer, 'listPayer', []),
         'listMetode' => ArrayHelper::getValue($dataPayer, 'listMetode', []),
         'jenis_invoice' => $this->jenis_invoice,
         'penjaminId' => $this->penjaminId,
         'totalTagihan' => ArrayHelper::getValue($dataTagihan, 'totalTagihan', 0),
         'dijamin' => ArrayHelper::getValue($dataTagihan, 'totalDijamin', 0),
         'totalDitagihkan' => ArrayHelper::getValue($dataTagihan, 'totalDitagihkan'),
         'biaya_admin' => $shown ? ArrayHelper::getValue($dataTagihan, 'dataBiayaAdmin', 0) : 0,
         'tindakan_admin' => ArrayHelper::getValue($dataTagihan, 'namaBiayaAdmin'),
         'dataDiskon' => ArrayHelper::getValue($dataTagihan, 'dataDiskon'),
         'diskonPayer' => ArrayHelper::getValue($dataTagihan, 'diskonPayer'),
         'diskonPasien' => ArrayHelper::getValue($dataTagihan, 'diskonPasien'),
         'uangMuka' => ArrayHelper::getValue($dataTagihan, 'uangMuka'),
         'discount' => ArrayHelper::getValue($dataTagihan, 'discount'),
         'totalTunai' => ArrayHelper::getValue($dataTagihan, 'totalTunai'),
         'totalNonTunai' => ArrayHelper::getValue($dataTagihan, 'totalNonTunai'),
         'totalKembalian' => ArrayHelper::getValue($dataTagihan, 'totalKembalian'),
         'sisaTagihan' => ArrayHelper::getValue($dataTagihan, 'sisaTagihan'),
         'pembulatan' => ArrayHelper::getValue($dataTagihan, 'pembulatan'),
         'patientAmount' => ArrayHelper::getValue($dataTagihan, 'patientAmount'),
         'totalPembulatan' => ArrayHelper::getValue($dataTagihan, 'totalPembulatan'),
         'totalPembulatanPayer' => ArrayHelper::getValue($dataTagihan, 'totalPembulatanPayer'),
         'totalPembulatanPasien' => ArrayHelper::getValue($dataTagihan, 'pembulatan'),
         'additionalPembayaran' => $additionalPembayaran,
         'pembayaran' => $this->pembayaran,
         'is_pembulatankeatas' => $isPembulatanKeAtas,
         'satuan_pembulatan' => $satuanPembulatan,
         'jenisTunai' => self::TUNAI,
         'jenisNonTunai' => self::NON_TUNAI,
         'jenisPenjamin' => self::PENJAMIN,
         'header' => $this->header,
         'pasienadmisi_id' => $pasienadmisi_id,
         'listCaraBayar' => $listCaraBayar,
         'billNo' => $billNo,
         'discDokterPasien' => ArrayHelper::getValue($dataTagihan, 'discDokterPasien'),
         'discDokterPayer' => ArrayHelper::getValue($dataTagihan, 'discDokterPayer'),
      ];
   }

   protected function getListCaraBayar($shown = true)
   {
      // kebutuhan RSU ADHYAKSA
      $dataTagihan = $this->getDataTagihan($shown);
      $listCaraBayar = [];
      $totalTunai = ArrayHelper::getValue($dataTagihan, 'totalTunai');
      if($totalTunai > 0){
         $listCaraBayar[] = [
            'metode_bayar' => self::TUNAI,
            'total_dibayar' => $totalTunai,
            'no_kartu' => '-',
            'label_edc' => self::TUNAI
         ];
      }

      if(!empty($additionalPembayaran)) {
         $jenisPembayaran = ArrayHelper::getValue($additionalPembayaran, 'pembayaran_jenis_pembayaran', []);
         $pembayaranPenjamin = ArrayHelper::getValue($additionalPembayaran, 'pembayaran_penjamin', []);
         if(!empty($jenisPembayaran)) {
            foreach ($jenisPembayaran as $value) {
               $totalDibayar = ArrayHelper::getValue($value, 'total_dibayar', 0);
               $metodeBayar = ArrayHelper::getValue($value, 'metode_bayar', '-');
               $noKartu = ArrayHelper::getValue($value, 'no_kartu', '-');
               $labelEdc = ArrayHelper::getValue($value, 'label_edc', '-');
               $listCaraBayar[]= [
                  'metode_bayar' => $metodeBayar,
                  'total_dibayar' => $totalDibayar,
                  'no_kartu' => $noKartu,
                  'label_edc' => $labelEdc
               ];
            }
         }
         if(!empty($pembayaranPenjamin)) {
            foreach ($pembayaranPenjamin as $value) {
               $totalDibayar = ArrayHelper::getValue($value, 'total_dijamin', 0);
               $metodeBayar = ArrayHelper::getValue($value, 'penjamin_nama', '-');
               $noKartu = ArrayHelper::getValue($value, 'no_kartu', '-');
               $labelEdc = ArrayHelper::getValue($value, 'label_edc', '-');
               $listCaraBayar[]= [
                  'metode_bayar' => $metodeBayar,
                  'total_dibayar' => $totalDibayar,
                  'no_kartu' => $noKartu,
                  'label_edc' => $labelEdc
               ];
            }
         }
      }

      return $listCaraBayar;
   }

   protected function getDataTagihan($shown)
   {
      $invoiceLengkap = self::INVOICE_LENGKAP;
      $invoicePasien = self::INVOICE_PASIEN;
      $invoicePenjamin = self::INVOICE_PENJAMIN;
      $defaultBiayaAdmin = self::DEFAULT_BIAYA_ADMIN;

      $pembayaran = $this->pembayaran;
      $dataPayer = $this->dataPayer;
      $jenisInvoice = $this->jenis_invoice;
      $dataDiskon = $this->getTotalDiskon();
      $dataBiayaAdmin = $this->getBiayaAdmin();

      $totalAdministrasi = ArrayHelper::getValue($pembayaran, 'total_administrasi', 0);
      $dataAdmin = [];
      if($totalAdministrasi > 0 ){
         $dataAdmin = $this->getDataAdmin();
      }

      $namaBiayaAdmin = ArrayHelper::getValue($dataAdmin, 'daftartindakan_nama', $defaultBiayaAdmin);
      $discount = ArrayHelper::getValue($pembayaran, 'discount', 0);
      $totalTagihan = ArrayHelper::getValue($pembayaran, 'total_tagihan', 0);
      $totalTunai = ArrayHelper::getValue($pembayaran, 'total_tunai', 0);
      $totalNonTunai = ArrayHelper::getValue($pembayaran, 'total_nontunai', 0);
      $totalKembalian = ArrayHelper::getValue($pembayaran, 'total_kembalian', 0);
      $totalPembulatan = ArrayHelper::getValue($pembayaran, 'total_pembulatan', 0);
      $pembulatan = ArrayHelper::getValue($pembayaran, 'pembulatan', 0);
      $totalDijamin = $jenisInvoice != $invoicePasien ? (float) ArrayHelper::getValue($pembayaran, 'total_dijamin', 0) : 0;
      $totalPembulatanPayer = ArrayHelper::getValue($dataPayer, 'totalPembulatan', 0);
      $totalDitagihkan = ArrayHelper::getValue($pembayaran, 'total_ditagihkan', 0);
      $uangMuka = ArrayHelper::getValue($pembayaran, 'penggunaan_uangmuka', 0);
      $sisaTagihan = ArrayHelper::getValue($pembayaran, 'total_sisatagihan', 0);
      $totalDibayar = ArrayHelper::getValue($pembayaran, 'total_dibayar', 0);
      $patientAmount = ($jenisInvoice != $invoicePenjamin) ? $totalDitagihkan : 0;

      $diskonPayer = ArrayHelper::getValue($dataDiskon, 'diskonPayer', 0);
      $diskonPasien = ArrayHelper::getValue($dataDiskon, 'diskonPasien', 0);
      $discDokterPasien = ArrayHelper::getValue($dataDiskon, 'discDokterPasien', 0);
      $discDokterPayer = ArrayHelper::getValue($dataDiskon, 'discDokterPayer', 0);

      if($jenisInvoice == $invoicePasien) {
         $totalPembulatan = $pembulatan;
         $diskonPayer = 0;
      }
      elseif($jenisInvoice == $invoicePenjamin) {
         $totalPembulatan = $totalPembulatanPayer;
         $diskonPasien = 0;
         $patientAmount = 0;
         $pembulatan = 0;
      }
      return [
         'totalDibayar' => $totalDibayar,
         'totalTagihan' => $totalTagihan,
         'totalDitagihkan' => $totalDitagihkan,
         'discount' => $discount,
         'totalTunai' => $totalTunai,
         'totalNonTunai' => $totalNonTunai,
         'totalKembalian' => $totalKembalian,
         'dataBiayaAdmin' => $dataBiayaAdmin,
         'totalDijamin' => $totalDijamin,
         'namaBiayaAdmin' => $namaBiayaAdmin,
         'dataDiskon' => $dataDiskon,
         'diskonPayer' => $diskonPayer,
         'diskonPasien' => $diskonPasien,
         'uangMuka' => $uangMuka,
         'sisaTagihan' => $sisaTagihan,
         'pembulatan' => $pembulatan,
         'patientAmount' => $patientAmount,
         'totalPembulatan' => $totalPembulatan,
         'totalPembulatanPayer' => $totalPembulatanPayer,
         'discDokterPasien' => $discDokterPasien,
         'discDokterPayer' => $discDokterPayer,
      ];
   }

   protected function cetakDetailInvoice()
   {
      ini_set('memory_limit', '256M');
      set_time_limit (60);
      $print = new DocoPrint($this->dokTercetak);
      $print->cacheTables = true;
      $print->simpleTables = true;
      $print->packTableData = true;
      $multiple = false;
      
      if ($this->pisahBill && !empty($this->groupBill) && !empty($this->pasienadmisi_id)) {
         $currPage = 1;
         $countGroup = count($this->groupBill);
         $multiple = true;
         foreach ($this->groupBill as $group => $billing) {
            $diTagihkan = preg_match("/\d-\d/",$group);
            $this->dataTindakan = $billing;
            $attributes = $this->setAttrPrint($diTagihkan);
            $print->attributes = $attributes;

            $break = ($currPage == $countGroup) ? false : true;
            $print->generateHtml($break);
            $currPage++;
         }
      } else {
         $attributes = $this->setAttrPrint();
         $print->shrink_tables_to_fit = 1;
         $print->attributes = $attributes;
      }

      $print->Output($multiple);
   }

   protected function getPayer()
   {
      $payer = '';
      $isPayer = false;
      $qPayer = $listMetode = $listPayer = [];
      $where = '';
      $totalDijamin = 0;
      $totalPembulatan = 0;
      $invoiceId = $this->invoice_id;
      if($this->jenis_invoice != self::INVOICE_LENGKAP) {
         if(!empty($this->penjaminId)) {
            $where = ' AND pembayaranpelayanan_t.penjamin_id = '.$this->penjaminId.'';
         }
      }

      $totalDijamin = ArrayHelper::getValue($this->pembayaran, 'total_dijamin', 0);
      $totalNonTunai = ArrayHelper::getValue($this->pembayaran, 'total_nontunai', 0);
      if ($totalDijamin > 0) {
         $isPayer = true;
         $qPayer = Yii::$app->db->createCommand("
            SELECT pembayaranpenjamin_t.penjamin_id, pembayaranpenjamin_t.penjamin_nama, 
            pembayaranpenjamin_t.total_dijamin AS dijamin,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpenjamin_t.total_dijamin + pembayaranpelayanan_t.pembulatan AS total_dijamin
            FROM pembayaranpenjamin_t
            JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaran_id = pembayaranpenjamin_t.pembayaran_id
            AND pembayaranpenjamin_t.penjamin_id = pembayaranpelayanan_t.penjamin_id
            WHERE pembayaranpenjamin_t.pembayaran_id = {$this->invoice_id} {$where}
         ")->queryAll();
         
         $listPayer = [];
         if (!empty($qPayer)) {
            foreach ($qPayer as $value) {
               $pembulatan = ArrayHelper::getValue($value, 'pembulatan', 0);
               $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');
               $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
               $totalDijamin = ArrayHelper::getValue($value, 'total_dijamin', 0);
               $totalPembulatan += $pembulatan;
               $penjaminNama = preg_replace("/^\w+ - /", '', $penjaminNama);
               $payer .= '<p>'.$penjaminNama.'</p>';
               $listPayer[$penjaminId] = [
                  'penjamin_nama' => $penjaminNama,
                  'total_dijamin' => $totalDijamin,
               ];
               $totalDijamin = $totalDijamin;
            }
         }
      }
      if ($totalNonTunai > 0) {
         $listMetode = Yii::$app->db->createCommand("
            SELECT 
            metode_bayar,
            total_dibayar,
            no_kartu
            FROM pembayaranmetode_t
            WHERE pembayaran_id = {$invoiceId}
         ")->queryAll();
      }
      return [
         'listPayer' => $listPayer,
         'payer' => $payer,
         'isPayer' => $isPayer,
         'listMetode' => $listMetode,
         'totalDijamin' => $totalDijamin,
         'totalPembulatan' => $totalPembulatan,
      ];
   }

   protected function getPembayaran()
   {
      if(empty($this->invoice_id)) {
         return [];
      }

      return Yii::$app->db->createCommand("
         SELECT
         pembayaran_t.created_date as tgl_pembayaran,
         pembayaran_t.no_pembayaran,
         pembayaran_t.total_tagihan,
         pembayaran_t.total_administrasi,
         (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) as discount,
         pembayaran_t.sisa_uangmuka as penggunaan_uangmuka,
         pembayaran_t.total_dijamin,
         pembayaran_t.total_sisatagihan,
         (pembayaran_t.total_ditagihkan - pembayaran_t.penggunaan_uangmuka) as total_ditagihkan,
         (pembayaran_t.total_sisatagihan + pembayaran_t.total_ditagihkan) as patient_amount,
         pembayaran_t.total_tunai,
         pembayaran_t.total_nontunai,
         pembayaran_t.total_kembalian,
         pembayaran_t.pembulatan,
         pembayaran_t.total_discountpembayaran,
         pembayaran_t.additional_data,
         pembayaran_t.total_pembulatan,
         pembayaran_t.no_invoicepasien,
         pembayaran_t.total_dibayar,
         pegawai_m.nama_pegawai AS kasir
         FROM pembayaran_t
         JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayaran_t.created_by
         JOIN pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
         WHERE pembayaran_t.pembayaran_id = {$this->invoice_id}
      ")->queryOne();
   }

   protected function getHeader()
   {
      $invoice_id = $this->invoice_id;
      $pasienadmisi_id = $this->model['pasienadmisi_id'] ? $this->model['pasienadmisi_id'] : null;
      $dataranap = $datarj = $result = [];
      if($this->isObat) {
         $result = Yii::$app->db->createCommand("
            SELECT
            pembayaran_id,no_pembayaran,tgl_pembayaran,no_pendaftaran,no_resep,ruangan_nama,
            no_rekam_medik,nama_pasien,tgl_lahir,jenis_kelamin,dok_resep,null AS alamat,
            penjamin_nama, kelaspelayanan_nama, tgl_pendaftaran, null as tgl_pasienpulang
            FROM
            invoiceobat_v 
            WHERE pembayaran_id = {$invoice_id}")
         ->queryOne();
      }
      else {
         if($pasienadmisi_id){
            $dataranap = Yii::$app->db->createCommand("
               SELECT
               pasienadmisi_id,tgl_admisi,admisi_dokter as dokter_admisi,
               ruangan_nama,kamarruangan_nokamar,no_tempattidur,
               tgl_stopakomodasi,kelaspelayanan_nama AS kelas_admisi,
               kelas_ditagihkan_nama,tagihan_rs,tgl_pulang
               FROM
               infopasienri_v 
               WHERE pasienadmisi_id = {$pasienadmisi_id}")
            ->queryOne();
         }

         $datarj = Yii::$app->db->createCommand("
            SELECT pasienrj.pembayaran_id,pasienrj.no_pendaftaran,pasienrj.no_rekam_medik,
            pasienrj.nama_pasien,pasienrj.alamat,pasienrj.tanggal_lahir,pasienrj.jenis_kelamin,
            pasienrj.tgl_pembayaran,pasienrj.no_pembayaran,pasienrj.dok_pendaftaran,
            pasienrj.dok_ranap,pasienrj.r_pendaftaran,pasienrj.r_ranap,pasienrj.carabayar_nama,
            pasienrj.penjamin_nama,pasienrj.tgl_pendaftaran,pasienrj.kelaspelayanan_nama,pasienrj.biaya_administrasi,
            pasienrj.adm_resep,pasienrj.tgl_pasienpulang,null as umur,null as pasienadmisi_id,
            null as tgl_admisi,null as dokter_admisi,null as ruangan_nama,null as kamarruangan_nokamar,
            null as no_tempattidur,null as tgl_stopakomodasi,null as kelas_admisi,null as tagihan_rs,
            null as tgl_pulang,pasienrj.total_dijamin 
            FROM
            invoicesudahbayar_v pasienrj
            WHERE pasienrj.pembayaran_id = {$invoice_id}")
         ->queryOne();
         
         if(!empty($datarj) || !empty($dataranap)) {
            $result = array_merge($datarj, $dataranap);
         }
      }

      return $result;
   }

   protected function getDetailInvoice()
   {
      $dataTindakan = [];
      $prevTindakanId = null;
      $constantID = new DocoConstansId;
      $kelompokTindakan = $constantID->actionGetAdditional('kelompok_tindakan');
      $arrKelompok = [];
      if(!empty($kelompokTindakan)) {
         $kelompokTindakan = json_decode($kelompokTindakan, true);
         foreach ($kelompokTindakan as $key => $value) {
            $arrKelompok[$value] = $value;
         }
      }

      $konfigTarif = $this->getKonfigTarif();
      $isInvoiceDiskon = ArrayHelper::getValue($konfigTarif, 'is_invoice_diskon', false);
      $isDiskonPasien = ArrayHelper::getValue($konfigTarif, 'is_diskon_pasien', false);
      $detail = $this->getDetailTindakan();
      $grandTotal = $totalDijamin = 0;
      foreach ($detail as $value) {
         $value['diskon_payer'] = $value['diskon_pasien'] = 0;
         $layananJenis = ArrayHelper::getValue($value, 'layanan_jenis');
         $isObat =  ArrayHelper::getValue($value, 'is_obat', false);
         if(!empty($layananJenis) && $layananJenis == self::LAYANAN_TINDAKAN) {
            $isObat = false;
         }
         $ruangan = ArrayHelper::getValue($value, 'ruangan');
         $kelompok = ArrayHelper::getValue($value, 'kelompok');
         $registid = ArrayHelper::getValue($value, 'pendaftaran_id');
         $admisiId = ArrayHelper::getValue($value, 'pasienadmisi_id');
         $isKonsultasi = ArrayHelper::getValue($value, 'is_konsultasi', false);
         $kelompokId = ArrayHelper::getValue($value, 'kelompoktindakan_id');
         $pelayananId = ArrayHelper::getValue($value, 'pelayanan_id');
         $tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', 0);
         $hargaSatuan = ArrayHelper::getValue($value, 'harga_satuan', 0);
         $tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', 0);
         $tarifDiskon = ArrayHelper::getValue($value, 'tarif_diskon', 0);
         $tarif = ArrayHelper::getValue($value, 'tarif', 0);
         $isDiskon = ArrayHelper::getValue($value, 'is_diskon', false);
         $grandTotal += $tarif;
         $totalDijamin += $tarifDijamin;

         if(!empty($ruangan)) {
            if($kelompokId) {
               $tindakanObat = ArrayHelper::getValue($value, 'tindakan_obat');
               $dokter = ArrayHelper::getValue($value, 'dokter');
               if($this->jenis_invoice == self::INVOICE_PENJAMIN) {
                  $value['tarif_dibayarkan'] = 0;
               }
               $str = !empty($dokter) ? ' ( '.$dokter.' )' : '';
               if(!$isObat && $isKonsultasi || isset($arrKelompok[$kelompokId])) {
                  if($this->additionalTindakan) {
                     $value['tindakan_obat'] = $tindakanObat.$str;
                  }
                  else {
                     $value['tindakan_obat'] = $tindakanObat;
                  }
               }
            }
            if ($this->pisahBill && !empty($this->pasienadmisi_id)) {
               $groupBill = "{$registid}-{$admisiId}";
               $dataTindakan[$groupBill][$ruangan][$kelompok][] = $value;
            } else {
               // if($prevTindakanId == $pelayananId){
               //    $value['tarif_dijamin'] = $hargaSatuan;
               //    if($hargaSatuan < 0){
               //       $value['tarif_dibayarkan'] = 0;
               //    }
               //    if($tarifDijamin <= 0){
               //       $value['tarif_dibayarkan'] = $hargaSatuan;
               //       $value['tarif_dijamin'] = 0;
               //    }
               // }
               
               if($isInvoiceDiskon) {
                  if($isDiskon) {
                     if($tarifDijamin > 0 && $tarifDibayarkan > 0) {
                        $value['tarif_dijamin'] = ($isDiskonPasien) ? 0 : $tarif;
                        $value['tarif_dibayarkan'] = ($isDiskonPasien) ? $tarif : 0;
                     }
                     else {
                        if($tarifDibayarkan == 0) {
                           $value['tarif_dijamin'] = $tarif;
                           $value['tarif_dibayarkan'] = 0;
                        }
                        elseif($tarifDijamin == 0) {
                           $value['tarif_dijamin'] = 0;
                           $value['tarif_dibayarkan'] = $tarif;
                        }
                     }
                  }
                  else {
                     if($tarifDijamin > 0 && $tarifDibayarkan > 0) {
                        $value['tarif_dibayarkan'] = ($isDiskonPasien) ? $tarifDibayarkan + $tarifDiskon : $tarifDibayarkan;
                        $value['tarif_dijamin'] = ($isDiskonPasien) ? $tarifDijamin : $tarifDijamin + $tarifDiskon;
                        $value['diskon_payer'] = ($isDiskonPasien) ? 0 : $tarifDiskon;
                        $value['diskon_pasien'] = ($isDiskonPasien) ? $tarifDiskon : 0;
                     }
                     else {
                        $value['tarif_dijamin'] = ($tarifDibayarkan == 0) ? $tarif : $tarifDijamin;
                        $value['tarif_dibayarkan'] = ($tarifDijamin == 0) ? $tarif : $tarifDibayarkan;
                        $value['diskon_payer'] = ($tarifDibayarkan == 0) ? $tarifDiskon : 0;
                        $value['diskon_pasien'] = ($tarifDijamin == 0) ? $tarifDiskon : 0;
                     }
                  }
               }
               $dataTindakan[$ruangan][$kelompok][] = $value;
            }
         }
         $prevTindakanId = ArrayHelper::getValue($value, 'pelayanan_id');
      }
      $this->grandTotal = $grandTotal;
      $this->totalDijamin = $totalDijamin;
      
      if ($this->pisahBill && !empty($this->pasienadmisi_id)) {
         $this->groupBill = $dataTindakan;
      } else {
         $this->dataTindakan = $dataTindakan;
      }
   }

   protected function getDataRoomRent()
   {
      $dataRoomRent = [];
      $prevTindakanObatId = null;
      $whereClause = '';
      $withPenjaminId = '';
      if(!empty($this->penjaminId)) {
         $withPenjaminId = ' AND penjamin_pelayanan_id = '.$this->penjaminId.'';
      }
      if($this->jenis_invoice == self::INVOICE_PENJAMIN) {
         $whereClause = 'AND tarif_dijamin != 0 '.$withPenjaminId.' ';
      }
      elseif($this->jenis_invoice == self::INVOICE_PASIEN) {
         $whereClause = 'AND tarif_dibayarkan != 0';
      }

      $instalasiRanap = DocoConstants::INST_ID_RI;
      $roomrent = Yii::$app->db->createCommand("
         SELECT 
            tgl_pelayanan,
            kelompok,
            ruangan,
            dokter,
            tarif as total_amount,
            harga_satuan,
            tarif_dijamin,
            tarif_dibayarkan,
            tindakan_obat,
            tindakan_obat_id,
            tindakan_obat_kode,
            kamar,
            kelas,
            no_bed,
            is_diskon,
            (((additional_data::json)->>'detail_akomodasi')::json)->>'tanggal' as min,
            (((additional_data::json)->>'detail_akomodasi')::json)->>'tanggal' as max,
            qty,
            COALESCE (tarif_diskon,0) AS tarif_diskon
         FROM invoiceridetail_v 
         WHERE pembayaran_id = {$this->invoice_id}
         AND is_akomodasi = true AND instalasi_id = {$instalasiRanap} {$whereClause}
         ORDER BY tgl_pelayanan ASC
      ")->queryAll();
      
      $konfigTarif = $this->getKonfigTarif();
      $isInvoiceDiskon = ArrayHelper::getValue($konfigTarif, 'is_invoice_diskon', false);
      $isDiskonPasien = ArrayHelper::getValue($konfigTarif, 'is_diskon_pasien', false);
      if(!empty($roomrent)) {
         foreach ($roomrent as $key => $value) {
            $value['diskon_payer'] = $value['diskon_pasien'] = 0;
            $ruangan = ArrayHelper::getValue($value, 'ruangan');
            $kelompok_tindakan = ArrayHelper::getValue($value, 'kelompok');
            $tindakanObatId = ArrayHelper::getValue($value, 'tindakan_obat_id');
            $hargaSatuan = ArrayHelper::getValue($value, 'harga_satuan', 0);
            $tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', 0);
            $tarifDiskon = ArrayHelper::getValue($value, 'tarif_diskon', 0);
            $tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', 0);
            $subTotal = ArrayHelper::getValue($value, 'total_amount', 0);
            $isDiskon = ArrayHelper::getValue($value, 'is_diskon', false);

            if($prevTindakanObatId == $tindakanObatId){
               $value['tarif_dijamin'] = $hargaSatuan;
               if($hargaSatuan < 0){
                  $value['tarif_dibayarkan'] = 0;
               }
               if($tarifDijamin <= 0){
                  $value['tarif_dibayarkan'] = $hargaSatuan;
                  $value['tarif_dijamin'] = 0;
               }
            }

            if($isInvoiceDiskon) {
               if($isDiskon) {
                  if($tarifDijamin > 0 && $tarifDibayarkan > 0) {
                     $value['tarif_dijamin'] = ($isDiskonPasien) ? 0 : $subTotal;
                     $value['tarif_dibayarkan'] = ($isDiskonPasien) ? $subTotal : 0;
                  }
                  else {
                     if($tarifDibayarkan == 0) {
                        $value['tarif_dijamin'] = $subTotal;
                        $value['tarif_dibayarkan'] = 0;
                     }
                     elseif($tarifDijamin == 0) {
                        $value['tarif_dijamin'] = 0;
                        $value['tarif_dibayarkan'] = $subTotal;
                     }
                  }
               }
               else {
                  if($tarifDijamin > 0 && $tarifDibayarkan > 0) {
                     $tarifDiskon = ($isDiskonPasien) ? $tarifDiskon : 0;
                     $value['tarif_dibayarkan'] = ($isDiskonPasien) ? $tarifDibayarkan + $tarifDiskon : $tarifDibayarkan;
                     $value['tarif_dijamin'] = ($isDiskonPasien) ? $tarifDijamin : $tarifDijamin + $tarifDiskon;
                     $value['diskon_payer'] = ($isDiskonPasien) ? 0 : $tarifDiskon;
                     $value['diskon_pasien'] = ($isDiskonPasien) ? $tarifDiskon : 0;
                  }
                  else {
                     $value['tarif_dijamin'] = ($tarifDibayarkan == 0) ? $subTotal : $tarifDijamin;
                     $value['tarif_dibayarkan'] = ($tarifDijamin == 0) ? $subTotal : $tarifDibayarkan;
                     $value['diskon_payer'] = ($tarifDibayarkan == 0) ? $tarifDiskon : 0;
                     $value['diskon_pasien'] = ($tarifDijamin == 0) ? $tarifDiskon : 0;
                  }
               }
            }
            $prevTindakanObatId = $tindakanObatId;
            $dataRoomRent[$ruangan][$kelompok_tindakan][] = $value;
         }
      }
      return $dataRoomRent;
   }

   protected function getKonfigSistem()
   {
      return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
         return KonfigSystem::find()->asArray()->one();
      });
   }
   
   protected function getKonfigTarif()
   {
      return KonfigTarif::find()->asArray()->one();
   }

   protected function getUmur($tgl_lahir, $yearOnly = false)
   {
      $umur = '-';
      if (!empty($tgl_lahir)) {
         if($yearOnly) {
            $umur = DocoHelpers::getUmur($tgl_lahir, true, false).' Years';
         }
         else {
            $umur = DocoHelpers::getUmur($tgl_lahir);
            $umur = str_replace("tahun", "Year(s)", $umur);
            $umur = str_replace("bulan", "Month(s)", $umur);
            $umur = str_replace("hari", "Day(s)", $umur);
         }
      }

      return $umur;
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

   protected function getPendaftaran()
   {
      if(empty($this->invoice_id)) {
         return [];
      }

      $invoice_id = $this->invoice_id;
      $sql = "SELECT pendaftaran_t.pendaftaran_id, 
               pendaftaran_t.instalasi_id, 
               pembayaran_t.pasienadmisi_id,
               pendaftaran_t.umur 
         FROM pendaftaran_t 
         JOIN pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
         WHERE pembayaran_t.pembayaran_id = {$invoice_id}";

      return Yii::$app->db->createCommand($sql)->queryOne();
   }

   protected function getDataAdmin()
   {
        $confSistem = Cache::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $model = new DaftarTindakan;
        $query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();

        return $query;
    }

    protected function getTotalDiskon()
    {
      // $diskonAdmin = [];
      $tmpDiskon = 0;
      $db = Yii::$app->db;
      $pembayaran = $this->pembayaran;
      $additionalData = ArrayHelper::getValue($pembayaran, 'additional_data', []);
      $additionalData = json_decode($additionalData, true);
      $pembayaranPenjamin = ArrayHelper::getValue($additionalData, 'pembayaran_penjamin', []);
      $totalDiscountAdm = ArrayHelper::getValue($pembayaran, 'total_discountadm', 0);
      $totalDiscountPembayaran = ArrayHelper::getValue($pembayaran, 'total_discountpembayaran', 0);
      $konfigTarif = $this->getKonfigTarif();
      $isInvoiceDiskon = ArrayHelper::getValue($konfigTarif, 'is_invoice_diskon', false);
      $isDiskonPasien = ArrayHelper::getValue($konfigTarif, 'is_diskon_pasien', false);

      $query = "SELECT sum(tarif_diskon) AS total_diskon 
         FROM invoicesudahbayardetail_v 
         WHERE is_diskon = TRUE AND pembayaran_id = $this->invoice_id ";

      if($isInvoiceDiskon){
         $query .= " AND sub_total < 0 ";
      }

      if($this->jenis_invoice == self::INVOICE_PENJAMIN) {
         if(!empty($this->penjaminId)) {
            $diskonPayer = $db
            ->createCommand("{$query} AND tarif_dijamin > 0 AND penjamin_pelayanan_id = {$this->penjaminId} ")
            ->queryScalar();
         }
      }

      $totalDiskonTotal = $db
         ->createCommand("{$query}")
         ->queryScalar();
      
      $diskonPayer = $diskonPasien = 0;
      if(!$isDiskonPasien) {
         $diskonPasien = 0;
         $diskonPayer = $totalDiskonTotal;
         // $diskonPayer = 0;
      }
      else {
         $diskonPayer = 0;
         $diskonPasien = $totalDiskonTotal;
         // $diskonPasien = 0;
      }
      
      $listDiskonPayer = [];
      if($totalDiscountAdm == 0){
         if (is_array($pembayaranPenjamin) || is_object($pembayaranPenjamin)){
            foreach ($pembayaranPenjamin as $key => $value) {
               $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');
               $discountAdmPenjamin = ArrayHelper::getValue($value, 'discount_adm_penjamin', 0);
               $listDiskonPayer[$penjaminId] = $value;
               $tmpDiskon += $discountAdmPenjamin;
            }
            if(!empty($this->penjaminId)) {
               if(isset($listDiskonPayer[$this->penjaminId])) {
                  $discountAdmPenjamin = isset($listDiskonPayer[$this->penjaminId]['discount_adm_penjamin']) ? $listDiskonPayer[$this->penjaminId]['discount_adm_penjamin'] : 0;
                  $diskonPayer += $discountAdmPenjamin;
               }
            }
            else {
               $diskonPayer += $tmpDiskon;
            }
         }
      }        
      
      //get data diskon dokter
      $diskonDokter = ArrayHelper::getValue($additionalData, 'pembayaran_diskon', []);
      $totalDiskonDokter = $discDokterPayer = $discDokterPasien = 0;
      if(!empty($diskonDokter)) {
         foreach ($diskonDokter as $key => $value) {
            $totalDiscount = ArrayHelper::getValue($value, 'total_diskon', 0);
            $totalDiskonDokter += $totalDiscount;
         }
         $pendaftaran = Yii::$app->db->createCommand("
            SELECT pasienadmisi_t.carabayar_id AS carabayar_ranap, pendaftaran_t.carabayar_id, carabayar_m.groupcarabayar_id, carabayar_ranap.groupcarabayar_id AS groupcarabayar_ranap
            FROM pembayaran_t 
            JOIN pendaftaran_t on pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
            LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
            LEFT JOIN carabayar_m carabayar_ranap ON pasienadmisi_t.carabayar_id = carabayar_ranap.carabayar_id
            WHERE pembayaran_t.pembayaran_id = {$this->invoice_id}
         ")->queryOne();

         $groupCaraBayarId = ArrayHelper::getValue($pendaftaran, 'groupcarabayar_id');
         $groupCaraBayarRanap = ArrayHelper::getValue($pendaftaran, 'groupcarabayar_ranap');
         if(!empty($groupCaraBayarRanap)) {
            $groupCaraBayarId = $groupCaraBayarRanap;
         }
         
         if($groupCaraBayarId == DocoConstants::GROUP_UMUM) {
            $diskonPasien += $totalDiskonDokter;
            $discDokterPasien = $totalDiskonDokter;
         }
         else {
            $diskonPayer += $totalDiskonDokter;
            $discDokterPayer = $totalDiskonDokter;
         }
      }
      return [
         'diskonPayer' => round($diskonPayer),
         'diskonPasien' => round($diskonPasien),
         'discDokterPasien' => $discDokterPasien,
         'discDokterPayer' => $discDokterPayer,
      ];
   }

   protected function getBiayaAdmin()
   {
      $pembayaran = $this->pembayaran;
      $additionalData = ArrayHelper::getValue($pembayaran, 'additional_data', []);
      if(!empty($additionalData)) {
         $additionalData = json_decode($additionalData, true);
      }

      $penjaminId = null;
      $biayaAdmin = $nominPayer = $nominPatient = 0;
      $admAsuransi = ArrayHelper::getValue($additionalData, 'adm_asuransi', []);
      if(!empty($admAsuransi)) {
         $dijaminAsuransi = ArrayHelper::getValue($admAsuransi, 'dijamin', 0);
         $dijaminPasien = ArrayHelper::getValue($admAsuransi, 'harusbayar', 0);
         $diskonAdmin = ArrayHelper::getValue($admAsuransi, 'nominal_diskon', 0);
         if($this->jenis_invoice == self::INVOICE_PENJAMIN && !empty($this->penjaminId)) {
            $penjaminId = $this->penjaminId;
            $defaultPenjamin = ArrayHelper::getValue($admAsuransi, 'defaultPenjamin', []);
            $defaultPenjaminId = ArrayHelper::getValue($defaultPenjamin, 'id');
            if($defaultPenjaminId == $penjaminId) {
               $penjaminId = $defaultPenjaminId;
               if($dijaminAsuransi > 0){
                  $biayaAdmin = $dijaminAsuransi;
                  $nominPayer = $biayaAdmin;
               }                                  
            }
         }
         elseif($this->jenis_invoice == self::INVOICE_LENGKAP) {
            $penjaminId = null;
            if($dijaminAsuransi > 0 && $dijaminPasien > 0){
               $biayaAdmin = $dijaminAsuransi + $dijaminPasien;
               $nominPayer = $dijaminAsuransi;
               $nominPatient = $dijaminPasien;
            }
            if($dijaminAsuransi > 0 && $dijaminPasien == 0){
               $biayaAdmin = $dijaminAsuransi;
               $nominPayer = $biayaAdmin;
            }                  
            if($dijaminAsuransi == 0 && $dijaminPasien > 0){
               $biayaAdmin = $dijaminPasien;
               $nominPatient = $biayaAdmin;
            }               
         }
         else {
            $penjaminId = null;
            if($dijaminPasien > 0){
               $biayaAdmin = $dijaminPasien;
               $nominPatient = $biayaAdmin;
            }
         }
         if($biayaAdmin > 0) {
            $biayaAdmin += $diskonAdmin;
         }
      }

      return [
         'biaya_admin' => $biayaAdmin,
         'penjamin_id' => $penjaminId,
         'nominPayer' => $nominPayer,
         'nominPatient' => $nominPatient,
      ];
   }

   protected function getDataPegawai($pegawaiId)
   {
      if(empty($pegawaiId)) {
         return [];
      }

      $result = Pegawai::findOne($pegawaiId);
      return ArrayHelper::getValue($result, 'nama_pegawai');
   }

   protected function getHistoryPindahKamar($pendaftaran_id)
   {
      if ( is_null($pendaftaran_id) || empty($pendaftaran_id) ) {
         return [];
      }
      return Yii::$app->db->createCommand("
         SELECT * FROM infopindahkamar_v WHERE pendaftaran_id = :pendaftaran_id 
         ORDER BY pindahkamar_id DESC LIMIT 1
      ")->bindValues([
         ':pendaftaran_id' => $pendaftaran_id
      ])->queryOne();
   }

   protected function processFlow()
   {
      $request = $this->_requestData;
      $type = $request->get('invoice_type', 'invoice');
      $outputAttributes = $request->get('outputAttributes', false);
      if ($type == 'tmp') {
         $pathPenjamin = new InvoicePayerBelumBayar;
         $pathPenjamin->dokPath = 'invoice-penjamin-mhbg';
         return $pathPenjamin->execute();
      } else if ($type == 'invoice') {
         if($outputAttributes) {
            $this->populateData();
            $this->getDetailInvoice();
            // return $this->setAttrPrint();
            return [
               'attributes' => $this->setAttrPrint(),
               'kode_doc' => $this->dokTercetak,
            ];
         }
         else {
            $this->populateData();
            $this->getDetailInvoice();
            $this->cetakDetailInvoice();
         }
      }
   }
}
