<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\PasienPulangRdDanRiView;
use app\modules\v1\models\InfoTagihanPasienPulangView;
use app\modules\v1\models\InfoTagihanPasienPulangDetail;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\LogHistoryPlafonBpjs;
use Doco\components\NoCountDataProvider;
use Doco\components\DocoHelpers;

class InfPasienPulangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PasienPulangRdDanRiView';

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
        $model = new InfoTagihanPasienPulangView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $startPulang = date('Y-m-d 00:00:00');
        $endPulang = date('Y-m-d 23:59:59');
        $start = null;
        $end = null;
        // return $_GET;
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }
            if(isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                if(count($explode) == 2) {
                    $startPulang = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endPulang = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }
            if(isset($_GET['advanced-filter']['nama_pasien'])) {
                $namaPasien = $_GET['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($namaPasien)]);
                $query->orWhere(['ILIKE', 'no_rekam_medik', $namaPasien]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }
            if(isset($_GET['advanced-filter']['status_approve_id'])) {
                $statusApproveID = $_GET['advanced-filter']['status_approve_id'];
                $query->andWhere(['status_approve_id' => $statusApproveID]);
                unset($_GET['advanced-filter']['status_approve_id']);
            }
        }

        $query->andWhere(['between', 'tglpasienpulang', $startPulang, $endPulang]);
        if(!is_null($start) && !is_null($end) ) {
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        }
        $query->andWhere(['status_bayar' => 'Belum Lunas']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new NoCountDataProvider([
            'query' => $query,
        ]);
    }

    // Action view
    public function actionView()
    {
        // Try catch
        try {
            // Get id
            $id = Yii::$app->request->get('id');
            // Get data
            $model = [];
            $model['header'] = InfoTagihanPasienPulangView::find()->where(['pendaftaran_id' => $id])->one();
            $model['detail'] = InfoTagihanPasienPulangDetail::find()->where(['pendaftaran_id' => $id])->all();
            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    //action buat handle select no pendaftaran
    public function actionGetDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if($post['date']){
            $newData = explode(' - ', $post['date']);
            if(count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "SELECT
                infotagihanpasienpulang_v.pendaftaran_id,
                infotagihanpasienpulang_v.no_pendaftaran
                FROM infotagihanpasienpulang_v
                LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = infotagihanpasienpulang_v.pendaftaran_id
                LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                WHERE pasienpulang_t.kondisikeluar_id IS NOT NULL AND infotagihanpasienpulang_v.no_pendaftaran LIKE '%{$term}%' AND infotagihanpasienpulang_v.tglpasienpulang BETWEEN '{$start}' AND'{$end}'
                GROUP BY infotagihanpasienpulang_v.no_pendaftaran,infotagihanpasienpulang_v.pendaftaran_id
                ORDER BY infotagihanpasienpulang_v.no_pendaftaran ASC LIMIT 50";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }


    // Get data tindakan
    public function actionGetDataTindakan()
    {
        // Try catch
        try {
            // Get id
            $id = Yii::$app->request->get('id');
            $instalasi = Yii::$app->request->get('instalasi');

            // Get data
            $model = new InfoTagihanPasienPulangDetail();

            // Check instalasi
            if ($instalasi == "RI") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::RI]);
            }
            else if ($instalasi == "RD") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::RD]);
            }
            else if ($instalasi == "RJ") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id,'is_obat'=>false,'instalasi_pelayanan'=>'Rawat Jalan']);
            }
            else if ($instalasi == "LAB") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::LAB]);
            }
            else if ($instalasi == "GUD") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::GUD]);
            }
            else if ($instalasi == "REHAB") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::REHAB]);
            }
            else if ($instalasi == "RM") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::RM]);
            }
            else if ($instalasi == "KASIR") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::KASIR]);
            }
            else if ($instalasi == "INFO") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::INFO]);
            }
            else if ($instalasi == "PDF") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::PDF]);
            }
            else if ($instalasi == "BDH") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::BDH]);
            }
            else if ($instalasi == "ambulan") {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::AMB]);
            }
            else {
                // Query
                $query = $model::find()->where(['pendaftaran_id' => $id])->andWhere(['is_obat' => false])->andWhere(['instalasi_pelayanan' => DocoConstants::RAD]);
            }

            // Active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $total = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $total += (float)$value["sub_total"];
            }

            $total_count = count($query->asArray()->all());

            $data_query = new ActiveDataProvider([
                'query' => $query,
            ]);

            /// Return
            return [
                "data" => $data_query->getModels(),
                "total" => $total,
                "totalCount" => $total_count
            ];
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get data obat
    public function actionGetDataObat()
    {
        // Try catch
        try {
            // Get id
            $id = Yii::$app->request->get('id');

            // Get data
            $model = new InfoTagihanPasien();
            $query = $model::find()->where(['pendaftaran_id' => $id,'is_obat'=>true]);

            $total = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $total += (float)$value["sub_total"];
            }

            $total_count = count($query->asArray()->all());

            // Active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data_query = new ActiveDataProvider([
                'query' => $query,
            ]);

            // Return
            return [
                "data" => $data_query->getModels(),
                "total" => $total,
                "totalCount" => $total_count
            ];
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get data obat
    public function actionGetDataPrint()
    {
        // Try catch
        try {
            // Get id
            $id = Yii::$app->request->get('id');

            // Get data
            $modelPasien = InfoTagihanPasienPulangView::find()->where(['pendaftaran_id' => $id])->one();
            $modelTagihan = InfoTagihanPasienPulangDetail::find()->where(['pendaftaran_id' => $id])->all();

            // Return
            return [
                'data-pasien' => $modelPasien,
                'data-tagihan' => $modelTagihan,
            ];
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tgl_pendaftaran# => tanggal pendaftaran pasien
    * @attribute #no_rekam_medik# => no rekam medik pasien
    * @attribute #no_pendaftaran# => nomor pendaftaran
    * @attribute #nama_pasien# => nama pasien
    * @attribute #carabayar# => cara bayar pasien
    * @attribute #penjamin# => penjamin pasien
    * @attribute #jenis_kasus_penyakit# => jenis kasus penyakit pasien
    * @attribute #dokter# => dokter nama
    * @attribute #ruangan# => ruangan rawat pasien
    * @attribute #status_bayar# => status status_bayar pasien
    * @attribute #kelas_pelayanan# => kelas pelayanan pasien
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $modelPasien = InfoTagihanPasienPulangView::find()->where(['pendaftaran_id' => $id])->one();
            $modelTagihan = InfoTagihanPasienPulangDetail::find()->where(
                ['pendaftaran_id' => $id, 'is_obat' => false]
            )->all();

            $modelAmbulan = InfoTagihanPasienPulangDetail::find()->where(
                ['pendaftaran_id' => $id, 'is_obat' => false, 'instalasi_pelayanan' => 'Ambulan']
            )->all();

            $modelObat = InfoTagihanPasienPulangDetail::find()->where(
                ['pendaftaran_id' => $id, 'is_obat' => true])
            ->all();

            // return $modelTagihan;
            $print = new DocoPrint();
            $print->attributes = [
                '#tgl_pendaftaran#' => '',
                '#no_rekam_medik#' => '',
                '#no_pendaftaran#' => '',
                '#nama_pasien#' => '',
                '#carabayar#' => '',
                '#penjamin#' => '',
                '#jenis_kasus_penyakit#' => '',
                '#dokter#' => '',
                '#ruangan#' => '',
                '#status_bayar#' => '',
                '#kelas_pelayanan#' => '',
                '#datatable#' => $this->renderPartial('cetak', [
                    'data' => ['pasien'=>$modelPasien, 'tagihan'=>$modelTagihan, 'obat'=>$modelObat, 'ambulan' => $modelAmbulan ],
                ]),
            ];

            $print->Output();
        } catch (\RequestException $e) {
            return json_encode($e->getMessage());
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function actionGetApi()
    {
        $result['ruangan'] = [];
        $result['instalasi'] = [];
        $result['carabayar'] = [];
        $result['penjamin'] = [];
        try{
            $listInstalasi = [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI];
            $listInstalasi = implode(',', $listInstalasi);
            $result['ruangan'] = Yii::$app->runAction('v1/allow/get-ruangan', [
                'listInstalasi'=> $listInstalasi,
            ]);
            $result['ruangan'] = $result['ruangan']['response'];
            $result['instalasi'] = Yii::$app->runAction('v1/allow/get-instalasi', ['listInstalasi'=> $listInstalasi,]);
            $result['instalasi'] = $result['instalasi']['response'];
            $result['carabayar'] = Yii::$app->runAction('v1/allow/get-cara-bayar');
            $result['carabayar'] = $result['carabayar']['response'];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin');
            $result['penjamin'] = $result['penjamin']['response'];
            return $result;
        } catch(\Exception $e){
            return $result;
        }
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $result = $resultData = [];
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $type = $request->get('type', []);
        $additionalPayload = $request->get('additionalPayload', []);
        $carabayar_id = $instalasi_id = null;
        if(isset($additionalPayload['carabayar_id']) && !empty($additionalPayload['carabayar_id'])) {
            $carabayar_id = $additionalPayload['carabayar_id'];
        }
        if(isset($additionalPayload['instalasi_id']) && !empty($additionalPayload['instalasi_id'])) {
            $instalasi_id = $additionalPayload['instalasi_id'];
        }
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        switch ($type) {
            case 'instalasi':
                $listInstalasi = [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI];
                $result = Instalasi::find()
                    ->select(['instalasi_id as id', 'instalasi_nama as text'])
                    ->where(['is_pelayanan' => true, 'is_active' => true])
                    ->andWhere(['IN','instalasi_id', $listInstalasi]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(instalasi_nama)', strtolower($term)]);
                }
                $result->orderBy(['instalasi_nama' => SORT_ASC]);
                break;
            
            case 'ruangan': 
                $result = Ruangan::find()
                    ->select(['ruangan_id as id', 'ruangan_nama as text'])
                    ->where(['instalasi_id' => $instalasi_id, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['ruangan_nama' => SORT_ASC]);
                break;

            case 'carabayar':
                $result = CaraBayar::find()
                    ->select(['carabayar_id as id', 'carabayar_nama as text'])
                    ->where(['is_active' => true]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
                }
                $result->orderBy(['carabayar_nama' => SORT_ASC]);
                break;
            
            case 'penjamin': 
                $result = Penjamin::find()
                    ->select(['penjamin_id as id', 'penjamin_nama as text'])
                    ->where(['carabayar_id' => $carabayar_id, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['penjamin_nama' => SORT_ASC]);
                break;
        }
        $resultData = $result->asArray()->all();
        return $resultData;
    }

    public function actionGetDataPlafon()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        return $this->getPendaftaran($pendaftaranId);
    }

    public function actionSimpanPlafon()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->post('pendaftaran_id');
        $pendaftaran = $this->getPendaftaran($pendaftaranId);
        $connection = Yii::$app->db;
		$transaction = $connection->beginTransaction();
        try {
            if($pendaftaran) {
                $oldPlafon = ArrayHelper::getValue($pendaftaran, 'limit_tagihan', 0);
                $plafon = $request->post('plafon', 0);
                $modelPendaftaran = Pendaftaran::findOne($pendaftaranId);
                $modelPendaftaran->limit_tagihan = $plafon;
                if($modelPendaftaran->validate() && $modelPendaftaran->save()) {
                    $log = new LogHistoryPlafonBpjs;
                    $log->pendaftaran_id = $pendaftaranId;
                    $log->plafon_lama = $oldPlafon;
                    $log->plafon_baru = $plafon;
                    if($log->validate()) {
                        $log->save(false);
                    }
                    $transaction->commit();
                    $status = ['status' => 200, 'message' => 'Data Berhasil di simpan'];
                }
                else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($modelPendaftaran->errors, 'PendaftaranForm');
                    $status = [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
                
                return $status;
            }
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

    private function getPendaftaran($pendaftaranId)
    {
        return Pendaftaran::find()
            ->select(['pendaftaran_t.instalasi_id', 'pendaftaran_t.kelaspelayanan_id', 'pendaftaran_t.limit_tagihan',
                     'instalasi_m.instalasi_nama', 'kelaspelayanan_m.kelaspelayanan_nama'])
            ->innerJoin('instalasi_m', 'instalasi_m.instalasi_id = pendaftaran_t.instalasi_id')
            ->innerJoin('kelaspelayanan_m', 'kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id')
            ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaranId])->asArray()->one();
    }

    public function actionGetDataHistoryPlafon()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $model = new LogHistoryPlafonBpjs;
        $query = $model::find(true)
                ->select([
                    'histori_plafon_bpjs_pasien_r.histori_plafon_bpjs_pasien_id', 'histori_plafon_bpjs_pasien_r.created_date',
                    'pegawai_m.nama_pegawai', 'histori_plafon_bpjs_pasien_r.plafon_lama', 'histori_plafon_bpjs_pasien_r.plafon_baru'])
                ->innerJoin('loginpemakai_k', 'loginpemakai_k.loginpemakai_id = histori_plafon_bpjs_pasien_r.created_by')
                ->innerJoin('pegawai_m', 'pegawai_m.pegawai_id = loginpemakai_k.pegawai_id')
                ->where(['pendaftaran_id' => $pendaftaranId]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query->asArray(),
            'pagination' => false,
        ]);
    }
}
