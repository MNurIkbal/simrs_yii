<?php 

/**
 * @author Randy Vianda Putra
 * @todo User Management
 * @copyright 24 January 2018 aweutist
 */

namespace app\modules\v1\controllers;


use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\models\Loginpemakai;


class UserManagementController extends DocoActiveController
{

    public $modelClass = Loginpemakai::class;


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

    /**
     * @todo get all data user
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    private function getUser()
    {
        $query = Loginpemakai::find()->select([
            'loginpemakai_k.loginpemakai_id',
            'loginpemakai_k.pegawai_id',
            'loginpemakai_k.nama_pemakai',
            'loginpemakai_k.is_active',
        ]);
        // ->joinWith([
        //     'pegawai' => function ($query) {
        //         $query->select([
        //             'pegawai_m.pegawai_id',
        //             'pegawai_m.nama_pegawai'
        //         ]);
        //     }
        // ]);

        return $query;
    }

    /**
     * @todo get all data with ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionAjax()
    {
        $data_user = $this->getUser()->asArray()->all();

        return [
            'data-user' => $data_user
        ];
    }
}
