<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-18 11:53:06
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\HistoryTempatTidurView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\MasterKamarRuanganView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Ruangan;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;

use Yii;
use yii\data\ActiveDataProvider;

class HistoryTempatTidurController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\HistoryTempatTidurView';

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
        return $actions;
    }

    /**
     * @todo Fungsi untuk mendapatkan data bundle
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataBundle()
    {
        $listRequestMaster = [
            'ruangan' => ['Ruangan', ['instalasi_id' => DocoConstants::INST_ID_RI]],
            'kamar' => 'KamarRuangan',
        ];

        $listRequestLookup = [
            'keterangan_history_tt'
        ];

        $master = $this->getListMaster($listRequestMaster);
        $lookup = $this->listLookup($listRequestLookup);
        
        $result = [
            'master' => $master,
            'lookup' => $lookup,
        ];

        return $result;
    }

    /**
     * @todo Fungsi untuk mendapatkan list data pasien malnutrisi
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataHistoryTempatTidur()
    {
        $request = Yii::$app->request;

        $model = new HistoryTempatTidurView;
        $query = $model::find();

        if(isset($_GET['advanced-filter']['tgl_tthistory']) && $_GET['advanced-filter']['tgl_tthistory'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_tthistory']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                $query->andWhere(['between', 'tgl_tthistory', $start, $end]);
            }
            unset($_GET['advanced-filter']['tgl_tthistory']);
        } else {
            $start = date('Y-m-d 00:00:00', strtotime('NOW'));
            $end = date('Y-m-d 23:59:59', strtotime('NOW'));

            $query->andWhere(['between', 'tgl_tthistory', $start, $end]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #periode# => periode
    * @attribute #pj_ruangan# => pj_ruangan
    * @attribute #waktu_dicetak# => waktu_dicetak
    * @attribute #table# => table
    **/
    public function actionExportPdf()
    {
        $model = new HistoryTempatTidurView;
        $query = $model::find();

        if (isset($_GET['ruangan_id']) && $_GET['ruangan_id'] != '') {
            $pegawai = PegawaiView::find()->where(['ruangan_id' => $_GET['ruangan_id'], 'jabatan_id' => DocoConstants::VAR_J_K_R])->one();
        } else {
            $pegawai = [];
        }

        if(isset($_GET['advanced-filter']['tgl_tthistory']) && $_GET['advanced-filter']['tgl_tthistory'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_tthistory']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                $query->andWhere(['between', 'tgl_tthistory', $start, $end]);
            }
            unset($_GET['advanced-filter']['tgl_tthistory']);
        } else {
            $start = date('Y-m-d 00:00:00', strtotime('NOW'));
            $end = date('Y-m-d 23:59:59', strtotime('NOW'));

            $query->andWhere(['between', 'tgl_tthistory', $start, $end]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query = $query->asArray()->all();

        if (!empty($query)) {
            foreach ($query as $key => $value) {
                $query[$key]['tgl_tthistory'] = DocoHelpers::convDateTime($value['tgl_tthistory'], false, true);
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => DocoHelpers::convDateTime($start, false, false).' - '.DocoHelpers::convDateTime($end, false, false),
            '#pj_ruangan#' => isset($pegawai['nama_pegawai']) ? $pegawai['nama_pegawai'] : '',
            '#waktu_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, false),
            '#table#' => $this->renderPartial('pdf', [
                'data' => $query,
            ]),
        ];
        $print->Output();
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new HistoryTempatTidurView;
        $query = $model::find();
        $title = Yii::t('app', 'History Master Tempat Tidur');

        if(isset($_GET['advanced-filter']['tgl_tthistory']) && $_GET['advanced-filter']['tgl_tthistory'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_tthistory']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                $query->andWhere(['between', 'tgl_tthistory', $start, $end]);
            }
            unset($_GET['advanced-filter']['tgl_tthistory']);
        } else {
            $start = date('Y-m-d 00:00:00', strtotime('NOW'));
            $end = date('Y-m-d 23:59:59', strtotime('NOW'));

            $query->andWhere(['between', 'tgl_tthistory', $start, $end]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query = $query->asArray()->all();

        if (!empty($query)) {
            foreach ($query as $key => $value) {
                $query[$key]['tgl_tthistory'] = DocoHelpers::convDateTime($value['tgl_tthistory'], false, true);
            }
        }

        $header = [];
        $footer = [];
        $result = [];
        $options = ["subTitle" => "Periode ".DocoHelpers::convDateTime($start, false, false).' - '.DocoHelpers::convDateTime($end, false, false)];

        if (!empty($query)) {
            foreach ($query as $key => $value) {
                $newValue = [];
                $newValue[\Yii::t('app', 'Tanggal')] = $value['tgl_tthistory'];
                $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
                $newValue[\Yii::t('app', 'Kamar')] = $value['kamarruangan_nokamar'];
                $newValue[\Yii::t('app', 'No. Tempat Tidur')] = $value['no_tempattidur'];
                $newValue[\Yii::t('app', 'Keterangan')] = $value['keterangan'];
                // $newValue[\Yii::t('app', 'Status')] = $value['status'] ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
                $result[$key] = $newValue;
            }
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    /**
     * @todo Action untuk mendapatkan ruangan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetRuangan()
    {
        $request = Yii::$app->request;
        $kamarruangan_id = $request->get('kamarruangan_id', null);
        $model = new MasterKamarRuanganView;
        $query = $model::find()->select('ruangan_id, ruangan_nama');

        if ($kamarruangan_id) {
            $query->andWhere(['kamarruangan_id' => $kamarruangan_id]);
        }

        $query->groupBy('ruangan_id, ruangan_nama');

        return $query->asArray()->all();
    }

    /**
     * @todo Action untuk mendapatkan kamar ruangan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetKamarRuangan()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        $model = new MasterKamarRuanganView;
        $query = $model::find();

        if ($ruangan_id) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        return $query->asArray()->all();
    }

    /**
     * @todo Action untuk medapatkan list master
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getListMaster(array $listRequest)
    {
        $results = [];

        foreach ($listRequest as $key=>$request) {
            $class = "app\modules\\v1\models\\" . (is_array($request) ? $request[0] : $request);
            $model = new $class;

            $q = $model->find()->andWhere([
                'is_active'=>true,
                'is_deleted'=>false,
            ]);

            if (is_array($request) && $request[1]) {
                $q->andWhere($request[1]);
            }

            if (is_array($request) && isset($request[2])) {
                $q->orderBy([$request[2] => SORT_ASC]);
            }

            $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
        }

        return $results;
    }

    /**
     * @todo Action untuk medapatkan list lookup
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function listLookup($types){
        $results = [];

        foreach ($types as $key=>$type) {
            $lookup = new Lookup;
            $q = $lookup->find()->where(['lookup_type'=>$type, 'is_active' => true, 'is_deleted' => false]);
            $results[$type] = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP, $q, true, $type);
        }

        return $results;
    }
}