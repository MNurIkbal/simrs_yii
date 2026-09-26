<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Shift;

class ShiftController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Shift';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new Shift;
        $query = $model::find();
        if (isset($_GET['advanced-filter'])) {
            if (!empty($_GET['advanced-filter']['shift_jamawal']) && !empty($_GET['advanced-filter']['shift_jamakhir'])) {
                $query->andWhere('shift_jamawal >= :shift_jamawal AND shift_jamakhir <= :shift_jamakhir', [
                    ':shift_jamawal' => $_GET['advanced-filter']['shift_jamawal'],
                    ':shift_jamakhir' => $_GET['advanced-filter']['shift_jamakhir']
                ]);
            } elseif (!empty($_GET['advanced-filter']['shift_jamawal'])) {
                $query->andWhere('shift_jamawal >= :shift_jamawal', [':shift_jamawal' => $_GET['advanced-filter']['shift_jamawal']]);
            } elseif (!empty($_GET['advanced-filter']['shift_jamakhir'])) {
                $query->andWhere('shift_jamakhir <= :shift_jamakhir', [':shift_jamakhir' => $_GET['advanced-filter']['shift_jamakhir']]);
            }

            if (!empty($_GET['advanced-filter']['shift_kode'])) {
                $query->andWhere('LOWER(shift_kode) = :shift_kode', [':shift_kode' => strtolower($_GET['advanced-filter']['shift_kode'])]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    // Test Example
    public function actionGetCurrentShiftId()
    {
        return Shift::getCurrentShiftId();
    }

    public function actionSave()
    {
        $model = new Shift;
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model->attributes = $post['ShiftForm'];
            $model->is_active = 1;
            $model->shift_jamawal = date('H:i', strtotime($model->shift_jamawal));
            $model->shift_jamakhir = date('H:i', strtotime($model->shift_jamakhir));

            $cekJamAwal = Shift::find()
            ->andWhere(['<=', 'shift_jamawal', $model->shift_jamawal])
            ->andWhere(['>=', 'shift_jamakhir', $model->shift_jamawal])
            ->all();

            $cekJamAkhir = Shift::find()
            ->andWhere(['<=', 'shift_jamawal', $model->shift_jamakhir])
            ->andWhere(['>=', 'shift_jamakhir', $model->shift_jamakhir])
            ->all();

            if (!empty($cekJamAwal)) {
                return [
                    'status' => 422,
                    'data' => [
                        'shift_jamawal' => [Yii::t('app', 'Jam '.$model->shift_jamawal.' sudah digunakan.')]
                    ]
                ];
            }

            if (!empty($cekJamAkhir)) {
                return [
                    'status' => 422,
                    'data' => [
                        'shift_jamakhir' => [Yii::t('app', 'Jam '.$model->shift_jamakhir.' sudah digunakan.')]
                    ]
                ];
            }

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

    public function actionDelete($id)
    {
        try {
            $result = (new Shift)->delete($id);

            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionViewData($id)
    {
        $model = new Shift;
        $query = $model->find();
        if ($id) {
            $query->andWhere(['shift_id' => $id]);
        }

        return $query->one();
    }

    public function actionUpdate($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = Shift::findOne($id);
            $post = $request->post();
            if ($model && !empty($model)) {
                $model->attributes = $post;
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

    /**
     * @todo Method untuk export excel shift
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $model = new Shift;
            $query = Shift::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $data = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $newData = [];
                $newData['kode_shift'] = $value['shift_kode'];
                $newData['nama_shift'] = $value['shift_nama'];
                $newData['Jam awal'] = date('H:i', strtotime($value['shift_jamawal']));
                $newData['Jam akhir'] = date('H:i', strtotime($value['shift_jamakhir']));
                $newData['status'] = ($value['is_active'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif');
                $data[] = $newData;
            }

            $header = [
                Yii::t('app', 'Kode shift') => isset($_GET['advanced-filter']['shift_kode']) ? $_GET['advanced-filter']['shift_kode'] : '',
                Yii::t('app', 'Nama shift') => isset($_GET['advanced-filter']['shift_nama']) ? $_GET['advanced-filter']['shift_nama'] : '',
                Yii::t('app', 'Jam awal') => isset($_GET['advanced-filter']['shift_jamawal']) ? $_GET['advanced-filter']['shift_jamawal'] : '',
                Yii::t('app', 'Jam akhir') => isset($_GET['advanced-filter']['shift_jamakhir']) ? $_GET['advanced-filter']['shift_jamakhir'] : '',
                Yii::t('app', 'Status') => isset($_GET['advanced-filter']['is_active']) ? ($_GET['advanced-filter']['is_active'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif') : '',
            ];
            
            $filePath = DocoHelpers::exportExcel('Shift', $data, $header, array("uploadPath" => "./uploads"));
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}