<?php

namespace Doco\Traits;

use Yii;

use Doco\Repositories\RegionRepositories;

trait RegionTrait
{
    /**
     * Retrieve data region by type
     *
     * @param String $type type of request region: province,city,district,village
     * @param Integer $foreignId foreign id (if province not needed)
     * @return JSON
     * @author Tsani Nashrullah
     **/
    public function actionRegionByType()
    {
        $result = [];
        $payload = Yii::$app->request->get();
        return (new RegionRepositories)->regionByType($payload['type'], $payload['foreignId']);
    }
}
