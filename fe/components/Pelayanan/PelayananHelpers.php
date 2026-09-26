<?php

namespace app\components\Pelayanan;

use Yii;
use app\components\DocoConstants;
use app\components\DocoHelpers;

class PelayananHelpers
{
    public function isNurse(){
        $userIdentity = Yii::$app->session->get('user_identity');
        $kelompokpegawai_id = isset($userIdentity['kelompokpegawai_id']) ? $userIdentity['kelompokpegawai_id'] : 0 ;
        return ($kelompokpegawai_id == DocoConstants::KELOMPOK_KEPERAWATAN) ? true : false;

    }

    public function isDokter(){
        $userIdentity = Yii::$app->session->get('user_identity');
        $kelompokpegawai_id = isset($userIdentity['kelompokpegawai_id']) ? $userIdentity['kelompokpegawai_id'] : 0 ;
        return ($kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS) ? true : false;
    }

    public function isDokterSpesialis(){
        $userIdentity = Yii::$app->session->get('user_identity');
        return self::isDokter() ? (!empty($userIdentity['spesialis_id']) ? true : false) : false;
    }

    public function convertBooleanToNumeric($param){
        if($param === false){
            $param = 0;
        }else{
            $param = 1;
        }

        return $param;
    }

    public function decryptId($id){
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }

        return $id;
    }

    public function encryptId($id)
    {
        if(is_numeric($id)) {
            $id = DocoHelpers::encrypt($id);
        }

        return $id;
    }

    public function listDropdownTanggal(){
        $hari = [];
        $x = 1;
        while($x <= 31) {
            $hari[] =  ['hari'=>$x];
        $x++;
        }

        return $hari;
    }

    // created by: Prof. Ir. H. Yafi
    // output: html paragraph with tooltip
    public function cutTextToTooltips($text, $text_max_length = 10, $custom_class = '')
    {
        return '<p data-toggle="tooltip" class="'. $custom_class .'" data-placement="top" title="'. $text  .'">'.substr_replace($text, '...', $text_max_length).'</p>';
    }


    public function getDataPasien($rest, $key, $pendaftaran_id){
        $payload = [
            'key' => $key,
            'pendaftaran_id' => $pendaftaran_id,
        ];

        $response = $rest->get('allow/get-data-pasien',[
            'query' => $payload
        ]);
        $data = json_decode($response->getBody(), true);

        $message['response'] = [
            'title' => 'Proses Gagal !',
            'text' => 'Pasien Sudah Melakukan Stop Akomodasi'
        ];

        $result = [
            'is_stopakomodasi' => isset($data['response']['is_stopakomodasi']) ? $data['response']['is_stopakomodasi'] : false,
            'message' => $message,
        ];

        return $result;
    }

    public function getSingleConfig($key){
        $response = Yii::$app->docoRest->ranap->get('allow/get-config',[
            'query' => [
                'key' => $key,
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        return $data['response'];
    }
    
    public function coalesce(Array $data, $default_data = false) {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (!empty($value)) return $value;
            }
        }
        return $default_data;
    }

}
