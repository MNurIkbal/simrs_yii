<?php

namespace Extensions\pendaftaran;

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

class PrintSepSty extends \Doco\processes\PrintSepProcess
{
    public $modelClass = '';
    const JK_P = 'Perempuan';
    const JK_L = 'Laki-laki';

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
            $faskesKode = '-';
            $faskesNama = '-';
            $kelasRawat = '-';
            $kelasHak = '-';
            $dpjp = '-';
            $ktp = '-';
            $statusPasien = '-';
            $jnsKunjungan = '-';
            $penjamin_kll = '-';
            $render_partial = 'print_sep_sty';
            $showKelasRawat = true;
            $is_ranap = false;
            $prb = false;

            $kunjungan = $model::find()
            ->select(['bpjs_id', 'pasien_id', 'instalasi_id', 'status_pasien', 'additional_pasien', 'namadepan', 'nama_pasien', 'umur', 'nama_pegawai', 'no_pendaftaran', 'ruangan_nama', 'no_rekam_medik', 'penjamin_nama'])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['IS NOT', 'bpjs_id', null]);
            
            if ($ruangan_id == DocoConstants::RUANGAN_PENDAFTARAN_RANAP) {
                $kunjungan->andWhere(['IS NOT', 'pasienadmisi_id', null]);
            }

            $kunjungan = $kunjungan->one();
            
            if (!$kunjungan) {
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $bpjs = $modelBpjs::findOne($kunjungan->bpjs_id);

            if (empty($bpjs)) {
                throw new \yii\web\NotFoundHttpException("Data BPJS Tidak Ditemukan", 404);
            }

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
                if(empty($additional_data) && !empty($bpjs->nosep)) {
                    $getDataBpjs = $modelBpjs->referensiCariSep($bpjs->nosep);
                    $add['sep'] = $getDataBpjs['response'];
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

                if (array_key_exists('namafaskes_perujukterakhir', $getDataBpjs['response']['peserta'])) {
                    if (array_key_exists('nama', $getDataBpjs['response']['peserta']['namafaskes_perujukterakhir'])) {
                        $faskesNama = $getDataBpjs['response']['peserta']['namafaskes_perujukterakhir']['nama'];
                    }
                } else if (isset($bpjs->nama_ppk_perujuk) && !empty($bpjs->nama_ppk_perujuk)) {
                    $faskesNama = $bpjs->nama_ppk_perujuk;
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
            }

            //** Handle condition cetakan sep kosong karena nosep dari vclaim */
            if(empty($additional_data) && !empty($bpjs->nosep)) {
                $getDataBpjs = $modelBpjs->referensiCariSep($bpjs->nosep);
                $add['sep'] = $getDataBpjs['response'];
                $bpjs->additional_data = json_encode($add);
            }
            $bpjs->cetakan_ke += 1;
            $date = new \DateTime("now");
            $bpjs->tgl_cetak = $date->format('Y-m-d H:i:s');
            $bpjs->save(false);

            $detailBpjs = $additional_data['sep'];
            $kelasRawat = isset($detailBpjs['kelasRawat']) && $detailBpjs['kelasRawat'] != '-' ? $detailBpjs['kelasRawat'] : '-';
            $pattern = ['Kelas', 'kelas'];
            $kelasRawat = str_replace($pattern, '', $kelasRawat);

            if (!empty($detailBpjs['peserta']['hakKelas'])){
                $exp = explode("Kelas", $detailBpjs['peserta']['hakKelas']);
                $kelasHak = isset($exp[1]) && $exp[1] != '-' ? $exp[1] : '-';
            }

            if (isset($detailBpjs['peserta']['kelamin'])) {
                if ($detailBpjs['peserta']['kelamin'] == 'P') {
                    $detailBpjs['peserta']['kelamin'] = self::JK_P;
                } else {
                    $detailBpjs['peserta']['kelamin'] = self::JK_L;
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
                $faskesNama = '-';
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

            // $bpjs_sep = $modelBpjs->referensiCariSep($bpjs->nosep);
            // if ($bpjs_sep['metaData']['code'] == 200 || $bpjs_sep['metaData']['code'] == '200') {
            //     if (!empty($bpjs_sep['response'])) {
            //         if (!empty($bpjs_sep['response']['kdStatusKecelakaan'])) {
            //             if ($bpjs_sep['response']['kdStatusKecelakaan'] != 0 || $bpjs_sep['response']['kdStatusKecelakaan'] != '0') {
            //                 if ($bpjs_sep['response']['kdStatusKecelakaan'] == 1 || $bpjs_sep['response']['kdStatusKecelakaan'] == '1') { //KLL dan BKK
            //                     $penjamin_kll = '1. Jasa Raharja, 2. BPJS Kesehatan';
            //                 } 
                            
            //                 if ($bpjs_sep['response']['kdStatusKecelakaan'] == 2 || $bpjs_sep['response']['kdStatusKecelakaan'] == '2') { //KLL dan KK
            //                     if ($bpjs_sep['response']['peserta']['jnsPeserta'] == "PEGAWAI SWASTA") {
            //                         $penjamin_kll = '1. Jasa Raharja, 2. BPJS Ketenagakerjaan';
            //                     } else {
            //                         $penjamin_kll = '1. Jasa Raharja, 2. Taspen';
            //                     }
                                
            //                 }
            //                 if ($bpjs_sep['response']['kdStatusKecelakaan'] == 3 || $bpjs_sep['response']['kdStatusKecelakaan'] == '3') { //KL
            //                     if ($bpjs_sep['response']['peserta']['jnsPeserta'] == "PEGAWAI SWASTA") {
            //                         $penjamin_kll = '1. Jasa Raharja, 2. BPJS Ketenagakerjaan';
            //                     } else {
            //                         $penjamin_kll = '1. Jasa Raharja, 2. Taspen';
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // }
            
            /** Get nama Dokter DPJP */	
            if(isset($bpjs->nama_dpjp_melayani) && !empty($bpjs->nama_dpjp_melayani)) {
                $dpjp = $bpjs->nama_dpjp_melayani;
            } else if($bpjs->additional_request && !empty($bpjs->additional_request)) {	
                $arrRequest = json_decode($bpjs->additional_request, true);	
                $kodeDokter = '';	
                $dpjpServe = $bpjs->kode_dpjp_melayani;
                $param1 = BpjsConstans::PELAYANAN_RAWAT_INAP; 	
                $param2 = date('Y-m-d', strtotime($bpjs->tglsep));	
                $param3 = BpjsConstans::STRING_IGD;	

                if(!empty($dpjpServe)) {
                    $kodeDokter = (int) $dpjpServe;
                }

                if ($kunjungan->instalasi_id == DocoConstants::INST_ID_RI) {
                    $faskesNama = '-';
                    $is_ranap = true;
                    $kodeDokter = (int) $bpjs->kode_dpjp_spri;
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
                
                if ($kunjungan->instalasi_id == DocoConstants::INST_ID_RI) {
                    $faskesNama = '-';
                    $is_ranap = true;
                    $dpjp = $bpjs->nama_dpjp_spri;
                }
            }	

            if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_PROSEDUR) {
                $jnsKunjungan = $bpjs->flag_procedure == BpjsConstans::PROSEDUR_TIDAK_LANJUT ? BpjsConstans::STRING_PROSEDUR_TIDAK_LANJUT : BpjsConstans::STRING_PROSEDUR_LANJUT;
            } else if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_KONSUL) {
                $jnsKunjungan = BpjsConstans::STRING_KONSUL_DOKTER;
            } else if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_NORMAL) {
                $jnsKunjungan = BpjsConstans::STRING_TUJUAN_NORMAL;
            } else if ($bpjs->tujuan_kunj == BpjsConstans::TUJUAN_KONTROL) {
                $jnsKunjungan = BpjsConstans::STRING_TUJUAN_KONTROL;
            }   

            if (isset($kunjungan->status_pasien)) {
                $statusPasien = Lookup::findOne($kunjungan->status_pasien);
            }

            if (isset($kunjungan->additional_pasien) && !empty($kunjungan->additional_pasien)) {
                $additionalPasien = json_decode($kunjungan->additional_pasien);

                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == Dococonstants::CONS_ID_KTP) {
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