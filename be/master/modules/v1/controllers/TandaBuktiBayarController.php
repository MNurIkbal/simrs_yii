<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\TandaBuktiBayar;

class TandaBuktiBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TandaBuktiBayar';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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

    public function actionIndex($unpaid = false)
    {
        
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'ruangan_m,shift_m,pegawai_m,pembayaranpelayanan_t,pendaftaran_t,pasien_m');
        return $request->get();

        $model = new TandaBuktiBayar;
        $query = $model::find()
            ->joinWith(['ruangan' => function($query){
                $query->from('ruangan_m');
            }])
            ->joinWith(['shift' => function($query){
                $query->from('shift_m');
            }])
            ->joinWith(['pegawai' => function($query){
                $query->from('pegawai_m');
            }])
            ->joinWith(['pembayaranPelayanan' => function($query){
                $query->from('pembayaranpelayanan_t');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        if ($unpaid) {
            $query->where(['closingkasir_id' => null]);
            // $query->where(['pembatalanuangmuka_id' => null]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}