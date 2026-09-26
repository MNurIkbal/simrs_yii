<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 09:38:55
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\JenisDiet;

class JenisDietController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\JenisDiet';

    /**
     * @todo Verbs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /**
     * @todo Actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);

        return $actions;
    }

    /**
     * @todo Action untuk create jenis diet
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $model = new JenisDiet;
            $post = Yii::$app->request->post();
            $return = [];

            if (!$model->load($post, '')) {
                $errors = DocoHelpers::parseError($model->errors, 'JenisDietForm');
                $return = [
                    'status' => 422,
                    'data' => $errors,
                    'message' => $errors,
                ];

                $transaction->rollBack();
            }

            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, 'JenisDietForm');
                $return = [
                    'status' => 422,
                    'data' => $errors,
                    'message' => $errors,
                ];

                $transaction->rollBack();
            }

            if ($model->save()) {
                $transaction->commit();
                \Yii::$app->response->statusCode = 200;
                $return = [
                    'status' => 200,
                    'data' => Yii::t('app', 'Data Berhasil disimpan'),
                    'message' => Yii::t('app', 'Data Berhasil disimpan'),
                ];
            }

            return $return;
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk update status
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUpdateStatus()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $id = Yii::$app->request->post('id');
            $model = JenisDiet::findOne($id);

            if ($model != '') {
                if ($model->is_active == true) {
                    $model->is_active = false;
                } else {
                    $model->is_active = true;
                }
            }

            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, 'JenisDietForm');
                $return = [
                    'status' => 422,
                    'data' => $errors,
                    'message' => $errors,
                ];

                $transaction->rollBack();
            }

            if ($model->save()) {
                $transaction->commit();
                \Yii::$app->response->statusCode = 200;
                $return = [
                    'status' => 200,
                    'data' => Yii::t('app', 'Data Berhasil disimpan'),
                    'message' => Yii::t('app', 'Data Berhasil disimpan'),
                ];
            }

            return $return;
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisDiet;
            $query = $model::find();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            $header = [];
            $result = [];
            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    $newValue = [];
                    $newValue[\Yii::t('app', 'Kode')] = $value['jenisdiet_kode'];
                    $newValue[\Yii::t('app', 'Nama Jenis Diet')] = $value['jenisdiet_nama'];
                    $newValue[\Yii::t('app', 'Keterangan')] = $value['jenisdiet_keterangan'];
                    $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
                    $result[$key] = $newValue;
                }
            }

            $filePath = DocoHelpers::exportExcel(Yii::t('app', 'Jenis Diet'), $result, $header, array(
                "uploadPath" => "./uploads",
            ));
            
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk mendapatkan data jenis diet
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataJenisDiet()
    {
        try {
            $model = new JenisDiet();

            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }
}