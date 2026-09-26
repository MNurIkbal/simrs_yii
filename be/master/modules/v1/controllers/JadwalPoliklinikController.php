<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-15 15:00
*/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JadwalPoliklinik;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\Shift;
use app\modules\v1\models\CetakJadwalPoliView;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use app\modules\v1\models\JadwalBukaPoliView;
use app\modules\v1\models\KonfigSystemK;
use app\modules\v1\payload\PayloadForm;

class JadwalPoliklinikController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JadwalPoliklinik';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        // $verbs["create"] = ["POST", "GET"];
        // $verbs["view"] = ["POST", "GET"];
        // $verbs["update"] = ["POST", "GET"];
        // $verbs["delete"] = ["DELETE"];
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

    public function actionIndex()
    {

        $request = Yii::$app->request;
        $jadwal_data = JadwalBukaPoliView::find()
            ->andWhere([
                'instalasi_id' => '1'
            ])
            ->orderBy([
            'ruangan_nama'=> SORT_ASC,
            'hari'=> SORT_ASC,
            'jam_mulai'=> SORT_ASC,
            ]);

        if ($ruangan_id = $request->get('ruangan_id')) {
            $jadwal_data->andWhere([
                'ruangan_id' => $ruangan_id
            ]);
        }
        if ($hari = $request->get('hari')) {
            $jadwal_data->andWhere([
                'hari' => $hari
            ]);
        }
        if ($shift_id = $request->get('shift_id')) {
            $jadwal_data->andWhere([
                'shift_id' => $shift_id
            ]);
        }
        if ($jam_mulai = $request->get('jam_mulai')) {
            $jadwal_data->andWhere([
                '>=', 'jam_mulai', $jam_mulai
            ]);
        }
        if ($jam_selesai = $request->get('jam_selesai')) {
            $jadwal_data->andWhere([
                '<=', 'jam_tutup', $jam_selesai
            ]);
        }

        $data = [];
        $list_ruangan = [];
        $temp_list_ruangan = [];
        $no = 1;

         $sql = '
            SELECT kuota_antrian FROM konfigsystem_k
        ';
        $query = Yii::$app->db->createCommand($sql)->queryOne();
        $cek_kuota_antrian = $query['kuota_antrian'];


        foreach ($jadwal_data->asArray()->all() as $value) {
            $resource_id = $value['ruangan_id'].' - '.$value['hari'];
            if(!isset($temp_list_ruangan[$resource_id])){
                $temp_list_ruangan[$resource_id] = true;
                $list_ruangan[] = [
                    'id' => $resource_id,
                    'title' => $no.' - '.$value['ruangan_nama'].' - '.$value['hari_nama'],
                ];
                $no++;
            }
            
            switch ($value['is_active']) {
                case true:
                    $color = 'hsl(145, 82%, 61%)';
                    break;
                case false:
                    $color = 'rgb(224, 224, 224)';  
                    break;
                default:
                    break;
            }

            if(isset($value['shift_nama'])){
                $data_title = $value['shift_nama'] . ' : ' . $value['jam_mulai'] . ' - ' . $value['jam_tutup'];
            }
            else{
                $data_title = $value['jam_mulai'] . ' - ' . $value['jam_tutup'];
            }

            $data[] = [
                'id'                  => DocoHelpers::encrypt($value['jadwalbukapoli_id']),
                'resourceId'          => $resource_id,
                'start'               => $value['jam_mulai'],
                'jam_rencana_mulai'   => $value['jam_mulai'],
                'end'                 => $value['jam_tutup'],
                'jam_rencana_selesai' => $value['jam_tutup'],
                'title'               => $data_title,
                'className'           => 'text-center',
                'label'               => $value['ruangan_nama'],
                'nama_dokter'         => $value['ruangan_nama'],
                'backgroundColor'     => $color,
                'textColor'           => 'rgb(0, 0, 0)',
                'status'              => $value['ruangan_nama'],
                'kuota'               => $value['maxantrian_poli'],
                'kuota_online'        => $value['kuota_online'],
                'cek'                 => $cek_kuota_antrian,
                // 'allDay' => false
            ];
        }

        return [
            'data_ruangan' => $list_ruangan,
            'jadwal_data'  => $data
        ];
    }

    protected $_title = 'Jadwal Poliklinik';
    public function actionExportExcel($ruangan_id=null, $shift_id=null,$hari=null,$jam_mulai=null,$jam_selesai=null)
    {
        $wheres = '';
        if (isset($ruangan_id) && $ruangan_id != 'null' && $ruangan_id != '') {
            $wheres .= " AND jadwalbukapoli_m.ruangan_id = {$ruangan_id}";
            $ruangan = Ruangan::findOne($ruangan_id);
        }
        if (isset($shift_id) && $shift_id != 'null' && $shift_id != '') {
            $wheres .= " AND jadwalbukapoli_m.shift_id = {$shift_id}";
        }

        if (isset($hari) && $hari != 'null' && $hari != '') {
            $wheres .= " AND jadwalbukapoli_m.hari = {$hari}";
        }

        if (isset($jam_mulai) && $jam_mulai != 'null' && $jam_mulai != '') {
            $wheres .= " AND jadwalbukapoli_m.jam_mulai >= '{$jam_mulai}'";
        }

        if (isset($jam_selesai) && $jam_selesai != 'null' && $jam_selesai != '') {
            $wheres .= " AND jadwalbukapoli_m.jam_tutup <= '{$jam_selesai}'";
        }

        $query = "
            SELECT kuota_antrian FROM konfigsystem_k
            ";
        $result = Yii::$app->db->createCommand($query)->queryOne();
        $kuota = $result['kuota_antrian'];
        if($kuota == 597){
            $sql = "
            SELECT 
                jadwalbukapoli_m.ruangan_id,
                ruangan_m.ruangan_nama as Ruangan,
                hari.lookup_name AS Hari,
                shift_m.shift_nama AS Nama_Shift,
                jadwalbukapoli_m.jam_mulai AS Jam_Mulai,
                jadwalbukapoli_m.jam_tutup AS Jam_Tutup,
                jadwalbukapoli_m.maxantrian_poli AS Kuota,
                jadwalbukapoli_m.kuota_online AS Kuota_Online
            FROM jadwalbukapoli_m
                LEFT JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
                JOIN lookup_m hari ON jadwalbukapoli_m.hari = hari.lookup_id
            WHERE jadwalbukapoli_m.is_deleted = false AND ruangan_m.instalasi_id = 1
            {$wheres}
        ";
        }
        else {
            $sql = "
            SELECT 
                jadwalbukapoli_m.ruangan_id,
                ruangan_m.ruangan_nama as Ruangan,
                hari.lookup_name AS Hari,
                shift_m.shift_nama AS Nama_Shift,
                jadwalbukapoli_m.jam_mulai AS Jam_Mulai,
                jadwalbukapoli_m.jam_tutup AS Jam_Tutup
            FROM jadwalbukapoli_m
                LEFT JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
                JOIN lookup_m hari ON jadwalbukapoli_m.hari = hari.lookup_id
            WHERE jadwalbukapoli_m.is_deleted = false AND ruangan_m.instalasi_id = 1
            {$wheres}
        ";
        }

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            // 'totalCount' => $count,
            'pagination' => false,
        ]);

        $result = $dataProvider->getModels();
        $header = array(
            Yii::t('app', "Poliklinik") => isset($ruangan) ? $ruangan->ruangan_nama : '',
        );

        $footer = array();

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    public function actionListRuangan($instalasi_id=null,$singkatan=null) 
    {
        $data = Ruangan::find()->joinWith('instalasi');
        $data->where(['ruangan_m.is_active' => 't']);
        if ($instalasi_id) {
            $data->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
        }
        if ($singkatan) {
            $data->andWhere(['instalasi_m.instalasi_singkatan' => $singkatan]);
        }
         $data->orderBy('ruangan_m.ruangan_nama');

        $items = ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama');
        return $items;
    }

    public function actionListShift()
    {
        $data = Shift::find();
        
        $items = ArrayHelper::map($data->all(), 'shift_id', 'shift_nama');
        return $items;
    }

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
            WHERE is_active = true
            AND is_deleted = false
            ORDER BY shift_nama ASC
        ";
        $data_shift = Yii::$app->db->createCommand($que_shift)->queryAll();

        return [
            'hari' => ArrayHelper::map($result,'lookup_id','lookup_name'),
            'ruangan' => ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama'),
            'shift' => $data_shift,
            'konfig' => KonfigSystemK::find()->limit(1)->one(),
        ];
    }

    public function actionCheckJadwalDokter($id)
    {
        try{
            $min_time = null;
            $max_time = null;

            $jadwal = JadwalDokter::find()
                    ->select(['jadwaldokter_mulai','jadwaldokter_tutup'])
                    ->where(['jadwalbukapoli_id'=>$id,'is_deleted'=>FALSE,'is_active'=>TRUE]);
            $jadwal_poli = JadwalPoliklinik::findOne($id);
            if(!$jadwal_poli){
                throw new Exception("Terjadi Kesalahan", 500);
            }
            $data_jadwal = JadwalPoliklinik::find()
                        ->select(['jam_mulai','jam_tutup'])
                        ->where(['ruangan_id'=>$jadwal_poli->ruangan_id,'hari'=>$jadwal_poli->hari])
                        ->andWhere(['<>','jadwalbukapoli_id',$id]);

            $min_dokter = $jadwal->min('jadwaldokter_mulai');
            $max_dokter = $jadwal->max('jadwaldokter_tutup');
            if($data_jadwal->count() > 1 && $jadwal->count() > 1){
                $min_poli = $data_jadwal->min('jam_mulai');
                $max_poli = $data_jadwal->max('jam_tutup');
                if($min_dokter < $min_poli){
                    $min_time = $min_dokter;
                }else{
                    $min_time = $min_poli;
                }
                if($max_dokter > $max_poli){
                    $max_time = $max_dokter;
                }else{
                    $max_time = $max_poli;
                }
            }else{
                $min_time = $min_dokter;
                $max_time = $max_dokter;
            }

            return [
                'mulai'=>$min_time,
                'selesai'=>$max_time,
            ];
        } catch(Exception $e){
            // asd
        }
    }

    public function actionCheckJadwalPoli($hari=null,$ruangan_id=null)
    {
        try {
            $jadwal = JadwalPoliklinik::find()
                        ->where(['hari'=>$hari,'ruangan_id'=>$ruangan_id,'is_active'=>TRUE,'is_deleted'=>FALSE]);
            if($jadwal->count() == 0){
                return ['list_disable'=>null];
            }else if($jadwal->count() > 1){
                $jadwal_poli = $jadwal
                                ->select(['jadwalbukapoli_id','jam_mulai','jam_tutup'])
                                ->asArray()->all();
                $list_arr = [];
                foreach ($jadwal_poli as $value) {
                    $list_arr[] = $this->parseTime($value['jam_mulai'],$value['jam_tutup']); 
                }
                $list = [];
                for ($i=0; $i < count($list_arr); $i++) { 
                    $list = array_merge($list,$list_arr[$i]);
                }
                return ['list_disable'=>$list];
            }else{
                $jadwal_poli = $jadwal->one();
                $list = $this->parseTime($jadwal_poli->jam_mulai,$jadwal_poli->jam_tutup);
                return ['list_disable'=>$list];
            }
        } catch (Exception $e) {
            // asd/
        }
    }

    private function parseTime($mulai,$selesai)
    {
        $jam_mulai = date('H',strtotime($mulai));
        $menit_mulai = date('i',strtotime($mulai));
        $jam_selesai = date('H',strtotime($selesai));
        $menit_selesai = date('i',strtotime($selesai));
        for ($i=$jam_mulai; $i <= $jam_selesai; $i++) { 
            $i = (int)$i;
            if($i == $jam_mulai && $menit_mulai == 30){
                $list[] = [$i,30];
            }elseif($i == $jam_selesai && $menit_selesai == 00){
                $list[] = [$i,0];
            }else{
                $list[] = [$i,0];
                $list[] = [$i,30];
            }
        }

        return $list;
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
            $model = new CetakJadwalPoliView;

            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();

            $print = new DocoPrint();    
            $print->attributes = [
                '#title#' => Yii::t('app', 'Jadwal Poliklinik'),
                '#jadwal_poli#' => $this->renderPartial('index', [
                    'detail' => $data,
                    'konfigKuota' => KonfigSystemK::find()->limit(1)->one(),
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

    public function actionGetDataShift($shift_id)
    {
        try {
            $shift = Shift::findOne($shift_id);
            if ($shift) {
                $jam_mulai = date('H',strtotime($shift->shift_jamawal));
                $menit_mulai = date('i',strtotime($shift->shift_jamawal));
                $jam_selesai = date('H',strtotime($shift->shift_jamakhir));
                $menit_selesai = date('i',strtotime($shift->shift_jamakhir));
                // for ($i=$jam_mulai; $i <= $jam_selesai; $i++) { 
                //     $i = (int)$i;
                    if ($menit_mulai == 30) {
                        $ja = [(int)$jam_mulai,30];
                        $jh = [(int)$jam_selesai,30];
                    } elseif($menit_selesai == 00) {
                        $ja = [(int)$jam_mulai,0];
                        $jh = [(int)$jam_selesai,0];
                    } else {
                        $ja = [(int)$jam_mulai,0];
                        $jh = [(int)$jam_selesai,30];
                    }
                // }
                return [
                    'jam_awal' => [$jam_mulai, $menit_mulai],
                    'jam_akhir' => [$jam_selesai, $menit_selesai],
                    'shift_jamawal' => date('H:i', strtotime($shift->shift_jamawal)),
                    'shift_jamakhir' => date('H:i', strtotime($shift->shift_jamakhir)),
                    'j_awal' => $ja,
                    'j_akhir' => $jh,
                ];
            } else {
                return [
                    'jam_awal' => [date('H'), date('i')],
                    'jam_akhir' => [date('H'), date('i')],
                    'shift_jamawal' => date('H:i'),
                    'shift_jamakhir' => date('H:i'),
                ]; 
            }
        } catch (Exception $e) {
            // asd/
        }
    }

    public function actionDelete($id)
    {
        try {
            $data = JadwalDokter::find()->where(['jadwalbukapoli_id' => $id])->one();
            if (!empty($data)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Data poliklinik tidak bisa dihapus, Masih terdapat jadwal dokter di poli tersebut'
                ];
            } else {
                \Yii::$app->response->statusCode = 200;
                $delete = (new JadwalPoliklinik)->delete($id);
                $result = [
                    'title' => 'Berhasil',
                    'text' => 'Data berhasil dihapus'
                ];
            }

            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id)
    {
        $payload = new PayloadForm;
        $payload->jadwalbukapoli_id = $id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }
        $que_view = "
            SELECT 
                jadwalbukapoli_id,
                ruangan_id,
                waktu_pelayanan,
                to_char(jam_mulai,'HH24:MI')as jam_mulai, 
                to_char(jam_tutup,'HH24:MI')as jam_tutup,
                maxantrian_poli,
                kuota_online,
                hari,
                shift_id,
                is_active
            FROM jadwalbukapoli_m
            WHERE jadwalbukapoli_id = '{$id}'
        ";
        return Yii::$app->db->createCommand($que_view)->queryOne();

    }

    public function actionCreate()
    {
        try {
            $dataPost = [];
            $request = Yii::$app->request;
            $model = new JadwalPoliklinik;
            $model->scenario = 'create';
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post;
                if ($model->validate()) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'JadwalPoliklinikForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'JadwalPoliklinikForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                }
                
            }
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

    public function actionUpdate($id)
    {
        try {
            $payload = new PayloadForm;
            $payload->jadwalbukapoli_id = $id;
            if (!$payload->validate()) {
                return [
                    'status' => 422,
                    'data' => $payload->errors
                ];
            }
            $request = Yii::$app->request;
            $model = JadwalPoliklinik::findOne($id);
            $model->scenario = 'update';
            if ($request->post() && !empty($model)) {

                // case inaktivasi jadwal
                if (!$request->post('is_active')) {
                    // cek jadwal dokter aktif
                    $jdokter = JadwalDokter::find()
                        ->andWhere([
                            'jadwalbukapoli_id'=>$id,
                            'is_active'=>true,
                            'is_deleted'=>false
                        ])
                        ->one();
                    if ($jdokter) {
                        return [
                            'message' => 'Data poliklinik tidak bisa dinon-aktifkan, Masih terdapat jadwal dokter aktif di poli tersebut.',
                            'status' => 500
                        ];
                    }
                }

                $model->attributes = $request->post();
                if ($model->update()) {
                    return [
                        'message' => 'Data Berhasil di ubah',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JadwalPoliklinikForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
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
