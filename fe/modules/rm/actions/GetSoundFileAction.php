<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\rm\actions;

use Yii;
use yii\helpers\Url;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class GetSoundFileAction extends Action {
    public function run() {
        return Url::base(true) . DocoConstants::NOTIFICATION_RM;
    }
}