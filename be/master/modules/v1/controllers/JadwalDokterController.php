<?php

namespace app\modules\v1\controllers;

use Yii;
use \DateTime;
use \DateInterval;
use \DatePeriod;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoAccessRule;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoJwtHttpBearerAuth;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\JadwalDokterView;
use app\modules\v1\models\StokKuotaDokter;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\Notifikasi;
use app\modules\v1\models\JadwalCutiView;
use app\modules\v1\models\JadwalCuti;
use app\modules\v1\models\DataDokterView;
use app\modules\v1\models\JadwalBukaPoliklinik;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\filters\AccessControl;
use Doco\components\DocoNotification;
use app\modules\v1\models\NotifikasiComponent;
use app\modules\v1\models\KonfigSystemK;
use app\modules\v1\models\InfoJadwalDokterView;
use yii\base\DynamicModel;
use app\modules\v1\cache\Cache;
use SirsCore\businessLogic\AntrianPoliLogic;
use Doco\Services\Cache as GeneralCache;
use Doco\Services\InternalService;
use Doco\models\JadwalDokterInt;


class JadwalDokterController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\JadwalDokter';
    const HARI = [
        75 => 1,
        76 => 2,
        77 => 3,
        78 => 4,
        79 => 5,
        80 => 6,
        81 => 7,
    ];
    const APPOINTMENT = 'By Appointment';
    const SCHEDULE = 'By Schedule';

    /**
     * Untuk Kebutuhan Integerasi Odoo
     * @var array
     */
    public $messageBroker = [
        'create' => [
            'services' => [
                'Mhg' => [
                    'DoctorSchedule' => [
                        'last_insert' => true,
                        'state' => 'create',
                        'successProcess' => true
                    ]
                ],
                'Sirs' => [
                    'CreateSlot' => [
                        'successProcess' => true
                    ]
                ],
            ]
        ],
        'update' => [
            'services' => [
                'Mhg' => [
                    'DoctorSchedule' => [
                        'query_params' => ['id'],
                        'state' => 'update',
                        'successProcess' => true
                    ]
                ],
                'Sirs' => [
                    'UpdateSlot' => [
                        'query_params' => ['id'],
                        'successProcess' => true
                    ],
                    // 'UpdateDoctorScheduleJkn' => [
                    //     'query_params' => ['id'],
                    //     'successProcess' => true
                    // ]
                ],
            ]
        ],
        'delete' => [
            'services' => [
                'Mhg' => [
                    'DoctorSchedule' => [
                        'payload' => ['id'],
                        'state' => 'delete',
                        'successProcess' => true
                    ]
                ],
                'Sirs' => [
                    'UpdateSlot' => [
                        'payload' => ['id'],
                        'successProcess' => true
                    ]
                ],
            ]
        ],
        'update-jadwal-dokter' => [
            'services' => [
                'Mhg' => [
                    'UpdateDoctorSchedule' => [
                        'state' => 'update',
                        'successProcess' => true
                    ]
                ],
            ]
        ],
        'update-slot-dokter' => [
            'services' => [
                'Sirs' => [
                    'UpdateSlotDokter' => [
                        'result' => true,
                        'successProcess' => true,
                    ]
                ],
            ]
        ],
        'generate-slot-jadwal-dokter' => [
            'services' => [
                'Sirs' => [
                    'UpdateSlotDokter' => [
                        'query_params' => ['integrasi'],
                        'result' => true,
                        'state' => 'generate_slot',
                        'successProcess' => true,
                    ]
                ],
            ]
        ],
        'create-cuti' => [
            'services' => [
                'Sirs' => [
                    'UpdateCutiDokter' => [
                        'result' => true,
                        'state' => 'disable',
                        'successProcess' => true,
                    ]
                ],
                'Mhg' => [
                    'DoctorLeave' => [
                        'result' => true,
                        'state' => 'create',
                        'successProcess' => true
                    ]
                ],
            ]
        ],
        'disable-jadwal-dokter-cuti' => [
            'services' => [
                'Sirs' => [
                    'UpdateCutiDokter' => [
                        'result' => true,
                        'state' => 'disable',
                        'successProcess' => true,
                    ]
                ],
            ]
        ],
        'enable-jadwal-dokter-cuti' => [
            'services' => [
                'Sirs' => [
                    'UpdateCutiDokter' => [
                        'result' => true,
                        'state' => 'enable',
                        'successProcess' => true,
                    ]
                ],
            ]
        ],
        'delete-cuti' => [
            'services' => [
                'Sirs' => [
                    'UpdateCutiDokter' => [
                        'result' => true,
                        'state' => 'enable',
                        'successProcess' => true,
                    ]
                ],
                'Mhg' => [
                    'DoctorLeave' => [
                        'result' => true,
                        'state' => 'delete',
                        'successProcess' => true
                    ]
                ],
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        $verbs["create-cuti"] = ["POST"];
        $verbs["delete-cuti"] = ["DELETE"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => [
                'update-jadwal-dokter',
                'disable-jadwal-dokter-cuti',
                'enable-jadwal-dokter-cuti',
            ],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => [
                'update-jadwal-dokter',
                'disable-jadwal-dokter-cuti',
                'enable-jadwal-dokter-cuti',
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        if($tgl_kunjungan = $request->get('tgl_kunjungan', null)) { // hanya untuk kalau ada tgl_kunjungannya dari pendaftaranol_t
            $query = (new JadwalDokter)->getJadwalCountReservasi($tgl_kunjungan);
            $hariId = date('N', strtotime($tgl_kunjungan)) + 74;
            $query .= " AND hari = $hariId";
        } else {
            $query = (new JadwalDokter)->getJadwal();
        }
        if ($instalasi_id = $request->get('instalasi_id')) {
            $query .= " AND jadwaldokter_m.instalasi_id = $instalasi_id";
        }
        if ($ruangan_id = $request->get('ruangan_id')) {
            $query .= " AND jadwaldokter_m.ruangan_id = $ruangan_id";
        }
        if ($dokter_id = $request->get('dokter_id')) {
            $query .= " AND jadwaldokter_m.pegawai_id = $dokter_id";
        }
        $hari = null;
        if ($hari = $request->get('hari')) {
            $query .= " AND hari = $hari";
        }
        if ($jam_mulai = $request->get('jam_mulai')) {
            $query .= " AND to_char(jadwaldokter_m.jadwaldokter_mulai, 'HH24:MI') >= '$jam_mulai'";
        }
        if ($jam_selesai = $request->get('jam_selesai')) {
            $query .= " AND to_char(jadwaldokter_m.jadwaldokter_tutup, 'HH24:MI') <= '$jam_selesai'";
        }
        if ($is_active = $request->get('is_active')) {
            $query .= " AND jadwaldokter_m.is_active = $is_active";
        }

        $query .= " ORDER BY ruangan_nama, nama_pegawai, hari_jadwalbuka ASC";
        // Yii::error($query);
        $jadwal_data = Yii::$app->db->createCommand($query)->queryAll();

        $sql = '
            SELECT kuota_antrian FROM konfigsystem_k
        ';
        $query = Yii::$app->db->createCommand($sql)->queryOne();
        $cek_kuota_antrian = $query['kuota_antrian'];

        $data = [];
        $list_ruangan = [];
        $temp_list_ruangan = [];
        $no = 1;
        foreach ($jadwal_data as $value) {
            $resource_id = $value['ruangan_id'].'-'.$value['pegawai_id'].'-'.$value['hari_jadwalbuka'];
            if(!isset($temp_list_ruangan[$resource_id])){
                $temp_list_ruangan[$resource_id] = true;
                $list_ruangan[] = [
                    'id' => $resource_id,
                    'title' => $no.' - '.$value['ruangan_nama'].' - '.$value['nama_pegawai_lengkap'].' - '.$value['hari'],
                ];
                $no++;
            }

            $tgl_pendaftaran_mulai = date('Y-m-d H:i:s', strtotime($value['waktu_mulai']));
            $tgl_pendaftaran_selesai = date('Y-m-d H:i:s', strtotime($value['waktu_selesai']));
            $hari_buka = $value['hari_jadwalbuka'];

            // Get tanggal pendaftaran
            $date = date('Y-m-d');
            $no_hari = date('w', strtotime($date));
            $tgl_pendaftaran_mulai = date('Y-m-d H:i:s', strtotime((self::HARI[$hari_buka] - $no_hari).' day', strtotime($tgl_pendaftaran_mulai)));
            $tgl_pendaftaran_selesai = date('Y-m-d H:i:s', strtotime((self::HARI[$hari_buka] - $no_hari).' day', strtotime($tgl_pendaftaran_selesai)));

            switch ($value['is_active']) {
                case true:
                    $color = 'hsl(145, 82%, 61%)';
                    break;
                case false:
                    $color = 'rgb(224, 224, 224)';
                    break;
                default:
                    break;
            }
            $data[] = [
                'id'                  => DocoHelpers::encrypt($value['jadwaldokter_id']),
                'jadwaldokter_id'       => $value['jadwaldokter_id'],
                'resourceId'          => $resource_id,
                'start'               => $value['waktu_mulai'],
                'jam_rencana_mulai'   => $value['waktu_mulai'],
                'end'                 => $value['waktu_selesai'],
                'jam_rencana_selesai' => $value['waktu_selesai'],
                'title'               => $value['waktu_mulai'] . ' - ' . $value['waktu_selesai'],
                'className'           => 'text-center',
                'label'               => $value['nama_pegawai_lengkap'],
                'hari'                => $value['hari'],
                'nama_dokter'         => $value['nama_pegawai_lengkap'],
                'backgroundColor'     => $color,
                'textColor'           => 'rgb(0, 0, 0)',
                'status'              => $value['nama_pegawai'],
                'kuota'               => (isset($value['kuota'])) ? $value['kuota'] : '0',
                'sisa_kuota_onsite'   => $value['kuotaoffline_tersedia'],
                'kuota_online'        => (isset($value['kuota_online'])) ? $value['kuota_online'] : '0',
                'sisa_kuota_online'   => $value['kuota_tersedia'],
                'kuota_total'         => (isset($value['kuota_total'])) ? $value['kuota_total'] : '0',
                'cek'                 => $cek_kuota_antrian,
                // 'allDay' => false
            ];
        }

        return [
            'data_ruangan' => $list_ruangan,
            'jadwal_data' => $data
        ];
    }

    public function actionIndexWeb($instalasi_id=null, $ruangan_id=null, $pegawai_id=null, $hari = null, $jam_mulai=null, $jam_selesai = null)
    {
        $model = new JadwalDokter;
        $conditions = [];
        $conditionsTime = null;
        $conditionHari = null;
        if ($instalasi_id) {
            $conditions['instalasi_id'] = $instalasi_id;
        }
        if ($ruangan_id) {
            $conditions['ruangan_id'] = $ruangan_id;
        }
        if ($pegawai_id) {
            $conditions['pegawai_id'] = $pegawai_id;
        }
        if ($hari) {
            $conditionHari = $hari;
        }
        if ($jam_mulai) {
            $conditionsTime['jadwaldokter_mulai'] = date('H:i:s', strtotime($jam_mulai));
        }
        if ($jam_selesai) {
            $conditionsTime['jadwaldokter_tutup'] = date('H:i:s', strtotime($jam_selesai));
        }
        $query = $this->getData($conditions,$conditionsTime,$conditionHari, false);
        $data = $query->asArray()->all();

        $res = [];
        foreach ($data as $key => $value) {
            $path_foto = !empty($value['foto_pegawai'])
                ? Yii::$app->urlManagerFrontend->createUrl('') . "media/img/foto-dokter/" . $value["foto_pegawai"]
                : null;
            $res[] = [
                'jadwaldokter_id' => $value['jadwaldokter_id'],
                'ruangan_id' => $value['ruangan_id'],
                'ruangan_nama' => $value['ruangan_nama'],
                'instalasi_id' => $value['instalasi_id'],
                'instalasi_nama' => $value['instalasi_nama'],
                'pegawai_id' => $value['pegawai_id'],
                'nama_pegawai' => $value['nama_pegawai'],
                'hari_nama' => $value['hari_nama'],
                'jadwaldokter_mulai' => $value['jadwaldokter_mulai'],
                'jadwaldokter_tutup' => $value['jadwaldokter_tutup'],
                'maximumantrian' => $value['maximumantrian'],
                'kuota_online' => $value['kuota_online'],
                'kuota_tersedia' => (int) $value['kuota_tersedia'],
                'is_active' => $value['is_active'],
                'photo_pegawai' => $path_foto
            ];
        }
        return $res;
    }

    private function getData($conditions=[],$conditionsTime=null,$conditionHari=null, $is_mobile = true)
    {
        $condition = [];
        $sql = "
            SELECT
                t.jadwaldokter_id,
                t.ruangan_id,
                ruangan.ruangan_nama,
                t.instalasi_id,
                instalasi.instalasi_nama,
                t.pegawai_id,
                CONCAT(
                        gelarDepan.lookup_value,
                        '',
                        pegawai.nama_pegawai,
                        '',
                        gelarBelakang.gelarbelakang_nama
                ) as nama_pegawai,
                lookup_hari.lookup_name AS hari_nama,
                to_char(t.jadwaldokter_mulai, 'HH24:MI') as jadwaldokter_mulai,
                to_char(t.jadwaldokter_tutup, 'HH24:MI') as jadwaldokter_tutup,
                t.maximumantrian,
                t.kuota_online,
                t.is_active,
                r.kuota_tersedia as kuota_tersedia,
                pegawai.photopegawai as foto_pegawai
            FROM jadwaldokter_m t
            JOIN pegawai_m pegawai ON t.pegawai_id = pegawai.pegawai_id
            LEFT JOIN lookup_m gelarDepan ON pegawai.gelardepan::integer = gelarDepan.lookup_id
            LEFT JOIN gelarbelakang_m gelarBelakang ON pegawai.gelarbelakang::integer = gelarBelakang.gelarbelakang_id
            JOIN ruangan_m ruangan ON t.ruangan_id = ruangan.ruangan_id
            JOIN instalasi_m instalasi ON t.instalasi_id = instalasi.instalasi_id
            JOIN jadwalbukapoli_m bukapoli ON bukapoli.jadwalbukapoli_id = t.jadwalbukapoli_id
            JOIN lookup_m lookup_hari ON lookup_hari.lookup_id = bukapoli.hari
            LEFT JOIN kuotadokter_r r ON r.jadwaldokter_id = t.jadwaldokter_id
            WHERE t.is_deleted = false
            -- AND r.is_online = true
        ";
        if ($is_mobile) {
            $sql .= " AND r.is_online = true";
            $sql .= " AND t.is_active = true";
        }
        if ($conditions) {
            foreach ($conditions as $field=>$value) {
                $sql .= " AND t.{$field} = :{$field}";
                $condition[':' . $field] = $value;
            }
        }
        if($conditionHari){
            $sql .=" AND bukapoli.hari = :hari_id";
            $condition[':hari_id'] = $conditionHari;
        }
        if($conditionsTime){
            if($conditionsTime['jadwaldokter_mulai'] && $conditionsTime['jadwaldokter_tutup']){
                $sql .= " AND t.jadwaldokter_mulai >= :jadwaldokter_mulai AND t.jadwaldokter_tutup <= :jadwaldokter_tutup";
                $condition[':jadwaldokter_mulai'] = $conditionsTime['jadwaldokter_mulai'];
                $condition[':jadwaldokter_tutup'] = $conditionsTime['jadwaldokter_tutup'];
            }elseif($conditionsTime['jadwaldokter_mulai']){
                $sql .= " AND t.jadwaldokter_mulai >= :jadwaldokter_mulai";
                $condition[':jadwaldokter_mulai'] = $conditionsTime['jadwaldokter_mulai'];
            }elseif($conditionsTime['jadwaldokter_tutup']){
                $sql .= " AND t.jadwaldokter_tutup <= :jadwaldokter_tutup";
                $condition[':jadwaldokter_tutup'] = $conditionsTime['jadwaldokter_tutup'];
            }
        }

         $result = JadwalDokter::findBySql($sql, $condition);
         return $result;
    }

    public function actionCreate()
    {
        try {
            $dataPost = [];
            $request = Yii::$app->request;
            $model = new JadwalDokter;
            $model->scenario = 'create';
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post;
                $model->jadwaldokter_tgl = date('Y-m-d');
                if ($model->validate()) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'JadwalDokterForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'JadwalDokterForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                }

            }
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

    public function actionUpdateSlotDokter()
    {
        return [
            'message' => 'Data berhasil disimpan',
            'status'  => 200,

        ];
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = JadwalDokter::findOne($id);
            $model->scenario = 'update';
            $stok_kuota_dokter_updated = false;
            if ($request->post() && !empty($model)) {
                $jadwalbukapoli = JadwalBukaPoliklinik::find()->select(['jadwalbukapoli_id', 'hari'])
                                    ->where(['jadwalbukapoli_id' => $model->jadwalbukapoli_id])
                                    ->asArray()->one();
                if (ArrayHelper::getValue($jadwalbukapoli, 'hari') == DocoHelpers::getIdHariIni()) {
                    $stok_kuota_dokter_updated = $this->insertStokKuotaDokter($request, $model);
                }
                $model->attributes = $request->post();
                if ($model->update()) {
                    if ($request->post('is_skip_jkn', true) == false) {
                        $state = 'update-jkn';
                        $updateJadwalDokterJkn = JadwalDokterView::updateJadwalDokterJkn($model->jadwaldokter_id);
                        $this->setLogs($model->jadwaldokter_id, ArrayHelper::getValue($updateJadwalDokterJkn, 'result'), $state, ArrayHelper::getValue($updateJadwalDokterJkn, 'payload'));

                        if (ArrayHelper::getValue($updateJadwalDokterJkn, 'result.metadata.code') == 200 || ArrayHelper::getValue($updateJadwalDokterJkn, 'result.metaData.code') == 200) {
                            return [
                                'message' => 'Data Berhasil di ubah',
                            ];
                        } else {
                          return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                              'title' => 'Proses BPJS Gagal',
                              'text' => ArrayHelper::getValue($updateJadwalDokterJkn, 'result.metadata.message') ? ArrayHelper::getValue($updateJadwalDokterJkn, 'result.metadata.message') : ArrayHelper::getValue($updateJadwalDokterJkn, 'result.metaData.message'),
                              'data' => ['is_bpjs_error' => true]
                          ], 422);
                        }
                    } else {
                      $message = $stok_kuota_dokter_updated 
                              ? 'Data Berhasil di ubah dan stok kuota dokter sudah diperbaharui'
                              : 'Data Berhasil di ubah';
                  
                      return [
                          'message' => $message,
                      ];
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JadwalDokterForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id)
    {
        return $this->getData(['jadwaldokter_id'=>$id])->asArray()->one();
    }

    public function actionDelete($id)
    {
        try {
            $delete = (new JadwalDokter)->delete($id);
            if($delete){
                return "done";
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getListInstalasi(Array $instalasi_id = [])
    {
        try {
            $model = new Instalasi;
            $model = $model->find()
                ->andWhere(['is_pelayanan'=>true]);
            if($instalasi_id){
                $model->andWhere(['instalasi_id'=>$instalasi_id]);
            }
            $count = $model->count();
            $data = $model
                ->orderBy('instalasi_id')
                ->asArray()
                ->all();

            $results = [
                'data'=>$data,
                'count'=>$count,
            ];
            return $results;
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

    private function getHariLookup()
    {
        $data = Lookup::find();
        $data->andWhere(['lookup_type' => 'hari']);
        $data->andWhere(['is_active' => TRUE]);
        $data->select(['hari_id'=>'lookup_id','hari_nama'=>'lookup_name']);
        $data->orderBy('lookup_id');
        return [
            'data'=>$data->asArray()->all()
        ];
    }

    private function getListWaktuPelayanan()
    {
        $type = 'waktu_pelayanan';
        $data = $this->getLookupByType($type);
        return $data->asArray()->all();
    }

    public function actionGetBundleData()
    {
        $request = Yii::$app->request;
        $param_instalasi = [];
        try{
            $param_instalasi = $request->get('param_instalasi');
            $listData = [
                'list_instalasi' => $this->getListInstalasi($param_instalasi),
                'list_hari' => $this->getHariLookup(),
                'list_pelayanan' => $this->getListWaktuPelayanan()
            ];
            return $listData;
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

    protected $_title = 'Jadwal Dokter';
    public function actionExportExcel($instalasi_id=null, $ruangan_id=null, $pegawai_id=null,$hari=null,$jam_mulai=null,$jam_selesai=null)
    {
        $wheres = '';
        if (isset($ruangan_id) && $ruangan_id != 'null' && $ruangan_id != '') {
            $wheres .= " AND jadwaldokter_m.ruangan_id = {$ruangan_id}";
            $ruangan = Ruangan::findOne($ruangan_id);
        }
        if (isset($pegawai_id) && $pegawai_id != 'null' && $pegawai_id != '') {
            $wheres .= " AND jadwaldokter_m.pegawai_id = {$pegawai_id}";
            $pegawai = Pegawai::findOne($pegawai_id);
        }

        if (isset($hari) && $hari != 'null' && $hari != '') {
            $wheres .= " AND jadwalbukapoli_m.hari = {$hari}";
        }

        if (isset($jam_mulai) && $jam_mulai != 'null' && $jam_mulai != '') {
            $wheres .= " AND jadwaldokter_m.jadwaldokter_mulai >= '{$jam_mulai}'";
        }

        if (isset($jam_selesai) && $jam_selesai != 'null' && $jam_selesai != '') {
            $wheres .= " AND jadwaldokter_m.jadwaldokter_tutup <= '{$jam_selesai}'";
        }

        $sql = '
            SELECT kuota_antrian FROM konfigsystem_k
        ';

        $query = Yii::$app->db->createCommand($sql)->queryOne();
        $cek_kuota_antrian = $query['kuota_antrian'];

        if ($cek_kuota_antrian == 598){
            $sql = "
                SELECT
                ruangan_m.ruangan_nama AS Poliklinik,
                pegawai_m.nama_pegawai AS Dokter,
                lookup_hari.lookup_name AS Hari,
                concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS Waktu,
                jadwaldokter_m.maximumantrian AS Kuota,
                jadwaldokter_m.kuota_online AS Kuota_Online,
                jadwaldokter_m.jadwaldokter_mulai,
                jadwaldokter_m.jadwaldokter_tutup,
                jadwaldokter_m.jadwaldokter_id
               FROM jadwaldokter_m
                 JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
                 JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
                 JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
                 JOIN lookup_m lookup_hari ON lookup_hari.lookup_id = jadwalbukapoli_m.hari
              WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true
                {$wheres}
            ";
        }
        else{
            $sql = "
                SELECT
                ruangan_m.ruangan_nama AS Poliklinik,
                pegawai_m.nama_pegawai AS Dokter,
                lookup_hari.lookup_name AS Hari,
                concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS Waktu,
                jadwaldokter_m.jadwaldokter_mulai,
                jadwaldokter_m.jadwaldokter_tutup,
                jadwaldokter_m.jadwaldokter_id
               FROM jadwaldokter_m
                 JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
                 JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
                 JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
                 JOIN lookup_m lookup_hari ON lookup_hari.lookup_id = jadwalbukapoli_m.hari
              WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true
                {$wheres}
            ";
        }


        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            // 'totalCount' => $count,
            'pagination' => false,
        ]);

        $result = $dataProvider->getModels();
        $header = array(
            Yii::t('app', "Instalasi") => isset($instalasi) ? $instalasi->instalasi_nama : '',
            Yii::t('app', "Poliklinik") => isset($ruangan) ? $ruangan->ruangan_nama : '',
            Yii::t('app', "Dokter") => isset($pegawai) ? $pegawai->nama_pegawai : '',
        );

        $footer = array();

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    public function actionGetHari($id = null)
    {
        try {
            $model = Lookup::find()->select([
                'lookup_id',
                'lookup_name'
            ])
            ->andWhere(['lookup_type' => 'hari']);

            if ($id) {
                $model->andWhere(['lookup_id' => $id]);
            }

            $hari = $model->asArray()->all();

            if (empty($hari)) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data hari tidak ditemukan.'),
                ];
            }

            foreach ($hari as $key => $value) {
                $hari[$key]['hari_id'] = $value['lookup_id'];
                $hari[$key]['hari_nama'] = $value['lookup_name'];
                unset($hari[$key]['lookup_id']);
                unset($hari[$key]['lookup_name']);
            }
            return $hari;
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

    /**
    * @author Randy Vianda Putra
    * @todo add notif keterlambatan dokter
    */
    public function actionTestNotif()
    {
        $heading = [
            'en' => 'Perhatian dokter terlambat!'
        ];
        $content = [
            'en' => 'sorry my pasien dokternya lagi ke dokter dulu ya'
        ];
        $response = DocoNotification::createNotification([], $heading, $content);
        $return["allresponses"] = $response;
        $return = json_encode($return);

        return $response;
    }

    /**
    * @author Randy Vianda Putra
    * @todo save template notification
    */
    public function actionSaveTemplate()
    {
        $model = new Notifikasi;
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model->attributes = $post;
            if ($model->validate() && $model->save()) {
                return ['message' => 'Data Berhasil di simpan'];
            } else {
                return [
                   'data' => $model->errors,
                   'status' => 422
               ];
           }
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

    public function actionGetTemplateNotif()
    {
        $data = Notifikasi::find()->all();

        return $data;
    }

    /**
    * @author Randy Vianda Putra
    * @todo delete template notification
    */
    public function actionDeleteTemplate()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $model = Notifikasi::findOne($id);
            if ($model->delete()) {
                $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil dihapus'
                ];
                return $response;
            } else {
                $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Data Gagal di hapus',
                    'status' => 422
                ];
                return $response;
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @author Randy Vianda Putra
    * @todo push notification to mobile apps
    */
    public function actionPushNotification()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $text = $post['pegawai_nama'] . ' dengan jadwal ' . $post['jam_mulai'] . ' - ' . $post['jam_tutup'] . ' Poliklinik ';
        $text .= $post['ruangan_nama'] . ' ' . $post['notifikasi'];
        try {
            $heading = [
                'en' => $post['judul_temp']
            ];
            $content = [
                'en' => $text
            ];
            $players = $this->getPlayerIdByJadwal($post['jadwal_id']);
            $modelJadwal = JadwalDokter::findOne($post['jadwal_id']);
            $modelJadwal->notifikasi_id = $post['notifikasi_id'];
            $modelJadwal->scenario = 'update';
            $modelJadwal->save();
            $hari = date('N');
            $hari_id = DocoConstants::$look_hari[$hari];
            $data = NotifikasiComponent::queryListNotifByDay($hari_id);
            $data_notif['list_notification'] = $data;
            $mode = Yii::$app->params['mode'];
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-notif-'.$mode,
                'message' => json_encode(['data' => $data_notif])
            ]);
            if (!empty($players)) {
                $list_players_id = [];
                foreach ($players as $key => $value) {
                    $data_players = json_decode($value['player_id']);
                    for ($i=0; $i < count($data_players); $i++) {
                        $list_players_id[] = $data_players[$i];
                    }
                }
                $push = DocoNotification::createNotification([], $heading, $content, $list_players_id);
                $response = json_decode($push, true);

                return $response;

            } else {
                return ['message' => 'Tidak ada data player_id'];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @author Randy Vianda Putra
    * @todo get player id by jadwaldokter_id
    * @param integer jadwaldokter_id
    */
    private function getPlayerIdByJadwal($id)
    {
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';
        $connection = Yii::$app->db;
        $sql = "SELECT
                lm.loginmobile_id,
                lm.player_id,
                po.jadwaldokter_id
            FROM
                loginmobile_k lm
                JOIN (
                SELECT
                    pendaftaranol_t.created_by as user_id,
                    pendaftaranol_t.jadwaldokter_id
                FROM
                    pendaftaranol_t
                    LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id
                WHERE
                    (
                        tgl_pendaftaranol BETWEEN '{$startDate}'
                        AND '{$endDate}'
                        OR tgl_pendaftaran BETWEEN '{$startDate}'
                        AND '{$endDate}'
                    )
                ) po ON po.user_id = lm.loginmobile_id
            WHERE
                lm.player_id IS NOT NULL
            AND
                po.jadwaldokter_id = {$id}
        ";
        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }

    /**
    * @author Sigit Arif Munandar <sigit@docotel.com>
    * @todo Mendapatkan data konfig
    */
    public function actionGetKonfigSistem()
    {
        $model = KonfigSystemK::find()->limit(1)->one();

        return $model;
    }

    /**
    * @author Sigit Arif Munandar <sigit@docotel.com>
    * @todo Mendapatkan data konfig
    */
    public function actionGetKonfigPoli()
    {
        $model = AntrianPoliLogic::isKonfig();

        return [
            'status' => 200,
            'konfig_poli' => $model
        ];
    }

    /**
    * @todo Mendapatkan data jadwal dokter
    * @author Sigit Arif Munandar <sigit@docotel.com>
    */
    public function actionGetJadwalDokterById()
    {
        $params = Yii::$app->request;
        $id = $params->get('id', null);
        $data = null;

        if ($id) {
            $data = InfoJadwalDokterView::find()->where(['jadwaldokter_id' => $id])->one();
        }

        return $data;
    }

    public function actionGetJadwalDokterByDate($start_date = null, $end_date = null, $room_id = null)
    {
        $model = new DynamicModel(compact('start_date', 'end_date', 'room_id'));
        $model->addRule(['start_date', 'end_date'], 'datetime', ['format' => 'php:Y-m-d'])
            ->addRule(['start_date', 'end_date'], 'required')
            ->addRule(['room_id'], 'safe');

        if (!$model->validate())
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $model->errors]);

        $mappDate = DocoHelpers::mappDateWithLookup($start_date, $end_date);
        if(!empty($mappDate)) {
            $listHari = $mappDate['listHari'];
            $mappDaysToDate = $mappDate['mappDate'];
        }
        $jadwal = (new JadwalDokter)->getJadwal();
        $jadwal .= " AND jadwaldokter_m.is_loaddokter = true";
        $jadwal .= " AND jadwaldokter_m.is_active = true";

        if (isset($room_id) && $room_id != null && $room_id != '' && $room_id != "0") {
            $jadwal .= " AND ruangan_m.ruangan_id = {$room_id}";
        }
        
        $dataJadwal = Yii::$app->db->createCommand($jadwal)->queryAll();
        $ruanganTelekonsultasi = GeneralCache::lookTeleRoom()->kode_id;

        if(!empty($dataJadwal)) {
            $listJadwal = [];
            foreach($dataJadwal as $k => $v) {
                $hariId = $v['hari_jadwalbuka'];
                $listJadwal[$hariId][] = [
                    'jadwaldokter_id' => $v['jadwaldokter_id'],
                    'kode_dokter' => $v['kode_dokter'],
                    'nama_dokter' => $v['nama_pegawai'],
                    'schedule_type' => ($v['type'] == false) ? self::SCHEDULE : self::APPOINTMENT,
                    'section' => strtoupper($this->getShift($v['waktu_mulai'])),
                    'schedule_start' => $v['waktu_mulai'],
                    'schedule_end' => $v['waktu_selesai'],
                    'slot_duration' => $v['jumlah_loaddokter'],
                    'room_name' => $v['ruangan_nama'],
                    'is_teleconsultation' => (!empty($ruanganTelekonsultasi) && $v['ruangan_id'] == $ruanganTelekonsultasi) ? 1 : 0,
                    'doctor_id' => isset($v['pegawai_id']) ? $v['pegawai_id'] : null,
                    'kuota_nonbpjs_online' => $v['kuota_nonbpjs_online'],
                ];
            }

            $jadwalDokter = [];
            foreach ($mappDaysToDate as $date => $days) {
                if (isset($listJadwal[$days])) {
                    foreach ($listJadwal[$days] as $rowData) {
                        $dataJadwal = $rowData;
                        $dataJadwal['day_id'] = $this->mappDays((int) $days);
                        $jadwalDokter[] = $dataJadwal;
                    }
                }
            }

            return [
                'data' => $jadwalDokter
            ];
        }

        return [
            'status' => 422,
            'messages' => "Jadwal tidak tersedia"
        ];
    }

    private function mappDays($dayId)
    {
        $mappedDay = null;
        switch ($dayId) {
            case 75:
                $mappedDay = 2;
                break;
            case 76:
                $mappedDay = 3;
                break;
            case 77:
                $mappedDay = 4;
                break;
            case 78:
                $mappedDay = 5;
                break;
            case 79:
                $mappedDay = 6;
                break;
            case 80:
                $mappedDay = 7;
                break;
            case 81:
                $mappedDay = 1;
                break;
            default:
                $mappedDay = null;
                break;
        }

        return $mappedDay;
    }

    private function getShift($dateTime = null)
    {
        $time = strtotime(date($dateTime ? : "H:i:s"));
        $listShift = Cache::getShift();
        $shift_name = "";
        foreach ($listShift as $value) {
            $timeStart = strtotime($value['shift_jamawal']);
            $timeEnd = strtotime($value['shift_jamakhir']);
            if ($time >= $timeStart && $time <= $timeEnd) {
                $shift_name = $value['shift_nama'];
                break;
            }
        }
        return $shift_name;
    }
    /**
     * Untuk update jadwal dokter SMH setiap jam 00.00
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionUpdateJadwalDokter()
    {
        return [
            'message' => 'Proses Update Jadwal Berhasil',
        ];
    }

    /**
     * @method create slotjadwal dokter after db inject(required jumlah_loaddokter set to null)
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function actionGenerateSlotJadwalDokter($integrasi = false)
    {
        return [
            'message' => 'Generate Slot Jadwal Dokter Berhasil',
        ];
    }

    public function actionGetDataJadwalCuti()
    {
        try {
            $request = Yii::$app->request;
            $model = new JadwalCutiView;
            $query = $model->find();

            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');

            $ruangan_id = null;
            $dokter_id = null;

            unset($_GET['order']);
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_cuti'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_cuti']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_cuti']);

                }

                if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                    $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
                    unset($_GET['advanced-filter']['ruangan_nama']);
                }

                if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                    $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
                    unset($_GET['advanced-filter']['ruangan_nama']);
                }
            }

            $query->andWhere(['between', 'tgl_cuti_awal', $start, $end]);
            $query->orWhere(['between', 'tgl_cuti_akhir', $start, $end]);

            if ($ruangan_id != null){
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query
            ]);
        }
        catch (\yii\db\Exception $e) {
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

    public function actionGetPackCuti()
    {
        $dokterList = DataDokterView::find()->select([
            'pegawai_id',
            'nama_pegawai',
            'spesialis_id',
            'spesialis_nama'
        ])->orderBy('nama_pegawai')
        ->asArray()->all();

        return [
            'dokterList' => $dokterList,
        ];
    }

    public function actionCreateCuti()
    {
        $request = Yii::$app->request;
        $model = new JadwalCuti;
        $payload = [];
        try {
            $post = $request->post();
            $pegawaiId = ArrayHelper::getValue($post, 'pegawai_id');
            $ruanganId = ArrayHelper::getValue($post, 'ruangan_id');
            //$spesialisId = ArrayHelper::getValue($post, 'spesialis_id');
            $spesialisId = (!empty($request->post('spesialis_id'))) ? $request->post('spesialis_id') : null;
            $tglCutiAwal = date('Y-m-d 00:00:00', strtotime($post['tgl_cuti_awal']));
            $tglCutiAkhir = date('Y-m-d 23:59:59', strtotime($post['tgl_cuti_akhir']));
            $alasanCuti = ArrayHelper::getValue($post, 'alasan_cuti');
            // Checking if leave date already exist in db
            $param = [
                'pegawai_id' => $pegawaiId,
                'ruangan_id' => $ruanganId,
                'tgl_cuti_awal' => $tglCutiAwal,
                'tgl_cuti_akhir' => $tglCutiAkhir,
            ];
            $validateCuti = $model->validateTanggalCuti($param);
            if (!$validateCuti) {
                return $this->helper->response([
                    'title' => 'Proses Gagal!',
                    'text' => 'Tanggal tersebut telah dibuatkan cuti.',
                ], 422);
            }

            $listTgl = $this->getListTanggal($tglCutiAwal, $tglCutiAkhir);
            foreach ($listTgl as $value) {
                $payload[] = [
                    'pegawai_id' => $pegawaiId,
                    'spesialis_id' => $spesialisId,
                    'ruangan_id' => $ruanganId,
                    'tgl_cuti_awal' => $value . " 00:00:00",
                    'tgl_cuti_akhir' => $value . " 23:59:59",
                    'alasan_cuti' => $alasanCuti,
                ];
            }

            if ($model::batchInsert($payload)) {
                return $this->helper->response([
                    'text' => 'Data berhasil disimpan',
                    'data' => $param,
                ]);
            }

            Yii::error($model->errors);
            return $this->helper->response($model->errors,422,'JadwalCutiForm');
        } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());

            return $this->helper->response(['text' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());

            return $this->helper->response(['text' => $e->getMessage()], 500);
        }
    }

    public function actionDeleteCuti()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $jadwalcutiId = ArrayHelper::getValue($post, 'jadwalcuti_id');
        $password = ArrayHelper::getValue($post, 'password');
        $result = [];

        try {
            if (!$jadwalcutiId) {
                return $this->helper->response([
                    'title' => 'Proses Gagal!',
                    'text' => 'Jadwal Cuti ID tidak boleh kosong.',
                ], 422);
            }

            $valid = $this->helper->validatePassword($password);
            if (!$valid) {
                return $this->helper->response([
                    'title' => 'Proses Gagal!',
                    'text' => 'Password salah.',
                ], 422);
            }

            $model = JadwalCuti::find()
                ->where(['jadwalcuti_id' => $jadwalcutiId])
                ->one();

            if ($model->delete()) {
                return $this->helper->response([
                    'jadwalcuti_id' => $jadwalcutiId,
                ]);
            }

            Yii::error($model->errors);
            return $this->helper->response($model->errors,422,'HapusJadwalCutiForm');
        } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());
            return $this->helper->response(['text' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return $this->helper->response(['text' => $e->getMessage()], 500);
        }
    }

    private function getListTanggal($tglAwal, $tglAkhir) {
        $result = [];
        $tgl_awal = new DateTime($tglAwal);
        $tgl_akhir = new DateTime($tglAkhir);
        $interval = new DateInterval('P1D');
        $rangeTanggal = new DatePeriod($tgl_awal, $interval ,$tgl_akhir);
        foreach($rangeTanggal as $value){
            $result[] = $value->format("Y-m-d");
        }

        return $result;
    }

    /**
     * @method disable doctor schedule by doctor leave date from today until next week
     * @return array
     * @author : Fajar (fajar.supriadi@sirs.co.id)
     */
    public function actionDisableJadwalDokterCuti()
    {
        return [
            'message' => 'Disable Jadwal Dokter Berhasil',
        ];
    }

    /**
     * @method enable doctor schedule by doctor leave date for yesterday
     * @return array
     * @author : Fajar (fajar.supriadi@sirs.co.id)
     */
    public function actionEnableJadwalDokterCuti()
    {
        return [
            'message' => 'Enable Jadwal Dokter Berhasil',
        ];
    }

    private function setLogs($primaryId, $result, $state, $payload)
    {
        $responseCode = ArrayHelper::getValue($result, 'metadata', null);
        if (!empty($responseCode)) {
            $isSent = ArrayHelper::getValue($result,'metadata.code') == 200 ? true : false;
            $syncResponse = json_encode($result);
        } else {
            $isSent = false;
            $syncResponse = json_encode($result);
        }

        $logData[] = [
            'jadwaldokter_id' => $primaryId,
            'is_sending' => true,
            'is_sent' => $isSent,
            'id_sync_serconn' =>null,
            'sync_respon' => $syncResponse,
            'state' => $state,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset(Yii::$app->jwt->user->loginpemakai_id) ? Yii::$app->jwt->user->loginpemakai_id : null ,
            'payload' => json_encode($payload),
        ];

        $this->saveLogs($logData);
    }

    private function saveLogs($data)
    {
        return JadwalDokterInt::batchInsert($data);
    }

    private function insertStokKuotaDokter($request, $model)
    {
        $stok_kuota_dokter_updated = false;
        $stok_kuota_dokter = [];
        
        $kuota_bpjs_online_lama = $model->kuota_bpjs_online + $model->kuota_nonbpjs_online != $model->kuota_online
                                ? floor($model->kuota_online / 2) 
                                : $model->kuota_bpjs_online;
        $kuota_nonbpjs_online_lama = $model->kuota_bpjs_online + $model->kuota_nonbpjs_online != $model->kuota_online 
                                ? $model->kuota_online - $kuota_bpjs_online_lama 
                                : $model->kuota_nonbpjs_online;
        $kuota_bpjs_offline_lama = $model->kuota_bpjs_offline + $model->kuota_nonbpjs_offline != $model->maximumantrian 
                                ? floor($model->maximumantrian / 2) 
                                : $model->kuota_bpjs_offline;
        $kuota_nonbpjs_offline_lama = $model->kuota_bpjs_offline + $model->kuota_nonbpjs_offline != $model->maximumantrian 
                                ? $model->maximumantrian - $kuota_bpjs_offline_lama 
                                : $model->kuota_nonbpjs_offline;
                                
        $kuota_bpjs_online = $request->post('kuota_bpjs_online', 0);
        $kuota_nonbpjs_online = $request->post('kuota_nonbpjs_online', 0);
        $kuota_bpjs_offline = $request->post('kuota_bpjs_offline', 0);
        $kuota_nonbpjs_offline = $request->post('kuota_nonbpjs_offline', 0);
                                
        // cek kuotadokter_r exist (tidak ada kemungkinan dari cron gagal terbentuk)
        $query = "SELECT kuotadokter_id, is_online FROM kuotadokter_r
                  WHERE jadwaldokter_id = {$model->jadwaldokter_id}";
        $kuotadokter = Yii::$app->db->createCommand($query)->queryAll();
        $kuotadokter_is_online = ArrayHelper::getColumn($kuotadokter, 'is_online');
        $kuotadokter_exist = in_array(true, $kuotadokter_is_online) && in_array(false, $kuotadokter_is_online);
        
        if ($kuotadokter_exist) { // jadwal exist online and offline
            $kuota_bpjs_online -= $kuota_bpjs_online_lama;
            $kuota_nonbpjs_online -= $kuota_nonbpjs_online_lama;
            $kuota_bpjs_offline -= $kuota_bpjs_offline_lama;
            $kuota_nonbpjs_offline -= $kuota_nonbpjs_offline_lama;
            
            $total_kuota_online = $kuota_bpjs_online + $kuota_nonbpjs_online;
            $total_kuota_offline = $kuota_bpjs_offline + $kuota_nonbpjs_offline; 
            
            $payload_stokkuotadokter_offline = [
                'jadwaldokter_id' => $model->jadwaldokter_id,
                'is_online' => false,
                'kuota_in' => 0,
                'kuota_out' => $total_kuota_offline <= 0 ? abs($total_kuota_offline) : (-$total_kuota_offline),
                'tgltransaksi_out' => date('Y-m-d H:i:s'),
                'flag' => true,
                'kuota_bpjs_offline' => $kuota_bpjs_offline > 0 ? $kuota_bpjs_offline : 0,
                'kuota_nonbpjs_offline' => $kuota_nonbpjs_offline > 0 ? $kuota_nonbpjs_offline : 0,
                'kuota_out_bpjs' => $kuota_bpjs_offline < 0 ? abs($kuota_bpjs_offline) : 0,
                'kuota_out_nonbpjs' => $kuota_nonbpjs_offline < 0 ? abs($kuota_nonbpjs_offline) : 0,
                'additional_data' => 'Perubahan Master Jadwal Dokter'
            ];
            
            $payload_stokkuotadokter_online = [
                'jadwaldokter_id' => $model->jadwaldokter_id,
                'is_online' => true,
                'kuota_in' => 0,
                'kuota_out' => $total_kuota_online < 0 ? abs($total_kuota_online) : (-$total_kuota_online),
                'tgltransaksi_out' => date('Y-m-d H:i:s'),
                'flag' => true,
                'kuota_bpjs_online' => $kuota_bpjs_online > 0 ? $kuota_bpjs_online : 0,
                'kuota_nonbpjs_online' => $kuota_nonbpjs_online > 0 ? $kuota_nonbpjs_online : 0,
                'kuota_out_bpjs' => $kuota_bpjs_online < 0 ? abs($kuota_bpjs_online) : 0,
                'kuota_out_nonbpjs' => $kuota_nonbpjs_online < 0 ? abs($kuota_nonbpjs_online) : 0,
                'additional_data' => 'Perubahan Master Jadwal Dokter'
            ];
        } else {
            $total_kuota_online = $kuota_bpjs_online + $kuota_nonbpjs_online;
            $total_kuota_offline = $kuota_bpjs_offline + $kuota_nonbpjs_offline; 
            
            $payload_stokkuotadokter_offline = [
                'jadwaldokter_id' => $model->jadwaldokter_id,
                'is_online' => false,
                'kuota_in' => $total_kuota_offline,
                'kuota_out' => 0,
                'tgltransaksi_in' => date('Y-m-d H:i:s'),
                'flag' => true,
                'kuota_bpjs_online' => 0,
                'kuota_nonbpjs_online' => 0,
                'kuota_bpjs_offline' => $kuota_bpjs_offline > 0 ? $kuota_bpjs_offline : 0,
                'kuota_nonbpjs_offline' => $kuota_nonbpjs_offline > 0 ? $kuota_nonbpjs_offline : 0,
                'kuota_out_bpjs' => $kuota_bpjs_offline < 0 ? abs($kuota_bpjs_offline) : 0,
                'kuota_out_nonbpjs' => $kuota_nonbpjs_offline < 0 ? abs($kuota_nonbpjs_offline) : 0,
                'additional_data' => 'Perubahan Master Jadwal Dokter'
            ];
            
            $payload_stokkuotadokter_online = [
                'jadwaldokter_id' => $model->jadwaldokter_id,
                'is_online' => true,
                'kuota_in' => $total_kuota_online,
                'kuota_out' => 0,
                'tgltransaksi_in' => date('Y-m-d H:i:s'),
                'flag' => true,
                'kuota_bpjs_online' => $kuota_bpjs_online > 0 ? $kuota_bpjs_online : 0,
                'kuota_nonbpjs_online' => $kuota_nonbpjs_online > 0 ? $kuota_nonbpjs_online : 0,
                'kuota_bpjs_offline' => 0,
                'kuota_nonbpjs_offline' => 0,
                'kuota_out_bpjs' => $kuota_bpjs_online < 0 ? abs($kuota_bpjs_online) : 0,
                'kuota_out_nonbpjs' => $kuota_nonbpjs_online < 0 ? abs($kuota_nonbpjs_online) : 0,
                'additional_data' => 'Perubahan Master Jadwal Dokter'
            ];
            
            
            // get kuota antrian online yang daftar pada hari ini
            $query = "SELECT 
                        jadwaldokter_id, 
                        count(antrian_id) as total_antrian_online, 
                        count(carabayar_id) filter (where groupcarabayar_id <> '".DocoConstants::GROUP_BPJS."') as non_bpjs_count, 
                        count(carabayar_id) filter (where groupcarabayar_id = '".DocoConstants::GROUP_BPJS."') as bpjs_count
                      FROM antrian_t
                      WHERE jadwaldokter_id = ".$model->jadwaldokter_id." AND tgl_antrian::date = '".date('Y-m-d')."'
                        AND jenisantrian_id = '".DocoConstants::VAR_JA_P."'  AND is_online = TRUE
                        AND is_deleted = FALSE
                      GROUP BY jadwaldokter_id";
            $antrian_online = Yii::$app->db->createCommand($query)->queryOne();
            
            if (!empty($antrian)) {
                $total_kuota_online -= ArrayHelper::getValue($antrian, 'total_antrian_online');
                $kuota_bpjs_online -= ArrayHelper::getValue($antrian, 'bpjs_count');
                $kuota_nonbpjs_online -= ArrayHelper::getValue($antrian, 'non_bpjs_count');
                
                $payload_stokkuotadokter_online_from_antrian = [
                    'jadwaldokter_id' => $model->jadwaldokter_id,
                    'is_online' => true,
                    'kuota_in' => 0,
                    'kuota_out' => $total_kuota_online < 0 ? abs($total_kuota_online) : (-$total_kuota_online),
                    'tgltransaksi_out' => date('Y-m-d H:i:s'),
                    'flag' => true,
                    'kuota_bpjs_online' => $kuota_bpjs_online > 0 ? $kuota_bpjs_online : 0,
                    'kuota_nonbpjs_online' => $kuota_nonbpjs_online > 0 ? $kuota_nonbpjs_online : 0,
                    'kuota_out_bpjs' => $kuota_bpjs_online < 0 ? abs($kuota_bpjs_online) : 0,
                    'kuota_out_nonbpjs' => $kuota_nonbpjs_online < 0 ? abs($kuota_nonbpjs_online) : 0,
                    'additional_data' => 'Perubahan Master Jadwal Dokter (data dari antrian)'
                ];
            }
        }
        
        
        if (!$kuotadokter_exist || $payload_stokkuotadokter_online['kuota_out'] != 0) {
            $stok_kuota_dokter[] = $payload_stokkuotadokter_online;
        }
        
        if (!$kuotadokter_exist || $payload_stokkuotadokter_offline['kuota_out'] != 0) {
            $stok_kuota_dokter[] = $payload_stokkuotadokter_offline;
        } 
        
        if (!$kuotadokter_exist && isset($payload_stokkuotadokter_online_from_antrian)) {
            $stok_kuota_dokter[] = $payload_stokkuotadokter_online_from_antrian;
        }
        
        if (!empty($stok_kuota_dokter)) {
            StokKuotaDokter::batchInsert($stok_kuota_dokter, false);
            $stok_kuota_dokter_updated = true;
        }
        
        return $stok_kuota_dokter_updated;
    }
}
