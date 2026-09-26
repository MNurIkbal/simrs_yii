<?php


namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanController extends DocoController
{
    protected $_title = 'Laporan';
    protected $_module = 'dcms/laporan/';
    protected $_restDCMS;

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $userIdentity = Yii::$app->session->get('user_identity');

        $roles = empty($userIdentity["roles"]) ? [] : $userIdentity["roles"];
        $modul_id = empty($active_workspace["modul_id"]) ? null : $active_workspace["modul_id"];


        $title = $this->_title;
        try {
            $this->_restDCMS = Yii::$app->docoRest->dcms;
            $response = $this->guzzleExec($this->_restDCMS, [
                'url' => "report/all-viewer",
                'payload' => [
                    'query' => [
                        'module_id' => $modul_id,
                        'roles' => $roles
                    ],
                ]
            ]);
            $dokumen = $response['data'];
        } catch (RequestException $e) {
            $dokumen = [];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetContentLaporan()
    {
        $request = Yii::$app->request;
        $dokumen = $request->get('dokumen');
        $this->layout = false;
        return $this->renderContent(Yii::$app->report->embedViewer($dokumen));
    }

    public function actionFull($dokumen)
    {
        $this->layout = false;
        return $this->renderContent(Yii::$app->report->embedViewer($dokumen));
    }
}