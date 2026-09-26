<?php

/**
 * ? @author : Budi (budi@docotel.com)
 * * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use Doco\models\kasir\PasienView;
use Doco\models\kasir\InvoiceObatView;
use Doco\models\ProfilRsView;
use Doco\models\KonfigSystem;
use Doco\models\kasir\HeaderInvoice;
use Doco\models\kasir\PembayaranInvoice;
use Extensions\kasir\InvoicePayerBelumBayar;
use Doco\models\kasir\PembayaranPelayanan;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\DaftarTindakan;

class CetakDetailInvoiceGabungProcess extends \Doco\components\DocoBaseProcessExtension
{
    public $dokTercetak = 'cetak-detail-invoice-gabung';
    public $dokPath = 'invoice-gabung-detail';
    public $pisahBill = false;
    protected $invoicegabung_id;
    protected $invoice_id;
    protected $jenis_invoice;
    protected $kelompok;
    protected $nama_pegawai;
    protected $isObat = false;
    protected $pasienadmisi_id;
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
    protected $penjaminId;
    protected $headerGabung;
    protected $grandTotal;
    protected $is_akomodasi = false;

    protected function populateData()
    {
        $request = $this->_requestData;
        $invoicegabung_id = $request->get('invoicegabung_id', null);
        $this->invoicegabung_id = $invoicegabung_id;
        $headerGabung = $this->headerGabung();
        $this->headerGabung = $headerGabung;
        $model = $this->getPendaftaran();
        $this->model = $model;
        $this->nama_pegawai = $request->get('nama_pegawai', null);
        $pasienadmisi_id = !empty($model['pasienadmisi_id']) ? $model['pasienadmisi_id'] : '';
        $this->pasienadmisi_id = $pasienadmisi_id;
        $header = $this->getHeader();
        $this->header = $header;
        $detailPasien = $this->detailPasien();
        $this->detailPasien = $detailPasien;
        $this->setDataPasien();
        $dataRoomRent = !empty($pasienadmisi_id) ? $this->getDataRoomRent() : [];
        $this->dataRoomRent = $dataRoomRent;
        $pembayaran = $this->getPembayaran();
        $this->pembayaran = $pembayaran;
        $dataPayer = $this->getPayer();
        $this->dataPayer = $dataPayer;
        $this->dataRs = $this->getProfileRs();
        $this->konfigSystem = $this->getKonfigSistem();
        $constantID = new DocoConstansId;
        $tindakan_keperawatan = $constantID->ActionGetAdditional('tindakan_keperawatan');
        $tindakan_keperawatan = json_decode($tindakan_keperawatan, true);
        $this->tindakan_keperawatan = $tindakan_keperawatan;
    }

    protected function setDataPasien()
    {
        $kecamatan_nama = $kelurahan_nama = $kabupaten_nama = $propinsi_nama = $warganegara = '-';
        if(isset($this->detailPasien['kecamatan_nama'])) {
            $kecamatan_nama = $this->detailPasien['kecamatan_nama'];
        }
        if(isset($this->detailPasien['kelurahan_nama'])) {
            $kelurahan_nama = $this->detailPasien['kelurahan_nama'];
        }
        if(isset($this->detailPasien['kabupaten_nama'])) {
            $kabupaten_nama = $this->detailPasien['kabupaten_nama'];
        }
        if(isset($this->detailPasien['propinsi_nama'])) {
            $propinsi_nama = $this->detailPasien['propinsi_nama'];
        }

        if(isset($this->header['kecamatan_nama'])) {
            $this->header['kecamatan_nama'] = $kecamatan_nama;
        }
        if(isset($this->header['kelurahan_nama'])) {
            $this->header['kelurahan_nama'] = $kelurahan_nama;
        }
        if(isset($this->header['kabupaten_nama'])) {
            $this->header['kabupaten_nama'] = $kabupaten_nama;
        }
        if(isset($this->header['propinsi_nama'])) {
            $this->header['propinsi_nama'] = $propinsi_nama;
        }
        if(isset($this->header['warganegara'])) {
            $this->header['warganegara'] = $warganegara;
        }
        
        $this->header['umur'] = !empty($this->model['umur']) ? $this->model['umur'] : '-'; 
    }

    protected function getDetailTindakan()
    {
        $dataTindakan = $this->getDetailInvoice();
        $this->dataTindakan = $dataTindakan;
        return $dataTindakan;
    }

    protected function getBillNo($modelPembayaran)
    {
        $bill_no = $modelPembayaran->no_pembayaran;
        $jenis_invoice = $this->jenis_invoice;
        if($jenis_invoice != 1) {
            if($jenis_invoice == 2) {
                $bill_no = $modelPembayaran->no_invoicepasien;
            }
            else {
                if(!empty($this->penjaminId)) {
                    $pembayaranPelayanan = PembayaranPelayanan::find()->where([
                        'pembayaran_id' => $this->invoice_id,
                        'penjamin_id' => $this->penjaminId,
                    ])->one();
                    if($pembayaranPelayanan) {
                        $bill_no = !empty($pembayaranPelayanan['no_pembayaran']) ? $pembayaranPelayanan['no_pembayaran'] : '-';
                    }
                }
            }
        }
        if(!$bill_no) {
            $pembayaranPelayanan = PembayaranPelayanan::find()->where([
                'pembayaran_id' => $this->invoice_id,
            ])->one();
            $bill_no = !empty($pembayaranPelayanan['no_pembayaran']) ? $pembayaranPelayanan['no_pembayaran'] : '-';
        }
        return $bill_no;
    }

    protected function setAttrPrint($shown = true)
    {
        $dataRs = $this->dataRs;
        $dataPayer = $this->dataPayer;
        $nama_pegawai = $this->nama_pegawai;
        $pasienadmisi_id = $this->pasienadmisi_id;
        $header = $this->header;
        $pembayaran = $this->pembayaran;
        $modelHeader = new HeaderInvoice;
        $modelPembayaran = New PembayaranInvoice;
        $modelHeader->attributes = $header;
        $modelPembayaran->attributes = $pembayaran;
        $modelHeader->nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '';
        $tglKeluar = !empty($pasienadmisi_id) ? $modelHeader->tgl_stopakomodasi : $modelHeader->tgl_pasienpulang;
        $tglMasuk = !empty($pasienadmisi_id) ? $modelHeader->tgl_admisi : $modelHeader->tgl_pendaftaran;
        $different = strtotime($tglKeluar)-strtotime($tglMasuk);
        $dijamin = $modelPembayaran->total_dijamin;
        $ruangan_nama = !empty($pasienadmisi_id) ? $modelHeader->ruangan_nama : $modelHeader->r_pendaftaran;
        $kelas_pelayanan = !empty($pasienadmisi_id) ? $modelHeader->kelas_admisi : $modelHeader->kelaspelayanan_nama;
        if(isset($modelHeader->kelas_ditagihkan_nama)){
            $kelas_pelayanan = $modelHeader->kelas_ditagihkan_nama;
        }
        $primary_doctor = !empty($pasienadmisi_id) ? $modelHeader->dokter_admisi : $modelHeader->dok_pendaftaran;
        $tgl_admisi = (!empty($pasienadmisi_id) && !empty($modelHeader->tgl_admisi)) 
            ? date('d/M/Y H:i', strtotime($modelHeader->tgl_admisi)) 
            : '';
        if(empty($tgl_admisi)) {
            $tgl_admisi = !empty($modelHeader->tgl_pendaftaran) ? date('d/M/Y H:i', strtotime($modelHeader->tgl_pendaftaran)) : '-';
        }
        $tgl_stopakomodasi = (!empty($pasienadmisi_id) && !empty($modelHeader->tgl_stopakomodasi)) 
            ? date('d/M/Y H:i', strtotime($modelHeader->tgl_stopakomodasi)) 
            : '-';
        $bed_type = !empty($pasienadmisi_id) ? $modelHeader->kamarruangan_nokamar : '-';
        $bed_no = !empty($pasienadmisi_id) ? $modelHeader->kamarruangan_nokamar . '-' . $modelHeader->no_tempattidur : '-';
        $tagihan_rs = !empty($pasienadmisi_id) ? $modelHeader->tagihan_rs : $modelPembayaran->total_tagihan;
        $jumlahHari = floor($different / (60 * 60 * 24));
        $umur = !empty($modelHeader->tanggal_lahir) ? $this->getUmur($modelHeader->tanggal_lahir) : '-';
        $pembulatan = isset($pembayaran['pembulatan']) ? $pembayaran['pembulatan'] : 0;
        $total_pembulatan = isset($pembayaran['total_pembulatan']) ? $pembayaran['total_pembulatan'] : 0;
        $discount = isset($pembayaran['discount']) ? $pembayaran['discount'] : 0;
        $penggunaan_uangmuka = isset($pembayaran['penggunaan_uangmuka']) ? $pembayaran['penggunaan_uangmuka'] : 0;
        $total_sisatagihan = isset($pembayaran['total_sisatagihan']) ? $pembayaran['total_sisatagihan'] : 0;
        $total_dijamin = isset($pembayaran['total_dijamin']) ? $pembayaran['total_dijamin'] : 0;
        $total_ditagihkan = isset($pembayaran['total_ditagihkan']) ? $pembayaran['total_ditagihkan'] : 0;
        $roundeBillAmountPayer = ($total_dijamin > 0) ? $total_pembulatan : 0;
        $roundeBillAmountPatient = ($total_dijamin == 0) ? $total_pembulatan : 0;
        $diskonPayer = $total_dijamin > 0 ? $discount : 0;
        $diskonPasien = $total_ditagihkan > 0 ? 0 : $discount;
        $headerGabung = $this->headerGabung;
        $tgl_pembayaran = !empty($headerGabung['tgl_invoicegabung']) ? $headerGabung['tgl_invoicegabung'] : null;
        $bill_no = isset($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-';
		$tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? date('d M Y', strtotime($headerGabung['tgl_invoicegabung_cetak'])) : $tgl_pembayaran;
		$penjamin = !empty($headerGabung['penjamin']) ? $headerGabung['penjamin'] : $dataPayer['payer'];
        $no_pendaftaran = !empty($headerGabung['no_pendaftaran']) ? $headerGabung['no_pendaftaran'] : $modelHeader->no_pendaftaran;
        
		$additionalInvoice = !empty($pembayaran['additional_data']) ? $pembayaran['additional_data'] : [];
		$admDetail = isset($pembayaran['admAsuransi']) ? $pembayaran['admAsuransi'] : [];
		$nominalAdm = !empty($pembayaran['total_administrasi']) ? $pembayaran['total_administrasi'] : 0;
		$discount = !empty($pembayaran['discount']) ? $pembayaran['discount'] : 0;
		$admPatient = isset($pembayaran['admPatient']) ? $pembayaran['admPatient'] : 0;
		$admPayer = isset($pembayaran['admPayer']) ? $pembayaran['admPayer'] : 0;
        
        return [
            '#printed_by#' => $nama_pegawai,
            '#printed_date#' => date('d/M/Y H:i'),
            '#no_pendaftaran#' => $no_pendaftaran,
            '#no_rekam_medik#' => $modelHeader->no_rekam_medik,
            '#nama_pasien#' => $modelHeader->nama_pasien,
            '#gender#' => $modelHeader->jenis_kelamin,
            '#age#' => $umur,
            '#bill_date#' => !empty($tgl_pembayaran) ? date('d/M/Y', strtotime($tgl_pembayaran)) : '-',
			'#tgl_invoicegabung_cetak#' => $tgl_invoicegabung_cetak,
            '#bill_no#' => $bill_no,
            '#address#' => $modelHeader->alamat,
            '#address4#' => $modelHeader->kabupaten_nama.' '.$modelHeader->propinsi_nama.' '.$modelHeader->warganegara,
            '#ward#' => $ruangan_nama,
            '#bed_type#' => $kelas_pelayanan,
            '#bed_no#' => $bed_no,
            '#primary_doctor#' => $primary_doctor,
            '#admission#' => $tgl_admisi,
            '#discharge_date#' => $tgl_stopakomodasi,
            '#payer#' => $penjamin,
            '#lokasi#' => $dataRs['kota']. ', ' .(!empty($tgl_pembayaran) ? date('d M Y', strtotime($tgl_pembayaran)) : ''),
            '#rs_name#' => !empty($dataRs['namaRs']) ? $dataRs['namaRs'] : '-',
            '#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
                'tindakan_keperawatan' => $this->tindakan_keperawatan,
                'pembayaran' => $pembayaran,
                'dataPayer' => $dataPayer,
                'namaPasien' => $modelHeader->nama_pasien,
                'dataRoomRent' => $shown ? $this->dataRoomRent : [],
                'tgl_admisi' => $tgl_admisi,
                'tgl_stopakomodasi' => $tgl_stopakomodasi,
                'bed_type' => $bed_type,
                'bed_no' => $bed_no,
                'tagihan_rs' => $tagihan_rs,
                'jumlahHari' => $jumlahHari,
                'dijamin' => (float) $dijamin,
                'tgl_pembayaran' => $tgl_pembayaran,
                'dataTindakan' => $this->dataTindakan,
                'isPayer' => $dataPayer['isPayer'],
                'jenis_invoice' => $this->jenis_invoice,
                'konfigSystem' => $this->konfigSystem,
                'pembulatan' => $pembulatan,
                'discount' => $discount,
                'penggunaan_uangmuka' => $penggunaan_uangmuka,
                'total_sisatagihan' => $total_sisatagihan,
                'total_dijamin' => $total_dijamin,
                'total_ditagihkan' => $total_ditagihkan,
                'roundeBillAmountPayer' => $roundeBillAmountPayer,
                'roundeBillAmountPatient' => $roundeBillAmountPatient,
                'diskonPayer' => $diskonPayer,
                'diskonPasien' => $diskonPasien,
				'additionalInvoice' => $additionalInvoice,
				'admDetail' => $admDetail,
				'nominalAdm' => $nominalAdm,
				'discount' => $discount,
				'admPatient' => $admPatient,
				'admPayer' => $admPayer,
                'headerGabung' => $headerGabung,
            ])
        ];
    }

    protected function detailPasien()
    {
        $result = [];
        $header = $this->header;
        if(!empty($header)) {
            $result = PasienView::find()->select([
                'alamat_pasien','propinsi_nama', 'kabupaten_nama',
                'kecamatan_nama', 'kelurahan_nama', 'warganegara'
            ])->where([
                'no_rekam_medik' => $header['no_rekam_medik']
            ])->one();
        }
        return $result;
    }

    protected function getPayer()
    {
        $payer = '';
        $isPayer = false;
        $qPayer = $listMetode = $listPayer = [];
        $totalDijamin = 0;
        $totalPembulatan = 0;
        $pembayaranId = $this->getPembayaranId();
        $pembayaranId = "(" . implode(",", $pembayaranId) . ")";
        if (!empty($this->pembayaran['total_dijamin'])) {
            $isPayer = true;
            $qPayer = Yii::$app->db->createCommand("
                SELECT 
                    penjamin_id,
                    penjamin_nama,
                    total_dijamin
                FROM pembayaranpenjamin_t
                WHERE pembayaran_id IN {$pembayaranId}
            ")->queryOne();
                
            $roundPayer = Yii::$app->db->createCommand("
                SELECT 
                penjamin_id,
                pembulatan
                FROM pembayaranpelayanan_t
                WHERE pembayaran_id IN {$pembayaranId}
            ")->queryAll();

            if (!empty($qPayer)) {
                $penjamin_nama = isset($qPayer['penjamin_nama']) ? $qPayer['penjamin_nama'] : '-';
                $totalDijamin = isset($qPayer['total_dijamin']) ? $qPayer['total_dijamin'] : 0;
                $payer = $penjamin_nama;
                // foreach ($qPayer as $value) {
                //     $penjaminNama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
                //     $listPayer[] = $penjaminNama;
                //     $totalDijamin = $value['total_dijamin'];
                //     foreach($roundPayer as $value2){
                //         if($value['penjamin_id'] == $value2['penjamin_id']){
                //             $totalPembulatan += $value2['pembulatan'];
                //         }
                //     }
                // }
                // $payer = implode(", ", $listPayer);
            }
        }
        if (!empty($this->pembayaran['total_nontunai'])) {
            $listMetode = Yii::$app->db->createCommand("
                SELECT 
                    metode_bayar,
                    total_dibayar,
                    no_kartu
                FROM pembayaranmetode_t
                WHERE pembayaran_id IN {$pembayaranId}
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
        $pembayaranId = $this->getPembayaranId();
        $pembayaranId = "(" . implode(",", $pembayaranId) . ")";
        
		$data = Yii::$app->db->createCommand("
            SELECT
            SUM(invoice.total_tagihan + invoice.total_pembulatan) as total_tagihan,
            SUM(invoice.total_administrasi) AS total_administrasi,
            SUM(invoice.total_discount + invoice.total_discountpembayaran) AS discount,
            SUM(invoice.sisa_uangmuka) AS penggunaan_uangmuka,
            SUM(invoice.total_dijamin) AS total_dijamin,
            SUM(invoice.total_sisatagihan) AS total_sisatagihan,
            SUM (invoice.total_ditagihkan - invoice.penggunaan_uangmuka) as total_ditagihkan,
            SUM (invoice.total_sisatagihan + invoice.total_ditagihkan) as patient_amount,
            SUM(invoice.total_tunai) AS total_tunai,
            SUM(invoice.total_nontunai) AS total_nontunai,
            SUM(invoice.total_kembalian) AS total_kembalian,
            SUM(invoice.total_pembulatan) AS total_pembulatan
            FROM pembayaran_t invoice
            WHERE invoice.pembayaran_id IN {$pembayaranId}
        ")->queryOne();

        $additionalData = Yii::$app->db->createCommand("
			SELECT
			additional_data
			FROM pembayaran_t
			WHERE pembayaran_id IN {$pembayaranId} AND is_deleted = FALSE
		")->queryAll();
		
		$admAsuransi = $pembayaranPelayanan = $pembayaranPenjamin = $pembayaranJenisPembayaran = $pembayaranDiskon = [];
		if(!empty($additionalData)) {
			foreach ($additionalData as $key => $value) {
				$additional = isset($value['additional_data']) ? $value['additional_data'] : [];
				if(!empty($additional)) {
					$additional = json_decode($additional, true);
					$admAsuransi[] = isset($additional['adm_asuransi']) ? $additional['adm_asuransi'] : [];
					$pembayaranPelayanan[] = isset($additional['pembayaran_pelayanan']) ? $additional['pembayaran_pelayanan'] : [];
					$pembayaranPenjamin[] = isset($additional['pembayaran_penjamin']) ? $additional['pembayaran_penjamin'] : [];
					$pembayaranJenisPembayaran[] = isset($additional['pembayaran_jenis_pembayaran']) ? $additional['pembayaran_jenis_pembayaran'] : [];
					$pembayaranDiskon[] = isset($additional['pembayaran_diskon']) ? $additional['pembayaran_diskon'] : [];
				}
			}
		}

		/** get total biaya administrasi dijamin dan dibayarkan pasien */
		$admPayer = $admPatient = 0;
		if(!empty($admAsuransi) && is_array($admAsuransi)){
			foreach($admAsuransi as $val){
				$admPayer += !empty($val['dijamin']) ? $val['dijamin'] : 0;
				$admPatient += !empty($val['harusbayar']) ? $val['harusbayar'] : 0;
			}
		}

		$data['admAsuransi'] = $admAsuransi;
		$data['pembayaranPelayanan'] = $pembayaranPelayanan;
		$data['pembayaranPenjamin'] = $pembayaranPenjamin;
		$data['pembayaranJenisPembayaran'] = $pembayaranJenisPembayaran;
		$data['pembayaranDiskon'] = $pembayaranDiskon;
		$data['additional_data'] = $additionalData;
		$data['admPayer'] = $admPayer;
		$data['admPatient'] = $admPatient;
        return $data;
    }

    protected function getTotalPembayaran()
    {
        return Yii::$app->db->createCommand("
            SELECT
                created_date AS tgl_pembayaran,
                no_pembayaran,
                (total_tagihan + total_pembulatan) as total_tagihan,
                total_administrasi,
                (total_discount + total_discountpembayaran) as discount,
                sisa_uangmuka as penggunaan_uangmuka,
                total_dijamin,
                total_sisatagihan,
                (total_tagihan + total_administrasi + total_pembulatan - (total_discount + total_discountpembayaran) - penggunaan_uangmuka - total_sisatagihan) as total_ditagihkan,
                (total_sisatagihan + total_ditagihkan) as patient_amount,
                total_tunai,
                total_nontunai,
                total_kembalian,
                total_pembulatan,
                additional_data,
                total_discountpembayaran,
                no_invoicepasien
            FROM pembayaran_t
            WHERE pembayaran_id = {$this->invoice_id}
        ")->queryOne();
    }

    protected function getHeader()
    {
        $pembayaranId = $this->getPembayaranId();
        $pembayaranId = "(" . implode(",", $pembayaranId) . ")";
        $pasienadmisi_id = !empty($this->model['pasienadmisi_id']) ? $this->model['pasienadmisi_id'] : null;
        $headerGabung = $this->headerGabung;
        $pendaftaran_id = !empty($headerGabung['pendaftaran_id_cetak']) ? $headerGabung['pendaftaran_id_cetak'] : '';
        $dataranap = $datarj = $result = [];
        if($pasienadmisi_id){
            $dataranap = Yii::$app->db->createCommand("
                SELECT
                pasienadmisi_id,
                tgl_admisi,
                dokter_admisi,
                ruangan_nama,
                kamarruangan_nokamar,
                no_tempattidur,
                tgl_stopakomodasi,
                kelaspelayanan_nama AS kelas_admisi,
                kelas_ditagihkan_nama,
                tagihan_rs,
                tgl_pulang
            FROM
                infopasienri_v 
                WHERE pasienadmisi_id = {$pasienadmisi_id}")
            ->queryOne();
        }
        $datarj = Yii::$app->db->createCommand("
            SELECT
            pasienrj.pembayaran_id,
            pasienrj.no_pendaftaran,
            pasienrj.no_rekam_medik,
            pasienrj.nama_pasien,
            pasienrj.alamat,
            pasienrj.tanggal_lahir,
            pasienrj.jenis_kelamin,
            pasienrj.tgl_pembayaran,
            pasienrj.no_pembayaran,
            pasienrj.dok_pendaftaran,
            pasienrj.dok_ranap,
            pasienrj.r_pendaftaran,
            pasienrj.r_ranap,
            pasienrj.carabayar_nama,
            pasienrj.penjamin_nama,
            pasienrj.tgl_pendaftaran,
            pasienrj.kelaspelayanan_nama,
            pasienrj.biaya_administrasi,
            pasienrj.adm_resep,
            pasienrj.tgl_pasienpulang,
            null as umur,
            null as pasienadmisi_id,
            null as tgl_admisi,
            null as dokter_admisi,
            null as ruangan_nama,
            null as kamarruangan_nokamar,
            null as no_tempattidur,
            null as tgl_stopakomodasi,
            null as kelas_admisi,
            null as tagihan_rs,
            null as tgl_pulang,
            pasienrj.total_dijamin 
        FROM
            invoicesudahbayar_v pasienrj
            WHERE pasienrj.pendaftaran_id ={$pendaftaran_id}")
        ->queryOne();
        
        if(!empty($datarj) || !empty($dataranap)) {
            $result = array_merge($datarj, $dataranap);
        }
        
        return $result;
    }

    protected function getDetailInvoice()
    {
        $pembayaranId = $this->getPembayaranId();
        $pembayaranId = "(" . implode(",", $pembayaranId) . ")";
        $dataTindakan = [];
        $condAkomodasi = '';
        if(!$this->is_akomodasi){
            $condAkomodasi = "AND is_akomodasi = false";
        }
        $akomodasi = Yii::$app->db->createCommand("
            SELECT 
                kelompok,
                tindakan_obat,
                tgl_pelayanan, 
                dokter,
                qty,
                harga_satuan,
                tarif,
                layanan_jenis,
                is_konsultasi,
                tarif_diskon,
                cyto_tindakan,
                tarifcyto_tindakan,
                ruangan,
                kelompoktindakan_id,
                tarif_dijamin,
                tarif_dibayarkan,
                tarifpenyulit_tindakan,
                pasienadmisi_id,
                pendaftaran_id,
                kamar,
                kelas,
                no_bed,
                tindakan_obat_kode
            FROM invoiceridetail_v 
            WHERE pembayaran_id IN {$pembayaranId}
            AND is_akomodasi = true
        ")->queryAll();
        
        $detail = Yii::$app->db->createCommand("
            SELECT 
                kelompoktindakan_nama AS kelompok, 
                tindakan_obat_nama AS tindakan_obat, 
                tgl_pelayanan, 
                dokter_tindakan AS dokter, 
                qty, 
                tarif_satuan AS harga_satuan, 
                sub_total AS tarif, 
                is_obat, 
                is_konsultasi, 
                tarif_diskon, 
                tarif_cyto AS tarifcyto_tindakan, 
                ruangan_pelayanan AS kamar,
                CASE WHEN tarif_cyto > 0 THEN true ELSE false END AS cyto_tindakan,
                kelompoktindakan_id,
                tarif_dijamin,
                tarif_dibayarkan,
                tarifpenyulit_tindakan,
                null as pasienadmisi_id,
                pendaftaran_id,
                kelaspelayanan_nama AS kelas,
                jenis_racikan
                FROM invoicesudahbayardetail_v 
                WHERE pembayaran_id IN {$pembayaranId} AND pembayaranpelayanan_id IS NOT NULL 
                ".$condAkomodasi."
                ORDER BY tandabuktibayar_id DESC
        ")->queryAll();

        $constantID = new DocoConstansId;
        $kelompokTindakan = $constantID->actionGetAdditional('kelompok_tindakan');
        $arrKelompok = [];
        if(!empty($kelompokTindakan)) {
            $kelompokTindakan = json_decode($kelompokTindakan, true);
            foreach ($kelompokTindakan as $key => $value) {
                $arrKelompok[$value] = $value;
            }
        }

		$grandTotal = 0;
        $headerGabung = $this->headerGabung;
		$tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? $headerGabung['tgl_invoicegabung_cetak'] : '';

        $detail = array_merge($detail, $akomodasi);
        foreach ($detail as $value) {
			$grandTotal += isset($value['tarif']) ? $value['tarif'] : 0;
            $ruangan =!empty($value['ruangan']) ? $value['ruangan'] :(!empty($value['kamar']) ? $value['kamar'] : '');
            $kelompok =!empty($value['kelompok']) ? $value['kelompok'] : null;
            $registid =!empty($value['pendaftaran_id']) ? $value['pendaftaran_id'] : null;
            $admisiId =!empty($value['pasienadmisi_id']) ? $value['pasienadmisi_id']: null;
            $is_konsultasi = isset($value['is_konsultasi']) ? $value['is_konsultasi'] : false;
            $kelompokId = isset($value['kelompoktindakan_id']) ? $value['kelompoktindakan_id'] : null;
            if(!empty($tgl_invoicegabung_cetak)){
                $value['tgl_pelayanan'] = $tgl_invoicegabung_cetak;
            }

            if(!empty($ruangan)) {
                if($kelompokId) {
                    $tindakan_obat = $value['tindakan_obat'];
                    $dokter = !empty($value['dokter']) ? $value['dokter'] : '';
                    $str = !empty($dokter) ? ' ( '.$dokter.' )' : '';
                    if($is_konsultasi || isset($arrKelompok[$kelompokId])) {
                        $value['tindakan_obat'] = $tindakan_obat.$str;
                    }
                }
                $dataTindakan[$ruangan][$kelompok][] = $value;
                if ($this->pisahBill && !empty($this->pasienadmisi_id)) {
                    $groupBill = "{$registid}-{$admisiId}";
                    $dataTindakan[$groupBill][$ruangan][$kelompok][] = $value;
                } 
            }
        }
		$this->grandTotal = $grandTotal;
        return $dataTindakan;
    }

    protected function getDataRoomRent()
    {
        $dataRoomRent = [];
        $pembayaranId = $this->getPembayaranId();
        $pembayaranId = "(" . implode(",", $pembayaranId) . ")";
        $roomrent = Yii::$app->db->createCommand("
            SELECT 
                kelompok,
                ruangan,
                tarif as total_amount,
                harga_satuan,
                kamar,
                kelas,
                no_bed,
                (((additional_data::json)->>'detail_akomodasi')::json)->>'tanggal' as min,
                (((additional_data::json)->>'detail_akomodasi')::json)->>'tanggal' as max,
                (replace((((additional_data::json)->>'detail_akomodasi')::json)->>'persentase', '%', ''))::int as qty,
                COALESCE (tarif_diskon,0) AS tarif_diskon,
                COALESCE (tarif_dijamin,0) AS tarif_dijamin,
                COALESCE (tarif_dibayarkan,0) AS tarif_dibayarkan 
            FROM invoiceridetail_v 
            WHERE pembayaran_id IN {$pembayaranId}
            AND is_akomodasi = true
            ORDER BY tgl_pelayanan ASC
        ")->queryAll();

        if(!empty($roomrent)) {
            foreach ($roomrent as $key => $value) {
                $ruangan = $value['ruangan'];
                $kelompok_tindakan = $value['kelompok'];
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
        $headerGabung = $this->headerGabung;
        $pendaftaran_id = !empty($headerGabung['pendaftaran_id_cetak']) ? $headerGabung['pendaftaran_id_cetak'] : '';
        $data = [];
        if(!empty($pendaftaran_id)) {
            $sql = "SELECT pendaftaran_id, 
                        instalasi_id, 
                        pasienadmisi_id,
                        umur 
            FROM pendaftaran_t 
            WHERE  pendaftaran_id = {$pendaftaran_id}";

            $data = Yii::$app->db->createCommand($sql)->queryOne();
        }

        return $data;
    }

    protected function getPembayaranId()
    {
        $invoicegabung_id = $this->invoicegabung_id;
        $pembayaran = "SELECT pembayaran_id 
            FROM invoicegabungdetail_t
            WHERE invoicegabung_id = {$invoicegabung_id}";

        $dataPembayaran = Yii::$app->db->createCommand($pembayaran)->queryAll();
        $pembayaranId = [];
        if(!empty($dataPembayaran)) {
            foreach ($dataPembayaran as $key => $value) {
                $pembayaranId[] = isset($value['pembayaran_id']) ? $value['pembayaran_id'] : null;
            }
        }
        return $pembayaranId;
    }

    protected function getBiayaAdmin()
    {
        $pembayaran = $this->pembayaran;
        $additionalData = isset($pembayaran['additional_data']) ? json_decode($pembayaran['additional_data'], true) : [];
        $penjamin_id = null;
        $biayaAdmin = 0;
        if(isset($additionalData['adm_asuransi'])) {
            $admAsuransi = $additionalData['adm_asuransi'];
            if($this->jenis_invoice == 3 && !empty($this->penjaminId)) {
                $penjamin_id = $this->penjaminId;
                $defaultPenjamin = $admAsuransi['defaultPenjamin'];
                if($defaultPenjamin['id'] == $penjamin_id) {
                    $penjamin_id = $defaultPenjamin['id'];
                    if($admAsuransi['dijamin'] > 0){
                        $biayaAdmin = $admAsuransi['dijamin'];
                    }                                  
                }
            }
            elseif($this->jenis_invoice == 1) {
                $penjamin_id = null;
                if($admAsuransi['dijamin'] > 0 && $admAsuransi['harusbayar'] > 0){
                    $biayaAdmin = $admAsuransi['dijamin'] + $admAsuransi['harusbayar'];
                }
                if($admAsuransi['dijamin'] > 0 && $admAsuransi['harusbayar'] == 0){
                    $biayaAdmin = $admAsuransi['dijamin'];
                }                  
                if($admAsuransi['dijamin'] == 0 && $admAsuransi['harusbayar'] > 0){
                    $biayaAdmin = $admAsuransi['harusbayar'];
                }               
            }
            else {
                $penjamin_id = null;
                if($admAsuransi['harusbayar'] > 0){
                    $biayaAdmin = $admAsuransi['harusbayar'];
                }
            }
        }
        return $biayaAdmin;
    }

    protected function headerGabung()
    {
        $invoicegabung_id = $this->invoicegabung_id;
        $sql = "SELECT * FROM infoinvoicegabung_v 
            WHERE invoicegabung_id = {$invoicegabung_id}";

        return Yii::$app->db->createCommand($sql)->queryOne();;
    }

	protected function getPembulatan($total_ditagihkan)
	{
		$helpers = new DocoHelpers;
		$konfigSystem = $this->konfigSystem;
		$isPembulatan = isset($konfigSystem['is_pembulatankeatas']) ? $konfigSystem['is_pembulatankeatas'] : false;
		$satuanPembulatan = !empty($konfigSystem['satuanpembulatan']) ? $konfigSystem['satuanpembulatan'] : 0;
		$pembulatanTagihan = $helpers->pembulatan(round($total_ditagihkan,2), $isPembulatan, $satuanPembulatan);
		$total_ditagihkan = isset($pembulatanTagihan['total']) ? (int) $pembulatanTagihan['total'] : (int) $total_ditagihkan;
		$nominalPembulatan = isset($pembulatanTagihan['pembulatan']) ?  $pembulatanTagihan['pembulatan'] : 0;
		return [
			'nominalPembulatan' => $nominalPembulatan,
			'total_ditagihkan' => $total_ditagihkan,
		];
	}

    protected function getHistoryPindahKamar($pendaftaran_id)
	{
		$data = Yii::$app->db->createCommand("
			SELECT * FROM infopindahkamar_v WHERE pendaftaran_id = {$pendaftaran_id} 
			ORDER BY pindahkamar_id DESC LIMIT 1
		")->queryOne();

		return $data;
	}

    protected function getKunjunganRanap()
	{
		$result = [];
		$status_kamar = '-';
		if(!empty($this->pasienadmisi_id)){
			$result = Yii::$app->db->createCommand("
			SELECT no_pendaftaran,no_rekam_medik,nama_pasien,nama_depan,alamat_pasien,rt,rw,tgl_pendaftaran,tgl_admisi,tgl_pulang,tgl_stopakomodasi,
			jenis_kelamin,tanggal_lahir,umur,pasienadmisi_id,carabayar_id,klsrawat,is_aps,is_pasientitipan,is_pasientitipan_pk,penjamin_id,
			is_stoppasientitipan,bpjs_kelas,kelaspelayanan_id,kelaspelayanan_nama,nama_pegawai,
			carabayar_nama,penjamin_nama,ruangan_nama,kamarruangan_nokamar,no_tempattidur,
			kelas_hak,status_kelas
			FROM infokunjunganri_v 
			WHERE pasienadmisi_id = {$this->pasienadmisi_id}")
			->queryOne();
		}
		return $result;
	}

    protected function getDataAdmin(){
        $confSistem = Cache::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $model = new DaftarTindakan;
        $query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();

        return $query;
    }

    protected function processFlow()
    {
        $this->populateData();
        $this->getDetailTindakan();
        return [
            'attributes' => $this->setAttrPrint(),
            'kode_doc' => $this->dokTercetak,
        ];
    }
}
