<?php

/**
 * @Author: johndoe
 * @Date:   2018-04-26 10:45:06
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-27 15:35:42
 * @Description: controller untuk master Tindakan
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoPrint;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\TindakanRuangan;
use app\modules\v1\models\TarifTindakan;
use app\modules\v1\models\PaketPelayanan;
use app\modules\v1\models\TindakanBmhp;
use app\modules\v1\models\TindakanBmhpView;
use app\modules\v1\models\KategoriTindakan;
use app\modules\v1\models\KelompokTindakan;
use app\modules\v1\models\JenisKegiatanTindakan;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\GroupInaCbg;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\KonfigSystem;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use yii\helpers\ArrayHelper;

class TindakanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DaftarTindakan';
    /**
     * Untuk Kebutuhan Integerasi Odoo
     * @var array
     */
    public $messageBroker = [
        'create' => [
            'services' => [
                'Odoo' => [
                    'Tindakan' => [
                        'last_insert' => true,
                    ]
                ],
                'MobileMhg' => [
                    'Tindakan' => [
                        'last_insert' => true,
                        'state' => 'create',
                        'successProcess' => true
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'Tindakan' => [
                        'query_params' => ['id'],
                    ]
                ],
                'MobileMhg' => [
                    'Tindakan' => [
                        'query_params' => ['id'],
                        'state' => 'update',
                        'successProcess' => true
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'Tindakan' => [
                        'query_params' => ['id'],
                    ]
                ],
                'MobileMhg' => [
                    'Tindakan' => [
                        'query_params' => ['id'],
                        'state' => 'delete',
                        'successProcess' => true
                    ]
                ]
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
        $verbs["delete"] = ["DELETE", "POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }


    /**
     * @author johndoe
     * @since 2018-04-26 10:58:06
     * @return array list data
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new DaftarTindakan;
        $query = $model::find()->joinWith(['jeniskegiatantindakan', 'kategoritindakan', 'kelompoktindakan', 'groupinacbg']);

        if (!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            if (isset($advancedFilter['daftartindakan_kode'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_kode)', strtolower($advancedFilter['daftartindakan_kode'])],
                ]);
            }
            if (isset($advancedFilter['daftartindakan_nama'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($advancedFilter['daftartindakan_nama'])],
                ]);
            }
            if (isset($advancedFilter['daftartindakan_namalainnya'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_namalainnya)', strtolower($advancedFilter['daftartindakan_namalainnya'])],
                ]);
            }
            if (isset($advancedFilter['catatan'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_m.catatan)', strtolower($advancedFilter['catatan'])],
                ]);
            }
            if (isset($advancedFilter['status'])) {
                $is_active = $advancedFilter['status'];
                $query->andWhere(['daftartindakan_m.is_active' => $is_active]);
            }
            if (isset($advancedFilter['kategori_tindakan'])) {
                if (is_numeric($advancedFilter['kategori_tindakan'])) {
                    $query->andWhere(['daftartindakan_m.kategoritindakan_id' => $advancedFilter['kategori_tindakan']]);
                } else {
                    $query->andWhere(['ILIKE', 'kategoritindakan_m.kategoritindakan_nama', $advancedFilter['kategoritindakan']]);
                }
            }
            if (isset($advancedFilter['kategoritindakan_nama'])) {
                $query->andWhere(['kategoritindakan_m.kategoritindakan_nama' =>
                $advancedFilter['kategoritindakan_nama']]);
            }
            if (isset($advancedFilter['jeniskegiatantindakan_nama'])) {
                $query->andWhere(['jeniskegiatantindakan_m.jeniskegiatantindakan_nama' =>
                $advancedFilter['jeniskegiatantindakan_nama']]);
            }
            if (isset($advancedFilter['kelompoktindakan_nama'])) {
                $query->andWhere(['kelompoktindakan_m.kelompoktindakan_nama' => $advancedFilter['kelompoktindakan_nama']]);
            }
            if (isset($advancedFilter['groupinacbg_nama'])) {
                $query->andWhere(['groupinacbg_m.groupinacbg_nama' => $advancedFilter['groupinacbg_nama']]);
            }
            if (isset($advancedFilter['jenis_kegiatan'])) {
                if (is_numeric($advancedFilter['jenis_kegiatan'])) {
                    $query->andWhere(['daftartindakan_m.jeniskegiatantindakan_id' => $advancedFilter['jenis_kegiatan']]);
                } else {
                    $query->andWhere(['ILIKE', 'jeniskegiatantindakan_m.jeniskegiatantindakan_nama', $advancedFilter['jenis_kegiatan']]);
                }
            }
            if (isset($advancedFilter['kelompok_tindakan'])) {
                if (is_numeric($advancedFilter['kelompok_tindakan'])) {
                    $query->andWhere(['daftartindakan_m.kelompoktindakan_id' => $advancedFilter['kelompok_tindakan']]);
                } else {
                    $query->andWhere(['ILIKE', 'kelompoktindakan_m.kelompoktindakan_nama', $advancedFilter['kelompok_tindakan']]);
                }
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = DaftarTindakan::findOne($id);
            if ($request->post()) {
                $post = $request->post('DaftarTindakanForm');
                $model->attributes = $post;
                $additonalData = [];
                if (!empty($model->additional_data)) {
                    $additonalData = json_decode($model->additional_data, true);
                }
                $model->additional_data = json_encode(array_merge($additonalData, ['isGroupInaCbg' => $post['isGroupInaCbg']]));
                $model->is_active = $post['is_active'];
                $model->catatan = $post['catatan'];
                $is_akomodasi = ($model->is_akomodasi == 1) ? true : false;
                if ($is_akomodasi) {
                    $checkIsAkomodasi = $model::find()->where(['is_akomodasi' => true, 'is_deleted' => false])->one();

                    if (!empty($checkIsAkomodasi) && $checkIsAkomodasi->daftartindakan_id != $model->daftartindakan_id) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => 'Akomodasi Kamar sudah ada.',
                        ]);
                    }
                }

                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'DaftarTindakanForm');
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

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new DaftarTindakan;
            if (!empty($request->post())) {
                $model->attributes = $request->post('DaftarTindakanForm');
                $is_akomodasi = ($model->is_akomodasi == 1) ? true : false;
                if ($is_akomodasi) {
                    $checkIsAkomodasi = $model::find()
                        ->where(['is_akomodasi' => true, 'is_deleted' => false])
                        ->one();

                    if (!empty($checkIsAkomodasi)) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => 'Akomodasi Kamar sudah ada.',
                        ]);
                    }
                }

                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'DaftarTindakanForm');
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

    public function actionGenerateApi()
    {
        // kategori tindakan
        $modelKategoriTindakan = new KategoriTindakan;
        $queryKategoriTindakan = $modelKategoriTindakan::find()->where(['is_active' => true]);
        $queryKategoriTindakan = DocoRestActiveFilter::advancedFilter($modelKategoriTindakan, $queryKategoriTindakan);

        // kegiatan tindakan
        $modelJenisKegiatan = new JenisKegiatanTindakan;
        $queryJenisKegiatan = $modelJenisKegiatan::find()->where(['is_active' => true]);
        $queryJenisKegiatan = DocoRestActiveFilter::advancedFilter($modelJenisKegiatan, $queryJenisKegiatan);

        // kelompok tindakan
        $modelKelompokTindakan = new KelompokTindakan;
        $queryKelompokTindakan = $modelKelompokTindakan::find()->where(['is_active' => true]);
        $queryKelompokTindakan = DocoRestActiveFilter::advancedFilter($modelKelompokTindakan, $queryKelompokTindakan);

        // group ina cbg
        $modelInacbg = new GroupInaCbg;
        $queryInacbg = $modelInacbg::find()->where(['is_active' => true, 'is_obat' => false]);
        $queryInacbg = DocoRestActiveFilter::advancedFilter($modelInacbg, $queryInacbg);

        return [
            'kategori_tindakan' => $queryKategoriTindakan->asArray()->all(),
            'jenis_kegiatan' => $queryJenisKegiatan->asArray()->all(),
            'kelompok_tindakan' => $queryKelompokTindakan->asArray()->all(),
            'ina_cbg' => $queryInacbg->asArray()->all(),
        ];
    }

    public function actionView($id)
    {
        $count = TindakanPelayanan::find()->where(['daftartindakan_id' => $id])->count();
        $data = $this->getData($id)->asArray()->one();
        return [
            'count' => $count,
            'data' => $data,
            'config' => KonfigSystem::find()->select(['is_set_tindakan'])->one()
        ];
    }

    private function getData($id = null)
    {
        $model = DaftarTindakan::find()->joinWith([
            'jeniskegiatantindakan',
            'kategoritindakan',
            'kelompoktindakan',
            'groupinacbg',
            'serviceGroup',
            'serviceCategory',
        ]);
        if ($id) {
            $model->where(['daftartindakan_id' => $id]);
        }

        return $model;
    }

    // public function actionDelete($id)
    // {
    //     try {
    //         $data = DaftarTindakan::findOne($id);
    //         if (!empty($data)) {
    //             $data->is_deleted = true;
    //             $data->deleted_date = date('Y-m-d H:i:s');
    //             $data->deleted_by = Yii::$app->user->id;
    //             $data->save(false);
    //             \Yii::$app->response->statusCode = 500;
    //             $result = [
    //                 'title' => 'Terjadi kesalahan',
    //                 'text' => 'Data sudah di gunakan'
    //             ];
    //         } else {
    //             \Yii::$app->response->statusCode = 200;
    //             $result = (new KegiatanOperasi)->delete($id);
    //         }

    //         return $result;
    //     } catch (\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();

        try {
            $tindakanRuangan = TindakanRuangan::find()->where(['daftartindakan_id' => $id])->count();
            $paketPelayanan = PaketPelayanan::find()->where(['daftartindakan_id' => $id])->count();
            $tarifTindakan = TarifTindakan::find()->where(['daftartindakan_id' => $id])->count();
            if ($tindakanRuangan > 0 || $paketPelayanan > 0 || $tarifTindakan > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Tindakan, data sudah digunakan di master lain.";
                $result['title'] = "Proses Gagal!";
            } else {
                $model = Daftartindakan::findOne($id);
                if ($model) {
                    $model->is_deleted = true;
                    $model->deleted_date = date('Y-m-d H:i:s');
                    $model->save(false);
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Tindakan Berhasil',
                    ];
                } else {
                    $transaction->rollBack();
                    $result['status'] = 422;
                    $result['text'] = "Gagal Menghapus Data";
                }
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    protected $_title = "Data Master Tindakan";
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new DaftarTindakan;
        $query = $model::find()->joinWith(['jeniskegiatantindakan', 'kategoritindakan', 'kelompoktindakan', 'groupinacbg']);
        $header = array();

        if (!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            if (isset($advancedFilter['daftartindakan_kode'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_kode)', strtolower($advancedFilter['daftartindakan_kode'])],
                ]);

                $header[Yii::t('app', 'Kode Tindakan')] = $advancedFilter['daftartindakan_kode'];
            }
            if (isset($advancedFilter['daftartindakan_nama'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($advancedFilter['daftartindakan_nama'])],
                ]);

                $header[Yii::t('app', 'Nama Tindakan')] = $advancedFilter['daftartindakan_nama'];
            }
            if (isset($advancedFilter['daftartindakan_namalainnya'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_namalainnya)', strtolower($advancedFilter['daftartindakan_namalainnya'])],
                ]);

                $header[Yii::t('app', 'Nama Lainnya')] = $advancedFilter['daftartindakan_namalainnya'];
            }
            if (isset($advancedFilter['catatan'])) {
                $query->andFilterWhere([
                    'or',
                    ['ILIKE', 'LOWER(daftartindakan_m.catatan)', strtolower($advancedFilter['catatan'])],
                ]);

                $header[Yii::t('app', 'Catatan')] = $advancedFilter['catatan'];
            }
            if (isset($advancedFilter['status'])) {
                $is_active = $advancedFilter['status'];
                $query->andWhere(['daftartindakan_m.is_active' => $is_active]);

                $header[Yii::t('app', 'Status')] = $advancedFilter['status'] ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
            }
            if (isset($advancedFilter['kategori_tindakan'])) {
                if (is_numeric($advancedFilter['kategori_tindakan'])) {
                    $query->andWhere(['daftartindakan_m.kategoritindakan_id' => $advancedFilter['kategori_tindakan']]);
                } else {
                    $query->andWhere(['ILIKE', 'kategoritindakan_m.kategoritindakan_nama', $advancedFilter['kategoritindakan']]);
                }

                $header[Yii::t('app', 'Nama Kategori')] = $advancedFilter['kategori_tindakan'];
            }
            if (isset($advancedFilter['kategoritindakan_nama'])) {
                $query->andWhere(['kategoritindakan_m.kategoritindakan_nama' =>
                $advancedFilter['kategoritindakan_nama']]);

                $header[Yii::t('app', 'Nama Kategori')] = $advancedFilter['kategoritindakan_nama'];
            }
            if (isset($advancedFilter['jeniskegiatantindakan_nama'])) {
                $query->andWhere(['jeniskegiatantindakan_m.jeniskegiatantindakan_nama' =>
                $advancedFilter['jeniskegiatantindakan_nama']]);

                $header[Yii::t('app', 'Nama Kegiatan')] = $advancedFilter['jeniskegiatantindakan_nama'];
            }
            if (isset($advancedFilter['kelompoktindakan_nama'])) {
                $query->andWhere(['kelompoktindakan_m.kelompoktindakan_nama' => $advancedFilter['kelompoktindakan_nama']]);

                $header[Yii::t('app', 'Nama Kelompok')] = $advancedFilter['kelompoktindakan_nama'];
            }
            if (isset($advancedFilter['groupinacbg_nama'])) {
                $query->andWhere(['groupinacbg_m.groupinacbg_nama' => $advancedFilter['groupinacbg_nama']]);

                $header[Yii::t('app', 'Group INA CBGS')] = $advancedFilter['groupinacbg_nama'];
            }
            if (isset($advancedFilter['jenis_kegiatan'])) {
                if (is_numeric($advancedFilter['jenis_kegiatan'])) {
                    $query->andWhere(['daftartindakan_m.jeniskegiatantindakan_id' => $advancedFilter['jenis_kegiatan']]);
                } else {
                    $query->andWhere(['ILIKE', 'jeniskegiatantindakan_m.jeniskegiatantindakan_nama', $advancedFilter['jenis_kegiatan']]);
                }

                $header[Yii::t('app', 'Nama Kegiatan')] = $advancedFilter['jenis_kegiatan'];
            }
            if (isset($advancedFilter['kelompok_tindakan'])) {
                if (is_numeric($advancedFilter['kelompok_tindakan'])) {
                    $query->andWhere(['daftartindakan_m.kelompoktindakan_id' => $advancedFilter['kelompok_tindakan']]);
                } else {
                    $query->andWhere(['ILIKE', 'kelompoktindakan_m.kelompoktindakan_nama', $advancedFilter['kelompok_tindakan']]);
                }

                $header[Yii::t('app', 'Nama Kelompok')] = $advancedFilter['kelompok_tindakan'];
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Kode Tindakan')] = $value['daftartindakan_kode'];
            $newValue[\Yii::t('app', 'Nama Tindakan')] = $value['daftartindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['daftartindakan_namalainnya'];
            $newValue[\Yii::t('app', 'Nama Kategori')] = $value['kategoritindakan']['kategoritindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Kelompok')] = $value['kelompoktindakan']['kelompoktindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Kegiatan')] = $value['jeniskegiatantindakan']['jeniskegiatantindakan_nama'];
            $newValue[\Yii::t('app', 'Group INA CBGS')] = ($value['groupinacbg']) ? $value['groupinacbg']['groupinacbg_nama'] : '';
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif';
            $newValue[\Yii::t('app', 'Catatan')] = $value['catatan'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], [], [], true);
        $filePath->save('php://output');
        die;
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk mengganti data di table
     * @attribute #tanggal# => tanggal sekarang
     */
    public function actionExportPdf()
    {
        ini_set('memory_limit', '-1'); // set memori lebih banyak untuk kebutuhan cetak data yang banyak
        ini_set("pcre.backtrack_limit", "500000000"); // menambah large code size untuk mpdf, default limit = 1000000
        ini_set('max_execution_time', '600'); // set maks process execute
        set_time_limit(600);
        $request = Yii::$app->request;
        $title = 'Data Master Tindakan';
        $get = $request->get();
        $model = new DaftarTindakan;
        $query = $model::find()->joinWith(['jeniskegiatantindakan', 'kategoritindakan', 'kelompoktindakan', 'groupinacbg']);

        if (!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            if (isset($advancedFilter['daftartindakan_kode'])) {
                $query->andWhere(['daftartindakan_kode' => $advancedFilter['daftartindakan_kode']]);
            }
            if (isset($advancedFilter['kategori_tindakan'])) {
                if (is_numeric($advancedFilter['kategori_tindakan'])) {
                    $query->andWhere(['daftartindakan_m.kategoritindakan_id' => $advancedFilter['kategori_tindakan']]);
                } else {
                    $query->andWhere(['ILIKE', 'kategoritindakan_m.kategoritindakan_nama', $advancedFilter['kategoritindakan']]);
                }
            }
            if (isset($advancedFilter['kategoritindakan_nama'])) {
                $query->andWhere([
                    'ILIKE', 'kategoritindakan_m.kategoritindakan_nama',
                    $advancedFilter['kategoritindakan_nama']
                ]);
            }
            if (isset($advancedFilter['jeniskegiatantindakan_nama'])) {
                $query->andWhere([
                    'ILIKE', 'jeniskegiatantindakan_m.jeniskegiatantindakan_nama',
                    $advancedFilter['jeniskegiatantindakan_nama']
                ]);
            }
            if (isset($advancedFilter['kelompoktindakan_nama'])) {
                $query->andWhere([
                    'ILIKE', 'kelompoktindakan_m.kelompoktindakan_nama',
                    $advancedFilter['kelompoktindakan_nama']
                ]);
            }
            if (isset($advancedFilter['groupinacbg_nama'])) {
                $query->andWhere([
                    'ILIKE', 'groupinacbg_m.groupinacbg_nama',
                    $advancedFilter['groupinacbg_nama']
                ]);
            }
            if (isset($advancedFilter['jenis_kegiatan'])) {
                if (is_numeric($advancedFilter['jenis_kegiatan'])) {
                    $query->andWhere(['daftartindakan_m.jeniskegiatantindakan_id' => $advancedFilter['jenis_kegiatan']]);
                } else {
                    $query->andWhere(['ILIKE', 'jeniskegiatantindakan_m.jeniskegiatantindakan_nama', $advancedFilter['jenis_kegiatan']]);
                }
            }
            if (isset($advancedFilter['kelompok_tindakan'])) {
                if (is_numeric($advancedFilter['kelompok_tindakan'])) {
                    $query->andWhere(['daftartindakan_m.kelompoktindakan_id' => $advancedFilter['kelompok_tindakan']]);
                } else {
                    $query->andWhere(['ILIKE', 'kelompoktindakan_m.kelompoktindakan_nama', $advancedFilter['kelompok_tindakan']]);
                }
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y'),
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $dataProvider->getModels(),
                'title' => $title,
            ]),
        ];

        $print->Output();
    }

    public function actionDataKategori()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = KategoriTindakan::find();
        if (!empty($post['term'])) {
            $term = strtoupper($post['term']);
            $result->where(['ILIKE', 'kategoritindakan_nama', $term]);
        }

        return $result->asArray()->all();
    }

    public function actionDataKelompok()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = KelompokTindakan::find();
        if (!empty($post['term'])) {
            $term = strtoupper($post['term']);
            $result->where(['ILIKE', 'kelompoktindakan_nama', $term]);
        }

        return $result->asArray()->all();
    }

    public function actionDataKegiatan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = JenisKegiatanTindakan::find();
        if (!empty($post['term'])) {
            $term = strtoupper($post['term']);
            $result->where(['ILIKE', 'jeniskegiatantindakan_nama', $term]);
        }

        return $result->asArray()->all();
    }

    public function actionDataCbg()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = GroupInaCbg::find();
        if (!empty($post['term'])) {
            $term = strtoupper($post['term']);
            $result->where(['ILIKE', 'groupinacbg_nama', $term]);
        }

        return $result->asArray()->all();
    }

    public function actionGetTindakanMaping()
    {
        $request = Yii::$app->request;
        $column_id = $request->get('column_id');
        $term = $request->get('q');
        $model = new DaftarTindakan;
        $query = $model->find();
        $query->where(['is_active' => true]);
        $query->andWhere(['IS', $column_id, NULL]);

        // return $query->all();
        if (!is_null($term)) {
            $query->andWhere(['ILIKE', 'daftartindakan_nama', $term]);
        }

        // return $query->all();
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetObatMaping()
    {
        $request = Yii::$app->request;
        $term = $request->get('q');
        $model = new ObatAlkes;
        $query = $model->find();
        $query->where(['is_active' => true]);
        $query->andWhere(['IS', 'groupinacbg_id', NULL]);
        // return $query->all();
        if (!is_null($term)) {
            $query->andWhere(['ILIKE', 'LOWER(obatalkes_nama)', strtolower($term)]);
        }

        // return $query->all();
        return $query->asArray()->all();
    }

    public function actionTindakanBmhp()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = TindakanBmhpView::find();
        if (!empty($post['term'])) {
            $term = strtoupper($post['term']);
            $result->where(['ILIKE', 'daftartindakan_nama', $term]);
        }

        return $result->asArray()->all();
    }

    /**
     * @todo Fungsi untuk melakukan pengecekan transaksi tindakan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCekTransaksiTindakan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $count = TindakanPelayanan::find()->where(['daftartindakan_id' => $id])->count();

        return $count;
    }

    public function actionListDaftarTindakanNama()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = DaftarTindakan::find();
        $result->select(['daftartindakan_id', 'daftartindakan_nama', 'daftartindakan_kode', 'is_active']);
        $result->where(['is_active' => true]);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->andWhere(['ILIKE', 'LOWER(daftartindakan_nama)', $term]);
            $result->orWhere(['ILIKE', 'LOWER(daftartindakan_kode)', $term]);
        }
        return $result->asArray()->all();
    }
}
