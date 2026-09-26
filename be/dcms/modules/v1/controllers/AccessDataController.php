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

/**
 * 
 */
class AccessDataController extends DocoActiveController
{
    public $modelClass = PeranPengguna::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
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

    protected function createEmptyActiveWorkspace($moduleSlug='')
    {
        return [
            'instalasi_id' => 0,
            'instalasi_name' => '',
            'modul_slug' => $moduleSlug,
            'modul_id' => 0,
            'modul_name' => '',
            'modul_page' => '',
            'ruangan_id' => 0,
            'ruangan_name' => '',
            'url' => '',
            'modul_alias' => '',
            'modul_icon' => '',
            'instalasi_index' => '',
            'ruangan_index' => '',
            'ruangan_lain' => null,
            'loket' => null
        ];
    }

    protected function fillActiveWorkspaceAndMenu($accessData,&$active_workspace,$instId,$instIdx,$roomId,$roomIdx,$moduleId,$moduleSlug)
    {
        $workspace = $accessData['workspace'];
        $dMenu = $accessData['menus'];

        //Find module id if only slug is known.
        if (($moduleSlug != '') && ($moduleId == 0))
        {
            foreach ($workspace as $id => $item)
            {
                if ($item['slug'] == $moduleSlug)
                {
                    $moduleId = $id;
                    break;
                }
            }
        }
        $instIdx = array_search($instId, array_column($workspace[$moduleId]['installation'], 'id'));
        $roomIdx = array_search($roomId, array_column($workspace[$moduleId]['installation'][$instIdx]['rooms'], 'id'));

        $active_workspace['modul_id'] = $moduleId;
        $active_workspace['modul_slug'] = $moduleSlug;
        $active_workspace['url'] = preg_replace('/^\//i', '', $workspace[$moduleId]['url']);
        $active_workspace['modul_page'] = $workspace[$moduleId]['page'];
        $active_workspace['modul_name'] = $workspace[$moduleId]['name'];
        $active_workspace['modul_alias'] = str_replace("Modul ", "", $workspace[$moduleId]['name']);
        $active_workspace['modul_icon'] = $workspace[$moduleId]['icon'];
        $active_workspace['instalasi_id'] = $instId;
        $active_workspace['instalasi_index'] = (string)$instIdx;
        $active_workspace['instalasi_name'] = $workspace[$moduleId]['installation'][$instIdx]['name'];
        $active_workspace['ruangan_id'] = $roomId;
        $active_workspace['ruangan_index'] = (string) $roomIdx;
        $active_workspace['ruangan_name'] = $workspace[$moduleId]['installation'][$instIdx]['rooms'][$roomIdx]['name'];
        $active_workspace['ruangan_lain'] = $workspace[$moduleId]['installation'][$instIdx]['rooms'];
        $active_workspace['loket'] = null;

        if (!empty($dMenu[$moduleId]['menu']))
            $menus = $dMenu[$moduleId]['menu'];
        $menuAccess = (isset($dMenu[$moduleId]['akses']) ? $dMenu[$moduleId]['akses'] : []);

        $workspaceKey = ($moduleSlug.'_'.$active_workspace['instalasi_id'].'_'.$active_workspace['ruangan_id']);
        return [
            'key' => $workspaceKey,
            'module_id' => $moduleId,
            'module_slug' => $moduleSlug,
            'active_workspace' => $active_workspace,
            'menu' => $menus,
            'menu_access' => $menuAccess
        ];
    }

    /**
     * Fungsi untuk ambil data akses dari cache (pengecekan saja)
     * @author rbs1518 (rinardi@docotel.com)
     */
    public function actionIndex()
    {
        $data = $this->getAccessDataFromCache();
        return $data;
    }

    public function actionGetSavedWorkspace()
    {
        if ($this->uniqueToken == '')
            throw new Exception('No unique token.');

        $cache = Yii::$app->accessCache;
        $awData = $cache->get('saved_ws_'.$this->uniqueToken);
        if ($awData === false)
            $awData = [];

        return $awData;
    }

    public function actionClearSavedWorkspace()
    {
        if ($this->uniqueToken == '')
            throw new Exception('No unique token.');

        $cache = Yii::$app->accessCache;
        $cache->set('saved_ws_'.$this->uniqueToken,[],1);

        return true;
    }

    public function actionSetActiveWorkspace()
    {
        $post = Yii::$app->request->post();
        $cache = Yii::$app->accessCache;

        if ($this->uniqueToken == '')
            throw new Exception('No unique token.');

        $isUnset = (isset($post['is_unset']) ? $post['is_unset'] == '1' : false);
        $unset_all = (isset($post['unset_all']) ? $post['unset_all'] == '1' : false);
        $moduleId = (isset($post['module_id']) ? (int)$post['module_id'] : 0);
        $instalasiId = (isset($post['instalasi_id']) ? (int)$post['instalasi_id'] : 0);
        $instalasiIndex = (isset($post['instalasi_index']) ? (int)$post['instalasi_index'] : 0);
        $roomId = (isset($post['room_id']) ? (int)$post['room_id'] : 0);
        $roomIndex = (isset($post['room_index']) ? (int)$post['room_index'] : 0);

        $aData = $this->getAccessDataFromCache();
        if ($aData === null)
            throw new Exception('Access data is null.');
        $workspace = $aData['workspace'];

        $moduleSlug = 'unknown';
        if (isset($workspace[$moduleId]))
            $moduleSlug = $workspace[$moduleId]['slug'];

        $menus = [];
        $menuAccess = [];
        $active_workspace = $this->createEmptyActiveWorkspace($moduleSlug);

        if ($isUnset)
        {
            if (!$unset_all)
            {
                $active_workspace['ruangan_index'] = $roomIdx;
                $active_workspace['ruangan_id'] = $roomId;
                $active_workspace['ruangan_name'] = $workspace[$moduleId]['installation'][$instalasiIndex]['rooms'][$roomIdx]['name'];
                //$active_workspace['ruangan_name'] = $workspace[$active_workspace['modul_id']]['installation'][$instalasiIdx]['rooms'][$roomIdx]['name'];
            }
            else
            {
                //unset all
            }
        }
        else
        {
            $awItem = $this->fillActiveWorkspaceAndMenu($aData,$active_workspace,$instalasiId,$instalasiIndex,$roomId,$roomIndex,$moduleId,$moduleSlug);
            $menus = $awItem['menu'];
            $menuAccess = $awItem['menu_access'];

            $workspaceKey = $awItem['key'];
            $awData = $cache->get('saved_ws_'.$this->uniqueToken);
            if ($awData === false)
                $awData = [];
            $awData[$workspaceKey] = $awItem;

            $atDuration = (3600*24*30);
            $cache->set('saved_ws_'.$this->uniqueToken,$awData,$atDuration);            
        }

        $dataForResponse = [
            'is_unset' => $isUnset,
            'unset_all' => $unset_all,
            'module_id' => $moduleId,
            'module_slug' => $moduleSlug,
            'active_workspace' => $active_workspace,
            'menus' => $menus,
            'menu_access' => $menuAccess
        ];

        if ($isUnset)
        {
            $cache->delete('current_ws_'.$this->uniqueToken);
        }
        else
        {
            $cache->set('current_ws_'.$this->uniqueToken,[
                'module_id' => $moduleId,
                'module_slug' => $moduleSlug,
                'active_workspace' => $active_workspace,
                'menus' => $menus,
                'menu_access' => $menuAccess
            ],$atDuration);
        }

        return $dataForResponse;        
    }

    public function actionSetActiveWorkspaceForJump()
    {
        $post = Yii::$app->request->post();
        $cache = Yii::$app->accessCache;
        if ($this->uniqueToken == '')
            throw new Exception('No unique token.');

        $isTemporary = ((isset($post['is_temporary']) ? (int)$post['is_temporary'] : 0) == 1);
        $moduleSlug = trim(isset($post['module_slug']) ? $post['module_slug'] : '');
        $instalasiId = (isset($post['installation_id']) ? (int)$post['installation_id'] : 0);
        $instalasiIndex = (isset($post['instalasi_index']) ? (int)$post['instalasi_index'] : 0);
        $roomId = (isset($post['room_id']) ? (int)$post['room_id'] : 0);
        $roomIndex = (isset($post['room_index']) ? (int)$post['room_index'] : 0);
        
        Yii::error([
            'params' => compact('isTemporary', 'moduleSlug', 'instalasiId', 'instalasiIndex', 'roomId', 'roomIndex')
        ]);
        $aData = $this->getAccessDataFromCache();
        if ($aData === null)
            throw new Exception('Access data is null.');
        $workspace = $aData['workspace'];

        $menus = [];
        $menuAccess = [];
        $active_workspace = $this->createEmptyActiveWorkspace($moduleSlug);

        $awItem = $this->fillActiveWorkspaceAndMenu($aData,$active_workspace,$instalasiId,$instalasiIndex,$roomId,$roomIndex,0,$moduleSlug);

        $moduleId = (int)$awItem['module_id'];
        $menus = $awItem['menu'];
        $menuAccess = $awItem['menu_access'];

        $workspaceKey = $awItem['key'];
        $awData = $cache->get('saved_ws_'.$this->uniqueToken);
        if ($awData === false)
            $awData = [];
        $awData[$workspaceKey] = $awItem;

        $atDuration = (3600*24*30);
        $cache->set('saved_ws_'.$this->uniqueToken,$awData,$atDuration);
        
        if (!$isTemporary)
        {
            $cache->set('current_ws_'.$this->uniqueToken,[
                'module_id' => $moduleId,
                'module_slug' => $moduleSlug,
                'active_workspace' => $active_workspace,
                'menus' => $menus,
                'menu_access' => $menuAccess
            ],$atDuration);
        }
        
        return [
            'module_id' => $moduleId,
            'module_slug' => $moduleSlug,
            'active_workspace' => $active_workspace,
            'menus' => $menus,
            'menu_access' => $menuAccess
        ];
    }

    public function actionMatchActiveWorkspace()
    {
        $post = Yii::$app->request->post();
        $cache = Yii::$app->accessCache;

        if ($this->uniqueToken == '')
            throw new Exception('No unique token.');

        $moduleSlug = (isset($post['module_slug']) ? $post['module_slug'] : 'unknown');
        $instalasiId = (isset($post['instalasi_id']) ? (int)$post['instalasi_id'] : 0);        
        $roomId = (isset($post['room_id']) ? (int)$post['room_id'] : 0);
        $workspaceKey = ($moduleSlug.'_'.$instalasiId.'_'.$roomId);

        $aData = $this->getAccessDataFromCache();
        $workspace = $aData['workspace'];

        $awData = $cache->get('saved_ws_'.$this->uniqueToken);
        if ($awData === false)
            $awData = [];

        if (isset($awData[$workspaceKey]))
            return $awData[$workspaceKey];
        else
            return false;
    }

    public function actionGetCurrentWorkspace()
    {        
        $cache = Yii::$app->accessCache;
        if ($this->uniqueToken == '')
            throw new Exception('No unique token.');

        $awData = $cache->get('current_ws_'.$this->uniqueToken);        
        if ($awData === false)
            $awData = null;


        if (!empty($awData['menus'])) {
            foreach ($awData['menus'] as $k => $v) {
                if (!empty($v['item']['data'])) {

                    $keys = array_column($v['item']['data'], 'menu_urutan');
                    array_multisort($keys, SORT_ASC, $v['item']['data']);
                    $awData['menus'][$k]['item']['data'] = $v['item']['data'];
                    
                    foreach ($awData['menus'][$k]['item']['data'] as $key => $value) {
                        if (!empty($value['item'])) {
                            $keys_submodule = array_column($value['item'], 'menu_urutan');
                            array_multisort($keys_submodule, SORT_ASC, $value['item']);
                            $awData['menus'][$k]['item']['data'][$key]['item'] = $value['item'];
                        }
                    }


                }
            }
        }

        return $awData;
    }
}
?>