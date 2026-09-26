<?php
/**
 * @author: arief saputra
 * @modified : ali.padilah@docotel.com
 * @description: master untuk CRUD Layar Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\base\Exception;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\KonfigAntrian;
use app\modules\v1\models\KonfigSystemK;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\KonfigantrianV;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\KelompokTindakan;
use app\modules\v1\models\Layarantrian;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\cache\Cache;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class KonfigSystemController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Konfigantrian';

    protected $allowAction = [ '*' ];

    public $messageBroker = [
        'update' => [
            'services' => [
                'Sirs' => [
                    'ClearCache' => [
                        'query_params' => ['id'],
                        'state' => 'default_kelas_registrasi',
                        'successProcess' => true
                    ]
                ],
            ]
        ],
        'reset-cache-kelas-regis' => [
            'services' => [
                'Sirs' => [
                    'ClearCache' => [
                        'query_params' => [],
                        'state' => 'default_kelas_registrasi',
                        'successProcess' => true
                    ]
                ],
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["create"] = ["POST"];
        $verbs["get-attribute-options"] = ["GET"];
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
        $get = $request->get();

        $result = $this->getData();
        if(isset($_GET['advanced-filter'])) {
            $post = $_GET['advanced-filter'];
            if (!empty($post['jenis_antrian'])) {
                $result->andWhere(['jenisantrian_id' => $post['jenis_antrian']]);
                unset($_GET['advanced-filter']['jenis_antrian']);
            }
            if (!empty($post['fungsi_antrian'])) {
                $result->andWhere(['fungsiantrian_id' => $post['fungsi_antrian']]);
                unset($_GET['advanced-filter']['fungsi_antrian']);
            }
            if (!empty($post['group_carabayar'])) {
                $result->andWhere(['groupcarabayar_id' => $post['group_carabayar']]);
                unset($_GET['advanced-filter']['group_carabayar']);
            }

        }
        $query = DocoRestActiveFilter::advancedFilter(new KonfigantrianV, $result);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
     * request multiple data from FE to any Controller BE
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionMultiReq()
    {
        $request = Yii::$app->request;
        $arr = $request->post();
        $data = [];
        try {
            foreach ($arr as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = (count($value) > 1)
                    ? $this->{$value[0]}($value[1])
                    : $this->{$value[0]}();
                } else {
                    $data[$key] = $this->{$value}();
                }
            }
        } catch (\Exception $e) {
            $data[] = $e->getMessage();
        }
        return $data;
    }

    public function actionGetAttributeOptions($id = null)
    {
        $cara_bayar = Cache::getGroupCaraBayar();
        $klasifikasi = Cache::getKlasifikasiPasien();
        $jenis_antrian = Cache::getJenisAntrian();
        $fungsi_antrian = Cache::getFungsiAntrian(3600,false);
        $instalasi = Cache::getInstalasi();
        $ruangan = Cache::getRuangan(3600,false);
        $find = [];
        if ($id) {
            $find = KonfigAntrian::find()->where([
                'konfigantrian_id' => $id
            ])->one();
        }
        return [
            'cara_bayar' => $cara_bayar,
            'klasifikasi' => $klasifikasi,
            'jenis_antrian' => $jenis_antrian,
            'fungsi_antrian' => $fungsi_antrian,
            'instalasi' => $instalasi,
            'ruangan' => $ruangan,
            'data' => $find
        ];
    }


    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = KonfigSystemK::find()->where([
            'konfigsystem_id' => $id
        ])->one();
        
        $transaction = Yii::$app->db->beginTransaction(); 
        try {
            // set array for kelas default -> kelas_pelayanan
            $val_kelas = $request->post('kelas_pelayanan', []);
            $tmpDefaultVal = [];
            if (!empty($val_kelas)) {
                if(is_array($val_kelas)) {
                    foreach ($val_kelas as $key => $value) {
                        $tmpDefaultVal[] = $value;
                    }
                    $model->kelas_pelayanan = json_encode($tmpDefaultVal);
                }
            }
            
            $model->attributes = $request->post();
            if ($model->save()) {
                Yii::$app->cache->delete(DocoConstants::CACHE_KELAS_PELAYANAN);
                Yii::$app->cache->set(DocoConstants::VAR_K_S, $model->attributes);
                $transaction->commit();
                return [
                    'message' => 'Data berhasil disimpan'
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }


        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            Yii::info($e->getMessage());
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollback();
            Yii::info($e->getMessage());
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    

    public function actionView($id)
    {
        // Try catch
        try {
            // Define model
            $Konfigsystem = KonfigSystemK::findOne(['konfigsystem_id' => $id]);
            if (!empty($Konfigsystem)) {
                return $result = [
                    'status' => 200,
                    'data' => $Konfigsystem
                ];
            } else {
                return $result = [
                    'status' => 500,
                    'data' => array()
                ];
            }

        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getData($filter = null)
    {
        $returnData = KonfigantrianV::find();
        return $returnData;
    }

    public function getKelasPelayanan()
    {
        $data = KelasPelayanan::find()->where(['is_active' => true])->all();

        return $data;
    }

    public function actionKelasPelayanan() 
    {
        $data = KelasPelayanan::find();
        $data->where(['is_deleted' => false,
                        'is_active' => true]);

        $items = ArrayHelper::map($data->all(), 'kelaspelayanan_id', 'kelaspelayanan_nama');

        return $items;
    }

    /**
     * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
     * dropdown pagination infinity scroll - get data
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionListTindakanInfinity()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page',0);
        $limit = $params->get('limit',5);
        $offset = $params->get('offset',0);
          
        $model = new DaftarTindakan();
        $query = $model::find();
        if($term){
            $query->where(['ILIKE','LOWER(daftartindakan_nama)',$term]);
            $query->orWhere(['ILIKE','LOWER(daftartindakan_kode)',$term]);
        }

        return $query->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionListKelompokTindakanInfinity()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page',0);
        $limit = $params->get('limit',5);
        $offset = $params->get('offset',0);
          
        $model = new KelompokTindakan();
        $query = $model::find();
        if($term){
            $query->where(['ILIKE','LOWER(kelompoktindakan_nama)',$term]);
            $query->orWhere(['ILIKE','LOWER(kelompoktindakan_kode)',$term]);
        }

        return $query->offset($offset)->limit($limit)->asArray()->all();
    }

    /**
     * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
     * get value tindakan by ID
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionTindakan($id)
    {
        $data = DaftarTindakan::find()->where(['daftartindakan_id' => $id])->one();
        return $data;
    }

    public function actionGetLookup($type)
    {
        $qLook = Lookup::find()->select([
            'lookup_id',
            'lookup_type',
            'lookup_name',
            'lookup_value',
        ])->andWhere([
            'lookup_type' => 'nilai_pembulatan'
        ])->orderBy([
            'lookup_id' => SORT_ASC
        ])->asArray()->all();

        return $qLook;
    }

    public function actionResetCacheKelasRegis()
    {
        return [
            'message' => 'Clear Cache Regis Berhasil',
        ];
    }

    public function actionGetKonfigTarifDefault()
    {
        try {
            $model = new CaraBayar;
            $query = $model::find()
            ->select([
                'carabayar_m.carabayar_id as carabayar_id',
                'carabayar_m.carabayar_nama',
                'carabayar_m.penjamindefault_id',
                'penjamin_m.penjamin_nama'
            ])->joinWith(['penjamin' => function($penjamin){
                $penjamin->select(['penjamin_m.penjamin_id','penjamin_m.penjamin_nama'])->where([
                    'penjamin_m.is_active' => true,
                    'penjamin_m.is_deleted' => false,
                ]);
            }])->where([
                'carabayar_m.is_active' => true,
                'carabayar_m.is_deleted' => false
            ])
            ->orderBy(['carabayar_id' => SORT_ASC]);
            return new ActiveDataProvider([
                'query' => $query->asArray(),
            ]);
        } catch (\Exception $e) {
            return [
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
                'Message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetPenjamin()
    {
        $model = new Penjamin;
        $query = $model::find();
        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10);
        $query->select([
            'penjamin_id as id',
            'penjamin_nama as text',
        ])
        ->andWhere([
            'is_active' => true,
            'is_deleted' => false
        ])
        ->limit($limit + 1)
        ->offset(($page - 1) * $limit);
        if (!empty($term)) {
            $query->andWhere([
                'like',
                'LOWER(penjamin_nama)',
                strtolower($term)
            ]);
        }
        return $query->asArray()->all();
    }

    public function actionUpdatePenjaminDefault()
    {
        $payload = $this->validatePayload([
            'payloadKey' => [
                'carabayar_id' => 'required',
                'penjamindefault_id' => 'required',
            ]
        ]);
        if (isset($payload['errors'])) {
            return $this->responseJson(422, 'Silakan cek kembali input', ['errors' => $payload['errors']]);
        } else {
            $carabayar = Carabayar::find()
                ->select([
                    'carabayar_m.carabayar_id as carabayar_id',
                    'carabayar_nama',
                    'penjamindefault_id',
                ])
                ->andWhere([
                    'carabayar_id' => $payload['carabayar_id']
                ])
                ->asArray()
                ->one();
            if (!empty($carabayar)) {
                $whereClause = [
                    'carabayar_id' => $payload['carabayar_id'],
                ];
                CaraBayar::updateAll([
                    'penjamindefault_id' => $payload['penjamindefault_id'],
                ], $whereClause);
                return $this->responseJson(200, 'Penjamin Default berhasil diperbarui.');
            } else {
                return $this->responseJson(400, empty($carabayar) ? 'Data tidak ditemukan.' : 'Tidak dapat memperbarui data Penjamin Default.');
            }
        }
    }
}