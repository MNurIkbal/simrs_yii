<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\SatusehatInt;
use app\modules\v1\models\SatusehatIntegrasiView;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use yii\helpers\ArrayHelper;

class DashboardSatusehatController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\SatusehatInt';
    protected $_title = "Dashboard Satu Sehat ";

    public $messageBroker = [
        'resend-satusehat' => [
            'services' => [
                'Satusehat' => [
                    'ResendSatuSehat' => [
                        'result' => true,
                        'successProcess' => true,
                    ]
                ],
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
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
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        try {
            $model = new SatusehatIntegrasiView;
            $query = $model::find();

            if(isset($_GET['advanced-filter'])) {
                $filter = $_GET['advanced-filter'];

                if (isset($filter['tgl_sync'])) {
                    $explode = explode(" - ", $filter['tgl_sync']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_sync']);
                }

                if (isset($filter['type'])) {
                    $list_type = $filter['type'];
                    $list_type = explode(",", strtolower($list_type));
                    $query->andWhere(['IN', 'LOWER(type)', $list_type]);
                    unset($_GET['advanced-filter']['type']);
                }

                if (isset($filter['state'])) {
                    $list_state = $filter['state'];
                    $list_state = explode(",", strtolower($list_state));
                    $query->andWhere(['IN', 'LOWER(state)', $list_state]);
                    unset($_GET['advanced-filter']['state']);
                }

                if (isset($filter['status_integrasi'])) {
                    $status_integrasi = $filter['status_integrasi'];
                    $query->andWhere(['LIKE','LOWER(status_integrasi)', strtolower($status_integrasi)]);
                    unset($_GET['advanced-filter']['state']);
                }
            }
            
            $query->andWhere(['between', 'tgl_sync', $start, $end]);
            $query->orderBy(['tgl_sync' => SORT_DESC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider(['query' => $query]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
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
                    ->where(['is_pelayanan' => true, 'is_active' => true]);

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

            case 'dokter': 
                $kelompokPegawai = [
                    DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
                    DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                    DocoConstants::KELOMPOK_PEGAWAI_BIDAN
                ];

                $result = Pegawai::find()
                    ->select(['pegawai_id as id', 'nama_pegawai as text'])
                    ->where(['kelompokpegawai_id' => $kelompokPegawai, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
                    }
                    $result->orderBy(['nama_pegawai' => SORT_ASC]);
                break;
            case 'status_rm':
                $result = Lookup::find()
                    ->select(['lookup_id as id', 'lookup_name as text'])
                    ->where(['lookup_type' => 'status_konfirmasirm']);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
                    }
                    $result->orderBy(['lookup_name' => SORT_ASC]);
                break;
            case 'jenis_pendaftaran':
                $result = Lookup::find()
                    ->select(['(lookup_id::text) as id', 'lookup_name as text'])
                    ->union("SELECT 'Pendaftaran Langsung' as id, 'Pendaftaran Langsung' as text")
                    ->where(['lookup_type' => 'jenis_reservasi']);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
                    }
                    $result->orderBy(['lookup_name' => SORT_ASC]);
                break;
        }
        $resultData = $result->asArray()->all();
        return $resultData;
    }

    public function actionGetDataTransaksi()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        
        try{
            $model = new SatusehatInt;
            $query = $model::find()->select([
                'id', 'payload', 'sync_response'
            ])->andWhere(['id' => $id]);
            $data = $query->asArray()->one();
            
            return [
                'detail' => $data,
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return $response['response'] = [
                'message' => $e->getMessage(),
                'status'  => 500  
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetDataToFilter()
    {
        $request = Yii::$app->request;
        try {
            $model = new SatusehatIntegrasiView;
            $dataFilterType = $model::find()->select(['type'])->groupBy('type')->asArray()->all();
            $dataFilterState = $model::find()->select(['state'])->groupBy('state')->asArray()->all();

            return [
                'dataFilterType' => $dataFilterType,
                'dataFilterState' => $dataFilterState
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 422;
            $this->logError($e);
            return $response['response'] = [
                'message' => $e->getMessage(),
                'status'  => 422  
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        }
    }

    public function actionResendSatusehat()
    {
        $request = Yii::$app->request;
        $list_id = $request->post('list_id', []);
        try {
            if (empty($list_id)) return $this->responseJson(422, 'Tidak Ada Data yang dapat di Resend');
            /*
            $dataSatuSehat = SatusehatIntegrasiView::find()->where(['IN', 'id', $list_id])->asArray()->all();
            $validate = false;
            foreach($dataSatuSehat as $key => $value) {
                if (strtolower(ArrayHelper::getValue($value, 'status_integrasi')) != 'gagal') $validate = true;
            }
            if ($validate) return $this->responseJson(422, 'Tidak Ada Data yang dapat di Resend');
            */
            return [
                'message' => 'Proses Resend Berhasil',
                'list_id_satusehat_int' => $list_id,
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::ERR_MESSAGE);
        }
    }

    public function actionCountDataMaster()
    {
        /** jumlah pegawai */
        $totalPegawai = Pegawai::find()->select(['pegawai_m.pegawai_id'])
        ->leftJoin('(
            SELECT p_satusehat.pegawai_id, p_satusehat.satusehat_pegawai_id, p_satusehat.satusehat_integration_id
            FROM pegawai_satusehat_m p_satusehat 
            JOIN (
                SELECT pegawai_id FROM pegawai_m 
                WHERE is_active = TRUE
                AND is_deleted = FALSE
            ) pegawai_m ON p_satusehat.pegawai_id = pegawai_m.pegawai_id
            WHERE 
                p_satusehat.satusehat_pegawai_id IS NOT NULL 
                AND p_satusehat.is_active = TRUE
                AND p_satusehat.is_deleted = FALSE
        ) satusehat_pegawai', 'satusehat_pegawai.pegawai_id = pegawai_m.pegawai_id')
        ->where('pegawai_m.is_active = true AND pegawai_m.is_deleted = false AND pegawai_m.noidentitas is not null AND pegawai_m.noidentitas <> \'-\' AND satusehat_pegawai.satusehat_pegawai_id is null')
        ->count();
        
        /** jumlah instalasi */
        $totalnstalasi = Instalasi::find()->select(['instalasi_m.instalasi_id',])
        ->leftJoin('(SELECT 
                i_satusehat.instalasi_id, 
                i_satusehat.satusehat_instalasi_id,
                i_satusehat.satusehat_integration_id
            FROM 
                instalasi_satusehat_m i_satusehat 
            WHERE 
                i_satusehat.satusehat_instalasi_id IS NOT NULL 
                AND is_deleted = FALSE
                AND is_active = TRUE
        ) satusehat_instalasi', 'satusehat_instalasi.instalasi_id = instalasi_m.instalasi_id')
        ->where('satusehat_instalasi.satusehat_instalasi_id IS NULL AND instalasi_m.is_active = TRUE AND instalasi_m.is_deleted = FALSE AND instalasi_m.is_pelayanan = TRUE')
        ->count();

        /** jumlah ruangan */
        $totalRuangan = Ruangan::find()->select(['ruangan_m.ruangan_id'])
        ->join('JOIN', '(
            SELECT
                a.instalasi_id,
                a.instalasi_nama,
                a.is_pelayanan,
                b.satusehat_instalasi_id
            FROM instalasi_m a
            JOIN (SELECT
                    ism.instalasi_id,
                    ism.satusehat_instalasi_id
                FROM instalasi_satusehat_m ism
                WHERE 
                    ism.satusehat_instalasi_id IS NOT NULL
                    AND ism.is_active = true
                    AND ism.is_deleted = false
            ) b ON b.instalasi_id = a.instalasi_id
            WHERE
                a.is_pelayanan = TRUE
                AND a.is_active = TRUE
                AND a.is_deleted = FALSE
        ) instalasi_m', 'ruangan_m.instalasi_id = instalasi_m.instalasi_id')
        ->leftJoin('(
            SELECT 
                r_satusehat.ruangan_id, 
                r_satusehat.satusehat_ruangan_id,
                r_satusehat.satusehat_integration_id
            FROM 
                ruangan_satusehat_m r_satusehat 
            WHERE 
                r_satusehat.satusehat_ruangan_id IS NOT NULL 
                AND is_deleted = FALSE
                AND is_active = TRUE
        ) satusehat_ruangan', 'satusehat_ruangan.ruangan_id = ruangan_m.ruangan_id')
        ->where('satusehat_ruangan.satusehat_ruangan_id IS NULL AND ruangan_m.is_active = TRUE AND ruangan_m.is_deleted = FALSE')
        ->count();
        
        /** jumlah pegawai */
        $totaPasien = Yii::$app->db->createCommand("
            SELECT count(*) FROM satusehat_pasien_v
            WHERE jenis_identitas <> 'null' AND jenis_identitas <> '' 
            AND nomor_id_pasien <> 'null' AND nomor_id_pasien <> '' 
            AND jenis_identitas = '".DocoConstants::IDENTITAS_KTP."' 
            AND (satusehat_pasien_id IS NULL OR satusehat_pasien_id = '--')
            AND CHAR_LENGTH ( nomor_id_pasien ) = 16;
        ")->queryScalar();

        
        return [
            1 => DocoHelpers::formatNumber($totalPegawai), 
            2 => DocoHelpers::formatNumber($totalnstalasi), 
            3 => DocoHelpers::formatNumber($totalRuangan),
            4 => DocoHelpers::formatNumber($totaPasien)
        ];
    }
}
