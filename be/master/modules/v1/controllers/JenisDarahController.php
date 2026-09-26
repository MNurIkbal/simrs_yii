<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\JenisDarah;
use app\modules\v1\models\JenisDarahView;
use app\modules\v1\models\PesanDarahDetail;
use app\modules\v1\models\PesanDarahPmiDetail;
use Doco\components\DocoPrint;

class JenisDarahController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JenisDarah';
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = new JenisDarah;
            $post = $request->post();
            $model->scenario = 'default';
            $model->attributes = $post;
            if($model->validate()){
                if ($model->save()) {
                    $transaction->commit();
                    $return = [
                        'text' => 'Data Berhasil di simpan',
                        'title' => 'Proses berhasil !',
                        'code' => 200
                    ];

                    return $return;
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($model->errors,'JenisDarah');
                    return ['data' => $errors,'status' => 422];
                }
            }else{
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($model->errors, 'JenisDarah');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisDarahView;
            $query = $model->find();
            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['is_active'])) {
                    $is_active = ($advancedFilter['is_active'] == 1) ? true : false;
                    $query->andWhere(['is_active' => $is_active]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new JenisDarahView;
        $query = $model::find();
        $title = 'Jenis Darah';
        $arrayJenisDarah = [];
        $arrayStatus = [];
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['jenisdarah_nama'])) {
                $query->andWhere(['ILIKE', 'jenisdarah_nama', $advancedFilters['jenisdarah_nama']]);
                $arrayJenisDarah = [
                    Yii::t('app', "Jenis Darah") => $advancedFilters['jenisdarah_nama']
                ];
            }
            if(isset($advancedFilters['is_active'])) {
                $is_active = ($advancedFilters['is_active'] == 1) ? true : false;
                $query->andWhere(['is_active' => $is_active]);
                $arrayStatus = [
                    Yii::t('app', "Status") => ($is_active == true) ? "Aktif" : "Tidak Aktif"
                ];
            }
        }

        $additional = (array_merge($arrayJenisDarah, $arrayStatus));
        $header = $additional;
        $result = [];
        $query->orderBy($request->get('order'));
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Jenis Darah')] = $value['jenisdarah_nama'];
            $newValue[\Yii::t('app', 'Lama Penyimpanan (Hari)')] = $value['lama_penyimpanan'];
            $newValue[\Yii::t('app', 'Suhu Penyimpanan (C)')] = $value['suhu_penyimpanan'];
            $newValue[\Yii::t('app', 'Harga')] = $value['harga'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif' ;
            $result[$key] = $newValue;
        }

        $footer = [
            'title' => [
                0 => '',
                1 => '',
            ],
            'data' => [
                'Jenis Darah' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
            ]
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],$footer,[],true);
        $filePath->save('php://output');
        die;
    }

    public function actionDelete($id)
    {
        $checkDarah = PesanDarahDetail::find()->where([
            'jenisdarah_id' => $id
        ])->one();

        $checkPmi = PesanDarahPmiDetail::find()->where([
            'jenisdarah_id' => $id
        ])->one();

        if (empty($checkDarah) && empty($checkPmi)) {
            $delete = (new JenisDarah)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        }
        return [
            'status' => 422,
            'title' => 'Proses Gagal !',
            'text' => 'Data sudah di gunakan'
        ];
    }
}