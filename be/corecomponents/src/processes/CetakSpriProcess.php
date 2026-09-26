<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\InfoPasienPulangRjRd;
use app\modules\v1\models\CpptRjV;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\InfoPasienRi;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KelasPelayanan;
use GuzzleHttp\Client;

class CetakSpriProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected function processFlow()
    {
        $request = Yii::$app->request;
        $type = $request->get('type','');
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $prev_pendaftaran_id = $request->get('prev_no_pendaftaran',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $pegawai_id = $request->get('pegawai_id',0);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id',0);
        $nama_usercetak = $request->get('nama_usercetak','');
        $id_usercetak = $request->get('id_usercetak',0);
        // try {
            $qry = new PasienPulang;
            $checked = '<input checked="checked" type="checkbox" />';
            $non_checked = '<input type="checkbox" />';
            $konfirmasi_ruangan = null;
            if($type == 'rj'){
                    $pPulang = $qry::find()->Select([
                        'pasienpulang_t.pendaftaran_id',
                        'pasienpulang_t.dpjp_id',
                        'pasienpulang_t.tempattidurtujuan_id',
                        'pasienpulang_t.infeksi',
                        'pendaftaran_t.kelaspelayanan_id',
                        'pendaftaran_t.status_periksa',
                        'kelaspelayanan_m.kelaspelayanan_nama',
                        'kelaspelayanan_m.kelaspelayanan_id',
                    ])
                    ->join('JOIN', 'pendaftaran_t', "pendaftaran_t.pendaftaran_id=$pendaftaran_id")
                    ->join('JOIN', 'kelaspelayanan_m', "kelaspelayanan_m.kelaspelayanan_id=pendaftaran_t.kelaspelayanan_id")
                    ->andWhere([
                        'pasienpulang_t.pendaftaran_id'=>$pendaftaran_id
                    ])->asArray()->one();

                    $order_operasi = $qry::find()->Select([
                        'rencanaoperasi_t.jenis_operasi_cyto',
                        'rencanaoperasi_t.jenis_operasi_elektif',
                        'rencanaoperasi_t.jenis_operasi_odc',
                    ])
                    ->join('JOIN', 'rencanaoperasi_t', 'rencanaoperasi_t.pendaftaran_id=pasienpulang_t.pendaftaran_id')
                    ->andWhere([
                        'pasienpulang_t.pendaftaran_id'=>$pendaftaran_id
                    ])->asArray()->one();

                    $klasifikasi_ruangan['klasifikasi_ruangan_id'] = null;
                    $tempattujuan= $pPulang['tempattidurtujuan_id'];
                    if($pPulang['tempattidurtujuan_id']!=null){
                        $klasifikasi_ruangan = $qry::find()->Select([
                            'kamartempattidur_m.kamarruangan_id',
                            'kamarruangan_m.ruangan_id',
                            'ruangan_m.klasifikasi_ruangan_id',
                            'ruangan_m.ruangan_nama',
                            'jenis_kamar.lookup_name AS kamarruangan_jenis_nama',
                        ])
                        ->join('JOIN', 'kamartempattidur_m', "kamartempattidur_m.kamartempattidur_id=$tempattujuan")
                        ->join('JOIN', 'kamarruangan_m', "kamarruangan_m.kamarruangan_id=kamartempattidur_m.kamarruangan_id")
                        ->join('JOIN', 'ruangan_m', "ruangan_m.ruangan_id=kamarruangan_m.ruangan_id")
                        ->join('LEFT JOIN', 'lookup_m as jenis_kamar', "jenis_kamar.lookup_id=kamarruangan_m.kamarruangan_jenis")
                        ->asArray()->one();

                        if($pPulang['status_periksa'] != 1058 ){
                            $konfirmasi_ruangan = $qry::find()->Select([
                                'ketersediaankamar_r.ruangan_id',
                                'ketersediaankamar_r.kelaspelayanan_id',
                                'ketersediaankamar_r.kamarruangan_id',
                                'ketersediaankamar_r.kamartempattidur_id',
                                'kelaspelayanan_m.kelaspelayanan_nama',
                                'kamarruangan_m.kamarruangan_nokamar',
                                'kamartempattidur_m.no_tempattidur',
                                'ruangan_m.ruangan_nama',
                            ])
                            ->join('JOIN', 'ketersediaankamar_r', "ketersediaankamar_r.pendaftaran_id=$pendaftaran_id")
                            ->join('JOIN', 'ruangan_m', "ruangan_m.ruangan_id=ketersediaankamar_r.ruangan_id")
                            ->innerJoin('kelaspelayanan_m', "kelaspelayanan_m.kelaspelayanan_id=ketersediaankamar_r.kelaspelayanan_id")
                            ->innerJoin('kamarruangan_m', "kamarruangan_m.kamarruangan_id=ketersediaankamar_r.kamarruangan_id")
                            ->innerJoin( 'kamartempattidur_m', "kamartempattidur_m.kamartempattidur_id=ketersediaankamar_r.kamartempattidur_id")
                            ->andWhere([
                                'pasienpulang_t.pendaftaran_id'=>$pendaftaran_id
                            ])->orderBy([
                                'ketersediaankamar_r.created_date' => SORT_DESC,
                              ])->asArray()->one();
                        }
                    }
                    $pulang = $qry::find()->Select([
                        'ruanganakhir_id',
                        'ruangan_m.instalasi_id'
                    ])->join('JOIN', 'ruangan_m', 'ruangan_m.ruangan_id=pasienpulang_t.ruanganakhir_id')
                        ->andWhere([
                            'pendaftaran_id'=>$pendaftaran_id
                        ])->asArray()->one();


                    $modelHeader = new InfoPasienPulangRjRd;
                    $queryHeader = $modelHeader::find()
                        ->andWhere([
                            'pendaftaran_id'=>$request->get('pendaftaran_id')
                        ]);
                    $resultHeader = $queryHeader->asArray()->one();
                    $dokter_merawat = $resultHeader['dokter'];
                    $diagnosa_asesmen = CpptRjV::find()->select([
                        'a_diag_utama',
                        'tgl_soaprj',
                    ])
                    ->Where([
                        'pegawai_id'=>$resultHeader['pegawai_id'],
                        'pendaftaran_id'=>$pendaftaran_id,
                    ])->asArray()->all();
                        $arr= [];
                        $diagnosis_masuk = ' - ';
                        if(!empty($diagnosa_asesmen)){
                            foreach ($diagnosa_asesmen as $key => $value) {
                                if(empty($arr)){
                                    $arr = $value['a_diag_utama'];
                                }
                            }
                            $diagnosa = json_decode($arr);
                            $diagnosis_masuk = $diagnosa->text == '' ? ' - ' : $diagnosa->text ;
                        }

            }else if($type == 'rd'){
                $pPulang = $qry::find()->Select([
                    'pasienpulang_t.pendaftaran_id',
                    'pasienpulang_t.dpjp_id',
                    'pasienpulang_t.tempattidurtujuan_id',
                    'pasienpulang_t.infeksi',
                    'pegawai_m.nama_pegawai',
                    'pendaftaran_t.kelaspelayanan_id',
                    'pendaftaran_t.status_periksa',
                    'kelaspelayanan_m.kelaspelayanan_nama',
                    'kelaspelayanan_m.kelaspelayanan_id',
                ])
                ->join('JOIN', 'pegawai_m', 'pegawai_m.pegawai_id=pasienpulang_t.dpjp_id')
                ->join('JOIN', 'pendaftaran_t', "pendaftaran_t.pendaftaran_id=$pendaftaran_id")
                ->join('JOIN', 'kelaspelayanan_m', "kelaspelayanan_m.kelaspelayanan_id=pendaftaran_t.kelaspelayanan_id")
                ->andWhere([
                    'pasienpulang_t.pendaftaran_id'=>$pendaftaran_id
                ])->asArray()->one();

                $order_operasi = $qry::find()->Select([
                    'rencanaoperasi_t.jenis_operasi_cyto',
                    'rencanaoperasi_t.jenis_operasi_elektif',
                    'rencanaoperasi_t.jenis_operasi_odc',
                ])
                ->join('JOIN', 'rencanaoperasi_t', 'rencanaoperasi_t.pendaftaran_id=pasienpulang_t.pendaftaran_id')
                ->andWhere([
                    'pasienpulang_t.pendaftaran_id'=>$pendaftaran_id
                ])->asArray()->one();
                $dokter_merawat = $pPulang['nama_pegawai'];
                $klasifikasi_ruangan['klasifikasi_ruangan_id'] = null;
                $tempattujuan= $pPulang['tempattidurtujuan_id'];

                if($pPulang['tempattidurtujuan_id']!=null){
                   $klasifikasi_ruangan = $qry::find()->Select([
                            'kamartempattidur_m.kamarruangan_id',
                            'kamarruangan_m.ruangan_id',
                            'ruangan_m.klasifikasi_ruangan_id',
                            'ruangan_m.ruangan_id',
                            'ruangan_m.ruangan_nama',
                            'jenis_kamar.lookup_name AS kamarruangan_jenis_nama',
                        ])
                        ->join('JOIN', 'kamartempattidur_m', "kamartempattidur_m.kamartempattidur_id=$tempattujuan")
                        ->join('JOIN', 'kamarruangan_m', "kamarruangan_m.kamarruangan_id=kamartempattidur_m.kamarruangan_id")
                        ->join('JOIN', 'ruangan_m', "ruangan_m.ruangan_id=kamarruangan_m.ruangan_id")
                        ->join('LEFT JOIN', 'lookup_m as jenis_kamar', "jenis_kamar.lookup_id=kamarruangan_m.kamarruangan_jenis")
                        ->asArray()->one();

                    if($pPulang['status_periksa'] != 1058 ){
                    $konfirmasi_ruangan = $qry::find()->Select([
                        'ketersediaankamar_r.ruangan_id',
                        'ketersediaankamar_r.kelaspelayanan_id',
                        'ketersediaankamar_r.kamarruangan_id',
                        'ketersediaankamar_r.kamartempattidur_id',
                        'kelaspelayanan_m.kelaspelayanan_nama',
                        'kamarruangan_m.kamarruangan_nokamar',
                        'kamartempattidur_m.no_tempattidur',
                        'ruangan_m.ruangan_nama',
                    ])
                    ->join('JOIN', 'ketersediaankamar_r', "ketersediaankamar_r.pendaftaran_id=$pendaftaran_id")
                    ->join('JOIN', 'ruangan_m', "ruangan_m.ruangan_id=ketersediaankamar_r.ruangan_id")
                    ->innerJoin('kelaspelayanan_m', "kelaspelayanan_m.kelaspelayanan_id=ketersediaankamar_r.kelaspelayanan_id")
                    ->innerJoin('kamarruangan_m', "kamarruangan_m.kamarruangan_id=ketersediaankamar_r.kamarruangan_id")
                    ->innerJoin( 'kamartempattidur_m', "kamartempattidur_m.kamartempattidur_id=ketersediaankamar_r.kamartempattidur_id")
                    ->andWhere([
                        'pasienpulang_t.pendaftaran_id'=>$pendaftaran_id
                    ])->orderBy([
                        'ketersediaankamar_r.created_date' => SORT_DESC,
                      ])->asArray()->one();
                    }
                }
                $pulang = $qry::find()->Select([
                    'ruanganakhir_id',
                    'ruangan_m.instalasi_id',
                    'ruangan_m.klasifikasi_ruangan_id'
                ])->join('JOIN', 'ruangan_m', 'ruangan_m.ruangan_id=pasienpulang_t.ruanganakhir_id')
                    ->andWhere([
                        'pendaftaran_id'=>$pendaftaran_id
                    ])->asArray()->one();

                $diagnosa_asesmen = AsesmenMedisRD::find()->select([
                    'asesmenmedisrd_t.diagnosa_id',
                    'diagnosa_m.diagnosa_nama',
                ])
                ->join('JOIN', 'diagnosa_m', 'diagnosa_m.diagnosa_id=asesmenmedisrd_t.diagnosa_id')
                ->andWhere([
                    'pendaftaran_id'=>$pendaftaran_id
                ])->asArray()->one();

                $diagnosis_masuk = isset($diagnosa_asesmen['diagnosa_nama']) ? $diagnosa_asesmen['diagnosa_nama'] : ' - ';
                $modelHeader = new InfoPasienRdV;
                $queryHeader = $modelHeader::find()
                    ->andWhere([
                        'pendaftaran_id'=>$request->get('pendaftaran_id')
                    ]);
                $resultHeader = $queryHeader->asArray()->one();

            }else if($type == 'ri'){
                    $pPulang = $qry::find()->Select([
                        'pasienpulang_t.pendaftaran_id',
                        'pasienpulang_t.dpjp_id',
                        'pasienpulang_t.tempattidurtujuan_id',
                        'pasienpulang_t.infeksi',
                        'pendaftaran_t.kelaspelayanan_id',
                        'pendaftaran_t.status_periksa',
                        'kelaspelayanan_m.kelaspelayanan_nama',
                        'kelaspelayanan_m.kelaspelayanan_id',
                    ])
                    ->join('JOIN', 'pendaftaran_t', "pendaftaran_t.pendaftaran_id=$pendaftaran_id")
                    ->join('JOIN', 'kelaspelayanan_m', "kelaspelayanan_m.kelaspelayanan_id=pendaftaran_t.kelaspelayanan_id")
                    ->andWhere([
                        'pasienpulang_t.pendaftaran_id'=>$prev_pendaftaran_id
                    ])->asArray()->one();

                    $order_operasi = $qry::find()->Select([
                        'rencanaoperasi_t.jenis_operasi_cyto',
                        'rencanaoperasi_t.jenis_operasi_elektif',
                        'rencanaoperasi_t.jenis_operasi_odc',
                    ])
                    ->join('JOIN', 'rencanaoperasi_t', 'rencanaoperasi_t.pendaftaran_id=pasienpulang_t.pendaftaran_id')
                    ->andWhere([
                        'pasienpulang_t.pendaftaran_id'=>$prev_pendaftaran_id
                    ])->asArray()->one();

                    $klasifikasi_ruangan['klasifikasi_ruangan_id'] = null;
                    $tempattujuan= $pPulang['tempattidurtujuan_id'];
                    if($pPulang['tempattidurtujuan_id']!=null){
                        $klasifikasi_ruangan = $qry::find()->Select([
                            'kamartempattidur_m.kamarruangan_id',
                            'kamarruangan_m.ruangan_id',
                            'ruangan_m.klasifikasi_ruangan_id',
                            'ruangan_m.ruangan_nama',
                            'jenis_kamar.lookup_name AS kamarruangan_jenis_nama',
                        ])
                        ->join('JOIN', 'kamartempattidur_m', "kamartempattidur_m.kamartempattidur_id=$tempattujuan")
                        ->join('JOIN', 'kamarruangan_m', "kamarruangan_m.kamarruangan_id=kamartempattidur_m.kamarruangan_id")
                        ->join('JOIN', 'ruangan_m', "ruangan_m.ruangan_id=kamarruangan_m.ruangan_id")
                        ->join('LEFT JOIN', 'lookup_m as jenis_kamar', "jenis_kamar.lookup_id=kamarruangan_m.kamarruangan_jenis")
                        ->asArray()->one();

                        if($pPulang['status_periksa'] != 1058 ){
                            $konfirmasi_ruangan = $qry::find()->Select([
                                'ketersediaankamar_r.ruangan_id',
                                'ketersediaankamar_r.kelaspelayanan_id',
                                'ketersediaankamar_r.kamarruangan_id',
                                'ketersediaankamar_r.kamartempattidur_id',
                                'kelaspelayanan_m.kelaspelayanan_nama',
                                'kamarruangan_m.kamarruangan_nokamar',
                                'kamartempattidur_m.no_tempattidur',
                                'ruangan_m.ruangan_nama',
                            ])
                            ->join('JOIN', 'ketersediaankamar_r', "ketersediaankamar_r.pendaftaran_id=$pendaftaran_id")
                            ->join('JOIN', 'ruangan_m', "ruangan_m.ruangan_id=ketersediaankamar_r.ruangan_id")
                            ->innerJoin('kelaspelayanan_m', "kelaspelayanan_m.kelaspelayanan_id=ketersediaankamar_r.kelaspelayanan_id")
                            ->innerJoin('kamarruangan_m', "kamarruangan_m.kamarruangan_id=ketersediaankamar_r.kamarruangan_id")
                            ->innerJoin( 'kamartempattidur_m', "kamartempattidur_m.kamartempattidur_id=ketersediaankamar_r.kamartempattidur_id")
                            ->andWhere([
                                'pasienpulang_t.pendaftaran_id'=>$prev_pendaftaran_id
                            ])->orderBy([
                                'ketersediaankamar_r.created_date' => SORT_DESC,
                              ])->asArray()->one();
                        }
                    }
                    $pulang = $qry::find()->Select([
                        'ruanganakhir_id',
                        'ruangan_m.instalasi_id'
                    ])->join('JOIN', 'ruangan_m', 'ruangan_m.ruangan_id=pasienpulang_t.ruanganakhir_id')
                        ->andWhere([
                            'pendaftaran_id'=>$prev_pendaftaran_id
                        ])->asArray()->one();

                    $modelHeader = new InfoPasienRi;
                    $queryHeader = $modelHeader::find()
                        ->andWhere([
                            'pendaftaran_id'=>$request->get('pendaftaran_id')
                        ]);
                    $resultHeader = $queryHeader->asArray()->one();


                    $dokter_merawat = $resultHeader['dokter_admisi'];
                    $diagnosa_asesmen = AsesmenMedisRD::find()->select([
                        'asesmenmedisrd_t.diagnosa_id',
                        'diagnosa_m.diagnosa_nama',
                    ])
                    ->join('JOIN', 'diagnosa_m', 'diagnosa_m.diagnosa_id=asesmenmedisrd_t.diagnosa_id')
                    ->andWhere([
                        'pendaftaran_id'=>$pendaftaran_id
                    ])->asArray()->one();

                    $diagnosis_masuk = isset($diagnosa_asesmen['diagnosa_nama']) ? $diagnosa_asesmen['diagnosa_nama'] : ' - ';
            }

            if(!isset($klasifikasi_ruangan['kamarruangan_jenis_nama'])){
                $klasifikasi_ruangan = $qry::find()->Select([
                        'jenis_kamar.lookup_name AS kamarruangan_jenis_nama',
                    ])->andWhere([
                        'pasienpulang_t.pendaftaran_id'=>$pendaftaran_id
                    ])->join('LEFT JOIN', 'lookup_m as jenis_kamar', "jenis_kamar.lookup_id=pasienpulang_t.kamarruangan_jenis")
                    ->asArray()->one();
            }


            // LEFT JOIN lookup_m jenis_kamar ON ((COALESCE(pasienpulang_t.kamarruangan_jenis, kamarkamarruangan_m.kamarruangan_jenis) = jenis_kamar.lookup_id)))


                //infeksi
            $infeksi = $pPulang['infeksi'] == 'inf' ? $checked.'&emsp;Infeksi' : $non_checked.'&emsp;Infeksi';
            $non_infeksi =$pPulang['infeksi'] == 'non_inf' ?$checked.'&emsp;Non Infeksi' : $non_checked.'&emsp;Non Infeksi';
            $infeksi_form = $infeksi.'&emsp;'.$non_infeksi;

                //asal ruangan
            $igd = isset($pulang['instalasi_id']) && $pulang['instalasi_id'] == 2? $checked.' IGD' : $non_checked.' IGD';
            $poliklinik = isset($pulang['instalasi_id']) && $pulang['instalasi_id'] == 1? $checked.' COE/Poliklinik ' : $non_checked.' COE/Poliklinik';
            $ldr = isset($pulang['klasifikasi_ruangan_id']) && $pulang['klasifikasi_ruangan_id'] == 1066? $checked.' LDR' : $non_checked.' LDR';
            $asal = $igd.'&emsp;&emsp;'.$poliklinik.'&emsp;&emsp;'.$ldr;

                //jenis operasi cito
            $cyto = isset($order_operasi) && $order_operasi['jenis_operasi_cyto'] == true ? $checked.'&emsp;Cito' : $non_checked.'&emsp;Cito';
            $elektif = isset($order_operasi) && $order_operasi['jenis_operasi_elektif'] == true || isset($order_operasi) && $order_operasi['jenis_operasi_odc'] == true ? $checked.'&emsp;Elektif' : $non_checked.'&emsp;Elektif';
            $rencana_operasi = $cyto.'&emsp;&emsp;'.$elektif;

                //ruangan tujuan
            $umum     = preg_match('/rawat inap/i', $klasifikasi_ruangan['kamarruangan_jenis_nama']) ? $checked.'&nbsp;Umum' : $non_checked.'&nbsp;Umum';
            $khusus     = preg_match('/icu|iccu|nicu|picu|isolasi/i', $klasifikasi_ruangan['kamarruangan_jenis_nama'])  ? $checked.'&nbsp;Khusus' : $non_checked.'&nbsp;Khusus';
            $isolasi     = preg_match('/isolasi/i', $klasifikasi_ruangan['kamarruangan_jenis_nama']) ? $checked.' Isolasi' : $non_checked.' Isolasi';
            $icu     = preg_match('/icu/i', $klasifikasi_ruangan['kamarruangan_jenis_nama']) && !preg_match('/nicu/i', $klasifikasi_ruangan['kamarruangan_jenis_nama']) ? $checked.' ICU' : $non_checked.' ICU';
            $iccu     = preg_match('/iccu/i', $klasifikasi_ruangan['kamarruangan_jenis_nama']) ? $checked.' ICCU' : $non_checked.' ICCU';
            $nicu     = preg_match('/nicu/i', $klasifikasi_ruangan['kamarruangan_jenis_nama'])  ? $checked.' NICU/PICU' : $non_checked.' NICU/PICU';
            $lainnya     =  !preg_match('/icu|iccu|nicu|picu|isolasi|rawat inap/i', strtolower($klasifikasi_ruangan['kamarruangan_jenis_nama'])) ? $checked.'&nbsp;Lainnya' : $non_checked.'&nbsp;Lainnya';
            $ruangan = $umum.' : <br><br>'.$khusus.' :&emsp;&emsp;'.$isolasi.'&emsp;&emsp;'.$icu.'&emsp;&emsp;'.$iccu.'&emsp;&emsp;'.$nicu.'&nbsp;<br><br>'.$lainnya;


            $pegawailogin_id   = Yii::$app->jwt->user->pegawai_id;
            $pegawailogin      = Pegawai::findOne($pegawailogin_id);
            $pegawailogin_nama = $pegawailogin->nama_pegawai;
            $waktu_cetak       = date('d F Y');

            $kls_p = KelasPelayanan::find()->Select([
                'kelaspelayanan_id',
                'kelaspelayanan_nama',
            ])->Where([
                'is_active' => true,
                'is_deleted' => false,
            ])
            ->orderBy(['kelaspelayanan_nama'=> SORT_ASC])
            ->asArray()->all();
            $space = '&emsp;';
            $html = '<table style="font-size:14px">';
            $html .= '<tr>&emsp;';
            $no = 1;
            $check = '<input checked="checked" type="checkbox" />';
            $len = count($kls_p);
            if(isset($klasifikasi_ruangan['ruangan_id']) &&  $klasifikasi_ruangan['ruangan_id'] != (isset($konfirmasi_ruangan['ruangan_id']) ? $konfirmasi_ruangan['ruangan_id'] : null)){
                $row1 = $checked.'Saat ini,';
            }else{
                $check = '<input type="checkbox" />';
                $row1 = $non_checked.' Saat ini,';
            }
            foreach ($kls_p as $key => $value) {
                if($no % 4 == 0){
                    $html .= '<td>';
                    $html .= $value['kelaspelayanan_id'] == $pPulang['kelaspelayanan_id'] ? $check.'&nbsp;'.$value['kelaspelayanan_nama'] : '<td>'.$non_checked.'&nbsp;'.$value['kelaspelayanan_nama'];
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '<tr>&emsp;';
                }else{
                    $html .= '<td>';
                    $html .= $value['kelaspelayanan_id'] == $pPulang['kelaspelayanan_id'] ? $check.'&nbsp;'.$value['kelaspelayanan_nama']: $non_checked.'&nbsp;'.$value['kelaspelayanan_nama'];
                    $html .= '</td>';
                }

                if ($no == $len){
                    $html .= '<td>';
                    $html .=strpos($html,'checked')!== false ? $non_checked.'&nbsp; Lainnya' : $check.'&nbsp; Lainnya';
                    $html .= '</td>';
                    $html .= '</table>';
                }
                $no++;
            }

           $dirawat = (isset($konfirmasi_ruangan['ruangan_id']) )? $checked.'Pasien bisa dirawat di ruang rawat' : $non_checked.'Pasien bisa dirawat di ruang rawat';
           $pasien_akan_dirujuk = '<input type="checkbox"/>&nbsp;Pasien akan di RUJUK<br>
                                    <input type="checkbox" />&nbsp;Pasien tetap di IGD dengan surat pernyataan<br>
                                    <input type="checkbox" />&nbsp;Mohon untuk didaftarkan sebagai pasien daftar tunggu / Reservasi.';

            $saat_ini = '<input type="checkbox" />&nbsp;Saat ini, Tanggal Jam:    WIB<br>
                        <input type="checkbox" />&nbsp;Pasien Bisa dirawat di ruang rawat : <br><br>
                        Kelas : &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Kamar No. : ';

            $kelas   = (isset($konfirmasi_ruangan['ruangan_id'])  )? isset($konfirmasi_ruangan['kelaspelayanan_nama'])?$konfirmasi_ruangan['kelaspelayanan_nama'] : ' - ' : ' - ';
            $kamar = isset($konfirmasi_ruangan['kamarruangan_nokamar']) && isset($konfirmasi_ruangan['no_tempattidur'])? $konfirmasi_ruangan['kamarruangan_nokamar'].' - '.$konfirmasi_ruangan['no_tempattidur'] : ' - ';

            $print = new DocoPrint();
            $print->attributes = [
                '#operasi#' => isset($rencana_operasi) ? $rencana_operasi : null,
                '#diagnosis_masuk#' => isset($diagnosis_masuk) ? $diagnosis_masuk : ' - ',
                '#dokter_merawat#' => isset($dokter_merawat) ? $dokter_merawat : ' - ',
                '#infeksi#' => $infeksi_form,
                '#asal#' => $asal,
                '#ruangan#' => $ruangan,
                '#inf_namapasien#' => $resultHeader ? @$resultHeader['nama_pasien'] : '',
                '#inf_norekammedik#' => $resultHeader ? @$resultHeader['no_rekam_medik'] : '',
                '#inf_jeniskelamin#' => $resultHeader ? @$resultHeader['jenis_kelamin'] : '',
                '#inf_tgllahir#' => $resultHeader ? (isset($resultHeader['tanggal_lahir']) ? date('d/m/Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
                // '#hari_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s"), 'w'),
                '#tanggal_skr#' => date('d F Y'),
                '#inf_dokterdpjp#' => $resultHeader ? @$resultHeader['dokter'] : '',
                '#row1#' => $row1,
                '#line1#' => $html,
                '#dirawat#' => $dirawat,
                '#pasien_akan_dirujuk#' => $pasien_akan_dirujuk,
                '#saat_ini#' => $saat_ini,
                '#kelas#' => $kelas,
                '#kamar#' => $kamar,
                '#nama_pegawai#' => $pegawailogin_nama,
                '#timestamps#' => $waktu_cetak,
                '#untuk#' => $html,
                '#space#' => $space,
                '#jam#' => date("h:i:s"),
            ];
            $print->Output();
        // } catch (\yii\db\Exception $e) {
        //     throw new \Exception($e->getMessage(), 1);
        // } catch (\Exception $e) {
        //     throw new \Exception($e->getMessage(), 1);
        // }
    }

}