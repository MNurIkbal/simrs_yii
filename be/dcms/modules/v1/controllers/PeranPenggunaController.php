<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\models\MenuModul;
use Doco\models\Modul;
use Doco\models\KelompokMenu;
use Doco\models\KelompokMenuGroup;
use Doco\models\PeranPengguna;
use Doco\models\AksesPengguna;
use Doco\components\DocoPrint;

class PeranPenggunaController extends DocoActiveController
{

    public $modelClass = PeranPengguna::class;

    protected $_tmpMenu = [];
    protected $_parent = [];
    protected $_skipMenu = [];
    protected $_menuKey = [
        'informasi',
        'master',
        'aplikasi',
        'laporan',
        'transaksi'
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["get-menus"] = ["GET"];
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
        // $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'modul_k');
        // // return $_GET['advanced-filter'];
        // $model = new PeranPengguna;
        // $query = $model::find()
        //     ->joinWith(['modul' => function($query){
        //         $query->from('modul_k');
        //     }]);
        // if($_GET['advanced-filter']){
        //     if($_GET['advanced-filter']['modul_k.modul_namalainnya']){
        //         $query->where()
        //     }
        // }
        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);

        $request = Yii::$app->request;
        $get = $request->get();

        $query = PeranPengguna::find()->select([
            'peranpengguna_k.peranpengguna_id',
            'peranpengguna_k.peranpenggunanama',
            'peranpengguna_k.peranpenggunanamalain',
            'peranpengguna_k.modul_id',
            'peranpengguna_k.peranpengguna_aktif',
        ])
        ->joinWith([
            'modul' => function ($query) {
                $query->select([
                    'modul_k.modul_id',
                    'modul_k.modul_nama',
                    'modul_k.modul_namalainnya',
                ]);
            }
        ])->asArray();

        $query = DocoRestActiveFilter::advancedFilter(new PeranPengguna, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $akses = $this->generateAkses($request);
            $model = new PeranPengguna;
            $model->attributes = $request->post();
            $model->peranpengguna_menu = serialize($akses->menu);
            $model->peranpengguna_akses = serialize($akses->action);
            if ($model->save()) {
                return [
                    'message' => 'Data Berhasil di simpan',
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } else {
            return $this->getData();
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $akses = $this->generateAkses($request);
            $model = PeranPengguna::find()->where(['peranpengguna_id' => $id])->one();

            if (empty($model)) {
                $model = new PeranPengguna;
            }
            $model->attributes = $request->post();
            if(empty($akses->menu) || empty($akses->action)){
                return [
                    'data' => null,
                    'status' => 422,
                    'text' => 'Menu Kosong'
                ];
            }
            $model->peranpengguna_menu = serialize($akses->menu);
            $model->peranpengguna_akses = serialize($akses->action);
            if ($model->save()) {
                return [
                    'message' => 'Data Berhasil di simpan',
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } else {
            $model = PeranPengguna::find()->select([
                'peranpengguna_id',
                'peranpenggunanama',
                'peranpenggunanamalain',
                'peranpengguna_aktif',
                'is_active',
                'is_exception',
                'peranpengguna_akses',
                'modul_id'
            ])->where(['peranpengguna_id' => $id])->asArray()->one();

            $data = $this->getData($id);
            if (isset($model['peranpengguna_akses'])) {
                $model['peranpengguna_akses'] = unserialize($model['peranpengguna_akses']);
            }
            $data['peran_pengguna'] = $model;
            return $data;
        }
    }

    public function actionDelete($id)
    {
        try {
            $count = AksesPengguna::find()->where(['peranpengguna_id' => $id])->count();
            if ($count) {
                return [
                    'status' => 422,
                    'title' => 'Proses gagal !',
                    'text' => 'Data tidak dapat di hapus'
                ];
            }
            $result = (new PeranPengguna)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetMenus($id_modul,$id_parent = null)
    {
        $query = KelompokMenu::find()->select([
            'kelompokmenu_k.kelmenu_id',
            'kelompokmenu_k.kelmenu_nama',
            'kelompokmenu_k.kelmenu_key',
            'kelompokmenu_k.kelmenu_url',
            'kelompokmenu_k.kelmenu_icon',
        ])->joinWith([
            'item' => function ($query) {
                $query->select([
                    'kelompokmenugroup_k.kelompokmenu_id',
                    'encode(kelompokmenugroup_k.data::bytea, \'base64\') as data',
                ]);
            }
        ])->where([
            'kelompokmenu_k.is_active' => true
        ])->orderBy(['kelompokmenu_k.kelmenu_nama' => SORT_ASC])->asArray()->all();
        $aksesPengguna = [];
        if ($id_parent) {
            $model = PeranPengguna::find()->where(['peranpengguna_id' => $id_parent])->one();
            $aksesPengguna = !empty($model->peranpengguna_akses) 
                                ? unserialize($model->peranpengguna_akses) 
                                : [];
        }
        $data = [];
        foreach ($query as $key => $value) {
            $item = isset($value['item']['data']) ? unserialize(base64_decode($value['item']['data'])) : [];
            if (in_array($value['kelmenu_key'], $this->_menuKey)) {
                $data[$key]= $value;
                $data[$key]['item']['data'] = $item;
            } else {
                $this->_tmpMenu = [];
                $this->normalizationMenu($item,false);
                // Filter dengan id modul yang sama
                $data_filter = $checkParent = [];

                foreach ($this->_tmpMenu as $id_menu => $item) {
                    if ($item['modul_id'] == $id_modul) {
                        $dataPase = $this->_tmpMenu[$id_menu];
                        $this->checkParent($dataPase);
                    }
                }
                // Refreseh _tmpMenu
                $this->_tmpMenu = [];
                $this->parsingMenus();
                $data[$key]= $value;
                $dataCild = isset($this->_tmpMenu[$value['kelmenu_id']]) 
                                                ? $this->_tmpMenu[$value['kelmenu_id']] 
                                                : [];
                $data[$key]['item']['data'] = [];
                if ($dataCild) {
                    $data[$key]['item'] = $dataCild;
                }
            }
        }

        return [
            'kelompok_menu' => $data,
            'akses_pengguna' => $aksesPengguna
        ];
    }

    private function getData($id = null)
    {
        $modul = ArrayHelper::map(
                Modul::find()->where(['NOT IN','modul_key',['dcms','master']])->all(),
                'modul_id',
                'modul_nama');

        $result = [
            'modul' => $modul
        ];

        return $result;
    }

    private function generateAkses($request)
    {
        $kelompok_menu = [];
        if ($kel_id = $request->post('kelompokmenu_id')) {
            $kelompok_menu = KelompokMenuGroup::find()
                                ->select([
                                    'kelompokmenu_id',
                                    'encode(data::bytea, \'base64\') as data'
                                ])
                                ->where(['kelompokmenu_id' => $kel_id])
                                ->asArray()->all();
        }

        $dataChaceMenu = $dataChaceAksi = [];
        $menuPeranCache = [];
        // Untuk Menormalkan data chace menu
        foreach ($kelompok_menu as $val) {
            $data = isset($val['data']) ? unserialize(base64_decode($val['data'])) : [];
            $this->normalizationMenu($data);
        }

        $peranAkses = $checkParent = [];
        if ($action_akses = $request->post('action_akses')) {
            foreach ($action_akses as $val) {
                $id_menu = DocoHelpers::decrypt($val);
                if (isset($this->_tmpMenu[$id_menu])) {
                    $data_menu = $this->_tmpMenu[$id_menu];
                    $peranAkses[$id_menu] = $data_menu['menu_key'];
                    // Sekip ketika udah ada parentnya
                    $menuPeranCache[$data_menu['parentmenu_id']][] = $data_menu['menu_nama'];
                    // if (!isset($checkParent[$data_menu['parentmenu_id']])) {
                    //     $data = $this->_tmpMenu[$data_menu['parentmenu_id']];
                        // $this->checkParent($data);
                    //     $checkParent[$data_menu['parentmenu_id']] = true;
                    // }
                }
            }
            // Refreseh _tmpMenu
            // $this->_tmpMenu = [];
            // $this->parsingMenus();
            // $kelompokMenu = KelompokMenu::find()->asArray()->all();
            // foreach ($kelompokMenu as $value) {
            //     $menuPeranCache[] = [
            //         'kelmenu_id' => $value['kelmenu_id'],
            //         'kelmenu_nama' => $value['kelmenu_nama'],
            //         'kelmenu_url' => $value['kelmenu_url'],
            //         'kelmenu_icon' => $value['kelmenu_icon'],
            //         'item' => isset($this->_tmpMenu[$value['kelmenu_id']]) 
            //                         ? $this->_tmpMenu[$value['kelmenu_id']] 
            //                         : []
            //     ];
            // }
        }

        return (object) [
            'menu' => $menuPeranCache,
            'action' => $peranAkses
        ];
    }

    /**
    ** @var integer $id_parent
    * @return array|void
    **/
    private function parsingMenus($id_parent = 0)
    {
        if (isset($this->_parent[$id_parent])) {
            if (!$id_parent) {
                foreach ($this->_parent[$id_parent] as $value) {
                    $kelmenu_id = $value['kelmenu_id'];
                    if (!isset($this->_tmpMenu[$kelmenu_id])) {
                        $this->_tmpMenu[$kelmenu_id] = [
                            'kelompokmenu_id' => $kelmenu_id,
                            'data' => []
                        ];
                    }
                    $dataChild = $this->parsingMenus($value['menu_id']);
                    $value['item'] = [];
                    if ($dataChild) {
                        $value['item'] = $dataChild;
                    }
                    $this->_tmpMenu[$kelmenu_id]['data'][] = $value;
                }
            } else {
                $data_return = [];
                foreach ($this->_parent[$id_parent] as $value) {
                    $dataChild = $this->parsingMenus($value['menu_id']);
                    $value['item'] = [];

                    if ($dataChild) {
                        $value['item'] = $dataChild;
                    }

                    $data_return[] = $value;
                }

                return $data_return;
            }
        }

        return false;
    }

    /**
    * @var array $data
    * @return void
    **/
    private function checkParent($data)
    {
        if (empty($data['groupmenu_id']) || !isset($this->_skipMenu[$data['menu_id']])) {
            if (!empty($data['groupmenu_id'])) {
                $this->_parent[$data['groupmenu_id']][] = $data;
                $this->_skipMenu[$data['menu_id']] = true;
            } 
            if (!empty($this->_tmpMenu[$data['parentmenu_id']])) {
                $data = $this->_tmpMenu[$data['parentmenu_id']];
            }

            if (!empty($this->_tmpMenu[$data['groupmenu_id']])) {
                $data = $this->_tmpMenu[$data['groupmenu_id']];
            }

            if (empty($data['groupmenu_id']) && !isset($this->_parent[0][$data['menu_id']])) {
                $this->_parent[0][$data['menu_id']] = $data;
            } else {
                if (!empty($data['groupmenu_id']) && !isset($this->_skipMenu[$data['menu_id']])) {
                    $this->_parent[$data['groupmenu_id']][] = $data;
                }

                $this->_skipMenu[$data['menu_id']] = true;
                if (isset($this->_tmpMenu[$data['groupmenu_id']])) {
                    $this->checkParent($this->_tmpMenu[$data['groupmenu_id']]);
                }
            }
        }
    }

    /**
    * @var array $data 
    * @return array $this->_tmpMenu
    **/
    private function normalizationMenu(array $data, $no_action = true)
    {
        foreach ($data as $key => $value) {
            $value_item = $value_action = [];

            if (!empty($value['item'])) {
                $this->normalizationMenu($value['item'],$no_action);
                unset($value['item']);
            }

            if (!empty($value['action']) && $no_action) {
                $this->normalizationMenu($value['action'], $no_action);
                unset($value['action']);
            }
            $this->_tmpMenu[$value['menu_id']] = $value;
        }

        return $this->_tmpMenu;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Peran Pengguna';
        if ($request->get()) {
            $get = $request->get();

            $model = new PeranPengguna;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('pdf', [
                    'data' => $query->all(),
                    'title' => $title,
                ]),
            ];

            $print->Output();
        }
        \Yii::$app->response->statusCode = 500;
        return ['message' => 'Tidak Ada Data yang harus di cetak'];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $type = $request->get('type');

        $model = new PeranPengguna;
        $title = 'Peran Pengguna';

        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        foreach ($query->all() as $key => $value) {
            $newValue = [];

            $newValue[\Yii::t('app', 'Nama Peran Pengguna')] = $value['peranpenggunanama'];
            $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['peranpenggunanamalain'];
            $newValue[\Yii::t('app', 'Modul')] = $value['modul']['modul_nama'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
            
            $result[$key] = $newValue;
        }

        $header = array();

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }
}
