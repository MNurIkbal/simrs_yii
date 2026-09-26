<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 17:26:59
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\Makanan;

class MakananController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\Makanan';

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
     * @todo Action untuk create makanan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $model = new Makanan;
            $post = Yii::$app->request->post();
            $return = [];

            if (!$model->load($post, '')) {
                $errors = DocoHelpers::parseError($model->errors, 'MakananForm');
                $return = [
                    'status' => 422,
                    'data' => $errors,
                    'message' => $errors,
                ];

                $transaction->rollBack();
            }

            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, 'MakananForm');
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
            $model = Makanan::findOne($id);

            if ($model != '') {
                if ($model->is_active == true) {
                    $model->is_active = false;
                } else {
                    $model->is_active = true;
                }
            }

            if (!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, 'MakananForm');
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
    * @controller actionExportPdf
    * @attribute #table# => table
    **/
    public function actionExportPdf()
    {
        try {
            $model = new Makanan;
            $query = $model::find()
            ->select([
                'makanandiet_kode',
                'makanandiet_nama',
                'makanandiet_keterangan',
                'is_active',
            ]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            $header = array(
                'title' => Yii::t('app', "Asal Rujukan"),
            );

            $print = new DocoPrint();
            $print->attributes = [
                '#table#' => $this->renderPartial('pdf', [
                    'header' => $header,
                    'model' => $query,
                ]),
            ];
            $print->Output();
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
            $model = new Makanan;
            $query = $model::find();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            $header = [];
            $result = [];
            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    $newValue = [];
                    $newValue[\Yii::t('app', 'Kode')] = $value['makanandiet_kode'];
                    $newValue[\Yii::t('app', 'Nama Makanan')] = $value['makanandiet_nama'];
                    $newValue[\Yii::t('app', 'Keterangan')] = $value['makanandiet_keterangan'];
                    $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
                    $result[$key] = $newValue;
                }
            }

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Kode' => 'Tanggal Unduh : ' . date('d M Y'),
                ]
            ];

            $filePath = DocoHelpers::exportExcel(Yii::t('app', 'Makanan'), $result, $header, [],$footer,[],true);
            $filePath->save('php://output');
            die;
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
    public function actionGetDataMakanan()
    {
        try {
            $model = new Makanan();

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