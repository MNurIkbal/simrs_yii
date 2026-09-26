<?php

namespace Doco\rabbitmq\task;

use GuzzleHttp\Client;
abstract class ReportTask extends BaseTask {
    
    protected $sendToUrl;
    
    protected $base_uri;

    /**
     * @return array|null output nya array atau null, kalau null nanti proses di json response nya hanya sukses biasa.
     */

    // abstract protected function processFlow(array $data);

    public function processFlow($params)
    {   
        $this->prosesGetData();
        $this->prosesExport();
    }

    protected function dbConnection()
    {
        return !empty(\Yii::$app->dbslave->username) ? \Yii::$app->dbslave : \Yii::$app->db;
    }

    abstract protected function prosesGetData();
    abstract protected function prosesExport();
}
