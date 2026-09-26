<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Loket
 * @copyright 12 Desember 2018 aweutist
 */


namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

class InformasiDashboardKamarController extends DocoController
{
    protected $_restPendaftaran;
    protected $_module = 'pendaftaran/informasi-dashbaord-kamar/';
    protected $allowAction = ['*'];
    
    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;

    }

    public function behaviors()
    {
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    public function actionIndex()
    {
        try {
            $title = Yii::t('fe', 'Dashboard Kamar');
            $response = $this->_restPendaftaran->get('info-dashboard-kamar/list-kamar',['query'=>[]]);
            $body = json_decode($response->getBody(), true);
            $kamar = $body['response'];

            $konfig = Yii::$app->session->get('konfig_system');
            if (!$konfig) {
                $response = $this->_restPendaftaran->get('info-dashboard-kamar/get-konfig-system',['query'=>[]]);
                $body = json_decode($response->getBody(), true);
                $konfig = $body['response'];                
                Yii::$app->session->set('konfig_system', $konfig);
            }
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
