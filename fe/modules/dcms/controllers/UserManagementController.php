<?php 

/**
 * @author Randy Vianda Putra
 * @todo User Management
 * @copyright 24 January 2018 aweutist
 */

namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\dcms\models\UserManagementForm;
use yii\helpers\ArrayHelper;

class UserManagementController extends DocoController
{
    
    protected $_title = "User Management";
    protected $_module = '/dcms/user-management';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        $model = new UserManagementForm;
        $request = Yii::$app->request;
        $title = $this->_title;
        $response = $this->_restDcms->get('user-management/ajax', 
            [
                'form_params' => []
            ]
        );
        $response = json_decode($response->getBody(),true);
        $data_user = isset($response['response']['data-user']) 
                        ? $response['response']['data-user']
                        : [];
        $options_data_user = ArrayHelper::map($data_user, 'loginpemakai_id', 'nama_pemakai');
        // dump($response);exit;

        return $this->render('index', get_defined_vars());

    }

    

}