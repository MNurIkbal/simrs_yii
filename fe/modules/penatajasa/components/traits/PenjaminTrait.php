<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\penatajasa\components\traits;

use Yii;
use yii\web\Response;
use yii\web\UploadedFile;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;

use app\modules\penatajasa\models\PenjaminForm;
use app\modules\penatajasa\models\HapusPenjaminForm;

trait PenjaminTrait {
    protected $_penataJasa;

    public function actionGetListPenjamin()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $data = [];
        $draw = $request->get('draw', 1);

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $pendaftaran_id = $request->get('pendaftaran_id');
        try{
            $rest = $this->_penataJasa->get('informasi-pasien/get-list-penjamin', [
                'query' => [
                    'pendaftaran_id' => DocoHelpers::decrypt($pendaftaran_id)
                ],
            ]);
            $body = json_decode($rest->getBody(), true);
            $response = $body['response'];
            $i = 0;
            foreach ($response as $key => $value) {
                $i++;
                $primary = $key;
                $value['rowNum'] = $i;
                $value['penjamin_nama'] = $value['carabayar_nama'].' - '.$value['penjamin_nama'];
                $value['nokartuasuransi'] = $value['nokartuasuransi'];
                $value['nominal_dijaminR'] = DocoHelpers::rupiahDisplay($value['nominal_dijamin']);
                $value['nominal_dijamin'] = $value['nominal_dijamin'];
                $value['aksi'] = '<button class="btn btn-danger" style="margin-bottom: 5px" data-toggle="modal" data-target="#modal_backdrop" action="'.Url::to(['form-delete-penjamin', 'id' => $value['pendaftaranpenjamin_id']]).'"><i class="fa fa-trash fa-xs"></i></button>';
                // if(isset($value['pendaftaranpenjamin_id'])){
                //     $value['aksi'] = '';
                //     if($value['is_deleted'] == false){
                //         $value['aksi'] = '<button class="btn btn-danger" style="margin-bottom: 5px" data-toggle="modal" data-target="#modal_backdrop" action="'.Url::to(['form-delete-penjamin', 'id' => $primary]).'"><i class="fa fa-trash fa-xs"></i></button>';
                //     }
                // }else{
                //     $value['aksi'] = '<button class="btn btn-danger btn-delete-penjamin" style="margin-bottom: 5px" data-url="'.Url::to(['delete-penjamin', 'id' => $primary]).'"><i class="fa fa-trash fa-xs"></i></button>';
                // }
                $data[] = $value;
            }
        } catch(\Exception $e){
            $data = [];
        } catch(\RequestException $e){
            $data = [];
        }

        $result['data'] = $data;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return DocoHelpers::response($result);
    }

    public function actionSavePenjamin()
    {
        $title =Yii::t('fe', 'Tambah Penjamin');
        $request = Yii::$app->request;
        $carabayar_id = $request->get('carabayar_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $pasien_id = $request->get('pasien_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $nokartuasuransi = $request->get('nokartuasuransi', null);
        $model = new PenjaminForm;
        $getForm = $request->post();
        if($getForm && $model->load($getForm) ){
            if ($getForm['PenjaminForm']['penjamin_id'] == 0 || $getForm['PenjaminForm']['penjamin_id'] == '') {
                $get_penjamin = explode(' - ', $getForm['PenjaminForm']['penjamin_carabayar']);
                $_carabayar = $get_penjamin[0];
                $_penjamin = $get_penjamin[1];
                $getP = $this->_penataJasa->get('informasi-pasien/get-list-penjamin-default', [
                    'query' => [
                        'penjamin_nama' => $_penjamin,
                        'carabayar_nama' => $_carabayar
                    ],
                ]);
                $bodyP = json_decode($getP->getBody(), true);
                $responseP = $bodyP['response'];
                $model->penjamin_id = $responseP['penjamin_id'];
                $model->carabayar_nama = $responseP['carabayar_nama'];
                $model->carabayar_id = $responseP['carabayar_id'];
                $model->penjamin_nama = $responseP['penjamin_nama'];
                $model->id_penjamin = $responseP['penjamin_id'];
            }
            $model->nominal_dijamin = DocoHelpers::convertToAngka($model->nominal_dijamin);
            if($model->validate()){
                if( empty($model->asuransipasien_id) ){
                    try{
                        $saveAsuransiPenjamin = $this->_penataJasa->post('informasi-pasien/save-asuransi', [
                            'form_params' => $model->attributes
                        ]);
                        $resultSave = json_decode($saveAsuransiPenjamin->getBody(), true);
                        return DocoHelpers::response($resultSave, false, 'PenjaminForm');
                    } catch(\Exception $e){
                        return DocoHelpers::response([
                            'text' => 'Terjadi Kesalahan di Server!',
                        ], 500);
                    } catch(\RequestException $e){
                        return DocoHelpers::response([
                            'text' => 'Terjadi Kesalahan Ketika Menyambungkan! harap cek koneksi anda',
                        ], 500);
                    }
                }
                return DocoHelpers::response(['text' => 'Simpan penjamin berhasil!']);
            }else{
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'PenjaminForm');
            }
        }
    }

    public function actionTambahPenjamin()
    {
        $title =Yii::t('fe', 'Tambah Penjamin');
        $request = Yii::$app->request;
        $carabayar_id = $request->get('carabayar_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $pasien_id = $request->get('pasien_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $nokartuasuransi = $request->get('nokartuasuransi', null);
        $model = new PenjaminForm;
        $session_name = 'list-penjamin-data-'.$pendaftaran_id;
        if($request->post() && $model->load($request->post()) ){
            if($model->validate()){
                $cekTersedia = $this->cekTersedia($model->attributes, $session_name);
                if($cekTersedia){
                    $model->addError('nokartuasuransi', 'Tidak Bisa Menambah Tanggungan Penjamin dengan No Kartu Asuransi sama dan Penjamin yang sama!');
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, 'PenjaminForm');
                }
                if( empty($model->asuransipasien_id) ){
                    try{
                        $saveAsuransiPenjamin = $this->_penataJasa->post('informasi-pasien/save-asuransi', [
                            'form_params' => $model->attributes
                        ]);
                        $resultSave = json_decode($saveAsuransiPenjamin->getBody(), true);
                        if(!isset($resultSave['response']['asuransipasien_id'])){
                            throw new \Exception("Terjadi Kesalahan");
                        }
                        $model->asuransipasien_id = $resultSave['response']['asuransipasien_id'];
                    } catch(\Exception $e){
                        return DocoHelpers::response([
                            'text' => 'Terjadi Kesalahan di Server!',
                        ], 500);
                    } catch(\RequestException $e){
                        return DocoHelpers::response([
                            'text' => 'Terjadi Kesalahan Ketika Menyambungkan! harap cek koneksi anda',
                        ], 500);
                    }
                }
                $this->setSessionPenjamin($model->attributes, false, $session_name);
                return DocoHelpers::response(['text' => 'Simpan penjamin berhasil!']);
            }else{
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'PenjaminForm');
            }
        }
        try{
            $rest = $this->_penataJasa->get('allow/get-api-modal-penjamin');
            $body = json_decode($rest->getBody(), true);
            $carabayar = isset($body['response']['carabayar']) ? $body['response']['carabayar'] : [];
            $kelaspelayanan = isset($body['response']['kelaspelayanan']) ? $body['response']['kelaspelayanan'] : [];
        } catch (\Exception $e){
            $carabayar = $kelaspelayanan = [];
        } catch(\RequestException $e){
            $carabayar = $kelaspelayanan = [];
        }
        $model->carabayar_id = $carabayar_id;
        $model->pasien_id = $pasien_id;
        $model->kelastanggunganasuransi_id = $kelaspelayanan_id;
        $model->nokartuasuransi = $nokartuasuransi;
        return $this->renderAjax('partial/penjamin/_form_penjamin', [
            'model' => $model,
            'title' => $title,
            'carabayar' => $carabayar,
            'penjamin_id' => $penjamin_id,
            'kelaspelayanan' => $kelaspelayanan
        ]);
    }

    public function actionDeletePenjamin($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_penataJasa->delete('informasi-pasien/delete-penjamin?id=' . $id);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                []
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionFormDeletePenjamin(){
        $model = new HapusPenjaminForm;
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Hapus Penjamin');
        $id = $request->get('id');
        if($request->post() && $model->load($request->post())){
            if($model->validate()){
            $response = $this->_penataJasa->request('DELETE', 'informasi-pasien/delete-penjamin',[
                'form_params' => [ 'alasan' => $model->alasan ],
                'query' => ['id' => $id ]
            ]);
            // $response = json_decode($response->getBody(), true);

            // return DocoHelpers::response($response);
                return DocoHelpers::response([
                    'response' => [
                            'title' => 'Proses Berhasil!',
                            'text' => 'Data Berhasil Dihapus!'
                    ]
                ]);
            }else{
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'HapusPenjaminForm');
            }
        }
        return $this->renderAjax('partial/penjamin/_form_alasan', [
            'model' => $model,
            'title' => $title,
        ]);
    }

    protected function setSessionPenjamin($data, $init = false, $session_name)
    {
        $session = Yii::$app->session;
        if($init){
            $session->set($session_name, []);
            $session->set($session_name, $data);
        }else{
            $sessionData = $session->get($session_name);
            $sessionData[] = $data;
            $session->set($session_name, $sessionData);
        }
        return true;
    }
    public function actionCariNoAsuransi()
    {
        $request = Yii::$app->request;
        try{
            $rest = $this->_penataJasa->get('allow/check-exist-asuransi', [
                'query' => [
                    'no_asuransi' => trim($request->post('no_asuransi', null)),
                    'pasien_id' => trim($request->post('pasien_id', null)),
                    'penjamin_id' => trim($request->post('penjamin_id', null)),
                ]
            ]);
            $result = json_decode($rest->getBody(), true);
            return DocoHelpers::response($result['response']);
        } catch(\Exception $e){
            return DocoHelpers::response([
                'text' => 'Terjadi Kesalahan Koneksi!',
            ], 500);
        } catch(\RequestException $e){
            return DocoHelpers::response([
                'text' => 'Terjadi Kesalahan Server!',
            ], 500);
        }
    }
    public function cekTersedia($attributes, $session_name)
    {
        $sessionData = Yii::$app->session->get($session_name);
        foreach ($sessionData as $key => $value) {
            if($attributes['penjamin_id'] == $value['penjamin_id'] && $attributes['nokartuasuransi'] == $value['nokartuasuransi']){
                return true;
            }
        }
        return false;
    }
}