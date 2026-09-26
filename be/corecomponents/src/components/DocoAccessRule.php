<?php

namespace Doco\components;

use Yii;
use Doco\components\DocoHelpers;

class DocoAccessRule extends \yii\filters\AccessControl
{
    public function beforeAction($action)
    {
        $enableAutoLogin = isset(Yii::$app->params['enableAutoLogin']) ? Yii::$app->params['enableAutoLogin'] : null;
        if (!empty($enableAutoLogin)) {
            return true;
        }
        $user = $this->user;
        $modul_id = Yii::$app->jwt->modul_id;
        $owner = Yii::$app->jwt->owner;

        if (Yii::$app->jwt->is_mobile) {
            return true;
        }
        
        $akses_pengguna = Yii::$app->jwt->user->akses_pengguna;
        $modulName = Yii::$app->params['service'];
        $key_module = strtolower($modulName);

        if ($owner == 'bdg-simrs-doco-development') {
            return true;
        }

        if (!empty($modul_id)) {
            if (isset($akses_pengguna[$modul_id])) {
                $test = substr(strrchr(get_class($action->controller), "\\"), 1);
                $name_controller = preg_replace("/(?<=\w)(?=[A-Z])/"," $1", $test);
                $current_key = $key_module .'-'. $name_controller . '-' . $action->id;
                $encrypt = DocoHelpers::encrypt($current_key);
                if (in_array($encrypt, $akses_pengguna[$modul_id])) {
                    return true;
                } else {
                    throw new \yii\web\UnauthorizedHttpException();
                }
            } else {
                throw new \yii\web\UnauthorizedHttpException();
            }
        } else {
            $test = substr(strrchr(get_class($action->controller), "\\"), 1);
            $name_controller = preg_replace("/(?<=\w)(?=[A-Z])/"," $1", $test);
            $current_key = $key_module .'-'. $name_controller . '-' . $action->id;
            $encrypt = DocoHelpers::encrypt($current_key);

            foreach ($akses_pengguna as $akses) {
                if (in_array($encrypt, $akses)) {
                    return true;
                }
            }

            throw new \yii\web\UnauthorizedHttpException();
        }

    }
}