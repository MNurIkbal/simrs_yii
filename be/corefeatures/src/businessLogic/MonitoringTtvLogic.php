<?php

namespace SirsCore\businessLogic;

use Yii;
use Doco\components\DocoConstants;
use Doco\Repositories\MonitoringTtvRepositories;

class MonitoringTtvLogic
{
    // This feed data are used for any entry data
    // sourced outside monitoring TTV Trait
    public function feedData($data, $type, $source)
    {
        $extractedData = MonitoringTtvRepositories::mapDataTtv($data, $type, $source);
        $checkIfExist = MonitoringTtvRepositories::checkExistData($extractedData);

        if($checkIfExist) {
            return [
                'status' => 'error',
                'message' => 'Failed to insert TTV data',
            ];
        } else {
            $response = MonitoringTtvRepositories::insertData($extractedData);
        }

        return $response;
    }
}