<?php
// Author : Ramdhan Nurrachman
// Updated By : Dede Herdiana

namespace Doco\bedah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\models\LoginForm;
use GuzzleHttp\Exception\RequestException;

class EndPointController extends DocoController
{
    protected $_title = "End Point";
    protected $_module = 'master/end-point/';
    protected $_restBedah;

    public function beforeAction($action)
    {
        return true;
    }

    public function init()
    {
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionCheckAuthorization()
    {
        $urlRef = Yii::$app->request->referrer;
        $pattern = preg_replace("/(http[s]?:\/\/)?([^\/\s]+)(.*)/",'$3',$urlRef);
        $pattern2 = preg_replace("/(\/(?:.(?!\/))+$)/",'',$pattern);

        $request = Yii::$app->request;
        $akses = $request->post('akses');
        $authOnly = $request->post('isAuthOnly', false);
        $model = new LoginForm;
        $model->username = $request->post('nama_pemakai');
        $model->password = $request->post('katakunci_pemakai');
        if ($response = $model->getAuthorization()) {
            $menus = isset($response['response']['menus']) ? $response['response']['menus'] : [];
            $uid = isset($response['response']['uid']) ? $response['response']['uid'] : null;

            /** blok untuk pengecekan username password saja */
            if($authOnly){
                $userIdentity = Yii::$app->session->get('user_identity');
                $loginPemakaiId = isset($userIdentity['loginpemakai_id']) ? $userIdentity['loginpemakai_id'] : null;

                /**
                 *  Pastikan username dan password yang dimasukkan sama dengan session saat ini.
                 *  Saat ini method ini dipakai untuk button yang sudah disetting hak aksesnya.
                 *  Mencegah yang dimasukkan adalah akun orang lain dan tidak memiliki akses.
                 */
                if($loginPemakaiId == $uid){
                    return DocoHelpers::response([
                        'response' => [
                            'message' => true,
                        ]]);
                }else{
                    return DocoHelpers::response([
                        'response' => [
                            'text' => 'User tidak memiliki hak akses!'
                        ]
                    ], 422);
                }
            }

            foreach ($menus as $key => $value) {
                if (!empty($value['akses'][$pattern2])) {
                    if (in_array($akses, $value['akses'][$pattern2])) {
                        return DocoHelpers::response([
                            'response' => [
                                'message' => true,
                                'verify_uid' => $uid
                            ]]);
                    }
                }else if(!empty($value['akses'][$pattern])) {
                    if (in_array($akses, $value['akses'][$pattern])) {
                        return DocoHelpers::response([
                            'response' => [
                                'message' => true,
                                'verify_uid' => $uid
                            ]]);
                    }
                }
            }
            return DocoHelpers::response([
                'response' => [
                    'text' => 'User tidak memiliki hak akses.'
                ]
            ], 422);
        } else {
            return DocoHelpers::response([
                'response' => [
                    'text' => 'user/password tidak valid.'
                ]
            ], 422);
        }
    }
}
