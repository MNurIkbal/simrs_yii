<?php

/**
 * @Author: Ikhwanu Arriyadh T
 * @Date:   2022-02-11 17:42:34
 */

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\components\BpjsController;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\BpjsView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\PasienPulangRekapan;
use app\modules\v1\models\RencanaKontrolT;
use app\modules\v1\models\RencanaKontrolView;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\UpdateTanggalPulangView;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoMessages;
use Doco\models\Pasien;
use Doco\models\TindakanPelayanan;
use Doco\models\ObatAlkesPasien;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class UpdateTanggalPulangController extends BpjsController
{
    public $modelClass = 'app\modules\v1\models\PasienPulangT';

    const CARA_PULANG_INACBG_AWAL = 567;
    const CARA_PULANG_INACBG_AKHIR = 571;
    const ERROR_MESSAGE_1 = 'Tidak bisa update tanggal pulang karena pasien belum dipulangkan';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    /**
     * @todo List Pasien ranap bpjs
     * @author  Ikhwanu Arriyadh T
     */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new UpdateTanggalPulangView;
            $advancedFilters = $request->get('advanced-filter', []);
            $query = $model::find();
            $tgl_awal = date('Y-m-d  00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:59');

            if (isset($advancedFilters['tgl_pulang_awal']) && isset($advancedFilters['tgl_pulang_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pulang_awal'];
                $tgl_akhir = $advancedFilters['tgl_pulang_akhir'];
            }
            $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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
    
    /**
     * @todo Get Informasi pasien untuk validasi data
     * @author  Ikhwanu Arriyadh T
     */
    public function actionGetInfoPeserta($bpjs_id = null)
    {
        try {
            $request = Yii::$app->request;
            $bpjs = new Bpjs;
            $model = new UpdateTanggalPulangView;
            $bpjs_id_get = $request->get('bpjs_id', null);
            $set_bpjs_id = null;
            if (!is_null($bpjs_id_get)) {
                $set_bpjs_id = $bpjs_id_get;
            } else {
                $set_bpjs_id = $bpjs_id;
            }
            $rencana = $model::find()
            ->where(['bpjs_id' => $set_bpjs_id]);
            $data = $rencana->asArray()->one();
            $vcalim = $bpjs->referensiCariSep($data['nosep']);
            $can_update = true;
            $error = null;
            if ($data['status_pulang_id'] != DocoConstants::STATUS_RANAP_PULANG) {
                $can_update = false;
                $error = self::ERROR_MESSAGE_1;
            }

            if ($vcalim['metaData']['code'] != '200') {
                $can_update = false;
                $error = $vcalim['metaData']['message'];
            }

            $response = [
                            'data' => $data,
                            'can_update' => $can_update,
                            'Error' => $error,
                        ];

            return  $response;
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
        
    }

    public function actionGetInfoPasien()
    {
        try {
            $request = Yii::$app->request;
            $noSep = $request->get('noSep', null);
            if(!empty($noSep)){
                $bpjsData = Bpjs::find()
                ->where(['nosep' => $noSep])
                ->asArray()->one();
                if(!empty($bpjsData)){
                    return [
                        'data' => $bpjsData,
                        'can_update' => true,
                        'Error' => '',
                    ];
                }else{
                    return [
                        'data' => $bpjsData,
                        'can_update' => false,
                        'Error' => 'Data Tidak Ditemukan.',
                    ];
                }
            }else{
                return [
                    'data' => [],
                    'can_update' => false,
                    'Error' => 'Data Tidak Ditemukan.',
                ];
            }
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }


/**
     * @todo Get Data Keseluruhan mengenai pasien yang dipilih
     * @author  Ikhwanu Arriyadh T
     */
    public function actionGetDataUpdate()
    {
        $request = Yii::$app->request;
        $nosep = $request->get('nosep', null);
        $bpjs_id = $request->get('bpjs_id', null);
        $model = new Bpjs;
        $bpjsData = Bpjs::find()
        // ->where(['nosep' => $nosep])
        ->where(['bpjs_id' => $bpjs_id])
        ->asArray()->one();
        if(!empty($bpjsData)){
            $data_updat_tanggal_pulang = UpdateTanggalPulangView::find()
            ->where(['bpjs_id' => $bpjsData['bpjs_id']])
            ->asArray()->one();

            $data_bpjs = BpjsView::find()
            ->where(['bpjs_id' => $bpjsData['bpjs_id']])
            // ->where(['pasienadmisi_id' => $data_updat_tanggal_pulang['pasienadmisi_id']])
            // ->where(['pendaftaran_id' => $data_updat_tanggal_pulang['pendaftaran_id']])
            // ->where(['is_active' => true])
            // ->where(['is_deleted' => false])
            ->asArray()->one();

            $data_pasien_admisi = PasienAdmisi::find()
            ->where(['pasienadmisi_id' => $data_updat_tanggal_pulang['pasienadmisi_id']])
            // ->where(['is_active' => true])
            // ->where(['is_deleted' => false])
            ->asArray()->one();

            $data_pendaftaran = Pendaftaran::find()
            ->where(['pendaftaran_id' => $data_updat_tanggal_pulang['pendaftaran_id']])
            // ->where(['is_active' => true])
            // ->where(['is_deleted' => false])
            ->asArray()->one();

            $data_pasien_pulang = PasienPulang::find()
            ->where(['pasienpulang_id' => $data_updat_tanggal_pulang['pasienpulang_id']])
            ->where(['pasienadmisi_id' => $data_updat_tanggal_pulang['pasienadmisi_id']])
            ->where(['pendaftaran_id' => $data_updat_tanggal_pulang['pendaftaran_id']])
            // ->where(['is_active' => true])
            // ->where(['is_deleted' => false])
            ->asArray()->one();

            // $data_pasien = Pasien::find()
            // ->where(['pasien_id' => $data_pendaftaran['pasien_id']])
            // ->where(['is_active' => true])
            // ->where(['is_deleted' => false])
            // ->asArray()->one();

            $data_vclaim = $model->referensiCariSep($data_bpjs['nosep']);
            $status_pulang_option = $this->getCaraKeluar()->asArray()->all();

            return [
                'data_updat_tanggal_pulang' => $data_updat_tanggal_pulang,
                'data_bpjs' => $data_bpjs,
                'data_pasien_admisi' => $data_pasien_admisi,
                'data_pendaftaran' => $data_pendaftaran,
                'data_pasien_pulang' => $data_pasien_pulang,
                'data_vclaim' =>  $data_vclaim,
                // 'data_pasien' => $data_pasien,
                'status_pulang_option' => $status_pulang_option,
            ];
        }else{
            return DocoHelpers::responseTemplate(
                422,
                'Data tidak ditemukan.',
                []
            );
        }
    }

    private function getCaraKeluar()
    {
        return Lookup::find()
        ->where(['between', 'lookup_id', self::CARA_PULANG_INACBG_AWAL, self::CARA_PULANG_INACBG_AKHIR])
        ->orderBy(['lookup_name' => SORT_ASC]);
    }


    /**
     * @todo Action Update tanggal pulang
     * @author  Ikhwanu Arriyadh T
     */
    public function actionUpdateTanggalPulang($bpjs_id=null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $tglPasienPulang = !empty($post['tglpasienpulang']) ? date('Y-m-d', strtotime($post['tglpasienpulang'])) : null;
        $tglMeninggal = !empty($post['tgl_meninggal']) ? date('Y-m-d', strtotime($post['tgl_meninggal'])) : null;
        $model = new Bpjs;

        $t_sep_new = $update_pasien_pulang = $rekapan_pasien_pulang = [];
        $carakeluarBpjs = (new DocoConstansId)->actionGetAdditional('cara_pulang_bpjs',true);
        $caraPulangBpjs = isset($carakeluarBpjs[$post['status_pulang_id']]) ? $carakeluarBpjs[$post['status_pulang_id']] : 5;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $t_sep_new['noSep'] = $post['nosep'];
            $t_sep_new['statusPulang'] = $caraPulangBpjs;
            $t_sep_new['noSuratMeninggal'] = $post['status_pulang_id'] == 4 ? $post['no_surat_kematian'] : '';
            $t_sep_new['tglMeninggal'] = $post['status_pulang_id'] == 4 ? date('Y-m-d', strtotime($post['tgl_meninggal'])) : '';
            $t_sep_new['tglPulang'] = date('Y-m-d', strtotime($post['tglpasienpulang']));
            $t_sep_new['noLPManual'] = '';
            $t_sep_new['user'] = $post['user'];
            $model->t_sep_new = $t_sep_new;

            // check tindakan dan obat terkahir pasien
            $tindakanPasien = TindakanPelayanan::find()->where(['pendaftaran_id' => $post['pendaftaran_id']])->asArray()->one();
            $obatPasien = ObatAlkesPasien::find()->where(['pendaftaran_id' => $post['pendaftaran_id']])->asArray()->one();
            $lastTindakanPasien = date('Y-m-d', strtotime($tindakanPasien['tgl_tindakan']));
            $lastObatPasien = date('Y-m-d', strtotime($obatPasien['tglpelayanan']));

            if(!empty($tindakanPasien)){
                if($tglPasienPulang < $lastTindakanPasien){
                    return DocoHelpers::responseTemplate(
                        422,
                        'Tanggal pulang tidak boleh kurang dari tanggal Tindakan terakhir pasien ('. date('d-m-Y', strtotime($lastTindakanPasien)) .').' ,
                        []
                    );
                }
            }

            if(!empty($obatPasien)){
                if($tglPasienPulang < $lastObatPasien){
                    return DocoHelpers::responseTemplate(
                        422,
                        'Tanggal pulang tidak boleh kurang dari tanggal Obat terakhir pasien('. date('d-m-Y', strtotime($lastObatPasien)) .').',
                        []
                    );
                }
            }

            if ($t_sep_new['noSep'] != null && $t_sep_new['tglPulang'] != null) {
                $result = $model->updateTanggalPulangSepNew();
            }
            
            if (($result['metaData']['code'] == 200) || ( $result['metaData']['code'] == '200')) {
                $bpjsData = Bpjs::find()
                ->where(['bpjs_id' => $post['bpjs_id']])->asArray()->one();
                $update_pasien_pulang = Bpjs::find()
                ->where(['bpjs_id' => $post['bpjs_id']])->one();

                $update_pasien_pulang->status_pulang = ArrayHelper::getValue($post,"status_pulang_id", $bpjsData['status_pulang']);

                $temp_date_pulang = ArrayHelper::getValue($post,"tglpasienpulang", $bpjsData['tglpulang']);
                $date_pulang = ($temp_date_pulang != null) ? date('Y-m-d',strtotime($temp_date_pulang)) : null;
                $update_pasien_pulang->tglpulang = $date_pulang;

                $temp_date_meninggal = ArrayHelper::getValue($post,"tgl_meninggal", $bpjsData['tgl_meninggal_bpjs']);
                $date_meinggal = ($temp_date_meninggal != null) ? date('Y-m-d',strtotime($temp_date_meninggal)) : null;
                $update_pasien_pulang->tgl_meninggal_bpjs = $date_meinggal;
                
                $update_pasien_pulang->no_surat_meninggal = ArrayHelper::getValue($post,"no_surat_kematian", $bpjsData['no_surat_meninggal']);
                $update_pasien_pulang->no_lp_manual = ArrayHelper::getValue($post,"no_up_manual", $bpjsData['no_lp_manual']);  

                $model_update = new Bpjs;
                $model_update->attributes = $update_pasien_pulang->attributes;
                if ($update_pasien_pulang->save()) {
                    if ($model_update->save()) {
                        // $bpjsupdate = "
                        //     UPDATE bpjs_t SET is_deleted = true, is_active = false
                        //     WHERE bpjs_id = {$update_pasien_pulang_rekapan->bpjs_id}
                        // ";
                        // $qUpdateBpjs = Yii::$app->db->createCommand($bpjsupdate)->execute();
                        $transaction->commit();
                    }else {
                        $transaction->rollBack();
                        return [
                            'status' => 422,
                            'result' => $result,
                            'model' => $model_update,
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($update_pasien_pulang->errors,'Bpjs');
                    return [
                        'data' => $errors,
                        'status' => 422,
                        'result' => $result,
                        'model' => $update_pasien_pulang,
                    ];
                }
            }
            return [
                'result' => $result,
                'model' => $update_pasien_pulang,
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}