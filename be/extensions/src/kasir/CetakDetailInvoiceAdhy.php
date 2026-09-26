<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use GuzzleHttp\Exception\RequestException;
use Doco\models\kasir\HeaderInvoice;
use Doco\models\kasir\PembayaranInvoice;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use Doco\models\kasir\PasienView;
use Doco\models\kasir\InvoiceObatView;
use Doco\models\kasir\MasterTarifTindakanView;
use Doco\models\ProfilRsView;
use Doco\models\KonfigSystem;
use Extensions\kasir\InvoicePayerBelumBayar;
use Doco\models\kasir\PembayaranPelayanan;

class CetakDetailInvoiceAdhy extends \Doco\processes\CetakDetailInvoiceProcess
{
   public $dokTercetak = 'detail-invoice-ri-adhy';
   public $dokPath = 'invoice-detail-ranap-adhy';
   public $pisahBill = false;

   protected $id;
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

        $pasienadmisi_id = $model['pasienadmisi_id'];
        $this->pasienadmisi_id = $pasienadmisi_id;
        if(empty($this->model) || $this->kelompok == DocoConstants::PASIEN_ALKES) {
            $this->isObat = true;
        }

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
        
        $this->header['umur'] = $this->model['umur']; 
    }

    protected function getDetailTindakan()
    {
        $dataTindakan = $this->getDetailInvoice();
        if ($this->pisahBill && !empty($this->pasienadmisi_id)) {
            $this->groupBill = $dataTindakan;
        } else {
            $this->dataTindakan = $dataTindakan;
        }
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
            $print->attributes = $attributes;
        }

        $print->Output($multiple);
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
        $where = '';
        $totalDijamin = 0;
        if($this->jenis_invoice != 1) {
            if(!empty($this->penjaminId)) {
                $where = ' AND penjamin_id = '.$this->penjaminId.'';
            }
        }
        if (!empty($this->pembayaran['total_dijamin'])) {
            $isPayer = true;
            $qPayer = Yii::$app->db->createCommand("
                SELECT 
                    penjamin_id,
                    penjamin_nama,
                    total_dijamin
                FROM pembayaranpenjamin_t
                WHERE pembayaran_id = {$this->invoice_id} {$where}
            ")->queryAll();

            if (!empty($qPayer)) {
                foreach ($qPayer as $value) {
                    $penjaminNama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
                    $listPayer[] = $penjaminNama;
                    $totalDijamin = $value['total_dijamin'];
                }
                $payer = implode(", ", $listPayer);
            }
        }
        if (!empty($this->pembayaran['total_nontunai'])) {
            $listMetode = Yii::$app->db->createCommand("
                SELECT 
                    metode_bayar,
                    total_dibayar,
                    no_kartu
                FROM pembayaranmetode_t
                WHERE pembayaran_id = {$this->invoice_id}
            ")->queryAll();
        }
        return [
            'listPayer' => $listPayer,
            'payer' => $payer,
            'isPayer' => $isPayer,
            'listMetode' => $listMetode,
            'totalDijamin' => $totalDijamin,
        ];
    }
    protected function getPembayaran()
    {
        $pembayaran = Yii::$app->db->createCommand("
            SELECT
                created_date as tgl_pembayaran,
                no_pembayaran,
                total_tagihan,
                total_administrasi,
                (total_discount + total_discountpembayaran) as discount,
                sisa_uangmuka as penggunaan_uangmuka,
                total_dijamin,
                total_sisatagihan,
                (total_ditagihkan - penggunaan_uangmuka) as total_ditagihkan,
                (total_sisatagihan + total_ditagihkan) as patient_amount,
                total_tunai,
                total_nontunai,
                total_kembalian,
                additional_data,
                total_pembulatan,
                no_invoicepasien
            FROM pembayaran_t
            WHERE pembayaran_id = {$this->invoice_id}
        ")->queryOne();

        if($this->isObat) {
            $pembayaran = $this->getTotalPembayaran();
        }
        
        $list_cara_bayar = [];
        $total_tunai = isset($pembayaran['total_tunai']) ? $pembayaran['total_tunai'] : 0;
        if($total_tunai > 0){
            $list_cara_bayar[] = [
                'metode_bayar' => 'Tunai',
                'total_dibayar' => $total_tunai,
                'no_kartu' => '-',
                'label_edc' => 'Tunai'
            ];
        }

        if(!empty($pembayaran['additional_data'])){
            $additional_data = json_decode($pembayaran['additional_data'], true);
            if(!empty($additional_data['pembayaran_jenis_pembayaran'])){
                foreach( $additional_data['pembayaran_jenis_pembayaran'] as $value){
                    $total_dibayar = !empty($value['total_dibayar']) ? $value['total_dibayar'] : 0;
                    $metode_bayar = !empty($value['metode_bayar']) ? $value['metode_bayar'] : '-';
                    $no_kartu = !empty($value['no_kartu']) ? $value['no_kartu'] : '-';
                    $label_edc = !empty($value['label_edc']) ? $value['label_edc'] : '-';
                    $list_cara_bayar[]= [
                        'metode_bayar' => $metode_bayar,
                        'total_dibayar' => $total_dibayar,
                        'no_kartu' => $no_kartu,
                        'label_edc' => $label_edc
                    ];
                }
            }

            if(!empty($additional_data['pembayaran_penjamin'])){
                foreach($additional_data['pembayaran_penjamin'] as $value){
                    $total_dibayar = !empty($value['total_dijamin']) ? $value['total_dijamin'] : 0;
                    $metode_bayar = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : '-';
                    $no_kartu = !empty($value['no_kartu']) ? $value['no_kartu'] : '-';
                    $label_edc = !empty($value['label_edc']) ? $value['label_edc'] : '-';
                    $list_cara_bayar[]= [
                        'metode_bayar' => $metode_bayar,
                        'total_dibayar' => $total_dibayar,
                        'no_kartu' => $no_kartu,
                        'label_edc' => $label_edc
                    ];
                }
            }
        }
        $pembayaran['list_cara_bayar'] = $list_cara_bayar;

        return $pembayaran;
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
                no_invoicepasien
            FROM pembayaran_t
            WHERE pembayaran_id = {$this->invoice_id}
        ")->queryOne();
    }

    protected function getHeader()
    {
        $invoice_id = $this->invoice_id;
        $pasienadmisi_id = $this->model['pasienadmisi_id'] ? $this->model['pasienadmisi_id'] : null;
        $dataranap = $datarj = $result = [];
        if($this->isObat) {
            $result = InvoiceObatView::find()->where(['pembayaran_id' => $invoice_id])->one();
        }
        else {
            if($pasienadmisi_id){
                $dataranap = Yii::$app->db->createCommand("
                            SELECT
                            pasienadmisi_id,
                            tgl_admisi,
                            admisi_dokter as dokter_admisi,
                            ruangan_nama,
                            kamarruangan_nokamar,
                            no_tempattidur,
                            tgl_stopakomodasi,
                            kelaspelayanan_nama AS kelas_admisi,
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
        $whereClause = '';
        $dataTindakan = [];
        $withPenjaminId = '';
        if(!empty($this->penjaminId)) {
            $withPenjaminId = ' AND penjamin_pelayanan_id = '.$this->penjaminId.'';
        }
        if($this->jenis_invoice == 3) {
            $whereClause = 'AND tarif_dijamin > 0 '.$withPenjaminId.' ';
        }
        elseif($this->jenis_invoice == 2) {
            $whereClause = 'AND tarif_dibayarkan > 0';
        }
        
        if(!empty($this->pasienadmisi_id)) {
            $detail = Yii::$app->db->createCommand("
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
                    pendaftaran_id
                FROM invoiceridetail_v 
                WHERE pembayaran_id = {$this->invoice_id}
                {$whereClause}
                AND is_akomodasi = false
            ")->queryAll();
        } else {
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
                    ruangan_pelayanan AS ruangan,
                    CASE WHEN tarif_cyto > 0 THEN true ELSE false END AS cyto_tindakan,
                    kelompoktindakan_id,
                    tarif_dijamin,
                    tarif_dibayarkan,
                    tarifpenyulit_tindakan,
                    null as pasienadmisi_id,
                    pendaftaran_id,
                    jenis_racikan,
                    tindakan_obat_id
                    FROM invoicesudahbayardetail_v 
                    WHERE pembayaran_id = {$this->invoice_id} AND pembayaranpelayanan_id IS NOT NULL 
                    {$whereClause}
                    ORDER BY tandabuktibayar_id DESC
            ")->queryAll();
        }
        $constantID = new DocoConstansId;
        $kelompokTindakan = $constantID->actionGetAdditional('kelompok_tindakan');
        $arrKelompok = [];
        if(!empty($kelompokTindakan)) {
            $kelompokTindakan = json_decode($kelompokTindakan, true);
            foreach ($kelompokTindakan as $key => $value) {
                $arrKelompok[$value] = $value;
            }
        }
        
        foreach ($detail as $value) {
            $ruangan = $value['ruangan'];
            $kelompok = $value['kelompok'];
            $registid = $value['pendaftaran_id'];
            $admisiId = $value['pasienadmisi_id'];
            $is_konsultasi = isset($value['is_konsultasi']) ? $value['is_konsultasi'] : false;
            $kelompokId = isset($value['kelompoktindakan_id']) ? $value['kelompoktindakan_id'] : null;
            $is_obat = isset($value['is_obat']) ? $value['is_obat'] : false;
            $is_obat = isset($value['layanan_jenis']) && $value['layanan_jenis'] == "obat" ? true : $is_obat;
            if(!empty($ruangan)) {
                if($kelompokId) {
                    $tindakan_obat = $value['tindakan_obat'];
                    $dokter = !empty($value['dokter']) ? $value['dokter'] : '';
                    if($this->jenis_invoice == 3) {
                        $value['tarif_dibayarkan'] = 0;
                    }
                    
                    $str = !empty($dokter) ? ' ( '.$dokter.' )' : '';
                    if($is_konsultasi || isset($arrKelompok[$kelompokId])) {
                        $value['tindakan_obat'] = $tindakan_obat.$str;
                    }
                }
                if ($this->pisahBill && !empty($this->pasienadmisi_id)) {
                    $groupBill = "{$registid}-{$admisiId}";
                    $dataTindakan[$groupBill][$ruangan][$kelompok][] = $value;
                } else {
                    if($is_obat){
                        $dataTindakan['FARMASI'][$kelompok][] = $value;
                    }else{
                        $value['detail_tindakan'] = (!empty($value['tindakan_obat_id'])) ? $this->getTarifDataView($value['tindakan_obat_id']) : '';
                        $dataTindakan[$ruangan][$kelompok][] = $value;
                    }
                }
            }
        }
        return $dataTindakan;
    }

    protected function getTarifDataView($paketId)
    {
        $model = $this->masterData();
        $query = $model::find();
        $query->where(['tindakan_paket_id' => $paketId ]);
        $query->andWhere(['!=', 'komponentarif_id', DocoConstants::KOMPONEN_TARIF ] );
        $getDetail = $query->all();

        $arrResult = [];
        if (isset($getDetail)) {
            $arrData = [];
            foreach ($getDetail as $key => $value) {
                $arrData[$value['daftartindakan_id']][] = [
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'daftartindakan_nama' => $value['daftartindakan_nama'],
                        'harga_tariftindakan' => $value['harga_tariftindakan']
                    ];
            }
            
            foreach ($arrData as $k => $val) {
                $totalHargaTarifTindakan = 0;
                foreach ($val as $x => $v) {
                    $totalHargaTarifTindakan += $v['harga_tariftindakan'];
                }
                $arrResult[$k] = [
                    'daftartindakan_id' => $v['daftartindakan_id'],
                    'daftartindakan_nama' => $v['daftartindakan_nama'],
                    'harga_tariftindakan' => $totalHargaTarifTindakan
                ];
            }
        }
        return $arrResult;
    }

    protected function masterData()
    {
        $model = new MasterTarifTindakanView;
        return $model;
    }

    protected function getDataRoomRent()
    {
        $dataRoomRent = [];
        $whereClause = '';
        if($this->jenis_invoice == 3) {
            $whereClause = 'AND tarif_dijamin > 0';
        }
        elseif($this->jenis_invoice == 2) {
            $whereClause = 'AND tarif_dibayarkan > 0';
        }
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
            WHERE pembayaran_id = {$this->invoice_id}
            AND is_akomodasi = true {$whereClause}
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

    protected function getTotalDiskon()
    {
        $db = Yii::$app->db;
        $pembayaran = $this->pembayaran;
        $totalTagihan = isset($pembayaran['total_tagihan']) ? $pembayaran['total_tagihan'] : 0;
        $totalDijamin = isset($pembayaran['total_dijamin']) ? $pembayaran['total_dijamin'] : 0;
        $totalDibayar = isset($pembayaran['patient_amount']) ? $pembayaran['patient_amount'] : 0;
        $query = "SELECT sum(tarif_diskon) AS total_diskon 
            FROM invoicesudahbayardetail_v 
            WHERE pembayaran_id = $this->invoice_id";

        $diskonPayer = $diskonPasien = 0;
        $diskonPayer = $db
            ->createCommand("{$query} AND tarif_dijamin > 0 ")
            ->queryScalar();
        
        if($this->jenis_invoice == 3) {
            if(!empty($this->penjaminId)) {
                $diskonPayer = $db
                ->createCommand("{$query} AND tarif_dijamin > 0 AND penjamin_pelayanan_id = {$this->penjaminId} ")
                ->queryScalar();
            }
        }

        $pembayaran = $this->pembayaran;
        $biayaAdmin = isset($pembayaran['total_administrasi']) ? $pembayaran['total_administrasi'] : 0;
        $diskonPasien = $totalTagihan - ($totalDijamin + $totalDibayar + round($diskonPayer)) + round($biayaAdmin);

        return [
            'diskonPayer' => round($diskonPayer),
            'diskonPasien' => round($diskonPasien),
        ];
    }

    protected function getBiayaAdmin()
    {
        $pembayaran = $this->getPembayaran();
        $additionalData = json_decode($pembayaran['additional_data'], true);

        // menghitung biaya admin
        $penjamin_id = null;
        $biayaAdmin = 0;
        $nominPayer = 0;
        $nominPatient = 0;
        if(isset($additionalData['adm_asuransi'])) {
            $admAsuransi = $additionalData['adm_asuransi'];
            if($this->jenis_invoice == 3 && !empty($this->penjaminId)) {
                $penjamin_id = $this->penjaminId;
                $defaultPenjamin = $admAsuransi['defaultPenjamin'];
                if($defaultPenjamin['id'] == $penjamin_id) {
                    $penjamin_id = $defaultPenjamin['id'];
                    $biayaAdmin = $admAsuransi['dijamin'];
                    $nominPayer = $biayaAdmin;
                }
            }
            elseif($this->jenis_invoice == 1) {
                $penjamin_id = null;
                $biayaAdmin = $admAsuransi['dijamin'];
                $nominPayer = $biayaAdmin;
            }
            else {
                $penjamin_id = null;
                $pembayaranDiskon = $additionalData['pembayaran_diskon'];
                if(!empty($pembayaranDiskon)) {
                    $biayaAdmin = $admAsuransi['dijamin'];
                    $nominPatient = $biayaAdmin;
                }
            }
        }

        return [
            'biaya_admin' => $biayaAdmin,
            'penjamin_id' => $penjamin_id,
            'nominPayer' => $nominPayer,
            'nominPatient' => $nominPatient,
        ];
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
       $modelHeader->nama_pasien = $header['nama_pasien'];

       $tglKeluar = !empty($pasienadmisi_id) ? $modelHeader->tgl_stopakomodasi : $modelHeader->tgl_pasienpulang;
       $tglMasuk = !empty($pasienadmisi_id) ? $modelHeader->tgl_admisi : $modelHeader->tgl_pendaftaran;
       $different = strtotime($tglKeluar)-strtotime($tglMasuk);
       $dijamin = $modelPembayaran->total_dijamin;
       $tgl_pembayaran = !empty($pasienadmisi_id) ? $modelHeader->tgl_pembayaran : $modelPembayaran->tgl_pembayaran;
       $ruangan_nama = !empty($pasienadmisi_id) ? $modelHeader->ruangan_nama : $modelHeader->r_pendaftaran;
       $kelas_pelayanan = !empty($pasienadmisi_id) ? $modelHeader->kelas_admisi : $modelHeader->kelaspelayanan_nama;
       $primary_doctor = !empty($pasienadmisi_id) ? $modelHeader->dokter_admisi : $modelHeader->dok_pendaftaran;
       $tgl_admisi = (!empty($pasienadmisi_id) && !empty($modelHeader->tgl_admisi)) 
           ? date('d/m/Y', strtotime($modelHeader->tgl_admisi)) 
           : '-';
       $tgl_stopakomodasi = (!empty($pasienadmisi_id) && !empty($modelHeader->tgl_stopakomodasi)) 
           ? date('d/m/Y', strtotime($modelHeader->tgl_stopakomodasi)) 
           : '-';
       $bed_type = !empty($pasienadmisi_id) ? $modelHeader->kamarruangan_nokamar : '-';
       $bed_no = !empty($pasienadmisi_id) ? $modelHeader->kamarruangan_nokamar . '-' . $modelHeader->no_tempattidur : '-';
       $tagihan_rs = !empty($pasienadmisi_id) ? $modelHeader->tagihan_rs : $modelPembayaran->total_tagihan;
       $biaya_admin = $modelPembayaran->total_administrasi;
       $jumlahHari = floor($different / (60 * 60 * 24));
       $umur = !empty($modelHeader->tanggal_lahir) ? $this->getUmur($modelHeader->tanggal_lahir) : '-';
       $modelPembayaran->no_pembayaran = $modelHeader->no_pembayaran;
       $list_cara_bayar = !empty($pembayaran['list_cara_bayar']) ? $pembayaran['list_cara_bayar'] : [];

       return [
           '#printed_by#' => $nama_pegawai,
           '#printed_date#' => date('d/M/Y H:i'),
           '#no_pendaftaran#' => $modelHeader->no_pendaftaran,
           '#no_rekam_medik#' => $modelHeader->no_rekam_medik,
           '#nama_pasien#' => $modelHeader->nama_pasien,
           '#gender#' => $modelHeader->jenis_kelamin,
           '#age#' => $umur,
           '#bill_date#' => date('d/M/Y H:i', strtotime($tgl_pembayaran)),
           '#bill_no#' => $modelPembayaran->no_pembayaran,
           '#address#' => $modelHeader->alamat,
           '#address2#' => $modelHeader->kelurahan_nama,
           '#address3#' => isset($modelHeader->kecamatan_nama) ? $modelHeader->kecamatan_nama : '-',
           '#address4#' => $modelHeader->kabupaten_nama.' '.$modelHeader->propinsi_nama.' '.$modelHeader->warganegara,
           '#ward#' => $ruangan_nama,
           '#bed_type#' => $kelas_pelayanan,
           '#bed_no#' => $bed_no,
           '#primary_doctor#' => $primary_doctor,
           '#admission#' => $tgl_admisi,
           '#discharge_date#' => $tgl_stopakomodasi,
           '#payer#' => $dataPayer['payer'],
           '#lokasi#' => $dataRs['kota']. ', ' .date('d M Y', strtotime($tgl_pembayaran)),
           '#rs_name#' => !empty($dataRs['namaRs']) ? $dataRs['namaRs'] : '-',
           '#tgl_masuk#' => !empty($tglMasuk) ? date('d-m-y ', strtotime($tglMasuk)) : '',
           '#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
               'tindakan_keperawatan' => $this->tindakan_keperawatan,
               'pembayaran' => $modelPembayaran,
               'namaPasien' => $modelHeader->nama_pasien,
               'dataRoomRent' => $shown ? $this->dataRoomRent : [],
               'tgl_admisi' => $tgl_admisi,
               'tglMasuk' => $tglMasuk,
               'tgl_stopakomodasi' => $tgl_stopakomodasi,
               'bed_type' => $bed_type,
               'bed_no' => $bed_no,
               'tagihan_rs' => $tagihan_rs,
               'jumlahHari' => $jumlahHari,
               'dijamin' => $dijamin,
               'tgl_pembayaran' => $tgl_pembayaran,
               'biaya_admin' => $shown ? $biaya_admin : 0,
               'dataTindakan' => $this->dataTindakan,
               'isPayer' => $dataPayer['isPayer'],
               'jenis_invoice' => $this->jenis_invoice,
               'konfigSystem' => $this->konfigSystem,
               'pasienadmisi_id' => $this->pasienadmisi_id,
               'printed_by' => $nama_pegawai,
               'modelHeader' => $modelHeader,
                // 'lokasi' => $dataRs['kota']. ', ' .date('d M Y', strtotime($tgl_pembayaran)),
                'lokasi' => 'Jakarta, '.date('d M Y', strtotime($tgl_pembayaran)),
                'list_cara_bayar' => $list_cara_bayar,
           ])
       ];
   }

   protected function processFlow()
   {
      $this->populateData();
      $this->getDetailTindakan();
      $this->cetakDetailInvoice();
   }
}