<?php

namespace Doco\components;

use Yii;
use Doco\models\Modul;
use Doco\models\KelompokMenu;
use Doco\models\MenuModul;
use Doco\models\KelompokMenuGroup;
use Doco\components\DocoHelpers;

trait ConfigTrait 
{
    protected $_tmpMenu = [];
    protected $_renderMenu = [];
    protected $_groupMenu = [];
    protected $_globCond;
    protected $_kelMenu = [
        'Informasi',
        'Laporan',
        'Transaksi',
        'Master',
        'Aplikasi'
    ];

    public function actionConfig()
    {
        $request = Yii::$app->request;
        if ($request->getQueryParam('create-module')) {
            $modulName = Yii::$app->params['service'];
            $modul = Modul::find()->where(['modul_key' => strtolower($modulName)])->one();
            if (empty($modul)) {
                $modul = new Modul;
                $attributes = [
                    'modul_nama' => $modulName,
                    'modul_namalainnya' => $modulName,
                    'modul_key' => strtolower($modulName),
                    'modul_urutan' => strtolower($modulName),
                ];
                $modul->attributes = $attributes;

                if (!$modul->save()) {
                    \Yii::trace('Terjadi Kesalahan' . __METHOD__);
                    \Yii::$app->response->statusCode = 500;
                    return $modul->errors;
                }
            } 
            $modul_id = $modul->modul_id;

            $createKel = $this->createKelMenu();

            // Check Menu Apakah Sudah ada atau belum
            $data_tmp = $data_menu_modul = $parent_identity = [];
            $tmp_menus = [];
            $menuModul = MenuModul::find()->select([
                                'menumodul_k.menu_id',
                                'menumodul_k.kelmenu_id',
                                'menumodul_k.modul_id',
                                'menumodul_k.menu_key',
                                'menumodul_k.parentmenu_id',
                        ])->joinWith([
                            'kelmenu' => function ($query) {
                                $query->select([
                                    'kelompokmenu_k.kelmenu_nama',
                                    'kelompokmenu_k.kelmenu_key',
                                    'kelompokmenu_k.kelmenu_id',
                                ]);
                            },
                            'modul' => function ($query) {
                                $query->select([
                                    'modul_k.modul_key',
                                    'modul_k.modul_id',
                                ]);
                            }
                        ])->where([
                            'modul_k.modul_id' => $modul_id
                        ])->orderBy(['menumodul_k.menu_urutan' => SORT_ASC])->all();

            foreach ($menuModul as $val) {
                if (!empty($val->kelmenu->kelmenu_key) && !empty($val->modul->modul_key)) {
                    $kelmenu_key = $val->kelmenu->kelmenu_key;
                    $modul_key = $val->modul->modul_key;
                    if (empty($val->parentmenu_id)) {
                        $parent_identity[$kelmenu_key][$modul_key][$val->menu_key] = $val->menu_id;
                    } else {
                        $data_tmp[$kelmenu_key][$modul_key][$val->parentmenu_id][] = $val->menu_key;
                    }
                }
            }

            $get_pages = $this->getPages();
            $key_module = strtolower($modulName);
            $kelMenu = [];
            foreach ($get_pages as $key => $value) {
                // Untuk Menandakan Menu Masuk Ke kelompok mana
                $connection = \Yii::$app->db;
                $transaction = $connection->beginTransaction();
                try {

                    $name_controller = preg_replace("/(?<=\w)(?=[A-Z])/"," $1", $key);
                    $string = preg_replace("/(Controller)/","", $name_controller);
                    $format_key = $key_module . '-' . $name_controller;
                    
                    $check_kel_menu = $this->checkRegex($string);
                    $kelmenu_id = isset($createKel[$check_kel_menu]) ? $createKel[$check_kel_menu] : '';

                    $key_secret_menu = DocoHelpers::encrypt($format_key);

                    foreach ($value as $item) {

                        if (preg_match("/allow/i", $item)) continue;

                        if (!isset($parent_identity[$check_kel_menu][$key_module][$key_secret_menu])) {
                           $parent = [
                                'kelmenu_id' => $kelmenu_id,
                                'modul_id' => $modul_id,
                                'menu_nama' => $string,
                                'menu_namalainnya' => $string,
                                'menu_key' => DocoHelpers::encrypt($format_key),
                            ];
                            $menuModul = new MenuModul;
                            $menuModul->attributes = $parent;
                            $menuModul->save(false);

                            $parent_identity[$check_kel_menu][$key_module][$key_secret_menu] = $menuModul->menu_id;
                            $id_parent = $menuModul->menu_id;
                        }

                        $id_parent = $parent_identity[$check_kel_menu][$key_module][$key_secret_menu];
                        $format_key = $key_module . '-' . $name_controller . '-' . $item;
                        $child_encrypt = DocoHelpers::encrypt($format_key);

                        if (isset($data_tmp[$check_kel_menu][$key_module][$id_parent])) {
                            if (!in_array($child_encrypt, $data_tmp[$check_kel_menu][$key_module][$id_parent])) {
                                $data_menu_modul[] = [
                                    'kelmenu_id' => $kelmenu_id,
                                    'modul_id' => $modul_id,
                                    'menu_nama' => $item,
                                    'menu_namalainnya' => $item,
                                    'menu_key' => DocoHelpers::encrypt($format_key),
                                    'parentmenu_id' => $id_parent
                                ];
                            }
                        } else {
                            $data_menu_modul[] = [
                                'kelmenu_id' => $kelmenu_id,
                                'modul_id' => $modul_id,
                                'menu_nama' => $item,
                                'menu_namalainnya' => $item,
                                'menu_key' => DocoHelpers::encrypt($format_key),
                                'parentmenu_id' => $id_parent
                            ];
                        }

                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                }
            }

            if ($data_menu_modul) {
                MenuModul::batchInsert($data_menu_modul);
            }

            if ($request->getQueryParam('flag')) {
                
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
                ])->asArray()->all();

                foreach ($query as $key => $value) {
                    if (!empty($value['kelmenu_id'])) {
                        $kelMenu[$value['kelmenu_id']] = $value['kelmenu_id'];
                        if (!isset($this->_groupMenu[$value['kelmenu_id']])) {
                            $this->_groupMenu[$value['kelmenu_id']] = [];
                        }
                        $this->_groupMenu[$value['kelmenu_id']][] = $value;
                    }
                }

                Yii::$app->db->createCommand("DELETE FROM kelompokmenugroup_k")->execute();
                $kelMenuGroup = [];
                foreach ($kelMenu as $val) {
                    $kelMenuGroup[] = [
                        'kelompokmenu_id' => $val,
                        'data' => serialize($this->serializeMenus($val)),
                        'created' => date('Y-m-d H:i:s')
                    ];
                    KelompokMenuGroup::batchInsert($kelMenuGroup);
                    $kelMenuGroup = [];
                }

            }

        }
    }

    public function actionGetListControllers()
    {
        $modulName = Yii::$app->params['service'];
        $modul = Modul::find()->where(['modul_key' => strtolower($modulName)])->one();
        $pages = $this->getPages();
        $key_module = strtolower($modulName);
        $modul_id = !empty($modul->modul_id) ? $modul->modul_id : null;
        $dataList = $collectData = [];
        foreach ($pages as $key => $value) {
            $dataList[] = ['id' => strtolower($modulName) .'-'. $key, 'name' => strtoupper($modulName) .' - ' .$key];
            $name_controller = preg_replace("/(?<=\w)(?=[A-Z])/"," $1", $key);
            $format_key = $key_module . '-' . $name_controller;

            $collectData[strtolower($modulName) .'-'. $key] = [
                'kelmenu_id' => '',
                'modul_id' => $modul_id,
                'menu_nama' => '',
                'menu_namalainnya' => '',
                'menu_key' => DocoHelpers::encrypt($format_key),
                'child' => []
            ];

            foreach ($value as $item) {
                if (preg_match("/allow/i", $item)) continue;
                $format_key = strtolower($modulName) . '-' . $name_controller . '-' . $item;
                $collectData[strtolower($modulName) .'-'. $key]['child'][] = [
                    'kelmenu_id' => '',
                    'modul_id' => $modul_id,
                    'menu_nama' => $item,
                    'menu_namalainnya' => $item,
                    'menu_key' => DocoHelpers::encrypt($format_key),
                    'parentmenu_id' => 0
                ];
            }
        }

        return [
            'output' => $dataList,
            'data_collect' => $collectData,
            'selected' => ''
        ];
    }

    private function serializeMenus($kelmenu_id)
    {
        $query = isset($this->_groupMenu[$kelmenu_id]) ? $this->_groupMenu[$kelmenu_id] : [];
        $this->_tmpMenu = [];
        $this->_renderMenu = [];
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
    * @param string $nameAction
    * @return string
    */

    protected function checkRegex($nameAction)
    {
        $modulName = Yii::$app->params['service'];
        $result = 'transaksi';
        if (strtolower($modulName) === "master") {
            $result = strtolower($modulName);
        }

        if (strtolower($modulName) === "dcms") {
            $result = strtolower('Aplikasi');
        }

        if (strtolower($modulName) === "informasi") {
            $result = strtolower($modulName);
        }

        if (preg_match('/lap/i', $nameAction)) {
            $result = 'laporan';
        }

        if (preg_match('/inf/i', $nameAction)) {
            $result = 'informasi';
        }

        return $result;
    }

    /**
    *   [Untuk membuat kelompok menu]
    *   @return array
    **/

    protected function createKelMenu()
    {
        $rows = $cond = [];
        foreach ($this->_kelMenu as $value) {
            $alias = strtolower(str_replace(" ", "-", $value));
            $cond[] = $alias;
            $rows[$alias] = [
                'kelmenu_nama' => $value,
                'kelmenu_key' => $alias
            ];
        }

        $this->_globCond = $cond;
        $kelMenu = KelompokMenu::find()->where(['kelmenu_key' => $cond])->all();
        $data_kelompok = [];
        foreach ($kelMenu as $val) {
            $kelmenu_key = $val->kelmenu_key;
            $data_kelompok[$kelmenu_key] = $val->kelmenu_id;
            if (isset($rows[$kelmenu_key]['kelmenu_key'])) {
                if ($rows[$kelmenu_key]['kelmenu_key'] == $kelmenu_key) {
                    unset($rows[$kelmenu_key]);
                }
            }
        }

        $result = false;
        if (count($rows)) {
            $attributes = [
                'kelmenu_nama',
                'kelmenu_key'
            ];

            $result = Yii::$app->db->createCommand()->batchInsert('kelompokmenu_k',$attributes, $rows)->execute();
        }

        if ((count($cond) != count($kelMenu)) || $result) {
            $kelMenu = KelompokMenu::find()->all();
            foreach ($kelMenu as $val) {
                $kelmenu_key = $val->kelmenu_key;
                $data_kelompok[$kelmenu_key] = $val->kelmenu_id;
            }
        }

        return $data_kelompok;
    }

    /**
    ** untuk extract attributes dokument tercetak
    **/

    public function actionGetAttributesDok()
    {
        $modulName = Yii::$app->params['service'];
        $keyModule = strtolower($modulName);
        $controllerlist = [];
        if ($handle = opendir('../modules/v1/controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }

        asort($controllerlist);
        // Extract All Action Method
        $listKeterangan = [
            'output' => [],
            'data_collect' => [],
            'selected' => ""
        ];
        foreach ($controllerlist as $controller) {
            if (substr($controller, 0, -4) != "AuthController" && substr($controller, 0, -4) != "AllowController") {
                $handle = fopen('../modules/v1/controllers/' . $controller, "r");
                if ($handle) {
                    $newController = str_replace(".php", "", $controller);
                    $keyIdentity = strtolower($modulName) . '-' . $newController;

                    $readContoller = file_get_contents('../modules/v1/controllers/' . $controller);
                    if (preg_match_all('/\/\*[\s\S]*?\*\//', $readContoller, $display)) {
                        $controlName = '';
                        foreach ($display as $value) {
                            foreach ($value as $attr) {
                                if (preg_match('/@controller(.*)/', $attr, $controller)) {
                                    if (isset($controller[1])) {
                                        $listAttribute = [];
                                        $controlName =  $controller[1];
                                        if (preg_match_all('/@attribute(.*)/', $attr, $attribute)) {
                                           if (isset($attribute[1])) {
                                              $listAttribute = $attribute[1];
                                           }
                                        }
                                    }
                                    if ($listAttribute) {
                                        $listKeterangan['data_collect'][$keyIdentity][trim($controlName)] = $listAttribute;
                                    }
                                }
                            }
                            
                            if ($controlName) {
                                $listKeterangan['output'][] = [
                                    'id' => $keyIdentity, 
                                    'name' => strtoupper($modulName) .' - ' .$newController
                                ];
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }
        return $listKeterangan;
    }

    protected function getPages() 
    {
        $controllerlist = [];
        if ($handle = opendir('../modules/v1/controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }
        asort($controllerlist);
        $fulllist = [];
        // If Child of ActiveController
        foreach ($controllerlist as $controller) {
            if (substr($controller, 0, -4) != "AuthController" && substr($controller, 0, -4) != "AllowController") {
                $fulllist[substr($controller, 0, -4)] = [];
                $content = file_get_contents('../modules/v1/controllers/' . $controller, "r");
                if (preg_match("/( )*public( )+.modelClass( )*\=/", $content)) {
                    if ( ! in_array("index", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "index";
                    if ( ! in_array("view", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "view";
                    if ( ! in_array("create", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "create";
                    if ( ! in_array("update", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "update";
                    if ( ! in_array("delete", $fulllist[substr($controller, 0, -4)]))
                        $fulllist[substr($controller, 0, -4)][] = "delete";
                }
            }
        }
        // Extract All Action Method
        foreach ($controllerlist as $controller) {
            if (substr($controller, 0, -4) != "AuthController" && substr($controller, 0, -4) != "AllowController") {
                $handle = fopen('../modules/v1/controllers/' . $controller, "r");
                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        if (preg_match('/public.*function.*action(.*?)\(/', $line, $display)) {
                            if (strlen($display[1]) > 2) {
                                $string = preg_replace("/(?<=\\w)(?=[A-Z])/","-$1", $display[1]); 
                                $string = strtolower($string);
                                if (!in_array($string, $fulllist[substr($controller, 0, -4)])) {
                                    $fulllist[substr($controller, 0, -4)][] = strtolower($string);
                                }
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }

        // Extract action from action folder per controller
        foreach ($controllerlist as $controller) {
            $adir = substr($controller, 0, -14);
            if(is_dir("../modules/v1/actions/".$adir) && substr($controller, 0, -4) != "AuthController" && substr($controller, 0, -4) != "AllowController") {
                if ($ahandle = opendir('../modules/v1/actions/'.$adir)) {
                    while (false !== ($afile = readdir($ahandle))) {
                        if ($afile != "." && $afile != "..") {
                            $string = preg_replace("/(?<=\\w)(?=[A-Z])/","-$1", $afile);
                            $string = substr($string, 0, -11);
                            $string = strtolower($string);
                            if (!in_array($string, $fulllist[substr($controller, 0, -4)])) {
                                $fulllist[substr($controller, 0, -4)][] = $string;
                            }
                        }
                    }
                    closedir($ahandle);
                }
            }
        }
        return $fulllist;
    }
}
