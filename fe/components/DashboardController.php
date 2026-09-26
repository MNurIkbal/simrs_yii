<?php

namespace app\components;

use Yii;
use app\components\DocoController;


class DashboardController extends DocoController
{

    protected $supersetKey = '';

    protected $supersetFrameWidth = '100%';

    public function actionIndex()
    {
        $session = Yii::$app->session;
        if ($workspace = $session->get('active_workspace')) {
            if (isset($workspace['modul_page']) && !in_array($workspace['modul_page'], ['default', '__dashboard__'])) {
                $tmp = trim($workspace['modul_page'], '/');
                if (in_array($tmp, [$this->module->id, $this->module->id . '/default', $this->module->id . '/default/index'])) {
                    $workspace['modul_page'] = false;
                }
                return Yii::$app->runAction($workspace['modul_page'] ?: $this->module->id . '/dashboard');
            }
        }
        return $this->renderContent(
            Yii::$app->superset->dashboard(
                $this->supersetKey ?: $this->module->id,
                $this->supersetFrameWidth
            )
        );
    }
}
