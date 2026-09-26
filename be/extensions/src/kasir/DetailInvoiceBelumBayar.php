<?php

/**
 * ? @author : Budi (budi@docotel.com)
 * * Powered by Sirs
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
use SirsCore\businessLogic\TagihanHelper;

class DetailInvoiceBelumBayar extends \Doco\processes\CetakDetailInvoiceProcess
{

    protected $totalDijamin = 0;
    protected $biayaAdm = 0;
    protected $totalDiskon = 0;

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
            'pendaftaran_id' => $id
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
                $dibayar = isset($tmpTagihan[$labelObt][$pelayananId]['dibayar']) 
                                            ? $tmpTagihan[$labelObt][$pelayananId]['dibayar'] : 0;
                $dijamin = isset($tmpTagihan[$labelObt][$pelayananId]['dijamin']) 
                                            ? $tmpTagihan[$labelObt][$pelayananId]['dijamin'] : 0;
                if ($this->jenis_invoice == 2) {
                    if ($dibayar > 0) {
                        $row['sub_total'] = $dibayar;
                    } else {
                        continue;
                    }
                }

                $this->totalDiskon += $row['tarif_diskon'];
                $row['dokter_tindakan'] = '';
                $detail[$kelompok][] = $row;
            }
            
            $cond_administrasi = $id;
            if (!is_null($tipe_pasien)) {
                if ($tipe_pasien == 'pasien_rs' || $tipe_pasien == 'pasien_bebas') {
                    $cond_administrasi =  [
                        'penjualanresep_id' => $id
                    ];
                   
                } 
            }
            $total_tagihan_admin = $total_tagihan - $total_jpk; 
            if($this->biayaAdm == 0) {
                $this->biayaAdm = round(TagihanHelper::getBiayaAdmin($cond_administrasi, $penjamin, $kelasPelayanan,(int) $total_tagihan_admin, $admisiId));
            }
        }

        $this->dataTindakan = $detail;
    }

    protected function setAttrPrint()
    {
        $modelHeader = new HeaderInvoice;
        $modelHeader->attributes = $this->model;
        $modelPembayaran = New PembayaranInvoice;
        $modelPembayaran->total_dijamin = $this->totalDijamin;
        $modelPembayaran->total_administrasi = $this->biayaAdm;
        $modelPembayaran->discount = $this->totalDiskon;
        $dataRs = $this->dataRs;

        $tglMasuk = !empty($modelHeader->tgl_pendaftaran) ? date('d/M/Y H:i',strtotime($modelHeader->tgl_pendaftaran)) : '';
        $tglKeluar = date('d/M/Y H:i');

        $tindakan_keperawatan = DocoConstansId::ActionGetAdditional('tindakan_keperawatan');
        $tindakan_keperawatan = json_decode($tindakan_keperawatan, true);
        $this->tindakan_keperawatan = $tindakan_keperawatan;

        $dokter = '';
        $akomodasi = DocoConstansId::actionGetId('tindakan_akomodasi');

        return [
            '#printed_by#' => $this->nama_pegawai,
            '#printed_date#' => date('d/m/Y g:i A'),
            '#no_pendaftaran#' => $modelHeader->no_pendaftaran,
            '#no_rekam_medik#' => $modelHeader->no_rekam_medik,
            '#nama_pasien#' => $modelHeader->nama_pasien,
            '#gender#' => $modelHeader->jenis_kelamin,
            '#alamat#' => $modelHeader->alamat_pasien,
            '#nobuktibayar#' => $modelPembayaran->nobuktibayar,
            '#age#' => $modelHeader->umur,
            '#bill_date#' => '-',
            '#bill_time#' => '-',
            '#lokasi#' => $dataRs['kota']. ', ' .date('d M Y'),
            '#rs_name#' => !empty($dataRs['namaRs']) ? $dataRs['namaRs'] : '-',
            '#bill_no#' => $modelPembayaran->no_pembayaran,
            '#tgl_masuk#' => $tglMasuk,
            '#tgl_keluar#' => $tglKeluar,
            '#dokter#' => $dokter,
            '#datatable#' => Yii::$app->controller->renderPartial('invoice-rajal-mhbg', [
                'tindakan_keperawatan' => $this->tindakan_keperawatan,
                'data' => $this->dataTindakan,
                'nama_pasien' => $modelHeader->nama_pasien,
                'bill_date' => '-',
                'pembayaran' => $modelPembayaran,
                'listPayer' => [],
                'listMetode' => [],
                'konfigSystem' => $this->konfigSystem,
                'header' => $modelHeader,
                'tgl_masuk' => $tglMasuk,
                'tgl_keluar' => $tglKeluar,
                'jenis_invoice' => $this->jenis_invoice,
                'tindakan_akomodasi' => $akomodasi,
            ])
        ];
    }

    protected function cetakDetailInvoice()
    {
        $attributes = $this->setAttrPrint();
        $print = new DocoPrint('invoice-pembayaran-mhbg');
        $print->attributes = $attributes;
        $print->Output();
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
                if ($this->jenis_invoice == 2) {
                    $this->biayaAdm = $default['dibayar'];
                } 
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

    protected function processFlow()
    {
        $this->populateData();
        $this->cetakDetailInvoice();
    }
}
