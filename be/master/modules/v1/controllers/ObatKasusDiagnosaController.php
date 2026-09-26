<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Obat Alkes Jenis Kasus Penyakit
 * @copyright 3 January 2018 aweutist
 * @edited yaya
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\DiagnosaObat;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Diagnosa;
use Doco\components\DocoPrint;

class ObatKasusDiagnosaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ObatAlkes';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $query = $this->dataProvider();

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function dataProvider()
    {
        $model = new DiagnosaObat;
        $request = Yii::$app->request->get('advanced-filter');
        $query = $model::find()->select([
            'diagnosaobat_mp.diagnosa_id',
            'diagnosaobat_mp.obatalkes_id',
            'obatalkes_m.obatalkes_nama'
        ])->joinWith([
            'diagnosa' => function ($query) {
                $query->select([
                    'diagnosa_m.diagnosa_id',
                    'diagnosa_m.diagnosa_nama',
                    'diagnosa_m.diagnosa_namalainnya',

                ]);
            },
            'obatalkes' => function ($query) {
                $query->select(['obatalkes_m.obatalkes_id',
                    'obatalkes_m.obatalkes_kode',
                    'obatalkes_m.obatalkes_namalain',
                    'obatalkes_m.obatalkes_nama']);
            }
        ])->asArray();

        if (isset($request['obatalkes_m.obatalkes_nama'])) {
            $idObat = $request['obatalkes_m.obatalkes_nama'];
            $query->where([
                'diagnosaobat_mp.obatalkes_id' => $idObat
            ]);
            unset($_GET['advanced-filter']['obatalkes_m.obatalkes_nama']);
        }

        if (isset($request['diagnosa_m.diagnosa_nama'])) {
            $idDiagnosa = $request['diagnosa_m.diagnosa_nama'];
            $query->andWhere([
                'diagnosa_m.diagnosa_id' => $idDiagnosa
            ]);
            unset($_GET['advanced-filter']['diagnosa_m.diagnosa_nama']);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionGetFiltered($id_obat = null, $id_diagnosa = null)
    {
        $data = [];
        $obatAlkes = ArrayHelper::map(ObatAlkes::find()->all(),'obatalkes_id','obatalkes_nama');
        $kasusPenyakit = ArrayHelper::map(Diagnosa::find()->all(),
            'diagnosa_id','diagnosa_nama');

        if ($id_obat && $id_diagnosa) {
            $data = DiagnosaObat::find()
            ->joinWith(['diagnosa', 'obatalkes'])
            ->where([
                'diagnosaobat_mp.obatalkes_id' => $id_obat,
                'diagnosaobat_mp.diagnosa_id' => $id_diagnosa
            ])->asArray()->one();
        }
        return [
            'obat_alkes' => $obatAlkes,
            'kasus_penyakit' => $kasusPenyakit,
            'data' => $data
        ];
    }

    public function actionListDiagnosa($q = null)
    {
        $query = Diagnosa::find()->where(['ILIKE', 'diagnosa_nama', $q])->orderBy('diagnosa_nama')->all();
        $out = [];
        foreach ($query as $d) {
            
            $out[] = ['id' => $d['diagnosa_id'], 'text' => $d['diagnosa_nama']];
        }
        
        return $out;
    }

    public function actionListObatAlkes($q = null)
    {
        $query = ObatAlkes::find()->where(['ILIKE', 'obatalkes_nama', $q])->orderBy('obatalkes_nama')->all();
        $out = [];
        foreach ($query as $d) {
            
            $out[] = ['id' => $d['obatalkes_id'], 'text' => $d['obatalkes_nama']];
        }
        
        return $out;
    }

    /**
     * @todo get data obat
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    private function getDataObat()
    {
        $obat = ObatAlkes::find()->select([
            'obatalkes_id',
            'obatalkes_kode',
            'obatalkes_namalain'
        ]);
        
        return $obat;

    }

    /**
     * @todo get data kasus penyakit
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    private function getDataJenisKasus()
    {
        $kasus = Diagnosa::find()->select([
            'diagnosa_id',
            'diagnosa_nama'
        ]);

        return $kasus;
    }

    public function actionCreate()
    {
        try {
            $model = new DiagnosaObat;
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $request->post();
                if(is_array($model->obatalkes_id)) {
                    if ($model->validate()) {
                        $batchInsert = [];
                        foreach ($model->obatalkes_id as $val) {
                            $batchInsert[] = [
                                'diagnosa_id' => $model->diagnosa_id,
                                'obatalkes_id' => $val
                            ];
                        }
                        DiagnosaObat::batchInsert($batchInsert,false);
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
                }
                else {
                    $modelDiagnosaObat = new DiagnosaObat;
                    $modelDiagnosaObat->diagnosa_id = $model->diagnosa_id;
                    $modelDiagnosaObat->obatalkes_id = $model->obatalkes_id;
                    if ($model->validate() && $modelDiagnosaObat->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    }
                    else {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
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

    private function getData($id = null, $id2 = null)
    {
        $kasusObat = DiagnosaObat::find()
                    ->select([
                        'obatalkes_m.obatalkes_namalain',
                        'obatalkes_m.obatalkes_id',
                        'diagnosa_m.diagnosa_id',
                        'diagnosa_m.diagnosa_nama'
                    ])
                    ->joinWith([
                        'obatalkes' => function ($query) {
                        $query->select(['obatalkes_m.obatalkes_namalain','obatalkes_m.obatalkes_id']);
                    }])
                    ->joinWith([
                        'diagnosa' => function ($query) {
                        $query->select(['diagnosa_m.diagnosa_nama','diagnosa_m.diagnosa_id']);
                    }]);
        if ($id && $id2) {
            $kasusObat->where(['diagnosa_m.diagnosa_id' => $id, 'obatalkes_m.obatalkes_id' => $id2]);
        }

        return $kasusObat;
    }

    public function actionAjax()
    {
        $data_obat = $this->getDataObat()->asArray()->all();
        $data_kasus = $this->getDataJenisKasus()->asArray()->all();

        return [
            'data-obat' => $data_obat,
            'data-kasus' => $data_kasus
        ];
    }

    public function actionDataObat()
    {
        try {
            $model = new ObatAlkes;
            $request = Yii::$app->request;

            $result = $this->getDataObat()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($obat_alkes = $request->post('obat_alkes')) {
                $result->andFilterWhere(['ILIKE', 'obatalkes_namalain', $obat_alkes]);
            }

            if ($kode_alkes = $request->post('kode_alkes')) {
                $result->andFilterWhere(['ILIKE','obatalkes_kode',$kode_alkes]);
            }


            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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

    public function actionDataPenyakit()
    {
        try {
            $model = new Diagnosa;
            $request = Yii::$app->request;

            $result = $this->getDataJenisKasus()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($jenis_kasus = $request->post('jeniskasus')) {
                $result->andFilterWhere(['ILIKE', 'diagnosa_nama', $jenis_kasus]);
            }

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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

    public function actionDelete($id_obat, $id_diagnosa)
    {
        try {
            $result = (new DiagnosaObat)->delete([
                'diagnosa_id' => $id_diagnosa,
                'obatalkes_id' => $id_obat
            ]);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id, $id2)
    {
        return $this->getData($id, $id2)->asArray()->one();
    }

    /**
    * @controller actionCetakObatDiagnosa
    * @attribute #tabel_alkes_kasus_penyakit# => Untuk Menampilkan data obat kasus penyakit
    **/

    public function actionCetakObatDiagnosa()
    {
        $query = $this->dataProvider();
        $print = new DocoPrint();
        $print->attributes = [
            '#tabel_alkes_kasus_penyakit#' => $this->renderPartial('index',[
                'detail' => $query->asArray()->all()
            ]),
        ];

        $print->Output();
    }

    public function actionUpdate($id_obat, $id_diagnosa)
    {
        try {
            $model = DiagnosaObat::find()->where([
                'diagnosa_id' => $id_diagnosa,
                'obatalkes_id' => $id_obat
            ])->one();
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->diagnosa_old = $id_diagnosa;
                $model->obatalkes_old = $id_obat;
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di update'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
