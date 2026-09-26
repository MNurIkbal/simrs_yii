<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\MenuModul;
use Doco\models\KelompokMenu;
use Doco\models\KelompokMenuGroup;
use Doco\components\DocoHelpers;

class MenuController extends \Doco\components\DocoActiveController
{
    protected $_tmpMenu = [];
    protected $_renderMenu = [];
    public $modelClass = 'app\modules\v1\models\Menumodul';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["create"] = ["POST"];
        $verbs["grouping-menus"] = ["POST"];
        $verbs["view"] = ["POST","GET"];
        $verbs["get-child"] = ["GET"];
        $verbs["sync-list-api"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['view']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $query = KelompokMenu::find()
                    ->select([
                        'kelompokmenu_k.kelmenu_id',
                        'kelompokmenu_k.kelmenu_nama',
                        'kelompokmenu_k.kelmenu_key',
                        'kelompokmenu_k.kelmenu_url',
                        'kelompokmenu_k.kelmenu_icon'])
                    ->joinWith([
                    'menuModul' => function ($query) use ($request) {
                        $query->select([
                            'menu_id',
                            'kelmenu_id',
                            'modul_id',
                            'menu_nama',
                            'menu_namalainnya',
                            'menu_key',
                            'menu_url',
                            'menu_fungsi',
                            'menu_urutan',
                            'menu_icon',
                            'parentmenu_id',
                            'groupmenu_id'
                        ])->where(['parentmenu_id' => null])
                          ->orderBy(['menu_urutan' => SORT_ASC]);
                    }
                ]);

        return $query->asArray()->all();
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $model = new MenuModul;
                $model->attributes = $request->post();
                $kelmenu_id = $model->kelmenu_id; 

                if ($request->post('akses') && is_array($request->post('akses'))) {
                    $dataInsert = [];
                    $akses = $request->post('akses');
                    $firstKey = isset($akses[0]) ? json_decode($akses[0],true) : [];
                    $model->menu_key = isset($firstKey['menu_key']) ? $firstKey['menu_key'] : '';
                    $model->modul_id = isset($firstKey['modul_id']) ? $firstKey['modul_id'] : '';

                    if ($model->save()) {
                        foreach ($akses as $key => $value) {
                            $toArray = json_decode($value,true);
                            if (isset($toArray['child'])) {
                                foreach ($toArray['child'] as $key => $value) {
                                    $dataInsert[] = [
                                        'kelmenu_id' => $model->kelmenu_id,
                                        'menu_key' => $value['menu_key'],
                                        'menu_nama' => $value['menu_nama'],
                                        'menu_namalainnya' => $value['menu_namalainnya'],
                                        'modul_id' => $value['modul_id'],
                                        'menu_url' => null,
                                        'menu_fungsi' => null,
                                        'menu_urutan' => null,
                                        'menu_icon' => null,
                                        'menu_shortcut' => null,
                                        'parentmenu_id' => $model->menu_id,
                                        'groupmenu_id' => null
                                    ];
                                }
                            }
                        }
                        MenuModul::batchInsert($dataInsert,false);

                        $cache = KelompokMenuGroup::find(true)->where([
                            'kelompokmenu_id' => $kelmenu_id
                        ])->one();

                        if (empty($cache)) {
                            $cache = new KelompokMenuGroup;
                        }

                        $cache->kelompokmenu_id = $kelmenu_id;
                        $cache->data = serialize($this->serializeMenus($kelmenu_id));
                        $cache->save();
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
                } else {
                    if ($model->save()) {
                        $cache = KelompokMenuGroup::find(true)->select([
                            'kelompokmenu_id',
                            'encode(data::bytea, \'base64\') as data'
                        ])->where([
                            'kelompokmenu_id' => $kelmenu_id
                        ])->one();

                        if (!empty($cache)) {
                            $data_attr = [
                                'menu_id' => $model->menu_id,
                                'kelmenu_id' => $model->kelmenu_id,
                                'modul_id' => $model->modul_id,
                                'menu_nama' => $model->menu_nama,
                                'menu_namalainnya' => $model->menu_namalainnya,
                                'menu_key' => $model->menu_key,
                                'menu_url' => $model->menu_url,
                                'menu_fungsi' => $model->menu_fungsi,
                                'menu_urutan' => $model->menu_urutan,
                                'menu_icon' => $model->menu_icon,
                                'menu_shortcut' => $model->menu_shortcut,
                                'parentmenu_id' => $model->parentmenu_id,
                                'groupmenu_id' => $model->groupmenu_id,
                                'action' => [],
                                'item' => []
                            ];
                            $cache_data = !empty($cache->data) ? unserialize(base64_decode($cache->data)) : [];
                            $cache_data[] = $data_attr;
                            $cache->kelompokmenu_id = $kelmenu_id;
                            $cache->data = serialize($cache_data);
                        } else {
                            $cache = new KelompokMenuGroup;
                            $cache->kelompokmenu_id = $kelmenu_id;
                            $cache->data = serialize($this->serializeMenus($kelmenu_id));
                        }

                        $cache->save();
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [ 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    
    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = MenuModul::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                $kelmenu_id = $model->kelmenu_id; 
                if ($model->save()) {

                    $cache = KelompokMenuGroup::find(true)->select([
                        'kelompokmenu_id',
                        'encode(data::bytea, \'base64\') as data'
                    ])->where([
                        'kelompokmenu_id' => $kelmenu_id
                    ])->one();

                    if (!empty($cache)) {
                        $cache_data = !empty($cache->data) ? unserialize(base64_decode($cache->data)) : [];
                        $cache->kelompokmenu_id = $kelmenu_id;
                        $cache->data = serialize($this->updateCache($cache_data,$model, $id));
                    } else {
                        $cache = new KelompokMenuGroup;
                        $cache->kelompokmenu_id = $kelmenu_id;
                        $cache->data = serialize($this->serializeMenus($kelmenu_id));
                    }
                    $cache->save();
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    public function actionSyncListApi($id_menu)
    {
        $request = Yii::$app->request;
        if ($request->post() && $request->post('akses')) {
            $model = new MenuModul;
            $menuparent = MenuModul::find()->where(['menu_id' => $id_menu])->one();
            $childExsist = MenuModul::find()->select([
                    'menu_id',
                    'menu_key'
                ])->where(['parentmenu_id' => $id_menu])->orderBy(['menu_urutan' => SORT_ASC])->all();
            $actionExist = [];
            foreach ($childExsist as $val) {
                if (!empty($val->menu_key)) {
                    $actionExist[$val->menu_key] = $val->menu_id;
                }
            }
            $dataInsert = [];
            if ($akses = $request->post('akses')) {
                $kelmenu_id = $menuparent->kelmenu_id;
                foreach ($akses as $key => $value) {
                    $toArray = $value;
                    if (isset($toArray['child'])) {
                        foreach ($toArray['child'] as $key => $value) {
                            if (isset($actionExist[$value['menu_key']])) {
                                unset($actionExist[$value['menu_key']]);
                            } else {
                                $dataInsert[] = [
                                    'kelmenu_id' => $kelmenu_id,
                                    'menu_key' => $value['menu_key'],
                                    'menu_nama' => $value['menu_nama'],
                                    'menu_namalainnya' => $value['menu_namalainnya'],
                                    'modul_id' => $value['modul_id'],
                                    'menu_url' => null,
                                    'menu_fungsi' => null,
                                    'menu_urutan' => null,
                                    'menu_icon' => null,
                                    'menu_shortcut' => null,
                                    'parentmenu_id' => $id_menu,
                                    'groupmenu_id' => null
                                ];
                            }
                        }
                    }
                }

                $model->delete(['menu_id' => $actionExist]);
                if ($dataInsert) {
                    MenuModul::batchInsert($dataInsert);
                }

                $cache = KelompokMenuGroup::find(true)->where([
                    'kelompokmenu_id' => $kelmenu_id
                ])->one();

                if (empty($cache)) {
                    $cache = new KelompokMenuGroup;
                }

                $cache->kelompokmenu_id = $kelmenu_id;
                $cache->data = serialize($this->serializeMenus($kelmenu_id));
                $cache->save();
                return ['message' => 'Data Berhasil di simpan'];
            }
        }
    }

    // public function actionSyncListApi($id_menu)
    // {
    //     $request = Yii::$app->request;
    //     if ($request->post() && $request->post('akses')) {
    //         $model = new MenuModul;
    //         $menuparent = MenuModul::find()->where(['menu_id' => $id_menu])->one();
    //         if ($model->delete(['parentmenu_id' => $id_menu])) {
    //             $akses = $request->post('akses');
    //             $kelmenu_id = $menuparent->kelmenu_id;
    //             foreach ($akses as $key => $value) {
    //                 $toArray = $value;
    //                 if (isset($toArray['child'])) {
    //                     foreach ($toArray['child'] as $key => $value) {
    //                         $dataInsert[] = [
    //                             'kelmenu_id' => $kelmenu_id,
    //                             'menu_key' => $value['menu_key'],
    //                             'menu_nama' => $value['menu_nama'],
    //                             'menu_namalainnya' => $value['menu_namalainnya'],
    //                             'modul_id' => $value['modul_id'],
    //                             'menu_url' => null,
    //                             'menu_fungsi' => null,
    //                             'menu_urutan' => null,
    //                             'menu_icon' => null,
    //                             'menu_shortcut' => null,
    //                             'parentmenu_id' => $id_menu,
    //                             'groupmenu_id' => null
    //                         ];
    //                     }
    //                 }
    //             }
    //             MenuModul::batchInsert($dataInsert);

    //             $cache = KelompokMenuGroup::find(true)->where([
    //                 'kelompokmenu_id' => $kelmenu_id
    //             ])->one();

    //             if (empty($cache)) {
    //                 $cache = new KelompokMenuGroup;
    //             }

    //             $cache->kelompokmenu_id = $kelmenu_id;
    //             $cache->data = serialize($this->serializeMenus($kelmenu_id));
    //             $cache->save();
    //             return ['message' => 'Data Berhasil di simpan'];
    //         }
    //     }
    // }
    public function actionGroupingMenus($id_parent)
    {
        $request = Yii::$app->request;
        $data_menu = [];
        if ($post = $request->post()) {
            $data_menu = $post['data_menu'];
            $result = $this->groupingMenus($data_menu);
            $cache = KelompokMenuGroup::find(true)->where([
                'kelompokmenu_id' => $id_parent
            ])->one();

            if (empty($cache)) {
                $cache = new KelompokMenuGroup;
            }

            $cache->kelompokmenu_id = $id_parent;
            $cache->data = serialize($this->serializeMenus($id_parent));
            $cache->save();
        }
        return $data_menu;
    }

    public function actionDelete($id)
    {
        try {
            MenuModul::updateAll(['groupmenu_id' => null],[
                            'groupmenu_id' => $id
                        ]);
            
            $result = MenuModul::find()->where([
                'menu_id' => $id
            ])->one();
            
            $kelmenu_id = !empty($result->kelmenu_id) ? $result->kelmenu_id : false;

            if ($kelmenu_id && $result->delete()) {
                $cache = KelompokMenuGroup::find(true)->where([
                    'kelompokmenu_id' => $kelmenu_id
                ])->one();

                if (empty($cache)) {
                    $cache = new KelompokMenuGroup;
                }

                $cache->kelompokmenu_id = $kelmenu_id;
                $cache->data = serialize($this->serializeMenus($kelmenu_id));
                $cache->save();
            }
            return ['message' => 'Data Berhasil di simpan'];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id = null)
    {
        $request = Yii::$app->request;
        $query = MenuModul::find()->select([
            'menu_id',
            'kelmenu_id',
            'modul_id',
            'menu_nama',
            'menu_namalainnya',
            'menu_key',
            'menu_url',
            'menu_fungsi',
            'menu_urutan',
            'menu_icon',
            'menu_shortcut',
            'parentmenu_id',
            'groupmenu_id',
        ]);

        if ($id) {
            return $query->where([
                            'menu_id' => $id,
                        ])->orderBy(['menu_urutan' => SORT_ASC])->asArray()->one();
        }

        if ($kelmenu_id = $request->post('kelmenu_id')) {
            $query->andWhere(['kelmenu_id' => $kelmenu_id]);
        }
        $query->andWhere(['parentmenu_id' => null])
            ->orderBy(['menu_urutan' => SORT_ASC]);
        return $query->asArray()->all();
    }

    public function actionGetChild($id_menu)
    {
        $query = MenuModul::find()->select([
            'menu_id',
            'kelmenu_id',
            'modul_id',
            'menu_nama',
            'menu_namalainnya',
            'menu_key',
            'menu_url',
            'menu_fungsi',
            'menu_urutan',
            'menu_icon',
            'menu_shortcut',
            'parentmenu_id',
            'groupmenu_id',
        ])->where([
            'parentmenu_id' => $id_menu
        ])->orderBy(['menu_urutan' => SORT_ASC])->asArray()->all();
        return $query;
    }

    /**
    *  @param array $data
    *  @param object $data_update Doco\models\MenuModul
    *  @param integer $id
    *  
    *  @return array
    **/
    private function updateCache($data,$data_update, $id)
    {
        $cache_update = [];
        foreach ($data as $value) {
            if ($value['menu_id'] == $id) {
                $value['menu_namalainnya'] = $data_update->menu_namalainnya;
                $value['menu_url'] = $data_update->menu_url;
                $value['menu_fungsi'] = $data_update->menu_fungsi;
                $value['menu_icon'] = $data_update->menu_icon;
            } else {
                if (isset($value['item'])) {
                    $dataItem = $this->updateCache($value['item'],$data_update, $id);
                    $value['item'] = [];
                    if ($dataItem) {
                        $value['item'] = $dataItem;
                    }
                }
            }
            $cache_update[] = $value;
        }
        return $cache_update;

    }

    private function serializeMenus($kelmenu_id)
    {
        $query = MenuModul::find()->select([
            'menu_id',
            'kelmenu_id',
            'modul_id',
            'menu_nama',
            'menu_namalainnya',
            'menu_key',
            'menu_url',
            'menu_fungsi',
            'menu_urutan',
            'menu_icon',
            'menu_shortcut',
            'parentmenu_id',
            'groupmenu_id',
        ])->where(['kelmenu_id' => $kelmenu_id])->orderBy(['menu_urutan' => SORT_ASC])->asArray()->all();

        foreach ($query as $val) {
            if (empty($val['parentmenu_id'])) {
                if (!empty($val['groupmenu_id'])) {
                    $this->_tmpMenu['child'][$val['groupmenu_id']][] = $val;
                } else {
                    $this->_tmpMenu[0][] = $val;
                }
            } else {
                $this->_tmpMenu['action'][$val['parentmenu_id']][] = $val;
            }
        }

        $this->setParsingMenus(0);

        return $this->_renderMenu;
    }

    private function setParsingMenus($id_parent = 0)
    {
        if (!$id_parent) {
            $data = $this->_tmpMenu[0];
            foreach ($data as $value) {
                $value['action'] = isset($this->_tmpMenu['action'][$value['menu_id']]) 
                                    ? $this->_tmpMenu['action'][$value['menu_id']] 
                                    : [];
                $dataChild = $this->setParsingMenus($value['menu_id']);
                $value['item'] = [];
                if ($dataChild) {
                    $value['item'] = $dataChild;
                }
                $this->_renderMenu[] = $value;
            }
        } else {
            $data = isset($this->_tmpMenu['child'][$id_parent]) 
                        ? $this->_tmpMenu['child'][$id_parent] 
                        : []; 
            $data_return = [];
            foreach ($data as $key => $value) {
                $value['action'] = isset($this->_tmpMenu['action'][$value['menu_id']]) 
                                    ? $this->_tmpMenu['action'][$value['menu_id']] 
                                    : [];
                $dataChild = $this->setParsingMenus($value['menu_id']);
                $value['item'] = [];
                if ($dataChild) {
                    $value['item'] = $dataChild;
                }
                $data_return[] = $value;
            }
            return $data_return;
        }
        return false;
    }

    /**
    *  @param array $data
    *  
    *  @return void
    **/

    private function groupingMenus(array $data, $id_parent = null)
    {
        $data_update = [];
        foreach ($data as $value) {
            $id = DocoHelpers::decrypt($value['id']);

            $data_update[] = $id;

            if (isset($value['children'])) {
                $this->groupingMenus($value['children'], $id);
            }
        }

        $groupmenu_id = null;

        if ($id_parent) {
            $groupmenu_id = $id_parent;
        }

        $data_bulk = null;
        foreach ($data_update as $key => $value) {
            $data_bulk[] = [
                'menu_id' => $value,
                'menu_urutan' => $key
            ];
        }

        $sql = $this->batchUpdate("menumodul_k", "menu_id", "menu_urutan", $data_bulk);
        $res = Yii::$app->db->createCommand($sql)->execute();

        return MenuModul::updateAll(['groupmenu_id' => $groupmenu_id], [
            'menu_id' => $data_update
        ]);
    }

    public function batchUpdate($table, $key, $val, $data){
        $ids = implode(",", array_column($data, $key));
        $condition = " ";
        foreach ($data as $v){
            $condition .= "WHEN {$v[$key]} THEN {$v[$val]} ";
        }
        $sql = "UPDATE {$table} SET  {$val} = CASE {$key} {$condition} END WHERE {$key} in ({$ids})";
        return $sql;
    }
}
