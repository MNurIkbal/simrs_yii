<?php

namespace Doco\rabbitmq\task;

use GuzzleHttp\Client;
abstract class IntegrasiTask extends BaseTask {
    
    protected $sendToUrl;
    
    protected $base_uri;

    /**
     * @return array|null output nya array atau null, kalau null nanti proses di json response nya hanya sukses biasa.
     */

    // abstract protected function processFlow(array $data);

    public function processFlow($params)
    {   
        $this->prosesSync();
    }

    abstract protected function prosesSync();
}
