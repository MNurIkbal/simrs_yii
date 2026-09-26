<?php

/**
 * @Author: Aris
 * @Date:   2019-06-24 15:00:00
 * @Description:
 */

namespace Doco\ranap\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\ranap\models\SuratKeteranganBayiForm;

class InfSuratKeteranganBayiController extends DocoController
{
    protected $_title      = "Informasi pasien rawat inap";
    protected $_controller = '/ranap/inf-surat-keterangan-bayi';
    protected $_restRanap;

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;

    }

    public function behaviors()
    {
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    /*=========================================
    =            list info pasien bayi            =
    =========================================*/

    public function actionIndex()
    {
        try 
        {
            $title = Yii::t('fe', 'Informasi Surat Keterangan Bayi');
            return $this->render('index', get_defined_vars());
        }
        catch(RequestException $e)
        {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataBayi()
    {
        $request          = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try 
        {
           $response = $this->_restRanap->get('inf-surat-keterangan-bayi',
            [
                'query'=>$yiiRestfulParams
            ]);

           $body = json_decode($response->getBody(), TRUE);
           $data = [];
           $no   = $request->get('start');

            foreach ($body['response']["data"] as $key => $value) :
                $no++;
                $primary                  = json_encode($value['pendaftaran_id']);
                $value['primary']         = DocoHelpers::encrypt($primary);
                $value['rowNum']          = $no;
                $value['tgl_pendaftaran'] = date('d-M-Y',strtotime($value['tgl_pendaftaran']));
                $value['rm_namabayi']     = $value['rm_bayi'].'-'.$value['nama_bayi'];
                $value['tgl_lahir']       = date('d-M-Y',strtotime($value['tanggal_lahir']));
                $value['nama_ruangan']    = $value['ruangan'].'-'.$value['kamar'].'-'.$value['no_tempattidur'];
                $value['rm_namaibu']      = $value['rm_ibu'].'-'.$value['nama_ibu'];
                $value['status_skl']      = $value['status_skl'];
                $data[$key]               = $value;
            endforeach;

            $return = 
            [
                'data'            => $data,
                'draw'            => $request->get('draw'),
                'recordsTotal'    => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);

        }
        catch(RequestException $e)
        {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionBuatSk($id)
    {
        $pendaftaran_id = $id;
        $id = DocoHelpers::decrypt($id);
        $title = "Surat Keterangan Lahir";
        $data_pasien = [];

        $model = new SuratKeteranganBayiForm;
        try
        {

            $response            = $this->_restRanap->get('inf-surat-keterangan-bayi/get-pasien?id=' . $id);
            
            $response            = json_decode($response->getBody(),true);
            
            $data_pasien         = isset($response['response']['data_bayi'])?$response['response']['data_bayi']:[];
            $data_pekerjaan      = isset($response['response']['data_pekerjaan'])?$response['response']['data_pekerjaan']:[];
            $data_golongan_darah = isset($response['response']['data_golongan_darah'])?$response['response']['data_golongan_darah']:[];
            //$data_hari           = isset($response['response']['data_hari'])?$response['response']['data_hari']:[];
            
            $resp_data_bayi      = isset($response['response']['data_bayi'])?$response['response']['data_bayi']:[];
            $resp_skl_data_bayi  = isset($response['response']['data_skl_bayi'])?$response['response']['data_skl_bayi']:[];

            //Value Hidden Input
            $model->pendaftaran_id    = isset($resp_data_bayi['pendaftaran_id'])?$resp_data_bayi['pendaftaran_id']:'';
            $model->pasienadmisi_id   = isset($resp_data_bayi['pasienadmisi_id'])?$resp_data_bayi['pasienadmisi_id']:'';
            $model->pasien_id         = isset($resp_data_bayi['pasien_id'])?$resp_data_bayi['pasien_id']:'';
            $model->dokterdpjp_id     = isset($resp_data_bayi['dokter_id'])?$resp_data_bayi['dokter_id']:'';
            $model->ibu_nama          = isset($resp_data_bayi['nama_ibu'])?$resp_data_bayi['nama_ibu']:'';
            $model->ibu_ktp           = isset($resp_data_bayi['no_identitas'])?$resp_data_bayi['no_identitas']:'';
            $model->ibu_alamat        = isset($resp_data_bayi['alamat'])?$resp_data_bayi['alamat']:'';
            $model->ibu_pekerjaan     = isset($resp_data_bayi['pekerjaan'])?$resp_data_bayi['pekerjaan']:'';
            $model->ibu_golongandarah = isset($resp_data_bayi['golongan_darah'])?$resp_data_bayi['golongan_darah']:'';

            //Value Suggestion Dari infopasienbayi_v
            $model->ayah_nama     = isset($resp_data_bayi['nama_ayah'])?$resp_data_bayi['nama_ayah']:'';
            $model->anakke        = isset($resp_data_bayi['anakke'])?$resp_data_bayi['anakke']:'';
            $model->panjang_lahir = isset($resp_data_bayi['tinggi_badan'])?$resp_data_bayi['tinggi_badan']:0;
            $model->bb_lahir      = isset($resp_data_bayi['berat_badan'])?$resp_data_bayi['berat_badan']:0;

            //Value Dari suratketlahir_t
            $model->ayah_nama             = isset($resp_skl_data_bayi['ayah_nama'])?$resp_skl_data_bayi['ayah_nama']:$resp_data_bayi['nama_ayah'];
            $model->ayah_ktp              = isset($resp_skl_data_bayi['ayah_ktp'])?$resp_skl_data_bayi['ayah_ktp']:'';
            $model->ayah_alamat           = isset($resp_skl_data_bayi['ayah_alamat'])?$resp_skl_data_bayi['ayah_alamat']:'';
            $model->ayah_pekerjaan_id     = isset($resp_skl_data_bayi['ayah_pekerjaan_id'])?$resp_skl_data_bayi['ayah_pekerjaan_id']:'';
            $model->ayah_golongandarah_id = isset($resp_skl_data_bayi['ayah_golongandarah_id'])?$resp_skl_data_bayi['ayah_golongandarah_id']:'';
            $model->tgl_lahir             = isset($resp_skl_data_bayi['tgl_lahir'])?$resp_skl_data_bayi['tgl_lahir']:'';
            $model->jam_lahir             = isset($resp_skl_data_bayi['jam_lahir'])?$resp_skl_data_bayi['jam_lahir']:'';
            $model->bb_lahir              = isset($resp_skl_data_bayi['bb_lahir'])?$resp_skl_data_bayi['bb_lahir']:$resp_data_bayi['berat_badan'];
            $model->panjang_lahir         = isset($resp_skl_data_bayi['panjang_lahir'])?$resp_skl_data_bayi['panjang_lahir']:$resp_data_bayi['tinggi_badan'];
            $model->kelahiran             = isset($resp_skl_data_bayi['kelahiran'])?$resp_skl_data_bayi['kelahiran']:'';
            $model->anakke                = isset($resp_skl_data_bayi['anakke'])?$resp_skl_data_bayi['anakke']:$resp_data_bayi['anakke'];
            $model->golongan_darah        = isset($resp_skl_data_bayi['golongan_darah'])?$resp_skl_data_bayi['golongan_darah']:'';

            //echo "<pre>";
            //var_dump($response['response']);die();
            
        }
        catch(RequestException $e)
        {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
        
        //var_dump($response);die();
        return $this->render('buat_surat_keterangan_lahir',get_defined_vars());

    }

    public function actionSave()
    {
        $model   = new SuratKeteranganBayiForm;
        $request = Yii::$app->request;
        $model->load($request->post());
        if ($model->validate()) {
            $response = $this->_restRanap->post("inf-surat-keterangan-bayi/save", [
                    "form_params" => $model->attributes
                ]);
            $result = json_decode($response->getBody(),true);
            return DocoHelpers::response($result);
        }
        else
        {
            return DocoHelpers::response($model->errors,422,'SuratKeteranganBayiForm');
        }
    }

    public function actionExportPdf($pendaftaran_id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $request = Yii::$app->request;
        $path    = Yii::getAlias("@download") . "/inf-surat-keterangan-bayi.pdf";
        try {
            $response = $this->_restRanap->get('inf-surat-keterangan-bayi/export-pdf?pendaftaran_id='.$pendaftaran_id,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/inf-surat-keterangan-bayi.xlsx";
            $response = $this->_restRanap->get('inf-surat-keterangan-bayi/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}
