<?php

namespace app\components;


class DocoAccessRule extends \yii\filters\AccessControl
{
    public function beforeAction($action)
    {
        $user = $this->user;
        $this->denyAccess($user);
    }
}