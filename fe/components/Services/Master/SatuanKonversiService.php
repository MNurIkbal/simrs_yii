<?php

/**
 * @author 
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * SatuanKonversiService digunakan untuk kebutuhan 
 * mengambil satuan konversi obat alkes
 */

namespace app\components\Services\Master;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class SatuanKonversiService
{
    use ControllerHelperTrait;

    protected $service;

    public function __construct()
    {
        $this->service = Yii::$app->docoRest->master;
    }

    public function execute($satuankonversi_id = null)
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
        }

        if($satuankonversi_id) {
            $parent_label = $satuankonversi_id;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        
            $column = ($satuankonversi_id) ? 'satuankonversi_id' : 'obatalkes_id';
            $response = $this->guzzleExec(Yii::$app->docoRest->master, [
                'url' => 'allow/list-satuan-konversi',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'parent_label' => $parent_label,
                        'column' => $column
                    ]
                ],
                'returnResponse' => true
            ]);

            foreach ($response['data'] as $value)
                if($satuankonversi_id) {
                    $result['response'] = [
                        "satuankonversi_id" => $value['satuankonversi_id'],
                        "nilai_konversi" => $value['nilai_konversi'],
                        "kecil" => $value['kecil'],
                        "besar" => $value['besar'],
                        "satuankecil_id" => $value['satuankecil_id'],
                        "satuanbesar_id" => $value['satuanbesar_id'],
                    ];

                    return $result;
                }
                else {
                    $result['output'][] = [
                        'id' => $value['satuankonversi_id'],
                        'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                    ];
                }

            return $result;
    }
}
