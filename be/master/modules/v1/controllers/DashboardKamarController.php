<?php
/**
 * @Author: Iqbal
 * @Date:   2018-09-17 90:20:59
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-11-13 10:04:40
 */
namespace app\modules\v1\controllers;

use app\modules\v1\cache\Cache;
use app\modules\v1\models\DashboardBedView;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoAccessRule;
use Doco\components\DocoJwtHttpBearerAuth;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\DashboardKamarView;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\MasterKamarRuanganView;
use app\modules\v1\models\KamarRuanganHeaderView;

class DashboardKamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DashboardKamarView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * @todo Action untuk unset authenticator dan access rule
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['get-kelas', 'get-ruangan', 'get-ketersediaan-kamar'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['get-kelas', 'get-ruangan', 'get-ketersediaan-kamar'],
        ];

        return $behaviors;
    }

    private function model(){
        $model = new DashboardKamarView;
        return $model::find();   
    }

    public function actionIndex()
    {
        // try {
            $request = Yii::$app->request;
            
            $model = new DashboardKamarView;
            $query = $this->model();
            // $query->groupBy(['ruangan_id']);
            $res =  $query->asArray()->all();
            echo "<pre>"; var_dump($res);die();
            /*$query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['nama_rumahsakit' => SORT_ASC,
                             'instalasi_nama' => SORT_ASC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);*/
       /* } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }*/
    }

    public function actionListInstalasi() {
        $data = Instalasi::find()->where(['is_active' => 't'])->orderBy('instalasi_id');
        $items = ArrayHelper::map($data->all(), 'instalasi_id', 'instalasi_nama');

        return $items;
    }

    /**
     * @todo Action untuk mendapatkan data kelas
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKelas($kelaspelayanan_id = null) {
        try {
            $model = KelasPelayanan::find();

            if ($kelaspelayanan_id) {
                $model->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
            }

            $kelas_pelayanan = $model->all();

            if (!empty($kelas_pelayanan)) {
                return $kelas_pelayanan;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data kelas tidak ditemukan.')
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @todo Action untuk mendapatkan data ruangan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetRuangan($ruangan_id = null, $kelaspelayanan_id = null) {
        try {
            $model = MasterKamarRuanganView::find()->select([
                'ruangan_id',
                'kelaspelayanan_id',
                'ruangan_nama'
            ])->groupBy('ruangan_id, kelaspelayanan_id, ruangan_nama');

            if ($ruangan_id) {
                $model->andWhere(['ruangan_id' => $ruangan_id]);
            }

            if ($kelaspelayanan_id) {
                $model->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
            }

            $ruangan = $model->all();

            if (!empty($ruangan)) {
                return $ruangan;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data ruangan tidak ditemukan.')
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @todo Action untuk mendapatkan data ketersediaan kamar
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKetersediaanKamar($kelaspelayanan_id = null, $ruangan_id = null) {
        try {
            
            $where ="";

            if ($kelaspelayanan_id) {
                $where .= " where kelaspelayanan_id = ".$kelaspelayanan_id;
                // $model->andWhere(['ruangan_id' => $kelaspelayanan_id]);
            }

            if ($ruangan_id) {
                if ($kelaspelayanan_id) {
                    $where .= " AND ruangan_id = ".$ruangan_id;
                } else {
                    $where .= " where ruangan_id = ".$ruangan_id;
                }
                // $model->andWhere(['ruangan_id' => $ruangan_id]);
            }

            $sql = "SELECT * FROM (SELECT kamarruangan_m.kelaspelayanan_id AS ruangan_id,
                        kelaspelayanan_m.kelaspelayanan_nama AS ruangan_nama,
                        kamartempattidur_m.status_isi,
                        count(kamartempattidur_m.status_isi) AS jumlah
                   FROM (((kamarruangan_m
                     LEFT JOIN ruangan_m ON (((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.is_deleted = false))))
                     LEFT JOIN kamartempattidur_m ON (((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id) AND (kamartempattidur_m.is_deleted = false))))
                     LEFT JOIN kelaspelayanan_m ON (((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id) AND (kelaspelayanan_m.is_deleted = false))))
                  where kamarruangan_m.is_dashboard = true
                  GROUP BY kamarruangan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, kamartempattidur_m.status_isi) query";

            $sql .= $where;

            $ketersediaan_kamar =  Yii::$app->db->CreateCommand($sql)->queryAll();

            // $model = KamarRuanganHeaderView::find();

            // if ($kelaspelayanan_id) {
            //     $model->andWhere(['ruangan_id' => $kelaspelayanan_id]);
            // }

            // if ($ruangan_id) {
            //     $model->andWhere(['ruangan_id' => $ruangan_id]);
            // }

            // $ketersediaan_kamar = $model->asArray()->all();

            $temp = [];
            $content = [];
            $return = [];
            if (!empty($ketersediaan_kamar)) {
                foreach ($ketersediaan_kamar as $key => $value) {
                    if (isset($content[$value['ruangan_id']])) {
                        if ($content[$value['ruangan_id']]['status_isi'] == true) {
                            $content[$value['ruangan_id']]['kosong'] = $value['jumlah'];
                        } else {
                            $content[$value['ruangan_id']]['isi'] = $value['jumlah'];
                        }

                        unset($content[$value['ruangan_id']]['jumlah']);
                    } else {
                        if ($value['status_isi'] == true) {
                            $value['isi'] = $value['jumlah'];
                            $value['kosong'] = 0;
                        } else {
                            $value['kosong'] = $value['jumlah'];
                            $value['isi'] = 0;
                        }

                        $content[$value['ruangan_id']] = $value;
                        unset($content[$value['ruangan_id']]['jumlah']);
                    }
                }

                if (!empty($content)) {
                    foreach ($content as $key => $value) {
                        $return[] = $value;
                    }
                }

                return $return;
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'message' => Yii::t('app', 'Data ketersediaan kamar tidak ditemukan.')
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDataLayarKetersediaanKamar()
    {
        $data = DashboardBedView::find()->asArray()->all();
        $konfig_layar = Cache::getKonfigAntrian();
        return [
            'dataKamar' => $data,
            'konfig-layar' => $konfig_layar
        ];
    }
}