<?php

/**
 * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
*/

namespace Doco\Services;

use Yii;
use Doco\Services\BaseService;
use Doco\components\DocoHelpers;

class FarmasiService extends BaseService
{
    public function __construct()
    {
        $this->service = Yii::$app->docoRest->apotek;
    }

    /**
    * Function sentUdd
    * 
    * @return JSON
    * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
    * A product of PT. Citraraya Nusatama
    * Powered by Sirs
    */
    public function sentUdd($payload)
    {
        Yii::error([
            'udd-payload' => $payload
        ]);
        return $this->post('unit-dose-dispensing/order', [
            'form_params' => $payload,
            'success' => function($data) use ($payload) {
                // \Yii::error(
                //     'Message : API UDD BERHASIL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : unit-dose-dispensing/order --||--Method : POST--||--Payload : ' . json_encode($payload),
                //     'server-error'
                // );
            },
            'failed' => function($data) use ($payload) {
                \Yii::error(
                    'Message : API UDD GAGAL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : unit-dose-dispensing/order --||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            }
        ]);
    }

    public function getUdd($payload = [])
    {
        return $this->get('unit-dose-dispensing/detail-pendaftaran',[
            'query' => $payload,
            'success' => function($data) use ($payload) {
                // \Yii::error(
                //     'Message : API UDD BERHASIL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : unit-dose-dispensing/get-list-detail --||--Method : GET--||--Payload : ' . json_encode($payload).'--||--Result : '.json_encode($data),
                //     'server-error'
                // );
            },
            'failed' => function($data) use ($payload) {
                \Yii::error(
                    'Message : API UDD GAGAL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : unit-dose-dispensing/get-list-detail --||--Method : GET--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            }
        ]);
    }

    /**
    * Function saveReseptur
    * 
    * function for handle reseptur proccess
    *
    * @param $payload['reseptur_header']
    * @param $payload['reseptur_detail']
    *
    * @return JSON
    * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
    * A product of PT. Citraraya Nusatama
    * Powered by Sirs
    */
    public function saveResep($payload = [])
    {
        return $this->post('transaksi-resep/simpan-reseptur', [
            'form_params' => $payload,
            'success' => function($data) use ($payload) {
                // \Yii::error(
                //     'Message : API Simpan Resep BERHASIL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : reseptur/penjualan --||--Method : POST--||--Payload : ' . json_encode($payload).'--||--Result : '.json_encode($data),
                //     'server-error'
                // );
            },
            'failed' => function($data, $statusCode) use ($payload) {
                \Yii::error(
                    'Message : API Simpan Resep GAGAL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : reseptur/penjualan --||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
                return [
                    'code' => $statusCode,
                    'data' => $data
                ];
            }
        ]);
    }

    public function potongStok($payload) {
        // Yii::error($payload);
        return $this->post('allow/potong-stok', [
            'form_params' => $payload,
            'success' => function($response, $status) use ($payload) {
                // \Yii::error(
                //     'Message : API POTONG STOK BERHASIL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : allow/potong-stok --||--Method : POST--||--Payload : ' . json_encode($payload),
                //     'server-error'
                // );
            },
            'failed' => function($response, $status) use ($payload) {
                \Yii::error(
                    'Message : API POTONG STOK GAGAL--||--Line : NULL --||--File : FarmasiService.php --||--API URL : allow/potong-stok --||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
                \Yii::$app->response->statusCode = 422;
            }
        ]);
    }
}