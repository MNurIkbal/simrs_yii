<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Obat Alkes Jenis Kasus Penyakit
 * @copyright 3 January 2018 aweutist
 * @edited yaya
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\KasusPenyakitObat;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\SatuanKonversi;
use Doco\components\DocoPrint;
use app\modules\v1\models\SatuanUnit;

class ObatAlkesKasusController extends DocoActiveController
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
        // return $query->asArray()->all();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function dataProvider()
    {
        $model = new KasusPenyakitObat;
        $request = Yii::$app->request->get('advanced-filter');
        $query = $model::find()->select([
            'kasuspenyakitobat_mp.jeniskasuspenyakit_id',
            'kasuspenyakitobat_mp.obatalkes_id',
            'obatalkes_m.obatalkes_nama'
        ])->joinWith([
            'jeniskasuspenyakit' => function ($query) {
                $query->select([
                    'jeniskasuspenyakit_m.jeniskasuspenyakit_id',
                    'jeniskasuspenyakit_m.jeniskasuspenyakit_nama',
                    'jeniskasuspenyakit_m.jeniskasuspenyakit_namalainnya',
                ]);
            },
            'obatalkes' => function ($query) {
                $query->select([
                    'obatalkes_m.obatalkes_id',
                    'obatalkes_m.obatalkes_kode',
                    'obatalkes_m.obatalkes_namalain',
                    'obatalkes_m.obatalkes_nama']);
            }
        ])->asArray();

        if (isset($request['obatalkes_m.obatalkes_nama'])) {
            $idObat = $request['obatalkes_m.obatalkes_nama'];

            $query->andWhere([
                'kasuspenyakitobat_mp.obatalkes_id' => $idObat
            ]);
            unset($_GET['advanced-filter']['obatalkes_m.obatalkes_nama']);
        }

        if (isset($request['jeniskasuspenyakit_m.jeniskasuspenyakit_namalainnya'])) {
            $jenisPenyakit = $request['jeniskasuspenyakit_m.jeniskasuspenyakit_namalainnya'];
            $query->andWhere([
                'kasuspenyakitobat_mp.jeniskasuspenyakit_id' => $jenisPenyakit
            ]);
            unset($_GET['advanced-filter']['jeniskasuspenyakit_m.jeniskasuspenyakit_namalainnya']);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->andWhere([
            'obatalkes_m.is_deleted' => false
        ]);
        return $query;
    }

    public function actionGetFiltered($id_obat = null, $id_penyakit = null)
    {
        $data = [];
        $obatAlkes = ArrayHelper::map(ObatAlkes::find()->all(),'obatalkes_id','obatalkes_nama');
        $kasusPenyakit = ArrayHelper::map(JenisKasusPenyakit::find()->all(),
            'jeniskasuspenyakit_id','jeniskasuspenyakit_nama');
        if ($id_obat && $id_penyakit) {
            $data = KasusPenyakitObat::find()->where([
                'obatalkes_id' => $id_obat,
                'jeniskasuspenyakit_id' => $id_penyakit
            ])->asArray()->one();
        }
        return [
            'obat_alkes' => $obatAlkes,
            'kasus_penyakit' => $kasusPenyakit,
            'data' => $data
        ];
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
            'obatalkes_nama',
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
        $kasus = JenisKasusPenyakit::find()->select([
            'jeniskasuspenyakit_id',
            'jeniskasuspenyakit_nama'
        ]);

        return $kasus;
    }

    public function actionCreate()
    {
        try {
            $model = new KasusPenyakitObat;
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $request->post();
                if ($model->validate() && is_array($model->obatalkes_id)) {
                    $batchInsert = [];
                    foreach ($model->obatalkes_id as $val) {
                        $batchInsert[] = [
                            'jeniskasuspenyakit_id' => $model->jeniskasuspenyakit_id,
                            'obatalkes_id' => $val
                        ];
                    }
                    KasusPenyakitObat::batchInsert($batchInsert,false);
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
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

    private function getData($id = null, $id2 = null)
    {
        $kasusObat = KasusPenyakitObat::find()
                    ->select([
                        'obatalkes_m.obatalkes_namalain',
                        'obatalkes_m.obatalkes_id',
                        'jeniskasuspenyakit_m.jeniskasuspenyakit_id',
                        'jeniskasuspenyakit_m.jeniskasuspenyakit_nama'
                    ])
                    ->joinWith([
                        'obatalkes' => function ($query) {
                        $query->select(['obatalkes_m.obatalkes_namalain','obatalkes_m.obatalkes_id']);
                    }])
                    ->joinWith([
                        'jeniskasuspenyakit' => function ($query) {
                        $query->select(['jeniskasuspenyakit_m.jeniskasuspenyakit_nama','jeniskasuspenyakit_m.jeniskasuspenyakit_id']);
                    }]);
        if ($id && $id2) {
            $kasusObat->where(['jeniskasuspenyakit_m.jeniskasuspenyakit_id' => $id, 'obatalkes_m.obatalkes_id' => $id2]);
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
            $model = new JenisKasusPenyakit;
            $request = Yii::$app->request;

            $result = $this->getDataJenisKasus()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($jenis_kasus = $request->post('jeniskasus')) {
                $result->andFilterWhere(['ILIKE', 'jeniskasuspenyakit_nama', $jenis_kasus]);
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

    public function actionDelete($id_obat, $id_penyakit)
    {
        try {
            $result = (new KasusPenyakitObat)->delete([
                'jeniskasuspenyakit_id' => $id_penyakit,
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
    * @controller actionCetakObatAlkes
    * @attribute #tabel_alkes_kasus_penyakit# => Untuk Menampilkan data obat kasus penyakit
    **/

    public function actionCetakObatAlkes()
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

    public function actionUpdate($id_obat, $id_penyakit)
    {
        try {
            $model = KasusPenyakitObat::find()->where([
                'jeniskasuspenyakit_id' => $id_penyakit,
                'obatalkes_id' => $id_obat
            ])->one();

            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->jeniskasuspenyakit_old = $id_penyakit;
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

    public function actionGetObat()
    {
        $request = Yii::$app->request;
        
        $post = $request->post();       
        $result = $this->getDataObat();
        $result->select(['obatalkes_id','obatalkes_nama']);     
        if(!empty($post['term'])){
            $term = $post['term'];    
            $result->where(['ILIKE', 'LOWER(obatalkes_nama)', $term]);            
        }

        if(!empty($post['q'])){
            $term = $post['q'];    
            $result->where(['ILIKE', 'LOWER(obatalkes_nama)', $term]);            
        }
        if(!empty($request->get('q'))){
            $term = $request->get('q');    
            $result->where(['ILIKE', 'LOWER(obatalkes_nama)', $term]);            
        }        
        return $result->asArray()->all();
    }

    public function actionGetSatuan()
    {
        $request = Yii::$app->request;
        $obatalkes_id = $request->get('obatalkes_id');
        $query = ObatAlkes::findOne($obatalkes_id);
        $query = SatuanKonversi::find()
            ->joinWith(['satuankecil'])
            ->where(['satuanbesar_id' => $query->satuanbesar_id]);

        return $query->asArray()->all();
    }

    public function actionGetApi()
    {
        $request = Yii::$app->request;
        $obat_alkes = ObatAlkes::findOne($request->get('obatalkes_id'));
        $satuan_besar = SatuanUnit::findOne($request->get('satuanbesar_id'));
        $satuan_kecil = SatuanUnit::findOne($request->get('satuankecil_id'));
        $konversi = SatuanKonversi::find()->where([
            'satuanbesar_id' => $request->get('satuanbesar_id'),
            'satuankecil_id' => $request->get('satuankecil_id'),
        ])->one();

        $qty_konversi = $konversi->nilai_konversi * $request->get('qty');

        return [
            'obatalkes_nama' => ($obat_alkes) ? $obat_alkes->obatalkes_nama : '',
            'satuanbesar_nama' => ($satuan_besar) ? $satuan_besar->satuanunit_nama : '',
            'satuankecil_nama' => ($satuan_kecil) ? $satuan_kecil->satuanunit_nama : '',
            'qty_konversi' => $qty_konversi,
            'hargajual' => ($obat_alkes) ? $obat_alkes->hargajual : '',
            'harganetto' => ($obat_alkes) ? $obat_alkes->harganetto : '',
        ];
    }

    public function actionObat()
    {
        $model = new ObatAlkes;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            
        }
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
