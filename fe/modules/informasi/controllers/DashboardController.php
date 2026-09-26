<?php

/**
 * setup config application
 *
 * @author ali.padilah@docotel.com
 * @return session json
 */

namespace Doco\informasi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;


class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/informasi/dashboard';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $session = Yii::$app->session;

        $jsonPath = 'http://'.$_SERVER['HTTP_HOST'] . '/json/mainmenu.json'; // diganti saja dengan url json dari menu
        $str = file_get_contents($jsonPath);
        $json = json_decode($str, true);

        // $menuOptions = [];

        // foreach($json['menu'] as $row) {
        //     if($row['moduleID'] == $session->get('moduleID')) {
        //         $menu_name = explode(" ", $row['name']);
        //         $firstWord = array_shift($menu_name);
        //         $nextMenu = implode(" ", $menu_name);

        //         $menuOptions[] = [
        //                 'name' => '<br><b>'.Yii::t('fe', $firstWord).'</b><br>'.Yii::t('fe', $nextMenu),
        //                 'icon' => '<img src="http://'.$_SERVER['SERVER_NAME'] . '/' . $row['icon'] . '" alt="icon-'.$nextMenu.'" >',
        //                 'url' => $row['url'],
        //             ];
        //     }
        // }

        // #Display Options
        // $displayMenu = [];
        // $response = $this->_restMaster->request('POST', 'layarantrian/',[
        //                     'form_params' => [
        //                         'is_active' => true
        //                     ]
        //                 ]);

        // $body = json_decode($response->getBody(),TRUE);
        // $DisplayOptions = $body['response']['data'];

        // foreach($DisplayOptions as $row)
        // {
        //     $primaryKey = DocoHelpers::encrypt($row['layarantrian_id']);

        //     $menu_name = explode(" ", $row['layarantrian_judul']);
        //     $firstWord = array_shift($menu_name);
        //     $nextMenu = implode(" ", $menu_name);

        //     $displayMenu[] = [
        //             'name' => '<br><b>'.Yii::t('fe', $firstWord).'</b><br>'.Yii::t('fe', $nextMenu),
        //             'icon' => '<img src="http://'.$_SERVER['SERVER_NAME'] . '/media/img/icon-antrian/display-icon.png" alt="icon-'.$nextMenu.'" >',
        //             'url' => Url::to([$this->_module .'/layar-antrian','layar' => $primaryKey ]),
        //         ];
        // }

        return $this->render('index', get_defined_vars());
    }
    public function actionLayarAntrian($layar) {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($layar);

        $response = $this->_restMaster->get('layarantrian/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $infoLayarAntrian = $body['response'];

        $title = $infoLayarAntrian['layarantrian_judul'];

        return $this->render('layarantrian', get_defined_vars());
    }
}