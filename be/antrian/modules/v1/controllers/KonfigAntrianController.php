<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-14 17:56:19
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\KonfigSystemDetail;
use app\modules\v1\models\LayarAntrian;
use app\modules\v1\models\LayarAntrianDetail;
use app\modules\v1\models\Lookup;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;

class KonfigAntrianController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\KonfigSystem';

    /**
     * @todo Verbs function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /**
     * @todo Actions function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    /**
     * @todo Fungsi untuk mendapatkan konfig kuota antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetBundleData()
    {
        $konfig = KonfigSystem::find()->limit(1)->one();

        if ($konfig->is_slider == 0) {
            $konfigDetail = KonfigSystemDetail::find()
            ->where(['is_foto' => true])
            ->all();
        } else {
            $konfigDetail = KonfigSystemDetail::find()
            ->where(['is_foto' => false])
            ->all();
        }

        $lookup = Lookup::find()->where([
            'lookup_type' => DocoConstants::VAR_LOOKUP_TYPE_KUOTA_ANTRIAN
        ])->orderBy([
            'lookup_kode' => SORT_ASC
        ])->all();

        return [
            'konfig' => $konfig,
            'slides' => $konfigDetail,
            'listKuotaAntrian' => $lookup,
        ];
    }

    /**
     * @todo Fungsi untuk mengubah konfig kuota antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUbahKonfigAntrian()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $post = \Yii::$app->request->post('post');
        $model = KonfigSystem::find()->limit(1)->one();
        $detail = array();

        if (isset($post['is_banyakloket'])) {
            $maxLoket = $post['is_banyakloket'] == true ? 8 : 4;
            $idJenisAntrian = DocoConstants::VAR_JA_PD;

            if ($model->is_banyakloket != $post['is_banyakloket']) {
                $sql = "
                    SELECT
                        layarantriandetail_m.layarantrian_id,
                        COUNT(*) AS jumlah_loket
                    FROM
                        layarantriandetail_m
                    LEFT JOIN layarantrian_m ON layarantrian_m.layarantrian_id = layarantriandetail_m.layarantrian_id
                    WHERE
                        layarantriandetail_m.is_deleted = FALSE
                    AND layarantrian_m.jenisantrian_id = '{$idJenisAntrian}'
                    GROUP BY
                        layarantriandetail_m.layarantrian_id;
                ";

                $layarAntrianDetail = Yii::$app->db->createCommand($sql)->queryall();

                if (!empty($layarAntrianDetail)) {
                    foreach ($layarAntrianDetail as $value) {
                        if ($value['jumlah_loket'] > $maxLoket) {
                            $message = Yii::t(
                                'app',
                                'Jumlah loket pada layar antrian pendaftaran lebih besar dari jumlah loket ditampilkan.'
                            );
                            $model->addError(
                                'is_banyakloket',
                                $message
                            );
                            $result['status'] = 422;
                            $result['data'] = $model->errors;

                            return $result;
                        }
                    }
                }
            }
        }
        if ($model) {
            $model->kuota_antrian = $post['kuota_antrian'];
            $model->header = $post['header'];
            $model->header_detail = $post['header_detail'];
            $model->footer = $post['footer'];
            $model->is_slider = $post['is_slider'];
            $model->url_slider = $post['url_slider'];
            $model->is_banyakloket = $post['is_banyakloket'];
            $model->is_keteranganpasien = $post['is_pilihketeranganpasien'];
            $model->is_nourut = $post['is_nourut'];
            $model->is_pisah_cabar = $post['is_pisah_cabar'];
            if (array_key_exists('path_logoheader', $post)) {
                $model->path_logoheader = $post['path_logoheader'];
            }

            if ($model->is_slider == 0) {
                $isFoto = true;
            } else {
                $isFoto = false;
            }

            if ($model->save()) {
                if (array_key_exists('slides', $post) && is_array($post['slides'])) {
                    foreach ($post['slides'] as $slide) {
                        $detail[] = [
                            'konfigsystem_id' => 1,
                            'file' => $slide,
                            'is_foto' => $isFoto
                        ];
                    }
                }

                if (!empty($detail)) {
                    KonfigSystemDetail::batchInsert($detail);
                }

                $transaction->commit();
                $result['title'] = Yii::t('app', 'Proses Berhasil.');
                $result['text'] = Yii::t('app', 'Data berhasil disimpan.');

                $data['autirefresh_layarantrian'] = true;
                $mode = Yii::$app->params['mode'];
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'display-antrian-'.$mode,
                    'message' => json_encode(['data' => $data])
                ]);
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['title'] = Yii::t('app', 'Proses Gagal.');
                $result['text'] = Yii::t('app', 'Data gagal disimpan.');
            }
        } else {
            $transaction->rollBack();
            $result['status'] = 422;
            $result['title'] = Yii::t('app', 'Proses Gagal.');
            $result['text'] = Yii::t('app', 'Data gagal disimpan.');
        }

        return $result;
    }

    /**
     * @todo Fungsi untuk hapus logo header
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionHapusLogoHeader()
    {        
        $model = KonfigSystem::find()->one();
        $model->path_logoheader = null;

        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @todo Fungsi untuk hapus slideshow
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionHapusSlideshow()
    {
        $id = Yii::$app->request->get('id');
        
        if ($id) {
            KonfigSystemDetail::deleteAll(['konfigsystemdetail_id' => $id]);

            return true;
        }

        return false;
    }
}
