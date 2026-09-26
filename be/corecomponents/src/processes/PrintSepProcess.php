<?php

namespace Doco\processes;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Rujukan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\InfKunjunganRsView;
use app\modules\v1\models\InfoKunjunganRiView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\InfoJadwalDokterView;
use Doco\components\constans\BpjsConstans;
use Da\QrCode\QrCode;

class PrintSepProcess extends \Doco\components\DocoBaseProcessExtension
{
    public $modelClass = '';

    private function getJenisKunjungan($bpjs)
    {
        if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_PROSEDUR) {
            $jnsKunjungan = $bpjs->flag_procedure == BpjsConstans::PROSEDUR_TIDAK_LANJUT ? BpjsConstans::STRING_PROSEDUR_TIDAK_LANJUT : BpjsConstans::STRING_PROSEDUR_LANJUT;
        } else if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_KONSUL) {
            $jnsKunjungan = BpjsConstans::STRING_KONSUL_DOKTER;
        } else if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_NORMAL) {
            $jnsKunjungan = BpjsConstans::STRING_TUJUAN_NORMAL;
        } else if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_KONTROL) {
            $jnsKunjungan = BpjsConstans::STRING_TUJUAN_KONTROL;
        }

        return $jnsKunjungan;
    }

	protected function processFlow()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '360');
        try{
            $request = Yii::$app->request;
            $model = new InfKunjunganRsView;
            $modelBpjs = new Bpjs;
            $pasien_id = $request->get('pasien_id', null);
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $nosep = $request->get('nosep', null);
            $ruangan_id = $request->get('ruangan_id', null);
            $jenis_pendaftaran = $request->get('jenis_pendaftaran', null);
            $faskesKode = '-';
            $faskesNama = '-';
            $kelasRawat = '-';
            $kelasHak = '-';
            $dpjp = '-';
            $ktp = '-';
            $penjamin_kll = '-';
            $statusPasien = '-';
            $jnsKunjungan = '-';
            $render_partial = 'print_sep';
            $showKelasRawat = true;
            $prb = false;
            $eSep = 'False';
            $is_ranap = false;
            $lookup_ppkPelayanan = $modelBpjs->ppkPelayanan;
            $kunjungan = $model::find()
            ->select(['bpjs_id','pasien_id', 'instalasi_id', 'status_pasien', 'additional_pasien', 'namadepan', 'nama_pasien', 'umur', 'nama_pegawai', 'no_pendaftaran', 'ruangan_nama', 'no_rekam_medik', 'penjamin_nama', 'penanggungjawab_nama'])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['IS NOT', 'bpjs_id', null]);

            if ($jenis_pendaftaran == DocoConstants::JENIS_PENDAFTARAN_RANAP) {
                $kunjungan->andWhere(['IS NOT', 'pasienadmisi_id', null]);
            }

            $kunjungan = $kunjungan->one();
            if (!$kunjungan) {
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $bpjs = $modelBpjs::findOne($kunjungan->bpjs_id);
            // $bpjs = Bpjs::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();

            if (empty($bpjs)) {
                throw new \yii\web\NotFoundHttpException("Data BPJS Tidak Ditemukan", 404);
            }

            $additional_request = json_decode($bpjs->additional_request, true);
            if ($bpjs->cetakan_ke === 0) {
                $qPasien = Pasien::find()->select([
                    'additional_data'
                ])->where([
                    'pasien_id' => $kunjungan->pasien_id
                ])->asArray()->one();

                $getDataBpjs['response'] = ($qPasien['additional_data'] != null) ? json_decode($qPasien['additional_data'],true) : null;
                // If data bpjs empty
                if (empty($getDataBpjs)) {
                    // Get data bpjs
                    $getDataBpjs = $modelBpjs->peserta($bpjs->nokartuasuransi, $bpjs->tglsep);
                }
                $noKartu = $bpjs['nokartuasuransi'];

                // Get data faskes perujuk
                if ($bpjs->asal_rujukan && empty($bpjs->nama_ppk_perujuk) && empty($bpjs->kode_ppk_perujuk)) {
                    $faskesRujukan = $modelBpjs->referensiFaskes($bpjs->ppkrujukan, $bpjs->asal_rujukan);

                    if (isset($faskesRujukan['response']['faskes'][0])) {
                        $faskesKode = $faskesRujukan['response']['faskes'][0]['kode'];
                        $faskesNama = $faskesRujukan['response']['faskes'][0]['nama'];
                    }
                } else if (!empty($bpjs->kode_ppk_perujuk) && !empty($bpjs->nama_ppk_perujuk)) {
                    $faskesNama = $bpjs->nama_ppk_perujuk;
                    $faskesKode = $bpjs->kode_ppk_perujuk;
                }else{
                    $faskesRujukan = $modelBpjs->referensiFaskes($lookup_ppkPelayanan, 2);
                    if (isset($faskesRujukan['response']['faskes'][0])) {
                        $faskesKode = $faskesRujukan['response']['faskes'][0]['kode'];
                        $faskesNama = $faskesRujukan['response']['faskes'][0]['nama'];
                    }
                }

                // Simpan rujukan terakhir
                $getDataBpjs['response']['peserta']['kodefaskes_perujukterakhir']['kode'] = $faskesKode;
                $getDataBpjs['response']['peserta']['namafaskes_perujukterakhir']['nama'] = $faskesNama;

                $qPasien = Pasien::updateAll([
                    'additional_data' => json_encode($getDataBpjs['response']),
                    'nopeserta_bpjs' => $noKartu,
                ],['pasien_id' => $kunjungan->pasien_id]);

                $additional = json_decode($bpjs->additional_data,true);
                $dataPeserta = $additional['sep'];
                if(!empty($additional) && isset($getDataBpjs['response']['peserta']['informasi'])) {
                    $additional['sep']['informasi']['prolanisPRB'] = $getDataBpjs['response']['peserta']['informasi']['prolanisPRB'];
                }
                $bpjs->additional_data = json_encode($additional);
                //** Handle condition cetakan sep kosong karena nosep dari vclaim */
                $getDataBpjs = $modelBpjs->referensiCariSep($bpjs->nosep);
                if (ArrayHelper::getValue($getDataBpjs, 'metaData') == '200') {
                    $add = ['sep' => $getDataBpjs['response']];
                    $bpjs->additional_data = json_encode($add);
                }
            } else {
                $qPasien = Pasien::find()->select([
                    'additional_data'
                ])->where([
                    'pasien_id' => $kunjungan->pasien_id
                ])->asArray()->one();
                $additional = json_decode($qPasien['additional_data'],true);
                $dataPeserta = $additional['peserta'];
                $getDataBpjs['response'] = json_decode($qPasien['additional_data'],true);

                if (isset($bpjs->nama_ppk_perujuk) && !empty($bpjs->nama_ppk_perujuk)) {
                    $faskesNama = $bpjs->nama_ppk_perujuk;
                }else{
                    $faskesRujukan = $modelBpjs->referensiFaskes($lookup_ppkPelayanan, 2);
                    if (isset($faskesRujukan['response']['faskes'][0])) {
                        $faskesKode = $faskesRujukan['response']['faskes'][0]['kode'];
                        $faskesNama = $faskesRujukan['response']['faskes'][0]['nama'];
                    }
                }
            }

            // update cetakan_ke & tgl_cetak
            $additional_data = json_decode($bpjs->additional_data,true);
            // info peserta
            $infoPeserta = !empty($bpjs->info_response) ? json_decode($bpjs->info_response, true) : null;
            if($infoPeserta) {
                if(isset($infoPeserta['peserta']['informasi']) && !empty($infoPeserta['peserta']['informasi']['prolanisPRB'])) {
                    $prb = $infoPeserta['peserta']['informasi']['prolanisPRB'];
                }
                if(isset($infoPeserta['peserta']['informasi']) && isset($infoPeserta['peserta']['informasi']['eSEP'])) {
                    $eSep = $infoPeserta['peserta']['informasi']['eSEP'];
                }
            }

            //** Handle condition cetakan sep kosong karena nosep dari vclaim */
            $getDataBpjs = $modelBpjs->referensiCariSep($bpjs->nosep);
            if (ArrayHelper::getValue($getDataBpjs, 'metaData.code') == '200') {
                $add = ['sep' => $getDataBpjs['response']];
                $bpjs->additional_data = json_encode($add);
                $additional_data = json_decode($bpjs->additional_data,true);

                if(isset($getDataBpjs['response']['eSEP'])){
                    $eSep = ArrayHelper::getValue($getDataBpjs,'response.eSEP');
                }
            }
            $bpjs->cetakan_ke += 1;
            $date = new \DateTime("now");
            $bpjs->tgl_cetak = $date->format('Y-m-d H:i:s');
            $bpjs->save(false);

            $detailBpjs = $additional_data['sep'];
            $kelasRawat = isset($detailBpjs['kelasRawat']) && $detailBpjs['kelasRawat'] != '-' ? $detailBpjs['kelasRawat'] : '-';
            if (!empty($detailBpjs['peserta']['hakKelas'])){
                $exp = explode("Kelas", $detailBpjs['peserta']['hakKelas']);
                $kelasHak = isset($exp[1]) && $exp[1] != '-' ? $exp[1] : '-';

                /*condition default ext room in sign set null*/
                $kunjungan->ruangan_nama = null;
                if ($kunjungan->instalasi_id != DocoConstants::INST_ID_RI) {
                    $kelasRawat = '-';
                    $kelasHak = $kelasHak;
                }

            }
            if(is_numeric($detailBpjs['jnsPelayanan'])) {
                $jnsPelayanan = ($detailBpjs['jnsPelayanan'] == 1) ? 'Rawat Inap' : 'Rawat Jalan';
            }
            else {
                $jnsPelayanan = $detailBpjs['jnsPelayanan'];
            }
            $exp = explode(" - ", $detailBpjs['diagnosa']." - ");
            $cnt = count($exp);

            $diagnosa = ($cnt == 3) ? $exp[1] : $exp[0];

            if ($kunjungan->instalasi_id == DocoConstants::INST_ID_RD) {
                $showKelasRawat = false;
            }

            if ($bpjs->lakalantas != BpjsConstans::LAKALANTAS_BKLL && !empty($bpjs->lakalantas)) {
                if ($bpjs->lakalantas == BpjsConstans::LAKALANTAS_KLL_BKK) {
                    $penjamin_kll = '1. Jasa Raharja, 2. BPJS Kesehatan';
                }

                if ($bpjs->lakalantas == BpjsConstans::LAKALANTAS_KLL_KK) {
                    if (!empty($kunjungan->pekerjaan_id) &&  $kunjungan->pekerjaan_id == BpjsConstans::JENIS_PESERTA_PNS) {
                        $penjamin_kll = '1. Jasa Raharja, 2. Taspen';
                    } else {
                        $penjamin_kll = '1. Jasa Raharja, 2. BPJS Ketenagakerjaan';
                    }
                }

                if ($bpjs->lakalantas == BpjsConstans::LAKALANTAS_KK) {
                    if (!empty($kunjungan->pekerjaan_id) &&  $kunjungan->pekerjaan_id == BpjsConstans::JENIS_PESERTA_PNS) {
                        $penjamin_kll = '1. Jasa Raharja, 2. Taspen';
                    } else {
                        $penjamin_kll = '1. Jasa Raharja, 2. BPJS Ketenagakerjaan';
                    }
                }
            }

            /** Get nama Dokter DPJP
            * Kode Sudah tidak terpakai tapi belum dihapus, nunggu instruksi selanjutnya untuk refactory
            */
            if(isset($bpjs->kode_dpjp_melayani) && !empty($bpjs->kode_dpjp_melayani)) {
                $dpjp = $bpjs->nama_dpjp_melayani;
            } else if($bpjs->additional_request && !empty($bpjs->additional_request)) {
                if ($kunjungan->instalasi_id == DocoConstants::INST_ID_RI) {
                    $is_ranap = true;
                    $dpjp = $bpjs->nama_dpjp_spri;
                } else {
                    $arrRequest = json_decode($bpjs->additional_request, true);
                    $kodeDokter = '';
                    $dpjpServe = $bpjs->kode_dpjp_melayani;
                    $param1 = BpjsConstans::PELAYANAN_RAWAT_INAP;
                    $param2 = date('Y-m-d', strtotime($bpjs->tglsep));
                    $param3 = BpjsConstans::STRING_IGD;

                    if(!empty($dpjpServe)) {
                        $kodeDokter = (int) $dpjpServe;
                    }

                    if(!empty($kodeDokter)) {
                        $lookDpjp = $bpjs->referensiDpjp($param1, $param2, $param3);
                        if($lookDpjp['response']['list'] && !empty($lookDpjp['response']['list'])) {
                            foreach($lookDpjp['response']['list'] as $k => $v) {
                                if($kodeDokter == $v['kode']) {
                                    $dpjp = $v['nama'];
                                }
                            }
                        }
                    }
                }
            }else if(!empty($detailBpjs)){
                $dpjp = !empty($detailBpjs['dpjp']['nmDPJP']) ? $detailBpjs['dpjp']['nmDPJP'] : '-';
            }

            /** End of get DPJP */

            $jnsKunjungan = $this->getJenisKunjungan($bpjs);

            if (isset($kunjungan->status_pasien)) {
                $statusPasien = Lookup::findOne($kunjungan->status_pasien);
            }

            if (isset($kunjungan->additional_pasien) && !empty($kunjungan->additional_pasien)) {
                $additionalPasien = json_decode($kunjungan->additional_pasien);

                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                }
            }

            if (isset($kunjungan->namadepan) && $kunjungan->namadepan != '') {
                $kunjungan->nama_pasien = $kunjungan->namadepan.' '.$kunjungan->nama_pasien;
            }

            if (isset($kunjungan->umur) && $kunjungan->umur != '') {
                $kunjungan->umur = str_replace('Tahun', 'thn,', $kunjungan->umur);
                $kunjungan->umur = str_replace('Bulan', 'bln,', $kunjungan->umur);
                $kunjungan->umur = str_replace('Hari', 'hr', $kunjungan->umur);
            }

            $display_dokter = '';
            $tempText = explode(" ", $kunjungan->nama_pegawai);
            $display_dokter = implode("\n", $tempText);
            $print = new DocoPrint();
            error_reporting(0);
            $print->useSubstitutions=false;
            $print->simpleTables = true;

            $poliTujuan = $detailBpjs['poli'];
            if(!empty($bpjs->politujuan)) {
                $referensiPoli = $modelBpjs->referensiPoli($bpjs->politujuan);
                $referensiPoli = ArrayHelper::getValue($referensiPoli, 'response', []);
                if(!empty($referensiPoli)) {
                    if(isset($referensiPoli['poli']) && is_array($referensiPoli['poli'])) {
                        foreach ($referensiPoli['poli'] as $key => $valPoli) {
                            $kode = ArrayHelper::getValue($valPoli, 'kode');
                            if(!empty($kode)) {
                                if($kode == $bpjs->politujuan) {
                                    $poliTujuan = ArrayHelper::getValue($valPoli, 'nama');
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($poliTujuan)) {
                $detailBpjs['poli'] = $poliTujuan;
            }

            $dataSep = $modelBpjs::find()->where(['nosep' => $bpjs->nosep]);
            $countSep = $dataSep->count();
            $firstSep = $dataSep->orderBy(['bpjs_id' => SORT_ASC])->one();
            $firstSep = ArrayHelper::getValue($firstSep, 'bpjs_id');
            $isAdditionKunjungan = false;
            if($countSep > 1) {
                if($bpjs->bpjs_id == $firstSep){
                    $jnsKunjungan = BpjsConstans::STRING_TUJUAN_NORMAL;
                }else if($bpjs->politujuan == $detailBpjs['poli']){
                    $jnsKunjungan = isset($detailBpjs['tujuanKunj']['nama']) && !empty($detailBpjs['tujuanKunj']['nama']) ? $detailBpjs['tujuanKunj']['nama'] : null;
                    if(empty($jnsKunjungan)){
                        $jnsKunjungan = $this->getJenisKunjungan($bpjs);
                    }
                    if(isset($detailBpjs['assestmenPel']['kode']) && $detailBpjs['assestmenPel']['kode'] == BpjsConstans::TUJUAN_KONTROL){
                        $jnsKunjungan = '- Kunjungan Kontrol (ulangan)';
                        if(isset($detailBpjs['flagProcedure']['kode']) && $detailBpjs['flagProcedure']['kode'] == BpjsConstans::PROSEDUR_TIDAK_LANJUT){
                            $isAdditionKunjungan = true;
                            $jnsKunjunganAddition = isset($detailBpjs['flagProcedure']['nama']) && !empty($detailBpjs['flagProcedure']['nama']) ? "- " . $detailBpjs['flagProcedure']['nama'] : null;
                        }
                    }
                }else{
                    $jnsKunjungan = BpjsConstans::STRING_KUNJUNGAN_INTERNAL;
                }
            }
            $qrCode = (new QrCode(isset($detailBpjs['peserta']['noKartu']) ? $detailBpjs['peserta']['noKartu'] : ''))
            ->setSize(80)
            ->setMargin(3)
            ->useForegroundColor(0, 0, 0);

            if (isset($kunjungan->nama_pasien) && isset($kunjungan->no_rekam_medik) && isset($detailBpjs['noSep'])) {
                $fileName = $kunjungan->nama_pasien.'_'.$detailBpjs['noSep'].'_'.$kunjungan->no_rekam_medik.'.pdf';
                $print->docName = $fileName;
                $print->SetTitle($fileName);
            }

            $qrcode = '<img src="data:image/png;base64,' . base64_encode($qrCode->writeString()) . '">';
            $print->attributes = [
                '#sep#' => Yii::$app->controller->renderPartial($render_partial, get_defined_vars()),
                '#no_pendaftaran#' => !empty($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : null,
                '#poli_tujuan#' => !empty($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : null,
                '#display_dokter#' => $display_dokter

            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }
}
