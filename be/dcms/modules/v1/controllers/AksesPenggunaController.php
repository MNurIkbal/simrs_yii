<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\models\Modul;
use Doco\models\PeranPengguna;
use Doco\models\AksesPengguna;
use Doco\models\Loginpemakai;
use Doco\components\DocoPrint;

class AksesPenggunaController extends DocoActiveController
{

    public $modelClass = PeranPengguna::class;

    protected $_tmpMenu = [];
    protected $_parent = [];
    protected $_skipMenu = [];
    protected $_menuKey = [
        'informasi',
        'master',
        'aplikasi'
    ];
    protected $_title = 'Akses Pengguna';    

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $filter = $request->get('advanced-filter');
        $hakAkses = null;
        if (isset($filter['peranpengguna_k.peranpenggunanama'])) {
            $hakAkses = $filter['peranpengguna_k.peranpenggunanama'];
            unset($_GET['advanced-filter']['peranpengguna_k.peranpenggunanama']);
        }


        $query = Loginpemakai::find()->select([
            'loginpemakai_k.loginpemakai_id',
            'loginpemakai_k.nama_pemakai'
        ])->distinct();
        $query->joinWith([
            'aksesPengguna' => function ($query) {
            $query->select([
                'peranpengguna_k.peranpenggunanama'
                ])->joinWith([
                    'peranPengguna' => function ($query) {
                        $query->select([
                            'peranpengguna_k.peranpengguna_id'
                        ]);
                    }
                ]);
            }
        ]);
        if (! empty($hakAkses)) {
            $query->andFilterWhere([
                'ILIKE', 'peranpengguna_k.peranpenggunanama', $hakAkses 
            ]);
        }
        $query = DocoRestActiveFilter::advancedFilter(new Loginpemakai, $query);
        $dataProvider =  new ActiveDataProvider([
            'query' => $query,
        ]);

        $data = $listLoginId = [];
        foreach ($dataProvider->getModels() as $key => $value) {
            $data[$value->loginpemakai_id] = $value->attributes;
            $listLoginId[] = $value->loginpemakai_id;
        }
        
        $akses = AksesPengguna::find()->select([
            'aksespengguna_k.aksespengguna_id',
            'aksespengguna_k.peranpengguna_id',
            'aksespengguna_k.loginpemakai_id',
            'peranpengguna_k.peranpenggunanama',
            'peranpengguna_k.peranpenggunanamalain'
        ])->joinWith([
            'peranPengguna' => function ($query) {
                $query->select([
                    'peranpengguna_k.peranpengguna_id',
                ]);
            }
        ])->where([
            'loginpemakai_id' => $listLoginId
        ])->asArray();

        if ($hakAkses) {
            $akses->andFilterWhere([
                'ILIKE', 'peranpengguna_k.peranpenggunanama', $hakAkses 
            ]);
        }

        foreach ($akses->all() as $k => $val) {
            if (isset($data[$val['loginpemakai_id']])) {
                $data[$val['loginpemakai_id']]['aksesPengguna'][$k] = $val;
                $data[$val['loginpemakai_id']]['aksesPengguna'][$k]['peranPengguna'] = $val['peranPengguna'];
            }
        }
        // penambahan set aksesPengguna default array untuk user yg belum di assign : Ali
        foreach ($data as $k => $v) {
            if (empty($v['aksesPengguna'])) {
                $data[$k]['aksesPengguna'] = [];
            }
        }
    
        return [
            'data' => $data,
            '_meta' => [
                'totalCount' => $dataProvider->getTotalCount()
            ]
        ];
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $delete = (new AksesPengguna)->delete([
                'loginpemakai_id' => $id
            ]);
            $akses = $request->post('akses_pemakai',[]);

            $dataInsert = [];
            foreach ($akses as $value) {
                $id_peran = DocoHelpers::decrypt($value);
                $dataInsert[] = [
                    'peranpengguna_id' => $id_peran,
                    'loginpemakai_id' => $id
                ];
            }

            AksesPengguna::batchInsert($dataInsert);
            return ['message' => 'Data Berhasil di simpan'];
        } else {

            $model = Loginpemakai::find()
                ->select([
                    'loginpemakai_k.loginpemakai_id',
                    'loginpemakai_k.nama_pemakai',
                ])
                ->joinWith([
                    'aksesPengguna' => function ($query) {
                        $query->select([
                            'aksespengguna_k.peranpengguna_id',
                            'aksespengguna_k.loginpemakai_id',
                        ]);
                    }
                ])->where([
                    'loginpemakai_k.loginpemakai_id' => $id
                ])->asArray()->one();
            $data_akases = [];
            foreach ($model['aksesPengguna'] as $value) {
                $data_akases[] = $value['peranpengguna_id'];
            }

            unset($model['aksesPengguna']);

            $data = $this->getData();

            $data['akses_pengguna'] = $data_akases;
            $data['data'] = $model;
            return $data;
        }
    }

    public function actionAutocompleteUser()
    {
        $request = Yii::$app->request;
        $search = $request->get('q');
        $query = Loginpemakai::find()->select([
            'loginpemakai_id',
            'nama_pemakai',
        ])->andFilterWhere(['ILIKE','nama_pemakai',$search])->all();
        $result = [];
        
        foreach ($query as $value) {
            $result[] = [
                'id' => $value->loginpemakai_id, 
                'value' => $value->nama_pemakai
            ];
        }

        return [
            'data' => $result
        ];
    }

    public function actionGetUser()
    {
        $model = new Loginpemakai;
        $query = $model::find();
        $query->select([
            'pegawai_m.pegawai_id',
            'pegawai_m.nomorindukpegawai',
            'pegawai_m.nama_pegawai',
            'pegawai_m.tgl_lahirpegawai',
            'pegawai_m.alamat_pegawai',
            'loginpemakai_k.loginpemakai_id',
            'loginpemakai_k.nama_pemakai'
        ])->joinWith([
            'pegawai' => function ($query) {
                $query->select([
                    'pegawai_m.pegawai_id'
                ]);
            }
        ])->asArray();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getData()
    {
        $query = Modul::find()
                    ->select([
                        'modul_k.modul_id',
                        'modul_k.modul_nama',
                        'modul_k.modul_namalainnya',
                    ])
                    ->joinWith([
                    'peranPengguna' => function ($query) {
                        $query->select([
                            'peranpengguna_k.peranpengguna_id',
                            'peranpengguna_k.modul_id',
                            'peranpengguna_k.peranpenggunanama',
                            'peranpengguna_k.peranpenggunanamalain',
                        ])->where([
                            'peranpengguna_k.peranpengguna_aktif' => true
                        ]);
                    }
                ])->asArray()->all();

        $result = [
            'modul' => $query
        ];

        return $result;
    }

    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $filter = $request->get('advanced-filter');
            $namaPemakai = null;
            $hakAkses = null;
            if (isset($filter['nama_pemakai'])) {
                $namaPemakai = $filter['nama_pemakai'];
                unset($_GET['advanced-filter']['nama_pemakai']);
            }

            if (isset($filter['peranpengguna_k.peranpenggunanama'])) {
                $hakAkses = $filter['peranpengguna_k.peranpenggunanama'];
                unset($_GET['advanced-filter']['peranpengguna_k.peranpenggunanama']);
            }
    
            $query = Loginpemakai::find()->select([
                'loginpemakai_k.loginpemakai_id',
                'loginpemakai_k.nama_pemakai',
                'peranpengguna_k.peranpenggunanama'
            ])->joinWith([
                'aksesPengguna' => function ($query) {
                    $query->select([
                        'aksespengguna_k.peranpengguna_id',
                        'aksespengguna_k.loginpemakai_id',
                        'peranpengguna_k.peranpenggunanama'
                    ])->joinWith([
                        'peranPengguna' => function ($query) {
                            $query->select([
                                'peranpengguna_k.peranpengguna_id'
                            ]);
                        }
                    ]);
                }
            ]);
            if($namaPemakai) {
                $query->andFilterWhere([
                    'ILIKE', 'loginpemakai_k.nama_pemakai', $namaPemakai 
                ]);
            }

            if($hakAkses) {
                $query->andFilterWhere([
                    'ILIKE', 'peranpengguna_k.peranpenggunanama', $hakAkses 
                ]);
            }
       
            $result = $query->asArray()->all();
            // Yii::error($result);
            // die;
    
            foreach ($result as $key => $value) {
                $newValue = [];
                $aksesPengguna = [];
                foreach ($value['aksesPengguna'] as $val) {
                    $aksesPengguna[] = $val['peranpenggunanama'];
                }
    
                $newValue[\Yii::t('app', 'Peran pengguna nama')] = $value['nama_pemakai'];
                $newValue[\Yii::t('app', 'Hak akses')] = empty($aksesPengguna) ? '-' : implode(', ', $aksesPengguna);
                $result[$key] = $newValue;
            }

            $header = array(
                Yii::t('app', "Peran pengguna nama") => (@$_GET['advanced-filter']['nama_pemakai']),            
                Yii::t('app', "Hak akses") => '',            
            );

            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

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
    * @attribute #akses_pengguna# => table 
    **/

    public function actionCetakPdf()
    {
        $request = Yii::$app->request;
        $filter = $request->get('advanced-filter');
        $namaPemakai = null;
        $hakAkses = null;
        if (isset($filter['nama_pemakai'])) {
            $namaPemakai = $filter['nama_pemakai'];
            unset($_GET['advanced-filter']['nama_pemakai']);
        }

        if (isset($filter['peranpengguna_k.peranpenggunanama'])) {
            $hakAkses = $filter['peranpengguna_k.peranpenggunanama'];
            unset($_GET['advanced-filter']['peranpengguna_k.peranpenggunanama']);
        }

        $query = Loginpemakai::find()->select([
                'loginpemakai_k.loginpemakai_id',
                'loginpemakai_k.nama_pemakai',
                'peranpengguna_k.peranpenggunanama'
        ])->joinWith([
            'aksesPengguna' => function ($query) {
                $query->select([
                    'aksespengguna_k.peranpengguna_id',
                    'aksespengguna_k.loginpemakai_id',
                    'peranpengguna_k.peranpenggunanama'
                ])->joinWith([
                    'peranPengguna' => function ($query) {
                        $query->select([
                            'peranpengguna_k.peranpengguna_id'
                        ]);
                    }
                ]);
            }
        ]);

        if($namaPemakai) {
            $query->andFilterWhere([
                'ILIKE', 'loginpemakai_k.nama_pemakai', $namaPemakai 
            ]);
        }

        if($hakAkses) {
            $query->andFilterWhere([
                'ILIKE', 'peranpengguna_k.peranpenggunanama', $hakAkses 
            ]);
        }
        
        $result = $query->asArray()->all();

        $data = [];
        foreach ($result as $key => $value) {
            $newValue = [];
            $aksesPengguna = [];
            foreach ($value['aksesPengguna'] as $val) {
                $aksesPengguna[] = $val['peranpenggunanama'];
            }
            $newValue['peran_pengguna'] = $value['nama_pemakai'];
            $newValue['hak_akses'] = empty($aksesPengguna) ? '-' : implode(', ', $aksesPengguna);
            $data[$key] = $newValue;
        }
        $print = new DocoPrint();    
        $print->attributes = [
            '#akses_pengguna#' => $this->renderPartial('index',[
                'detail' => $data,
            ]),            
        ];
        $print->Output();
    }

}
