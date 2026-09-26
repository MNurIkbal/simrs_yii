<?php
/* Author: Ardi */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\JadwalPoliklinik;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\CetakJadwalPoliView;
use app\modules\v1\models\JadwalBukaPoliView;
use app\modules\v1\models\Shift;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;

class InfJadwalBukaPoliController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\jadwal-poliklinik';

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
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'ruangan_m');
        $model = new JadwalPoliklinik;
        $query = $model::find()
            ->where(['jadwalbukapoli_m.is_deleted'=>false,'jadwalbukapoli_m.is_active'=>true])
            ->joinWith(['ruangan' => function($query){
                $query->select(['ruangan_m.ruangan_id', 'ruangan_m.ruangan_nama']);
            }]);

        // $query = DocoRestActiveFilter::advancedFilter($model, $query);


        $filter = $request->get('filters', []);
        if(isset($filter['ruangan_id']) && strlen($filter['ruangan_id'])){
            $query->andWhere('jadwalbukapoli_m.ruangan_id = :ruangan_id',[':ruangan_id'=>$filter['ruangan_id']]);
        }
        if(isset($filter['jam_mulai']) && strlen($filter['jam_mulai'])){
            $query->andWhere('jadwalbukapoli_m.jam_mulai >= :jam_mulai',[':jam_mulai'=>$filter['jam_mulai']]);
        }
        if(isset($filter['jam_tutup']) && strlen($filter['jam_tutup'])){
            $query->andWhere('jadwalbukapoli_m.jam_tutup <= :jam_tutup',[':jam_tutup'=>$filter['jam_tutup']]);
        }
        return [
            'data'=>$query->asArray()->all(),
            'totalCount'=>$query->count(),
            'countPoli'=>$query->select('jadwalbukapoli_m.ruangan_id')->distinct()->count(),
        ];
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);
    }

    public function actionGetRuangan()
    {
        $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'kelompokjabatan_m,indexing_m');
        
        $model = new Ruangan;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }    
    /**
    * @controller actionPrintPdf 
    * @attribute #table_detail# => table 
    **/
    public function actionPrintPdf()
    {
        $request = Yii::$app->request;
        $filter='';
        $data = json_decode(urldecode($_POST['col_data']),TRUE);
        $filter = json_decode(urldecode($_POST['col_filter']),TRUE);
        $result = ['data'=>$data,'filter'=>$filter];
        $print = new DocoPrint();            
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('index',$result),
        ];
        $print->Output();

    }

    /**
     * @todo Fungsi untuk mendapatkan data jadwal poliklinik
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataJadwalPoliklinik()
    {
        $request = Yii::$app->request;
        $model = new JadwalBukaPoliView;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        
        $query->andWhere(['!=', 'ruangan_id', 0]);
        $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RJ]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @todo Fungsi untuk mendapatkan data options
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetOptions($instalasi_id=null,$singkatan=null)
    {
        $data = Ruangan::find()->joinWith('instalasi');
        $data->where(['ruangan_m.is_active' => true]);
        $data->orderBy('ruangan_m.ruangan_nama');
        if ($instalasi_id) {
            $data->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
        }

        if ($singkatan) {
            $data->andWhere(['instalasi_m.instalasi_singkatan' => $singkatan]);
        }

        $result = Lookup::find()->where([
            'lookup_type' => 'hari',
            'is_active' => true
        ])->orderBy([
            'lookup_urutan' => SORT_ASC
        ])->all();

        $que_shift = "
            SELECT 
                shift_id,
                shift_nama,
                to_char(shift_jamawal,'HH24:SS')as shift_jamawal, 
                to_char(shift_jamakhir,'HH24:SS')as shift_jamakhir
            FROM shift_m
            ORDER BY shift_nama ASC
        ";
        $data_shift = Yii::$app->db->createCommand($que_shift)->queryAll();

        return [
            'hari' => ArrayHelper::map($result,'lookup_id','lookup_name'),
            'ruangan' => ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama'),
            'shift' => $data_shift,
            'konfig' => KonfigSystem::find()->limit(1)->one(),
        ];
    }

    /**
     * @todo Fungsi untuk export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_title = 'Jadwal Poliklinik';
    public function actionExportExcel($ruangan_id=null)
    {
        $model = new JadwalBukaPoliView;
        $query = $model::find(true);
        $ruangan = '';
        $hari = '';
        $shift = '';
        $is_active = '';

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_id'];
                $ruangan = Ruangan::findOne($ruangan_id);
            }

            if (isset($_GET['advanced-filter']['hari'])) {
                $hari_id = $_GET['advanced-filter']['hari'];
                $hari = Lookup::findOne($hari_id);
            }

            if (isset($_GET['advanced-filter']['shift_id'])) {
                $shift_id = $_GET['advanced-filter']['shift_id'];
                $shift = Shift::findOne($shift_id);
            }

            if (isset($_GET['advanced-filter']['is_active'])) {
                $is_active = $_GET['advanced-filter']['is_active'];

                if ($is_active == true) {
                    $is_active = Yii::t('app', 'Aktif');
                } else {
                    $is_active = Yii::t('app', 'Tidak Aktif');
                }
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $result = $dataProvider->getModels();

        $konfigKuota = KonfigSystem::find()->limit(1)->one();
        $konfigKuota = $konfigKuota['kuota_antrian'];
        $konfigKuotaPoli = DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK;
        
        $header = array(
            Yii::t('app', "Nama Ruangan") => $ruangan ? $ruangan->ruangan_nama : '',
            Yii::t('app', "Hari") => $hari ? $hari->lookup_value : '',
            Yii::t('app', "Shift") => $shift ? $shift->shift_nama : '',
            Yii::t('app', "Status") => $is_active,
        );

        $result_data = [];
        foreach ($result as $k => $v) {
            $data['Nama Ruangan'] = $v['ruangan_nama'];
            $data['Hari'] = $v['hari_nama'];
            $data['Shift'] = $v['shift_nama'];
            $data['Waktu Pelayanan'] = date('H:i', strtotime($v['jam_mulai'])).' - '.date('H:i', strtotime($v['jam_tutup']));
            $data['Status'] = $v['is_active'] == true ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');

            if ($konfigKuota == $konfigKuotaPoli) {
                $data['Kuota Offline'] = $v['maxantrian_poli'];
                $data['Kuota Online'] = $v['kuota_online'];
            }

            $result_data[] = $data;
        }
        $filePath = DocoHelpers::exportExcel($this->_title, $result_data, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionCetakPdf 
    * @attribute #title# => title 
    * @attribute #jadwal_poli# => table 
    **/
    public function actionCetakPdf()
    {
        try {
            $request = Yii::$app->request;
            $model = new JadwalBukaPoliView;

            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();

            foreach ($data as $k => $v) {
                $data[$k]['jam_mulai'] = date('H:i', strtotime($v['jam_mulai']));
                $data[$k]['jam_tutup'] = date('H:i', strtotime($v['jam_tutup']));
            }


            $print = new DocoPrint();    
            $print->attributes = [
                '#title#' => Yii::t('app', 'Jadwal Poliklinik'),
                '#jadwal_poli#' => $this->renderPartial('index', [
                    'detail' => $data,
                    'konfigKuota' => KonfigSystem::find()->limit(1)->one(),
                    'konfigKuotaPoli' => DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK,
                ])
            ];
            $print->Output();
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
}