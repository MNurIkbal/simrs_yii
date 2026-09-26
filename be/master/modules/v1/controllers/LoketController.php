<?php
/**
 * @author: arief saputra
 * @description: master untuk CRUD Layar Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Loket;
use app\modules\v1\models\LoketMp;
use app\modules\v1\models\CaraBayar;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use app\modules\v1\controllers\AllowController;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoConstants;
use app\modules\v1\payload\MasterForm;

class LoketController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Loket';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-carabayar"] = ["POST", "GET"];
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
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->get('length',10))
                            ->offset($request->get('start',0));
            if (!empty($request->get('advanced-filter'))) {
                // return $request->get('advanced-filter');
                $filter = $request->get('advanced-filter');
                if (isset($filter['jenis_name'])) {
                    $result->andWhere(['t.jenisantrian_id' => $filter['jenis_name']]);
                }
                if (isset($filter['loket_nourut'])) {
                    $result->andWhere(['t.loket_nourut' => $filter['loket_nourut']]);
                }
                if (isset($filter['loket_nama'])) {
                    $result->andFilterWhere(['ILIKE','t.loket_nama', $filter['loket_nama']]);
                    unset($filter['loket_nama']);
                }
                if (isset($filter['loket_namalain'])) {
                    $result->andFilterWhere(['ILIKE','t.loket_namalain', $filter['loket_namalain']]);
                }
                if (isset($filter['status'])) {
                    $result->andWhere(['t.is_active' => $filter['status']]);
                }
            }

            $result->orderby(['lookupjenis.lookup_name' => SORT_DESC]);
            // dump($result->all());exit;

            // $config = AllowController::actionGetGroupKonfig();
            // $fungsi = [];
            // foreach($config as $key => $value) {
            //     $label = $key . " (" . implode(", ", array_unique(array_values($value))) . ")";
            //     $id = json_encode(array_keys($value));
            //     $fungsi[$id] = $label;
            // }



            // $data = $list_id = [];

            // foreach ($result->all() as $key => $value) {
            //     $row = $value;
            //     $list_id[] = $value['loket_id'];
            //     $data[] = $row;
            // }

            // $loketMp = LoketMp::find()->where([
            //     'loket_id' => $list_id
            // ])->all();
            // $valKonfig = [];
            // foreach ($loketMp as $key => $value) {
            //     $valKonfig[$value['loket_id']][] = $value['konfigantrian_id'];
            // }
            // $result_data = [];
            // foreach ($data as $key => $value) {
            //     $row = $value;
            //     $compare = isset($valKonfig[$value['loket_id']]) ? $valKonfig[$value['loket_id']] : [];
            //     $label = null;
            //     foreach ($fungsi as $x => $v) {
            //         if (!array_diff(json_decode($x),$compare)) {
            //             $label = $v;
            //             break;
            //         }
            //     }
            //     $row['fungsi_antrian'] = $label;
            //     $result_data[] = $row;
            // }

            return [
                'data' => $result->all(),
                'count' => $result->count(),
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Loket::findOne($id);
            $post = $request->post();
            if ($request->post() && !empty($model)) {
                $cekLoket = self::checkUserLoket($model);
                if($cekLoket == false) {
                    $model->loket_namalain = "Loket " . $request->post('nama_loket');
                    $model->loket_nama = $request->post('nama_loket');
                    $model->jenisantrian_id = $request->post('jenisantrian_id');
                    $model->layarantrian_id = $request->post('layarantrian_id');
                    $model->loket_nourut = $request->post('no_loket');
                    $model->ruangan_id = empty($request->post('ruangan_id')) ? null : $request->post('ruangan_id');
                    $model->is_active = $request->post('is_active');
                    if ($model->save()) {
                        $insertMp = [];
                        $lastloket_id = $model->loket_id;
                        $delete = (new LoketMp)->delete(['loket_id' => $lastloket_id]);
                        if (isset($post['konfigantrian_id'])) {
                            $konfig = $post['konfigantrian_id'];
                            foreach ($konfig as $value) {
                                $insertMp[] = [
                                    'konfigantrian_id' => $value,
                                    'loket_id' => $lastloket_id
                                ];
                            }
                        }
                        LoketMp::batchInsert($insertMp,false);
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
                }else {
                    \Yii::$app->response->statusCode = 400;
                    return [
                        'message' => "User sedang memakai loket ini"
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @param Model $model
     * @return bool
     */
    private function checkUserLoket($model)
    {
        if (! empty($model)) {
            if(isset($model['loginpemakai_id'])) return true;

            if(isset($model->loginpemakai_id)) return true;
        }

        return false;
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();

            $model = new Loket;

            if ($request->post()) {
                $model->loket_namalain = "Loket " . $request->post('nama_loket');
                $model->loket_nama = $request->post('nama_loket');
                $model->jenisantrian_id = $request->post('jenisantrian_id');
                $model->layarantrian_id = $request->post('layarantrian_id');
                $model->loket_nourut = $request->post('no_loket');
                $model->ruangan_id = empty($request->post('ruangan_id')) ? null : $request->post('ruangan_id');

                if ($model->save()) {
                    $insertMp = [];
                    $lastloket_id = $model->loket_id;
                    if (isset($post['konfigantrian_id'])) {
                        foreach ($post['konfigantrian_id'] as $value) {
                            $insertMp[] = [
                                'konfigantrian_id' => $value,
                                'loket_id' => $lastloket_id
                            ];
                        }
                    }
                    LoketMp::batchInsert($insertMp,false);

                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete($id)
    {
        try {
            $pemakai = Loket::find()->where(['loket_id' => $id])->select(['loginpemakai_id'])->asArray()->one();
            if(!empty($pemakai)) {
                if(isset($pemakai['loginpemakai_id'])) {
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'message' => "User sedang memakai loket ini"
                    ];
                }else{
                    $model = (new Loket)->delete(['loket_id' => $id]);
                    $mapping = (new LoketMp)->delete(['loket_id' => $id]);
                }
            }
         
            return ['message' => 'success'];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        $dataRuangan = Ruangan::find()->where(['instalasi_id'=>DocoConstants::INSTALASI_FARMASI]);
        $data_ruangan_farmasi = ArrayHelper::map($dataRuangan->all(),'ruangan_id','ruangan_nama');

        $option = AllowController::getLookupByType('jenis_antrian')->all();
        $payload = new MasterForm;
        $payload->loket_id = $id;
        if($payload->validate()) {
            $loket = Loket::find()->select([
                'loket_m.loket_id',
                'loket_m.ruangan_id',
                'nama_loket' => 'loket_m.loket_nama',
                'loket_m.loket_namalain',
                'loket_m.loket_fungsi',
                'loket_m.loket_formatnomor',
                'loket_m.fungsiantrian_id',
                'loket_m.jenisantrian_id',
                'loket_m.layarantrian_id',
                'is_active' => 'loket_m.is_active',
                'no_loket' => 'loket_m.loket_nourut',
            ])->where([
                'loket_m.loket_id' => $id
            ])->joinWith([
                'detail' => function ($query) {
                    $query->select([
                        'loket_mp.loket_id',
                        'loket_mp.konfigantrian_id',
                    ]);
                }
            ])->asArray()->one();

            $valKonfig = [];
            if (!empty($loket['detail'])) {
                foreach ($loket['detail'] as $value) {
                    $valKonfig[] = $value['konfigantrian_id'];
                }
            }
            return [
                'option' => $option,
                'loket' => $loket,
                'val_konfig' => $valKonfig,
                'ruangan' => $data_ruangan_farmasi
            ];
        }
        else {
            return [
                'data' => $payload->errors,
                'status' => 422
            ];
        }
    }

    public function actionLoketByLayar($id)
    {
        return $this->getData(['t.layarantrian_id' => $id])->all();
    }

    private function getData($arr_data = null)
    {
        $returnData = (new \yii\db\Query())
                        ->select([
                                't.loket_id',
                                't.carabayar_id',
                                't.layarantrian_id',
                                't.loket_nama',
                                't.loket_namalain',
                                't.loket_fungsi',
                                't.loket_singkatan',
                                't.loket_nourut',
                                't.loket_formatnomor',
                                't.loket_maxantrian',
                                't.filesuara',
                                't.is_pendaftaran',
                                't.is_kasir',
                                't.additional_data',
                                't.is_active',
                                't.is_deleted',
                                'lookupjenis.lookup_id as lookupJenisId',
                                'lookupjenis.lookup_name as jenis_name',
                                'lookupjenis.lookup_value as jenis_value',
                                // 'lookupfungsi.lookup_name as fungsi_name',
                                // 'lookupfungsi.lookup_value as fungsi_value'
                            ])->from('loket_m t')
                        // ->join('JOIN','layarantrian_m ly','ly.layarantrian_id = t.layarantrian_id')
                        ->join('JOIN', 'lookup_m lookupjenis','lookupjenis.lookup_id = t.jenisantrian_id')
                        // ->join('JOIN', 'loket_mp lk', 'lk.loket_id = t.loket_id')
                        // ->join('JOIN', 'lookup_m lookupfungsi','lookupfungsi.lookup_id = t.fungsiantrian_id')
                        ->orderBy([ 't.loket_id' => SORT_ASC ])
                        ->where([ 't.is_deleted' => false ]);

        if ($arr_data) {
            foreach ($arr_data as $key => $value) {
                $returnData->where([$key => $value]);
            }
        }

        return $returnData;
    }

    public function actionListCarabayar() {
        $items = ArrayHelper::map(CaraBayar::find()->where(['is_active' => true])->all(), 'carabayar_id', 'carabayar_nama');

        return $items;
    }

    public function actionListJenisAntrian() {
        $items = ArrayHelper::map(Loket::find()->where(['is_active' => true])->all(), 'jenisantrian_id', 'fungsi_name');

        return $items;
    }

    protected $_title = 'Loket';
    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
            ->limit($request->post('length',10))
            ->offset($request->post('start',0));

            if ($indexing = $request->post('loket')) {
                $result->andFilterWhere(['ILIKE', 't.loket_nama', $indexing]);
            }

            $status = $request->post('is_active');
            if($status) {
                $status = $status ? true : false;
                $result->andWhere(['t.is_active' => $status]);
            }

            $result->andWhere(['t.is_deleted' => 'false']);
            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            $data=$result->all();
            $header=[];
            $newData=[];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $newData[$key]['Nama Jenis Antrian'] = $value['jenis_name'];
                    $newData[$key]['Kode Antrian'] = $value['jenis_value'];
                    $newData[$key]['No Loket'] = $value['loket_nourut'];
                    $newData[$key]['Nama Loket'] = $value['loket_namalain'];
                    $newData[$key]['Status'] = $value['is_active'] == true ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
                }
            }

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Nama Jenis Antrian' => 'Tanggal Unduh : ' . date('d M Y'),
                ]
            ];

            $filePath = DocoHelpers::exportExcel($this->_title, $newData, $header, [],$footer,[],true);
            $filePath->save('php://output');
            die;
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

    /**
    * @controller actionCetakPdf 
    * @attribute #layarantrian# => table 
    **/

    public function actionCetakPdf()
    {
        $request = Yii::$app->request;
        $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('loket')) {
                $result->andFilterWhere(['ILIKE', 't.loket_nama', $indexing]);
            }

            $status = $request->post('is_active');
            if($status) {
                $status = $status ? true : false;
                $result->andWhere(['t.is_active' => $status]);
            }
            $result->andWhere(['t.is_deleted' => 'false']);
            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }
            $data=$result->all();
            $header=[];
        // $filter = [
        //     'Ruangan nama' => $ruangan ? $ruangan->ruangan_nama : '-',
        //     'Status' => isset($_GET['advanced-filter']['is_active']) ? ($_GET['advanced-filter']['is_active'] == 0) ? 'Aktif' : 'Tidak Aktif' : '-' ,
        // ];
        $print = new DocoPrint();    
        $print->attributes = [
            '#loket#' => $this->renderPartial('index', [
                'filter'=> $header,                
                'detail' => $data,
        'title' => 'Loket'
            ]),            
        ];
        $print->Output();
    }

    public function actionUbahPengambilanAntrian($id)
    {
        try {
            $request = Yii::$app->request;
            $payload = new MasterForm;
            $payload->loket_id = $id;
            if($payload->validate()) {
                $model = Loket::findOne($id);
                $post = $request->post();
                if ($request->post() && !empty($model)) {
                    if ($model->save()) {
                        $insertMp = [];
                        $lastloket_id = $model->loket_id;
                        $delete = (new LoketMp)->delete(['loket_id' => $lastloket_id]);
                        if (isset($post['konfigantrian_id'])) {
                            $konfig = $post['konfigantrian_id'];
                            foreach ($konfig as $value) {
                                $insertMp[] = [
                                    'konfigantrian_id' => $value,
                                    'loket_id' => $lastloket_id
                                ];
                            }
                        }
                        LoketMp::batchInsert($insertMp,false);
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
                }
                throw new Exception("Data Tidak Di Temukan");
            }
            else {
                return [
                    'status' => 422,
                    'data' => $payload->errors,
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

}