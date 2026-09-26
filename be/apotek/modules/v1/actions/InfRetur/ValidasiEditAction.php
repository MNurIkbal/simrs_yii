<?php

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\InfoReturResepView;
use Doco\models\LoginForm;

class ValidasiEditAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        try {
            $pass = $request->get('pass', null);
            $name = $request->get('username', null);
            $modelLogin = new LoginForm();
            $modelLogin->username = $name;
            $modelLogin->password = $pass;
            
            if (!$modelLogin->validate()) {
              return $this->controller->responseJson(400, 'Password Salah', [], ['title' => 'Proses Gagal !']);
            }
            
            return $this->controller->responseJson(200, 'Berhasil');
        } catch(\yii\base\Exception $e) {
            $this->controller->logError($e);
            return $this->controller->responseJson(400, $e->getMessage());
        } catch (\Exception $e) {
            $this->controller->logError($e);
            return $this->controller->responseJson(400, $e->getMessage());
        }
    }
}