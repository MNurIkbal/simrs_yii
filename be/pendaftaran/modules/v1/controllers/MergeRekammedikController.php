<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;

use app\modules\v1\models\Pasien;
use app\modules\v1\models\InfoKunjunganRsView;
use app\modules\v1\models\PasienV;

use app\modules\v1\payload\MergeRekammedikPayload;

class MergeRekammedikController extends DocoActiveController
{
    public $modelClass = '';
    const SUCCESS = 'Success';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionDataPasien(){
        $request = Yii::$app->request;
        $search = $request->get();
        $term = $search['q'];
        $model = self::getModelPasien(true);
        $query = $model::find();
        if(!empty($term)){
            $term = strtolower($term);
            $query->andWhere(['like', 'LOWER(no_rekam_medik)', $term]);
            $query->orWhere(['like', 'LOWER(nama_pasien)', $term]);
            if ($this->isRealDate($term)) {
                $query->orWhere(['tanggal_lahir' => $term]);
            }
        }
        $query->andWhere(['is_active' => true, 'is_deleted' => false])
        ->limit(50);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function isRealDate($date)
    {
        if(strpos($date,"-") !== false){
            if (false === strtotime($date)) {
                return false;
            }
            list($year, $month, $day) = explode('-', $date);
            return checkdate($month, $day, $year);
        }else{
            return false;
        }
    }

    public function actionGetInfoPasien()
    {
        $resultAsal = $resulTujuan = [];
        $request = Yii::$app->request->get();
        $rmAsal = $request['no_rekam_medik_asal'];
        $rmTujuan = $request['no_rekam_medik_tujuan'];

        if (empty($rmAsal) && empty($rmTujuan)) return [];

        $resAsal = self::getDataPasien();
        $resTujuan = self::getDataPasien();

        $resAsal->andWhere([
            'no_rekam_medik' => $rmAsal
        ]);

        $resTujuan->andWhere([
            'no_rekam_medik' => $rmTujuan
        ]);

        $resultAsal['info_pasien'] = $resAsal->asArray()->one();
        $resulTujuan['info_pasien'] = $resTujuan->asArray()->one();

        $kunjAsal = self::getKunjunganPasien();
        $kunjTujuan = self::getKunjunganPasien();

        $kunjAsal->andWhere([
            'no_rekam_medik' => $rmAsal
        ]);

        $kunjTujuan->andWhere([
            'no_rekam_medik' => $rmTujuan
        ]);

        $resultAsal['kunjungan_pasien'] = $kunjAsal->orderBy([
            'tgl_pendaftaran' => SORT_DESC
        ])->one();

        $resulTujuan['kunjungan_pasien'] = $kunjTujuan->orderBy([
            'tgl_pendaftaran' => SORT_DESC
        ])->one();

        return [
            'info_asal' => $resultAsal,
            'info_tujuan' => $resulTujuan,
        ];
    }

    private static function getModelPasien($new = false)
    {
        if($new) {
            return new Pasien;
        } else {
            return Pasien::find();
        }
    }

    private static function getDataPasien()
    {
        return PasienV::find();
    }

    private static function getKunjunganPasien()
    {
        return InfoKunjunganRsView::find();
    }

    public function actionSave()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $tmpMergeAwal = $tmpMergeTujuan = [];
        try {
            $post = Yii::$app->request->post();
            $payload = new MergeRekammedikPayload;
            $payload->attributes = $post;
            if (!$payload->validate()) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $payload->errors
                ]);
            }
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

            $loginpemakai_id = $jwt->loginpemakai_id;
            $check = $jwt->katakunci_pemakai;
            $valid = Yii::$app->security->validatePassword($payload->password, $check);
            if (!$valid) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Password salah.'
                ]);
            }

            $pasienAwal = self::getDataPasien()
            ->select(['pasien_id'])
            ->where(['no_rekam_medik' => $payload->no_rekammedik_asal])
            ->asArray()
            ->one();

            $pasienTujuan = self::getDataPasien()
            ->select(['pasien_id'])
            ->where(['no_rekam_medik' => $payload->no_rekammedik_tujuan])
            ->asArray()
            ->one();

            if($pasienAwal && $pasienTujuan) {
                $noRmTujuan = $payload->no_rekammedik_tujuan;
                $noRmLama = $payload->no_rekammedik_asal;
                $command = Yii::$app->db->createCommand('SELECT * FROM sp_merge_rm(:xrm_tujuan, :xrm_lama, :xuser_id)')
                ->bindParam(':xrm_tujuan', $noRmTujuan)
                ->bindParam(':xrm_lama', $noRmLama)
                ->bindParam(':xuser_id', $loginpemakai_id);
                $query = $command->queryAll();
                if($query[0]['message'] && $query[0]['message'] == self::SUCCESS) {
                    // $modelPasienAwal = self::getModelPasien()
                    // ->where(['pasien_id' => $pasienAwal['pasien_id']])
                    // ->one();

                    // $modelPasienTujuan = self::getModelPasien()                    
                    // ->where(['pasien_id' => $pasienTujuan['pasien_id']])
                    // ->one();

                    // if(!empty($modelPasienTujuan->is_mergerm)) {
                    //     $tmpMergeTujuan = json_decode($modelPasienTujuan->is_mergerm, true);
                    // } else {
                    //     $tmpMergeTujuan[] = $modelPasienAwal->pasien_id;
                    // }

                    // if(!empty($modelPasienAwal->is_mergerm)) {
                    //     $tmpMergeAwal[] = json_decode($modelPasienAwal->is_mergerm, true);
                    // }

                    // if(!empty($tmpMergeAwal)) {
                    //     $tmpMergeTujuan = array_merge($tmpMergeTujuan, $tmpMergeAwal);
                    // }

                    // $modelPasienTujuan->is_mergerm = json_encode($tmpMergeTujuan);
                    // if($modelPasienTujuan->save(false)) {
                    //     $modelPasienAwal->is_active = false;
                    //     $modelPasienAwal->is_deleted = true;
                    //     $modelPasienAwal->save(false);
                    // }
                    $transaction->commit();
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                        'text' => 'No rekam medik berhasil di gabungkan.',
                    ]);
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => $query[0]['message']
                    ]);
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Data Tidak Ditemukan.'
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/',$e->getMessage(),$out);
            $message = $e->getMessage();
            if (isset($out[1])) {
                $message = 'Query :' . $out[1];
            }
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}