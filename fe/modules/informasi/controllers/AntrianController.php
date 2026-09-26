<?php

namespace Doco\informasi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

/**
 * Default controller for the `informasi` module
 */
class AntrianController extends DocoController
{
    const INSTALASI_RAWAT_JALAN = 1;
    const INSTALASI_RAWAT_DARURAT = 2;
    const INSTALASI_PENUNJANG = [4,5,8];

    protected $_title_penunjang;
    protected $_title_poliklinik;
    protected $_module = '/informasi/antrian';
    protected $restInformasi;

    public function init()
    {
        parent::init();

        $this->_title_penunjang = Yii::t('fe', 'Informasi antrian penunjang');
        $this->_title_poliklinik = Yii::t('fe', 'Informasi antrian poliklinik');
        $this->restInformasi = Yii::$app->docoRest->informasi;
    }

    public function actionPenunjang()
    {
        $title = $this->_title_penunjang;
        
        $api_dokter = $this->restInformasi->get('antrian/list-dokter');
        $dokter = json_decode($api_dokter->getBody(), True);

        $api_ruangan = $this->restInformasi->get('antrian/list-ruangan', [
            'query' => [
                'penunjang' => true, 
            ]
        ]);

        $ruangan = json_decode($api_ruangan->getBody(), True);

        $api_status_periksa = $this->restInformasi->get('antrian/list-status-periksa');
        $status_periksa = json_decode($api_status_periksa->getBody(), True);
        
        return $this->render('penunjang', get_defined_vars());
    }

    public function actionPoliklinik()
    {
        $title = $this->_title_poliklinik;
        
        $api_dokter = $this->restInformasi->get('antrian/list-dokter');
        $dokter = json_decode($api_dokter->getBody(), True);

        $api_ruangan = $this->restInformasi->get('antrian/list-ruangan', [
            'query' => [
                'penunjang' => false, 
                'instalasi' => self::INSTALASI_RAWAT_JALAN
            ]
        ]);

        $ruangan = json_decode($api_ruangan->getBody(), True);

        $api_status_periksa = $this->restInformasi->get('antrian/list-status-periksa');
        $status_periksa = json_decode($api_status_periksa->getBody(), True);
        
        return $this->render('poliklinik', get_defined_vars());
    }

    public function actionGetDataPenunjang()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $row = [];

            $response = $this->restInformasi->request('POST', 'antrian/penunjang', [
                        'form_params' => $post
                    ]);
        
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->post('start',1);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                // $value['rowNum'] = $no;
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataPoliklinik()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $row = [];

            $response = $this->restInformasi->request('POST', 'antrian/poliklinik',[
                        'form_params' => $post
                    ]);
        
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->post('start',1);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                // $value['rowNum'] = $no;
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
