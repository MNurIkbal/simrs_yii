<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\DokRekamMedis;
use app\modules\v1\models\PengirimanRm;
use app\modules\v1\models\PeminjamanRm;
use app\modules\v1\models\KirimDokRm;

use app\modules\v1\models\InfoKirimDok;
use app\modules\v1\models\InfoKirimDokDetail;
use app\modules\v1\models\KirimDokRmDetail;
use Doco\components\DocoHelpers;
use app\modules\v1\cache\Cache;


class PengirimanDokRekamMedikController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DokRekamMedis';

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
        unset($actions['view']);
        unset($actions['create']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new InfoKirimDok;
        $ruangan = Yii::$app->jwt->ruangan_id;
        $query = $model::find();
        $start = date('Y-m-01');
        $end = date('Y-m-d');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_kirim'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_kirim']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_kirim']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['instalasi_pengirim'])) {
                $query->andWhere(['insalasi_pengirim_id' => $_GET['advanced-filter']['instalasi_pengirim']]);
                unset($_GET['advanced-filter']['instalasi_pengirim']);
            }

            if (isset($_GET['advanced-filter']['ruangan_pengirim'])) {
                $query->andWhere(['ruanganpengirim_id' => $_GET['advanced-filter']['ruangan_pengirim']]);
                unset($_GET['advanced-filter']['ruangan_pengirim']);
            }
        }
        $query->andWhere(['between', 'tgl_kirim', $start, $end]);
        $query->andWhere(['ruanganpemesan_id' => $ruangan]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new PengirimanRm;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $request->post();
                $mDokRekamMedis = DokRekamMedis::find()
                ->where(['pasien_id' => $model->pasien_id])->one();
                if($mDokRekamMedis){
                    $model->dokrekammedis_id = $mDokRekamMedis->dokrekammedis_id;
                }
                $model->ruanganpengirim_id = 1;
                $model->nourut_keluar = '1';
                $model->tgl_pengirimanrm = date('Y-m-d');
                if ($model->save()) {
                    $mPeminjaman = new PeminjamanRm;
                    $mPeminjaman->pengirimanrm_id = $model->getPrimaryKey();
                    $mPeminjaman->dokrekammedis_id = $model->dokrekammedis_id;
                    $mPeminjaman->pasien_id = $model->pasien_id;
                    $mPeminjaman->instalasi_id = $post['instalasi_id'];
                    $mPeminjaman->ruangan_id = $model->ruangan_id;
                    $mPeminjaman->nourut_pinjam = '1';
                    $mPeminjaman->tglpeminjamanrm = $post['tglpeminjamanrm'];
                    $mPeminjaman->save();
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PengirimanRmForm');
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

    public function actionGetList($id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pasien_m');
        $query = DokRekamMedis::find()
                ->where(['pasien_id' => $id,'is_deleted'=>false])
                ->one();
        return $query;
    }

    public function actionView($id)
    {
        $cond = [
            'kirimdokrm_id' => $id
        ];
        $header = InfoKirimDok::find()->where($cond)->one();
        $detail = InfoKirimDokDetail::find()->where($cond)->all();
        return [
            'header' => $header,
            'detail' => $detail
        ];
    }

    public function actionListPasien()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pasien_m');
        
        $model = new DokRekamMedis;
        $query = $model::find()
            ->where(['dokrekammedis_m.is_deleted' => false])
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListNomor()
    {
        $request = Yii::$app->request;
        $model = new InfoKirimDok;
        $query = $model::find();
        $term = $request->get("term");
        $tanggal = $request->get("tanggal");
        $start = date('Y-m-01');
        $end = date('Y-m-d');
        if (!empty($term)) {
            $query->andWhere(['ILIKE','no_kirimdokrm',$term]);
        }

        if (!empty($tanggal)) {
            $explode = explode(" - ", $tanggal);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));
            }
        }
        $query->andWhere(['BETWEEN','tgl_kirim',$start, $end])
              ->limit(10);
        $result = $query->asArray()->all();
        $return = [];
        foreach ($result as $value) {
            $return[] = [
                'id' => $value['no_kirimdokrm'],
                'text' => $value['no_kirimdokrm']
            ];
        }
        return ['result' => $return];
    }

    public function actionTerima($id)
    {
        $result = KirimDokRm::find()->where([
            'kirimdokrm_id' => $id
        ])->one();

        $detail = KirimDokRmDetail::find()->where([
            'kirimdokrm_id' => $id
        ])->orderBy(['kirimdokrmdetail_id'=> SORT_DESC])->one();

        if (!empty($result) && !empty($detail)) {
            $result->status_kirim = DocoConstants::SUDAH_DITERIMA;
            $ruangPemesan = $result->ruanganpemesan_id;
            if ($result->save()) {
                $idDok = $detail->dokrekammedis_id;
                Yii::$app->db->createCommand("
                    UPDATE posisidokrm_r SET is_pesan = false, ruanganakhir_id = {$ruangPemesan} WHERE dokrekammedis_id = {$idDok}
                ")->execute();
                return [
                    'message' => 'success'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'failed'
        ];
    }

    public function actionGetOptions()
    {
        return [
            'instalasi' => Cache::getListInstalasi(),
            'ruangan' => Cache::getListRuangan(),
            'status_kirim' => Cache::getListStatus()
        ];
    }

    public function actionFormData()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'peminjamanrm_t');
        
        $model = new PengirimanRm;
        $query = $model::find()
            ->where(['pengirimanrm_t.is_deleted' => false])
            ->joinWith(['peminjamanrm' => function($query){
                $query->from('peminjamanrm_t');
            }])
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}