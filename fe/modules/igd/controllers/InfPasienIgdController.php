<?php

/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-06-05 13:42:42
 * @Description:
 */

namespace Doco\igd\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

// use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\models\User;

use app\modules\igd\models\AturDokterForm;
use app\modules\igd\models\PasienBatalPeriksaForm;

class InfPasienIgdController extends DocoController
{
    protected $_title = "Informasi pasien Rawat Darurat";
    protected $_controller = '/igd/inf-pasien-igd';
    protected $_restIgd;

    public function init()
    {
        parent::init();
        $this->_restIgd = Yii::$app->docoRest->igd;
    }

    public function behaviors()
    {
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    public function listRequest(){
        $result = ['instalasi_id'=>Yii::$app->docoVars->workspace("instalasi_id"), 
                        'ruangan_id'=>Yii::$app->docoVars->workspace("ruangan_id")];
        return $result;
    }

    public function actionIndex()
    {
        try {
            $response = $this->_restIgd->get('allow/get-api');
            $body = json_decode($response->getBody(), TRUE);
            $resFilter = $body['response'];
            $lookup = $resFilter['lookup'];
            $master = $resFilter['master'];
            $dokterJaga = ArrayHelper::map($resFilter['listDokter'], 'nama_pegawai', 'nama_pegawai');
            $title = Yii::t('fe', 'Informasi Pasien Rawat Darurat');
            $all_ruangan = false;

            // $responseDokterJaga = $this->_restIgd->get('allow/get-list-dokter');
            // $bodyDokterJaga = json_decode($responseDokterJaga->getBody(), True);
            // $resDokterJaga = $bodyDokterJaga['response'];
            // $dokterJaga = ArrayHelper::map($bodyDokterJaga['response'], 'nama_pegawai', 'nama_pegawai');
            // to disable button batal
            $_isBlmPeriksa = DocoConstants::STATUS_PERIKSA_BLM_PERIKSA;
            $_isBatal = DocoConstants::STATUS_PERIKSA_BTL_PERIKSA;
            $_isPeriksa = DocoConstants::STATUS_PERIKSA_DIPERIKSA;
            $_isSetDokter = DocoConstants::STATUS_PERIKSA_SET_DOKTER;
            $_isAntrPoli = DocoConstants::STATUS_PERIKSA_ANTR_POLI;
            $_isRujukRawatInap = DocoConstants::STATUS_PERIKSA_RUJUK_RANAP;
            $_isPeriksaPulang = DocoConstants::STATUS_PERIKSA_PULANG;

            $getcarabayar = isset($master['carabayar']) ? $master['carabayar'] : [];
            $listcarabayar = ArrayHelper::map($getcarabayar, 'carabayar_id', 'carabayar_nama');
            $penjamin = ArrayHelper::map($master['penjamin'], 'penjamin_id', 'penjamin_nama');
            $status_periksa = ArrayHelper::map($lookup['status_periksa'], 'lookup_name', 'lookup_name');

            $response_cara_bayar = $this->_restIgd->get('allow/cara-bayar-list', ['form_params' => []]);
            $list_cara_bayar = json_decode($response_cara_bayar->getBody(), True)['response'];

            return $this->render('index', get_defined_vars());
        } catch(RequestException $e){
            return $e->getMessage();
        } 
    }

    public function actionListPasienSaya() 
    {
        try {
            $response = $this->_restIgd->get('allow/get-api');
            $body = json_decode($response->getBody(), TRUE);
            $resFilter = $body['response'];
            $lookup = $resFilter['lookup'];
            $master = $resFilter['master'];
            $dokterJaga = ArrayHelper::map($resFilter['listDokter'], 'nama_pegawai', 'nama_pegawai');
            $title = Yii::t('fe', 'Informasi Pasien Rawat Darurat') . ' - ' . Yii::t('fe', 'Pasien saya');
            $all_ruangan = true;
            
            // to disable button batal
            $_isBlmPeriksa = DocoConstants::STATUS_PERIKSA_BLM_PERIKSA;
            $_isBatal = DocoConstants::STATUS_PERIKSA_BTL_PERIKSA;
            $_isPeriksa = DocoConstants::STATUS_PERIKSA_DIPERIKSA;
            $_isSetDokter = DocoConstants::STATUS_PERIKSA_SET_DOKTER;
            $_isAntrPoli = DocoConstants::STATUS_PERIKSA_ANTR_POLI;
            $_isRujukRawatInap = DocoConstants::STATUS_PERIKSA_RUJUK_RANAP;
            $_isPeriksaPulang = DocoConstants::STATUS_PERIKSA_PULANG;

            $getcarabayar = isset($master['carabayar']) ? $master['carabayar'] : [];
            $listcarabayar = ArrayHelper::map($getcarabayar, 'carabayar_id', 'carabayar_nama');
            $penjamin = ArrayHelper::map($master['penjamin'], 'penjamin_id', 'penjamin_nama');
            $status_periksa = ArrayHelper::map($lookup['status_periksa'], 'lookup_name', 'lookup_name');

            $response_cara_bayar = $this->_restIgd->get('allow/cara-bayar-list', ['form_params' => []]);
            $list_cara_bayar = json_decode($response_cara_bayar->getBody(), True)['response'];

            return $this->render('index', get_defined_vars());
        } catch(RequestException $e){
            return $e->getMessage();
        } 
    }
    
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $counter=0;

        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $all_ruangan = $request->get('all_ruangan');
            $idR = null;
            if (!$all_ruangan) {
                $idR = Yii::$app->docoVars->workspace("ruangan_id");
            }

            $login = Yii::$app->docoVars->user('id_pegawai');
            $kelompokpegawai_id = Yii::$app->docoVars->user('kelompokpegawai_id');  
            
            // echo '<pre>';var_dump($idR);die();
            $response = $this->_restIgd->get('inf-pasien-igd/index?idruangan='.$idR.'&'.http_build_query($yiiRestfulParams),['form_params'=>[] ]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            
            // print_r($body['response']); exit;
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                // $value['is_konsul'] = 
                //     $login != $value['dokter_jaga_id'] && 
                //         $login != $value['dokter_id'] && 
                //         $kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS 
                //     ? true 
                //     : false;
                $value['is_konsul'] = false;
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d M Y H:i:s', strtotime($value['tgl_pendaftaran']));
                $value['no_pendaftaran'] = strtoupper($value['nama_pasien']).' ('.substr($value['jenis_kelamin'], 0, 1).')<br>No. Registrasi : '.$value['no_pendaftaran'].'<br>No. RM : '.$value['no_rekam_medik'];
                $value['penjamin_nama'] = 'Cara Bayar : '.$value['carabayar_nama'].'<br>Penjamin : '.$value['penjamin_nama'];
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

    public function actionPeriksa($id){
        try {
            $response = $this->_restIgd->request('GET', 'inf-pasien-igd/update-status-periksa',
                [
                    'query' => ['id' => $id]
                ]
            );
            return $this->redirect(['pemeriksaan-igd/periksa?id='.$id]);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionAssignDokter($id, $ruangan_id = null, $instalasi_id = null, $pegawai_id = null, $crossModule = false)
    {
        $request = Yii::$app->request;
        try {
            $title = Yii::t('fe', 'Assign dokter');
            if ($crossModule && !empty($ruangan_id) && !empty($instalasi_id)) {
                $urlSubmit = $this->helper->crossUrl('opento', [
                    'ruangan_id' => $ruangan_id,
                    'instalasi_id' => $instalasi_id,
                    'modul' => 'igd',
                    'url' => 'igd/inf-pasien-igd/set-dokter'
                ]);
            } else {
                $urlSubmit = '/igd/inf-pasien-igd/set-dokter';
            }
            $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
            $model = new AturDokterForm;
            $model->pendaftaran_id = $pendaftaran_id;
            $docoVars = Yii::$app->docoVars;
            $default_id = !empty($pegawai_id) ? $pegawai_id : $docoVars->user("id_pegawai");
            $ruangan_id = $docoVars->workspace('ruangan_id');
            $req = $this->_restIgd->get('allow/get-list-dokter-jaga', [
                'query'=>['ruangan_id'=>$ruangan_id]
            ]);

            $body = json_decode($req->getBody(), true);
            $listDokterJaga = $body['response'];

            return $this->renderAjax('_assign_dokter',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionSetDokter()
    {
        $request = Yii::$app->request;
        try {
            $model = new AturDokterForm;
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restIgd->post('inf-pasien-igd/set-dokter',
                    [
                        'form_params' => $model->attributes
                    ]
                );
                $body = json_decode($response->getBody(), true);

                if ($body['metadata']['status'] == 200) {
                    $body['redirectUrl'] = $this->helper->crossUrl('jumpto', [
                        'ruangan_id' => $body['response']['data']['ruangan_id'],
                        'instalasi_id' => DocoConstants::INSTALASI_ID_RD,
                        'modul' => 'igd',
                        'url' => 'igd/pemeriksaan-igd/periksa?id='.$this->helper->encrypt($model->pendaftaran_id)
                    ]);
                    return DocoHelpers::response($body, false, 'AturDokterForm');
                } else {
                    return DocoHelpers::response($body, false, 'AturDokterForm');
                }
            } else {
                return DocoHelpers::response([
                    'response' => [
                        'data' => $model->getErrors()
                    ]
                ],422,'AturDokterForm');
            }
        } catch (RequestException $e) {
              throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
              throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionAksiBatal($id)
    {
        try {
            $title = Yii::t('fe', 'Batal Pemeriksaan');
            $model = new PasienBatalPeriksaForm;
            $request = Yii::$app->request;
            $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
            $bundleData = $this->guzzleExec($this->_restIgd, [
                'url' => 'inf-pasien-igd/bundle-data-batal-periksa',
                'payload' => [
                    'query' => compact('pendaftaran_id')
                ],
            ]);
            $username = Yii::$app->docoVars->user('nama');
            $model->load($request->post());
            return $this->renderAjax('_batal',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionBatal() 
    {
        $request = Yii::$app->request;
        try {
            $model = new PasienBatalPeriksaForm;
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restIgd->post('inf-pasien-igd/batal?id='.$model->pendaftaran_id,
                    [
                        'form_params' => $model->attributes
                    ]
                );
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body);
            } else {
                return DocoHelpers::response([
                    'response' => [
                        'data' => $model->getErrors()
                    ]
                ],422,'PasienBatalPeriksaForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }

    }


    public function actionExportRincianTagihanPdf($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        // return $request;

        $path = Yii::getAlias("@download") . "/rincian-tagihan-pasien-igd.pdf";
        try {
            // $response = $this->_restIgd->get('inf-pasien-igd/export-rincian-tagihan-pdf?pendaftaran_id=' . $pendaftaran_id, [
            //     'save_to' => $path,
            // ]);
            $response = $this->_restIgd->get('pemeriksaan-igd/cetak-rincian-tagihan?id=' . $pendaftaran_id, [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $all_ruangan = $request->get('all_ruangan');
        $idR = null;
        if (!$all_ruangan) {
            $idR = Yii::$app->docoVars->workspace("ruangan_id");
        }
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/informasi-pasien-rawat-darurat.pdf";
        try {
            $response = $this->_restIgd->get('inf-pasien-igd/export-pdf?ruangan='.Yii::$app->docoVars->workspace("ruangan_id").'&idruangan='.$idR.'&'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $all_ruangan = $request->get('all_ruangan');
        $idR = null;
        if (!$all_ruangan) {
            $idR = Yii::$app->docoVars->workspace("ruangan_id");
        }
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-rawat-darurat.xlsx";
            $response = $this->_restIgd->get('inf-pasien-igd/export-excel?ruangan='.Yii::$app->docoVars->workspace("ruangan_id").'&idruangan='.$idR.'&'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

        public function actionGetPenjamin($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('penjamin?advanced-filter[carabayar_m.carabayar_id]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $assign_id? $value['penjamin_id'] : $value['penjamin_nama'],
                    'name' => $value['penjamin_nama']
                ];
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
