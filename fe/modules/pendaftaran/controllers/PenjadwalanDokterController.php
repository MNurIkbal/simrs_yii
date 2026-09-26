<?php
// author : ardi


namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use kartik\widgets\ActiveForm;
use app\modules\pendaftaran\models\JadwalDokterForm;
use DateTime;

class PenjadwalanDokterController extends DocoController
{
    protected $_title = "Jadwal dokter";
    protected $_module = 'pendaftaran/penjadwalan-dokter/';
    protected $_restPendaftaran;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_status = [
            Yii::t('fe', 'Tidak aktif'),
            Yii::t('fe', 'Aktif')
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    // public function actionIndex() 
    // {
    //     // Define params
    //     // $post = [];

    //     // // Check ajax
    //     // if (Yii::$app->request->isAjax) {
    //     //     // Get params
    //     //     $post = Yii::$app->request->post();

    //     //     // Remove AM and PM
    //     //     if (isset($post['jam_mulai']) && $post['jam_mulai'] != '') {
    //     //         // Replace
    //     //         $post['jam_mulai'] = str_replace(" AM", "", $post['jam_mulai']);
    //     //         $post['jam_mulai'] = str_replace(" PM", "", $post['jam_mulai']);
    //     //     }

    //     //     // Remove AM and PM
    //     //     if (isset($post['jam_selesai']) && $post['jam_selesai'] != '') {
    //     //         // Replace
    //     //         $post['jam_selesai'] = str_replace(" AM", "", $post['jam_selesai']);
    //     //         $post['jam_selesai'] = str_replace(" PM", "", $post['jam_selesai']);
    //     //     }
    //     // }

    //     // // Define model
    //     // $modelJadwalDokter = new JadwalDokterForm;

    //     // $request = Yii::$app->request;
    //     // if ($request->post()) {
    //     //     $modelJadwalDokter->load($request->post());
    //     // }

    //     // $res = $this->_restPendaftaran->get('penjadwalan-dokter/ajax-instalasi');
    //     // $body = json_decode($res->getBody(), TRUE);
    //     // $list_instalasi = $body['response']['data'];

    //     // $res = $this->_restPendaftaran->get('penjadwalan-dokter/ajax-ruangan');
    //     // $body = json_decode($res->getBody(), TRUE);
    //     // $list_poli = $body['response']['data'];

    //     // // Get list ruangan
    //     // $res = $this->_restPendaftaran->get('penjadwalan-dokter/ajax-ruangan-non-poli');
    //     // $body = json_decode($res->getBody(), TRUE);
    //     // $listRuangan = $body['response']['ruangan'];

    //     // // Get list dokter
    //     // $res = $this->_restPendaftaran->get('penjadwalan-dokter/ajax-dokter');
    //     // $body = json_decode($res->getBody(), TRUE);
    //     // $listDokter = $body['response']['dokter'];
        
    //     // $listHari = $this->listHari();
    //     // $listJam = $this->listJam();
    //     // $listShift = $this->listShift();
    //     // $list = $this->refactorData($post);
    //     $modelJadwalDokter = new JadwalDokterForm;
        
    //     $response = $this->_restPendaftaran->get('allow/list-instalasi');
    //     $body = json_decode($response->getBody(), True);
    //     $listInstalasi = $body['response']['data'];
    //     $listInstalasi = ArrayHelper::map($listInstalasi, 'instalasi_id', 'instalasi_nama');
    //     foreach ($listInstalasi as $key => $value) {
    //         if($key != 1 && $key != 2){
    //             unset($listInstalasi[$key]);
    //         }
    //     }

    //     // $response = $this->_restPendaftaran->get('allow/list-dokter-rajal');
    //     // $body = json_decode($response->getBody(), True);
    //     // $listDokter = $body['response'];
    //     $listDokter = [];
        
    //     $listHari = $this->listHari();
    //     $listJam = $this->listJam();
    //     return $this->render('jadwal', get_defined_vars());
    // }
    
    public function actionIndex() 
    {
        $modelJadwalDokter = new JadwalDokterForm;
        
        $instalasiList = [DocoConstants::INSTALASI_ID_RJ, DocoConstants::INSTALASI_ID_RD, DocoConstants::INSTALASI_ID_RI];
        $instalasiList = (count($instalasiList) > 0 ) ? array_merge($instalasiList, DocoConstants::INSTALASI_ID_PENUNJANG) : [];
        $arr = ['list_instalasi'=>['actionListInstalasi', $instalasiList]];
        $response = $this->_restMaster->post('allow/loop-aksi', ['form_params'=>$arr]);
        $body = json_decode($response->getBody(), True);
        $listInstalasi = (count($body['response']['list_instalasi']['data']) > 0) ? ArrayHelper::map($body['response']['list_instalasi']['data'], 'instalasi_id', 'instalasi_nama') : [];
        $listHari = $this->listHari();
        $listJam = $this->listJam();

        return $this->render('index', get_defined_vars());
    }

    public function find($id)
    {
        try {
            return [
                'response'=>[
                    'jadwaldokter_id'=>1,
                    'pegawai_id'=>1,
                    'instalasi_id'=>1,
                    'ruangan_id'=>1,
                    'jadwaldokter_hari'=>75,
                    'jadwaldokter_mulai'=>'07:00',
                    'jadwaldokter_tutup'=>'10:00',
                    'maximumantrian'=>10,
                    'is_active'=>true,
                ]
            ];
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    // public function refactorData($instalasi_id=null, $ruangan_id=null, $pegawai_id=null, $hari_filt = null, $jam_mulai=null, $jam_selesai=null) {
    //     $data = $this->getData($instalasi_id, $ruangan_id, $pegawai_id, $hari_filt, $jam_mulai, $jam_selesai);

    //     $listHari = $this->listHari();
    //     $list = [];
    //     foreach ($data['data'] as $key => $jadwalDokter) {
    //         $namaRuangan = $jadwalDokter['ruangan_nama'];
    //         $namaPegawai = $jadwalDokter['nama_pegawai'];
    //         $namaHari = $jadwalDokter['jadwaldokter_hari'];
    //         // $namaHari = $jadwalDokter['lookup_hari']['lookup_name'];

    //         if (!isset($list[$namaRuangan])) {
    //             $list[$namaRuangan] = [];
    //         }
    //         if (!isset($list[$namaRuangan][$namaPegawai])) {
    //             $list[$namaRuangan][$namaPegawai] = []; 
    //         }
    //         foreach ($listHari as $keyHari => $hari) {
    //             if (!isset($list[$namaRuangan][$namaPegawai][$hari])) {
    //                 $list[$namaRuangan][$namaPegawai][$hari] = [];
    //                 // $list[$namaRuangan][$namaPegawai][$hari] = [
    //                 //     'instalasi_id' => $jadwalDokter['instalasi_id'],
    //                 //     'ruangan_id' => $jadwalDokter['ruangan_id'],
    //                 //     'pegawai_id' => $jadwalDokter['pegawai_id'],
    //                 //     'hari_id' => $keyHari,
    //                 // ];
    //             }
                
    //         }
    //         $list[$namaRuangan][$namaPegawai][$namaHari][] = [
    //             'id' => $jadwalDokter['jadwaldokter_id'],
    //             'instalasi_id' => $jadwalDokter['instalasi_id'],
    //             'ruangan_id' => $jadwalDokter['ruangan_id'],
    //             'pegawai_id' => $jadwalDokter['pegawai_id'],
    //             'namaHari' => $namaHari,
    //             'mulai' => $jadwalDokter['jadwaldokter_mulai'],
    //             'tutup' => $jadwalDokter['jadwaldokter_tutup'],
    //             'kuota' => $jadwalDokter['maximumantrian'],
    //             'status' => $jadwalDokter['is_active'],
    //         ];
    //     }
    //     return $list;
    // }

    public function actionFindRuangan($instalasi_id) {
        $listRuangan = $this->listRuangan($instalasi_id);
        return json_encode($listRuangan);
    }

    public function listRuangan($instalasi_id=null) {
        $listRuangan = [
            [
                'ruangan_id'=>1,
                'ruangan_nama' => 'Poliklinik Jantung'
            ],
            [
                'ruangan_id'=>2,
                'ruangan_nama' => 'Poliklinik Mata'
            ],
            [
                'ruangan_id'=>3,
                'ruangan_nama' => 'Poliklinik Anak'
            ], 
        ];
        return $listRuangan;
    }

    public function listHari() 
    {
        $results = [
            75=>'Senin',
            76=>'Selasa',
            77=>'Rabu',
            78=>'Kamis',
            79=>'Jumat',
            80=>'Sabtu',
            81=>'Minggu',
        ];

        return $results;
    }

    public function listJam() 
    {
        $results = [
            '00.00' => '00.00',
            '00.30' => '00.30',
            '01.00' => '01.00',
            '01.30' => '01.30',
            '02.00' => '02.00',
            '02.30' => '02.30',
            '03.00' => '03.00',
            '03.30' => '03.30',
            '04.00' => '04.00',
            '04.30' => '04.30',
            '05.00' => '05.00',
            '05.30' => '05.30',
            '06.00' => '06.00',
            '06.30' => '06.30',
            '07.00' => '07.00',
            '07.30' => '07.30',
            '08.00' => '08.00',
            '08.30' => '08.30',
            '09.00' => '09.00',
            '09.30' => '09.30',
            '10.00' => '10.00',
            '10.30' => '10.30',
            '11.00' => '11.00',
            '11.30' => '11.30',
            '12.00' => '12.00',
            '12.30' => '12.30',
            '13.00' => '13.00',
            '13.30' => '13.30',
            '14.00' => '14.00',
            '14.30' => '14.30',
            '15.00' => '15.00',
            '15.30' => '15.30',
            '16.00' => '16.00',
            '16.30' => '16.30',
            '17.00' => '17.00',
            '17.30' => '17.30',
            '18.00' => '18.00',
            '18.30' => '18.30',
            '19.00' => '19.00',
            '19.30' => '19.30',
            '20.00' => '20.00',
            '20.30' => '20.30',
            '21.00' => '21.00',
            '21.30' => '21.30',
            '22.00' => '22.00',
            '22.30' => '22.30',
            '23.00' => '23.00',
            '23.30' => '23.30',
        ];
        return $results;
    }

    public function listShift()
    {
        try {
            $response = $this->_restPendaftaran->get('penjadwalan-dokter/list-shift');
            $body = json_decode($response->getBody(), True);
            $shift = [];
            $jam = $this->listJam();
            foreach ($body['response']['data'] as $key => $value) {
                $temp = [];
                $temp['shift_nama'] = $value['shift_nama'];
                if($value['shift_jamakhir'] < $value['shift_jamawal']){
                    $temp['shift_jamawal'] = $this->roundToHalfHour($value['shift_jamawal']);
                    $temp['shift_jamakhir'] = date('H.i',strtotime('23:30:00'));
                }else{
                    $temp['shift_jamawal'] = $this->roundToHalfHour($value['shift_jamawal']);
                    $temp['shift_jamakhir'] = $this->roundToHalfHour($value['shift_jamakhir']);
                }
                array_push($shift, $temp);
            }
            $first = reset($jam);
            $last = end($jam);
            foreach ($shift as $key => $value) {
                if($first >= $value['shift_jamawal']){
                    $idx_start = $first;
                }else{
                    $idx_start = array_search($value['shift_jamawal'], $jam);
                    if($idx_start == false) $idx_start = $first;
                }

                if($last <= $value['shift_jamakhir']){
                    $idx_end = $last;
                }else{
                    $idx_end = array_search($value['shift_jamakhir'], $jam);
                    if($idx_end == false) $idx_end = $last;
                }

                $shift[$key]['idx_start'] = $idx_start;
                $shift[$key]['idx_end'] = $idx_end;

            }
            return $shift;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function getData($instalasi_id=null, $ruangan_id=null, $pegawai_id=null, $hari_filt = null, $jam_mulai=null,$jam_selesai=null)
    {
        try {
            // $response = $this->_restPendaftaran->get("penjadwalan-dokter/index?instalasi_id={$instalasi_id}&ruangan_id={$ruangan_id}&pegawai_id={$pegawai_id}");
            $response = $this->_restPendaftaran
                        ->get("penjadwalan-dokter/index",
                            ['query'=>[
                                'instalasi_id'=>$instalasi_id,
                                'ruangan_id'=>$ruangan_id,
                                'pegawai_id'=>$pegawai_id,
                                'hari' => $hari_filt,
                                'jam_mulai'=>$jam_mulai,
                                'jam_selesai'=>$jam_selesai,
                            ]]);
            $body = json_decode($response->getBody(), True);
            
            $result = $body['response'] ? : [];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function roundToHalfHour($timestring) {
        $hour = date('H',strtotime($timestring));
        $minutes = date('i', strtotime($timestring));
        $mins =  $minutes - ($minutes % 30);
        return $hour.'.'.date('i',strtotime($mins));
    }

    public function actionDataJadwalDokter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = $this->_restPendaftaran->get('penjadwalan-dokter/index',['a'=>1]);
        $body = json_decode($response->getBody(), True);

        $data = $body['response'];
        $jadwal = [];
        foreach ($data as $jadwal_dokter) {
            $temp = [
                    'dokter' => $jadwal_dokter['nama_pegawai'],
                    'hari' => $jadwal_dokter['jadwaldokter_hari'],
                    'start' => date('H:i',strtotime($jadwal_dokter['jadwaldokter_mulai'])),
                    'end' => date('H:i',strtotime($jadwal_dokter['jadwaldokter_tutup'])),
                    'instalasi' => $jadwal_dokter['instalasi_nama'],
                    'ruangan' => $jadwal_dokter['ruangan_nama'],
                    'durasi' => $this->getDuration($jadwal_dokter['jadwaldokter_mulai'],$jadwal_dokter['jadwaldokter_tutup']),
                    'kuota' => $jadwal_dokter['maximumantrian']
                ];
            array_push($jadwal, $temp);
        }
        return json_encode($jadwal);
    }

    public function getDuration($start,$end)
    {
        $datetime1 = new DateTime(date('Y-m-d h:i:s',strtotime($start)));
        $datetime2 = new DateTime(date('Y-m-d h:i:s',strtotime($end)));
        $interval = $datetime1->diff($datetime2);
        return $interval->format('%h')." Jam ".$interval->format('%i')." Menit";
    }

    public function actionGetListRuangan($instalasi_id)
    {
        $request = Yii::$app->request;
        $model = new JadwalDokterForm;
        $id = $instalasi_id;//DocoHelpers::decrypt($instalasi_id);
        $response = $this->_restPendaftaran->get('penjadwalan-dokter/get-listruangan-by-id?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $lists = $body['response']['data'];
        $lists_html = '';
        if (!empty($lists)) {
            foreach($lists as $list) {
                $lists_html.= "<option value='".$list['ruangan_id']."'>".$list['ruangan_nama']."</option>";
            }
        } else {
            $lists_html .= "<option>-</option>";
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $lists_html;
    }

    // Export excel
    public function actionExportExcel()
    {
        // Convert to json format
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Try catch
        try {
            $path = Yii::getAlias("@download") . "/penjadwalan-dokter.xlsx";
            $response = $this->_restPendaftaran->get('penjadwalan-dokter/export-excel',[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
           } catch (\Exception $e) {
                // Get message
                $result['error'] = $e->getMessage();

                // Return
                return $result;
           } catch (RequestException $e) {
                // Get message
                $result['error'] = $e->getMessage();
                
                // Return
                return $result;
           }
    }

    public function actionDepDropListRuangan() {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['depdrop_parents'][0];
        if ($instalasi_id) {
            $ruanganRequest = $this->_restPendaftaran->get('allow/list-ruangan?instalasi_id='.$instalasi_id);
            $body = json_decode($ruanganRequest->getBody(),TRUE);
            $responses = $body['response']['data'];
            $responses = ArrayHelper::map($responses, 'ruangan_id', 'ruangan_nama');
        } else {
            $responses = [];
        }

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return json_encode(['output'=>$out, 'selected'=>'']);
    }

    // Render jadwal dokter
    // public function actionRenderPenjadwalanDokter() {
    //     try {
    //         $post = Yii::$app->request->post();
    //         $instalasi_id = isset($post['instalasi_id']) ? $post['instalasi_id'] : null;
    //         $ruangan_id = isset($post['ruangan_id']) ? $post['ruangan_id'] : null;
    //         $pegawai_id = isset($post['pegawai_id']) ? $post['pegawai_id'] : null;
    //         $hari_filt = isset($post['hari']) ? $post['hari'] : null;
    //         $jam_mulai = isset($post['jam_mulai']) ? $post['jam_mulai'] : null;
    //         $jam_selesai = isset($post['jam_selesai']) ? $post['jam_selesai'] : null;

    //         $listHari = $this->listHari();
    //         $listJam = $this->listJam();
            
    //         $list = $this->refactorData($instalasi_id, $ruangan_id, $pegawai_id, $hari_filt, $jam_mulai, $jam_selesai);

    //         $tableList = "";
    //         if ($list) {
    //             foreach ($list as $poli=>$dokterList) {
    //                 $rowspan = count($dokterList) * 7;
    //                 $is_first = true;
    //                 foreach ($dokterList as $nama=>$listHari) {
    //                     foreach ($listHari as $hari=>$listJadwal) {
    //                         $tableList .= "<tr>";
    //                         if($is_first) {
    //                             $tableList .= "
    //                             <td rowspan=$rowspan>$poli</td>
    //                             ";
    //                         }
    //                         $tableList .= "
    //                         <td style='white-space: nowrap'>$nama</td>
    //                         <td style='white-space: nowrap'>$hari</td>
    //                         ";
    //                         $colspan = 1;
    //                         $id = '';
    //                         $kuota = '';
    //                         $attr = '';
    //                         $afterSpan = false;
    //                         foreach ($listJam as $jam) {
    //                             // $tableList .= "<td></td>
    //                             // ";
    //                             $fJam = date('H:i', strtotime($jam));
    //                             $classname = '';
    //                             $flag = false;
    //                             foreach ($listJadwal as $key=>$jadwal) {
    //                                 $mulai = $jadwal['mulai'];  
    //                                 $tutup = $jadwal['tutup'];
    //                                 $fMulai = date('H:i', strtotime($mulai));
    //                                 $fTutup = date('H:i', strtotime($tutup));
    //                                 if ($fJam >= $fMulai && $fJam < $fTutup) {
    //                                     $flag = true;
    //                                     $bg = 'bg-info';
    //                                     if ($jadwal['status'] == false) {
    //                                         $bg = 'bg-light';
    //                                     }
    //                                     $title = $mulai . ' - ' . $tutup;
    //                                     $attr = "class='{$bg} jadwal-dokter' style='padding:4px;' colspan={$colspan} data-id={$jadwal['id']} data-tooltip='tooltip' title='' data-placement='bottom' data-original-title='{$title}'";
    //                                     $colspan++;
    //                                     $id = DocoHelpers::encrypt($jadwal['id']);
    //                                     $kuota = $jadwal['kuota'];
    //                                     $afterSpan = true;
    //                                     break;
    //                                 }
    //                             }
    //                             if($flag == false) {
    //                                 $content = "<div class='col-sm-6'>Kuota " . $kuota . "</div>";

    //                                 $tableList .= "<td $attr>" . ($kuota ? $content : '') . "</td>";
    //                                 if ($afterSpan) {
    //                                     $tableList .= "<td></td>";
    //                                 }
    //                                 $kuota = '';
    //                                 $attr = '';
    //                                 $colspan = 1;
    //                                 $afterSpan = false;
    //                             }
    //                         }
    //                         $tableList .= "</tr>";
    //                         $is_first = false;
    //                     }
    //                 }
    //             }
    //         }

    //         return DocoHelpers::response($tableList);
    //     } catch (RequestException $e) {
    //         return DocoHelpers::response(['message' => $e->getMessage()],500);
    //     } catch (\Exception $e) {
    //         return DocoHelpers::response(['message' => $e->getMessage()],500);
    //     }
    // }

    public function actionRenderJadwalDokter() {
        try {
            $post = Yii::$app->request->post();
            $instalasi_id = isset($post['instalasi_id']) ? $post['instalasi_id'] : null;
            $ruangan_id = isset($post['ruangan_id']) ? $post['ruangan_id'] : null;
            $pegawai_id = isset($post['pegawai_id']) ? $post['pegawai_id'] : null;
            $hari_filt = isset($post['hari']) ? $post['hari'] : null;
            $jam_mulai = isset($post['jam_mulai']) ? $post['jam_mulai'] : null;
            $jam_selesai = isset($post['jam_selesai']) ? $post['jam_selesai'] : null;
            
            
            $listJam = $this->listJam();
            
            $list = $this->refactorData($instalasi_id, $ruangan_id, $pegawai_id, $hari_filt, $jam_mulai, $jam_selesai);

            $tableList = "";
            if ($list) {
                foreach ($list as $poli=>$dokterList) {
                    $counter_rowspan = 0;
                    // hitung rowspan
                    foreach ($dokterList as $kd => $vd) {
                        foreach ($vd as $kd2 => $vd2) {
                            if(count($vd2)){
                                $counter_rowspan++;
                            }
                        }
                    }
                    // hitung rowspan
                    $rowspan = "";
                    $rowspan = count($dokterList);
                    if($rowspan > 1){
                        $rowspan = "rowspan=$counter_rowspan";
                    }
                    
                    $is_first = true;
                    
                    foreach ($dokterList as $nama=>$listHari) {
                       
                        foreach ($listHari as $hari=>$listJadwal) {
                            // pengecekan jadwal
                            if(count($listJadwal) && !empty($post)){
                                $tableList .= "<tr>";
                                if ($is_first) {
                                    $tableList .= "
                                <td $rowspan>$poli</td>
                                ";
                                }
                                $tableList .= "
                                    <td style='white-space: nowrap'>$nama</td>
                                    <td style='white-space: nowrap'>$hari</td>
                                    ";
                                $colspan = 1;
                                $id = '';
                                $title = '';
                                $kuota = '';
                                $kuota_online = '';
                                $attr = '';
                                $afterSpan = false;
                                foreach ($listJam as $jam) {
                                // $tableList .= "<td></td>
                                // ";
                                    $fJam = date('H:i', strtotime($jam));
                                    $classname = '';
                                    $flag = false;
                                    foreach ($listJadwal as $key => $jadwal) {
                                        $mulai = $jadwal['mulai'];
                                        $tutup = $jadwal['tutup'];
                                        $fMulai = date('H:i', strtotime($mulai));
                                        $fTutup = date('H:i', strtotime($tutup));
                                        if ($fJam >= $fMulai && $fJam < $fTutup) {
                                            $flag = true;
                                            $bg = 'bg-info';
                                            if ($jadwal['status'] == false) {
                                                $bg = 'bg-light';
                                            }
                                            $title = $mulai . ' - ' . $tutup;
                                            $attr = "class='{$bg} jadwal-dokter' style='padding:4px;' colspan={$colspan} data-id={$jadwal['id']} data-tooltip='tooltip' title='' data-placement='bottom' data-original-title='{$title}'";
                                            $colspan++;
                                            $id = DocoHelpers::encrypt($jadwal['id']);
                                            $kuota = !empty($jadwal['kuota']) ? $jadwal['kuota'] : 0;
                                            $kuota_online = !empty($jadwal['kuota_online']) ? $jadwal['kuota_online'] : 0;
                                            $afterSpan = true;
                                            break;
                                        }
                                    }
                                    if ($flag == false) {
                                        $content = "<div class='col-sm-6'></div>";
                                        // if ($kuota) {
                                            $content = "<div class='col-sm-12 jadwal-info'><b>Kuota Online : " . $kuota_online . " | Kuota Offline : " . $kuota . "</b></div>";
                                            $content .= "<div class='show-info'>";
                                            $content .= "<b>Jadwal : " . $title . "</b><br>";
                                            $content .= "<b>Kuota Online : " . $kuota_online . "</b><br>";
                                            $content .= "<b>Kuota Offline : " . $kuota . "</b><br>";
                                            $content .= "<i class='btn btn-jadwal btn-sm btn-info fa fa-bell notif' 
                                                        data-id='{$id}' 
                                                        action='/master/jadwal-dokter/notif?id={$id}' 
                                                    ></i>";
                                            $content .= "<i class='btn btn-jadwal btn-sm btn-primary fa fa-pencil update-jadwal' data-id='{$id}' action='/master/jadwal-dokter/update?id={$id}'></i>";
                                            $content .= "<i class='btn btn-jadwal btn-sm btn-danger fa fa-trash delete' action='/master/jadwal-dokter/delete?id={$id}' data-id='{$id}'></i>";
                                            $content .= "</div>";
                                        // }
                                    
                                    // $content .= "<div class='col-sm-6'><div class='pull-right'>"; 
                                    // $content .= "<i class='fa fa-bell notif' 
                                    //                 data-id='{$id}' 
                                    //                 action='/master/jadwal-dokter/notif?id={$id}' 
                                    //                 data-toggle='modal' 
                                    //                 data-target='#modal_backdrop'
                                    //             ></i> "; 
                                    // $content .= "<i class='fa fa-pencil update-jadwal' data-id='{$id}' action='/master/jadwal-dokter/update?id={$id}'></i>"; 
                                    // $content .= " <i class='fa fa-trash delete' action='/master/jadwal-dokter/delete?id={$id}' data-id='{$id}'></i>"; 
                                    // $content .= "</div></div>";

                                        $tableList .= "<td $attr>" . ($id ? $content : '') . "</td>";
                                        if ($afterSpan) {
                                            $tableList .= "<td></td>";
                                        }
                                        $id = '';
                                        $kuota = '';
                                        $attr = '';
                                        $colspan = 1;
                                        $afterSpan = false;
                                    }
                                }
                                $tableList .= "</tr>";
                                $is_first = false;
                            }
                            // pengecekan jadwal
                        }
                    }
                }
            }

            return DocoHelpers::response($tableList);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function refactorData($instalasi_id=null, $ruangan_id=null, $pegawai_id=null, $hari_filt = null, $jam_mulai=null, $jam_selesai=null) 
    {
        $data = $this->getData($instalasi_id, $ruangan_id, $pegawai_id, $hari_filt, $jam_mulai, $jam_selesai);
        $list_hari = $this->listHari();
        if(isset($hari_filt)){
            $listHari = isset($list_hari[$hari_filt]) ? [$hari_filt => $list_hari[$hari_filt]] : $list_hari;
        }else{
            $listHari = $list_hari;
        }
        // var_dump($hari_filt);exit;
        // var_dump($data);exit;
        // var_dump($listHari);exit;
        // $listHari = $this->listHari();
        $list = [];

        foreach ($data as $key => $jadwalDokter) {
            $namaRuangan = $jadwalDokter['ruangan_nama'];
            $namaPegawai = $jadwalDokter['nama_pegawai'];
            $namaHari = $jadwalDokter['hari_nama'];
            // $idHari = $jadwa
            // $namaHari = $jadwalDokter['lookup_hari']['lookup_name'];

            if (!isset($list[$namaRuangan])) {
                $list[$namaRuangan] = [];
            }
            if (!isset($list[$namaRuangan][$namaPegawai])) {
                $list[$namaRuangan][$namaPegawai] = []; 
            }
            foreach ($listHari as $keyHari => $hari) {
                if (!isset($list[$namaRuangan][$namaPegawai][$hari])) {
                    
                    if(!$hari_filt){
                        $list[$namaRuangan][$namaPegawai][$hari] = [];
                    }

                    // $list[$namaRuangan][$namaPegawai][$hari] = [
                    //     'instalasi_id' => $jadwalDokter['instalasi_id'],
                    //     'ruangan_id' => $jadwalDokter['ruangan_id'],
                    //     'pegawai_id' => $jadwalDokter['pegawai_id'],
                    //     'hari_id' => $keyHari,
                    // ];
                }
                
            }
            $list[$namaRuangan][$namaPegawai][$namaHari][] = [
                'id' => $jadwalDokter['jadwaldokter_id'],
                'instalasi_id' => $jadwalDokter['instalasi_id'],
                'ruangan_id' => $jadwalDokter['ruangan_id'],
                'pegawai_id' => $jadwalDokter['pegawai_id'],
                'namaHari' => $namaHari,
                'mulai' => $jadwalDokter['jadwaldokter_mulai'],
                'tutup' => $jadwalDokter['jadwaldokter_tutup'],
                'kuota' => $jadwalDokter['maximumantrian'],
                'kuota_online' => $jadwalDokter['kuota_online'],
                'status' => $jadwalDokter['is_active'],
            ];
        }
        return $list;
    }
}
?>
