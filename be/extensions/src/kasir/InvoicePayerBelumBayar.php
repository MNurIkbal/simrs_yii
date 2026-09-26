<?php

/**
 * @author : Setyabudi Dwisandi Arifin (setyabudi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\kasir;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use Doco\models\kasir\InfoDataPendaftaran;
use Doco\models\kasir\HeaderInvoice;
use Doco\models\kasir\PembayaranInvoice;
use Doco\models\kasir\PenjualanResep;
use Doco\models\kasir\InfoTagihanPasien;
use Doco\models\kasir\TmpInfoTagihanPasien;
use Doco\models\kasir\InfoTagihanObatDetailView;

class InvoicePayerBelumBayar extends \Doco\processes\CetakDetailInvoiceProcess
{

    protected $totalDijamin = 0;
    protected $biayaAdm = 0;
    protected $additionalAdm = [];
    protected $tmpTagihan;

    protected function populateData()
    {
        $cacheKasir = Yii::$app->kasirCache;
        $request = $this->_requestData;
        $id = $request->get('id');
        $kelompok = $request->get('kelompok');
        $tipe_pasien = $request->get('tipe');
        $config_sistem = $cacheKasir::getKonfigSistem();

        $this->nama_pegawai = $request->get('nama_pegawai', null);
        $this->jenis_invoice = $request->get('jenis_invoice', 1);
        $cond = [
            'pendaftaran_id' => $id,
            
        ];

        $isPenunjang = false;
        if ($kelompok == DocoConstants::PASIEN_PENUNJANG) {
            $isPenunjang = true;
            $cond = [
                'pasienmasukpenunjang_id' => $id
            ];
        } else if (!is_null($tipe_pasien) && in_array($tipe_pasien, ['pasien_bebas','pasien_rs'])) {
            $cond = [
                'penjualanresep_id' => $id
            ];
            $penjualanResep = PenjualanResep::find()->select([
                'pendaftaran_id',
                'biayaadministrasi',
            ])->andWhere($cond)->asArray()->one();

            if (!empty($penjualanResep)) {
                $biayaAdmResep = isset($penjualanResep['biayaadministrasi']) ? $penjualanResep['biayaadministrasi'] : 0;
                if ($tipe_pasien == 'pasien_rs') {
                    $cond = [
                        'pendaftaran_id' => $penjualanResep['pendaftaran_id']
                    ];
                }
            }
        }

        $biayaAdmResep = 0; 

        $infoPasien = InfoDataPendaftaran::find()->where($cond)->asArray()->one();
        $this->model = $infoPasien;
        $this->pasienadmisi_id = $this->model['pasienadmisi_id'];
        $this->dataRs = $this->getProfileRs();
        $this->konfigSystem = $this->getKonfigSistem();

        $kelasPelayanan = isset($infoPasien['kelaspelayanan_id']) ? $infoPasien['kelaspelayanan_id'] : null;
        $penjamin = isset($infoPasien['penjamin_id']) ? $infoPasien['penjamin_id'] : null;
        $regisId = !empty($infoPasien['pendaftaran_id']) ? $infoPasien['pendaftaran_id'] : null;
        $admisiId = !empty($infoPasien['pasienadmisi_id']) ? $infoPasien['pasienadmisi_id'] : null;
        $admTindakanId = !empty($config_sistem['adm_tindakan_id']) ? $config_sistem['adm_tindakan_id'] : null;
        $administrasi_ri = [];
        $groupcarabayarumum_id =  DocoConstants::GROUP_UMUM;

        switch ($kelompok) {
            case DocoConstants::PASIEN_KARCIS :
                $cond['kelompoktindakan_id'] = DocoConstants::VAR_KEL_KRCS;
                break;
            case DocoConstants::PASIEN_ALKES :
                $cond['is_obat'] = true;
                break;
            default:
                # code...
                break;
        }

        $header = [];
        $resultTarifRs = [];

        if (in_array($tipe_pasien, ['pasien_bebas', 'pasien_rs'])) {
            if (isset($infoPasien['administrasi'])) {
                $infoPasien['administrasi'] = $biayaAdmResep;
            }
            $detailTagihan = InfoTagihanObatDetailView::find()->where([
                'penjualanresep_id' => $id
            ]);
        } else {
            $detailTagihan = InfoTagihanPasien::find()
                                ->andWhere($cond)->orderBy([
                                    'tgl_pelayanan' => SORT_ASC
                                ]);
        }

        if ($kelompok == DocoConstants::PASIEN_ALKES) $detailTagihan->andWhere(['NOT', ['penjualanresep_id' => NULL]]);

        if ($kelompok == DocoConstants::PASIEN_PENUNJANG) {
            $detailTagihan->andWhere(['NOT IN', 'instalasi_id', DocoConstants::$exceptPenunjang]);
        }

        // $detailTagihan->andWhere(['is_akomodasi' => false]);
        $detailTagihan = $detailTagihan->asArray()->all();
        $tindakan = $paket = $detail = [];

        $total_tagihan = 0;
        $total_jpk = 0;
        $jpk_id =json_decode(DocoConstansId::actionGetAdditional("JPK"));
        $tindakan_visitdokter = json_decode(DocoConstansId::actionGetAdditional('tindakan_keperawatan'), true);

        if (!empty($detailTagihan)) {
            $tmpTagihan = [];
            if (!empty($regisId)) {
                $id = isset($cond['pasienmasukpenunjang_id']) ? $cond['pasienmasukpenunjang_id'] : $regisId;
                $tmpTagihan = $this->getTmpTagihan($id, $kelompok);
                $this->tmpTagihan = $tmpTagihan;
            } 
            foreach ($detailTagihan as $key => $value) {
                $kelompok = $value['kelompoktindakan_nama'];
                $isObat = $value['is_obat'];
                $labelObt = $isObat ? 'obat' : 'tindakan';
                $pelayananId = $value['pelayanan_id'];
                if (empty($isObat) && in_array($value['tindakan_obat_id'], $jpk_id)){
                    $total_jpk += $value['sub_total'];
                }
                $total_tagihan += $value['sub_total'];
                $row = $value;
                $row['tarif_diskon'] = isset($tmpTagihan[$labelObt][$pelayananId]['diskon']) 
                                            ? $tmpTagihan[$labelObt][$pelayananId]['diskon'] : 0;
                $row['tarif_dijamin'] = isset($tmpTagihan[$labelObt][$pelayananId]['dijamin']) 
                                            ? $tmpTagihan[$labelObt][$pelayananId]['dijamin'] : 0;
                if (empty($row['tarif_dijamin'])) continue;
                $row['harga_satuan'] = $value['tarif_satuan'];
                $row['tindakan_obat'] = $value['tindakan_obat_nama'];
                $row['tarif_dibayarkan'] = isset($tmpTagihan[$labelObt][$pelayananId]['dibayar'])
                    ? $tmpTagihan[$labelObt][$pelayananId]['dibayar'] : 0;
                $detail[] = $row;
            }
        }

        $this->dataTindakan = $detail;
    }

    private function getTmpTagihan($id, $kelompok)
    {
        $cond = [
            'pendaftaran_id' => $id
        ];
        $isPenunjang = false;
        if ($kelompok == DocoConstants::PASIEN_PENUNJANG) {
            $isPenunjang = true;
            $cond = [
                'pasienmasukpenunjang_id' => $id
            ];
        }
        $infoPasien = TmpInfoTagihanPasien::find()->where($cond)->asArray()->all();
        $result = [
            'tindakan' => [],
            'obat' => []
        ];

        foreach ($infoPasien as $val) {
            $isObat = isset($val['is_obat']) ? $val['is_obat'] : null;
            $pelayanan = !empty($val['value']) 
                                ? json_decode($val['value'], true) : [];
            $pelayananId = isset($pelayanan['pelayanan_id']) ? $pelayanan['pelayanan_id'] : null;

            $default = [
                'dijamin' => isset($val['dijamin']) ? $val['dijamin'] : 0,
                'dibayar' => isset($val['totalDibayar']) ? $val['totalDibayar'] : 0,
                'diskon' => isset($val['nominal_diskon']) ? $val['nominal_diskon'] : 0
            ];

            if (empty($val['tindakan_obat_id'])) {
                $this->biayaAdm = !empty($val['subtotal']) ? $val['subtotal'] : 0;
                $this->additionalAdm = $default;
            }

            $this->totalDijamin += $default['dijamin'];

            if (!empty($val['is_obat'])) {
                $result['obat'][$pelayananId] = $default;
            } else {
                $result['tindakan'][$pelayananId] = $default;
            }
        }

        return $result;
    }

    protected function getDetailInvoice()
    {
        $dataTindakan = [];
        $kelompokTindakan = DocoConstansId::actionGetAdditional('kelompok_tindakan');
        $arrKelompok = [];
        if(!empty($kelompokTindakan)) {
            $arrKelompok = json_decode($kelompokTindakan, true);
        }
        foreach ($this->dataTindakan as $value) {
            $ruangan = $value['ruangan_pelayanan'];
            $kelompok = $value['kelompoktindakan_nama'];
            $registid = $value['pendaftaran_id'];
            $admisiId = $value['pasienadmisi_id'];
            $is_konsultasi = isset($value['is_konsultasi']) ? $value['is_konsultasi'] : false;
            $kelompokId = isset($value['kelompoktindakan_id']) ? $value['kelompoktindakan_id'] : null;
            if(!empty($ruangan)) {
                if($kelompokId) {
                    $tindakan_obat = $value['tindakan_obat_nama'];
                    $dokter = !empty($value['dokter']) ? $value['dokter'] : '';
                    if($this->jenis_invoice == 3) {
                        $value['tarif_dibayarkan'] = 0;
                    }
                    $str = !empty($dokter) ? ' ( '.$dokter.' )' : '';
                    if($is_konsultasi || isset($arrKelompok[$kelompokId])) {
                        $value['tindakan_obat_nama'] = $tindakan_obat.$str;
                    }
                }
                if ($this->pisahBill && !empty($this->pasienadmisi_id)) {
                    $groupBill = "{$registid}-{$admisiId}";
                    $dataTindakan[$groupBill][$ruangan][$kelompok][] = $value;
                } else {
                    $dataTindakan[$ruangan][$kelompok][] = $value;
                }
            }
        }
        return $dataTindakan;
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

    protected function setAttrPrint($shown = true)
    {
        $dataRs = $this->dataRs;
        $dataPayer = $this->dataPayer;
        $nama_pegawai = $this->nama_pegawai;
        $pasienadmisi_id = $this->pasienadmisi_id;
        $header = $this->model;
        $pembayaran = $this->pembayaran;
        $modelHeader = new HeaderInvoice;
        $modelPembayaran = New PembayaranInvoice;
        $modelHeader->attributes = $header;
        $modelPembayaran->attributes = $pembayaran;
        $modelHeader->nama_pasien = @$header['nama_pasien'];
        $detailRanap = !empty($pasienadmisi_id) ? $this->getDataRanap($pasienadmisi_id) : [];
        $modelPembayaran->tgl_pembayaran = date('Y-m-d H:i:s');
        $modelPembayaran->additional_data = json_encode([
            'adm_asuransi' => [
                'dijamin' => isset($this->additionalAdm['dijamin']) ? $this->additionalAdm['dijamin'] : 0,
                'nominal_diskon' => isset($this->additionalAdm['diskon']) ? $this->additionalAdm['diskon'] : 0,
                'harusbayar' => isset($this->additionalAdm['dibayar']) ? $this->additionalAdm['dibayar'] : 0,
            ]
        ]);
        // detail ranap
        $tgl_stopakomodasi = ($detailRanap) ? $detailRanap['tgl_stopakomodasi'] : '';
        $tgl_stopakomodasi = (!empty($tgl_stopakomodasi)) ? date('d/M/Y H:i', strtotime($tgl_stopakomodasi)) : '-';
        $tgl_admisi = ($detailRanap) ? $detailRanap['tgl_admisi'] : '';
        $tgl_admisi = (!empty($tgl_admisi)) ? date('d/M/Y H:i', strtotime($tgl_admisi)) : '-';
        $kelas_admisi = ($detailRanap) ? $detailRanap['kelas_admisi'] : '';
        $dokter_admisi = ($detailRanap) ? $detailRanap['dokter_admisi'] : '';
        $kamarruangan_nokamar = ($detailRanap) ? $detailRanap['kamarruangan_nokamar'] : '';
        $bed_type = !empty($pasienadmisi_id) ? $kamarruangan_nokamar : '-';
        $bed_no = !empty($pasienadmisi_id) ? $kamarruangan_nokamar . '-' . $modelHeader->no_tempattidur : '-';
        $primary_doctor = !empty($pasienadmisi_id) ? $dokter_admisi : $modelHeader->dok_pendaftaran;

        $tglKeluar = !empty($pasienadmisi_id) ? $tgl_stopakomodasi : $modelHeader->tgl_pasienpulang;
        $tglMasuk = !empty($pasienadmisi_id) ? $tgl_admisi : $modelHeader->tgl_pendaftaran;
        $different = strtotime($tglKeluar)-strtotime($tglMasuk);
        $dijamin = $modelPembayaran->total_dijamin;
        $tgl_pembayaran = !empty($pasienadmisi_id) ? $modelHeader->tgl_pembayaran : $modelPembayaran->tgl_pembayaran;
        $ruangan_nama = !empty($pasienadmisi_id) ? $modelHeader->ruangan_nama : $modelHeader->r_pendaftaran;
        $kelas_pelayanan = !empty($pasienadmisi_id) ? $kelas_admisi : $modelHeader->kelaspelayanan_nama;
        
        $tagihan_rs = !empty($pasienadmisi_id) ? $modelHeader->tagihan_rs : $modelPembayaran->total_tagihan;
        $biaya_admin = $this->biayaAdm;
        $jumlahHari = floor($different / (60 * 60 * 24));
        $umur = !empty($modelHeader->tanggal_lahir) ? $this->getUmur($modelHeader->tanggal_lahir) : '-';
        $dataRoomRent = !empty($pasienadmisi_id) ? $this->getRoomRent($pasienadmisi_id) : [];
        $tgl_cetak = empty($tgl_pembayaran) ? date('d M Y') : date('d M Y', strtotime($tgl_pembayaran));

        /** Remove Akomodasi */
        if (!empty($pasienadmisi_id) && $shown) {
            $detailTindakan = [];
            foreach ($this->dataTindakan as $key => $items) {
                foreach ($items as $kelompok => $trans) {
                    foreach ($trans as $value) {
                        if (empty($value['is_akomodasi'])) {
                            $detailTindakan[$key][$kelompok][] = $value;
                        }
                    }
                }
            }
            $this->dataTindakan = $detailTindakan;
        } 

        return [
            '#printed_by#' => $nama_pegawai,
            '#printed_date#' => date('d/M/Y H:i'),
            '#no_pendaftaran#' => $modelHeader->no_pendaftaran,
            '#no_rekam_medik#' => $modelHeader->no_rekam_medik,
            '#nama_pasien#' => $modelHeader->nama_pasien,
            '#gender#' => $modelHeader->jenis_kelamin,
            '#age#' => $umur,
            '#bill_date#' => '-',
            '#bill_no#' => '-',
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
            '#payer#' => $modelHeader->penjamin_nama,
            '#lokasi#' => $dataRs['kota']. ', ' .$tgl_cetak,
            '#rs_name#' => !empty($dataRs['namaRs']) ? $dataRs['namaRs'] : '-',
            '#datatable#' => Yii::$app->controller->renderPartial($this->dokPath, [
                'tindakan_keperawatan' => $this->tindakan_keperawatan,
                'pembayaran' => $modelPembayaran,
                'namaPasien' => $modelHeader->nama_pasien,
                'dataRoomRent' => $shown ? $dataRoomRent : [],
                'tgl_admisi' => $tgl_admisi,
                'tgl_stopakomodasi' => $tgl_stopakomodasi,
                'bed_type' => $bed_type,
                'bed_no' => $bed_no,
                'tagihan_rs' => 0,
                'jumlahHari' => 0,
                'dijamin' => $dijamin,
                'tgl_pembayaran' => $tgl_pembayaran,
                'biaya_admin' => $shown ? $biaya_admin : 0,
                'dataTindakan' => $this->dataTindakan ? $this->dataTindakan : [],
                'isPayer' => true,
                'jenis_invoice' => $this->jenis_invoice,
                'konfigSystem' => $this->konfigSystem,
            ])
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

    protected function getDataRanap($pasienadmisi_id)
    {
        return Yii::$app->db->createCommand("
            SELECT
            pasienadmisi_id,
            tgl_admisi,
            dokter_admisi,
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

    protected function getRoomRent($pasienadmisi_id)
    {
        $dataRoomRent = [];
        $roomrent = Yii::$app->db->createCommand("
            SELECT 
                daftartindakan_m.daftartindakan_nama, 
                kelompoktindakan_m.kelompoktindakan_nama AS kelompok,
                ruangan_m.ruangan_nama AS ruangan, 
                tindakanpelayanan_t.tarif_tindakan AS total_amount, 
                tindakanpelayanan_t.tarif_satuan AS harga_satuan, 
                kamarruangan_m.kamarruangan_nokamar AS kamar, 
                kelaspelayanan_m.kelaspelayanan_nama AS kelas,
                kamartempattidur_m.no_tempattidur AS no_bed,
                tindakanpelayanan_t.tindakanpelayanan_id,
                COALESCE (tindakanpelayanan_t.tarif_diskon,0) AS tarif_diskon,
                COALESCE (tindakanpelayanan_t.tarif_dijamin,0) AS tarif_dijamin,
                COALESCE (tindakanpelayanan_t.tarif_dibayarkan,0) AS tarif_dibayarkan,
                (((tindakanpelayanan_t.additional_data::json)->>'detail_akomodasi')::json)->>'tanggal' as min,
                (((tindakanpelayanan_t.additional_data::json)->>'detail_akomodasi')::json)->>'tanggal' as max,
                (replace((((tindakanpelayanan_t.additional_data::json)->>'detail_akomodasi')::json)->>'persentase', '%', ''))::int as qty
            FROM tindakanpelayanan_t 
            JOIN daftartindakan_m ON daftartindakan_m.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id
            JOIN kelompoktindakan_m ON kelompoktindakan_m.kelompoktindakan_id = daftartindakan_m.kelompoktindakan_id
            JOIN ruangan_m ON ruangan_m.ruangan_id = tindakanpelayanan_t.ruangan_id
            JOIN kamarruangan_m ON kamarruangan_m.kamarruangan_id = tindakanpelayanan_t.kamarruangan_id
            JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = tindakanpelayanan_t.kelaspelayanan_id
            JOIN kamartempattidur_m ON kamartempattidur_m.kamartempattidur_id = tindakanpelayanan_t.kamartempattidur_id
            WHERE tindakanpelayanan_t.pasienadmisi_id = {$pasienadmisi_id} AND daftartindakan_m.is_akomodasi = true
            AND tindakanpelayanan_t.is_deleted = FALSE
            ORDER BY tindakanpelayanan_t.tgl_tindakan ASC
        ")->queryAll();
        if(!empty($roomrent)) {
            foreach ($roomrent as $key => $value) {
                $row = $value;
                $ruangan = $value['ruangan'];
                $kelompok_tindakan = $value['kelompok'];
                $pelId = $value['tindakanpelayanan_id'];
                if (!empty($this->tmpTagihan['tindakan'][$pelId]['dijamin'])) {
                    $row['tarif_diskon'] = $this->tmpTagihan['tindakan'][$pelId]['diskon'];
                    $row['tarif_dibayarkan'] = $this->tmpTagihan['tindakan'][$pelId]['dibayar'];
                    $dataRoomRent[$ruangan][$kelompok_tindakan][] = $row;
                }
            }
        }
        return $dataRoomRent;
    }

    protected function processFlow()
    {
        $this->populateData();
        $this->getDetailTindakan();
        $this->cetakDetailInvoice();
    }
}