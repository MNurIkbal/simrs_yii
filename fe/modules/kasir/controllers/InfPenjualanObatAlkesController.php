<?php
// Author : Budi

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;

class InfPenjualanObatAlkesController extends DocoController
{
    protected $_title = "Informasi Penjualan Obat Alkes";
    protected $_module = 'kasir/inf-penjualan-obat-alkes/';
    protected $_restKasir;
    protected $_restMaster;

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
        $title = $this->_title;
        $jenis_penjualan = [];
        try {
            $response = $this->_restKasir->get('penjualan-resep/get-options');
            $body = json_decode($response->getBody(), true);
            $cara_bayar = isset($body['response']['cara_bayar']) ? $body['response']['cara_bayar'] : [];
            $penjamin = isset($body['response']['penjamin']) ? $body['response']['penjamin'] : '';
            $jenis_penjualan = DocoConstants::$optJenisResep;
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }

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
        $advancedFilter = $request->get('advancedFilter', []);
        if (!empty($advancedFilter)) {
            $is_kronis = array_merge($yiiRestfulParams['advanced-filter'], $advancedFilter);
            $yiiRestfulParams['advanced-filter'] = $is_kronis;
        }
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('penjualan-resep/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = $value['penjualanresep_id'];
                if (is_null($value['pendaftaran_id'])) {
                    $value['tipe'] = 'pasien_bebas';
                }else{
                    // $primaryKey = $value['pendaftaran_id'];
                    $value['tipe'] = 'pasien_rs';
                }

                $value['tglpenjualan'] = date("j-M-Y", strtotime($value['tglpenjualan']));
                $value['totalharga_jual'] = DocoHelpers::rupiahDisplay($value['totalharga_jual']);

                $value['rowNum'] = $no;
                $value['primary'] = DocoHelpers::encrypt($primaryKey);
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

    public function actionView($id,$status = null, $tipe)
    {
        $roleBtnCloseBill = DHtml::cekHakAkses('close-bill');
        $roleBtnCloseBill = ($roleBtnCloseBill) ? 1 : 0;
        if($tipe == 'pasien_bebas') {
            $roleBtnCloseBill = 0;
        }
        
        return Yii::$app->runAction('/kasir/pembayaran-tagihan',[
            'id' => $id,
            'kelompok' => DocoConstants::PASIEN_ALKES,
            'status' => $status,
            'tipe_pasien' => $tipe,
            'roleBtnCloseBill' => $roleBtnCloseBill,
        ]);
    }

    public function actionCancel($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restKasir->get('inf-penjualan-obat-alkes/cancel?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}