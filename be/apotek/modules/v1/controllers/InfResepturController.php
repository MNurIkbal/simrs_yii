<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\ProfilRumahSakit;
use Doco\components\DocoConstansId;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\models\PrescribeView;

use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoResepturDetailView;
use app\components\ApotekComponent;

use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\Loket;
use app\modules\v1\models\InfoPenjualanResepDetailView;
use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\ExpandResepFn;
use app\modules\v1\models\InfoDataResepView;
use app\modules\v1\models\InformasiResepDetailView;
use app\modules\v1\models\InfoResepDetail1View;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\PembatalanResep;
use app\modules\v1\models\ObatAlkesPasien;

use app\modules\v1\models\RiwayatAlergiView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\repositories\ResepRepository;
use Doco\Notifications\FarmasiNotification;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\repositories\ApproveResepRepository;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadFormPdf;
use yii\helpers\Json;

use Doco\Services\InternalService;
use Exception;
use Doco\components\NoCountDataProvider;

class InfResepturController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemesananObatAlkesView';

    public $messageBroker = [
        'serahkan-obat' => [
            'services' =>[
                'SatuSehat' => [
                    'Medication' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ],
                    'MedicationDispense' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                        'taskid' => '7',
                        'update_from' => 'serahkan_obat'
                    ]
                ],
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["batal-resep"] = ["DELETE"];
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
        $action = [
            'serahkan-obat' => 'app\modules\v1\actions\SerahkanObatAction',
            'generate-resep-kronis' => 'app\modules\v1\actions\GenerateResepKronisAction'
        ];
        $actions = array_merge($actions,$action);
        return $actions;
    }

    public function actionIndex()
    {
        // $model = new InformasiResepturView;
        // $model = new InfoResepView;
        $model = new PrescribeView;
        $query = $model::find(true);
        /*$query->select(['reseptur_id', 'ruangan_id', 'tglreseptur','noresep','nama_pasien','nama_pasien','no_pendaftaran','carabayar_nama','penjamin_nama','no_antrian','status_reseptur']);
        $query->Where(['!=', 'status_reseptur_id', DocoConstants::VAR_B_R]);*/

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['filters'])){
            unset($_GET['filters']);
        }

        if(isset($_GET['advanced-filter'])) {
            
            if(isset($_GET['advanced-filter']['tgl_resep_dibuat'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_resep_dibuat']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d H:i:s', strtotime($explode[0]));
                    $end = date('Y-m-d H:i:s', strtotime($explode[1]."23:59:59"));
                }
                unset($_GET['advanced-filter']['tgl_resep_dibuat']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['status_reseptur_id'])) {
                if (in_array($_GET['advanced-filter']['status_reseptur_id'], [DocoConstants::LUNAS,DocoConstants::BELUM_LUNAS])) {
                    $query->andWhere(['status_bayar' => $_GET['advanced-filter']['status_reseptur_id']]);
                    unset($_GET['advanced-filter']['status_reseptur_id']); // Unset Advanced Filter status bayar
                }
            }
            
            if(isset($_GET['advanced-filter']['ruanganreseptur_id'])) {
                if(is_numeric($_GET['advanced-filter']['ruanganreseptur_id'])){
                    if (is_int($_GET['advanced-filter']['ruanganreseptur_id']) != $_GET['advanced-filter']['ruangan_id']) {
                        $query->andWhere(['ruanganreseptur_id' => $_GET['advanced-filter']['ruanganreseptur_id']]);
                    } else {
                        $query->andWhere('ruanganreseptur_id IS NULL');
                    }
                    unset($_GET['advanced-filter']['ruanganreseptur_id']);
                }
            }

            if(isset($_GET['advanced-filter']['status_racikan'])) {
                $query->andWhere(['status_racikan' => $_GET['advanced-filter']['status_racikan']]);
                unset($_GET['advanced-filter']['status_racikan']); 
            }
            
            if(isset($_GET['advanced-filter']['kategori_resep'])) {
                $kategori_resep = $_GET['advanced-filter']['kategori_resep'];

                if($kategori_resep != 'all') {
                    $query->andWhere(['kategori_resep' => $kategori_resep]);
                }

                unset($_GET['advanced-filter']['kategori_resep']);
            }

            // Hide resep dengan status batal
            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                // Hide resep dengan ruangan apotek RJ
                $ruangan_id_apotek_rj = DocoConstansId::actionGetId('apotek_rj');
                if ($_GET['advanced-filter']['ruangan_id'] == $ruangan_id_apotek_rj) {
                    $query->andWhere(['<>', 'status_reseptur_id', DocoConstants::VAR_B_R]);
                }
            }
          
        }

        $query->andWhere(['between', new \yii\db\Expression('(tgl_resep_dibuat::date)'), $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new NoCountDataProvider([
            'query' => $query
        ]);
    }

    public function actionDataReseptur()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getReseptur();
        $result->select(['noresep','noresep']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(noresep)',$term]);
        }
        $result->andWhere(['penjualanresep_id' => null]);

        return $result->asArray()->all();
    }

    public function getReseptur()
    {
        $data = InformasiResepturView::find();
        return $data;
    }

    public function actionDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getReseptur();
        $result->select(['no_pendaftaran']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(no_pendaftaran)',$term]);
        }
        $result->groupBy(['no_pendaftaran']);
        $result->andWhere(['penjualanresep_id' => null]);

        return $result->asArray()->all();
    }

    public function actionDataObat()
    {
        $request = Yii::$app->request;
        $advancedFilter = $request->get('advanced-filter');
        $model = new InformasiResepDetailView;
        $query = $model::find();
        
        if($request->get('status_reseptur') == DocoConstants::VAR_B_R) {
            $model = new InfoResepDetail1View;
            $query = $model::find(true);    
        }

        if(isset($advancedFilter['noresep'])){
            $query->andWhere(['noresep'=>$advancedFilter['noresep']]);
            unset($_GET['advanced-filter']['noresep']);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        if (!empty($request->get('advanced-filter')['get_data'])) {
            if($request->get('advanced-filter')['get_data'] == true){
                return $query->asArray()->all();
            }
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
    }

    public function actionDataObatKronis()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $nomor = $request->get('nomor', null);
        $data_resep = [];
        $data_penjualanresep = PenjualanResep::find()->select(['reseptur_id','penjualanresep_id','carabayar_id','penjamin_id','pegawai_id','resep_kronis_asal_id','hasil_resep_kronis_id','reseptur_kronis_asal_id']);
        $bpjs = Bpjs::find()->select(['nosep','additional_data'])->where(['pendaftaran_id' => $request->get('pendaftaran_id')])->one();
        
        $model = new InfoResepDetailView;
        $query = $model::find();
        
        if($request->get('status_reseptur') == DocoConstants::VAR_B_R) {
            $model = new InfoResepDetail1View;
            $query = $model::find(true);    
        }

        if($request->get('jenis') == 'resep'){
            $data_penjualanresep = $data_penjualanresep->where(['penjualanresep_id' => $id])->one();
            $query = $query->where(['penjualanresep_id' => $id]);
            $reseptur_id = $data_penjualanresep['reseptur_id'];
        }else{
            $reseptur_id = $id;
            $data_penjualanresep = $data_penjualanresep->where(['reseptur_id' => $id])->one();
            $query = $query->where(['reseptur_id' => $id]);
        }

        $data_resep = Reseptur::find()
            ->select([
                'reseptur_t.*',
                'COALESCE(admisi.penjamin_id, pendaftaran.penjamin_id) as penjamin_id',
                'COALESCE(admisi.carabayar_id, pendaftaran.carabayar_id) as carabayar_id'
            ])
            ->leftJoin('pendaftaran_t pendaftaran', 'pendaftaran.pendaftaran_id = reseptur_t.pendaftaran_id')
            ->leftJoin('pasienadmisi_t admisi', 'admisi.pasienadmisi_id = pendaftaran.pasienadmisi_id')
            ->where(['reseptur_id' => $reseptur_id])
            ->asArray()->one();
        $query = $query->andWhere(['is_kronis' => true])->asArray()->all();
        $signa = SignaObat::find()->select(['signa_id', 'signa_nama', 'qty_obat', 'iterasi'])->asArray()->all();

        return [
            'detail_resep' => $query,
            'data_resep' => $data_resep,
            'data_penjualanresep' => $data_penjualanresep,
            'pagination' => false,
            'bpjs' => $bpjs,
            'count' => count($query),
            'master_signa' => $signa
        ];
    }

    public function actionDataObatKronisV2()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $nomor = $request->get('nomor', null);
        $data_resep = [];
        $data_penjualanresep = PenjualanResep::find()->select(['reseptur_id','penjualanresep_id','carabayar_id','penjamin_id','pegawai_id','resep_kronis_asal_id','hasil_resep_kronis_id','reseptur_kronis_asal_id']);
        
        $model = new InformasiResepDetailView;
        $query = $model::find();
        
        if($request->get('status_reseptur') == DocoConstants::VAR_B_R) {
            $model = new InfoResepDetail1View;
            $query = $model::find(true);    
        }

        if($request->get('jenis') == 'resep'){
            $data_penjualanresep = $data_penjualanresep->where(['penjualanresep_id' => $id])->one();
            $query = $query->where(['penjualanresep_id' => $id]);
            $reseptur_id = $data_penjualanresep['reseptur_id'];
        }else{
            $reseptur_id = $id;
            $data_penjualanresep = $data_penjualanresep->where(['reseptur_id' => $id])->one();
            $query = $query->where(['reseptur_id' => $id]);
        }

        $data_resep = Reseptur::find()
            ->select([
                'reseptur_t.*',
                'COALESCE(admisi.penjamin_id, pendaftaran.penjamin_id) as penjamin_id',
                'COALESCE(admisi.carabayar_id, pendaftaran.carabayar_id) as carabayar_id'
            ])
            ->leftJoin('pendaftaran_t pendaftaran', 'pendaftaran.pendaftaran_id = reseptur_t.pendaftaran_id')
            ->leftJoin('pasienadmisi_t admisi', 'admisi.pasienadmisi_id = pendaftaran.pasienadmisi_id')
            ->where(['reseptur_id' => $reseptur_id])
            ->asArray()->one();
        $query = $query->andWhere(['is_kronis' => true])->asArray()->all();

        return [
            'data_resep' => $data_resep,
            'data_penjualanresep' => $data_penjualanresep,
            'count' => count($query),
        ];
    }

    /* detail resep filler resep */
    public static function getResepturData($id, $duration = 5, $tagName = null)
    {
        return Reseptur::getDb()->cache(function ($db) use ($id) {
            return Reseptur::find()
                ->select([
                    'reseptur_t.*',
                    'COALESCE(admisi.penjamin_id, pendaftaran.penjamin_id) as penjamin_id',
                    'COALESCE(admisi.carabayar_id, pendaftaran.carabayar_id) as carabayar_id'
                ])
                ->leftJoin('pendaftaran_t pendaftaran', 'pendaftaran.pendaftaran_id = reseptur_t.pendaftaran_id')
                ->leftJoin('pasienadmisi_t admisi', 'admisi.pasienadmisi_id = pendaftaran.pasienadmisi_id')
                ->where('reseptur_id = :id', ['id' => $id])
                ->asArray()->one();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getPenjualanResepData($id = null, $reseptur_id = null, $selects = [], $duration = 5, $tagName = null)
    {
        $default_select = ['reseptur_id', 'penjualanresep_id', 'carabayar_id', 'penjamin_id', 'pegawai_id', 'resep_kronis_asal_id', 'hasil_resep_kronis_id', 'reseptur_kronis_asal_id'];
        if (!empty($selects)) {
            $selects = is_array($selects) ? $selects : [$selects];
        }
        $selects = array_merge($selects, $default_select);
        $where = [];
        if (!empty($id)) $where['penjualanresep_id'] = $id;
        if (!empty($reseptur_id)) $where['reseptur_id'] = $reseptur_id;
        return PenjualanResep::getDb()->cache(function ($db) use ($selects, $where) {
            return empty($where) ? [] : PenjualanResep::find()->select($selects)->where($where)->asArray()->one();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getInfoResepDetailData($penjualanresep_id = null, $reseptur_id = null, $selects = [], $is_count = false, $duration = 5, $tagName = null)
    {
        $where = ['is_kronis' => true];
        if (!empty($penjualanresep_id)) $where['penjualanresep_id'] = $penjualanresep_id;
        if (!empty($reseptur_id)) $where['reseptur_id'] = $reseptur_id;
        return InformasiResepDetailView::getDb()->cache(function ($db) use ($selects, $where, $is_count) {
            $model = InformasiResepDetailView::find();
            if (!empty($selects)) $model->select($selects);
            return $is_count ? $model->where($where)->count() : $model->where($where)->asArray()->all();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public function actionGetPenjualanResepData($reseptur_id = null, $penjualanresep_id = null, $cache_duration = 2)
    {
        $selected = Yii::$app->request->get('selected', []);
        try {
            return [
                // 'data_resep' => empty($reseptur_id) ? [] : self::getResepturData($reseptur_id, $cache_duration),
                // 'data_penjualanresep' => (empty($reseptur_id) && empty($penjualanresep_id)) ? [] : self::getPenjualanResepData($penjualanresep_id, $reseptur_id, [], $cache_duration),
                // 'count' => (empty($reseptur_id) && empty($penjualanresep_id)) ? 0 : self::getInfoResepDetailData($penjualanresep_id, $reseptur_id, [], true, $cache_duration),
                'data_racikan' => empty($reseptur_id) ? [] : ResepturRacikan::find()->where(['reseptur_id' => $reseptur_id])->asArray()->all(),
            ];
        } catch(Exception $e) {
            Yii::error($e);
            return [
                'data_resep' => [],
                'data_penjualanresep' => [],
                'count' => 0,
                'data_racikan' => [],
            ];
        }
    }

    public function actionDataDetail($id)
    {
        try {

            $query =  InfoPenjualanResepDetailView::find()->where(['penjualanresep_id' => $id]);
            $query->select(['carabayar_nama','penjamin_nama','pendaftaran_id','noresep','no_pendaftaran','nama_pasien','nama_pegawai','ruangan_id', 'carabayar_id','pasien_id','penjamin_id','pegawai_id','iter','instalasi_nama','ruangan_nama','pegawai_reseptur']);
            return $query->asArray()->one();
        } catch (\Exception $e) {
            return [];
        } catch (\RequestException $e) {
            return [];
        }
    }

    /**
    * @controller actionPrintResep
    * @attribute #dataTable# => Menampilkan data table
    * @attribute #noresep# => Menampilkan data noresep
    * @attribute #no_pendaftaran# => Menampilkan data No Pendaftaran
    * @attribute #nama_pasien# => Menampilkan data Nama Pasien
    * @attribute #dokter_resep# => Menampilkan data Nama Pegawai Dokter Resep
    * @attribute #instalasi_nama# => Menampilkan data Nama Instalasi
    * @attribute #ruangan_nama# => Menampilkan data Nama Ruangan
    * @attribute #ruangan_kamar_bed# => Menampilkan data Ruangan/Kamar/Bed
    * @attribute #caraBayar# => Menampilkan data Cara Bayar
    * @attribute #penjamin# => Menampilkan data Nama Penjamin
    * @attribute #iter# => Menampilkan data Iter
    * @attribute #diagnosa# => Menampilkan data diagnosa
    * @attribute #alergi# => Menampilkan data alergi
    * @attribute #catatan# => Menampilkan data Catatan
    **/
    public function actionPrintResep()
    {
        return Yii::$app->docoPlugin->execute('print_resep_penjualan');
    }

    public function actionBatalReseptur(){
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $reseptur_id = $request->get('id');
        try {
            $model = Reseptur::findOne($reseptur_id);
            $model->status_reseptur = DocoConstants::VAR_B_R; //432
            if($model->save()){
                (new ResepturDetail)->delete(['reseptur_id' => $reseptur_id]);

                $transaction->commit();
                $result = [
                        'status' => 200,
                        'title' => 'Berhasil Dibatalkan',
                        'text' => 'Pembatalan Reseptur berhasil!',
                    ];
                return $result;
            }else{
                $result = [
                        'status' => 422,
                        'title' => 'Gagal Dibatalkan',
                        'text' => 'Gagal Membatalkan Reseptur!',
                    ];
                return $result;
            }
        } catch (\Exception $e) {
        Yii::error($e->getMessage());

            $transaction->rollBack();
            return ['message'=>$e->getMessage()];

        } catch ( \yii\dbException $e) {
        Yii::error($e->getMessage());

            $transaction->rollBack();
            return ['message'=>$e->getMessage()];
        }
    }

    public function actionBatalResep($no_resep)
    {
        $resep = new ResepRepository;
        $resep->batal($no_resep);
        FarmasiNotification::updateNotif();
        return $resep->completedResponse();
    }

    // set loket clone from pendaftaran : ali.padilah@docotel.com
    public function actionSetLoket()
    {
        try {
            $post = Yii::$app->request->post();
            $loginpemakai_id = $post['loginpemakai_id'];
            $loket_id = $post['loket_id'];

            Loket::updateAll(['loginpemakai_id' => null], "loginpemakai_id = {$loginpemakai_id}");

            $loket = Loket::findOne($loket_id);
            $loket->loginpemakai_id = $loginpemakai_id;
            $loket->save(false);
            return $loket;
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

    public function actionExportExcel() {
        $request = Yii::$app->request;
        $model = new InfoResepView;
        $query = $model::find(true);

        $instalas_ruangan = $kategori_resep_key = '';
        $status_id = null;
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_resep_dibuat'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_resep_dibuat']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d H:i:s', strtotime($explode[0]));
                    $end = date('Y-m-d H:i:s', strtotime($explode[1]."23:59:59"));
                }
                unset($_GET['advanced-filter']['tgl_resep_dibuat']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['status_reseptur_id'])) {
                $status_id = $_GET['advanced-filter']['status_reseptur_id'];
                unset($_GET['advanced-filter']['status_reseptur_id']);
            }

            if(isset($_GET['advanced-filter']['status_racikan'])) {
                $query->andWhere(['status_racikan' => $_GET['advanced-filter']['status_racikan']]);
                unset($_GET['advanced-filter']['status_racikan']); 
            }

            if(isset($_GET['advanced-filter']['ruanganreseptur_id'])) {
                $ruanganreseptur_id = $_GET['advanced-filter']['ruanganreseptur_id'];
                $instalas_ruangan = RuanganView::find()->select(['ruangan_id', 'instalasi_nama', 'ruangan_nama'])->where(['ruangan_id' => $ruanganreseptur_id])->one();
                $instalas_ruangan = ArrayHelper::getValue($instalas_ruangan, 'instalasi_nama', ''). ' - ' .ArrayHelper::getValue($instalas_ruangan, 'ruangan_nama', '');
            }

            if(isset($_GET['advanced-filter']['kategori_resep'])) {
                $kategori_resep = $_GET['advanced-filter']['kategori_resep'];

                if($kategori_resep != 'all') {
                    $query->andWhere(['kategori_resep' => $kategori_resep]);

                    $kategori_resep_key = !empty(DocoConstants::NAMA_KATEGORI_RESEP[$kategori_resep]) ? DocoConstants::NAMA_KATEGORI_RESEP[$kategori_resep] : '-';
                }

                unset($_GET['advanced-filter']['kategori_resep']);
            }
        }

        if($status_id != null) {
            $query->andWhere(['or', ['status_reseptur_id' => $status_id], ['status_bayar' => $status_id]]);
        }
        
        $query->andWhere(['between', 'tgl_resep_dibuat', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();

        $counter = 0;
        foreach ($data as $key => $value) {
            $data_baru[$counter]['Status'] = $value['status_reseptur'];
            $data_baru[$counter]['Tanggal'] = !empty($value['tglreseptur']) ?
                                              date('d F Y H:i:s', strtotime($value['tglreseptur']))
                                              : date('d F Y H:i:s', strtotime($value['tglresep']));
            $data_baru[$counter]['No. Reseptur'] = !empty($value['no_reseptur']) ?
                                                   $value['no_reseptur'] : '-';
            $data_baru[$counter]['No. Resep'] = !empty($value['no_resep']) ?
                                                $value['no_resep'] : '-';
            $data_baru[$counter]['Tipe'] = !empty($value['kategori_resep_nama']) ?
                                                $value['kategori_resep_nama'] : '-';
            $data_baru[$counter]['Instalasi - Ruangan'] = ArrayHelper::getValue($value, 'instalasi_reseptur', ''). ' - ' .ArrayHelper::getValue($value, 'ruangan_reseptur');
            $data_baru[$counter]['Nama Dokter'] = !empty($value['nama_pegawai']) ?
                                                $value['nama_pegawai'] : '-';
            $data_baru[$counter]['No. RM'] = !empty($value['no_rekam_medik']) ?
                                                           $value['no_rekam_medik'] :
                                                           '-';
            $data_baru[$counter]['Nama Pasien'] = !empty($value['nama_pasien']) ?
                                                           $value['nama_pasien'] :
                                                           $value['nama_pembeli'];
            $data_baru[$counter]['No. Pendaftaran'] = !empty($value['no_pendaftaran']) ?
                                                      $value['no_pendaftaran'] : '-';
            $data_baru[$counter]['No. SEP BPJS'] = !empty($value['nosep_bpjs']) ? $value['nosep_bpjs'] : '-';
            $data_baru[$counter]['Cara Bayar - Penjamin'] = $value['carabayar_nama'].' - '.$value['penjamin_nama'];
            $data_baru[$counter]['Total Tagihan (Rp.)'] = DocoHelpers::formatNumber($value['totaltagihan'])." ";
            $counter++;
        }

        $header = [
            'Tanggal Reseptur' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end))
        ];

        if(!empty($kategori_resep_key) && $kategori_resep_key != '-') {
            $header['Tipe'] = $kategori_resep_key;
        }

        // display filter in excel header
        foreach ($_GET['advanced-filter'] as $key => $value) {
            if($key != 'ruangan_id'){
                // change variable display in excel (ex: no_resep => No Resep)
                if ($key == 'ruanganreseptur_id') {
                    $header['Instalasi - Ruangan'] = $instalas_ruangan;
                    unset($_GET['advanced-filter']['ruanganreseptur_id']);
                    continue;
                }

                $key = ucwords(str_replace('_', ' ', $key));
                $header[$key] = $value;
            }
        }
        $filePath = DocoHelpers::exportExcel("Laporan Informasi Reseptur", $data_baru, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionPrintResepDetail
    * @attribute #dataTable# => Menampilkan data table
    * @attribute #dataProfile# => Menampilkan data table
    * @attribute #tanggal# => Menampilkan data tanggal resep
    * @attribute #noresep# => Menampilkan data noresep
    * @attribute #dokter_resep# => Menampilkan data Nama Pegawai Dokter Resep
    * @attribute #no_pendaftaran# => Menampilkan data No Pendaftaran
    * @attribute #nama_pasien# => Menampilkan data Nama Pasien
    * @attribute #tgl_lahir# => Menampilkan data tanggal lahir
    * @attribute #no_rekam_medik# => Menampilkan data no rekam medik
    * @attribute #berat_badan# => Menampilkan data berat badan
    * @attribute #tinggi_badan# => Menampilkan data  tinggi badan
    * @attribute #poli# => Menampilkan data nama poli
    * @attribute #jenis_kelamin# => Menampilkan data nama jenis kelamin
    * @attribute #cara_bayar# => Menampilkan data nama cara bayar
    * @attribute #apoteker# => Menampilkan data nama Apoteker
    * @attribute #no_sipa# => Menampilkan data no sipa apoteker
    **/
    public function actionPrintResepDetail()
    {
        return Yii::$app->docoPlugin->execute('cetak_resep');
    }

    protected function capwords($str) {
        return ucwords(strtolower($str));
    }

    protected function getIdJabatan($nama_jabatan){
        $list_id_jabatan = [];
        foreach ($nama_jabatan as $value) {
            $list_id_jabatan[$value] = DocoConstansId::actionGetId($value);
        }
        return $list_id_jabatan;
    }

    protected function getPegawaiByJabatan($jabatan) {
        return PegawaiMasterView::find()->select([
            'distinct on (jabatan_id) nama_pegawai', 'jabatan_nama', 'jabatan_id', 'suratizinpraktek'
        ])->where([
            'IN', 'jabatan_id', $jabatan
        ])->asArray()->all();
    }

    protected function getSignIndex($pegawai, $jabatan) {
        $list_index = [];
        foreach ($jabatan as $key => $value) {
            $list_index[$key] = array_search($jabatan[$key], array_column($pegawai, 'jabatan_id'));
        }
        return $list_index;
    }

    public function actionGetExpandData() {
        try {
            $request = Yii::$app->request;
            $reseptur_id = $request->get('reseptur_id', null);
            $nomor = $request->get('nomor', null);
            $status_reseptur_id = $request->get('status_reseptur_id', null);

            $racikan = $detail_resep = [];
            if(!empty($reseptur_id)) {
                $racikan = ResepturRacikan::find()->where(['reseptur_id' => $reseptur_id])->asArray()->all();
            }

            $detail_resep = ExpandResepFn::getData($nomor);

            return [
                'racikan_freetext' => $racikan,
                'detail_resep' => $detail_resep
            ];
        } catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e){
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionBatalApprove()
    {
        $request = Yii::$app->request;
        $no_resep = $request->post('no_resep');
        $pendaftaranId = $request->post('pendaftaran_id');
        if(!empty($pendaftaranId)) {
            $dataPendaftaran = Pendaftaran::findOne($pendaftaranId);
            $isCloseBill = ArrayHelper::getValue($dataPendaftaran, 'is_close_bill', false);
            if($isCloseBill) {
                return $this->responseJson(400, 'Pasien sudah dilakukan proses Lock Bill.');
            }
        }

        $resep = new ApproveResepRepository;
        $resep->batal_approve($no_resep);
        FarmasiNotification::updateNotif();
        return $resep->completedResponse();
    }

    public function actionGetListReseptur() {
        $request = Yii::$app->request;
        $data = [['nomor' => $request->get('nomor_resep')]];

        return $this->getDataReseptur($data)->asArray()->one();
    }

    public function getDataReseptur($getData) {
        $date = date('Y-m-d');
        $no_resep = $getData[0]['nomor'];
        $arr_noresep = explode(',', $no_resep);
        $model = new InfoResepView;
        $query = $model::find()
            ->where(['no_resep' => $arr_noresep])
            ->orWhere(['no_reseptur' => $arr_noresep]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    // Kebutuhan untuk hanya mengambil ruangan, instalasi reseptur, dan status reseptur, kebutuhan persiapan data serahkan obat
    // improve dari actionGetListReseptur
    public function actionGetInfoDataResep() {
        $request = Yii::$app->request;
        $data = [['nomor' => $request->get('nomor_resep')]];

        $no_resep = $data[0]['nomor'];
        $arr_noresep = explode(',', $no_resep);
        $model = new InfoDataResepView;
        $query = $model::find()
            ->where(['no_resep' => $arr_noresep])
            ->orWhere(['no_reseptur' => $arr_noresep]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->one();
    }

    public function actionSyncSerahkanObat() {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        $data = $this->getDataReseptur($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData[0]['randString']) ? $getData[0]['randString'] : null;
        $totalPerPage = count($data);

        \Yii::error($getData[0]);
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'ProsesSerahkanObatReseptur' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExecuteSerahkanObatReseptur' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData
        ];
    }

      /**
    * @controller actionPrintMultipleResep
    * @attribute #no_rm# => Menampilkan no rekam medik pasien
    * @attribute #nama_ruangan# => Menampilkan data Ruangan pasien
    * @attribute #nama_pasien# => Menampilkan data Nama pasien
    * @attribute #tgl_lahir_pasien# => Menampilkan data Tanggal lahir pasien
    * @attribute #ket_waktu_obat# => Menampilkan data Ket waktu konsumsi obat
    * @attribute #ket_minum_obat# => Menampilkan data minum obat
    * @attribute #tahun# => Menampilkan data Tahun print 
    * @attribute #data_obat# => Menampilkan data Obat
    **/
    public function actionPrintMultipleResep()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_nama = $request->get('obatalkes_nama');
            $noresep = $request->get('noresep');
            $add_comment = $request->get('add_comment');
            $noresep = $noresep[0];

            $dataInfoResep = Yii::$app->db->createCommand(" 
                SELECT 
                inforesep_v.status_reseptur_id,
                inforesep_v.no_reseptur,
                inforesep_v.no_resep,
                inforesep_v.no_pendaftaran,
                inforesep_v.nama_pasien,
                inforesep_v.tanggal_lahir,
                inforesep_v.no_rekam_medik,
                inforesep_v.ruangan_reseptur,kamarruangan_m.kamarruangan_nokamar , kamartempattidur_m.no_tempattidur
                FROM inforesep_v
                LEFT JOIN pasienadmisi_t ON inforesep_v.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
                LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
                WHERE inforesep_v.pasienadmisi_id IS NOT NULL And inforesep_v.nomor = '{$noresep}'
            ")->queryOne();

            /*
            * NEVER USED
            *
            if($dataInfoResep['status_reseptur_id'] == DocoConstants::VAR_B_R) {
                $modelInfoResepturDetailView = new InfoResepDetail1View;
            } else {
                $modelInfoResepturDetailView = new InfoResepDetailView;
            }
    
            $InfoResepturDetailView = $modelInfoResepturDetailView::find(true)->where([
                'noresep'           => $noresep
            ])->where([
                'IN', 'obatalkes_nama', $obatalkes_nama,
            ])->orderBy([
                'racikan_id'    => SORT_ASC,
                'rke'           => SORT_ASC
            ]);
            $resDataObat = $InfoResepturDetailView->asArray()->all();
            */

            $data_ruangan = $dataInfoResep['ruangan_reseptur']." ".$dataInfoResep['kamarruangan_nokamar']." ".$dataInfoResep['no_tempattidur'];
            $tanggal_lahir = date("d-m-Y", strtotime($dataInfoResep['tanggal_lahir']));
            $i = 0;
            $data_obat = [];
            foreach($obatalkes_nama as $key => $nama_obat) {
                if($key % 7 == 0) {
                    $i++;
                }
                $arrayObat[$i][] = $nama_obat;
            }

            $header1 = array(
                'no_rm' => $dataInfoResep['no_rekam_medik'],
                'nama_ruangan' => $data_ruangan,
                'nama_pasien' => !empty($dataInfoResep['nama_pasien']) ? $dataInfoResep['nama_pasien'] : '-',
                'tgl_lahir_pasien' => $tanggal_lahir,
                'ket_waktu_obat' => $add_comment,
                // 'tahun' => date('Y')
            );

            $data_obat = [
                'header' => $header1,
                'obat' => $arrayObat
            ];

            $print = new DocoPrint();
            $print->attributes = [
                '#data_obat#' => $this->renderPartial('print_multiple_obat', [
                    'data' => $data_obat,
                ]),
            ];

            $print->Output();
        }catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       } catch (\Exception $e){
           throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       }
    }

      /**
    * @controller actionPrintAntrian
    * @attribute #ruangan_reseptur# => Menampilkan nama ruangan farmasi
    * @attribute #nama_rs# => Menampilkan nama rs
    * @attribute #alamat_rs# => Menampilkan alamat rs
    * @attribute #no_resep# => Menampilkan nomor resep
    * @attribute #tanggal_resep# => Menampilkan tanggal resep
    * @attribute #no_antrian# => Menampilkan no antrian
    * @attribute #nama_poli# => Menampilkan alamat rs
    * @attribute #nama_dokter_pembuat_resep# => Menampilkan nama dokter pembuat resep
    **/
    public function actionPrintAntrian()
    {

        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $resep_id = $request->get('resep_id');
            $jenis = $request->get('jenis');

            $where = ['resep_id' => $resep_id];
            if ($jenis == 'reseptur') {
                $where = ['reseptur_id' => $resep_id];
            }
            // return $resep_id;

            $modelResep = new InfoResepView;
            $query = $modelResep::find()
                    ->where($where)
                    ->asArray()->one();
            
            $noResep = $query['no_resep'];
            $tglResep = $query['tglresep'];
            if($jenis == 'reseptur'){
                $noResep = $query['no_reseptur'];
                $tglResep = $query['tglreseptur'];
            }

            $modelRs = new ProfilRumahSakit;
            $queryRs = $modelRs::find()->asArray()->one();

            $print = new DocoPrint();
            $print->attributes = [
                '#ruangan_reseptur#' => empty($query['ruangan_reseptur']) ? "-" : $query['ruangan_reseptur'],
                '#ruangan_tujuan#' => empty($query['ruangan_tujuan']) ? "-" : $query['ruangan_tujuan'],
                '#nama_rs#' => empty($queryRs['nama_rumahsakit']) ? "-" : $queryRs['nama_rumahsakit'],
                '#alamat_rs#' => empty($queryRs['alamatlokasi_rumahsakit']) ? "-" : $queryRs['alamatlokasi_rumahsakit'],
                '#no_resep#' => empty($noResep) ? "-" : $noResep,
                '#tanggal_resep#' => empty($tglResep) ? "-" : $tglResep,
                '#no_antrian#' => empty($query['no_antrian']) ? "-" : $query['no_antrian'],
                '#nama_poli#' => empty($query['ruangan_kamar_bed']) ? "-" : $query['ruangan_kamar_bed'],
                '#nama_dokter_pembuat_resep#' => empty($query['nama_pegawai']) ? "-" : $query['nama_pegawai']
            ];

            $print->Output();
        }catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       } catch (\Exception $e){
           throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       }
    }

    public function actionPanggilAntrian()
    {
        $request = Yii::$app->request;
        $antrian_id = $request->get('antrian_id');
        $no_antrian = $request->get('no_antrian');
        $no_loket = $request->get('no_loket');
        $loket_id = $request->get('loket_id');
        $jenis_resep = $request->get('jenis_resep');

        $data["panggil_antrian_farmasi"] = [
                'no_antrian' => $no_antrian,
                'antrian_id' => $antrian_id,
                'no_loket' => $no_loket,
                'loket_id' => $loket_id,
                'jenis_resep' => $jenis_resep,
                'text_panggil' => DocoHelpers::convertAntrian($no_antrian). ' Loket ' .DocoHelpers::convertAntrian($no_loket, false)
            ];

        try {
            $mode = Yii::$app->params['mode'];
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'display-antrian-' . $mode,
                'message' => Json::encode(['data' => $data])
            ]);

            $modelAntrian = Antrian::find()->where(['antrian_id' => $antrian_id])->one();

            if ($modelAntrian) {
                $modelAntrian->panggilan_ke = $modelAntrian->panggilan_ke + 1;
                $modelAntrian->save();
            }

            return true;
            
        } catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }

    }
}
