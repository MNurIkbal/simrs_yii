<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\GolonganUmurLab;
use app\modules\v1\models\NilaiRujukan;

class GolonganUmurLabController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\GolonganUmurLab';

    public function verbs()
    {
        $verbs = parent::verbs();
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
            // $request = Yii::$app->request;
            $model = new GolonganUmurLab;
            $query = $model::find();

            // if(isset($request->get('advanced-filter'))) {
            //     return $request->get('advanced-filter');
            // }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby([
                            'gol_umurlab_minimal' => SORT_ASC
                            ]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionConvertToDay($totalHari)
    {
        return DocoHelpers::convertToDay($totalHari);
    }

    public function actionView($id)
    {
        $model = new GolonganUmurLab;
        $query = $model::find()->where(['golonganumurlab_id' => $id])->asArray()->one();

        $totalHariMaks = $query['gol_umurlab_maksimal'];
        $totalHariMin = $query['gol_umurlab_minimal'];
        $convertHariMaks = DocoHelpers::convertToDay($totalHariMaks);
        $convertHariMin = DocoHelpers::convertToDay($totalHariMin);

        return [
            'query' => $query,
            'totalHariMaks' => $convertHariMaks,
            'totalHariMin' => $convertHariMin
        ];
    }

    public function actionUbahStatus()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $status = $request->get('is_active');
        try {
            $request = Yii::$app->request;
            $model = GolonganUmurLab::findOne($id);
            $checkAmbulan = $this->checkNilaiRujukan($id);
            if($checkAmbulan > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Nama Golongan '.'<strong>'.$model->gol_umurlab_nama.'</strong>'.' sedang dipakai di master lain, Status tidak bisa diubah.',
                            'status' => 422
                       ];
            }else{
                $model->is_active = $status;
                if ($model->update()) {
                    return $response['response'] = [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Status berhasil diubah',
                       ];
                } else {
                    return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Status gagal di ubah',
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

    private function checkNilaiRujukan($id){
        try {
            $model = new NilaiRujukan;
            $data = $model::find()
                            ->where(['golonganumur_id'=>$id])
                            ->count();
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }
}