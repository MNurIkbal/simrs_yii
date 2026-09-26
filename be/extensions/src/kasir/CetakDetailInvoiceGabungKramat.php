<?php

/**
 * @author : Dede Herdiana (dede.herdiana@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use Doco\components\DocoHelpers;
use Doco\models\kasir\HeaderInvoice;
use Doco\models\kasir\PembayaranInvoice;

class CetakDetailInvoiceGabungKramat extends \Doco\processes\CetakDetailInvoiceGabungProcess
{
    
	public $dokPath = '../tagihan-pasien/detail-invoice-non-ranap-kramat';
	public $dokTercetak = 'invoice-kramat';
    protected $is_akomodasi = false;

	private $_mappObatAlkes = [
		'Drugs & Consumables' => 'Obat Alkes',
		'kelompok_obat' => 'Obat Alkes',
		'kelompok_paket' => 'Paket',
		'kelompok_paket_mcu' => 'Paket MCU',
		'Consultation' => 'Konsultasi'
	];

    public $pisahBill = false;
    protected $invoicegabung_id;
    protected $invoice_id;
    protected $jenis_invoice = 1;
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

	protected function setAttrPrint($shown = true)
    {
        $dataRs = $this->dataRs;
        $dataPayer = $this->dataPayer;
        $nama_pegawai = $this->nama_pegawai;
        $pasienadmisi_id = $this->pasienadmisi_id;
        if(!empty($pasienadmisi_id)) {
            $this->dokPath = '../tagihan-pasien/detail-invoice-kramat';
            $this->dokTercetak = 'detail-gabung-invoice-ranap-kramat';
           return $this->setAttrPrintRanap();
		}
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
        $tagihan_rs = !empty($pasienadmisi_id) ? $modelHeader->tagihan_rs : $modelPembayaran->total_tagihan;
        $umur = !empty($header['umur']) ? $header['umur'] : '-';
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
        $diskonPasien = $total_dijamin > 0 ? 0 : $discount;
        $headerGabung = $this->headerGabung;
        $tgl_pembayaran = !empty($headerGabung['tgl_invoicegabung']) ? date('d-m-Y', strtotime($headerGabung['tgl_invoicegabung'])) : null;
        $bill_no = isset($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-';
		$tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? date('d M Y', strtotime($headerGabung['tgl_invoicegabung_cetak'])) : $tgl_pembayaran;
		$penjamin = !empty($headerGabung['penjamin']) ? $headerGabung['penjamin'] : $dataPayer['payer'];
        $no_pendaftaran = !empty($headerGabung['no_pendaftaran']) ? $headerGabung['no_pendaftaran'] : $modelHeader->no_pendaftaran;
		$tglLahir = !empty($header['tanggal_lahir']) ? date('d-M-Y', strtotime($header['tanggal_lahir'])) : '-';

        $pembayaranId = $this->getPembayaranId();
        $pembayaranId = "(" . implode(",", $pembayaranId) . ")";
		$qDetail = Yii::$app->db->createCommand("
            SELECT 
            a.tindakan_obat_kode,
            a.tindakan_obat_nama,
            a.kelompoktindakan_nama AS kelompok,
            a.qty as qty,
            a.sub_total
            FROM invoicesudahbayardetail_v a
            WHERE pembayaran_id IN {$pembayaranId}
        ")->queryAll();

		$grandTotal = $totalAkomodasi = 0;
		$newDetail = [];
        
		if(!empty($qDetail)) {
			foreach ($qDetail as $key => $value) {
				$grandTotal += isset($value['sub_total']) ? $value['sub_total'] : 0;
				$kelompok = isset($value['kelompok']) ? $value['kelompok'] : '';
				$kelompok = isset($this->_mappObatAlkes[$kelompok]) 
				? strtoupper($this->_mappObatAlkes[$kelompok]) : strtoupper($kelompok);
				$newDetail[$kelompok][] = $value;
			}
		}

        $total_administrasi = isset($this->pembayaran['total_administrasi']) ? $this->pembayaran['total_administrasi'] : 0;
		$total_discount = isset($this->pembayaran['discount']) ? $this->pembayaran['discount'] : 0;
		$total_dijamin = isset($this->pembayaran['total_dijamin']) ? $this->pembayaran['total_dijamin'] : 0;
		$penggunaan_uangmuka = isset($this->pembayaran['penggunaan_uangmuka']) ? $this->pembayaran['penggunaan_uangmuka'] : 0;
		$subTotal = $grandTotal + $totalAkomodasi;
		$total_ditagihkan = ($subTotal + $total_administrasi) - $total_discount - $total_dijamin - $penggunaan_uangmuka;

		$pembulatanPenjamin = $this->getPembulatan($total_dijamin);
		$total_dijamin = isset($pembulatanPenjamin['total_ditagihkan']) ? (int) $pembulatanPenjamin['total_ditagihkan'] : (int) $total_dijamin;
		$pembulatan = $this->getPembulatan($total_ditagihkan);
		$nominalPembulatan = isset($pembulatan['nominalPembulatan']) ?  $pembulatan['nominalPembulatan'] : 0;
		$total_ditagihkan = isset($pembulatan['total_ditagihkan']) ? (int) $pembulatan['total_ditagihkan'] : (int) $total_ditagihkan;
        
        return [
			'#tgl_invoice#' => $tgl_pembayaran,
			'#no_transkasi#' => $bill_no,
			'#nama_pasien#' => $modelHeader->no_rekam_medik .' '.  $modelHeader->nama_pasien,
			'#tgl_lahir#' => $tglLahir,
			'#dokter#' => $primary_doctor,
			'#tgl_pelayanan#' => !empty($modelHeader->tgl_pendaftaran) ? date('d-M-Y', strtotime($modelHeader->tgl_pendaftaran)) : '-',
			'#penjamin#' => $penjamin,
			'#nama_kasir#' => isset($headerGabung['kasir']) ? $headerGabung['kasir'] : '-',
			'#title#' => 'PERINCIAN BIAYA DAN PEMBAYARAN',
			'#ruangan_nama#' => isset($header['r_pendaftaran']) ? $header['r_pendaftaran'] : '',
			'#no_pendaftaran#' => isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '',
			'#printed_by#' => $nama_pegawai,
			'#subTotal#' => DocoHelpers::formatNumber($subTotal),
			'#total_administrasi#' => DocoHelpers::formatNumber($total_administrasi),
			'#total_discount#' => DocoHelpers::formatNumber($total_discount),
			'#penggunaan_uangmuka#' => DocoHelpers::formatNumber($penggunaan_uangmuka),
			'#total_dijamin#' => DocoHelpers::formatNumber($total_dijamin),
			'#pembulatan#' => DocoHelpers::formatNumber($nominalPembulatan),
			'#total_ditagihkan#' => DocoHelpers::formatNumber($total_ditagihkan),
			'#data_tabel#' => Yii::$app->controller->renderPartial($this->dokPath, [
				'data' => $newDetail,
			]),
		];
        
    }

    protected function setAttrPrintRanap(){

        $dataRs = $this->dataRs;
        $dataPayer = $this->dataPayer;
        $nama_pegawai = $this->nama_pegawai;
        $pasienadmisi_id = $this->pasienadmisi_id;
        $header = $this->header;
        $pembayaran = $this->pembayaran;
        $headerGabung = $this->headerGabung;
        $pendaftaran_id = !empty($headerGabung['pendaftaran_id_cetak']) ? $headerGabung['pendaftaran_id_cetak'] : (!empty($header['pendaftaran_id']) ? $header['pendaftaran_id'] : null);
        $historyKamar = $this->getHistoryPindahKamar($pendaftaran_id);
        $modelHeader = new HeaderInvoice;
        $modelPembayaran = New PembayaranInvoice;
        $modelHeader->attributes = $header;
        $modelPembayaran->attributes = $pembayaran;
        $modelHeader->nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $modelHeader->no_pembayaran = isset($header['no_pembayaran']) ? $header['no_pembayaran'] : '-';
        $modelHeader->no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '';
        $modelHeader->no_rekam_medik = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-';
        $modelHeader->jenis_kelamin = isset($header['jenis_kelamin']) ? $header['jenis_kelamin'] : '-';
        $modelHeader->alamat = isset($header['alamat']) ? $header['alamat'] : '-';
        $data_admin = [];
        $tglKeluar = !empty($pasienadmisi_id) ? $modelHeader->tgl_stopakomodasi : $modelHeader->tgl_pasienpulang;
        $tglMasuk = !empty($pasienadmisi_id) ? $modelHeader->tgl_admisi : $modelHeader->tgl_pendaftaran;
        $different = strtotime($tglKeluar)-strtotime($tglMasuk);
        $dijamin = $modelPembayaran->total_dijamin;
        $tgl_pembayaran = !empty($pasienadmisi_id) ? $modelHeader->tgl_pembayaran : $modelPembayaran->tgl_pembayaran;
        $ruangan_nama = !empty($modelHeader->ruangan_nama) ? $modelHeader->ruangan_nama : '';
        $kelas_admisi = !empty($modelHeader->kelas_admisi) ? $modelHeader->kelas_admisi : '';
        $kamarruangan_nokamar = !empty($modelHeader->kamarruangan_nokamar) ? $modelHeader->kamarruangan_nokamar : '';
        $no_tempattidur = !empty($modelHeader->no_tempattidur) ? $modelHeader->no_tempattidur : '';
        $primary_doctor = !empty($pasienadmisi_id) ? $modelHeader->dokter_admisi : $modelHeader->dok_pendaftaran;
        
        if(isset($historyKamar['dokter_admisi']) && !empty($historyKamar['dokter_admisi'])) {
            $primary_doctor = $historyKamar['dokter_admisi'];
        }
        if(isset($historyKamar['kelas_ditagihkan_nama']) && !empty($historyKamar['kelas_ditagihkan_nama'])){
            $kelas_admisi = $historyKamar['kelas_ditagihkan_nama'];
        }
        if(isset($historyKamar['ruangan_pindah']) && !empty($historyKamar['ruangan_pindah'])){
            $ruangan_nama = $historyKamar['ruangan_pindah'];
        }
        if(isset($historyKamar['kamar_pindah']) && !empty($historyKamar['kamar_pindah'])){
            $kamarruangan_nokamar = $historyKamar['kamar_pindah'];
        }
        if(isset($historyKamar['tempattidur_pindah']) && !empty($historyKamar['tempattidur_pindah'])){
            $no_tempattidur = $historyKamar['tempattidur_pindah'];
        }
        
        $tgl_admisi = (!empty($pasienadmisi_id) && !empty($modelHeader->tgl_admisi)) 
            ? date('d/M/Y H:i', strtotime($modelHeader->tgl_admisi)) 
            : '';

        if(empty($tgl_admisi)) {
            $tgl_admisi = !empty($modelHeader->tgl_pendaftaran) ? date('d/M/Y H:i', strtotime($modelHeader->tgl_pendaftaran)) : '-';
        }
        $tgl_stopakomodasi = (!empty($pasienadmisi_id) && !empty($modelHeader->tgl_stopakomodasi)) 
            ? date('d/M/Y H:i', strtotime($modelHeader->tgl_stopakomodasi)) 
            : '-';
        
        $tgl_invoicegabung_cetak = !empty($headerGabung['tgl_invoicegabung_cetak']) ? $headerGabung['tgl_invoicegabung_cetak'] : null;
        $tgl_biaya_admin = !empty($tgl_invoicegabung_cetak) ? $tgl_invoicegabung_cetak : ((!empty($pasienadmisi_id) && !empty($modelHeader->tgl_stopakomodasi)) 
        ? $modelHeader->tgl_stopakomodasi: null);
        $tgl_biaya_admin = !empty($tgl_biaya_admin) ? date('d/m/Y', strtotime($tgl_biaya_admin)) : '-';

        $bed_type = !empty($pasienadmisi_id) ? $kamarruangan_nokamar : '-';
        $kunjunganRanap = $this->getKunjunganRanap();
        $hakKelas = isset($kunjunganRanap['kelas_hak']) ? $kunjunganRanap['kelas_hak'] : '-';
        $statusKelas = isset($kunjunganRanap['status_kelas']) ? $kunjunganRanap['status_kelas'] : '';
        $bed_no = !empty($pasienadmisi_id) ? $kamarruangan_nokamar . '/' . $no_tempattidur : '-';
        $tagihan_rs = !empty($pasienadmisi_id) ? $modelHeader->tagihan_rs : $modelPembayaran->total_tagihan;
        $biaya_admin = $modelPembayaran->total_administrasi;
        $jumlahHari = floor($different / (60 * 60 * 24));
        $umur = !empty($header['umur']) ? $header['umur'] : '-';
        $data_admin = '';
        if( $biaya_admin > 0 ){
            $data_admin = $this->getDataAdmin();
        }
        $status_bayar = isset($header['status_bayar_nama']) ? $header['status_bayar_nama'] : '';
        $grandTotal = $this->grandTotal + $biaya_admin;
        $total_ditagihkan = $grandTotal;
        $dataPembulatan = $this->getPembulatan($total_ditagihkan);
        $total_pembulatan = isset($dataPembulatan['nominalPembulatan']) ? $dataPembulatan['nominalPembulatan'] : 0;
        $grandTotal = isset($dataPembulatan['total_ditagihkan']) ? $dataPembulatan['total_ditagihkan'] : 0;
		
        $penjamin = !empty($headerGabung['penjamin']) ? $headerGabung['penjamin'] : $dataPayer['payer'];
		$tgl_invoicegabung_cetak = !empty($tgl_invoicegabung_cetak) ? date('d M Y', strtotime($tgl_invoicegabung_cetak)) : $tgl_pembayaran;
        $no_pendaftaran = !empty($headerGabung['no_pendaftaran']) ? $headerGabung['no_pendaftaran'] : $modelHeader->no_pendaftaran;
        $bill_no = isset($headerGabung['no_invoicegabung']) ? $headerGabung['no_invoicegabung'] : '-';
        $tgl_pembayaran = !empty($headerGabung['tgl_invoicegabung']) ? $headerGabung['tgl_invoicegabung'] : $tgl_pembayaran;

        $total_administrasi = isset($this->pembayaran['total_administrasi']) ? $this->pembayaran['total_administrasi'] : 0;
		$total_discount = isset($this->pembayaran['discount']) ? $this->pembayaran['discount'] : 0;
		$total_dijamin = isset($this->pembayaran['total_dijamin']) ? $this->pembayaran['total_dijamin'] : 0;
		$penggunaan_uangmuka = isset($this->pembayaran['penggunaan_uangmuka']) ? $this->pembayaran['penggunaan_uangmuka'] : 0;
		$subTotal = $grandTotal;

		$pembulatanPenjamin = $this->getPembulatan($total_dijamin);
		$total_dijamin = isset($pembulatanPenjamin['total_ditagihkan']) ? (int) $pembulatanPenjamin['total_ditagihkan'] : (int) $total_dijamin;
		$total_ditagihkan = ($subTotal + $total_administrasi) - $total_discount - $total_dijamin - $penggunaan_uangmuka;
		$pembulatan = $this->getPembulatan($total_ditagihkan);
		$nominalPembulatan = isset($pembulatan['nominalPembulatan']) ?  $pembulatan['nominalPembulatan'] : 0;
		$total_ditagihkan = isset($pembulatan['total_ditagihkan']) ? (int) $pembulatan['total_ditagihkan'] : (int) $total_ditagihkan;
        return [
            '#printed_by#' => $nama_pegawai,
            '#printed_date#' => date('d/M/Y H:i'),
            '#no_pendaftaran#' => $no_pendaftaran,
            '#no_rekam_medik#' => $modelHeader->no_rekam_medik,
            '#nama_pasien#' => $modelHeader->nama_pasien,
            '#gender#' => $modelHeader->jenis_kelamin,
            '#age#' => $umur,
            '#bill_date#' => date('d/M/Y', strtotime($tgl_pembayaran)),
            '#bill_no#' => $bill_no,
            '#address#' => $modelHeader->alamat,
            '#address2#' => $modelHeader->kelurahan_nama,
            '#address3#' => isset($modelHeader->kecamatan_nama) ? $modelHeader->kecamatan_nama : '-',
            '#address4#' => $modelHeader->kabupaten_nama.' '.$modelHeader->propinsi_nama.' '.$modelHeader->warganegara,
            '#ward#' => $ruangan_nama,
            '#bed_type#' => $kelas_admisi,
            '#kamarruangan_nokamar#' => $kamarruangan_nokamar,
            '#no_tempattidur#' => $no_tempattidur,
            '#bed_no#' => $bed_no,
            '#primary_doctor#' => $primary_doctor,
            '#admission#' => $tgl_admisi,
            '#discharge_date#' => $tgl_stopakomodasi,
            '#payer#' => $dataPayer['payer'],
            '#lokasi#' => $dataRs['kota']. ', ' .date('d M Y', strtotime($tgl_pembayaran)),
            '#rs_name#' => !empty($dataRs['namaRs']) ? $dataRs['namaRs'] : '-',
            '#status_bayar#' => $status_bayar,
            '#hakKelas#' => $hakKelas,
            '#statusKelas#' => $statusKelas,
            '#total_pembulatan#' => $total_pembulatan,
			'#subTotal#' => DocoHelpers::formatNumber($subTotal),
			'#total_administrasi#' => DocoHelpers::formatNumber($total_administrasi),
			'#total_discount#' => DocoHelpers::formatNumber($total_discount),
			'#penggunaan_uangmuka#' => DocoHelpers::formatNumber($penggunaan_uangmuka),
			'#total_dijamin#' => DocoHelpers::formatNumber($total_dijamin),
			'#pembulatan#' => DocoHelpers::formatNumber($nominalPembulatan),
			'#total_ditagihkan#' => DocoHelpers::formatNumber($total_ditagihkan),
            '#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
                'tindakan_keperawatan' => $this->tindakan_keperawatan,
                'pembayaran' => $modelPembayaran,
                'namaPasien' => $modelHeader->nama_pasien,
                'dataRoomRent' => $this->dataRoomRent,
                'tgl_admisi' => $tgl_admisi,
                'tgl_stopakomodasi' => $tgl_stopakomodasi,
                'tgl_biaya_admin' => $tgl_biaya_admin,
                'bed_type' => $bed_type,
                'data_admin' => $data_admin,
                'bed_no' => $bed_no,
                'tagihan_rs' => $tagihan_rs,
                'jumlahHari' => $jumlahHari,
                'dijamin' => $dijamin,
                'tgl_pembayaran' => $tgl_pembayaran,
                'biaya_admin' => $biaya_admin,
                'dataTindakan' => $this->dataTindakan,
                'isPayer' => $dataPayer['isPayer'],
                'jenis_invoice' => $this->jenis_invoice,
                'konfigSystem' => $this->konfigSystem,
                'grandTotal' => $grandTotal,
                'total_pembulatan' => $total_pembulatan,
            ])
        ];
    }
	
}