<?php
/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

class NomorUrutProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $depdrop_params = $request->post('depdrop_params');
        $depdrop_all_params = $request->post('depdrop_all_params');//dari reservasi
        $is_update = $request->post('is_update');
        $is_nomor_urut = $request->get('is_nomor_urut',null);
        $date = null;
        $no_antrian_user = null;
        $monthsFull = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'Nopember',
            'Desember'
        ];
        $month = '';
        
        if(!empty($depdrop_all_params)) {

            if(!empty($depdrop_all_params['kunjunganform-dokter_id'])){
                $dokter_id = $depdrop_all_params['kunjunganform-dokter_id'];
            } else if(!empty($depdrop_all_params['pegawai_id'])){
                $dokter_id = $depdrop_all_params['pegawai_id'];
            } else{
                $dokter_id = ArrayHelper::getValue($depdrop_all_params, 'pegawai_id');
            }
            
             // convert to integer month, for reservasi poliklinik feature
             if(!empty($depdrop_all_params['tgl_pendaftaranol'])){
                if(strtotime($depdrop_all_params['tgl_pendaftaranol']) == false){
                    $temp_tgl_pendaftaranol = explode(' ',$depdrop_all_params['tgl_pendaftaranol']);
                    foreach ($monthsFull as $key => $value) {
                        # code...
                        if ($temp_tgl_pendaftaranol[1] == $value) {
                            # code...
                            $month = $key+1;
                        }
                    }
                    $tgl_pendaftaranol = $temp_tgl_pendaftaranol[0].'-'.$month.'-'.$temp_tgl_pendaftaranol[2];    
                }else{
                    $tgl_pendaftaranol = $depdrop_all_params['tgl_pendaftaranol'];
                }
            }
            

            $ruangan_id = $depdrop_all_params['ruangan_id'];
            if (isset($depdrop_all_params[''])) {
                # code...
            }
            $tanggal_pendaftaran_online = isset($depdrop_all_params['tgl_pendaftaranol']) ? $tgl_pendaftaranol : date("Y-m-d");
            $date = date('Y-m-d',strtotime($tanggal_pendaftaran_online));
            
            if(!is_null($is_nomor_urut)){
                $no_antrian_user = $is_nomor_urut;
            }else {
                $no_antrian_user = null;
            }
            
        } else {
            $dokter_id = array_key_exists(0, $depdrop_parents) ? $depdrop_parents[0] : null;
            $ruangan_id = array_key_exists(0, $depdrop_params) ? $depdrop_params[0] : null;
        }
        
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        if(empty($dokter_id) || empty($ruangan_id)) return $result;

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran->post('allow/get-nomor-urut?ruangan_id='.$ruangan_id.'&dokter_id='.$dokter_id.'&date='.$date.'&no_antrian_user='.$no_antrian_user);
            $response = json_decode($restPendaftaran->getBody(), true)['response'];
            if (!empty($response)) {
                if ($is_update) {
                    foreach ($response as $key => $value) {
                        $result['output'][] = [
                            'id' => $key,
                            'text' => $value
                        ];

                        if (empty($result['selected'])) {
                            $result['selected'] = $key;
                        }
                    }
                } else {
                    if (!empty($is_nomor_urut)){
                        foreach ($response as $key => $value) {
                            if ($key == $is_nomor_urut){
                                $result['output'][] = [
                                    'id' => $key,
                                    'name' => $value
                                ];
                            }
                            if (empty($result['selected'])) {
                                $result['selected'] = $is_nomor_urut;
                            }
                        }
                    } else {
                        foreach ($response as $key => $value) {
                            $result['output'][] = [
                                'id' => $key,
                                'name' => $value
                            ];
    
                            if (empty($result['selected'])) {
                                $result['selected'] = $is_nomor_urut;
                            }
                        }
                    }
                    
                }
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}