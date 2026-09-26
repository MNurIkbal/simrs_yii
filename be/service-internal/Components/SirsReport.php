<?php

namespace Integrasi\Components;

use GuzzleHttp\Client;
use Yii;
use yii\base\Component;

class SirsReport extends Component {

    public function renderDesigner($kode_report, $no_transaksi, $type_po, $path){

        $ini = @parse_ini_file(__DIR__ . './../../dcms/config/env/.env', true);

        $key = $ini['report'];
        
        $query_parameter = [
            'api-key' => $key['api_key'],
            'ds_access' => $key['schema'],
            'ds_kode' => $kode_report,
            'nomor_po' => $no_transaksi,
            'type_po' => $type_po
        ];

        $client = new Client([
            'base_uri' => $key['api_url']
        ]);
        $response = $client->get('api/preview-pdf',[
            'query' => $query_parameter,
            'save_to'=> $path
        ]);
        
        return $response;
    }

}