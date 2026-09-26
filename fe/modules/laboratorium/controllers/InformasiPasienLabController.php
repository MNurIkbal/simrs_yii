<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Pasien Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\master\models\KamarForm;
use app\modules\laboratorium\models\BatalPeriksaPenunjangForm;
use app\modules\laboratorium\models\InfoPasienLabView;
use app\modules\laboratorium\response\Pasienlab;

use yii\helpers\ArrayHelper;

class InformasiPasienLabController extends DocoController
{
    protected $_title = "Informasi Pasien Laboratorium";
    protected $_module = '/laboratorium/informasi-pasien-lab';
    protected $_restLab;
    protected $_restKasir;
    protected $_restMaster;
    protected $_id_ruangan;

    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $cara_bayar = $penjamin = $status_bayar = $asal_rujukan = [];

        $status = DocoConstants::$status_lab;
        $asalRujukan = DocoConstants::$asal_rujukan; 

        try {
            $response = $this->_restLab->get('inf-pasien-lab/get-options');
            $body = json_decode($response->getBody(), true);
            $cara_bayar = $body['response']['cara_bayar'];
            $penjamin = $body['response']['penjamin'];
            $asal_rujukan = $body['response']['asal_rujukan'];
            $status_bayar = $body['response']['status_bayar'];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = $status_bayar = $asal_rujukan = [];
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restLab->request('get', 'inf-pasien-lab/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $value['primary'] = $primaryKey;
                unset($value['kamarruangan_id']);
                $value['rowNum'] = $no;
                $value['noRegis'] = DocoHelpers::encrypt($value['no_pendaftaran']);;
                $value['status_periksa'] = DocoConstants::$status_lab[$value['status_periksa']];
                $color = '#FFFfff';
                $font = 'black';
                if (strtolower($value['status_periksa']) == 'batal') {
                    $color = '#d64541';
                    $font = 'white';
                } else if (strtolower($value['status_periksa']) == 'selesai') {
                    $color = '#26A65B';
                    $font = 'white';
                } else if(strtolower($value['status_periksa']) == 'periksa') {
                    $color = '#05bbbe';
                    $font = 'white';
                } else if(strtolower($value['status_periksa']) == 'ambil sampel') {
                    $color = '#F5D76E';
                    $font = 'white';
                } 
                $value['status_periksa_btn'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.$value['status_periksa'].'</span>';
                
                if (strtolower($value['status_bayar']) == 'sudah bayar') {
                    $colorStatusBayar = '#26A65B';
                    $fontStatusBayar = 'white';
                }
                else {
                    $colorStatusBayar = '#FFFfff';
                    $fontStatusBayar = 'black';
                }
                $value['status_bayar_btn'] = '<span class="badge" style="background: '.$colorStatusBayar.'; color: '.$fontStatusBayar.'">'.$value['status_bayar'].'</span>';
                $value['jenis_kelamin'] = '('.$value['jenis_kelamin_kode'].')';
                $value['tglmasukpenunjang'] = DocoHelpers::convDateTime($value['tglmasukpenunjang'],true,true);
                $value['tanggal_lahir'] = empty($value['tanggal_lahir']) ? " - " : DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])),true,false);
                $value['nama_pasien'] = $value['nama_pasien'].'&nbsp'.$value['jenis_kelamin'].'<br>'.$value['tanggal_lahir'].'<br>'.$value['no_pendaftaran'].' - '.$value['no_rekam_medik'];
                $value['no_antrian'] = $this->getAksiListPasien($value);
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function getAksiListPasien($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);

        $return_data = '';
        if(!empty($data['no_antrian'])){
            $return_data .= Html::button(
                '<i class="fa fa-volume-up"></i> '.$data['no_antrian'] ,
                [
                    'class' => 'btn btn-turquoise btn-xs-new antrian',
                    'data-tooltip' => "tooltip",
                    'data-original-title' => Yii::t('fe', 'Panggil antrian'),
                    'data-id' => $data['pendaftaran_id'],
                    'data-antrian' => $data['no_antrian'],
                ]
            );
        }
        $return_data .= '&nbsp;&nbsp;';

        return $return_data;
    }

    /**
     * @author Randy Vianda Putra
     * @todo Panggil Antrian
     * @copyright 7 May 2018 aweutist
     */
    public function actionPanggilAntrian()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $post = Yii::$app->request->post();

        try {
            $no_antrian = $post['no_antrian'];
            $teks_panggil = DocoHelpers::convertAntrian($no_antrian);
            $response['teks_panggil'] = $teks_panggil;
            return DocoHelpers::response($response, 200);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }


    public function actionGetAsalRujukan2($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if($get['asal_1'] == "order"){
                $response = $this->_restLab->get('inf-pasien-lab/list-instalasi');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value){
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if($get['asal_1'] == "rujukanRS"){
                $response = $this->_restLab->get('inf-pasien-lab/list-asal-rujukan');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionGetAsalRujukan3($q = "",$q2="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if ($get['asal_1'] == "order") {
                $response = $this->_restLab->get('inf-pasien-lab/list-ruangan?instalasi_id='.$get['asal_2']);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if ($get['asal_1'] == "rujukanRS") {
                $response = $this->_restLab->get('inf-pasien-lab/list-asal-rujukan-dari?asalrujukan_id=' . $get['asal_2']);
                $body = json_decode($response->getBody(), true);
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionFormBatal($id){
        $id = DocoHelpers::decrypt($id);
        $model = new BatalPeriksaPenunjangForm;
        $detail = $pemeriksaan = $data = [];
        try {
            $response = $this->_restLab->get('inf-pasien-lab/detail-periksa?pasienmasukpenunjang_id=' . $id);
            $body = json_decode($response->getBody(), true);
            $detail = $body['response']['labDetail'];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }
        return $this->render('form_batal', get_defined_vars());
    }

    public function actionBatalOrder()
    {
        $request = Yii::$app->request;
        $model = new BatalPeriksaPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {

                $formData = $request->post(); // tampung formdata

                $form_params['pasienmasukpenunjang_id'] = DocoHelpers::decrypt($formData['pasienmasukpenunjang_id']);
                $form_params['peg_menyetujui_id'] = $formData['BatalPeriksaPenunjangForm']['peg_menyetujui_id'];
                $form_params['tgl_batalperiksa'] = $formData['BatalPeriksaPenunjangForm']['tgl_batalperiksa'];
                $form_params['alasan'] = $formData['BatalPeriksaPenunjangForm']['alasan'];
                try {
                    $response = $this->_restLab->post('inf-pasien-lab/proses-batal', [
                        'form_params' => $form_params
                    ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }

            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } else {
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionGetDataPemeriksaan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restLab->get('inf-pasien-lab/get-pemeriksaan-view', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
                $value['primary'] = $primaryKey;
                $value['tp'] = $value['tindakanpelayanan_id'];
                unset($value['tindakanpelayanan_id']);
                $value['rowNum'] = $no;
                $color = '#FFFfff';
                $font = 'black';
                if (strtolower($value['status_periksa']) == 'batal') {
                    $color = '#D24D57';
                    $font = 'white';
                } else if (strtolower($value['status_periksa']) == 'selesai') {
                    $color = '#26A65B';
                    $font = 'white';
                } else if(strtolower($value['status_periksa']) == 'periksa') {
                    $color = '#2574A9';
                    $font = 'white';
                } else if(strtolower($value['status_periksa']) == 'ambil sampel') {
                    $color = '#F5D76E';
                    $font = 'white';
                }
                $value['status_periksa_btn'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.$value['status_periksa'].'</span>';

                if (strtolower($value['status_bayar']) == 'batal bayar') {
                    $color = '#D24D57';
                    $font = 'white';
                } else if (strtolower($value['status_bayar']) == 'sudah bayar') {
                    $color = '#26A65B';
                    $font = 'white';
                }
                $value['status_bayar_btn'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.$value['status_bayar'].'</span>';

                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionPrintRincian($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/_rincian_tagihan_lab.pdf";
        try {
            $response = $this->_restLab->get('inf-pasien-lab/print-rincian?id='.$id, ['save_to' => $path]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getListDataApi()
    {
            $request = Yii::$app->request;
            $response = $this->_restLab->get('inf-pasien-lab/get-list-data?id_ruangan='.$this->_id_ruangan);
            $body = json_decode($response->getBody(),TRUE);

            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
            ];

            return $result;
    }

    public function actionSetDokter($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $title = Yii::t('fe', 'Mulai Pemeriksaan');
        $list_data = $this->getListDataApi();
        $data_pegawai = $list_data["data_pegawai"];

        $modelPasien = new InfoPasienLabView;
        $form_name = substr(strrchr(get_class($modelPasien), "\\"), 1);
        $is_disabled = 'true';
        
        $response = $this->_restLab->get('inf-pasien-lab/get-pasien?id='.$id);
        $response = json_decode($response->getBody(), true);
        $data_pasien = $response["response"]["data"];
        // Cek data
        if ($modelPasien->load($data_pasien, '')) {
            // Assign manually
            $modelPasien->pegawai_id = $data_pasien['pegawai_id'];
            // Cek post
            if ($request->post()){
                $data = $request->post('InfoPasienLabView');
                $modelPasien->pegawai_id = $data["pegawai_id"];
                $modelPasien->tglmasukpenunjang = $data["tglmasukpenunjang"];
                $modelPasien->pasienmasukpenunjang_id = $id;
                if($modelPasien->validate()) {
                    $response = $this->_restLab->post('inf-pasien-lab/ubah-dokter', [
                            'form_params' => $modelPasien->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    $redirect = Url::to(['/laboratorium/speciment/index', 'id' => DocoHelpers::encrypt($id)]);
                    return json_encode(['url'=>$redirect]);
                }else{
                    return DocoHelpers::response($modelPasien->errors, 422, $form_name);
                }
            }else{
                return $this->renderAjax('_set_dokter', get_defined_vars());
            }
        }
        else {
            return DocoHelpers::responseTemplate(
                500,
                Yii::t('fe', 'Kegagalan Pada Sistem'),
                [],
                ['title' => Yii::t('fe', 'Gagal Mengubah Dokter'), 'text' => Yii::t('fe', 'Data tidak ditemukan.')]
            );
        }
    }

    
    public function actionFormEditPemeriksaan($noRegis, $id)
    {
        $response = $this->_restLab->get('inf-pasien-lab/pasien-lab-view',[
            'query' => [
                'id' =>  DocoHelpers::decrypt($id)
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        
        $responseLab = new Pasienlab;
        $responseLab->attributes = isset($body['response']) ? $body['response'] : [];
        $pasienmasukpenunjang_id = $id;
        return $this->renderAjax('form-edit-pemeriksaan', get_defined_vars());
    }

    public function actionBatalPemeriksaan($noRegis){
        $request = Yii::$app->request;
        if ($request->post()) {
            $formData = $request->post(); 
            $pasienmasukpenunjang_id= DocoHelpers::decrypt(isset($formData['pasienmasukpenunjang_id']) ? $formData['pasienmasukpenunjang_id'] : null);
            $is_all = isset($formData['is_all']) ? json_decode($formData['is_all'], true) : null;
            $form_params['pasienmasukpenunjang_id']= $pasienmasukpenunjang_id;
            $form_params['detail_tindakan'] = isset($formData['list_tindakan']) ? json_decode($formData['list_tindakan'], true) : null;
            $form_params['is_all'] = $is_all;
            $form_params['no_pendaftaran'] =  DocoHelpers::decrypt($noRegis);
            try {

                $response = $this->_restLab->post('inf-pasien-lab/batal-pemeriksaan', [
                    'form_params' => $form_params
                ]);
                $response = json_decode($response->getBody(), true);
                
                return DocoHelpers::response($response);
            } catch (RequestException $e) {
                return DocoHelpers::response($e->getMessage());
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }else{

            return DocoHelpers::response([
                'response'=>[
                    'title'=>"Proses Gagal", 
                    'text'=>'Parameter tidak dikenali'
                    ]
                ]);
        }
        

    }


}