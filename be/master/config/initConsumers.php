<?php
class initConsumers {
    
    protected $prefix;
    
    public function __construct()
    {
        $params = require __DIR__ . '/params.php';
        $this->prefix = isset($params['prefix']) ? $params['prefix'] : '';
    }

    /**
     * function untuk set ulang array consumers
     */
    public function transform($consumers) {
        foreach ($consumers as $key => $value) {
            // set exchange
            // unset($consumers[$key]);
            // set name exchange
            // if (isset($value['exchange_options']['name'])) {
            //     $value['exchange_options']['name'] = $value['exchange_options']['name'].$this->prefix;
            // }

            if (isset($value['queues'])) {
                foreach($value['queues'] as $keyQ => $queues) {
                    // set key queues
                    unset($value['queues'][$keyQ]);
                    $value['queues'][$keyQ.$this->prefix] = $queues;
                    // set name queues
                    if (isset($queues['name'])) {
                        $value['queues'][$keyQ.$this->prefix]['name'] = $queues['name'].$this->prefix;
                    }
                }
            }
            $consumers[$key] = $value;
        }
        
        return $consumers;
    }
}