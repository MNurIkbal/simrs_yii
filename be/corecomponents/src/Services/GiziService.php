<?php

namespace Doco\Services;

use Yii;
use Doco\Services\BaseService;

class GiziService extends BaseService {

    public function __construct()
    {
        $this->service = Yii::$app->docoRest->gizi;
    }
    /**
     * Function for handle integration of permintaan makan from unit
     * 
     * @param Array payload
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function permintaanMakan($payload)
    {
        return $this->post('api/simpan-permintaan-makan', [
            'form_params' => $payload,
            'success' => function ($data) use ($payload) {
                \Yii::error(
                    'Message : API Permintaan Makan SUKSES--||--Line : NULL --||--File : GiziService.php --||--API URL : api/simpan-permintaan-makan--||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            },
            'failed' => function ($data) use ($payload) {
                \Yii::error(
                    'Message : API Permintaan Makan GAGAL--||--Line : NULL --||--File : GiziService.php --||--API URL : api/simpan-permintaan-makan--||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            }
        ]);
    }
    /**
     * Function for handle get menu diet from gizi
     * 
     * @param String var
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function getMenuDiet($payload)
    {
        return $this->get('api/get-menu-diet', [
            'query' => $payload,
            'failed' => function ($data) use ($payload) {
                \Yii::error(
                    'Message : API Get Menu Diet GAGAL--||--Line : NULL --||--File : GiziService.php --||--API URL : api/get-menu-diet--||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            }
        ]);
    }
}
