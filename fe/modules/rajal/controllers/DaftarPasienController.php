<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-26 10:44:11 
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-26 15:49:04
 * @Description: 
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rajal\models\PendaftaranForm;

class DaftarPasienController extends DocoController
{
    protected $_title = "Rajal :: Laporan daftar pasien rawat jalan";
    protected $_module = '/rajal';
    protected $_controller = '/rajal/daftar-pasien';
    protected $_page;
    protected $_restRajal;
    protected $_id_ruangan;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_page = Yii::t('fe', 'Daftar pasien rawat jalan');
    }

    public function actionIndex()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $sub_title = $this->_page;
        $status = $this->_status;

        // data select
        $list_data = $this->getListData();
        $data_pegawai = $list_data["data_pegawai"];
        $data_penjamin = $list_data["data_penjamin"];
        $data_carabayar = $list_data["data_carabayar"];

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
            $response = $this->_restRajal->get('lap-daftar-pasien/index?ruangan_id='.$this->_id_ruangan.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     *
     * private function
     *
     */

    private function getListData()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('lap-daftar-pasien/get-list-data?ruangan_id='.$this->_id_ruangan);
            $body = json_decode($response->getBody(),TRUE);

            $data_carabayar = empty($body['response']['data-carabayar']) ? [] : $body['response']['data-carabayar'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];

            $result = [
                'data_carabayar' => $data_carabayar,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
            ];

            return $result;
        } catch (RequestException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}