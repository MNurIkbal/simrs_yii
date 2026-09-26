<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\GolonganOperasi;
use app\modules\v1\models\Operasi;

class GolonganOperasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\GolonganOperasi';

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

    public function actionIndex()
    {
        $model = new GolonganOperasi;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDelete($id)
    {
        $check = Operasi::find()->where([
            'golonganoperasi_id' => $id
        ])->one();

        if (empty($check)) {
            $delete = (new GolonganOperasi)->delete($id);
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