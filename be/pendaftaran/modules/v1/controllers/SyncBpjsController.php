<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\models\antrian\AntrianjknV;
use Doco\models\pendaftaran\PendaftaranOnline;
use Doco\rabbitmq\RabbitBgProcess;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\BpjsAntrianTanggal;
use app\modules\v1\models\BpjsInfoAntrean;
use app\modules\v1\models\BpjsInfoAntreanFn;
use app\modules\v1\models\BpjsJkn;
use app\modules\v1\models\BpjsListTask;
use app\modules\v1\models\LoginJknR;
use app\modules\v1\models\FGetReservasi;
use app\modules\v1\payload\AntrianPerTanggalPayload;

class SyncBpjsController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionAntrianPerTanggal()
    {
        $payload = new AntrianPerTanggalPayload;
        $payload->attributes = [
            'tanggalawal' => Yii::$app->request->post('tanggalawal',date('Y-m-d')),
            'tanggalakhir' => Yii::$app->request->post('tanggalakhir',date('Y-m-d')) 
        ];
        if (!$payload->validate()) {
            return $this->helper->callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'text' => 'Terjadi kesalahan, silahkan cek inputan.',
                'data' => $payload->errors
            ], 422);
        }

        $data = [];
        $period = new \DatePeriod(
            new \DateTime($payload->tanggalawal),
            new \DateInterval('P1D'),
            (new \DateTime($payload->tanggalakhir))->add(new \DateInterval('P1D'))
        );
        foreach($period as $_date){
            $res = (new BpjsJkn)->antrianPerTanggal($_date->format('Y-m-d'));
            $code = ArrayHelper::getValue($res,'metadata.code');
            $response = ArrayHelper::getValue($res,'response');
            if($code == 200 && is_array($response) && count($response) > 0){
                Yii::$app->db->createCommand('UPDATE bpjs_antrian_tanggal_t SET is_deleted=true WHERE tanggal = :tanggal')
                        ->bindValue(':tanggal',$_date->format('Y-m-d'))
                        ->execute();
                foreach($response as $_item){
                    $_item['created_date'] = date('Y-m-d H:i:s');
                    $_item['created_by'] = 1;
                    $_item['is_deleted'] = false;
                    $_item['last_sync'] = date('Y-m-d H:i:s');
                    $data[] = $_item;
                }
            }
        }
        $exec = 0;
        if(count($data)>0){
            $exec = BpjsAntrianTanggal::batchInsert($data,true);

            $data_taskid = [];
            foreach($data as $_data){
                if(isset($_data['kodebooking'])){
                    $res = (new BpjsJkn)->getListTaskJkn(['kodebooking'=>$_data['kodebooking']]);
                    $code = ArrayHelper::getValue($res,'metadata.code');
                    $response = ArrayHelper::getValue($res,'response');
                    if($code == 200 && is_array($response) && count($response) > 0){
                        Yii::$app->db->createCommand('UPDATE bpjs_list_task_t SET is_deleted=true WHERE kodebooking = :kodebooking')
                                ->bindValue(':kodebooking',$_data['kodebooking'])
                                ->execute();
                        $pattern = '/WIB$/i';
                        foreach($response as $_item){
                            $_item['created_date'] = date('Y-m-d H:i:s');
                            $_item['created_by'] = 1;
                            $_item['is_deleted'] = false;
                            $_item['last_sync'] = date('Y-m-d H:i:s');
                            $wakturs_time = preg_replace($pattern, '', $_item['wakturs']);
                            $waktu_time = preg_replace($pattern, '', $_item['waktu']);
                            $_item['wakturs_time'] = strtotime($wakturs_time);
                            $_item['waktu_time'] = strtotime($waktu_time);
                            $data_taskid[] = $_item;
                        }
                    }
                }
            }
            if(count($data_taskid)>0){
                $exec_taskid = BpjsListTask::batchInsert($data_taskid,true);
            }
        }
        return [
            'message' => 'Sinkron Antrean JKN berhasil dijalankan',
            'text' => 'Sinkron Antrean JKN berhasil dijalankan',
            'data' => $exec
        ];
    }

    public function actionAntrianPerKodeBooking()
    {
        $kodebooking = Yii::$app->request->post('kodebooking', null);
        $res = (new BpjsJkn)->antrianPerKodeBooking($kodebooking);
        $code = ArrayHelper::getValue($res,'metadata.code');
        $response = ArrayHelper::getValue($res,'response.0', []);
        if($code == 200 && is_array($response) && !empty($response)){
            Yii::$app->db->createCommand('UPDATE bpjs_antrian_tanggal_t SET is_deleted=true WHERE kodebooking = :kodebooking')
                    ->bindValue(':kodebooking',$kodebooking)
                    ->execute();

            $response['created_date'] = date('Y-m-d H:i:s');
            $response['created_by'] = 1;
            $response['is_deleted'] = false;
            $response['last_sync'] = date('Y-m-d H:i:s');
            $bpjs_antrian_tanggal = new BpjsAntrianTanggal;
            $bpjs_antrian_tanggal->attributes = $response;
            $bpjs_antrian_tanggal->save(false);
            $res = (new BpjsJkn)->getListTaskJkn(['kodebooking'=>$kodebooking]);
            $code = ArrayHelper::getValue($res,'metadata.code');
            $response = ArrayHelper::getValue($res,'response');

            if($code == 200 && is_array($response) && count($response) > 0){
                Yii::$app->db->createCommand('UPDATE bpjs_list_task_t SET is_deleted=true WHERE kodebooking = :kodebooking')
                        ->bindValue(':kodebooking', $kodebooking)
                        ->execute();
                $pattern = '/WIB$/i';

                foreach($response as $_item){
                    $_item['created_date'] = date('Y-m-d H:i:s');
                    $_item['created_by'] = 1;
                    $_item['is_deleted'] = false;
                    $_item['last_sync'] = date('Y-m-d H:i:s');
                    $wakturs_time = preg_replace($pattern, '', $_item['wakturs']);
                    $waktu_time = preg_replace($pattern, '', $_item['waktu']);
                    $_item['wakturs_time'] = strtotime($wakturs_time);
                    $_item['waktu_time'] = strtotime($waktu_time);
                    $data_taskid[] = $_item;
                }

                if (!empty($data_taskid)) {
                    $exec_taskid = BpjsListTask::batchInsert($data_taskid,true);
                }
            }
        }
    }

    public function actionResendAntrianByDaftar()
    {
        $pendaftaran_id = Yii::$app->request->post('pendaftaran_id',[]);
        $jkn = (new \yii\db\Query())
            ->select([
                'antrianjkn_v.antrian_id',
                'antrianjkn_v.tanggal_periksa',
                'antrianjkn_v.nomorkartu',
                'antrianjkn_v.no_identitas_pasien',
                'antrianjkn_v.no_telepon_pasien',
                'antrianjkn_v.status_pasien',
                'antrianjkn_v.jenispasien',
                'antrianjkn_v.jeniskunjungan',
                'antrianjkn_v.nomorreferensi',
                'antrianjkn_v.no_rekam_medik',
                'antrianjkn_v.pendaftaran_id',
                'antrianjkn_v.kodebooking',
                'antrianjkn_v.nomorantrean',
                'antrianjkn_v.angkaantrean',
                'antrianjkn_v.kuotajkn',
                'antrianjkn_v.kuotanonjkn',
                'antrianjkn_v.kodepoli',
                'antrianjkn_v.namapoli',
                'antrianjkn_v.kodedokter',
                'antrianjkn_v.jampraktek',
                'antrianjkn_v.jam_mulai',
                'antrianjkn_v.jam_selesai',
                'antrianjkn_v.keterangan',
                'bpjs_t.asal_rujukan',
                'ruangan_m.kode_ruangan_bpjs'
            ])
            ->from('antrianjkn_v')
            ->leftJoin('pendaftaran_t','pendaftaran_t.pendaftaran_id = antrianjkn_v.pendaftaran_id')
            ->leftJoin('bpjs_t','bpjs_t.bpjs_id = pendaftaran_t.bpjs_id')
            ->leftJoin('ruangan_m','ruangan_m.ruangan_id = pendaftaran_t.ruangan_id')
            ->where(['antrianjkn_v.pendaftaran_id'=>$pendaftaran_id])
            ->all();
        foreach($jkn as $_jkn){

            $getJumlahSep = (new Bpjs)->jumlahSEP([
                'jenisRujukan'=>$_jkn['asal_rujukan'] == 2 ? '2' : '1',
                'noRujukan'=>$_jkn['nomorreferensi']
            ]);
            $jumlahSep = (int) ArrayHelper::getValue($getJumlahSep,'response.jumlahSEP',0);
            $jenisKunjungan = $_jkn['jeniskunjungan'];
            if($jumlahSep == 0 && $_jkn['kode_ruangan_bpjs'] == $_jkn['kodepoli']){
                $jenisKunjungan = $_jkn['asal_rujukan'] == 2 ? '4' : '1';
            }elseif($jumlahSep >= 1 && $_jkn['kode_ruangan_bpjs'] == $_jkn['kodepoli']){
                $jenisKunjungan = 3;
            }elseif($jumlahSep >= 1 && $_jkn['kode_ruangan_bpjs'] != $_jkn['kodepoli']){
                $jenisKunjungan = 2;
            }

            $tanggalPeriksa = date('Y-m-d',strtotime($_jkn['tanggal_periksa']));
            $estimasiDilayani =  strtotime($tanggalPeriksa.' '.$_jkn['jam_mulai']);
            $request = [
                'kodebooking' => $_jkn['kodebooking'],
                'jenispasien' => $_jkn['jenispasien'],
                'nomorkartu' => $_jkn['nomorkartu'] ? $_jkn['nomorkartu'] : '',
                'nik' => $_jkn['no_identitas_pasien'] ? $_jkn['no_identitas_pasien'] : '',
                'nohp' => $_jkn['no_telepon_pasien'] ? $_jkn['no_telepon_pasien'] : '',
                'pasienbaru' => $_jkn['status_pasien'],
                'norm' => $_jkn['no_rekam_medik'],
                'tanggalperiksa' => $tanggalPeriksa,
                'jeniskunjungan' => $jenisKunjungan,
                'nomorreferensi' => $_jkn['nomorreferensi'],
                'nomorantrean' => $_jkn['nomorantrean'],
                'angkaantrean' => $_jkn['angkaantrean'],
                'kodepoli' => isset($_jkn['kodepoli']) ? $_jkn['kodepoli'] : '-',
                'namapoli' => isset($_jkn['namapoli']) ? $_jkn['namapoli'] : '-',
                'kodedokter' => isset($_jkn['kodedokter']) ? $_jkn['kodedokter'] : '-',
                'namadokter' => isset($_jkn['namadokter']) ? $_jkn['namadokter'] : '-',
                'jampraktek' => isset($_jkn['jampraktek']) ? $_jkn['jampraktek'] : '-',
                'estimasidilayani' => $estimasiDilayani,
                'sisakuotajkn' => isset($_jkn['sisakuotajkn']) ? $_jkn['sisakuotajkn'] : 0,
                'kuotajkn' => isset($_jkn['kuotajkn']) ? $_jkn['kuotajkn'] : 0,
                'sisakuotanonjkn' => isset($_jkn['sisakuotanonjkn']) ? $_jkn['sisakuotanonjkn'] : 0,
                'kuotanonjkn' => isset($_jkn['kuotanonjkn']) ? $_jkn['kuotanonjkn'] : 0,
                'keterangan' => $_jkn['keterangan'],
            ];
            $send = (new BpjsJkn)->simpanAntrianJkn($request);
            $model = new LoginJknR;
            $model->pendaftaran_id = $_jkn['pendaftaran_id'];
            $model->created_date = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->jwt->user->loginpemakai_id;
            $model->payload = isset($request) ? json_encode($request) : null;
            $model->sync_respon = isset($send) ? json_encode($send) : null;

            $model->save();
        }
        return $jkn;
    }
    
    public function actionGetKodebookingTaskUncomplete()
    {
        $model = (new BpjsInfoAntreanFn([
            'extParam' => [
                date('Y-m-d'),
                null,
                null
            ]
        ]));

        $kodebookings = $model::find()->select('kodebooking')
                          ->andWhere(['=', new \yii\db\Expression('(tgl_mulai_antrian::date)'), date('Y-m-d')])
                          ->andWhere(['is_success_antrean' => true])
                          ->andWhere(['or', ['and', 'task7 is null', 'tgl_order_resep is not null'], ['and', 'task5 is null', 'tgl_order_resep is null']])
                          ->column();
                          
        return $kodebookings;
    }

    public function actionResendTaskAntreanMultiple()
    {
        $kodebookings = Yii::$app->request->post('kodebookings',[]);
        $isGetKodebookingTaskUncomplete = Yii::$app->request->post('is_get_kodebooking_task', false);
        if ($isGetKodebookingTaskUncomplete) {
            $kodebookings = $this->actionGetKodebookingTaskUncomplete();
        }
        $response = [];
        
        (new RabbitBgProcess())->send([
            'type_sinkron' => "resend-all",
            'kodebooking' => $kodebookings,
            'xOwner' => Yii::$app->request->getHeaders()->get('X-Owner'),
            'token' => Yii::$app->request->getHeaders()->get('Authorization')
        ], 'resend_task_jkn', 'resend_task_jkn');
        
        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
            'text' => 'Proses Resend Task Berhasil.'
        ]);
    }

    public function actionResendTaskAntrean()
    {
        $kodebooking = Yii::$app->request->post('kodebooking');
        $query = "
            SELECT max(taskid) as last
            FROM bpjs_list_task_t
            WHERE kodebooking = :kodebooking
        ";
        $result = Yii::$app->db->createCommand($query)->bindValue(':kodebooking',$kodebooking)->queryScalar();

        $query = (new FGetReservasi(['extParam'=>[$kodebooking]]))->find()->asArray()->limit(1)->all(); // tidak bisa menggunakan query one atau asArray one
        $data = ArrayHelper::getValue($query, '0', []);

        $isbatal = isset($result) && $result == 99 ? true : false;
        $counter_process = 0;
        $is_success_antrean = ArrayHelper::getValue($data,'is_success_antrean');
        $konfig_flow_task = (new DocoConstansId)->actionGetAdditional('flow_taskid_alur_jam_pelayanan'); // if exists return string else null
        $konfig_flow_task = $konfig_flow_task ? strtoupper($konfig_flow_task) == 'TRUE' : FALSE;
        if(!$isbatal && $data !== null && $is_success_antrean == TRUE){
            $ispasienbaru = isset($data['status_pasien']) && $data['status_pasien'] == 310 ? true : false;
            $isresep = isset($data['tgl_order_resep']) && !empty($data['tgl_order_resep']) ? true : false;
            $start_task = isset($result)? $result+1 : (($ispasienbaru) ? 1 : 3);
            $latest_task = $isresep ? 7 : 5;
            $current_time = time();
            for ($i=$start_task; $i <= $latest_task ; $i++) {
                $time = $current_time + ($i * 60);
                switch ($i) {
                    case 1:
                        if ($konfig_flow_task) {
                            if (isset($data['tgl_selesai_pendaftaran'])) {
                                $time = strtotime($data['tgl_selesai_pendaftaran']) - (15); // -15 detik dari pendaftaran_t.created_date
                            } else {
                                continue 2;
                            }
                        } else {
                            if(isset($data['tgl_mulai_antrian'])){
                                $time = strtotime($data['tgl_mulai_antrian']);
                            } else {
                                continue 2;
                            }
                        }
                        break;
                    case 2:
                        if ($konfig_flow_task) {
                            if (isset($data['tgl_selesai_pendaftaran'])) {
                                $time = strtotime($data['tgl_selesai_pendaftaran']) - (10) ; // -10 detik dari pendaftaran_t.created_date
                            } else {
                                continue 2;
                            }
                        } else {
                            if (isset($data['tgl_mulai_pendaftaran'])) {
                                $time = strtotime($data['tgl_mulai_pendaftaran']);
                            } elseif (isset($data['tgl_mulai_antrian']) && isset($data['pendaftaran_id'])) {
                                $time = strtotime($data['tgl_mulai_antrian']) + (2 *60);
                            } else {
                                continue 2;
                            }
                        }
                        break;
                    case 3:
                        if ($konfig_flow_task) {
                            if (isset($data['tgl_created_soap_by_dpjp'])) {
                                $time = strtotime($data['tgl_created_soap_by_dpjp']);
                            } else {
                                continue 2;
                            }
                        } else {
                            if(isset($data['tgl_selesai_pendaftaran'])){
                                $time = strtotime($data['tgl_selesai_pendaftaran']);
                            } else {
                                continue 2;
                            }
                        }
                        break;
                    case 4:
                        if ($konfig_flow_task) {
                            if (isset($data['tgl_created_resumemedis'])) {
                                $time = strtotime($data['tgl_created_resumemedis']);
                            } else {
                                continue 2;
                            }
                        } else {
                            if(isset($data['tgl_masukperiksa'])){
                                $time = strtotime($data['tgl_masukperiksa']);
                            } else {
                                continue 2;
                            }
                        }
                        break;
                    case 5:
                        if (isset($data['tgl_tindaklanjut_pasien'])) {
                            $time = strtotime($data['tgl_tindaklanjut_pasien']);
                        } else {
                            continue 2;
                        }
                        break;
                    case 6:
                        if(isset($data['tgl_cetak_etiket'])){
                            $time = strtotime($data['tgl_cetak_etiket']);
                        }else{
                            continue 2;
                        }
                        break;
                    case 7:
                        if(isset($data['tgl_serahkan_resep'])){
                            $time = strtotime($data['tgl_serahkan_resep']);
                        }else{
                            continue 2;
                        }
                        break;

                    default:
                        # code...
                        break;
                }
                $payload = [
                    'kodebooking' => $kodebooking,
                    'taskid' => $i,
                    'waktu' => $time * 1000
                ];
                Yii::error($payload);
                Yii::error(isset($kodebooking) && isset($i) && isset($time));
                if(isset($kodebooking) && isset($i) && isset($time)){
                    $mBpjs = new BpjsJkn;
                    $mBpjs->antrian_jkn = $payload;
                    $result = $mBpjs->updateAntrianJkn();
                    Yii::error('kirim task '. $i);

                    $model = new LoginJknR;
                    $model->pendaftaran_id = ArrayHelper::getValue($data,'pendaftaran_id');
                    $model->pendaftaranol_id = ArrayHelper::getValue($data,'pendaftaranol_id');
                    $model->state = $i;
                    $model->created_date = date('Y-m-d H:i:s');
                    $model->created_by = isset(Yii::$app->jwt->user->loginpemakai_id) ? Yii::$app->jwt->user->loginpemakai_id : 1;
                    $model->payload = isset($payload) ? json_encode($payload) : null;
                    $model->sync_respon = isset($result) ? json_encode($result) : null;

                    $model->save();
                    $counter_process++;
                }
            }
        }
        if($counter_process == 0){
            Yii::error('Kode Booking: '.$kodebooking.' Tidak ada yang diproses');
            return [
                'message' => 'Tidak ada yang diproses',
                'text' => 'Tidak ada yang diproses'
            ];
        }else{
            Yii::$app->request->setBodyParams([
                'kodebooking' => $kodebooking
            ]);
            Yii::$app->runAction('/v1/sync-bpjs/list-task');
        }

        return [
            'message' => 'Ok',
            'text' => 'Berhasil'
        ];
    }

    public function actionMonitoringKunjungan()
    {
        $date = date('Y-m-d');
        $jenis = 2;
        return (new Bpjs)->monitoringKunjungan($date,$jenis);
    }

    public function actionListAntrol()
    {
        $dataAntrol = (new \yii\db\Query())
            ->select([
                'antrianjkn_v.pendaftaran_id',
                'antrianjkn_v.tgl_pendaftaran',
                'antrianjkn_v.kodebooking',
                'antrianjkn_v.antrian_id',
                'antrianjkn_v.tanggal_periksa',
                'antrianjkn_v.nomorkartu',
                'antrianjkn_v.no_identitas_pasien',
                'antrianjkn_v.no_telepon_pasien',
                'antrianjkn_v.status_pasien',
                'antrianjkn_v.jenispasien',
                'antrianjkn_v.jeniskunjungan',
                'antrianjkn_v.nomorreferensi',
                'antrianjkn_v.no_rekam_medik',
                'antrianjkn_v.pendaftaran_id',
                'antrianjkn_v.nomorantrean',
                'antrianjkn_v.angkaantrean',
                'antrianjkn_v.kuotajkn',
                'antrianjkn_v.kuotanonjkn',
                'antrianjkn_v.kodepoli',
                'antrianjkn_v.namapoli',
                'antrianjkn_v.kodedokter',
                'antrianjkn_v.jampraktek',
                'antrianjkn_v.jam_mulai',
                'antrianjkn_v.jam_selesai',
                'antrianjkn_v.keterangan',
                'bpjs_t.asal_rujukan',
                'ruangan_m.kode_ruangan_bpjs'
            ])
            ->from('antrianjkn_v')
            ->leftJoin('pendaftaran_t','pendaftaran_t.pendaftaran_id = antrianjkn_v.pendaftaran_id')
            ->leftJoin('bpjs_t','bpjs_t.bpjs_id = pendaftaran_t.bpjs_id')
            ->leftJoin('ruangan_m','ruangan_m.ruangan_id = pendaftaran_t.ruangan_id')
            ->where(['tipe'=>'offline']);

        $antrol = $dataAntrol->limit(10)->all();
        return $antrol;
    }

    public function actionListTask()
    {
        $kodebooking = Yii::$app->request->post('kodebooking',[]);
        if(!is_array($kodebooking)){
            $kodebooking = [$kodebooking];
        }
        $data = [];
        foreach ($kodebooking as $_kodebooking)
        {
            $res = (new BpjsJkn)->getListTaskJkn(['kodebooking'=>$_kodebooking]);
            $code = ArrayHelper::getValue($res,'metadata.code');
            $response = ArrayHelper::getValue($res,'response');
            if($code == 200 && is_array($response) && count($response) > 0){
                Yii::$app->db->createCommand('UPDATE bpjs_list_task_t SET is_deleted=true WHERE kodebooking = :kodebooking')
                        ->bindValue(':kodebooking',$_kodebooking)
                        ->execute();
                $pattern = '/WIB$/i';
                foreach($response as $_item){
                    $_item['created_date'] = date('Y-m-d H:i:s');
                    $_item['created_by'] = 1;
                    $_item['is_deleted'] = false;
                    $_item['last_sync'] = date('Y-m-d H:i:s');
                    $wakturs_time = preg_replace($pattern, '', $_item['wakturs']);
                    $waktu_time = preg_replace($pattern, '', $_item['waktu']);
                    $_item['wakturs_time'] = strtotime($wakturs_time);
                    $_item['waktu_time'] = strtotime($waktu_time);
                    $data[] = $_item;
                }
            }
        }
        $exec = 0;
        if(count($data)>0){
            $exec = BpjsListTask::batchInsert($data,true);
        }
        return [
            'message' => 'OK',
            'data' => $exec
        ];
    }

    public function actionInfoAntrean()
    {
        

        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $start = date('Y-m-d');
        $end = date('Y-m-d');

        if (isset($advancedFilters['tgl_pendaftaran'])) {
            $explode = explode(' - ',$advancedFilters['tgl_pendaftaran']);
            $start = date('Y-m-d',strtotime($explode[0]));
            $end = date('Y-m-d',strtotime($explode[1]));
            unset($_GET['advanced-filter']['tgl_pendaftaran']);
        }

        $model = (new BpjsInfoAntreanFn([
            'extParam' => [
                null,
                $start,
                $end
            ]
        ]));

        $query = $model::find();

        if(isset($advancedFilters['jenis_pasien'])){
            $query->andWhere(['jenis_pasien'=>$advancedFilters['jenis_pasien']]);
            unset($_GET['advanced-filter']['jenis_pasien']);
        }
        
        // Filter status lengkap
        if(isset($advancedFilters['pendaftaran_id'])){
            switch ($advancedFilters['pendaftaran_id']) {
              case 'resep-belum-lengkap':
                $query->andWhere(['and', 'task5 is null', 'tgl_order_resep is not null']);

                break;

              case 'non-resep-belum-lengkap':
                $query->andWhere(['and', 'task7 is null', 'tgl_order_resep is null']);
                break;

              default:
                // code...
                break;
            }
            unset($_GET['advanced-filter']['pendaftaran_id']);
        }
        
        if(isset($advancedFilters['status'])){
            switch ($advancedFilters['status']) {
              case 'selesai':
                $query->andWhere(['status' => 'Selesai dilayani']);
                break;

              case 'belum':
                $query->andWhere(['status' => 'Belum dilayani']);
                break;
                
              case 'kosong':
                $query->andWhere(['is', 'status', null]);
                break;

              default:
                // code...
                break;
            }
            unset($_GET['advanced-filter']['status']);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->asArray();
        
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionResendCreateAntreanJkn()
    {
        $request = DynamicModel::validateData(['tanggal' => Yii::$app->request->post('tanggal')], [
            [['tanggal'], 'safe'],
            [['tanggal'], 'date', 'format' => 'php:Y-m-d'],
        ]);

        if (!$request->validate()) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $request->errors
            ]);
        }

        $tgl_kodebooking = Yii::$app->request->post('tanggal', date('Y-m-d'));
        $resend_kodebooking = [];
        $kodebooking_from_jkn = [];
        $kodebooking_processed = 0;

        $kodebooking_from_sirs = (new PendaftaranOnline)->getJknDataReservasi($tgl_kodebooking);

        $res_kode_booking_from_jkn = (new BpjsJkn)->antrianPerTanggal($tgl_kodebooking);
        if (ArrayHelper::getValue($res_kode_booking_from_jkn, 'metadata.code', false) == 200) {
            $kodebooking_from_jkn = ArrayHelper::getColumn(ArrayHelper::getValue($res_kode_booking_from_jkn, 'response', []), 'kodebooking');
        }

        if (!empty($kodebooking_from_sirs)) {
            $resend_kodebooking = array_filter($kodebooking_from_sirs,
                function ($val) use ($kodebooking_from_jkn) {
                    return !in_array($val['kodebooking'], $kodebooking_from_jkn);
                }
            );
        }


        /** jika ada kode yang tidak ada di antrian per tanggal
        *   maka lakukan resend create antrian dari payload additional_jkn  */
        if (!empty($resend_kodebooking)) {
            foreach ($resend_kodebooking as $val) {
                if (isset($val['payload_jkn']) && !empty($val['payload_jkn'])) {
                    $payload_jkn = $this->transformPayloadJkn(json_decode($val['payload_jkn'], true));
                    Yii::error($payload_jkn);

                    (new RabbitBgProcess())->send([
                        'payload' => $payload_jkn,
                        'pendaftaran_id' => ArrayHelper::getValue($val, 'pendaftaran_id'),
                        'pendaftaranol_id' => ArrayHelper::getValue($val, 'pendaftaranol_id'),
                        'is_logged' => true,
                        'xOwner' => Yii::$app->request->getHeaders()->get('X-Owner'),
                        'token' => Yii::$app->request->getHeaders()->get('Authorization')
                    ], 'resend_antrian_jkn', 'resend_antrian_jkn');

                    $kodebooking_processed++;

                }
            }
        }
 
        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
            'text' => $kodebooking_processed.'/'.count($resend_kodebooking).' reservasi sedang diproses'
        ]);
    }

    public function actionSyncRujukanKhusus()
    {
        $start_date = Yii::$app->request->post('start_date',date('Y-m-d'));
        $max_month_backdate = Yii::$app->request->post('max_month_backdate', 3);
        $response = [];
        
        (new RabbitBgProcess())->send([
            'start_date' => $start_date,
            'max_month_backdate' => $max_month_backdate,
            'xOwner' => Yii::$app->request->getHeaders()->get('X-Owner'),
            'token' => Yii::$app->request->getHeaders()->get('Authorization')
        ], 'sync_rujukan_khusus', 'sync_rujukan_khusus');
        
        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
            'text' => 'Proses Sync Rujukan Khusus BPJS berhasil dijalankan.'
        ]);
    }

    private function transformPayloadJkn($payload_jkn = [])
    {
        return [
            'kodebooking' => ArrayHelper::getValue($payload_jkn, 'kodebooking', ''),
            'jenispasien' => ArrayHelper::getValue($payload_jkn, 'jenispasien', ''),
            'nomorkartu' => ArrayHelper::getValue($payload_jkn, 'nomorkartu', ''),
            'nik' => isset($payload_jkn['nik']) ? $payload_jkn['nik'] : (isset($payload_jkn['no_identitas_pasien']) && (isset($payload_jkn['jenisidentitas']) && isset($payload_jkn['jenisidentitas']) == DocoConstants::CONS_ID_KTP) ? $payload_jkn['no_identitas_pasien'] : '' ),
            'nohp' => DocoHelpers::coalesce(ArrayHelper::getValue($payload_jkn, 'nohp', ''), ArrayHelper::getValue($payload_jkn, 'no_telepon_pasien', '')),
            'kodepoli'=> ArrayHelper::getValue($payload_jkn, 'kodepoli', ''),
            'namapoli' => ArrayHelper::getValue($payload_jkn, 'namapoli', ''),
            'pasienbaru' => isset($payload_jkn['pasienbaru']) && !empty($payload_jkn['pasienbaru']) ? $payload_jkn['pasienbaru'] : (isset($payload_jkn['status_pasien']) ? $payload_jkn['status_pasien'] : 0) ,
            'norm' => isset($payload_jkn['norm']) && !empty($payload_jkn['norm']) ? $payload_jkn['norm'] : (isset($payload_jkn['no_rekam_medik']) ? $payload_jkn['no_rekam_medik'] : ''),
            'tanggalperiksa' => isset($payload_jkn['tanggalperiksa']) && !empty($payload_jkn['tanggalperiksa']) ? $payload_jkn['tanggalperiksa'] : (isset($payload_jkn['tanggal_periksa']) ? date('Y-m-d', strtotime($payload_jkn['tanggal_periksa'])) : ''),
            'kodedokter' => ArrayHelper::getValue($payload_jkn, 'kodedokter', ''),
            'namadokter' => ArrayHelper::getValue($payload_jkn, 'namadokter', ''),
            'jampraktek' => ArrayHelper::getValue($payload_jkn, 'jampraktek', ''),
            'jeniskunjungan' => ArrayHelper::getValue($payload_jkn, 'jeniskunjungan', ''),
            'nomorreferensi' => ArrayHelper::getValue($payload_jkn, 'nomorreferensi', ''),
            'nomorantrean' => ArrayHelper::getValue($payload_jkn, 'nomorantrean', ''),
            'angkaantrean' => ArrayHelper::getValue($payload_jkn, 'angkaantrean', ''),
            'estimasidilayani' => ArrayHelper::getValue($payload_jkn, 'estimasidilayani', ''),
            'sisakuotajkn' => ArrayHelper::getValue($payload_jkn, 'sisakuotajkn', ''),
            'kuotajkn' => ArrayHelper::getValue($payload_jkn, 'kuotajkn', ''),
            'sisakuotanonjkn' => ArrayHelper::getValue($payload_jkn, 'sisakuotanonjkn', ''),
            'kuotanonjkn' => ArrayHelper::getValue($payload_jkn, 'kuotanonjkn', ''),
            'keterangan' => ArrayHelper::getValue($payload_jkn, 'keterangan', ''),
        ];
    }
}
