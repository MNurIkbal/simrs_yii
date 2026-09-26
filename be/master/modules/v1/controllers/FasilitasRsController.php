<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-17 11:23:59
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\FasilitasRs;
use app\modules\v1\models\FasilitasRsDetail;
use app\modules\v1\models\FasilitasRsView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class FasilitasRsController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\FasilitasRs';

    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);

        return $actions;
    }

    /**
     * @todo Behaviors untuk skip authenticator di beberapa actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['api'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['api'],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new FasilitasRsView;

            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $is_mobile = Yii::$app->jwt->is_mobile;
            if ($is_mobile) {
                return $query->all();
            } else {
                return new ActiveDataProvider([
                    'query' => $query,
                ]);
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

    public function actionSimpanFasilitasRs()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $model = new FasilitasRs;
            $post = Yii::$app->request->post();
            $data_fasilitasdetail = [];

            $fasilitas = $post['FasilitasRs'];
            $fasilitasDetail = $post['FasilitasRsDetail'];

            $model->attributes = $fasilitas;

            if ($model->validate()) {
                if ($model->save(false)) {
                    if (!empty($fasilitasDetail['nama_fasilitas'])) {
                        foreach ($fasilitasDetail['nama_fasilitas'] as $key => $value) {
                            $data_fasilitasdetail[$key]['fasilitasrs_id'] = $model->fasilitasrs_id;
                            $data_fasilitasdetail[$key]['nama_fasilitas'] = $value;
                        }

                        FasilitasRsDetail::batchInsert($data_fasilitasdetail);
                        $transaction->commit();
                        $return = [
                            'data' => 'Data Berhasil disimpan',
                            'message' => 'Data Berhasil disimpan',
                        ];
                    } else {
                        $transaction->rollBack();
                        \Yii::$app->response->statusCode = 500;
                        $return = [
                            'title' => Yii::t('app', 'Proses Gagal'),
                            'message' => Yii::t('app', 'Data gagal disimpan, nama fasilitas tidak boleh kosong!')
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    \Yii::$app->response->statusCode = 500;
                    $return = [
                        'title' => Yii::t('app', 'Proses Gagal'),
                        'message' => Yii::t('app', 'Data gagal disimpan!')
                    ];
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'FasilitasRsForm');
                $return = [
                    'data' => $errors,
                    'message' => $errors,
                    'status' => 422
                ];

                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUbahFasilitasRs()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $post = Yii::$app->request->post();
            $fasilitas = $post['FasilitasRs'];
            $fasilitasDetail = $post['FasilitasRsDetail'];
            $lastModelDetail = [];
            $data_fasilitasdetail = [];
            $model = FasilitasRs::findOne($fasilitas['fasilitasrs_id']);
            
            if ($model) {
                $lastModelDetail = FasilitasRsDetail::find()->where(['fasilitasrs_id' => $model->fasilitasrs_id])->all();
            }

            if ($model->load($fasilitas, '')) {
                if ($model->validate()) {
                    if ($model->save(false)) {
                        if (!empty($fasilitasDetail['nama_fasilitas'])) {
                            foreach ($fasilitasDetail['nama_fasilitas'] as $key => $value) {
                                $data_fasilitasdetail[$key]['fasilitasrs_id'] = $model->fasilitasrs_id;
                                $data_fasilitasdetail[$key]['nama_fasilitas'] = $value;
                            }

                            (new FasilitasRsDetail)->delete([
                                'fasilitasrs_id' => $fasilitas['fasilitasrs_id']
                            ]);

                            FasilitasRsDetail::batchInsert($data_fasilitasdetail);
                            $transaction->commit();
                            $return = [
                                'data' => 'Data Berhasil disimpan',
                                'message' => 'Data Berhasil disimpan'
                            ];
                        } else {
                            $transaction->rollBack();
                            \Yii::$app->response->statusCode = 500;
                            $return = [
                                'title' => Yii::t('app', 'Proses Gagal'),
                                'message' => Yii::t('app', 'Data gagal disimpan, nama fasilitas tidak boleh kosong!')
                            ];
                        }
                    } else {
                        $transaction->rollBack();
                        \Yii::$app->response->statusCode = 500;
                        $return = [
                            'title' => Yii::t('app', 'Proses Gagal'),
                            'message' => Yii::t('app', 'Data gagal disimpan!')
                        ];
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'FasilitasRsForm');
                    $return = [
                        'data' => $errors,
                        'message' => $errors,
                        'status' => 422
                    ];

                    $transaction->rollBack();
                }
            } else {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                $return = [
                    'title' => Yii::t('app', 'Proses Gagal'),
                    'message' => Yii::t('app', 'Data gagal disimpan!')
                ];
            }
            
            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDelete($id)
    {
        try {
            $model = FasilitasRs::findOne($id);

            if ($model->delete($id)) {
                return [
                    'message' => Yii::t('app', 'Data berhasil dihapus')
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetFasilitasRs($id)
    {
        try {
            $model = FasilitasRs::findOne($id);
            $jenis_fasilitas = Instalasi::find()->all();
            $modelDetail = [];

            if ($model) {
                $modelDetail = FasilitasRsDetail::find()->where(['fasilitasrs_id' => $model->fasilitasrs_id])->all();
            }

            return [
                'model' => $model,
                'modelDetail' => $modelDetail,
                'jenis_fasilitas' => $jenis_fasilitas,
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

    public function actionGetJenisFasilitas()
    {
        try {
            $model = Instalasi::find()->all();
            $jenisFasilitasLainnya = FasilitasRsView::find()->where(['jenis_fasilitas' => null])->all();

            return [
                'jenis_fasilitas' => $model,
                'jenis_fasilitas_lainnya' => $jenisFasilitasLainnya
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

    public function actionGetNamaFasilitas($jenis_fasilitas)
    {
        try {
            $model = Ruangan::find()->select([
                'ruangan_id',
                'ruangan_nama',
            ])->where(['instalasi_id' => $jenis_fasilitas])->all();

            return $model;
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

    public function actionApi($id = null)
    {
        try {
            $detail = [];

            if ($id) {
                $model = FasilitasRs::find()->where(['fasilitasrs_id' => $id])->asArray()->one();

                if ($model) {
                    $detail = FasilitasRsDetail::find()->where(['fasilitasrs_id' => $model['fasilitasrs_id']])->asArray()->all();
                } else {
                    $detail = [];
                }

                if (!empty($detail)) {
                    foreach ($detail as $key => $value) {
                        $model['detail'][] = $value;
                    }
                }
            } else {
                $model = FasilitasRs::find()->asArray()->all();

                if (!empty($model)) {
                    foreach ($model as $key => $value) {
                        if ($model[$key]['fasilitasrs_id'] != '') {
                            $detail[$key] = FasilitasRsDetail::find()->where(['fasilitasrs_id' => $model[$key]['fasilitasrs_id']])->asArray()->all();
                        } else {
                            $detail[$key] = [];
                        }
                    }
                }

                if (!empty($detail)) {
                    foreach ($detail as $key => $value) {
                        $model[$key]['detail'] = $value;
                    }
                }
            }

            return $model;
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
}
