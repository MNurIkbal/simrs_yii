<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use app\components\DocoHelpers;
use app\modules\v1\models\InfoReturResepView;
use SirsCore\businessLogic\ReturResep;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoRestActiveFilter;

class ReturResepAction extends Action
{
    public function run()
    {
        return ReturResep::createReturReseptur();
    }
}
