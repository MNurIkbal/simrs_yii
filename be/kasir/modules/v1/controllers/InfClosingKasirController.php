<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Shift;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoClosingKasirView;
use app\modules\v1\models\InfoClosingKasirDetailView;
use app\modules\v1\models\InfoClosingKasirHeaderView;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class InfClosingKasirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoClosingKasirHeaderView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoClosingKasirHeaderView;
        $query = $model::find(true);
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_closingkasir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_closingkasir']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_closingkasir']);
                $between = true;
            }
        }
        
        $query->andWhere(['between', 'tgl_closingkasir', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionHeader($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirHeaderView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);
        return $query->asArray()->one();
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirDetailView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);
        return $query->asArray()->all();
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);
    }

    public function actionRincianClosing($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);
        return $query->asArray()->all();
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);
    }
    
    public function actionListRequest()
    {
        // instalasi
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();
        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
        ]);

        // shift
        $modelShift = new Shift;
        $queryShift = $modelShift::find();
        $queryShift = DocoRestActiveFilter::advancedFilter($modelShift, $queryShift);
        $queryShift = new ActiveDataProvider([
            'query' => $queryShift,
        ]);

        // pegawai
        $modelPegawai = new Pegawai;
        $queryPegawai = $modelPegawai::find();
        $queryPegawai = DocoRestActiveFilter::advancedFilter($modelPegawai, $queryPegawai);
        $queryPegawai = new ActiveDataProvider([
            'query' => $queryPegawai,
        ]);

        return [
            'ruangan' => $queryRuangan->getModels(),
            'shift' => $queryShift->getModels(),
            'pegawai' => $queryPegawai->getModels(),
        ];
    }

    /**
    * @controller actionCetak
    * @attribute #no_closingkasir# => no closing kasir 
    * @attribute #tgl_closingkasir# => tanggal closing kasir 
    * @attribute #shift_nama# => shift kasir
    * @attribute #nama_pegawai# => nama pegawai
    * @attribute #now# => tgl sekarang
    * @attribute #table# => table 
    **/
    public function actionCetak($id, $nama_ruangan)
    {
        return Yii::$app->docoPlugin->execute('cetak_closing_kasir');
    }

    public function actionExportExcel($id, $nama_ruangan)
    {
        $modelHeader = $this->actionHeader($id);
        $model = new InfoClosingKasirView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);
        $data = $query->asArray()->all();
        $title = "Closing Kasir";
        $options = ["subTitle" => $nama_ruangan];
        $result = [];
        foreach ($data as $key => $value) {
            $value['tgl_pembayaran'] = ArrayHelper::getValue($value,'tgl_pembayaran','');
            $value['no_pendaftaran'] = ArrayHelper::getValue($value,'no_pendaftaran','');
            $value['nama_pasien'] = ArrayHelper::getValue($value,'nama_pasien','');
            $value['carabayar_nama'] = ArrayHelper::getValue($value,'carabayar_nama','');
            $value['penjamin_nama'] = ArrayHelper::getValue($value,'penjamin_nama','');
            $value['metode_pembayaran_nama'] = ArrayHelper::getValue($value,'metode_pembayaran_nama','');
            $value['nama_pegawai'] = ArrayHelper::getValue($value,'nama_pegawai','');
            $value['total_tagihan'] = ArrayHelper::getValue($value,'total_tagihan',0);
            $value['total_tunai'] = ArrayHelper::getValue($value,'total_tunai',0);
            $value['total_nontunai'] = ArrayHelper::getValue($value,'total_nontunai',0);
            $value['total_dijamin'] = ArrayHelper::getValue($value,'total_dijamin',0);
            $value['no_rekam_medik'] = ArrayHelper::getValue($value,'no_rekam_medik');

            $value['tgl_pembayaran'] = date("j M Y H:i:s", strtotime($value['tgl_pembayaran']));
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pembayaran')] = $value['tgl_pembayaran'];
            $newValue[\Yii::t('app', 'Info Pasien')] = $value['no_pendaftaran'].' / '. $value['nama_pasien'] .' / '. $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Cara Bayar - Penjamin')] = $value['carabayar_nama'].' / '. $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Metode Pembayaran Non Tunai')] = $value['metode_pembayaran_nama'];
            $newValue[\Yii::t('app', 'Kasir')] = $value['nama_pegawai'];
            $newValue[\Yii::t('app', 'Total Tagihan (Rp)')] =$value['total_tagihan'];
            $newValue[\Yii::t('app', 'Total Tunai (Rp)')] =$value['total_tunai'];
            $newValue[\Yii::t('app', 'Total Non Tunai (Rp)')] =$value['total_nontunai'];
            $newValue[\Yii::t("app", "Total Dijamin (Rp)")] =$value['total_dijamin'];
            $result[$key] = $newValue;
        }
        $header = [
            Yii::t("app", "Nomor Closing Kasir") => $modelHeader['no_closingkasir'],
            Yii::t("app", "Tanggal Closing Kasir") => date('j M Y H:i:s', strtotime($modelHeader['tgl_closingkasir'])),
            Yii::t("app", "Pegawai Closing") => $modelHeader['nama_pegawai'],
            Yii::t("app", "Shift Kasir") => $modelHeader['shift_nama'],
        ];
        $footer = [];
        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, $footer, [], true);
        $filePath->save('php://output');
        die;
    }
}
