<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class MenuController extends Controller
{
    protected static $_listMappmenu = [];
    /**
     * Generate Menu.
     *
     * @return Response|string
     */
    public static function generateMenu($id = null)
    // public static function actionGenerate($id = 21)
    {
        $data_menu = Yii::$app->session->get('menu_module');
        $menu = "";

        if (!empty($data_menu[$id]['menu'])) {
            foreach ($data_menu[$id]['menu'] as $key => $value) {
                $icon = !empty($value['kelmenu_icon']) ? $value['kelmenu_icon'] : 'fa fa-book';
                $menu .= '<li class="dropdown">';
                $menu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
                $menu .= '<i class="'. $icon .' position-left" aria-hidden="true"></i>';
                $menu .= $value['kelmenu_nama'];
                $menu .= '<span class="caret"></span>';
                $menu .= '</a>';
                $menu .= '<ul class="dropdown-menu width-200 wrapper" id="scrollmenu">';
                $menu .= self::getParentMenu($value);
                $menu .= '</ul>';
                $menu .= '</li>';
            }
        }

        Yii::$app->session->set('url-to-menu',self::$_listMappmenu);
        Yii::$app->session->set('menu',$menu);
        $akses_menu = isset($data_menu[$id]['akses']) ? $data_menu[$id]['akses'] : []; 
        Yii::$app->session->set('akses_menu',$akses_menu);
    }

    /**
     * Generates menu string from menu data returned from rest service.
     * Different from similar function above, this function does not set the session for menu, only returns generated menu string.
     *      
     * @author rbs1518 (rinardi@docotel.com)
     * @param array|null $menuData menu data from rest service.
     * @return string
     */
    public static function generateMenuWithMenuData($menuData)
    {
        $data_menu = Yii::$app->session->get('menu_module');
        $menu = "";

        if ($menuData != null)
        {
            foreach ($menuData as $key => $value)
            {
                $icon = !empty($value['kelmenu_icon']) ? $value['kelmenu_icon'] : 'fa fa-book';
                $menu .= '<li class="dropdown">';
                $menu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
                $menu .= '<i class="'. $icon .' position-left" aria-hidden="true"></i>';
                $menu .= $value['kelmenu_nama'];
                $menu .= '<span class="caret"></span>';
                $menu .= '</a>';
                $menu .= '<div class="dropdown-menu dropdown-content wrapper-header" ><ul class="dropdown-content-body" id="" style="width:auto !important; min-width:0 !important;">';
                $menu .= self::getParentMenu($value);
                $menu .= '</ul></div>';
                $menu .= '</li>';
            }
            Yii::$app->session->set('url-to-menu',self::$_listMappmenu);
        }

        return $menu;        
    }

    private static function getParentMenu($value)
    {
        $parent_menu = "";
        if (!empty($value['item']['data'])) {
            foreach ($value['item']['data'] as $k => $v) {
                if (!empty($v['item'])) {
                    $parent_menu .= '<li class="dropdown-submenu parent">';
                    $parent_menu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="'.$v["menu_icon"].'"></i>'.$v["menu_namalainnya"].'</a>';
                    $parent_menu .= '<div class="dropdown-menu dropdown-content wrapper-header" ><ul class="dropdown-content-body">';
                    $parent_menu .= self::getSubMenu($v['item']);
                    $parent_menu .= '</ul></div>';
                    $parent_menu .= '</li>';
                } else {
                    $parent_menu .= self::getSubMenu($v);
                }
            }
        }

        return $parent_menu;
    }

    private static function getSubMenu($value)
    {
        $sub_menu = "";
        if (isset($value[0])) {
            foreach ($value as $key => $value) {
                if (!empty($value['item'])) {
                    $sub_menu .= '<li class="dropdown-submenu parent">';
                    $sub_menu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="'.$value["menu_icon"].'"></i>'.$value["menu_namalainnya"].'</a>';
                    $sub_menu .='<ul class="dropdown-menu wrapper">';
                    $sub_menu .= self::getSubMenu($value['item']);
                    $sub_menu .= '</ul>';
                    $sub_menu .= '</li>';
                } else {
                    $url = !empty($value["menu_url"]) ? $value["menu_url"] : '#';
                    self::$_listMappmenu[$url] = $value["menu_namalainnya"];
                    if( !filter_var($url, FILTER_VALIDATE_URL) ){
                        $url = (Url::home().preg_replace('/^\//i', '', $url));
                    }
                    $sub_menu .= '<li><a href="'.$url.'"><i class="'.$value["menu_icon"].'"></i>'.$value["menu_namalainnya"].'</a></li>';
                }
            }
        } else {
            $url = !empty($value["menu_url"]) ? $value["menu_url"] : '#';
            self::$_listMappmenu[$url] = $value["menu_namalainnya"];
            if( !filter_var($url, FILTER_VALIDATE_URL) ){
                $url = (Url::home().preg_replace('/^\//i', '', $url));
            }
            $sub_menu .= '<li><a href="'.$url.'"><i class="'.$value["menu_icon"].'"></i>'.$value["menu_namalainnya"].'</a></li>';
        }
        return $sub_menu;
    }
}
