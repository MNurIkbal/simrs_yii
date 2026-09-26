<?php
// Author : Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\CaraMasuk;
use app\modules\v1\models\GolonganUmur;
use app\modules\v1\models\ProfilRumahSakit;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

class IdentitasController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $actions['view-cara-masuk'] = [
            'class' => 'yii\rest\ViewAction',
            'modelClass' => CaraMasuk::className(),
            'checkAccess' => [$this, 'checkAccess'],
        ];
        $actions['update-cara-masuk'] = [
            'class' => 'yii\rest\UpdateAction',
            'modelClass' => CaraMasuk::className(),
            'checkAccess' => [$this, 'checkAccess']
        ];
        $actions['view-gol-umur'] = [
            'class' => 'yii\rest\ViewAction',
            'modelClass' => GolonganUmur::className(),
            'checkAccess' => [$this, 'checkAccess'],
        ];
        $actions['update-gol-umur'] = [
            'class' => 'yii\rest\UpdateAction',
            'modelClass' => GolonganUmur::className(),
            'checkAccess' => [$this, 'checkAccess']
        ];
        $actions['delete-gol-umur'] = [
            'class' => 'yii\rest\DeleteAction',
            'modelClass' => GolonganUmur::className(),
            'checkAccess' => [$this, 'checkAccess']
        ];
        $actions['delete-cara-masuk'] = [
            'class' => 'yii\rest\DeleteAction',
            'modelClass' => CaraMasuk::className(),
            'checkAccess' => [$this, 'checkAccess']
        ];
        $actions['create-cara-masuk'] = [
            'class' => 'yii\rest\CreateAction',
            'modelClass' => CaraMasuk::className(),
            'checkAccess' => [$this, 'checkAccess']
        ];
        $actions['create-gol-umur'] = [
            'class' => 'yii\rest\CreateAction',
            'modelClass' => GolonganUmur::className(),
            'checkAccess' => [$this, 'checkAccess']
        ];
        return $actions;
    }


    public function actionGetCaraMasuk()
    {
      	$request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'caramasuk_m');
        
        $model = new CaraMasuk;
        $query = $model::find()
             ->select([
                                'caramasuk_m.caramasuk_id',
                                'caramasuk_m.caramasuk_nama',
                                'caramasuk_m.caramasuk_namalainnya',
                                'caramasuk_m.is_active',
                      ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetGolUmur()
    {
        $request = Yii::$app->request;
        $advancedFilter = $request->get('advanced-filter');
        $_GET['expand'] = $request->get('expand', 'golonganumur_m');
        
        $model = new GolonganUmur;
        $query = $model::find()
             ->select([
                                'golonganumur_m.golonganumur_id',
                                'golonganumur_m.golonganumur_nama',
                                'golonganumur_m.golonganumur_namalainnya',
                                'golonganumur_m.golonganumur_minimal',
                                'golonganumur_m.golonganumur_maksimal',
                                'golonganumur_m.is_active',
                      ]);
        
        if(isset($advancedFilter)) {                        
            if(isset($advancedFilter['golonganumur_m.golonganumur_minimal'])) {
                // $query->where(['golonganumur_minimal' => $advancedFilter['golonganumur_minimal']]);
                $query->andWhere('golonganumur_minimal >= :golmin', [':golmin'=>$advancedFilter['golonganumur_m.golonganumur_minimal']]);
            }

            if(isset($advancedFilter['golonganumur_m.golonganumur_maksimal'])) {
                // $query->where(['golonganumur_maksimal' => $advancedFilter['golonganumur_maksimal']]);
                $query->andWhere('golonganumur_maksimal <= :golmaks', [':golmaks'=>$advancedFilter['golonganumur_m.golonganumur_maksimal']]);
            }
        }
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);        
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $type = $request->get('type');

        $model = ($type == 1) ? new CaraMasuk : new GolonganUmur;
        $title = ($type == 1) ? 'Cara Masuk' : 'Golongan Umur';

        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];

            if($type == 1) {
                $newValue[\Yii::t('app', 'Cara Masuk')] = $value['caramasuk_nama'];
                $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['caramasuk_namalainnya'];
                $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
            }
            else {
                $newValue[\Yii::t('app', 'Golongan Umur')] = $value['golonganumur_nama'];
                $newValue[\Yii::t('app', 'Usia')] = $value['golonganumur_namalainnya'];
                $newValue[\Yii::t('app', 'Umur Minimal')] = $value['golonganumur_minimal'];
                $newValue[\Yii::t('app', 'Umur Maksimal')] = $value['golonganumur_maksimal'];
                $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
            }
            
            $result[$key] = $newValue;
        }

        $header = array();

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ),[],[],true);
        $filePath->save('php://output');
        die;
}

    /**
    * @controller actionCetakCaraMasuk
    * @attribute #datatable# => Untuk mengganti data di table
    */
    public function actionCetakCaraMasuk()
    {
        $request = Yii::$app->request;
        $title = 'Cara Masuk';
        if ($request->get()) {
            $get = $request->get();

            $model = new CaraMasuk;
            $query = $model::find(true);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('cetak', [
                    'data' => $dataProvider->getModels(),
                    'title' => $title,
                ]),
            ];

            $print->Output();
        }
        \Yii::$app->response->statusCode = 500;
        return ['message' => 'Tidak Ada Data yang harus di cetak'];
    }

    /**
    * @controller actionCetakGolongan
    * @attribute #datatable# => Untuk mengganti data di table
    */
    public function actionCetakGolongan()
    {
        $request = Yii::$app->request;
        $title = 'Golongan Umur';
        if ($request->get()) {
            $get = $request->get();
            $model = new GolonganUmur;
            $query = $model::find(true);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('cetak_golongan', [
                    'data' => $dataProvider->getModels(),
                    'title' => $title,
                ]),
            ];

            $print->Output();
        }
        \Yii::$app->response->statusCode = 500;
        return ['message' => 'Tidak Ada Data yang harus di cetak'];
    }
}
