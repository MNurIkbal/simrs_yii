<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PenjaminView;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;
use Doco\components\DocoConstants;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\GroupMargin;
use app\modules\v1\models\KonfigAsuransi;

class PenjaminController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Penjamin';

    public $messageBroker = [
        'update-penjamin' => [
            'services' => [
                'Odoo' => [
                    'Penjamin' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-list-penjamin"] = ["GET"];
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
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'carabayar_m');

        $model = new Penjamin;
        $query = $model::find()
            ->joinWith(['caraBayar' => function ($query) {
                $query->from('carabayar_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreatePenjamin()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new Penjamin;
            $post = $request->post();
            $saving = false;
            if (empty($post)) {
                $result = [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Data Tidak Di Temukan'
                ];
                return $result;
            } else {
                $saveData = false;
                foreach ($post['penjamin'] as $key => $value) {
                    $model->attributes = $value;
                    if (!$model->validate()) {
                        $transaction->rollBack();
                        $errors = DocoHelpers::parseError($model->errors, 'PenjaminForm');
                        $result = [
                            'data' => $errors,
                            'status' => 422
                        ];
                        return $result;
                    } else {
                        $saveData = true;
                    }
                }

                if ($saveData && $model->validate()) {
                    Penjamin::batchInsert($post['penjamin']);
                    $transaction->commit();
                    (new InternalService)->sendTo([
                        'Odoo' => [
                            'Penjamin' => [
                                'last_insert' => true,
                                'count' => count($post['penjamin'])
                            ]
                        ]
                    ]);
                    $this->unsetCache(DocoConstants::CACHE_PENJAMIN);
                    Yii::$app->cache->delete(DocoConstants::PENDAFTARAN_PENJAMIN);
                    $result = [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Tersimpan'
                    ];
                    // return $result;
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($model->errors, 'PenjaminForm');
                    $result = [
                        'data' => $errors,
                        'status' => 422
                    ];
                    // $result['status'] = 422;
                    // $result['data'] = $model->errors;
                }
                return $result;
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

    public function actionUpdatePenjamin($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Penjamin::findOne($id);
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $post = $request->post();
            $model->attributes = $post;

            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, 'PenjaminForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            } else {

                if ($model->save()) {
                    $transaction->commit();
                    $this->unsetCache(DocoConstants::CACHE_PENJAMIN);
                    Yii::$app->cache->delete(DocoConstants::PENDAFTARAN_PENJAMIN);
                    $result = [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil diubah'
                    ];
                    return $result;
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PenjaminForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }

                /** 
                 * Pelepasan validasi update agar bisa edit penjamin
                 * 08-12-2021 
                 */

                // $cekPendaftaran = $connection->createCommand('select * from pendaftaran_t where penjamin_id=:id')
                // ->bindValue(':id', $id)
                // ->queryOne();

                // $cekAdmisi = $connection->createCommand('select * from pasienadmisi_t where penjamin_id=:id')
                //     ->bindValue(':id', $id)
                //     ->queryOne();

                // $cekPembayaran = $connection->createCommand('select * from pembayaranpelayanan_t where penjamin_id=:id')
                //     ->bindValue(':id', $id)
                //     ->queryOne();

                // $cekTindakanPelayanan = $connection->createCommand('select * from tindakanpelayanan_t where penjamin_id=:id')
                //     ->bindValue(':id', $id)
                //     ->queryOne();

                // if($cekPendaftaran) {
                //     $result['title'] = 'Proses Gagal';
                //     $result['status'] = 422;
                //     $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                // }
                // elseif($cekAdmisi) {
                //     $result['title'] = 'Proses Gagal';
                //     $result['status'] = 422;
                //     $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                // }
                // elseif($cekPembayaran) {
                //     $result['title'] = 'Proses Gagal';
                //     $result['status'] = 422;
                //     $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                // }
                // elseif($cekTindakanPelayanan) {
                //     $result['title'] = 'Proses Gagal';
                //     $result['status'] = 422;
                //     $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                // }
                // else {
                //     if ($model->save()) {
                //         $transaction->commit();
                //         $this->unsetCache(DocoConstants::CACHE_PENJAMIN);
                //         $result = [
                //                 'status' => 200,
                //                 'title' => 'Proses Berhasil',
                //                 'text' => 'Data Berhasil diubah'
                //             ];                        
                //         return $result;
                //     } else {
                //         $errors = DocoHelpers::parseError($model->errors,'PenjaminForm');
                //         return [
                //             'data' => $errors,
                //             'status' => 422
                //         ];
                //     }
                // }
                /** */
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeletePenjamin()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = Penjamin::findOne($id);
            $cekPendaftaran = $connection->createCommand('select * from pendaftaran_t where penjamin_id=:id')
                ->bindValue(':id', $id)
                ->queryOne();

            $cekAdmisi = $connection->createCommand('select * from pasienadmisi_t where penjamin_id=:id')
                ->bindValue(':id', $id)
                ->queryOne();

            $cekPembayaran = $connection->createCommand('select * from pembayaranpelayanan_t where penjamin_id=:id')
                ->bindValue(':id', $id)
                ->queryOne();

            $cekTindakanPelayanan = $connection->createCommand('select * from tindakanpelayanan_t where penjamin_id=:id')
                ->bindValue(':id', $id)
                ->queryOne();

            if ($cekPendaftaran) {
                $result['title'] = 'Proses Gagal';
                $result['status'] = 422;
                $result['text'] = "Tidak bisa menghapus, data sudah digunakan di transaksi.";
            } elseif ($cekAdmisi) {
                $result['title'] = 'Proses Gagal';
                $result['status'] = 422;
                $result['text'] = "Tidak bisa menghapus, data sudah digunakan di transaksi.";
            } elseif ($cekPembayaran) {
                $result['title'] = 'Proses Gagal';
                $result['status'] = 422;
                $result['text'] = "Tidak bisa menghapus, data sudah digunakan di transaksi.";
            } elseif ($cekTindakanPelayanan) {
                $result['title'] = 'Proses Gagal';
                $result['status'] = 422;
                $result['text'] = "Tidak bisa menghapus, data sudah digunakan di transaksi.";
            } else {
                if ($model->delete()) {
                    $transaction->commit();
                    Yii::$app->cache->delete(DocoConstants::PENDAFTARAN_PENJAMIN);
                    $this->unsetCache(DocoConstants::CACHE_PENJAMIN);
                    $result['title'] = 'Proses Berhasil';
                    $result['text'] = 'Data berhasil dihapus';
                } else {
                    $transaction->rollBack();
                    $result['title'] = 'Proses Gagal';
                    $result['text'] = 'Data Gagal dihapus';
                    $result['status'] = 422;
                }
            }

            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @author Rizal
     * @since 2018-01-24 14:01:16
     * @param int instalasi_id
     * @return array list of ruangan
     * @desc needs for depdrop or dropdown
     */
    public function actionListPenjamin($carabayar_id = null)
    {
        $data = Penjamin::find();
        if ($carabayar_id) {
            $data->where(
                [
                    'is_active' => 't',
                    'carabayar_id' => $carabayar_id
                ]
            );
        }
        $data->orderBy('penjamin_nama');

        $items = ArrayHelper::map($data->all(), 'penjamin_id', 'penjamin_nama');
        return $items;
    }

    private function requestFilterPenjamin($request, $query)
    {
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters)) {
            if (isset($advancedFilters['carabayar_id'])) {
                $query->andWhere(['carabayar_id' => (int)$advancedFilters['carabayar_id']]);
            }

            if (isset($advancedFilters['carabayar_nama'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($advancedFilters['carabayar_nama'])]);
            }

            if (isset($advancedFilters['penjamin_nama'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_nama)', strtolower($advancedFilters['penjamin_nama'])]);
            }
            if (isset($advancedFilters['metode_pembayaran'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(metode_pembayaran)', strtolower($advancedFilters['metode_pembayaran'])]);
            }
            if (isset($advancedFilters['penjamin_namalainnya'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_namalainnya)', strtolower($advancedFilters['penjamin_namalainnya'])]);
            }

            if (isset($advancedFilters['is_active'])) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active']]);
            }
        }

        return $query;
    }

    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        $model = new PenjaminView;
        $query = $model::find();

        $getQueryFilter = $this->requestFilterPenjamin($request, $query);

        $query = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
        $query->orderby(['carabayar_nama' => SORT_ASC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk menampilkan data table
     */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Penjamin Pasien';
            $get = $request->get();
            $model = new PenjaminView;
            $query = $model::find();

            $getQueryFilter = $this->requestFilterPenjamin($request, $query);

            $query = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
            $query->orderby(['carabayar_nama' => SORT_ASC]);
            $data = $query->asArray()->all();

            $result = [];
            $no = 0;
            foreach ($data as $key => $value) {
                $no++;
                $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif';
                $value['rowNum'] = $no;
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
            $title = 'Master Penjamin Pasien';
            $get = $request->get();
            $model = new PenjaminView;
            $query = $model::find();

            $getQueryFilter = $this->requestFilterPenjamin($request, $query);

            $query = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
            $query->orderby(['carabayar_nama' => SORT_ASC]);
            $resData = $query->asArray()->all();

            $no = 0;
            foreach ($resData as $key => $value) {
                $no++;
                $data[\Yii::t('app', 'Cara Bayar')] = $value['carabayar_nama'];
                $data[\Yii::t('app', 'Nama Penjamin')] = $value['penjamin_nama'];
                $data[\Yii::t('app', 'Nama Lainnya')] = $value['penjamin_namalainnya'];
                $data[\Yii::t('app', 'Status')] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif';
                $data[\Yii::t('app', 'Tampilkan di Mobile')] = ($value['is_online']) ? 'Ya' : 'Tidak';
                $result[] = $data;
            }

            $header = [];
            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], [], [], true);
            $filePath->save('php://output');
            die;

            // 


            // return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));

        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateOnline($id, $is_online)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = Penjamin::findOne($id);

            if ($model && !empty($model)) {
                $model->is_online = $is_online;
                if ($model->validate() && $model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
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

    public function actionGetListPenjamin()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);

        // Penjamin
        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find()->where(['is_active' => true])
            ->select(['penjamin_id', 'penjamin_nama', 'penjamin_kode', 'penjamin_namalainnya']);

        if (!empty($term)) {
            $queryPenjamin->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
        }
        $queryPenjamin->orderBy(['penjamin_nama' => SORT_ASC]);

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $queryPenjamin->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetListPenjaminDepDrop()
    {
        $request = Yii::$app->request;
        $caraBayarIds = $request->get('carabayar_ids', []);
        $queryPenjamin = Penjamin::find()->where(['is_active' => true])->select(['penjamin_id', 'penjamin_nama', 'penjamin_kode', 'penjamin_namalainnya']);
        if (!empty($caraBayarIds)) $queryPenjamin = $queryPenjamin->andWhere(['in', 'carabayar_id', $caraBayarIds]);
        $queryPenjamin->orderBy(['penjamin_nama' => SORT_ASC]);
        return $queryPenjamin->asArray()->all();
    }

    public function actionGetDataSelect2()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;
        $carabayarId = isset($payload['carabayar_id']) ? $payload['carabayar_id'] : null;

        $result = Penjamin::find()
                ->select(['penjamin_id AS id', 'penjamin_nama AS text'])
                ->where(['is_active' => true]);

        if(!empty($carabayarId)) {
            $result->andWhere(['carabayar_id' => $carabayarId]);
        }

        if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
        }

        $result->orderBy(['penjamin_nama' => SORT_ASC]);

        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }

    private function unsetCache($cacheName){
        return Yii::$app->cache->delete($cacheName);
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $dataPenjamin = Penjamin::findOne($id);
        $dataCaraBayar = CaraBayar::find()->orderBy(['carabayar_nama' => SORT_ASC])->asArray()->all();
        $dataCaraBayar = ArrayHelper::map($dataCaraBayar, 'carabayar_id', 'carabayar_nama');
        $caraBayar = CaraBayar::findOne($dataPenjamin->carabayar_id);
        $dataGroupMargin = GroupMargin::find()->where([
            'is_deleted' => false,
            'is_active' => true
        ])->orderBy(['groupmargin_id' => SORT_ASC])->asArray()->all();
        $dataGroupMargin = ArrayHelper::map($dataGroupMargin, 'groupmargin_id', 'groupmargin_nama');
        $konfigAsuransi = KonfigAsuransi::find()->where(['is_active' => true])->orderBy(['provider_code' => SORT_ASC])->all();
        $konfigAsuransi = ArrayHelper::map($konfigAsuransi, 'konfigasuransi_id', 'provider_code');
        return [
            'penjamin' => $dataPenjamin,
            'cara_bayar' => $dataCaraBayar,
            'group_margin' => $dataGroupMargin,
            'konfig_asuransi' => $konfigAsuransi,
            'groupcarabayar_id' => $caraBayar->groupcarabayar_id,
            'carabayar_nama' => $caraBayar->carabayar_nama,
        ];
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $result = [];
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $result = CaraBayar::find()
            ->select(['carabayar_id as id', 'carabayar_nama as text', 'groupcarabayar_id'])
            ->where(['is_active' => true]);

        if (!empty($term)) {
            $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
        }

        $result->orderBy(['carabayar_nama' => SORT_ASC]);
        $result->limit(($limit + 1))->offset($limit * ($page - 1));
        return $result->asArray()->all();
    }
}
