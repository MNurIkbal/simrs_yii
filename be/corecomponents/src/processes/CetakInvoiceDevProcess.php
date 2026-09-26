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
use Doco\models\kasir\InvoiceObatView;
use Doco\models\kasir\InvoiceObatDetailView;
use Doco\models\ProfilRsView;
use Doco\models\Pendaftaran;
use Doco\models\KonfigSystem;

class CetakInvoiceDevProcess extends \Doco\components\DocoBaseProcessExtension
{

  /**
   * @var object
   */
  protected $pembayaranPelayananId;

  /**
   * @var object
   */
  protected $invoiceId;

  /**
   * @var object
   */
  protected $jenisInvoice;

  /**
   * @var object
   */
  protected $namaPegawai;

  /**
   * @var object
   */
  protected $penjualanResepId;

  /**
   * @var object
   */
  protected $kelompok;

  /**
   * @var object
   */
  protected $pendaftaranId;

  /**
   * @var [array]
   */
  protected $cekPembayaran;

  /**
   * @var [array]
   */
  protected $dataInvoice;

  /**
   * @var [array]
   */
  protected $dataPayer;

  /**
   * @var [array]
   */
  protected $profilRs;

  /**
   * @var [array]
   */
  protected $umur;

  /**
   * @var [array]
   */
  protected $namaPasien;

  /**
   * @var array
   */
  protected $dataRegist;

  /**
   * @var array
   */
  protected $konfigSystem;

  /**
   * @return void
   * @throws Doco\exceptions\ValidationException
   */
  protected function populateData()
  {
      $pembayaranPelayananId = $this->_requestData->get('id', null);
      $invoiceId = $this->_requestData->get('invoice_id', null);
      $jenisInvoice = $this->_requestData->get('jenis_invoice', 1);
      $namaPegawai = $this->_requestData->get('nama_pegawai', null);
      $cekPembayaran = $this->cekDataPembayaran($pembayaranPelayananId, $invoiceId);
      $namaPasien = isset($cekPembayaran['nama_pasien']) ? $cekPembayaran['nama_pasien'] : '-';
      $penjualanResepId = $cekPembayaran['penjualanresep_id'];
      $kelompok = ($penjualanResepId) ? DocoConstants::PASIEN_ALKES : '';
      $pendaftaranId = ($cekPembayaran) ? $cekPembayaran['pendaftaran_id'] : null;
      $dataInvoice = $this->getDetailInvoice($invoiceId, $jenisInvoice);
      $umur = isset($dataInvoice['header']) ? $this->getUmur($dataInvoice['header']['tanggal_lahir']) : '-';

      $this->pembayaranPelayananId = $pembayaranPelayananId;
      $this->invoiceId = $invoiceId;
      $this->jenisInvoice = $jenisInvoice;
      $this->namaPegawai = $namaPegawai;
      $this->cekPembayaran = $cekPembayaran;
      $this->penjualanResepId = $penjualanResepId;
      $this->kelompok = $kelompok;
      $this->pendaftaranId = $pendaftaranId;
      $this->dataInvoice = $dataInvoice;
      $this->dataRegist = $this->getDataPendaftaran($invoiceId);
      $this->dataPayer = $this->getListPayer($pendaftaranId,$invoiceId);
      $this->profilRs = $this->getProfileRs();
      $this->umur = $umur;
      $this->namaPasien = $namaPasien;
      $this->konfigSystem = $this->getKonfigSistem();
  }

  protected function generateCetakan()
  {
    if (is_null($this->kelompok) || $this->kelompok != DocoConstants::PASIEN_ALKES) {
      $model = $this->dataRegist;
      if (empty($model)) {
          throw new ValidationException(422, $this->_error, [
              'text' => "Pendaftaran tidak ditemukan."
          ]);
      }
      $params = [
        'header' => $this->dataInvoice['header'],
        'detail' => $this->dataInvoice['detail'],
        'data' => $this->dataInvoice['data'],
        'pembayaran' => $this->dataPayer['pembayaran'],
        'instalasi_id' => $model['instalasi_id'],
      ];

      return $this->cetakInvoice($params);
    }
    else {
      if ($this->kelompok == DocoConstants::PASIEN_ALKES) {
          $this->cetakInvoiceObat($this->pendaftaranId, $this->invoiceId);
      }
    }
  }

  private function getTotalPembayaran($invoice_id)
  {
      $db = Yii::$app->db;
      $pembayaran = $db->createCommand("
          SELECT
              pelayanan.tgl_pembayaran,
              pelayanan.no_pembayaran,
              (invoice.total_tagihan + invoice.total_pembulatan) as bill_amount,
              invoice.total_administrasi,
              (invoice.total_discount + invoice.total_discountpembayaran) as discount,
              invoice.sisa_uangmuka as penggunaan_uangmuka,
              invoice.total_dijamin,
              invoice.total_sisatagihan,
              (invoice.total_sisatagihan + invoice.total_ditagihkan) as patient_amount,
              invoice.total_tunai,
              invoice.total_nontunai,
              invoice.total_kembalian
          FROM pembayaran_t invoice
          JOIN pembayaranpelayanan_t pelayanan ON invoice.pembayaran_id = pelayanan.pembayaran_id
          WHERE invoice.pembayaran_id = {$invoice_id}
      ")->queryOne();
      
      $listPayer = $listMetode = [];
      if (!empty($pembayaran['total_dijamin'])) {
          $listPayer = $db->createCommand("
              SELECT 
                  penjamin_nama,
                  total_dijamin
              FROM pembayaranpenjamin_t
              WHERE pembayaran_id = {$invoice_id}
          ")->queryAll();
      }

      if (!empty($pembayaran['total_nontunai'])) {
          $listMetode = $db->createCommand("
              SELECT 
                  metode_bayar,
                  total_dibayar,
                  no_kartu
              FROM pembayaranmetode_t
              WHERE pembayaran_id = {$invoice_id}
          ")->queryAll();
      }

      return [
          'pembayaran' => $pembayaran,
          'listPayer' => $listPayer,
          'listMetode' => $listMetode,
      ];
  }

  protected function cetakInvoiceObat($id, $invoice_id)
  {
      $request = Yii::$app->request;
      $nama_pegawai = $request->get('nama_pegawai', null);
      $header = InvoiceObatView::find()
          ->where(['pembayaran_id' => $invoice_id])
          ->one();
      
      $detail = InvoiceObatDetailView::find()
          ->where(['pembayaran_id' => $invoice_id])
          ->orderBy(['obatalkes_nama' => SORT_ASC])
          ->all();
      
      $dataPembayaran = $this->getTotalPembayaran($invoice_id);
      $pembayaran = $dataPembayaran['pembayaran'];
      $listPayer = $dataPembayaran['listPayer'];
      $listMetode = $dataPembayaran['listMetode'];
      $profilRs = $this->getProfileRs();
      $namaRs = $profilRs['namaRs'];
      $kota = $profilRs['kota'];
      $umur = $this->getUmur($header['tgl_lahir'], true);
      $billDate = !empty($header['tgl_pembayaran']) ? date('d/M/Y H:i', strtotime($header['tgl_pembayaran'])) : null;
      
      $print = new DocoPrint('invoice-obat-alkes');
      $print->attributes = [
          '#printed_by#' => $nama_pegawai,
          '#printed_date#' => date('d/m/Y g:i A'),
          '#no_resep#' => $header['no_resep'],
          '#no_rekam_medik#' => !empty($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-',
          '#nama_pasien#' => DocoHelpers::namaPasien($header['nama_pasien']),
          '#gender#' => !empty($header['jenis_kelamin']) ? $header['jenis_kelamin'] : '-',
          '#age#' => !empty($umur) ? $umur : '-',
          '#bill_no#' => $header['no_pembayaran'],
          '#doctor_name#' => !empty($header['dok_resep']) ? DocoHelpers::namaPasien($header['dok_resep']) : '-',
          '#doctor_speciality#' => !empty($header['ruangan_nama']) ? $header['ruangan_nama'] : '-',
          '#bill_date#' => $billDate,
          '#lokasi#' => $kota. ', ' .date('d-M-Y'),
          '#remarks#' => $header['komponen'],
          '#rs_name#' => !empty($namaRs) ? $namaRs : '-',
          '#datatable#' => Yii::$app->controller->renderPartial('invoice_obat_dev', [
              'nama_pasien' => $header['nama_pasien'],
              'total_diskon' => empty($header['total_diskon']) ? 0 : $header['total_diskon'],
              'pembayaran' => $pembayaran,
              'listPayer' => $listPayer,
              'listMetode' => $listMetode,
              'detail' => $detail,
          ]),
      ];
      $print->Output();
  }

  protected function getDataPendaftaran($invoiceId)
  {
      return  Yii::$app->db->createCommand("
          SELECT 
            a.instalasi_id,
            b.pasienadmisi_id
          FROM 
            pendaftaran_t a
          JOIN pembayaran_t b ON b.pendaftaran_id = a.pendaftaran_id
          WHERE b.pembayaran_id = {$invoiceId}
      ")->queryOne();
  }

  protected function cetakInvoice($params)
  {
    $header = $params['header'];
    $detail = $params['detail'];
    $data = $params['data'];
    $pembayaran = $params['pembayaran'];
    $instalasi_id = $params['instalasi_id'];
    $kode = $this->getKodeCetakan($instalasi_id);
    $paramsCetakan = $this->getAttrCetakan($instalasi_id);
    $print = new DocoPrint($kode);
    $print->attributes = $paramsCetakan;
    $print->Output();
  }

  protected function cekDataPembayaran($pembayaranPelayananId, $invoiceId)
  {
      return InfoPasienSudahBayarView::find()
          ->where([
              'pembayaran_id' => $invoiceId
          ])
          ->one();
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

      $kota = str_replace($pattern,"", $profilRs['kota']);
    }
      
    return [
      'namaRs' => $namaRs,
      'kota' => $kota,
    ];
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

  protected function getDetailInvoice($invoiceId, $jenisInvoice)
  {
    $data = $dataDokter = [];
    $arrDokter = $where = '';
    $total = 0;
    $header = InvoiceSudahBayarView::find()
        ->where(['pembayaran_id' => $invoiceId])
        ->andWhere(['IS NOT', 'pembayaranpelayanan_id', NULL])
        ->orderBy('tandabuktibayar_id DESC')
        ->one();

    $instalasi_id = $header['instalasi_id'];
    if($instalasi_id == DocoConstants::INST_ID_RI) {
      // if($jenisInvoice != DocoConstants::INV_TOTAL) {
      //     $where = ($jenisInvoice == DocoConstants::INV_PENJAMIN) ? 
      //     ' AND tarif_dijamin > 0' : ' AND tarif_dibayarkan > 0';
      // }
       
      $db = Yii::$app->db;
      $detail = $db->createCommand("
          SELECT 
              kelompoktindakan_nama, 
              SUM(sub_total) as sub_total,
              SUM(tarif_dijamin) as tarif_dijamin, 
              SUM(tarif_dibayarkan) as tarif_dibayarkan
          FROM invoicesudahbayardetail_v 
          WHERE pembayaran_id = {$invoiceId}
          {$where}
          GROUP BY kelompoktindakan_nama
      ")->queryAll();
    }
    else {
      $queryDetail = InvoiceSudahBayarDetailView::find()
          ->select(['tindakan_obat_nama', 'qty', 'tarif_satuan', 'sub_total', 
              'tarif_dijamin', 'tarif_dibayarkan', 'kelompoktindakan_nama', 
              'dokter_tindakan'])
          ->where(['pembayaran_id' => $invoiceId])
          ->andWhere(['IS NOT', 'pembayaranpelayanan_id', NULL]);

      $detail = $queryDetail->orderBy('tandabuktibayar_id DESC')->all();
    }
    
    if(!empty($detail) && $instalasi_id != DocoConstants::INST_ID_RI) {
      foreach ($detail as $key => $value) {
          $kelompok_tindakan = $value['kelompoktindakan_nama'];
          $data[$kelompok_tindakan][] = $value;
          $total = $total + $value['tarif_satuan'];
          $dataDokter[] = isset($value['dokter_tindakan']) ? $value['dokter_tindakan'] : null;
      }
    }

    if(!empty($dataDokter)) {
      $arrDokter = "" . implode(", ", $dataDokter) . "";
    }
    
    return [
      'header' => $header,
      'detail' => $detail,
      'data' => $data,
      'arrDokter' => $arrDokter,
      'total' => $total
    ];
  }

  protected function getListPayer($pendaftaranId,$invoiceId,$detailInvoice = false)
  {
      $payer = '';
      $isPayer = false;
      $listMetode = $listPayer = [];
      $pembayaran = $this->getSummaryPembayaran($pendaftaranId, $invoiceId, $detailInvoice);
      $total_dijamin = $pembayaran['total_dijamin'];
      $total_nontunai = $pembayaran['total_nontunai'];
      $db = Yii::$app->db;
      if(!empty($total_dijamin)) {
          $isPayer = true;
          $listPayer = PembayaranPenjamin::find()
              ->select(['penjamin_nama', 'total_dijamin'])
              ->where(['pembayaran_id' => $invoiceId])
              ->all();
      }

      if(!empty($total_nontunai)) {
          $listMetode = $db->createCommand("
              SELECT 
                  metode_bayar,
                  total_dibayar,
                  no_kartu
              FROM pembayaranmetode_t
              WHERE pembayaran_id = {$invoiceId}
          ")->queryAll();
      }

      return [
          'payer' => $payer,
          'isPayer' => $isPayer,
          'listPayer' => $listPayer,
          'listMetode' => $listMetode,
          'pembayaran' => $pembayaran,
      ];
  }

  protected function getSummaryPembayaran($pendaftaranId, $invoiceId, $detailInvoice = false)
  {
      $model = $this->dataRegist;
      $pasienadmisi_id = $model['pasienadmisi_id'];
      $instalasi_id = $model['instalasi_id'];
      $additionalSelect = '';

      if(!empty($pasienadmisi_id) || $instalasi_id == DocoConstants::INST_ID_RD) {
          $additionalSelect = ['(pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) - pembayaran_t.penggunaan_uangmuka - pembayaran_t.total_sisatagihan) as total_ditagihkan'];
      }
      
      if($detailInvoice) {
          $additionalSelect = ['pembayaran_t.total_tagihan','pembayaran_t.total_ditagihkan'];
      }
      else {
          $additionalSelect = ['(pembayaran_t.total_tagihan + pembayaran_t.total_pembulatan) as total_tagihan'];
      }
      
      $select = [
          'pembayaran_t.pembayaran_id',
          'pembayaranpelayanan_t.tgl_pembayaran', 
          'pembayaranpelayanan_t.no_pembayaran',
          'pembayaran_t.total_administrasi',
          'pembayaran_t.sisa_uangmuka as penggunaan_uangmuka',
          'pembayaran_t.total_dijamin', 'pembayaran_t.total_sisatagihan',
          'pembayaran_t.total_tunai', 'pembayaran_t.total_nontunai', 
          'pembayaran_t.total_kembalian', 'pembayaran_t.total_pembulatan', 
          '(pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran) as discount',
          '(pembayaran_t.total_sisatagihan + pembayaran_t.total_ditagihkan) as patient_amount',  
          'pembayaran_t.catatan'
      ];

      $select = array_merge($select, $additionalSelect);
      return Pembayaran::find()
          ->joinWith('pembayaranPelayanan')
          ->select($select)
          ->where(['pembayaran_t.pembayaran_id' => $invoiceId])
          ->asArray()->one();
  }

  protected function getKodeCetakan($instalasi_id)
  {
    $model = $this->dataRegist;
    $pasienadmisi_id = $model['pasienadmisi_id'];
    $instalasi_id = $model['instalasi_id'];
    if (!empty($pasienadmisi_id)) {
      $kode = 'invoice-pembayaran-ri';
    } else if ($instalasi_id == DocoConstants::INST_ID_RD ) {
      $kode = 'invoice-pembayaran-rd';
    } else {
      $kode = 'invoice-pembayaran-rj';
    }
    return $kode;
  }

  protected function getPath($instalasi_id)
  {
    switch ($instalasi_id) {
      case DocoConstants::INST_ID_RJ:
        break;
      
      case DocoConstants::INST_ID_RD:

        break;

      case DocoConstants::INST_ID_RI:

        break;
    }
    
    return $folder.$path;
  }

  protected function getAttrCetakan($instalasi_id)
  {
    $header = $this->dataInvoice['header'];
    $detail = $this->dataInvoice['detail'];
    $jenis_invoice = $this->jenisInvoice;
    $data = $this->dataInvoice['data'];
    $pembayaran = $this->dataPayer['pembayaran'];
    $listMetode = $this->dataPayer['listMetode'];
    $listPayer = $this->dataPayer['listPayer'];

    $model = $this->dataRegist;
    $pasienadmisi_id = $model['pasienadmisi_id'];
    $instalasi_id = $model['instalasi_id'];

    $globalParamsTable = [
      'data' => $data,
      'pembayaran' => $pembayaran,
      'listMetode' => $listMetode,
      'jenis_invoice' => $jenis_invoice,
      'konfigSystem' => $this->konfigSystem
    ];

    if (!empty($pasienadmisi_id)) {
      $infoPasien = InfoPasienRiView::find()->select([
          'no_rekam_medik',
          'no_pendaftaran',
          'nama_pasien',
          'alamat_pasien',
          'umur',
          'jenis_kelamin',
          'ruangan_nama',
          'kelas_pelayanan',
          'kamarruangan_nokamar',
          'no_tempattidur',
          'dokter_admisi',
          'tgl_admisi',
          'tgl_stopakomodasi',
      ])->where([
          'pendaftaran_id' => $this->pendaftaranId
      ])->asArray()->one();

      $detailPasien = PasienView::find()->select([
          'alamat_pasien','propinsi_nama', 'kabupaten_nama',
          'kecamatan_nama', 'kelurahan_nama', 'warganegara'
      ])->where([
          'no_rekam_medik' => $infoPasien['no_rekam_medik']
      ])->asArray()->one();
      $attributes = [
        '#no_pendaftaran#' => $infoPasien['no_pendaftaran'],
        '#no_rekam_medik#' => $infoPasien['no_rekam_medik'],
        '#nama_pasien#' => $infoPasien['nama_pasien'],
        '#gender#' => $infoPasien['jenis_kelamin'],
        '#bill_date#' => !empty($pembayaran['tgl_pembayaran']) ? date('d/m/Y H:i', strtotime($pembayaran['tgl_pembayaran'])) : null,
        '#lokasi#' => $this->profilRs['kota']. ', ' .date('d-M-Y'),
        '#rs_name#' => !empty($this->profilRs['namaRs']) ? $this->profilRs['namaRs'] : '-',
        '#bill_no#' => $pembayaran['no_pembayaran'],
        '#address#' => $detailPasien['alamat_pasien'],
        '#address2#' => $detailPasien['kelurahan_nama'],
        '#address3#' => $detailPasien['kecamatan_nama'],
        '#address4#' => $detailPasien['kabupaten_nama'].' '.$detailPasien['propinsi_nama'].' '.$detailPasien['warganegara'],
        '#payer#' => $this->dataPayer['payer'],
        '#ward#' => $infoPasien['ruangan_nama'],
        '#bed_type#' => $infoPasien['kelas_pelayanan'],
        '#bed_no#' => $infoPasien['kamarruangan_nokamar'] . '-' . $infoPasien['no_tempattidur'],
        '#primary_doctor#' => $infoPasien['dokter_admisi'],
        '#admission#' => date('d/m/Y H:i', strtotime($infoPasien['tgl_admisi'])),
        '#discharge_date#' => !empty($infoPasien['tgl_stopakomodasi']) 
            ? date('d/m/Y H:i', strtotime($infoPasien['tgl_stopakomodasi'])) : null,
      ];
      $path = 'new-invoice-ranap-dev';
      $tabelParams = [
        'detail' => $detail,
        'isPayer' => $this->dataPayer['isPayer'],
        'namaPasien' => $infoPasien['nama_pasien'],
        'qPayer' => $listPayer,
      ];
    } else if ($instalasi_id == DocoConstants::INST_ID_RD) {
      $total = $this->dataInvoice['total'];
      $attributes = [
        '#nama_pasien#' => $header['nama_pasien'],
        '#gender#' => $header['jenis_kelamin'],
        '#invoice_no#' => $header['no_pembayaran'],
        '#invoice_date#' => date('d M Y h:i A'),
        '#visit_no#' => $header['no_pendaftaran'],
        '#main_payer#' => $header['penjamin_nama'],
        '#mr_no#' => $header['no_rekam_medik'],
        '#address1#' => $header['alamat'],
        '#address2#' => '',
        '#terbilang#' => DocoHelpers::terbilangToEnglish($total),
        '#tgl_deposit#' => date('d M Y H:i'),
        '#balance_deposit#' => DocoHelpers::formatNumber($header['total_uang_muka']),
        '#remarks#' => '-',
        '#cash#' => DocoHelpers::formatNumber($total),
        '#ending_balance#' => DocoHelpers::formatNumber($header['total_sisatagihan']),
      ];
      $path = 'invoice_rd_dev';
      $tabelParams = [
        'received' => $header['nama_pasien'],
        'balance' => $pembayaran['total_kembalian'],
        'qPayer' => $listPayer,
      ];
    } else {
      $arrDokter = $this->dataInvoice['arrDokter'];

      // biaya adm
      $adm_resep = isset($header['adm_resep']) ? $header['adm_resep'] : 0;
      $biaya_administrasi = $header['biaya_administrasi'] + $adm_resep;
      $billDate = !empty($pembayaran['tgl_pembayaran']) ? date('d/M/Y H:i', strtotime($pembayaran['tgl_pembayaran'])) : null;

      $attributes = [
        '#no_pendaftaran#' => $header['no_pendaftaran'],
        '#no_rekam_medik#' => $header['no_rekam_medik'],
        '#nama_pasien#' => $header['nama_pasien'],
        '#gender#' => $header['jenis_kelamin'],
        '#bill_date#' => $billDate,
        '#lokasi#' => $this->profilRs['kota']. ', ' .date('d-M-Y'),
        '#rs_name#' => !empty($this->profilRs['namaRs']) ? $this->profilRs['namaRs'] : '-',
        '#bill_no#' => $pembayaran['no_pembayaran'],
      ];
      $path = 'invoice_dev';
      $tabelParams = [
        'nama_pasien' => $header['nama_pasien'],
        'list_dokter' => $arrDokter,
        'listPayer' => $listPayer,
      ];
    }

    $folder = '@app/modules/v1/views/tagihan-pasien/'. $path;
    $paramsTable = array_merge($globalParamsTable,$tabelParams);
    $globalAttributes = [
      '#printed_by#' => $this->namaPegawai,
      '#printed_date#' => date('d/m/Y g:i A'),
      '#age#' => $this->umur,
      '#datatable#' => Yii::$app->controller->renderPartial($path, $paramsTable),
    ];

    $paramsCetakan = array_merge($globalAttributes,$attributes);
    return $paramsCetakan;
  }

  protected function getKonfigSistem()
  {
      return Yii::$app->cache->getOrSet(DocoConstants::VAR_K_S , function ($cache) {
          return KonfigSystem::find()->asArray()->one();
      });
  }

  protected function processFlow()
  {
    $this->populateData();
    $this->generateCetakan();
  }
}