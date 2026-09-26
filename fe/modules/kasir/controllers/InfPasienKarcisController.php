<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Doco\kasir\controllers\PembayaranTagihanController;
use app\components\DocoConstants;

use Doco\kasir\models\TagihanPasienForm;
use yii\helpers\ArrayHelper;
use app\components\DHtml;

class InfPasienKarcisController extends DocoController
{
    public $_title = "Informasi Pasien Karcis";
    public $_module = 'kasir/inf-pasien-karcis/';
    public $_restKasir; 

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
        $this->_restMaster = Yii::$app->docoRest->master; 
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $response = $this->_restKasir->get('allow/get-api');
        $body = json_decode($response->getBody(), TRUE);
        $resMaster = isset($body['response']['master']) ? $body['response']['master'] : [];

        $getApiCaraBayar = $this->_restKasir->get('allow/get-cara-bayar');
        $bodyApiCaraBayar = json_decode($getApiCaraBayar->getBody(), TRUE);
        $getCaraBayar = isset($bodyApiCaraBayar['response']) ? $bodyApiCaraBayar['response'] : [];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restKasir->get('inf-pasien-karcis/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                // unset($value['pendaftaran_id']);

                $value['payment'] = Html::button('<i class="fa fa-shopping-cart" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-payment',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Pembayaran'),
                ]);

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['no_pendaftaran'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                
                $value['tarif_tindakan'] = DocoHelpers::formatNumber($value['tarif_tindakan']);
                $value['tgl_pendaftaran'] = date("j M Y", strtotime($value['tgl_pendaftaran']));

                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionView($id,$status = null)
    {
        $roleBtnCloseBill = DHtml::cekHakAkses('close-bill');
        $roleBtnCloseBill = ($roleBtnCloseBill) ? 1 : 0;
        return Yii::$app->runAction('/kasir/pembayaran-tagihan',[
            'id' => $id,
            'kelompok' => DocoConstants::PASIEN_KARCIS,
            'status' => $status,
            'roleBtnCloseBill' => $roleBtnCloseBill,
        ]);
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new TagihanPasienForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            $model->detail_tagihan = $request->post('detail_tagihan');
            if ($model->validate()) {

            } else {
                $response = $model->errors;
            }
            
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionGetPendaftaran()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restKasir->request('POST', 'inf-pasien-karcis/get-data-pendaftaran',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['pendaftaran_id'], 'text' => $value['no_pendaftaran']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportBkm($id)
    {
        // Get request
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        // Download path
        $path = Yii::getAlias("@download")."/bukti_kas_masuk.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restKasir->get('inf-pasien-karcis/cetak-bkm?id='.$id, ['save_to' => $path
            ]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'bukti_kas_masuk');
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportKwitansi($id)
    {
        // Get request
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        // Download path
        $path = Yii::getAlias("@download")."/kwitansi.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restKasir->get('inf-pasien-karcis/cetak-kwitansi?id='.$id, ['save_to' => $path
            ]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'kwitansi');
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
