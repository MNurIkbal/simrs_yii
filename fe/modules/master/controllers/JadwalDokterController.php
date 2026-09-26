<?php
// author : rizal


namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\modules\master\models\JadwalDokterForm;
use app\modules\master\models\JadwalCutiForm;
use app\modules\master\models\HapusJadwalCutiForm;
use app\components\DocoHelpers;
use Doco\master\models\NotifForm;
use app\assets\CalenderAssets;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;
use app\components\DHtml;

class JadwalDokterController extends DocoController
{
    protected $_title = "Jadwal dokter";
    protected $_titleJadwalCuti = 'Jadwal Cuti';
    protected $_module = 'master/jadwal-dokter/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        CalenderAssets::register(Yii::$app->view);
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

    public function actionIndex() 
    {
        return Yii::$app->docoPlugin->execute($this,'jadwal_dokter_index');
    }

    public function actionJadwalCuti() 
    {
        $title = $this->_titleJadwalCuti;

        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'ruangan/list-ruangan',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'instalasi_id' => null
                ]
            ]
        ]);

        $list_ruangan = [];
        foreach ($response as $key => $value) {
            $list_ruangan[] = [
                'id' => $key,
                'text' => $value
            ];
        }

        return $this->render('_jadwal-cuti', get_defined_vars());
    }
    
    public function actionRenderCreate()
    {
        $response = $this->_restMaster->get('ruangan/list-ruangan?instalasi_id=1');
        $body = json_decode($response->getBody(), True);
        $listRuangan = $body['response'];

        $response = $this->_restMaster->get('allow/list-dokter-rajal');
        $body = json_decode($response->getBody(), True);
        $listDokter = $body['response'];
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        
        $request = Yii::$app->request;
        return $this->render('create', get_defined_vars());
    }

    public function actionCreate() {
        try {
            $modelJadwalDokter = new JadwalDokterForm;
            $modelJadwalDokter->is_active = 1;
            $modelJadwalDokter->is_loaddokter = 0;
            $modelJadwalDokter->is_bersedia = 0;

            $title = Yii::t('fe', 'Tambah Jadwal dokter');
            $status = $this->_status;
            
            $instalasiList = [DocoConstants::INSTALASI_ID_RJ, DocoConstants::INSTALASI_ID_RD, DocoConstants::INSTALASI_ID_RI];
            $instalasiList = (count($instalasiList) > 0 ) ? array_merge($instalasiList, DocoConstants::INSTALASI_ID_PENUNJANG) : [];
            $arr = ['list_instalasi'=>['actionListInstalasi', $instalasiList], 'list_hari'=>['actionListHariLookup']];
            // $response = $this->_restMaster->post('allow/loop-aksi', ['form_params'=>$arr]);
            $response = $this->_restMaster->get('jadwal-dokter/get-bundle-data',['query'=>['param_instalasi'=> $instalasiList]]);
            $body = json_decode($response->getBody(), true);
            $listInstalasi = (count($body['response']['list_instalasi']['data']) > 0) ? ArrayHelper::map($body['response']['list_instalasi']['data'], 'instalasi_id', 'instalasi_nama') : [];
            $listHari = (count($body['response']['list_hari']['data']) > 0) ? ArrayHelper::map($body['response']['list_hari']['data'], 'hari_id', 'hari_nama') : [];
            $listWaktuPelayanan = (count($body['response']['list_pelayanan']) > 0) ? ArrayHelper::map($body['response']['list_pelayanan'], 'lookup_value', 'lookup_value') : [];
            $listDokter = [];
            $listRuangan = [];

            $response = $this->_restMaster->get('ruangan/list-ruangan?instalasi_id=1');
            $body = json_decode($response->getBody(), True);
            $listRuangan = $body['response'];

            $listHari = $this->listHari();

            $konfig = $this->getKonfigSistem();
            $konfigReservasi = $konfig['is_reservasi'];
            $kuota_antrian = isset($konfig['kuota_antrian']) && $konfig['kuota_antrian'] != '' ? $konfig['kuota_antrian'] : DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;
            $konfigCaraBayar = $konfig['is_pisah_cabar'];
            $konfigSlotDokter = $konfig['is_slot_dokter'];

            $request = Yii::$app->request;
            $formName = substr(strrchr(get_class($modelJadwalDokter), "\\"), 1);

            if ($request->post()) {
                $post = $request->post();
                // $modelJadwalDokter->scenario = JadwalDokterForm::SCENARIO_N_IGD;
                $modelJadwalDokter->load($request->post());
                // if($modelJadwalDokter->instalasi_id == 2){
                //     $modelJadwalDokter->scenario = JadwalDokterForm::SCENARIO_IGD;
                // }
                $modelJadwalDokter->jadwaldokter_tgl = date('Y-m-d');
                $modelJadwalDokter->jadwaldokter_waktupelayanan = '-';
                $modelJadwalDokter->jadwaldokter_hari = $post['JadwalDokterForm']['jadwaldokter_hari'];

                if(!empty($modelJadwalDokter->kuota_bpjs_offline) && !empty( $modelJadwalDokter->kuota_bpjs_online)) {
                    $modelJadwalDokter->kuota_bpjs_total = (int) $modelJadwalDokter->kuota_bpjs_offline + (int) $modelJadwalDokter->kuota_bpjs_online;
                }

                if(!empty($modelJadwalDokter->kuota_nonbpjs_offline) && !empty($modelJadwalDokter->kuota_nonbpjs_online)) {
                    $modelJadwalDokter->kuota_nonbpjs_total = (int) $modelJadwalDokter->kuota_nonbpjs_offline + (int) $modelJadwalDokter->kuota_nonbpjs_online;
                }

                if ($modelJadwalDokter->is_bersedia == true) {
                    $modelJadwalDokter->jadwaldokter_mulai = date('H:i:s', strtotime('00:01'));
                    $modelJadwalDokter->jadwaldokter_tutup = date('H:i:s', strtotime('23:59'));
                }
                
                if ($modelJadwalDokter->validate()) {
                    $response = $this->_restMaster->request('POST', 'jadwal-dokter/create',[
                                        'form_params' => $modelJadwalDokter->attributes
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false);
                } else {
                    return DocoHelpers::response($modelJadwalDokter->errors,422,'JadwalDokterForm');
                }
            }
            return $this->renderAjax('_form', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }

    }

    public function actionCheckJadwal()
    {
        try {
            $post = Yii::$app->request->post();
            $pegawai_id = $post['pegawai_id'];
            $hari = $post['hari'];
            $response = $this->_restMaster->get("allow/list-jadwal-dokter-tersedia",['query'=>[
                'pegawai_id' => $pegawai_id,
                'hari' => $hari,
            ]]);
            $body = json_decode($response->getBody(), True);
            $listJadwalDokter = $body['response'];
            
            return DocoHelpers::response($listJadwalDokter);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
        
    }

    public function actionUpdate($id)
    {
        try {
            $modelJadwalDokter = new JadwalDokterForm;
            $id = DocoHelpers::decrypt($id);
            // $status = $this->_status;

            $title = Yii::t('fe', 'Ubah Jadwal dokter');

            $listRuangan = [];

            $request = Yii::$app->request;
            $formName = substr(strrchr(get_class($modelJadwalDokter), "\\"), 1);

            if ($request->post()) {
                $modelJadwalDokter->load($request->post());
                $modelJadwalDokter->instalasi_id = 1;
                $modelJadwalDokter->jadwaldokter_tgl = date('Y-m-d');
                $modelJadwalDokter->jadwaldokter_waktupelayanan = '-';

                if(!empty($modelJadwalDokter->kuota_bpjs_offline) && !empty( $modelJadwalDokter->kuota_bpjs_online)) {
                    $modelJadwalDokter->kuota_bpjs_total = (int) $modelJadwalDokter->kuota_bpjs_offline + (int) $modelJadwalDokter->kuota_bpjs_online;
                }

                if(!empty($modelJadwalDokter->kuota_nonbpjs_offline) && !empty($modelJadwalDokter->kuota_nonbpjs_online)) {
                    $modelJadwalDokter->kuota_nonbpjs_total = (int) $modelJadwalDokter->kuota_nonbpjs_offline + (int) $modelJadwalDokter->kuota_nonbpjs_online;
                }

                if ($modelJadwalDokter->validate()) {
                    $response = $this->_restMaster->request('POST', "jadwal-dokter/update?id={$id}",[
                                        'form_params' => $modelJadwalDokter->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false);
                } else {
                    return DocoHelpers::response($modelJadwalDokter->errors,422,'JadwalDokterForm');
                }
            } 
            
            $response = $this->_restMaster->get('ruangan/list-ruangan?instalasi_id=1');
            $body = json_decode($response->getBody(), True);
            $listRuangan = $body['response'];

            $instalasiList = [DocoConstants::INSTALASI_ID_RJ, DocoConstants::INSTALASI_ID_RD, DocoConstants::INSTALASI_ID_RI];
            $instalasiList = (count($instalasiList) > 0 ) ? array_merge($instalasiList, DocoConstants::INSTALASI_ID_PENUNJANG) : [];
            $arr = [
                'list_instalasi'=>['actionListInstalasi', $instalasiList], 
                'list_hari'=>['actionListHariLookup'],
                'list_pelayanan'=>['actionListWaktuPelayanan'],
                'model'=>['actionViewJadwalDokter', $id],
            ];
            $response = $this->_restMaster->post('allow/loop-aksi', ['form_params'=>$arr]);
            $body = json_decode($response->getBody(), true);

            $listInstalasi = (count($body['response']['list_instalasi']['data']) > 0) ? ArrayHelper::map($body['response']['list_instalasi']['data'], 'instalasi_id', 'instalasi_nama') : [];
            $listHari = (count($body['response']['list_hari']['data']) > 0) ? ArrayHelper::map($body['response']['list_hari']['data'], 'hari_id', 'hari_nama') : [];
            $listDokter = [];
            $listWaktuPelayanan = (count($body['response']['list_pelayanan']) > 0) ? ArrayHelper::map($body['response']['list_pelayanan'], 'lookup_value', 'lookup_value') : [];

            //$listHari = $this->listHari();

            $konfig = $this->getKonfigSistem();
            $konfigReservasi = $konfig['is_reservasi'];
            $kuota_antrian = isset($konfig['kuota_antrian']) && $konfig['kuota_antrian'] != '' ? $konfig['kuota_antrian'] : DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;
            $konfigCaraBayar = $konfig['is_pisah_cabar'];
            $konfigSlotDokter = $konfig['is_slot_dokter'];

            $result = $body['response']['model'];
            $modelJadwalDokter->attributes = $result;
            $modelJadwalDokter->jadwaldokter_id = $id;
            $modelJadwalDokter->jadwaldokter_mulai = date('H:i', strtotime($modelJadwalDokter->jadwaldokter_mulai));
            $modelJadwalDokter->jadwaldokter_tutup = date('H:i', strtotime($modelJadwalDokter->jadwaldokter_tutup));
            
            
            // Kondisi jika inject data dan kuota nonbpjs/bpjs online/off = 0 / null
            $kuota_bpjs_online = $modelJadwalDokter->kuota_bpjs_online + $modelJadwalDokter->kuota_nonbpjs_online != $modelJadwalDokter->kuota_online 
                            ? floor($modelJadwalDokter->kuota_online / 2) 
                            : $modelJadwalDokter->kuota_bpjs_online;
            
            $kuota_nonbpjs_online = $modelJadwalDokter->kuota_bpjs_online + $modelJadwalDokter->kuota_nonbpjs_online != $modelJadwalDokter->kuota_online 
                            ? $modelJadwalDokter->kuota_online - $kuota_bpjs_online 
                            : $modelJadwalDokter->kuota_nonbpjs_online;
                            
            $kuota_bpjs_offline = $modelJadwalDokter->kuota_bpjs_offline + $modelJadwalDokter->kuota_nonbpjs_offline != $modelJadwalDokter->maximumantrian 
                            ? floor($modelJadwalDokter->maximumantrian / 2) 
                            : $modelJadwalDokter->kuota_bpjs_offline;
            
            $kuota_nonbpjs_offline = $modelJadwalDokter->kuota_bpjs_offline + $modelJadwalDokter->kuota_nonbpjs_offline != $modelJadwalDokter->maximumantrian 
                            ? $modelJadwalDokter->maximumantrian - $kuota_bpjs_offline 
                            : $modelJadwalDokter->kuota_nonbpjs_offline;
                            
            $modelJadwalDokter->kuota_bpjs_online = $kuota_bpjs_online;
            $modelJadwalDokter->kuota_nonbpjs_online = $kuota_nonbpjs_online;
            $modelJadwalDokter->kuota_bpjs_offline = $kuota_bpjs_offline;
            $modelJadwalDokter->kuota_nonbpjs_offline = $kuota_nonbpjs_offline;
            // end of kondisi
                            
            return $this->renderAjax('_form', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }

    }
    
    public function find($id)
    {
        try {
            $response = $this->_restMaster->get("jadwal-dokter/view?id={$id}");
            return json_decode($response->getBody(), True);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
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
            $konfig = $this->getKonfigSistem();
            $kuota_antrian = isset($konfig['kuota_antrian']) && $konfig['kuota_antrian'] != '' ? $konfig['kuota_antrian'] : DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;

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
                                //     $tableList .= "
                                // <td $rowspan>$poli</td>
                                // ";
                                    $tableList .= "
                                        <td rowspan=$counter_rowspan>$poli</td>
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
                                        if ($kuota_antrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER) {
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
                                        } else {
                                            $content = "<div class='col-sm-12 jadwal-info'></div>";
                                            $content .= "<div class='show-info'>";
                                            $content .= "<b>Jadwal : " . $title . "</b><br>";
                                            $content .= "<i class='btn btn-jadwal btn-sm btn-info fa fa-bell notif' 
                                                        data-id='{$id}' 
                                                        action='/master/jadwal-dokter/notif?id={$id}' 
                                                    ></i>";
                                            $content .= "<i class='btn btn-jadwal btn-sm btn-primary fa fa-pencil update-jadwal' data-id='{$id}' action='/master/jadwal-dokter/update?id={$id}'></i>";
                                            $content .= "<i class='btn btn-jadwal btn-sm btn-danger fa fa-trash delete' action='/master/jadwal-dokter/delete?id={$id}' data-id='{$id}'></i>";
                                            $content .= "</div>";
                                        }
                                    
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

    public function actionFindRuangan($instalasi_id) {
        $listRuangan = $this->listRuangan($instalasi_id);
        return json_encode($listRuangan);
    }

    public function listHari() 
    {
        try{

            $request = $this->_restMaster->get('allow/list-hari-lookup');
            $response = json_decode($request->getBody(),TRUE);
            $results = $response['response']['data'];
            $results = ArrayHelper::map($results,'hari_id','hari_nama');

            return $results;
        } catch(\Exception $e) {
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }

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

    public function actionDelete($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restMaster->request('DELETE', 'jadwal-dokter/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => Yii::t('fe', 'Proses Berhasil'),
                'text' => Yii::t('fe', 'Data berhasil dihapus')
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        // var_dump($request->post());die;
        try {
            $response = $this->_restMaster->get('jadwal-dokter', [
                'query' => $request->post()
            ]);

            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            $ruangan = isset($response['data_ruangan']) ? $response['data_ruangan'] : [];
            $data_jadwal = isset($response['jadwal_data']) ? $response['jadwal_data'] : [];
            return DocoHelpers::response([
                'ruangan' => $ruangan,
                'data_jadwal' => $data_jadwal
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        }
    }
    // public function getData($instalasi_id=null, $ruangan_id=null, $pegawai_id=null, $hari_filt = null, $jam_mulai=null,$jam_selesai=null)
    // {
    //     try {
    //         $response = $this->_restMaster->get("jadwal-dokter/index-web",[
    //             'query' => [
    //                 'instalasi_id'=>$instalasi_id,
    //                 'ruangan_id'=>$ruangan_id,
    //                 'pegawai_id'=>$pegawai_id,
    //                 'hari' => $hari_filt,
    //                 'jam_mulai'=>$jam_mulai,
    //                 'jam_selesai'=>$jam_selesai,
    //             ]
    //         ]);
    //         $body = json_decode($response->getBody(), True);
            
    //         $result = $body['response'] ? : [];

    //         return $result;
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    public function actionExportExcel(
        $instalasi_id=null, $ruangan_id=null, $pegawai_id=null,$hari=null,$jam_mulai=null,$jam_selesai=null)
    {

       Yii::$app->response->format = Response::FORMAT_JSON;
        $path = Yii::getAlias("@download") . "/Master Jadwal Dokter.xlsx";
        // var_dump($path);die();
        try {
            // $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restMaster->get("jadwal-dokter/export-excel?instalasi_id={$instalasi_id}&ruangan_id={$ruangan_id}&pegawai_id={$pegawai_id}&hari={$hari}&jam_mulai={$jam_mulai}&jam_selesai={$jam_selesai}", [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            return DocoHelpers::downloadFile($path, true);
           } catch (RequestException $e){
                $result['error'] = $e->getMessage();
                return $result;
           } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
           }
    }

    public function actionDepListRuangan() {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['depdrop_parents'][0];
        if ($instalasi_id) {
            $instalasi_id = (int)$instalasi_id;
            // $ruanganRequest = $this->_restMaster->get('allow/list-ruangan?instalasi_id='.$instalasi_id);
            $ruanganRequest = $this->_restMaster->get('allow/list-ruangan');
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

    public function actionDepListRuanganPegawai()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $jadwaldokter_id = ArrayHelper::getValue($post['depdrop_parents'], 1);
            $pegawai_id = $post['depdrop_parents'][0];
            $selected = '';
            if($pegawai_id && $jadwaldokter_id){
                $ruanganRequest = $this->_restMaster->get('allow/list-ruangan-pegawai',['query'=>['pegawai_id'=>$pegawai_id,'jadwaldokter_id'=>$jadwaldokter_id]]);
                $body = json_decode($ruanganRequest->getBody(),TRUE);
                $responses = $body['response']['data'];
                $selectedData = $body['response']['selectedData'];
                $selected = $selectedData['ruangan_id'];
                $responses = ArrayHelper::map($responses, 'ruangan_id', 'ruangan_nama');
            }else if ($pegawai_id) {
                $ruanganRequest = $this->_restMaster->get('allow/list-ruangan-pegawai',['query'=>['pegawai_id'=>$pegawai_id]]);
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

            return json_encode(['output'=>$out, 'selected'=>$selected]);
        } catch(\Exception $e){
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    public function actionDepListHariRuangan()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $ruangan_id = $post['depdrop_parents'][0];
            $jadwaldokter_id = $post['depdrop_parents'][1];
            $selected = '';
            if($ruangan_id && $jadwaldokter_id){
                $hariRequest = $this->_restMaster->get('allow/list-hari-ruangan',['query'=>['ruangan_id'=>$ruangan_id,'jadwaldokter_id'=>$jadwaldokter_id]]);
                $body = json_decode($hariRequest->getBody(),TRUE);
                $responses = $body['response']['data'];
                $selectedData = $body['response']['selectedData'];
                $selected = $selectedData['jadwalbukapoli_id'];
            }else if ($ruangan_id) {
                $hariRequest = $this->_restMaster->get('allow/list-hari-ruangan',['query'=>['ruangan_id'=>$ruangan_id]]);
                $body = json_decode($hariRequest->getBody(),TRUE);
                $responses = $body['response']['data'];
                // $responses = ArrayHelper::map($responses, 'jadwalbukapoli_id', 'hari_jam');
            } else {
                $responses = [];
            }

            $out = [];
            foreach($responses as $rowdata) {
                $out[] = [
                    'id' => $rowdata['jadwalbukapoli_id'],
                    'name' => $rowdata['hari_jam'],
                    'options' => [
                        'data-hari' => $rowdata['hari_id'],
                        'data-jam_mulai' => $rowdata['jam_mulai'],
                        'data-jam_selesai' => $rowdata['jam_tutup'],
                    ]
                ];
            }

            return json_encode(['output'=>$out, 'selected'=>$selected]);
        } catch(\Exception $e){
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    public function actionDepListDokterRuangan()
    {
        try{
            $request = Yii::$app->request;
            $post = $request->post();
            $ruangan_id = $post['depdrop_parents'][0];
            if ($ruangan_id) {
                $ruangan_id = (int)$ruangan_id;
                $dokterRequest = $this->_restMaster->get('allow/list-dokter-ruangan',['query'=>['ruangan_id'=>$ruangan_id]]);
                $body = json_decode($dokterRequest->getBody(),TRUE);
                $responses = $body['response']['data'];
            } else {
                $responses = [];
            }
            $out = [];
            foreach($responses as $rowdata) {
                $out[] = [
                    'id' => $rowdata['pegawai_id'],
                    'name' => $rowdata['nama_pegawai'],
                ];
            }

            return json_encode(['output'=>$out, 'selected'=>'']);
        } catch(\Exception $e){
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    /**
    * @author ali.padilah@docotel.com
    * @param 
    * @return json
    * @desc kebutuhan get jadwal dokter untuk depdrop berdasarkan jenis kasus penyakit
    */
    public function actionListDokterByKasusPenyakit($pegawai_id = null,$ruangan_id = null) {
        $request = Yii::$app->request;
        $post = $request->post();
        $kasus_penyakit_id = empty($post['depdrop_parents'][0]) ? null : $post['depdrop_parents'][0];

        $dokterRequest = $this->_restMaster->get('allow/list-dokter-by-kasus-penyakit?ruangan_id='.$ruangan_id.'&pegawai_id='.$pegawai_id);
        $body = json_decode($dokterRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return json_encode(['output'=>$out, 'selected'=>'']);
    }

     /**
    * @author Randy Vianda Putra
    * @todo add notif keterlambatan dokter
    */
    // public function actionNotif($id) {
    //     $id_decrypt = DocoHelpers::decrypt($id);
    //     $model = new NotifForm;
    //     $request = $this->_restMaster->get('jadwal-dokter/get-jadwal-dokter-by-id?id='.$id_decrypt);
    //     $body = json_decode($request->getBody(), true);
    //     $data_jadwal = $body['response'];

    //     return $this->render('notif', get_defined_vars());
    // }

    /**
    * @author Randy Vianda Putra
    * @todo add template notification
    */
    // public function actionPilihTemplate($id) {
    //     $id_decrypt = DocoHelpers::decrypt($id);
    //     $request = $this->_restMaster->get('jadwal-dokter/get-template-notif');
    //     $body = json_decode($request->getBody(), true);
    //     $data_template = $body['response']; 

    //     return $this->renderAjax('pilih-template', get_defined_vars());
    // }

    /**
    * @author Randy Vianda Putra
    * @todo add template new for notification
    */
    public function actionAddTemplate($id) {
        $title = Yii::t('fe', 'Tambah template');
        $model = new NotifForm;

        return $this->renderAjax('add-template', get_defined_vars());
    }

    /**
    * @author Randy Vianda Putra
    * @todo save template notification
    */
    public function actionSaveTemplate() {
        $model = new NotifForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->attributes = $post['NotifForm'];
        if ($model->validate()) {
            $response = $this->_restMaster->request('POST', 'jadwal-dokter/save-template',[
                'form_params' => $post['NotifForm']
            ]);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response,false,'NotifForm');
        } else {
            $errors = DocoHelpers::parseError($model->errors,'NotifForm');

            return DocoHelpers::response([
                'response' => [
                    'data' => $errors
                ]
            ],422);
        }

        return $this->renderAjax('add-template', get_defined_vars());
    }

    /**
    * @author Randy Vianda Putra
    * @todo add template new for notification
    */
    public function actionDeleteTemplate($id) {
        try {
            $response = $this->_restMaster->get('jadwal-dokter/delete-template', [
                'query' => ['id' => $id]
            ]);
            $response = json_decode($response->getBody(),true);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /**
    * @author Randy Vianda Putra
    * @todo push notificataion to mobile apps
    */
    public function actionPushNotification()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('POST', 'jadwal-dokter/push-notification',[
                'form_params' => $post['NotifForm']
            ]);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    /**
    * @author Sigit Arif Munandar <sigit@docotel.com>
    * @todo Function untuk mendapatkan data konfig sistem
    */
    private function getKonfigSistem() {
        try {
            $restMaster = $this->_restMaster->get('jadwal-dokter/get-konfig-sistem');
            $response = json_decode($restMaster->getBody(), true);
            $konfig = $response['response'];

            return $konfig;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetDataJadwalCuti()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        $draw = $request->get('draw', 1);
        $data = [];

        try {
            $body = $this->helper->guzzleExec($this->_restMaster, [
                'url' => 'jadwal-dokter/get-data-jadwal-cuti',
                'method' => 'GET',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);
            
            $no = $request->get('start', 1);
            $data = [];

            foreach ($body['data'] as $key => $value) {
                $no++;
                $value['primary'] = $value['jadwalcuti_id'];
                $value['tgl_cuti'] =  DocoHelpers::convDateTime($value['tgl_cuti_awal'], true, false).' - '.DocoHelpers::convDateTime($value['tgl_cuti_akhir'], true, false);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCreateCuti()
    {
        $request = Yii::$app->request;
        $model = new JadwalCutiForm();
        $title = 'Tambah Jadwal Cuti';
        $formAction = '/master/jadwal-dokter/create-cuti';
        $dokterList = $dokterOptions = [];

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->helper->guzzleExec($this->_restMaster, [
                    'url' => 'jadwal-dokter/create-cuti',
                    'method' => 'post',
                    'payload' => [
                        'form_params' => $model->attributes
                    ]
                ]);

                $statusCode = ArrayHelper::getValue($response, 'httpStatusCode', 200);
                return $this->helper->response([
                    'response' => $response
                ], $statusCode);
            }

            return $this->helper->response($model->errors,422,'JadwalCutiForm');
        } else {
            $dataPack = $this->helper->guzzleExec($this->_restMaster, [
                'url' => 'jadwal-dokter/get-pack-cuti',
                'method' => 'get',
                'payload' => []
            ]);
            $dokterList = ArrayHelper::map($dataPack['dokterList'], 'pegawai_id','nama_pegawai');
            $dokterOptions = $this->mapDokterOption($dataPack['dokterList']);            

            return $this->renderAjax('create-cuti', get_defined_vars());
        }
    }

    public function actionDeleteCuti()
    {
        $request = Yii::$app->request;
        $model = new HapusJadwalCutiForm();
        $title = 'Hapus Jadwal Cuti';
        $formAction = '/master/jadwal-dokter/delete-cuti';
        $jadwalcutiId = $request->get('jadwalcuti_id');
        $username = Yii::$app->docoVars->user('nama');

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->helper->guzzleExec($this->_restMaster, [
                    'url' => 'jadwal-dokter/delete-cuti',
                    'method' => 'delete',
                    'payload' => [
                        'query' => [
                            'jadwalcuti_id' => $jadwalcutiId,
                        ],
                        'form_params' => $model->attributes
                    ]
                ]);

                $statusCode = ArrayHelper::getValue($response, 'httpStatusCode', 200);
                return $this->helper->response([
                    'response' => $response
                ], $statusCode);
            }

            return $this->helper->response($model->errors,422,'HapusJadwalCutiForm');
        } else {           
            return $this->renderAjax('hapus-cuti', get_defined_vars());
        }
    }

    private function mapDokterOption($data)
    {
        $result = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $result[$value['pegawai_id']]['data-spesialis_id'] = $value['spesialis_id'];
                $result[$value['pegawai_id']]['data-spesialis_nama'] = $value['spesialis_nama'];
            }
        }

        return $result;
    }
}
?>
