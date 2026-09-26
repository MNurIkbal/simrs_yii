<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-06 11:49:01
 */

namespace app\modules\v1\controllers;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Yii;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\InfoPasienBpjsView;
use app\modules\v1\models\PendaftaranView;
use app\modules\v1\models\InfoPasienRsKoreksiView;

class InformasiPasienBpjsController extends DocoActiveController
{
    /**
     * @todo Variabel penampung model class
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\InfoPasienBpjsView';

    /**
     * @todo Fungsi untuk unset verbs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /**
     * @todo Fungsi untuk unset actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan rajal
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKunjunganRajal()
    {
        $model = new InfoPasienBpjsView;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);

                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->andWhere(['jenis' => DocoConstants::INSTALASI_RAWAT_JALAN_DARURAT]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan ranap
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKunjunganRanap()
    {
        $request = Yii::$app->request->get();
        $model = new InfoPasienBpjsView;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            $_GET['advanced-filter'] = json_decode($_GET['advanced-filter'], true);
            if (isset($_GET['advanced-filter']['status_verifikasi'])) {
                $status_verifikasi = (int) $_GET['advanced-filter']['status_verifikasi'];
                $query->where(['status_verifikasi' => $status_verifikasi]);
                unset($_GET['advanced-filter']['status_verifikasi']);
            }
            if (isset($_GET['advanced-filter']['no_pendaftaran'])) {
                $no_pendaftaran = $_GET['advanced-filter']['no_pendaftaran'];
                $query->where(['no_pendaftaran' => $no_pendaftaran]);
                unset($_GET['advanced-filter']['no_pendaftaran']);
            }
            if (isset($_GET['advanced-filter']['no_rekam_medik'])) {
                $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                $query->where(['no_rekam_medik' => $no_rekam_medik]);
                unset($_GET['advanced-filter']['no_rekam_medik']);
            }
            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruangan_id = (int) $_GET['advanced-filter']['ruangan_id'];
                $query->where(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }
            if (isset($_GET['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                $query->where(['nama_pasien' => $nama_pasien]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_pendaftaran', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }
            if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }
        }
        $query->andWhere(['jenis' => 'RI']);
        // $query->andWhere(['ILIKE', 'LOWER(no_pendaftaran)', 'RI']);
        $query->andWhere(['between', 'tglpasienpulang', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDataKunjunganRajalBpjs() 
    {
        $request = Yii::$app->request;
        $model = new InfoPasienRsKoreksiView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($_GET['advanced-filter'])) {
            $_GET['advanced-filter'] = json_decode($_GET['advanced-filter'], true);
            if (isset($_GET['advanced-filter']['status_verifikasi'])) {
                $status_verifikasi = (int) $_GET['advanced-filter']['status_verifikasi'];
                $query->where(['status_verifikasi' => $status_verifikasi]);
                unset($_GET['advanced-filter']['status_verifikasi']);
            }
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_pendaftaran', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }
            if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }
        }
        $query->andWhere(['jenis_rawat' => DocoConstants::INSTALASI_RAWAT_JALAN_DARURAT]);
        $query->andWhere(['between', 'tglpasienpulang', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
