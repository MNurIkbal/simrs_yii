<?php

/**
 * @author iqbal@docotel.com
 * @since 2018-08-28 10:11:20
 * @desc
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\Repositories\LookUpTransaksiRepositories;
use Doco\models\InfoRiwayatPasienView;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\JenisAntrianDetail;
use app\modules\v1\models\JenisAntrianRuanganMp;
use app\modules\v1\models\SatusehatRuangan;
use yii\validators\Validator;

class RuanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Ruangan';

    public $messageBroker = [
        'create-ruangan' => [
            'services' => [
                'Odoo' => [
                    'Ruangan' => [
                        'last_insert' => true
                    ]
                ],
                'Satusehat' => [
                    'SyncRuanganSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
        'update-ruangan' => [
            'services' => [
                'Odoo' => [
                    'Ruangan' => [
                        'query_params' => ['id'],
                    ]
                ],
                'Satusehat' => [
                    'SyncRuanganSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ],
                    'SyncUpdateRuanganSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'update'
                    ]
                ],

            ]
        ],
        'delete-ruangan' => [
            'services' => [
                'Odoo' => [
                    'Ruangan' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-ruangan-ranap"] = ["GET"];
        $verbs["get-ruangan-ranap-dep"] = ["GET"];
        $verbs["get-ruangan-by-instalasi-dep"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new RuanganView;
            $query = $model::find();

            $ruangan_nama = $request->get('ruangan_nama', null);
            $is_online = $request->get('is_online', null);
            if (!empty($ruangan_nama)) {
                $query->where(['ILIKE', 'LOWER(ruangan_nama)', strtolower($ruangan_nama)]);
            }

            if (!empty($is_online)) {
                $query->andWhere(['is_online' => $is_online]);
            }

            $query->orderby(['instalasi_id' => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $dataProvider['query'] = $query;

            $isPagination = $request->get('isPagination', true);
            if ($isPagination == false) {
                $dataProvider['pagination'] = false;
            }

            return new ActiveDataProvider($dataProvider);
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

    public function actionGetDisplayAntrian()
    {
        $connection = Yii::$app->db;
        $sql = "SELECT distinct jm.ruangan_id,rm.ruangan_nama  from jadwaldokter_m jm 
        inner join ruangan_m rm on rm.ruangan_id = jm.ruangan_id 
        where jm.is_deleted is false and jm.is_active is true 
        order by jm.ruangan_id";
        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionCreateRuangan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new Ruangan;
            $post = $request->post();
            $model->attributes = $post;
            $model->is_modul = true;
            if ($model->validate()) {
                if ($post) {
                    if ($model->save()) {

                        /* Proses Update SatuSehat Location */
                        $ruangan_id = NULL;
                        if (empty($satusehat_ruangan)) {
                            // Kondisi generate SatuSehat Location background process
                            $ruangan_id = $model->ruangan_id;
                        } else {
                            // Kondisi update SatuSehat Location
                            $locationSatusehat = SatusehatRuangan::find()
                                ->where('ruangan_id = ' . $model->ruangan_id . ' 
                                                            and satusehat_ruangan_id IS NOT NULL
                                                            and is_active = true 
                                                            and is_deleted = false')->one();

                            if (!empty($locationSatusehat)) {
                                $locationSatusehat->satusehat_ruangan_id = $satusehat_ruangan;
                                $locationSatusehat->save();
                            } else {
                                $locationSatusehat = new SatusehatRuangan;
                                $locationSatusehat->ruangan_id = $model->ruangan_id;
                                $locationSatusehat->satusehat_ruangan_id = $satusehat_ruangan;
                                $locationSatusehat->save();
                            }
                        }

                        if(!empty($post['lantairuangan_id'])){
                            $antrian = new JenisAntrianRuanganMp;
                            $antrian->jenisantriandetail_id = $post['lantairuangan_id'];
                            $antrian->ruangan_id = $model->ruangan_id;
                            $antrian->save();
                        }
                        Yii::$app->cache->delete(DocoConstants::CACHE_RUANGAN . '-' . $model->attributes['instalasi_id']);
                        $transaction->commit();
                        // $responseMessage =  ['message' => 'Data Berhasil di simpan'];
                        $responseMessage =  [
                            'message' => 'Data Berhasil di simpan',
                            'id' => !empty($ruangan_id) ? DocoHelpers::encrypt($ruangan_id) : NULL
                        ];

                        if (!$this->sinkronRuangan($model->attributes)) {
                            $responseMessage['errorMessage'] = 'Gagal Menyimpan Data Sinkronisasi';
                            // if (!$this->saveTempSinkron($post)) {
                            // }
                        }
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, 'RuanganForm');
                        $responseMessage =  ['data' => $errors, 'status' => 422];
                    }
                    return $responseMessage;
                }
            } else {
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateRuangan()
    {
        try {
            $request = Yii::$app->request;
            $model = Ruangan::findOne($request->get('id'));
            if ($request->post() && !empty($model)) {
                // $model->attributes = $request->post();
                $post = $request->post();

                $satusehat_ruangan = $post['satusehat_ruangan'];
                unset($post['satusehat_ruangan']);
                
                $model->attributes = $post;

                if ($model->validate() && $model->save()) {

                    /* Proses Update SatuSehat Location */
                    $locationSatusehat = SatusehatRuangan::find()
                        ->where('ruangan_id = ' . $model->ruangan_id . ' 
                                                    and satusehat_ruangan_id IS NOT NULL
                                                    and is_active = true 
                                                    and is_deleted = false');

                    $ruangan_id = NULL;
                    if (empty($satusehat_ruangan)) {
                        // Kondisi generate SatuSehat Location Background process
                        $ruangan_id = $model->ruangan_id;

                        $updateLocationSatusehat = $locationSatusehat->one();

                        if (!empty($updateLocationSatusehat)) {
                            $updateLocationSatusehat->is_deleted = true;
                            $updateLocationSatusehat->is_active = false;
                            $updateLocationSatusehat->save();
                        }

                        $state_input = 'create';
                    } else {
                        // Kondisi update SatuSehat Location
                        $ruangan_id = $model->ruangan_id;
                        $locationSatusehat = $locationSatusehat->one();

                        if (!empty($locationSatusehat)) {
                            $locationSatusehat->satusehat_ruangan_id = $satusehat_ruangan;
                            $locationSatusehat->save();

                            $state_input = 'update';
                        } else {
                            $locationSatusehat = new SatusehatRuangan;
                            $locationSatusehat->ruangan_id = $model->ruangan_id;
                            $locationSatusehat->satusehat_ruangan_id = $satusehat_ruangan;
                            $locationSatusehat->save();

                            $state_input = 'update';
                        }
                    }

                    if (!empty($request->post('lantairuangan_id'))){
                        $antrian = JenisAntrianRuanganMp::find()->Where(['ruangan_id' => $model->ruangan_id])->one();
                        if (empty($antrian)) {
                            $antrian = new JenisAntrianRuanganMp;
                        }
                        $antrian->jenisantriandetail_id = $request->post('lantairuangan_id');
                        $antrian->ruangan_id = $model->ruangan_id;
                        $antrian->save();
                    } else {
                        $antrian = JenisAntrianRuanganMp::find()->Where(['ruangan_id' => $model->ruangan_id])->one();
                        if (!empty($antrian)) {
                            $x = Yii::$app->db->createCommand("
                                DELETE FROM jenisantrianruangan_mp 
                                WHERE ruangan_id = '$model->ruangan_id'
                            ")->execute();
                        }
                    }
                    Yii::$app->cache->delete(DocoConstants::CACHE_RUANGAN . '-' . $model->instalasi_id);
                    return [
                        'message' => 'Data Berhasil di simpan',
                        'id' => !empty($ruangan_id) ? DocoHelpers::encrypt($ruangan_id) : NULL,
                        'state_input' => $state_input
                    ];
                } else {
                    $response = $model->getErrors();
                    Yii::error($response);
                    return DocoHelpers::responseTemplate(422, $response);
                }
            } else {
                throw new \Exception('Data Tidak Di Temukan');
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        }
    }

    private function Model()
    {
        $model = new RuanganView;
        return $model::find();
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk menampilkan data table
     */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Ruangan Rumah Sakit';
            $get = $request->get();

            $model = new RuanganView;
            $query = $this->model();
            $advancedFilters = $request->get('advanced-filter', []);
            if (isset($advancedFilters)) {
                if (isset($advancedFilters['instalasi_nama'])) {
                    $query->andWhere(['ILIKE', 'LOWER(instalasi_nama)', strtolower($advancedFilters['instalasi_nama'])]);
                }
                if (isset($advancedFilters['ruangan_nama'])) {
                    $query->andWhere(['ILIKE', 'LOWER(ruangan_nama)', strtolower($advancedFilters['ruangan_nama'])]);
                }
            }
            $query->orderby(['instalasi_id' => SORT_ASC]);

            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $result[] = $value;
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Laporan Master Ruangan Rumah Sakit';
            $result = [];
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $get = $request->get();

            $model = new RuanganView;
            $query = $this->model();
            $advancedFilters = $request->get('advanced-filter', []);
            if (isset($advancedFilters)) {
                if (isset($advancedFilters['instalasi_nama'])) {
                    $query->andWhere(['ILIKE', 'LOWER(instalasi_nama)', strtolower($advancedFilters['instalasi_nama'])]);
                }
                if (isset($advancedFilters['ruangan_nama'])) {
                    $query->andWhere(['ILIKE', 'LOWER(ruangan_nama)', strtolower($advancedFilters['ruangan_nama'])]);
                }
            }
            $query->orderby(['instalasi_id' => SORT_ASC]);
            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no++;
                $data['instalasi_nama'] = $value['instalasi_nama'];
                $data['ruangan_nama'] = $value['ruangan_nama'];
                $data['ruangan_singkatan'] = $value['ruangan_singkatan'];
                $data['Satu_Sehat_Location_ID'] = $value['satusehat_ruangan_id'];
                $result[] = $data;
            }

            /* $header = ['Periode'=> date('d F Y H:i:s', strtotime($start)) . ' - '.date('d F Y H:i:s', strtotime($end))
                        ];*/
            $header = [];
            $filePath = DocoHelpers::exportExcel('Master Ruangan Rumah Sakit', $result, $header, array("uploadPath" => "./uploads",), null, null, true);

            $filePath->save('php://output');
            die;

            // return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));

        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @author Ayip
     * @since 2018-02-01 10:10:10
     * @param all attributes
     * @return list of ruangan
     * @desc
     */
    /*public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('instalasi_id')) {
                $result->andFilterWhere(['ILIKE', 'instalasi_id', $indexing]);
            }*/

    // $layarantrian_id = $request->post('layarantrian_id');
    // if($layarantrian_id) {
    //     $result->andWhere(['antrian_t.layarantrian_id' => $layarantrian_id]);
    // }

    // $loket_id = $request->post('loket_id');
    // if($loket_id) {
    //     $result->andWhere(['antrian_t.loket_id' => $loket_id]);
    // }

    // $panggil_flag = $request->post('panggil_flag');
    // if($panggil_flag !== null) {
    //     $panggil_flag = $panggil_flag === '0' ? false : true;
    //     $result->andWhere(['antrian_t.panggil_flag' => $panggil_flag]);
    // }


    // $status = $request->post('is_active');
    // if($status === null) {
    //     $status = $status ? true : false;
    //     $result->andWhere(['antrian_t.is_active' => $status]);
    // }

    /*if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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
    */

    /**
     * @author Rizal
     * @since 2018-01-24 14:01:16
     * @param int instalasi_id
     * @return array list of ruangan
     * @desc needs for depdrop or dropdown
     */
    public function actionListRuangan($instalasi_id = null, $singkatan = null)
    {
        $data = Ruangan::find()->joinWith('instalasi');
        $data->where(['ruangan_m.is_active' => 't']);
        if ($instalasi_id) {
            $data->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
        }
        if ($singkatan) {
            $data->andWhere(['instalasi_m.instalasi_singkatan' => $singkatan]);
        }
        $data->orderBy('ruangan_m.ruangan_nama');

        $items = ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama');
        return $items;
    }

    private function getData($filter = null)
    {
        $returnData = Ruangan::find()
            ->select([
                'ruangan_id',
                'ruangan_nama',
                'ruangan_fasilitas',
                // 'ruangan_lokasi'
            ]);
        if ($filter) {
            if (is_array($filter)) {
                foreach ($filter as $key => $value) {
                    $returnData->andWhere([$key => $value]);
                }
            }
        }

        return $returnData;
    }

    public function actionListByPenyakit($penyakit)
    {
        $data = KasusPenyakitRuangan::find()
            ->joinWith(['ruangan'])
            ->where(['kasuspenyakitruangan_mp.is_active' => 't', 'kasuspenyakitruangan_mp.is_deleted' => 'f', 'kasuspenyakitruangan_mp.jeniskasuspenyakit_id' => $penyakit])
            ->select(['ruangan_m.ruangan_id', 'ruangan_m.ruangan_nama']);

        $items = $data->asArray()->all();

        return $items;
    }

    public function actionGenerateApi()
    {

        try {
            $result = Ruangan::find()->andWhere(['is_deleted' => false, 'is_active' => true]);
            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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

        return $result;
    }

    public function actionDeleteRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = Ruangan::findOne($id);
            $modelPendaftaran = new Pendaftaran;
            $getDataPendaftaran = $modelPendaftaran::find()->where(['ruangan_id' => $id])->count();
            if ($getDataPendaftaran > 0) {
                return $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Ruangan ini sedang dipakai',
                    'status' => 422
                ];
            } else {
                if ($model->delete()) {
                    Yii::$app->cache->delete(DocoConstants::CACHE_RUANGAN . '-' . $model->instalasi_id);
                    return $response['response'] = [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Data berhasil dihapus',
                    ];
                } else {
                    return $response['response'] = [
                        'title' => 'Proses Gagal !',
                        'text' => 'Data Gagal di hapus',
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        return $this->getDataRuangan($id)->asArray()->one();
    }

    private function getDataRuangan($id = null) 
    {
        $getRuangan = Ruangan::find()
            ->select(['ruangan_m.*', 'satusehat_ruangan.satusehat_ruangan_id'])
            ->leftJoin('(
                    SELECT 
                        r_satusehat.ruangan_id, 
                        r_satusehat.satusehat_ruangan_id,
                        r_satusehat.satusehat_integration_id
                    FROM 
                        ruangan_satusehat_m r_satusehat 
                    WHERE 
                        r_satusehat.satusehat_ruangan_id IS NOT NULL 
                        AND is_deleted = FALSE
                        AND is_active = TRUE
                ) satusehat_ruangan', 'satusehat_ruangan.ruangan_id = ruangan_m.ruangan_id')
            ->where(['is_deleted' => false]);
        if ($id) {
            $getRuangan->where(['ruangan_m.ruangan_id' => $id]);
        }

        $antrian = JenisAntrianRuanganMp::find()->Where(['ruangan_id' => $id])->one();
        if (!empty($antrian)){
            // $getRuangan->lantairuangan_id = $antrian['jenisantriandetail_id'];
        }

        return $getRuangan;
    }

    private function sinkronRuangan($post)
    {
        try {
            $restSync = Yii::$app->docoRest->sinkronisasi;
            $request = $restSync->post('sync-accounting/ruangan', [
                'json' => $post
            ]);

            $response = json_decode($request->getBody(), true);
            return $response['response'];
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionListLantai($id = null)
    {
        $data = JenisAntrianDetail::find()
            ->where(['is_active' => 't', 'is_deleted' => 'f'])
            ->select(['jenisantriandetail_id', 'nama']);

        $items = ArrayHelper::map($data->all(), 'jenisantriandetail_id', 'nama');

        $antrian_lantai = JenisAntrianRuanganMp::find()->Where(['ruangan_id' => $id])->one();

        return [
            'list_lantai' => $items,
            'get_lantai' => $antrian_lantai
        ];
    }

    /**
     * @author Andri Amirul (andri.amirul@sirs.co.id)
     * @method getRuanganRanap (Mengambil Data Ruangan Ranap)
     * @param String $term
     * @param Integer $page
     * @return Object
     */
    public function actionGetRuanganRanap()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $instalasiRanapId = (new LookUpTransaksiRepositories)->getInstalasiIdRi();
        $query = new \yii\db\Query();
        $ruanganQuery = $query->from('ruangan_m')
            ->where(['instalasi_id' => $instalasiRanapId]);
        if (!empty($term)) {
            $ruanganQuery->andWhere(['ILIKE', 'LOWER(ruangan_nama)', strtolower($term)]);
        }
        return $ruanganQuery->limit($limit)->offset(($page - 1) * $limit)->all();
    }

    /**
     * @author Bambang M A
     * @method actionGetRuanganRanapDep (Mengambil Data Ruangan Ranap Tanpa Infinity Scroll)
     * @return Array
     */
    public function actionGetRuanganRanapDep()
    {
        $instalasiRanapId = (new LookUpTransaksiRepositories)->getInstalasiIdRi();
        $result = Ruangan::find()->where(['is_active' => true, 'instalasi_id' => $instalasiRanapId]);
        return $result->asArray()->all();
    }


    /**
     * @author Andri Amirul
     * @method actionGetRuanganByInstalasiDep (Mengambil Data Ruangan Dengan Instalasi_id Untuk Widget Yang Dependen dengan Instalasi)
     * @param Integer $instalasi_id
     * @return Array
     */
    public function actionGetRuanganByInstalasiDep($instalasi_id = null)
    {
        $result = Ruangan::find();
        if ($instalasi_id) {
            $result->where(['is_active' => true])
                ->andWhere(['instalasi_id' => $instalasi_id]);
        }
        return $result->asArray()->all();
    }

    public function actionGetByRiwayat($norm)
    {
        $datas = InfoRiwayatPasienView::find()
            ->select(['ruangan_pend_id', 'ruangan_pend', 'ruangan_adm_id', 'ruangan_adm'])
            ->andWhere([
                'no_rekam_medik' => $norm,
            ])
            ->distinct()->asArray()->all();


        return $datas;
    }

    /**
     * @method actionGetRuanganRajal (Get Ruangan Rawat Jalan)
     * @return Array
     */
    public function actionGetRuanganRajal()
    {
        $validator = new Validator();

        $lookupTransaksi = new LookUpTransaksiRepositories;
        $instalasiRjId = $lookupTransaksi->getInstalasiIdRj();

        if ($validator->isEmpty($instalasiRjId)) return $this->responseJson(400, 'Instalasi ID Rawat Jalan Tidak Kosong');

        $ruangan = Ruangan::find()
            ->andWhere(['instalasi_id' => $instalasiRjId])
            ->asArray()
            ->all();

        return $ruangan;
    }
}
