<?php

namespace Integrasi\Components\Services;

use Integrasi\Service\Sirs\Models\Lookup;
use phpDocumentor\Reflection\Types\Array_;
use Yii;
use yii\helpers\ArrayHelper;

class RemunerasiService extends IntegrationBaseService
{

    /**
     * mencari config pada params yii
     * @var string
     */
    public $keyConfig = 'djamil';

    /**
     * kebutuhan untuk auth casemix
     * @var string
     */
    public $xOwner;

    /**
     * untuk key cache token
     * @var string
     */
    protected $tokenIdentity = 'djamil-token1';

    const EXPIRED_TOKEN = 3600;

    public function login()
    {
        $request = $this->post('sirs/auth',[
                        'form_params' => [
                            'username' => $this->getAttribute("username_djamil"),
                            'password' => $this->getAttribute("password_djamil")
                        ]
                    ]);
        $response = isset($request['response']) ? $request['response'] : null;
        $getToken = isset($request['response']['token']) ? $request['response']['token'] : null;
        return $getToken;
    }

    public function checkToken()
    {
        $token = Yii::$app->cache->get($this->tokenIdentity);
        if (empty($token)) {
            $token = $this->login();
            Yii::$app->cache->set($this->tokenIdentity, $token, self::EXPIRED_TOKEN);
        }
        return $token;
    }

    public function setHeaders()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->checkToken(),
                'X-Owner' => $this->xOwner
            ]
        ];
    }

    public function getDataAbsensi()
    {
        $jayParsedAry = [
            [
                "pegawai_id" => '11',
                "nik" => "11111",
                "detail_absen" => [
                    [
                        "terlambat_masuk" => [
                            [
                                "1_sd_30"  => 20, 
                                "31_sd_60" => 1, 
                                "61_sd_90" => 1, 
                                "lebih_dari_90" => 1 
                            ] 
                        ], 
                        "pulang_cepat" => [
                            [
                                "1_sd_30"  => 1, 
                                "31_sd_60" => 1, 
                                "61_sd_90" => 1, 
                                "lebih_dari_90" => 1 
                            ] 
                        ], 
                        "tidak_finger" => 3, 
                        "pengecualian_absen" => 0 
                    ] 
                ] 
            ]
        ];
        $value = [
            'response' => [
                'list' =>  $jayParsedAry
            ]
        ];
        return $value;
    }

    /**
     * @return Array
     */
    public function getDataTindakan($url, $options)
    {
        $payload = [
            'form_params' => [
                "token" => $this->checkToken(),
                "username" => $this->getAttribute("username_djamil"),
                "month" => ArrayHelper::getValue($options, 'month'),
                "year" => ArrayHelper::getValue($options, 'year')
            ]
        ];
        $request = $this->post($url,array_merge($payload, $this->setHeaders()));
        return $request;
    }

    public function getDataOther()
    {
        $result = [
            [
                "nik" => "11111",
                "pegawai_id" => '11',
                "ronde_besar" => 3,
                "koord_lap_jaga_bangsal" => 3,
                "rapat_koord_pelayanan" => 3,
                "audit_medik" => 1,
                "penelitian" => 2,
                "presentasi" => 1,
                "rapat_direksi" => 3,
                "kwalitas" => 2,
                "kepatuhan" => 3,
                "tidak_apel" => 1
            ]
        ];

        $value = [
            'response' => [
                'list' =>  $result
            ]
        ];
        return $value;
    }
}