<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\KelahiranBayiView;
use app\modules\v1\models\KelahiranBayi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\KamarTempatTidur;
use Doco\Services\Cache;

class BayibarulahirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KelahiranBayiView';
    protected $_asisfiksiaTidakAda  = 'Tidak Ada';

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

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $model = new KelahiranBayiView;
        $query = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy([
            'bayi_urut' => SORT_ASC
        ]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionBundleData()
    {
        try{
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id',0);

            $cacheKeperawatan = Cache::getLookUpKeperawatan();;
            $setKey = ArrayHelper::index($cacheKeperawatan, null, 'lookup_type');
            $list_lookup = ArrayHelper::filter($setKey, [
                'penilaian',
                'bayinormal_tindakan',
                'asfiksia',
                'asfiksia_tindakan'      
            ]);

            return [
                'penilaian' => ArrayHelper::map($list_lookup['penilaian'],'lookupkeperawatan_id','lookup_name'),
                'normal_tindakan' => ArrayHelper::map($list_lookup['bayinormal_tindakan'],'lookupkeperawatan_id','lookup_name'),
                'asfiksia' => ArrayHelper::map($list_lookup['asfiksia'],'lookupkeperawatan_id','lookup_name'),
                'asfiksia_tindakan' => ArrayHelper::map($list_lookup['asfiksia_tindakan'],'lookupkeperawatan_id','lookup_name'),
                'jenis_kelamin' => ArrayHelper::map(Lookup::find()->where(['lookup_type'=>'jenis_kelamin'])->orderBy(['lookup_urutan'=>SORT_ASC])->asArray()->all(),'lookup_id','lookup_name'),
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'data' => []
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function actionCreate()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $request = Yii::$app->request;
            // $model = new KelahiranBayi;
            $post = $request->post('KelahiranBayiForm',[]);
            $pendaftaran_id = $request->get('pendaftaran_id',0);
            $pasienadmisi_id = $request->get('pasienadmisi_id',0);
            $kelahiranbayi_id = $request->get('kelahiranbayi_id',0);
            $oldKamarTempatTidurId = null;
            if (!empty($kelahiranbayi_id)) {
                $model = KelahiranBayi::find()->where([
                    'kelahiranbayi_id' => $kelahiranbayi_id
                ])->one();
                $oldKamarTempatTidurId = $model->kamartempattidur_id;
            } else {
                $model =  new KelahiranBayi;
                $countBayi = KelahiranBayi::find(true)->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => $pasienadmisi_id
                ])->count();
                $bayi_urut = $countBayi+1;
                $model->bayi_urut = $bayi_urut;
            }


            $post['tgl_lahir'] = date("Y-m-d H:i:s", strtotime($post['tgl_lahir']));
            $model->attributes = $post;
            $model->keterangan_cacat = isset($post['cacat_kondisi']) ? $post['cacat_kondisi'] : '';
            $model->pendaftaran_id = $pendaftaran_id;
            $model->pasienadmisi_id = $pasienadmisi_id;
            $model->berat_badan = str_replace(',', '.', $model->berat_badan);
            $model->tinggi_badan = str_replace(',', '.', $model->tinggi_badan);

            if ($post['kondisi_bayi'] == 86) {
                $model->normal_tindakan = null;
                $model->asfiksia = null;
                $model->asfiksia_tindakan = null;
                $model->keterangan_hipotermi = null;
            } elseif ($post['kondisi_bayi'] == 87) {
                $model->normal_tindakan = null;
                $model->asfiksia = null;
                $model->asfiksia_tindakan = null;
                $model->keterangan_cacat = null;
            } else {
                $model->keterangan_cacat = null;
                $model->keterangan_hipotermi = null;

                if (isset($post['normal_tindakan'])) {
                    $model->normal_tindakan = json_encode($post['normal_tindakan']);
                }

                $setTindakan = false;
                $getDataAsfiksia = LookupKeperawatan::find()->where(['lookupkeperawatan_id' => $model->asfiksia])->one();
                if ($getDataAsfiksia) {
                    if ($getDataAsfiksia->lookup_value == $this->_asisfiksiaTidakAda) {
                        $setTindakan = true;
                    }
                }
                if ($setTindakan) {
                    $model->asfiksia_tindakan = null;
                }else{
                    if (isset($post['asfiksia_tindakan'])) {
                        $model->asfiksia_tindakan = json_encode($post['asfiksia_tindakan']);
                    }
                }

            }

            if (isset($post['hipotermi_keterangan'])) {
                $model->keterangan_hipotermi = json_encode($post['hipotermi_keterangan']);
            }
            
            if (!$model->save()) {
                $errors = DocoHelpers::parseError($model->errors,'KelahiranBayiForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
            // if( !is_null($oldKamarTempatTidurId) && $oldKamarTempatTidurId != $model->kamartempattidur_id ){
            //     $updateOldKamar = (new KamarTempatTidur)->updateStatusIsi([
            //         'jenis_kelamin' => $model->jenis_kelamin,
            //         'status_isi' => false,
            //         'kamartempattidur_id' => $oldKamarTempatTidurId
            //     ]);
            //     if( !$updateOldKamar ){
            //         $transaction->rollBack();
            //         return $this->responseJson(500, 'Terjadi Kesalahan!');
            //     }
            // }
            // if (!empty($model->kamartempattidur_id)) {

            //     $updateKamar = (new KamarTempatTidur)->updateStatusIsi([
            //         'jenis_kelamin' => $model->jenis_kelamin,
            //         'status_isi' => true,
            //         'kamartempattidur_id' => $model->kamartempattidur_id
            //     ]);
                
            //     if( !$updateKamar ){
            //         $transaction->rollBack();
            //         return $this->responseJson(500, 'Terjadi Kesalahan!');
            //     }
            // }

            $transaction->commit();
            return [
                'message' => 'Data Berhasil di simpan'
            ];

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    /**
     * @todo Fungsi untuk mendapatkan data kondisi bayi baru lahir
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKondisiBayiBaruLahir()
    {
        $model = array();
        $request = Yii::$app->request;
        $id = $request->get('id', null);

        if ($id) {
            $model = KelahiranBayiView::find()->where(['kelahiranbayi_id' => $id])->one();
        }

        return $model;

    }

    public function actionView($id)
    {
        $query = KelahiranBayi::find()->select([
            'kelahiranbayi_t.*',
            "CONCAT(kamarruangan_m.kamarruangan_nokamar, ' - ', kamartempattidur_m.no_tempattidur) as kamartempattidur_text",
            'lookupkeperawatan_m.lookup_value as asfiksia_name'
        ])
        ->leftJoin('kamartempattidur_m', 'kamartempattidur_m.kamartempattidur_id = kelahiranbayi_t.kamartempattidur_id')
        ->leftJoin('kamarruangan_m', 'kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id   ')
        ->leftJoin('lookupkeperawatan_m', 'lookupkeperawatan_m.lookupkeperawatan_id = kelahiranbayi_t.asfiksia')
        ->where(['kelahiranbayi_id' => $id])->asArray()->one();

        return $query;
    }

    public function actionDeleteBayi()
    {
        $request = Yii::$app->request;
        $kelahiranbayi_id = $request->get('kelahiranbayi_id');

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $model = KelahiranBayi::find()->where([
                'kelahiranbayi_id' => $kelahiranbayi_id
            ])->one();
            if($model) {
                $model->is_deleted = true;
                $model->deleted_date = date('Y-m-d H:i:s');
                $model->save(false);
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Bayi Berhasil',
                ];
            } 
            else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['text'] = "Gagal Menghapus Data";
            }

            return $result;
        } catch (Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }
}