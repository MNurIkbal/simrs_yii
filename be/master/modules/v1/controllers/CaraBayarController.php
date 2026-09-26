<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\CaraBayarView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Lookup;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;

use yii\web\HttpException;

class CaraBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\CaraBayar';
    const SINGKATAN_BPJS = 'BPJS';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST", "GET"];
        $verbs["get-list-data-cara-bayar"] = ["GET"];
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
        $model = new CaraBayar;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function requestFilterCaraBayar($request, $query)
    {
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters)) {

            if (isset($advancedFilters['carabayar_nama'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($advancedFilters['carabayar_nama'])]);
            }

            if (isset($advancedFilters['carabayar_namalainnya'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_namalainnya)', strtolower($advancedFilters['carabayar_namalainnya'])]);
            }

            if (isset($advancedFilters['metode_pembayaran_nama'])) {
                $query->andWhere(['metode_pembayaran' => (int)$advancedFilters['metode_pembayaran_nama']]);
            }

            if (isset($advancedFilters['carabayar_loket'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_loket)', strtolower($advancedFilters['carabayar_loket'])]);
            }
            if (isset($advancedFilters['carabayar_singkatan'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_singkatan)', strtolower($advancedFilters['carabayar_singkatan'])]);
            }

            if (isset($advancedFilters['is_active'])) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active']]);
            }
        }

        return $query;
    }

    public function actionGetCaraBayar()
    {
        $request = Yii::$app->request;
        $model = new CaraBayarView;
        $query = $model::find();

        $getQueryFilter = $this->requestFilterCaraBayar($request, $query);

        $query = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
        $query->orderby(['carabayar_nama' => SORT_ASC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetListCaraBayar()
    {

        $data = CaraBayar::find()->where([
            'is_deleted' => false, 'is_active' => true
        ])
            ->orderBy(['carabayar_nama' => SORT_ASC])
            ->asArray()->all();
        $items = ArrayHelper::map($data, 'carabayar_id', 'carabayar_nama');
        return $items;
    }

    public function actionCreateCaraBayar()
    {
        try {
            $request = Yii::$app->request;
            $model = new CaraBayar;
            $post = $request->post();
            $model->attributes = $post;
            $model->groupcarabayar_id = 417;
            if ($model->validate() && $model->save()) {
                Yii::$app->cache->delete(DocoConstants::CACHE_CB);
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => 'Data Berhasil Tersimpan'
                ];
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'CaraBayarForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeleteCaraBayar()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = CaraBayar::findOne($id);
            $modelPenjamin = new Penjamin;
            $getDataPenjamin = $modelPenjamin::find()->where(['carabayar_id' => $id])->count();
            if ($getDataPenjamin > 0) {
                return $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Cara Bayar ini sedang dipakai.',
                    'status' => 422
                ];
            } else {
                if ($model->delete()) {
                    return $response['response'] = [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Data berhasil dihapus'
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

    public function actionUpdateCaraBayar($id)
    {
        try {
            $request = Yii::$app->request;
            $model = CaraBayar::findOne($id);
            if ($request->post() && !empty($model)) {
                $post = $request->post();
                $model->attributes = $post;
                if ($model->validate() && $model->save()) {
                    Yii::$app->cache->delete(DocoConstants::CACHE_CB);
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'CaraBayarForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                $result = [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Data Tidak Di Temukan'
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionAllowGetCarabayarBpjs()
    {
        $model = new CaraBayar;
        $query = $model::find()->where(['carabayar_singkatan' => self::SINGKATAN_BPJS])->one();
        return $query;
    }

    public function actionListCaraBayar($default = '1')
    {
        $data = CaraBayar::find()->where(['is_deleted' => false])->orderBy('carabayar_nama');
        if ($default == "1") {
            $items = ArrayHelper::map($data->all(), 'carabayar_nama', 'carabayar_nama');
        } else {
            $items = ArrayHelper::map($data->all(), 'namaAndSingkatan', 'namaAndSingkatan');
        }

        return $items;
    }

    public function actionLookupMetodePembayaran($param = 'metode_bayar')
    {
        $data = Lookup::find();

        if ($param) {
            $data->where(['lookup_type' => $param]);
        }

        $items = ArrayHelper::map($data->all(), 'lookup_name', 'lookup_name');

        return $items;
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk menampilkan data table
     */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Cara Bayar';
            $get = $request->get();
            $model = new CaraBayarView;
            $query = $model::find();

            $getQueryFilter = $this->requestFilterCaraBayar($request, $query);
            $query = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
            $query->orderby($request->get('order'));
            $data = $query->asArray()->all();

            $result = [];
            $no = 0;
            foreach ($data as $key => $value) {
                $no++;
                $value['is_subsidiasuransi'] = ($value['is_subsidiasuransi']) ? 'Ya' : 'Tidak';
                $value['is_subsidipemerintah'] = ($value['is_subsidipemerintah']) ? 'Ya' : 'Tidak';
                $value['is_subsidirs'] = ($value['is_subsidirs']) ? 'Ya' : 'Tidak';
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
            $header = array();
            $footer = array();
            $title = Yii::t('app', 'Master Cara Bayar');
            $model = new CaraBayarView;
            $query = $model::find()/*->select('carabayar_id,carabayar_nama,carabayar_namalainnya,is_active,is_online')*/;
            $model = DocoRestActiveFilter::advancedFilter($model, $query);
            $model->orderby($request->get('order'));
            $model = $model->asArray()->all();

            if (isset($_GET['advanced-filter'])) {
                $advancedFilters = $_GET['advanced-filter'];

                if (isset($advancedFilters['carabayar_nama'])) {
                    $header[Yii::t('app', 'Nama')] = $advancedFilters['carabayar_nama'];
                }

                if (isset($advancedFilters['carabayar_namalainnya'])) {
                    $header[Yii::t('app', 'Nama Lainnya')] = $advancedFilters['carabayar_namalainnya'];
                }

                if (isset($advancedFilters['is_active'])) {
                    $header[Yii::t('app', 'Status')] = $advancedFilters['is_active'] ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
                }

                if (isset($advancedFilters['is_online'])) {
                    $header[Yii::t('app', 'Tampilkan di Mobile')] = $advancedFilters['is_online'] ? Yii::t('app', 'Ya') : Yii::t('app', 'Tidak');
                }
            }

            $result = [];
            foreach ($model as $key => $value) {
                $newValue = [];
                $newValue[\Yii::t('app', 'Nama')] = $value['carabayar_nama'];
                $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['carabayar_namalainnya'];
                $newValue[\Yii::t('app', 'Metode Pembayaran')] = $value['metode_pembayaran_nama'];
                $newValue[\Yii::t('app', 'Subsidi Asuransi')] = ($value['is_subsidiasuransi'] == true) ? "Ya" : "Tidak";
                $newValue[\Yii::t('app', 'Subsidi Pemerintah')] = ($value['is_subsidipemerintah'] == true) ? "Ya" : "Tidak";
                $newValue[\Yii::t('app', 'Subsidi Rumah Sakit')] = ($value['is_subsidirs'] == true) ? "Ya" : "Tidak";
                $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? "Aktif" : "Tidak Aktif";
                $result[$key] = $newValue;
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateOnline($id, $is_online)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = CaraBayar::findOne($id);

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

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = CaraBayar::findOne($id);
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $post = $request->post();
            $model->attributes = $post;
            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, 'CaraBayarForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            } else {
                $cekPendaftaran = $connection->createCommand('select * from pendaftaran_t where carabayar_id=:id')
                    ->bindValue(':id', $id)
                    ->queryOne();

                $cekAdmisi = $connection->createCommand('select * from pasienadmisi_t where carabayar_id=:id')
                    ->bindValue(':id', $id)
                    ->queryOne();

                $cekPembayaran = $connection->createCommand('select * from pembayaranpelayanan_t where carabayar_id=:id')
                    ->bindValue(':id', $id)
                    ->queryOne();

                $cekTindakanPelayanan = $connection->createCommand('select * from tindakanpelayanan_t where carabayar_id=:id')
                    ->bindValue(':id', $id)
                    ->queryOne();

                if ($cekPendaftaran) {
                    $result['title'] = 'Proses Gagal';
                    $result['status'] = 422;
                    $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                } elseif ($cekAdmisi) {
                    $result['title'] = 'Proses Gagal';
                    $result['status'] = 422;
                    $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                } elseif ($cekPembayaran) {
                    $result['title'] = 'Proses Gagal';
                    $result['status'] = 422;
                    $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                } elseif ($cekTindakanPelayanan) {
                    $result['title'] = 'Proses Gagal';
                    $result['status'] = 422;
                    $result['text'] = "Tidak bisa mengupdate data, data sudah digunakan di transaksi.";
                } else {
                    if ($model->save()) {
                        $transaction->commit();
                        $result = [
                            'status' => 200,
                            'title' => 'Update Data Berhasil',
                            'text' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $transaction->rollBack();
                        $result['status'] = 422;
                        $result['text'] = "Gagal Menyimpan Data";
                        $result['title'] = 'Proses Gagal';
                    }
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

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = CaraBayar::findOne($id);
            $cekPendaftaran = $connection->createCommand('select * from pendaftaran_t where carabayar_id=:id')
                ->bindValue(':id', $id)
                ->queryOne();

            $cekAdmisi = $connection->createCommand('select * from pasienadmisi_t where carabayar_id=:id')
                ->bindValue(':id', $id)
                ->queryOne();

            $cekPembayaran = $connection->createCommand('select * from pembayaranpelayanan_t where carabayar_id=:id')
                ->bindValue(':id', $id)
                ->queryOne();

            $cekTindakanPelayanan = $connection->createCommand('select * from tindakanpelayanan_t where carabayar_id=:id')
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
                    Yii::$app->cache->delete(DocoConstants::CACHE_CB);
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

    public function actionGetListDataCaraBayar()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);

        // CaraBayar
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find()->where(['is_active' => true])
            ->select(['carabayar_id', 'carabayar_nama', 'carabayar_namalainnya']);

        if (!empty($term)) {
            $queryCaraBayar->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
        }
        $queryCaraBayar->orderBy(['carabayar_nama' => SORT_ASC]);

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $queryCaraBayar->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetListCaraBayarDepDrop()
    {
        $datas = CaraBayar::find()->orderBy(['carabayar_nama' => SORT_ASC])->asArray()->all();
        return $datas;
    }

    public function actionGetDataSelect2()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;
        $carabayarId = isset($payload['carabayar_id']) ? $payload['carabayar_id'] : null;

        $result = CaraBayar::find()
            ->select(['carabayar_id AS id', 'carabayar_nama AS text'])
            ->where(['is_active' => true]);

        if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
        }

        $result->orderBy(['carabayar_nama' => SORT_ASC]);

        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }
}
