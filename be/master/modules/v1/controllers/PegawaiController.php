<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-17 16:00
 * @Last Modified by:   Ragnar-Lothbroc
 * @Description: controller untuk master Pegawai / Dokter (Pendaftaran)
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstansId;
use Doco\Repositories\LookUpTransaksiRepositories;
use Doco\Services\Esign\TilakaService;

use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Jabatan;
use app\modules\v1\models\PendidikanKualifikasi;
use app\modules\v1\models\Pangkat;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\LoginPemakai;
use yii\web\UploadedFile;   
use yii\helpers\Url;

class PegawaiController extends \Doco\components\DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\Pegawai';

    public $messageBroker = [
        'create-pegawai' => [
            'services' => [
                'Odoo' => [
                    'Pegawai' => [
                        'last_insert' => true
                    ]
                ],
                'Satusehat' => [
                    'SyncPegawaiSatusehat' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'Pegawai' => [
                        'query_params' => ['id'],
                    ]
                ],
                'Satusehat' => [
                    'SyncPegawaiSatusehat' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'update'
                    ]
                ]
    
            ]
        ],
        'delete' => [
            'services' => [
                'Mhg' => [
                    'DoctorSchedule' => [
                        'payload' => ['id' => 'doctor_id'],
                        'successProcess' => true,
                        'isDoctor' => true,
                        'state' => 'delete',
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST"];
        $verbs["delete"] = ["DELETE", "POST"];
        $verbs["get-list-data-pegawai"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $model = new Pegawai;
            $query = $model->find();
            if (isset($_GET['transpac'])) {
                $addAttributes = [
                    'suku_nama' => 'suku.suku_nama',
                    'propinsi_nama' => 'propinsi.propinsi_nama',
                    'kabupaten_nama' => 'kabupaten.kabupaten_nama',
                    'kecamatan_nama' => 'kecamatan.kecamatan_nama',
                    'kelurahan_nama' => 'kelurahan.kelurahan_nama',
                    'pendidikan_nama' => 'pendidikan.pendidikan_nama',
                    'pendkualifikasi_nama' => 'pendkualifikasi.pendkualifikasi_nama',
                    'jabatan_nama' => 'jabatan.jabatan_nama',
                    'pangkat_nama' => 'pangkat.pangkat_nama',
                    'kelompokpegawai_nama' => 'kelompokpegawai.kelompokpegawai_nama',
                    'agama_nama' => 'agama.lookup_name',
                    'jeniskelamin_nama' => 'jeniskelamin.lookup_name',
                    'status_kawin_nama' => 'status_kawin.lookup_name',
                    'golongan_darah_nama' => 'golongan_darah.lookup_name',
                    'warganegara_pegawai_nama' => 'warganegara_pegawai.lookup_name',
                    'kemampuan_bahasa_nama' => 'kemampuan_bahasa.lookup_name',
                ];
                $query->select(array_merge(['pegawai_m.*'], $addAttributes));
                $query->with([
                    'ruangan' => function($query) {
                        $query->select([
                            'ruangan_id',
                            'instalasi_id',
                            'ruangan_nama',
                            'is_active'
                        ]);
                    },
                    // 'instalasi' => function($query) {
                    //     $query->select([
                    //         'instalasi_id',
                    //         'instalasi_nama',
                    //         'is_active'
                    //     ]);
                    // },
                ]);
                $query->leftJoin(
                    'propinsi_m propinsi',
                    'pegawai_m.propinsi_id = propinsi.propinsi_id'
                );
                $query->leftJoin(
                    'kabupaten_m kabupaten',
                    'pegawai_m.kabupaten_id = kabupaten.kabupaten_id'
                );
                $query->leftJoin(
                    'kecamatan_m kecamatan',
                    'pegawai_m.kecamatan_id = kecamatan.kecamatan_id'
                );
                $query->leftJoin(
                    'kelurahan_m kelurahan',
                    'pegawai_m.kelurahan_id = kelurahan.kelurahan_id'
                );
                $query->leftJoin(
                    'lookup_m agama',
                    "pegawai_m.agama = agama.lookup_id::text and agama.lookup_type = 'agama'"
                );
                $query->leftJoin(
                    'lookup_m jeniskelamin',
                    "pegawai_m.jeniskelamin = jeniskelamin.lookup_id::text and jeniskelamin.lookup_type = 'jenis_kelamin'"
                );
                $query->leftJoin(
                    'lookup_m status_kawin',
                    "pegawai_m.status_kawin = status_kawin.lookup_id and status_kawin.lookup_type = 'status_perkawinan'"
                );
                $query->leftJoin(
                    'lookup_m golongan_darah',
                    "pegawai_m.golongan_darah = golongan_darah.lookup_id and golongan_darah.lookup_type = 'golongan_darah'"
                );
                $query->leftJoin(
                    'lookup_m warganegara_pegawai',
                    "pegawai_m.warganegara_pegawai = warganegara_pegawai.lookup_id::text and warganegara_pegawai.lookup_type = 'warga_negara'"
                );
                $query->leftJoin(
                    'lookup_m kemampuan_bahasa',
                    "pegawai_m.kemampuan_bahasa = kemampuan_bahasa.lookup_id::text and kemampuan_bahasa.lookup_type = 'bahasa'"
                );
                $query->leftJoin(
                    'suku_m suku',
                    "pegawai_m.suku_id = suku.suku_id"
                );
                $query->leftJoin(
                    'pendidikan_m pendidikan',
                    "pegawai_m.pendidikan_id = pendidikan.pendidikan_id"
                );
                $query->leftJoin(
                    'pendidikankualifikasi_m pendkualifikasi',
                    "pegawai_m.pendkualifikasi_id = pendkualifikasi.pendkualifikasi_id"
                );
                $query->leftJoin(
                    'jabatan_m jabatan',
                    "pegawai_m.jabatan_id = jabatan.jabatan_id"
                );
                $query->leftJoin(
                    'pangkat_m pangkat',
                    "pegawai_m.pangkat_id = pangkat.pangkat_id"
                );
                $query->leftJoin(
                    'kelompokpegawai_m kelompokpegawai',
                    "pegawai_m.kelompokpegawai_id = kelompokpegawai.kelompokpegawai_id"
                );
                $query->asArray();
                foreach ($addAttributes as $key => $field) {
                    if (isset($_GET['advanced-filter'][$key])) {
                        $query->andWhere(['ILIKE', $field, $_GET['advanced-filter'][$key]]);
                        unset($_GET['advanced-filter'][$key]);
                    }
                }
            }
            return new ActiveDataProvider([
                'query' => DocoRestActiveFilter::advancedFilter($model, $query),
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @author Rizal
     * @since 2018-01-29 11:50:50
     * @param
     * @return array map list data dokter rajal
     * @desc
     */
    public function actionAllowListDokterRajal()
    {
        $data = Pegawai::find()
            ->where(['is_active' => 't'])
            ->orderBy('nama_pegawai');
        $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

        return $items;
    }


    //edit by rizqi febian
    //edit for handle modal action di pegawai ruangan
    //23-02-2018
    public function actionIndexView()
    {        
        $model = new PegawaiMasterView();
        $query = $model->find();

        $request = Yii::$app->request;
        if ($request->get('advanced-filter')) {
            $advancedFilter = $request->get('advanced-filter');
            if (isset($advancedFilter['nama_pegawai'])) {
                $nama_pegawai = $advancedFilter['nama_pegawai'];
                $query->andWhere(['ILIKE', 'nama_pegawai', $nama_pegawai]);
            }
            if (isset($advancedFilter['nomorindukpegawai'])) {
                $nip = $advancedFilter['nomorindukpegawai'];
                $query->andWhere(['nomorindukpegawai' => $nip]);
            }
            if (isset($advancedFilter['jabatan_nama'])) {
                $jabatan_id = $advancedFilter['jabatan_nama'];
                $query->andWhere(['jabatan_id' => $jabatan_id]);
                unset($_GET['advanced-filter']['jabatan_nama']);
            }
            if (isset($advancedFilter['pangkat_nama'])) {
                $pangkat_id = $advancedFilter['pangkat_nama'];
                $query->andWhere(['pangkat_id' => $pangkat_id]);
                unset($_GET['advanced-filter']['pangkat_nama']);
            }
            if (isset($advancedFilter['aktif'])) {
                $aktif = $advancedFilter['aktif'];
                if($aktif == 1) {
                    $query->andWhere(['is_active' => true]);
                } else if($aktif == 2) {
                    $query->andWhere(['is_active' => false]);
                }
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['nama_pegawai' => SORT_ASC]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }


    public function actionCreatePegawai()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        $post = isset($post['PegawaiForm']) ? $post['PegawaiForm'] : $post;
        $model = new Pegawai;
        try {
            $model->load($post);
            $model->attributes = $post;
            $model->photopegawai_blob = ArrayHelper::getValue($post, 'photopegawai_blob', null);
            $is_active = true;
            // isset($post["aktif"]) && $post["aktif"] == 1 ? true : false;
            if(isset($post['is_active'])) {
                $is_active = $post['is_active'];
            } else if(isset($post["aktif"])) {
                $is_active = $post["aktif"] == 1 ? true : false;
            }
            // $is_active = isset($post['is_active']) ? $post['is_active'] : true;
            // $model->is_active = false;
            // if(!empty($post['is_active'])) {
            //     $model->is_active = true;
            // }
            $model->is_active = $is_active;
            $additional = !empty($model->additional_data) ? json_decode($model->additional_data, true) : [];
            if (!empty($additional['doctor_code'])) {
                $codeDoc = $additional['doctor_code'];
                $validate = $this->getDocByCode($codeDoc);
                if (!empty($validate)) {
                    $model->addError('doctor_code', 'kode dokter sudah di pakai');
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
                $model->nomorindukpegawai = $codeDoc;
            }

            if ($model->validate() && $model->save()) {
                // For Update LastModifiedDate
                $model->is_active = $is_active;
                $model->save();
                $transaction->commit();
                return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                    'additional' => [
                        'additional' => [
                            'doctor_id' => $model->getPrimaryKey(),
                        ]
                    ]
                ]);
            } else {
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($model->errors, 'PegawaiForm');
                foreach ($model->errors as $key => $value) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => $value[0]
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }

    public function actionViewData($id)
    {
        $model = new PegawaiMasterView;
        $query = $model->find();
        if ($id) {
            $query->andWhere(['pegawai_id' => $id]);
        }

        return $query->one();
    }

    private function checkLoginPemakaiTransaksi($id)
    {
        try {
            $model = new LoginPemakai;
            $getData = $model::find()->where(['pegawai_id' => $id])
                ->count();
            return $getData;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $id = $request->get('id');
        if (isset($post['doctor_id']) && !empty($post['doctor_id'])) {
            $id = $post['doctor_id'];
        }
        try {
            $checkData = $this->checkLoginPemakaiTransaksi($id);
            if ($checkData > 0) {
                return $response['response'] = [
                    'title' => 'Proses Hapus Gagal !',
                    'text' => 'Pegawai ini Tidak bisa di hapus.',
                    'status' => 422
                ];
            } else {
                $model = Pegawai::findOne($id);
                if (!empty($model) && $model->delete()) {
                    return $response['response'] = [
                        'title' => 'Proses Hapus Berhasil !',
                        'text' => 'Data berhasil dihapus',
                    ];
                } else {
                    return $response['response'] = [
                        'title' => 'Proses Hapus Gagal !',
                        'text' => 'Pegawai tidak ditemukan',
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $post = $request->post();
            $id = $request->get('id', null);
            if (isset($post['dokter_id']) && !empty($post['dokter_id'])) {
                $id = $post['dokter_id'];
            }

            $oldNik = Pegawai::find()->select(['noidentitas'])->where(['pegawai_id' => $id])->asArray()->one();
            $oldNik = $oldNik['noidentitas'];
            
            $model = Pegawai::findOne($id);
            if (empty($model)) {
                return [
                    'title' => 'Proses Hapus Gagal !',
                    'text' => 'Pegawai tidak ditemukan',
                    'status' => 422
                ];
            }
            $postData = isset($post['PegawaiForm']) ? $post['PegawaiForm'] : $post;
            $model->attributes = $postData;
            $photoBlob = ArrayHelper::getValue($postData, 'photopegawai_blob', null);
            if (!empty($photoBlob)) {
                $model->photopegawai_blob = $photoBlob;
            }

            $tandaTanggan = ArrayHelper::getValue($postData, 'tanda_tangan', null);
            if (!empty($tandaTanggan)) {
                $model->tanda_tangan = $tandaTanggan;
            }
            $is_active = true;
            if(isset($postData['is_active'])) {
                $is_active = $postData['is_active'];
            } else if (isset($postData["aktif"])) {
                $is_active = $postData["aktif"] == 1 ? true : false;
            }
            $model->is_active = $is_active;
            $additional = !empty($model->additional_data) ? json_decode($model->additional_data, true) : [];
            if (!empty($additional['doctor_code'])) {
                $codeDoc = $additional['doctor_code'];
                $validate = $this->getDocByCode($codeDoc);
                if (!empty($validate) && $model->pegawai_id !== $validate['pegawai_id']) {
                    $model->addError('doctor_code', 'kode dokter sudah di pakai');
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
                $model->nomorindukpegawai = $codeDoc;
            }
            if ($model->validate() && $model->save()) {
                $transaction->commit();
                return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                    'additional' => [
                        'doctor_id' => $id,
                        'old_nik' => $oldNik
                    ]
                ]);
            } else {
                $transaction->rollBack();
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $model->errors
                ]);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }

    protected $_title = 'Master Pegawai';
    public function actionExportExcel()
    {
        $title = 'Master Pegawai';
        try {
            $request = Yii::$app->request;

            $searchNamaPegawai = '';
            $searchNIP = '';
            $searchJabatan = '';
            $searchpangkat = '';
            $searchaktif = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if (isset($advancedFilters)) {
                if (!empty($advancedFilters['nama_pegawai'])) {
                    $searchNamaPegawai = $advancedFilters['nama_pegawai'];
                }

                if (!empty($advancedFilters['nomorindukpegawai'])) {
                    $searchNIP = $advancedFilters['nomorindukpegawai'];
                }

                if (!empty($advancedFilters['aktif'])) {
                    $searchaktif = $advancedFilters['aktif'] == 1 ? "Pegawai Aktif" : "Pegawai Tidak Aktif";
                }
            }
     
            $model = new PegawaiMasterView();
            $query = $model->find();
    
            $request = Yii::$app->request;
            if ($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if (isset($advancedFilter['nama_pegawai'])) {
                    $nama_pegawai = $advancedFilter['nama_pegawai'];
                    $query->andWhere(['ILIKE', 'nama_pegawai', $nama_pegawai]);
                }
                if (isset($advancedFilter['nomorindukpegawai'])) {
                    $nip = $advancedFilter['nomorindukpegawai'];
                    $query->andWhere(['nomorindukpegawai' => $nip]);
                }
                if (isset($advancedFilter['jabatan_nama'])) {
                    $jabatan_id = $advancedFilter['jabatan_nama'];
                    $query->andWhere(['jabatan_id' => $jabatan_id]);
                    unset($_GET['advanced-filter']['jabatan_nama']);
                }
                if (isset($advancedFilter['pangkat_nama'])) {
                    $pangkat_id = $advancedFilter['pangkat_nama'];
                    $query->andWhere(['pangkat_id' => $pangkat_id]);
                    unset($_GET['advanced-filter']['pangkat_nama']);
                }
                if (isset($advancedFilter['aktif'])) {
                    $aktif = $advancedFilter['aktif'];
                    if($aktif == 1) {
                        $query->andWhere(['is_active' => true]);
                    } else if($aktif == 2) {
                        $query->andWhere(['is_active' => false]);
                    }
                }
            }
    
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['nama_pegawai' => SORT_ASC]);

            $result = $query->all();

            $data = [];
            if (!empty($result)) {
                $counter = 0;
                foreach ($result as $index => $value) {

                    $satuSehatId = isset($value["satusehat_pegawai_id"]) ? $value["satusehat_pegawai_id"] : null;
                    $data[$counter][\Yii::t('app', 'Nama pegawai')] = $value["nama_pegawai"];
                    $data[$counter][\Yii::t('app', 'NIP')] = $value["nomorindukpegawai"];
                    $data[$counter][\Yii::t('app', 'Jabatan nama')] = $value["jabatan_nama"];
                    $data[$counter][\Yii::t('app', 'Pangkat nama')] = $value["pangkat_nama"];
                    $data[$counter][\Yii::t('app', 'Pegawai Aktif')] = $value["is_active"] ? "Aktif" : "Tidak Aktif";
                    $data[$counter][\Yii::t('app', 'Satu Sehat Practitioner ID')] = $satuSehatId;
                    $counter++;
                    if (!empty($advancedFilters['jabatan_nama'])) {
                        $searchJabatan = $value["jabatan_nama"];
                    }
                    if (!empty($advancedFilters['pangkat_nama'])) {
                        $searchpangkat = $value["pangkat_nama"];
                    }
                }
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Nama Pegawai' => $searchNamaPegawai,
                'NIP' => $searchNIP,
                'Jabatan' => $searchJabatan,
                'Pangkat' => $searchpangkat,
                'Status Pegawai' => $searchaktif,
            ];

            $header = array_filter($header);

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Nama' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }


    public function dataPegawai()
    {
        $data = PegawaiMasterView::find();

        return $data;
    }

    public function actionDataPegawai()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataPegawai();
        $result->select(['pegawai_id', 'nama_pegawai']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE', 'LOWER(nama_pegawai)', $term]);
        }
        $result->groupBy(['pegawai_id', 'nama_pegawai']);

        return $result->asArray()->all();
    }

    public function actionDataNip()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataPegawai();
        $result->select(['nomorindukpegawai', 'nomorindukpegawai']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE', 'LOWER(nomorindukpegawai)', $term]);
        }
        $result->groupBy(['nomorindukpegawai', 'nomorindukpegawai']);

        return $result->asArray()->all();
    }

    public function actionDataJabatan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = Jabatan::find();
        $result->select(['jabatan_id', 'jabatan_nama']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE', 'LOWER(jabatan_nama)', $term]);
        }
        $result->groupBy(['jabatan_id', 'jabatan_nama']);

        return $result->asArray()->all();
    }

    public function actionDataPangkat()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = Pangkat::find();
        $result->select(['pangkat_id', 'pangkat_nama']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE', 'LOWER(pangkat_nama)', $term]);
        }
        $result->groupBy(['pangkat_id', 'pangkat_nama']);

        return $result->asArray()->all();
    }

    public function actionGetKualifikasi()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $result = PendidikanKualifikasi::find();
        $result->select(['pendkualifikasi_id', 'pendkualifikasi_nama']);
        // if (!empty($get['id'])) {
        //     $term = $get['id'];
        //     $result->where(['pendidikan_id' => $term]);
        // }

        return $result->asArray()->all();
    }

    /**
     * @controller actionExportPdf
     * @attribute #table_exportpdf# => Untuk menampilkan data di table
     * @attribute #searchNamaPegawai# => Untuk menampilkan filter data nama pegawai
     * @attribute #searchNIP# => Untuk menampilkan filter data NIP
     * @attribute #searchJabatan# => Untuk menampilkan filter data jabatan
     * @attribute #searchpangkat# => Untuk menampilkan filter data pangkat
     */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;

            $searchNamaPegawai = '';
            $searchNIP = '';
            $searchJabatan = '';
            $searchpangkat = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if (isset($advancedFilters)) {
                if (!empty($advancedFilters['nama_pegawai'])) {
                    $searchNamaPegawai = $advancedFilters['nama_pegawai'];
                }

                if (!empty($advancedFilters['nomorindukpegawai'])) {
                    $searchNIP = $advancedFilters['nomorindukpegawai'];
                }
            }
     
            $model = new PegawaiMasterView();
            $query = $model->find();
    
            $request = Yii::$app->request;
            if ($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if (isset($advancedFilter['nama_pegawai'])) {
                    $nama_pegawai = $advancedFilter['nama_pegawai'];
                    $query->andWhere(['ILIKE', 'nama_pegawai', $nama_pegawai]);
                }
                if (isset($advancedFilter['nomorindukpegawai'])) {
                    $nip = $advancedFilter['nomorindukpegawai'];
                    $query->andWhere(['nomorindukpegawai' => $nip]);
                }
                if (isset($advancedFilter['jabatan_nama'])) {
                    $jabatan_id = $advancedFilter['jabatan_nama'];
                    $query->andWhere(['jabatan_id' => $jabatan_id]);
                    unset($_GET['advanced-filter']['jabatan_nama']);
                }
                if (isset($advancedFilter['pangkat_nama'])) {
                    $pangkat_id = $advancedFilter['pangkat_nama'];
                    $query->andWhere(['pangkat_id' => $pangkat_id]);
                    unset($_GET['advanced-filter']['pangkat_nama']);
                }
                if (isset($advancedFilter['aktif'])) {
                    $aktif = $advancedFilter['aktif'];
                    if($aktif == 1) {
                        $query->andWhere(['is_active' => true]);
                    } else if($aktif == 2) {
                        $query->andWhere(['is_active' => false]);
                    }
                }
            }
    
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['nama_pegawai' => SORT_ASC]);
            $getData = $query->all();

            $result = [];
            foreach ($getData as $key => $value) {
                $result[] = $value;
                if (!empty($advancedFilters['jabatan_nama'])) {
                    $searchJabatan = $value["jabatan_nama"];
                }
                if (!empty($advancedFilters['pangkat_nama'])) {
                    $searchpangkat = $value["pangkat_nama"];
                }
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#table_exportpdf#' => $this->renderPartial('index', [
                    'result' => $result,
                ]),
                '#searchNamaPegawai#' => $searchNamaPegawai,
                '#searchNIP#' => $searchNIP,
                '#searchJabatan#' => $searchJabatan,
                '#searchpangkat#' => $searchpangkat,
            ];
            $print->Output();
            die();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }


    private function getDocByCode($codeDoc)
    {
        return Yii::$app->db->createCommand("
            SELECT 
                pegawai_id 
            FROM pegawai_m WHERE additional_data::json->>'doctor_code' = '{$codeDoc}' AND is_deleted = false
        ")->queryOne();
    }

    public function actionDataDokter()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = Pegawai::find();
        $result->where(['is_active' => 't']);
        $result->andWhere(['is_deleted' => 'f']);
        $result->andWhere(['kelompokpegawai_id' => 1]);
        if(!empty($post['tgl_awal']) && !empty($post['tgl_akhir'])){
            $tgl_awal = date('Y-m-d', strtotime($post['tgl_awal']));
            $tgl_akhir = date('Y-m-d', strtotime($post['tgl_akhir']));
            $result->andWhere(['between', 'date(created_date)', $tgl_awal, $tgl_akhir]);
        }
        return $result->asArray()->all();
    }

    public function actionGetListDataPegawai()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $ruangan_id = $request->get('ruangan_id', null);
        $additionalPayload = $request->get('additionalPayload', []);
        $instalasi_id = ArrayHelper::getValue($additionalPayload, 'instalasi_id', null);
        $column = ArrayHelper::getValue($additionalPayload, 'column', []);
        $kelompokpegawai_id = ArrayHelper::getValue($additionalPayload, 'kelompokpegawai_id', null);

        if ( empty($column) ) {
            $column = ['ruangan_id', 'pegawai_id', 'nama_pegawai', 'instalasi_id'];
        }

        // pegawai
        $modelPegawai = new PegawaiView;
        $queryPegawai = $modelPegawai::find()->where(['is_active' => true])
            ->select($column)
            ->groupBy($column);

        if (!empty($term)) {
            $queryPegawai->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
        }
        if (!empty($ruangan_id)) {
            $queryPegawai->andWhere(['ruangan_id' => $ruangan_id]);
        }
        if (!empty($instalasi_id)) {
            $queryPegawai->andWhere(['instalasi_id' => $instalasi_id]);
        }
        if ( !empty($kelompokpegawai_id) ) {
            $queryPegawai->andWhere(['kelompokpegawai_id' => $kelompokpegawai_id]);
        }

        $queryPegawai->orderBy(['nama_pegawai' => SORT_ASC]);

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $queryPegawai->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     * @method actionGetTenagaMedis (Pegawai Dengan Kelompok Tenaga Medis)
     * @param String $term
     * @param Integer $page
     * @return Object
     */
    public function actionGetTenagaMedis()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $tenagaMedisId = (new LookUpTransaksiRepositories)->getTenagaMedisId();
        $query = new \yii\db\Query();
        $pegawaiQuery = $query->from('pegawai_m')
            ->andWhere([
                'kelompokpegawai_id' => $tenagaMedisId,
                'is_deleted' => false,
                'is_active' => true
            ]);
        if (!empty($term)) {
            $pegawaiQuery->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($term)]);
        }
        return $pegawaiQuery->limit($limit)->offset(($page - 1) * $limit)->all();
    }

    public function actionGetGambarPegawai(){
        $request = Yii::$app->request;
        $pegawai_id = $request->get('pegawai_id');
        $model = Pegawai::findOne($pegawai_id);
        if (!empty($model['photopegawai'])){
            $dir = Yii::$app->urlManagerFrontend->createUrl('');
            $rootPath = 'media/img/foto-dokter';
            $photo = isset($model['photopegawai']) ? $model['photopegawai'] : null;
            $path = $dir.$rootPath.'/'.$photo;
            return $path;
        } else {
            return $response['response'] = [
                'title' => 'Proses Ambil Gambar Gagal !',
                'text' => 'Gambar pegawai tidak ditemukan',
                'status' => 404
           ];
        }
    }

    public function actionGetEsignRegis($id, $type='regis'){
        $model = Pegawai::find()
            ->select([
                'pegawai_id',
                'alamatemail',
                'nama_pegawai',
                'jenisidentitas',
                'noidentitas',
                'additional_esign_data'
            ])
            ->where(['pegawai_id' => $id])
            ->asArray()->one();

        if(empty($model)){
            throw new \Exception('Pegawai tidak ditemukan');
        }

        $additional_esign_data = json_decode($model['additional_esign_data'], true);
        $curr_status = TilakaService::getCurrentStatus($additional_esign_data);
        if($type == 'regis' && !in_array($curr_status, [TilakaService::STATUS_REJECTED, TilakaService::STATUS_EXPIRED, TilakaService::STATUS_OTHER,])) {
            throw new \Exception('Pegawai sudah pernah didaftarkan');
        }
        if($type == 'reenroll' && !in_array($curr_status, [TilakaService::STATUS_INACTIVE, TilakaService::STATUS_REJECTED,])) {
            throw new \Exception('Pegawai tidak bisa Re-Enroll');
        }

        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);
        $tnc = isset($configEsign['tnc']['consent_text_html']) ? $configEsign['tnc']['consent_text_html'] : null;
        return [
            'data' => [
                'pegawai' => $model,
                'tnc' => $tnc,
            ],
        ];
    }

    public function actionEsignRegis($id){
        $request = Yii::$app->request;
        $post = $request->post();
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);
        $class = $configEsign['provider'];

        $model = Pegawai::find()
            ->select([
                'pegawai_id',
                'alamatemail',
                'nama_pegawai',
                'jenisidentitas',
                'noidentitas',
                'additional_esign_data'
            ])
            ->where(['pegawai_id' => $id])
            ->asArray()->one();

        if(empty($model)){
            throw new \Exception('Pegawai tidak ditemukan');
        }

        $additional_esign_data = json_decode($model['additional_esign_data'], true);
        $curr_status = TilakaService::getCurrentStatus($additional_esign_data);
        if(!in_array($curr_status, [TilakaService::STATUS_REJECTED, TilakaService::STATUS_EXPIRED, TilakaService::STATUS_OTHER,])) {
            throw new \Exception('Pegawai sudah pernah didaftarkan');
        }

        try {
            $data = $post['data'];
            foreach ($data as $key => $value) {
                if(empty($value)) {
                    unset($data[$key]);
                }
            }
            $register = $class::register($data);
            $additional_esign_data['registration_data'] = $register;
            unset($additional_esign_data['registration_result']);
            unset($additional_esign_data['cert_status']);
            Pegawai::updateAll([
                'additional_esign_data' => json_encode($additional_esign_data),
            ], [
                'pegawai_id' => $id,
            ]);
            return [
                'status' => 200,
                'message' => 'Sukses Register',
                'data' => [],
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function actionReEnroll($id){
        $request = Yii::$app->request;
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);
        $class = $configEsign['provider'];

        $model = Pegawai::find()
            ->select([
                'additional_esign_data'
            ])
            ->where(['pegawai_id' => $id])
            ->asArray()->one();

        if(empty($model)){
            throw new \Exception('Pegawai tidak ditemukan');
        }

        $additional_esign_data = json_decode($model['additional_esign_data'], true);
        $curr_status = TilakaService::getCurrentStatus($additional_esign_data);
        if(!in_array($curr_status, [TilakaService::STATUS_INACTIVE,TilakaService::STATUS_REJECTED,])) {
            throw new \Exception('Pegawai tidak bisa Re-Enroll');
        }

        try {
            $data = $request->post('data', []);
            $data['tilaka_name'] = isset($additional_esign_data['registration_result']['tilaka_name']) ? 
                $additional_esign_data['registration_result']['tilaka_name'] : "";
            foreach ($data as $key => $value) {
                if(empty($value)) {
                    unset($data[$key]);
                }
            }

            $reenroll = $class::reenroll($data);
            $reenroll['date_expire'] = null;
            $reenroll['done_reenroll'] = false;
            $additional_esign_data['registration_data'] = array_merge($additional_esign_data['registration_data'], $reenroll);
            $additional_esign_data['cert_status'] = TilakaService::certificateStatus(isset($additional_esign_data['registration_result']['tilaka_name']) ? 
                $additional_esign_data['registration_result']['tilaka_name'] : "");
            Pegawai::updateAll([
                'additional_esign_data' => json_encode($additional_esign_data),
            ], [
                'pegawai_id' => $id,
            ]);
            return [
                'status' => 200,
                'message' => 'Sukses Register',
                'data' => [],
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function actionGetEsignRevoke($id){
        $model = Pegawai::find()
            ->select([
                'useresign_id',
                'additional_esign_data',
            ])
            ->where(['pegawai_id' => $id])
            ->asArray()->one();

        if(empty($model)){
            throw new \Exception('Pegawai tidak ditemukan');
        }

        $additional_esign_data = json_decode($model['additional_esign_data'], true);
        $status = TilakaService::getCurrentStatus($additional_esign_data);
        if($status != TilakaService::STATUS_ACTIVE || empty($model['useresign_id'])) {
            throw new \Exception('Pegawai Tidak memiliki sertifikat aktif');
        }
        if(isset($additional_esign_data['revoke_data'])) {
            throw new \Exception('Sertifikat sudah di revoke');
        }

        return [
            'status' => 200,
            'message' => 'Sukses',
            'data' => [],
        ];
    }


    public function actionEsignRevoke($id){
        $request = Yii::$app->request;
        $post = $request->post();
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);
        $class = $configEsign['provider'];

        $model = Pegawai::find()
            ->select([
                'useresign_id',
                'additional_esign_data'
            ])
            ->where(['pegawai_id' => $id])
            ->asArray()->one();

        if(empty($model)){
            throw new \Exception('Pegawai tidak ditemukan');
        }

        $additional_esign_data = json_decode($model['additional_esign_data'], true);
        if($class::getCurrentStatus($additional_esign_data) != TilakaService::STATUS_ACTIVE || empty($model['useresign_id'])) {
            throw new \Exception('Pegawai tidak memiliki sertifikat aktif');
        }
        if(isset($additional_esign_data['revoke_data'])) {
            throw new \Exception('Sertifikat sudah di revoke');
        }

        try {
            $data = [
                'user_identifier' => $model['useresign_id'],
                'reason' => $post['data']['reason'],
            ];

            $revoke = $class::revoke($data);
            $additional_esign_data['revoke_data'] = [
                'reason' => $data['reason'],
                'revoke_id' => $revoke[0],
                'url' => $revoke[1],
                'last_revoke' => date('Y-m-d'),
            ];
            Pegawai::updateAll([
                'additional_esign_data' => json_encode($additional_esign_data),
            ], [
                'pegawai_id' => $id,
            ]);
            return [
                'status' => 200,
                'message' => 'Sukses Register',
                'data' => [],
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }


}
