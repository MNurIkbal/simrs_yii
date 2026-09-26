<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PenanggungJawab;
use Doco\components\DocoHelpers;

class PasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pasien';
    const DEFAULT_PAGESIZE = 20;

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
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $filter = $request->get();
        $results = [];
        // $_GET['expand'] = $request->get('expand', 'kelompokjabatan_m,indexing_m');
        
        $model = new PasienV;
        $query = $model::find()
            // ->select(['pasien_v.*', 'gender.lookup_name as jenis_kelamin'])
            // ->leftJoin("lookup_m gender ON to_number(pasien_v.jeniskelamin::text, '99'::text) = gender.lookup_id")
            // ->leftJoin("lookup_m nama_alias ON to_number(pasien_m.namadepan::text, '99'::text) = nama_alias.lookup_id")
            ;

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_rekam_medik'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_rekam_medik']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_rekam_medik']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tgl_rekam_medik', $start, $end]);
        // }

        if (array_key_exists('order', $filter)) {
            $order = explode(" ", $filter['order']);
            $key = $order[0];
            $type = ($order[1] == 'DESC') ? SORT_DESC : SORT_ASC;

            if($key == 'tgl_rekam_medik') {
                $type = SORT_DESC;
            }

            $query->orderBy([
                $key => $type
            ]);
        }

        $pageSize = self::DEFAULT_PAGESIZE;
        if (array_key_exists('per-page', $filter)) {
            $pageSize = $filter['per-page'];
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $data = $query->all();

        foreach ($data as $key => $value) {
            $umur = DocoHelpers::getUmur($value['tanggal_lahir']);
            $value['umur'] = $umur;
            $results[$key] = $value;
        }

        return new ArrayDataProvider([
            'allModels' => $results,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'lokasirak_m,subrak_m,pasien_m,warnadokrekammedik_m');
        
        $model = new Pasien;
        $query = Pasien::find()
            ->select(['pasien_m.*', 'nama_alias.lookup_name as nama_alias'])
            ->leftJoin("lookup_m nama_alias ON to_number(pasien_m.namadepan::text, '999'::text) = nama_alias.lookup_id")
            ->joinWith(['penanggungJawab'])
            ->where(['pasien_m.pasien_id' => $id]);

        return $query->asArray()->one();
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Pasien::findOne($id);
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post;
                
                $modelPenanggungJawab = PenanggungJawab::find()->where(['pasien_id' => $model->pasien_id])->one();
                $modelPenanggungJawab = ($modelPenanggungJawab) ? $modelPenanggungJawab : new PenanggungJawab();
                
                $modelPenanggungJawab->pengantar = '-';
                $modelPenanggungJawab->penanggungjawab_jeniskelamin = '-';
                $modelPenanggungJawab->pasien_id = $model->pasien_id;
                $modelPenanggungJawab->no_identitas = $post['no_identitas'];
                $modelPenanggungJawab->hubungankeluarga = $post['hubungankeluarga'];
                $modelPenanggungJawab->penanggungjawab_nama = $post['penanggungjawab_nama'];
                $modelPenanggungJawab->penanggungjawab_alamat = $post['penanggungjawab_alamat'];
                $modelPenanggungJawab->penanggungjawab_notelp = $post['penanggungjawab_notelp'];

                if ($model->update() && $modelPenanggungJawab->save()) {
                    return ['message' => 'Data Berhasil di simpan'];

                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PasienForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
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

    public function actionListStatusKawin()
    {
        $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'kelompokjabatan_m,indexing_m');
        
        $model = new Pasien;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}