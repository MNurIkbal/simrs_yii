<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-30 19:10:50
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-31 14:58:38
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\HasilPemeriksaanRadView;
use app\modules\v1\models\HasilBridgingRad;
use app\modules\v1\models\HasilPemeriksaanRad;
use app\modules\v1\models\ExpertiseView;
use app\modules\v1\models\PasienMasukPenunjangT;
use SirsCore\models\PasienMasukPenunjang;
use Doco\Services\Cache;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoMessages;
use Doco\Services\InternalService;

class ExpertiseController extends DocoActiveController
{
    const ID_TINDAKANPELAYANAN = 'tindakanpelayanan_id';
    const IS_ACTIVE = 'is_active';
    const ID_PASIENMASUKPENUNJANG = 'pasienmasukpenunjang_id';
    const NO_HASIL = 'no_hasilrad';
    const TGL_HASIL = 'tgl_hasilrad';
    const IDX_MESSAGE = 'message';
    public $modelClass = 'app\modules\v1\models\InfoPasienRadView';

    public $messageBroker = [
        'save-expertise' => [
            'services' => [
                'Jivex' => [
                    'OrderResults' => [
                        'query_params' => ['penunjang_id'],
                        'payload' => ['hasilpemeriksaanrad_id']
                    ]
                ]
            ]
        ],
    ];

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

    public function actionGetHasil($id)
    {
        $query = HasilPemeriksaanRadView::find()->where(['hasilpemeriksaanrad_id'=>$id, 'is_active' => true])->asArray()->one();
        $queryBridging = HasilBridgingRad::find()->select(['image_link', 'obv_value_html'])->where(['like', 'order_no', '-'.$query['tindakanpelayanan_id']])->orderBy(['hasilbridgingradiologi_id' => SORT_DESC])->asArray()->one();
        $konfigSystem = Cache::getKonfigSistem();
        $pasienMasukPenunjangId = ArrayHelper::getValue($query, 'pasienmasukpenunjang_id');
        $pasienPenunjang = [];
        if(!empty($pasienMasukPenunjangId)) {
            $pasienPenunjang = Yii::$app->db->createCommand("SELECT 
            pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran
            FROM pasienmasukpenunjang_t 
            JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = {$pasienMasukPenunjangId}")->queryOne();
        }

        return [
            'queryBridging' => $queryBridging,
            'query' => $query,
            'konfigSystem' => $konfigSystem,
            'pasienPenunjang' => $pasienPenunjang
        ];
    }
    public function actionGetExpertise($id)
    {
        $user_login = Yii::$app->jwt->user->pegawai_id;
        $query = ExpertiseView::find()->where(['pemeriksaanrad_id'=>$id]);
        $query = $query->andWhere(['OR', ['pegawai_id' => $user_login], ['pegawai_id' => null]])->asArray()->all();
        
        return $query;
    }
    public function actionSaveExpertise()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $post = $request->post();
        $isVerifikasi = ArrayHelper::getValue($get, 'is_verifikasi');
        $penunjang_id = ArrayHelper::getValue($get, 'penunjang_id');
        $daftartindakan_id = ArrayHelper::getValue($get, 'daftartindakan_id');
        $tgl_hasilrad = isset($post['tgl_hasilrad']) ? date('Y-m-d H:i:s', strtotime($post['tgl_hasilrad'])) : null;
        $hasilpemeriksaanrad_id = isset($post['hasilpemeriksaanrad_id']) ? $post['hasilpemeriksaanrad_id'] : null;
        unset($post['hasilpemeriksaanrad_id']);
        $getHasilRad = HasilPemeriksaanRadView::find()->where(['hasilpemeriksaanrad_id' => $hasilpemeriksaanrad_id])->one();
        if($getHasilRad->status_periksa == DocoConstants::ST_SELESAI){
            $getHasilSebelumnya = HasilPemeriksaanRad::find()->where([
                'hasilpemeriksaanrad_id' => $hasilpemeriksaanrad_id, 
                'is_deleted' => false
            ])->asArray()->one();
            
            unset($getHasilSebelumnya['hasilpemeriksaanrad_id']);
            HasilPemeriksaanRad::updateAll([
                'is_deleted' => true, 
                'is_active' => false, 
                'last_modified_date' => date('Y-m-d H:i:s')], 
                'hasilpemeriksaanrad_id = '.$hasilpemeriksaanrad_id.' AND is_deleted = false'
            );
            unset($getHasilSebelumnya['created_date']);
            unset($getHasilSebelumnya['last_modified_date']);
            $model = new HasilPemeriksaanRad;
            $model->attributes = $getHasilSebelumnya;
        }else{
            $model = HasilPemeriksaanRad::findOne($hasilpemeriksaanrad_id);
        }
        $model->attributes = $post;
        if($getHasilRad->status_periksa == DocoConstants::ST_SELESAI) {
            $model->tgl_hasilrad = !empty($tgl_hasilrad) ? $tgl_hasilrad : $model->tgl_hasilrad;
        }
        else {
            $model->tgl_hasilrad = !empty($model->tgl_hasilrad) ? date('Y-m-d H:i:s', strtotime($model->tgl_hasilrad)) : $tgl_hasilrad;
        }
        if($model->save()){
            // Verifikasi expertise
            if ($isVerifikasi) {
                $verifikasi = $this->verifikasiExpertise($penunjang_id, $hasilpemeriksaanrad_id, $daftartindakan_id);
                if (!$verifikasi['success']) {
                    throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => $verifikasi['message']
                    ]);
                }
            }

            return [
                'id_hasil' => $model->hasilpemeriksaanrad_id
            ];
        }
    }

    public function actionGetRiwayatExpertise()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new HasilPemeriksaanRadView;
            $query = $model::find(true);
            $query->where([self::ID_TINDAKANPELAYANAN => $get[self::ID_TINDAKANPELAYANAN]]);
            $query->andWhere([self::ID_PASIENMASUKPENUNJANG => $get[self::ID_PASIENMASUKPENUNJANG]]);
            $query->andWhere([self::IS_ACTIVE => false]);
            $query->andWhere(['NOT', [self::TGL_HASIL => null]]);
            $query->orderBy([self::TGL_HASIL => SORT_DESC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider(['query' => $query]);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                self::IDX_MESSAGE => $e->getMessage()
            ];
        }
    }

    /**
     * Untuk melakukan verifikasi expertise berdasarkan tindakan pemeriksaan
     * @param int $penunjang_id
     * @param int $hasilpemeriksaanrad_id
     * @param int $daftartindakan_id
     * @return array $result
     */
    private function verifikasiExpertise($penunjang_id, $hasilpemeriksaanrad_id, $daftartindakan_id)
    {
        $result = [];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $now = date('Y-m-d H:i:s');
        try{
            if (empty($hasilpemeriksaanrad_id) || empty($penunjang_id)) {
                $result = [
                    'success' => false,
                    'message' => 'Tidak bisa melakukan verifikasi, dikarenakan belum ada Upload Hasil Scan atau Ambil Foto'
                ];
                return $result;
            }
            
            $modelhasilExpertise = HasilPemeriksaanRad::find()
                            ->findByPenunjangId($penunjang_id)
                            ->findByHasilId($hasilpemeriksaanrad_id);

            if(!empty($daftartindakan_id)) {
                $modelhasilExpertise->findByTindakanId($daftartindakan_id);
            }
            $modelhasilExpertise = $modelhasilExpertise->count();
            if (empty($modelhasilExpertise)) {
                $result = [
                    'success' => false,
                    'message' => 'Data pemeriksaan tidak ditemukan'
                ];
                return $result;
            }

            $connection->createCommand("
                UPDATE 
                    hasilpemeriksaanrad_t 
                SET 
                    tgl_verifikasi = '$now'
                WHERE
                    hasilpemeriksaanrad_id = {$hasilpemeriksaanrad_id}
            ")->execute();

            $countHasilVerif = HasilPemeriksaanRadView::find()
                            ->findByPenunjangId($penunjang_id)
                            ->findByTglVerifNotNull(null)
                            ->count();

            $countHasil = HasilPemeriksaanRadView::find()
                            ->findByPenunjangId($penunjang_id)
                            ->count();

            if ($countHasilVerif == $countHasil) {
                $modelPenunjang = PasienMasukPenunjangT::findOne($penunjang_id);
                $modelPenunjang->status_periksa = DocoConstants::ST_SELESAI;
                $modelPenunjang->save();
            }

            $transaction->commit();
            $result = [
                'success' => true,
                'message' => 'Berhasil melakukan verifikasi'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (ValidationException $e) {
            $transaction->rollBack();
            $this->logError($e);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $result;
    }

    public function actionGetHasilRadiologi($id)
    {
        $query = HasilPemeriksaanRadView::find()->where(['hasilpemeriksaanrad_id'=>$id, 'is_active' => true])->asArray()->one();
        $tindakanPelayananId = ArrayHelper::getValue($query, 'tindakanpelayanan_id');
        $pasienMasukPenunjangId = ArrayHelper::getValue($query, 'pasienmasukpenunjang_id');
        $pasienPenunjang = PasienMasukPenunjangT::findOne($pasienMasukPenunjangId);
        $queryBridging = HasilBridgingRad::find()
            ->select(['image_link', 'obv_value_html'])
            ->where(['like', 'order_no', '-'.$tindakanPelayananId])
            ->orderBy(['hasilbridgingradiologi_id' => SORT_DESC])
            ->asArray()->one();
        
        (new InternalService)->sendTo([
            'InaBroker' => [
                'CreateHasilBridging' => [
                    'tindakan_id' => $tindakanPelayananId,
                    'no_masukpenunjang' => ArrayHelper::getValue($pasienPenunjang, 'no_masukpenunjang'),
                    'pendaftaran_id' => ArrayHelper::getValue($pasienPenunjang, 'pendaftaran_id'),
                    'daftartindakan_id' => ArrayHelper::getValue($query, 'daftartindakan_id'),
                    'pasienpenunjang_id' => $pasienMasukPenunjangId,
                ]
            ]
        ]);

        return $queryBridging;
    }
}
