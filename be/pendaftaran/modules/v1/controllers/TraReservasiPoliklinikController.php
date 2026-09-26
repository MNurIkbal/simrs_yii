<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-29 15:53
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\TraReservasiPoliklinik;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\Pasien;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\constans\StatusReservasi;
use Doco\components\DocoConstansId;
use Exception;

class TraReservasiPoliklinikController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PendaftaranOnline';
    public $messageBroker = [
        'update' => [
            'services' => [
                'Sirs' => [
                    'CancelReservasi' => [
                        'payload' => ['id' => 'id'],
                        'successProcess' => true,
                        'state' => 'cancel',
                        'result' => true
                    ],
                    'StatusUpdateJkn' => [
                        'successProcess' => true,
                        'taskid' => ['99'],
                        'result' => true
                    ]
                ],
            ],
        ],
        'cancel' => [
            'services' => [
                'Mhg' => [
                    'UpdateAppointment' => [
                        'result' => true,
                        'successProcess'=>true,
                    ],
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        /*$verbs["detail"] = ["GET"];
        $verbs["delete"] = ["DELETE"]; */
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        /*unset($actions['index']);
        unset($actions['create']);
        unset($actions['delete']);*/
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new TraReservasiPoliklinik;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_buatjanji', $tgl_awal, $tgl_akhir]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function getDataPasien($id = null)
    {
        $data = Pasien::find()
                    ->select([
                       'pasien_id',
                       'no_rekam_medik',
                       'nama_pasien',
                       'tgl_rekam_medik',   
                       'tempat_lahir',   
                       'tanggal_lahir',   
                       'jeniskelamin',   
                       'nama_panggilan',   
                       'alamat_pasien',
                       'no_mobile_pasien',
                       'no_telepon_pasien',
                    ])->where(['no_rekam_medik'=>$id]);
        return $data;
    }

    public function actionDataPasien(){
        $model = new Pasien;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    //action buat handle select no RM
    public function actionGetDataRekamMedik()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $sql = "select no_rekam_medik from pasien_m where no_rekam_medik LIKE '%{$term}%' group by no_rekam_medik order by no_rekam_medik asc limit 50";
        $data = Yii::$app->db->createCommand($sql)->queryAll();     
        return $data;
    }

    public function actionGetData($id){
        $data_pasien = $this->getDataPasien($id);
        $data= $data_pasien->asArray()->one();
        
        $data['umur'] = $this->umur($data['tanggal_lahir']);
        $return = ['data_pasien'=>$data];
        return $return;
    }

    public static function umur($param, $umur_only = false)
    {
        $diff = date_diff(date_create(date('Y-m-d', strtotime($param))), date_create(date('Y-m-d')));
        if (!$umur_only){
            return "umur ".$diff->y." tahun ".$diff->m." bulan ".$diff->d." hari";
        }else{
            return $diff->y;
        }
    }

    public function getDay($tgl,$sep){
        $sepparator = $sep; //separator. contoh: '-', '/'
        $parts = explode($sepparator, $tgl);
        $d = date("l", mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]));
 
        if ($d=='Monday'){
            return '75';
        }elseif($d=='Tuesday'){
            return '76';
        }elseif($d=='Wednesday'){
            return '77';
        }elseif($d=='Thursday'){
            return '78';
        }elseif($d=='Friday'){
            return '79';
        }elseif($d=='Saturday'){
            return '80';
        }elseif($d=='Sunday'){
            return '81';
        }else{
            return 'ERROR!';
        }
    }

    //action generate penomoran
    public function generateId($lookup)
    {
        $sql = "select penomoran_nama,prefix,last_generate,last_number from penomoran_k where penomoran_id = '{$lookup}'";
        $id = \Yii::$app->db->createCommand($sql)->queryOne();
        $prefix = substr($id['last_generate'], 0,3);
        $date = substr($id['last_generate'], 3,8);
        $number = substr($id['last_generate'], -4);
        $now = date('Ymd');         
        $numPrefix = "";
        if($date != $now){
            $newDate = $now;
            $newNumber = '0001';
        }else{              
            $newDate = $date;
            $newNumber = $number+1;
            if(strlen($newNumber) == 1){
                $numPrefix = "000";
            }else if(strlen($newNumber) == 2){
                $numPrefix = "00";
            }else if(strlen($newNumber) == 3){
                $numPrefix = "0";                   
            }else{
                $numPrefix = "";
            }               
        }
        $newId = $prefix.$newDate.$numPrefix.$newNumber;
        $this->updateId($lookup, $newId, $numPrefix.$newNumber);
        return $newId;
    }

    //action buat update penomoran
    public function updateId($lookup, $newId, $last_number)
    {
        $sql = \Yii::$app->db->createCommand()->update('penomoran_k', ['last_generate'=>$newId,'last_number'=>$last_number], "penomoran_id = '{$lookup}'")->execute();
    }

    public function actionUpdate($id) 
    {
        try {
            $request = Yii::$app->request;
            $model = PendaftaranOnline::findOne($id);
            if ($request->post() && !empty($model)) {

                if (!$this->validateRequestTolakReservasi($request, $model)) {
                    return DocoHelpers::response([
                        'message' => 'Data Pendaftaran Online / Reservasi yang ditolak tidak boleh melebihi hari ini',
                    ], 422);
                }

                $model->attributes = $request->post();
                if ($model->save()) {
                    $checkingExtension = Yii::$app->docoPlugin->getExtension('pendaftaran_online');
                    return [
                        'message' => 'Data Berhasil di simpan',
                        'pendaftaranol_id' => $id,
                        'is_extensionmhg' => ($checkingExtension == 'Extensions\pendaftaran\PendaftaranOnlineMhg'),
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendaftaranOnline');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\web\UnprocessableEntityHttpException $e) {
            \Yii::$app->response->statusCode = $e->statusCode;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCreate()
    {
        try {
            $model = new TraReservasiPoliklinik;
            $request = Yii::$app->request;
            if($request->post()){       
                $now = date('Y-m-d H:i:s');     
                $post = $request->post(); 
                //$post = json_decode($post['value'], true);    
                //$post = $post['response'];
                //return $post;
                //$post = json_decode($post['dataisi'],true);                                                           
                $ruangan_id = !empty($post['ruangan_id']) ? $post['ruangan_id'] : null;
                $pasien_id = !empty($post['pasien_id']) ? $post['pasien_id'] : null;
                $status_janjipoli = !empty($post['status_janjipoli']) ? $post['status_janjipoli'] : null;
                $nama_pasien = !empty($post['nama_pasien']) ? $post['nama_pasien'] : null;
                $alamat_pasien = !empty($post['alamat_pasien']) ? $post['alamat_pasien'] : null;
                $pegawai_id = !empty($post['pegawai_id']) ? $post['pegawai_id'] : null;
                $antrian_id = !empty($post['antrian_id']) ? $post['antrian_id'] : null;
                $tgl_jadwal = !empty($post['tgl_jadwal']) ? $post['tgl_jadwal'] : null;
                $keterangan_buatjanji = !empty($post['keterangan_buatjanji']) ? $post['keterangan_buatjanji'] : null;
                $by_phone = !empty($post['by_phone']) ? $post['by_phone'] : null;
                $carabayar = !empty($post['carabayar_id']) ? $post['carabayar_id'] : null;
                $penjamin = !empty($post['penjamin_id']) ? $post['penjamin_id'] : null;
                $nobuatjanji = $this->generateId(7);
                $hari_jadwal = $this->getDay($tgl_jadwal, '/');
                $data_reservasi = [
                        'pasien_id'=>$pasien_id,
                        'pegawai_id'=>$pegawai_id,
                        'ruangan_id'=>$ruangan_id,
                        'status_janjipoli'=>357,
                        'hari_jadwal'=>$hari_jadwal,
                        'keterangan_buatjanji'=>$keterangan_buatjanji,
                        'antrian_id'=>1,
                        'no_buatjanji'=> $nobuatjanji,
                        'tgl_jadwal'=>$tgl_jadwal,
                        'tgl_buatjanji'=>$now,
                        'created_date'=>$now,
                        'is_deleted'=>0,
                        'is_active'=>1,
                        'by_phone'=>$by_phone,
                        'carabayar_id'=>$carabayar,
                        'penjamin_id'=>$penjamin,
                        'is_rencanakontrol'=>0,
                    ];      
                $save_reservasi = Yii::$app->db->createCommand()->insert('buatjanjipoli_t', $data_reservasi)->execute();                         
                }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];    
        }
    }

    public function actionCancel()
    {
        try {
            $request = Yii::$app->request;
            $model = PendaftaranOnline::findOne($request->post('id'));
            if ($request->post() && !empty($model)) {
                if ($model->status_daftar_ol == StatusReservasi::DISETUJUI) {
                    return [
                        'message' => 'Pendaftar Reservasi sudah di approve',
                        'status' => 422
                    ];
                }
                $model->status_daftar_ol = StatusReservasi::DITOLAK;
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                        'pendaftaranol_id'    => $model->pendaftaranol_id,
                        'status' => 200,
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendaftaranOnline');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // validate request untuk tolak reservasi dan pengecekan konfig
    private function validateRequestTolakReservasi($request, $pendaftaranol)
    {
        if ($request->post('status_daftar_ol') == null || $request->post('status_daftar_ol') != DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK) {
            return true;
        }

        if (!empty($pendaftaranol) && $pendaftaranol['status_daftar_ol'] == DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK) {
            throw new \yii\web\UnprocessableEntityHttpException('Pendaftaran Online Sudah Pernah Dibatalkan');
        }

        if ((bool) DocoConstansId::actionGetId('batal_hadir_reservasi')) {
            return true;
        }

        if (empty($pendaftaranol)) {
            throw new \yii\web\UnprocessableEntityHttpException('Pendaftaran Online Tidak Boleh Kosong (Validate Request Tolak Reservasi)');
        } 
        
        if (!empty($pendaftaranol['tgl_pendaftaranol']) && date('Y-m-d', strtotime($pendaftaranol['tgl_pendaftaranol'])) > date('Y-m-d')) {
            return false;
        } else {
            return true;
        }

    }
}