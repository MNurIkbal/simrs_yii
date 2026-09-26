<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-21 11:46:02
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Lookup;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;

class KonfigPendaftaranController extends DocoActiveController
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
     * @todo Fungsi untuk mendapatkan konfig sistem
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKonfigSystem()
    {
        $konfig = KonfigSystem::find()->limit(1)->one();

        return $konfig;
    }

    /**
     * @todo Fungsi untuk mengubah konfig kuota antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUbahKonfigPendaftaran()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $post = \Yii::$app->request->post();
        $model = KonfigSystem::find()->limit(1)->one();

        if (!$model->load($post, '')) {
            $errors = DocoHelpers::parseError($model->errors, 'KonfigPendaftaranForm');
            return [
                'status' => 422,
                'data' => $errors
            ];
        }
        if (!$model->validate()) {
            $errors = DocoHelpers::parseError($model->errors, 'KonfigPendaftaranForm');
            return [
                'status' => 422,
                'data' => $errors
            ];
        }

        if ($model->save()) {
            $transaction->commit();
            Yii::$app->cache->delete(DocoConstants::VAR_K_S);
            $result['title'] = Yii::t('app', 'Proses Berhasil.');
            $result['text'] = Yii::t('app', 'Data berhasil disimpan.');
        } else {
            $transaction->rollBack();
            $result['status'] = 422;
            $result['title'] = Yii::t('app', 'Proses Gagal.');
            $result['text'] = Yii::t('app', 'Data gagal disimpan.');
        }

        return $result;
    }

    public function actionHapusLogoHeader()
    {        
        $model = KonfigSystem::find()->one();
        $model->dash_logo = null;

        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }
}