<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\fisioterapi;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class PasienNewFilter extends \app\components\DocoBaseProcessExtension
{
    protected $_title  = "Pasien Fisioterapi";
    protected $_module = '/fisioterapi/pasien-fisioterapi/';

    protected function processFlow($controller)
    {
        $title         = $this->_title;
        $dokterRujukan = [];
        $status        = [];

        try {
            $response      = Yii::$app->docoRest->fisioterapi->get('pasien-fisioterapi/get-attributes');
            $response      = json_decode($response->getBody(), true);
            $dokterRujukan = $response['response']['dokterPerujuk'];
            $statusPeriksa = $response['response']['statusPeriksa'];

            if(is_array($dokterRujukan)){
                foreach($dokterRujukan as $dokter){  
                    $list_dokter[] = ['id' => $dokter['pegawai_id'], 'text' => $dokter['nama_pegawai']]; 
                }
            }

            if(is_array($statusPeriksa)){
                $list_status[] = ['id' => 'Semua', 'text' => 'Semua'];

                foreach($statusPeriksa as $status){
                    $list_status[] = ['id' => $status['lookup_id'], 'text' => $status['lookup_name']];
                }
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            
            return $result;
        } 

        return $this->render('kunjungan', get_defined_vars());
    }
}