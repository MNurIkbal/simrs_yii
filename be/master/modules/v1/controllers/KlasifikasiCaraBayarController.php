<?php
// Author: Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\CaraBayarView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PenjaminDiskonView;
use app\modules\v1\models\PenjaminView;
use yii\helpers\ArrayHelper;

use yii\web\HttpException;

class KlasifikasiCaraBayarController extends DocoActiveController
{
    public $modelClass = '';
    const SINGKATAN_BPJS = 'BPJS';

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
        return $actions;
    }

    private function requestFilterCaraBayar($request, $query){
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){

            if (isset($advancedFilters['carabayar_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($advancedFilters['carabayar_nama']) ]);
            }

            if (isset($advancedFilters['carabayar_namalainnya']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_namalainnya)', strtolower($advancedFilters['carabayar_namalainnya']) ]);
            }

            /*if (isset($advancedFilters['metode_pembayaran_nama']) ) {
                $query->andWhere(['metode_pembayaran' => $advancedFilters['metode_pembayaran_nama'] ]);
            }*/

            /*if (isset($advancedFilters['metode_pembayaran_nama']) ) {
                $query->andWhere(['LIKE', 'metode_pembayaran_nama', $advancedFilters['metode_pembayaran_nama'] ]);
            }*/

            if (isset($advancedFilters['carabayar_loket']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_loket)', strtolower($advancedFilters['carabayar_loket']) ]);
            }
            if (isset($advancedFilters['carabayar_singkatan']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_singkatan)', strtolower($advancedFilters['carabayar_singkatan']) ]);
            }

            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
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
        // $query->orderby(['carabayar_nama'=> SORT_ASC]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionAllowGetCarabayarBpjs()
    {
        $model = new CaraBayar;
        $query = $model::find()->where(['carabayar_singkatan'=>self::SINGKATAN_BPJS])->one();
        return $query;
    }

    public function actionListCaraBayar($default='1') {
        $data = CaraBayar::find()->where(['is_active' => 't'])->orderBy('carabayar_id');
        if ($default=="1") {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'carabayar_nama');
        } else {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'namaAndSingkatan');
        }

        return $items;
    }

    public function actionLookupMetodePembayaran($param = 'metode_bayar')
    {
        $data = Lookup::find();

        if($param) {
            $data->where(['lookup_type' => $param]);
        }

        $items = ArrayHelper::map($data->all(), 'lookup_name', 'lookup_name');

        return $items;
    }

    private function requestFilterPenjamin($request, $query){
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (isset($advancedFilters['carabayar_id']) ) {
                $query->andWhere(['carabayar_id' => (int)$advancedFilters['carabayar_id'] ]);
            }

            if (isset($advancedFilters['carabayar_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($advancedFilters['carabayar_nama']) ]);
            }

            if (isset($advancedFilters['penjamin_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_nama)', strtolower($advancedFilters['penjamin_nama']) ]);
            }
            if (isset($advancedFilters['metode_pembayaran']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(metode_pembayaran)', strtolower($advancedFilters['metode_pembayaran']) ]);
            }
            if (isset($advancedFilters['penjamin_namalainnya']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_namalainnya)', strtolower($advancedFilters['penjamin_namalainnya']) ]);
            }

            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
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
            // echo "<pre>";var_dump($query->all());die();
        // $query->orderby(['carabayar_nama'=> SORT_ASC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetPenjaminDiskon()
    {
        $request = Yii::$app->request;
        $model = new PenjaminDiskonView;
        $query = $model::find();

        $getQueryFilter = $this->requestFilterPenjaminDiskon($request, $query);
        $query = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
        
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function requestFilterPenjaminDiskon($request, $query){
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (isset($advancedFilters['carabayar_id']) ) {
                $query->andWhere(['carabayar_id' => (int)$advancedFilters['carabayar_id'] ]);
            }
            
            if (isset($advancedFilters['penjamin_kode']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_kode)', strtolower($advancedFilters['penjamin_kode']) ]);
            }

            if (isset($advancedFilters['penjamin_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_nama)', strtolower($advancedFilters['penjamin_nama']) ]);
            }

            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
            }
        }

        return $query;
    }
}