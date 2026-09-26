<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Dede herdiana
 * @Date: 19 November 2021
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use app\modules\v1\models\InfoBatalPembayaranView;

class LapBatalPembayaranController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoBatalPembayaranView';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    protected $_title = "Laporan Batal Pembayaran";

    public function actionIndex()
    {
        $query = $this->getData();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    
    public function actionExportExcel()
    {
        
        $result = [];
        $query = $this->getData();
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Batal')] = !empty($value['tanggal_batal']) ? date('d-m-Y', strtotime($value['tanggal_batal'])) : '';
            $newValue[\Yii::t('app', 'Dibatalkan Oleh')] = $value['dibatalkan_oleh'];
            $newValue[\Yii::t('app', 'No Pembayaran')] = $value['no_pembayaran'];
            $newValue[\Yii::t('app', 'Tanggal Pembayaran')] =  !empty($value['tgl_pembayaran']) ? date('d-m-Y', strtotime($value['tgl_pembayaran'])) : '';
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'] . '-' . $value['no_rm'];
            $newValue[\Yii::t('app', 'No Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t("app", "Jumlah Tagihan")] = $value['jumlah_tagihan'];
            $newValue[\Yii::t("app", "Alasan Batal")] = $value['alasan_batal'];
            $result[$key] = $newValue;
        }

        $header = [];
        $footer = [];
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    protected function getData()
    {
        $model = new InfoBatalPembayaranView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tanggal_batal'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal_batal']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tanggal_batal']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['between', 'tanggal_batal', $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }
}
