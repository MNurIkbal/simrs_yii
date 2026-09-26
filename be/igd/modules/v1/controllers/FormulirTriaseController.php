<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Triase;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PerawatView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\BagianTubuh;
use app\modules\v1\models\BagianTubuhDetail;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\KamarTempatTidur;

use Doco\components\DocoPrint;
use Doco\components\DocoConstants;

class FormulirTriaseController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Triase';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["save-asesmen"] = ["POST"];
        $verbs["get-asesmen"] = ["GET"];

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);

        return $actions;
    }

    /**
     * function for handle show triase data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetTriase()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $triase_id = $request->get('triase_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }
        $alergi = [];
        $model = Triase::find()
            ->select([
                'dokter.nama_pegawai as dokter_nama',
                'perawat.nama_pegawai as perawat_nama',
                'pendaftaran_t.tgl_pendaftaran',
                'triase_t.*'
            ])
            ->leftJoin('pegawai_m as dokter', 'dokter.pegawai_id = triase_t.dokter_id')
            ->leftJoin('pegawai_m as perawat', 'perawat.pegawai_id = triase_t.perawat_id')
            ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id');
        $getTriase = $model->where(['triase_t.pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        if ($triase_id) {
            $model = $model->where(['triase_t.triase_id' => $triase_id])->asArray()->one();
            if (!empty($getTriase)) {
                $model['old_triase_id'] = $getTriase['triase_id'];
            }
        } else {
            $model = $getTriase;
        }

        if (empty($model)) {
            $model = Pendaftaran::find()->select([
                'pendaftaran_t.pasien_id',
                'dokter.nama_pegawai as dokter_nama',
                'pendaftaran_t.pegawai_id as dokter_id',
                'pendaftaran_t.tgl_pendaftaran'
            ])
                ->leftJoin('pegawai_m as dokter', 'dokter.pegawai_id = pendaftaran_t.pegawai_id')
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()
                ->one();

            $alergi = Pendaftaran::find()->select([
                'triase_t.triase_id',
                'triase_t.alergi as alergi_triase',
                'triase_t.alergi_obat alergi_obat_triase',
                'triase_t.alergi_lainnya alergi_lainnya_triase',
                'asesmenperawatrd_t.asesmenperawatrd_id',
                'asesmenperawatrd_t.is_alergi as alergi_askep',
                'asesmenperawatrd_t.alergi_obat as alergi_obat_askep',
                'asesmenperawatrd_t.alergi_lainnya as alergi_lainnya_askep',
                'asesmenmedisrd_t.asesmenmedisrd_id',
                'asesmenmedisrd_t.alergi as alergi_lainnya_asmed'
            ])
                ->leftJoin('triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->leftJoin('asesmenperawatrd_t', 'asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->leftJoin('asesmenmedisrd_t', 'asesmenmedisrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where(['pasien_id' => $model['pasien_id']])
                ->andWhere('pendaftaran_t.no_pendaftaran LIKE :query')
                ->addParams([':query' => 'RD%'])
                ->orderBy('pendaftaran_t.pendaftaran_id DESC')
                ->limit(2)
                ->offset(1)
                ->asArray()
                ->one();
        }

        return [
            'data'   => $model,
            'alergi' => $alergi,
        ];
    }

    public function actionDokterList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = DokterView::find()
            ->select([
                'pegawai_id as id',
                'nama_pegawai as text'
            ])
            ->andWhere([
                'ruangan_id' => Yii::$app->jwt->ruangan_id
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'ilike',
                'nama_pegawai',
                $term
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    public function actionPerawatList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = PerawatView::find()
            ->select([
                'pegawai_id as id',
                'nama_pegawai as text'
            ])
            ->andWhere([
                'ruangan_id' => Yii::$app->jwt->ruangan_id
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'ilike',
                'nama_pegawai',
                $term
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    public function actionGetDataGcs()
    {
        $data_gcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_MASTER, Gcs::find()->orderBy(['gcs_nilaimin' => SORT_ASC]));
        $data_metodegcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_METODE, MetodeGcs::find());
        // $data_metodegcs = MetodeGcs::find()->all();
        $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
        $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
        $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
        $data_listgcs = [];

        foreach ($data_metodegcs as $key => $value) {
            if (!$value['metodegcs_nilai']) {
                continue;
            }

            $value['nama_and_nilai'] = $value['metodegcs_nama'] . ' - ' . $value['metodegcs_nilai'];
            if ($value['metodegcs_singkatan'] == $gcsindicator_eye) {
                $data_listgcs['eye'][] = $value;
            } elseif ($value['metodegcs_singkatan'] == $gcsindicator_verbal) {
                $data_listgcs['verbal'][] = $value;
            } elseif ($value['metodegcs_singkatan'] == $gcsindicator_motorik) {
                $data_listgcs['motorik'][] = $value;
            }
        }

        return [
            'data-gcs' => $data_gcs,
            'data-listgcs' => $data_listgcs,
        ];
    }

    /**
     * function for save formulir triase
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    /**
     * function for save asesmen keperawatan
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveTriase()
    {
        $request = Yii::$app->request;
        $dataTriase = $request->post('formdata', []);

        $transaction = Yii::$app->db->beginTransaction();
        $pendaftaran_id = !isset($dataTriase['pendaftaran_id']) || empty($dataTriase['pendaftaran_id']) ? null : $dataTriase['pendaftaran_id'];
        $triase_id = !isset($dataTriase['triase_id']) || empty($dataTriase['triase_id']) ? null : $dataTriase['triase_id'];
        $old_triase_id = !isset($dataTriase['old_triase_id']) || empty($dataTriase['old_triase_id']) ? null : $dataTriase['old_triase_id'];

        $inputAttribute = new Triase;
        $inputAttribute->attributes = $dataTriase;
        // $inputAttribute->is_deleted = false;
        // $inputAttribute->is_active  = true;
        // $inputAttribute->created_date = date("Y-m-d H:i:s");

        $model = new Triase;
        if (!is_null($triase_id)) {
            $model = Triase::findOne($triase_id);
        }
        if (!is_null($old_triase_id)) {
            Triase::updateAll(['pendaftaran_id' => null, 'is_deleted' => true], ['triase_id' => $old_triase_id]);
        }
        $model->attributes = $inputAttribute->attributes;
        $model->pendaftaran_id = $pendaftaran_id;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }
        if(!empty($dataTriase['old_kamartempattidur_id']) && !empty($dataTriase['kamartempattidur_id']) && $dataTriase['kamartempattidur_id'] != $dataTriase['old_kamartempattidur_id']) {
            $updateKamarTempatTidur = KamarTempatTidur::updateAll(
                ['status_isi' => true], 
                ['kamartempattidur_id' => $dataTriase['kamartempattidur_id']]);
            if ( !$updateKamarTempatTidur ) {
                $transaction->rollBack();
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
            $updateKamarTempatTidur = KamarTempatTidur::updateAll(
                ['status_isi' => false], 
                ['kamartempattidur_id' => $dataTriase['old_kamartempattidur_id']]);
            if ( !$updateKamarTempatTidur ) {
                $transaction->rollBack();
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
            
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'ketersediaan-bed-'.Yii::$app->params['mode'],            
                'message' => json_encode([
                    ['kamartempattidur_id'=>$dataTriase['kamartempattidur_id'], 'status_isi'=>true , 'status_edit' => false],
                    ['kamartempattidur_id'=>$dataTriase['old_kamartempattidur_id'], 'status_isi'=>false, 'status_edit' => false],
                ]),
            ]);
        }
        else {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'ketersediaan-bed-'.Yii::$app->params['mode'],            
                'message' => json_encode([
                    ['kamartempattidur_id'=>$dataTriase['kamartempattidur_id'], 'status_isi'=>true , 'status_edit' => false],
                ]),
            ]);
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Formulir Triase Berhasil!');
    }

    public function actionBundleListTriase()
    {
        $model = Triase::find()
            ->select([
                'dokter.nama_pegawai as dokter_nama',
                'perawat.nama_pegawai as perawat_nama',
                'triase_t.pendaftaran_id',
                'triase_t.triase_id',
                'triase_t.tgl_triase',
                'kamartempattidur_m.no_tempattidur',
                'triase_t.is_doa'
            ])
            ->leftJoin('pegawai_m as dokter', 'dokter.pegawai_id = triase_t.dokter_id')
            ->leftJoin('pegawai_m as perawat', 'perawat.pegawai_id = triase_t.perawat_id')
            ->leftJoin('kamartempattidur_m', 'kamartempattidur_m.kamartempattidur_id = triase_t.kamartempattidur_id')
            ->where(['triase_t.pendaftaran_id' => null])
            ->andWhere(['IS NOT', 'triase_t.kamartempattidur_id', null])
            ->orWhere(['=', 'triase_t.is_doa', true])
            ->orderBy(['triase_t.tgl_triase' => SORT_DESC]);

        $model->limit(Yii::$app->request->get('length', 10));
        $model->offset(Yii::$app->request->get('start', 0));

        return [
            'data' => $model->asArray()->all(),
            'totalCount' => $model->count()
        ];
    }
}
