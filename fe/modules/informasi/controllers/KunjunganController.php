<?php

/**
 * @author : Budi
 * @description : Controller Informasi Kunjungan Pasien
 * @date : 5 Januari 2018 
 */

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

class KunjunganController extends DocoController
{
	const INSTALASI_RAWAT_JALAN = 1;
    const INSTALASI_RAWAT_DARURAT = 2;
    const INSTALASI_RAWAT_INAP = 3;
    const INSTALASI_PENUNJANG = [4,5,8];
    
    protected $_title;
    protected $_title_rd;
    protected $_title_ranap;
    protected $restInformasi;

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Informasi pasien rajal');
        $this->_title_rd = Yii::t('fe', 'Informasi pasien rawat darurat');
        $this->_title_ranap = Yii::t('fe', 'Informasi pasien rawat inap');
        $this->restInformasi = Yii::$app->docoRest->informasi;
    }

    public function actionRawatJalan()
    {
    	$title = $this->_title;
        
        $api_dokter = $this->restInformasi->get('antrian/list-dokter');
        $dokter = json_decode($api_dokter->getBody(), True);

        $api_ruangan = $this->restInformasi->get('antrian/list-ruangan', [
            'query' => [
                'penunjang' => false, 
                'instalasi' => self::INSTALASI_RAWAT_JALAN
            ]
        ]);

        $ruangan = json_decode($api_ruangan->getBody(), True);

        return $this->render('rajal', get_defined_vars());
    }

    public function actionRawatDarurat()
    {
        $title = $this->_title_rd;
        
        $api_dokter = $this->restInformasi->get('antrian/list-dokter');
        $dokter = json_decode($api_dokter->getBody(), True);

        $api_ruangan = $this->restInformasi->get('antrian/list-ruangan', [
            'query' => [
                'penunjang' => false, 
                'instalasi' => self::INSTALASI_RAWAT_DARURAT
            ]
        ]);

        $ruangan = json_decode($api_ruangan->getBody(), True);
        
        $api_status_periksa = $this->restInformasi->get('antrian/list-status-periksa');
        $status_periksa = json_decode($api_status_periksa->getBody(), True);

        return $this->render('rawat_darurat', get_defined_vars());
    }

    public function actionRawatInap()
    {
        $title = $this->_title_ranap;
        
        $api_dokter = $this->restInformasi->get('antrian/list-dokter');
        $dokter = json_decode($api_dokter->getBody(), True);

        $api_ruangan = $this->restInformasi->get('antrian/list-ruangan', [
            'query' => [
                'penunjang' => false, 
                'instalasi' => self::INSTALASI_RAWAT_INAP
            ]
        ]);

        $ruangan = json_decode($api_ruangan->getBody(), True);
        
        $api_kamar = $this->restInformasi->get('kunjungan/list-kamar');
        $kamar = json_decode($api_kamar->getBody(), True);

        $api_status_periksa = $this->restInformasi->get('antrian/list-status-periksa');
        $status_periksa = json_decode($api_status_periksa->getBody(), True);

        return $this->render('rawat_inap', get_defined_vars());
    }

    public function actionGetData($instalasi = NULL)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $row = [];

            if($instalasi == 'rj') {
                $response = $this->restInformasi->post('kunjungan/rawat-jalan', [
                        'form_params' => $post
                ]);
            } elseif($instalasi == 'ri') {
                $response = $this->restInformasi->post('kunjungan/rawat-inap', [
                        'form_params' => $post
                ]);
            } else {
                $response = $this->restInformasi->post('kunjungan/rawat-darurat', [
                        'form_params' => $post
                ]);
            }
            
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->post('start',1);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                
                if($instalasi == 'ri') {
                    $value['ruangan_nama'] = $value['ruangan_nama'].' / '.$value['kamarruangan_nokamar'].' / '.$value['no_tempattidur'];
                }

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

    public function actionGetKamar()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'][0])) {
            $ruangan = (int) $_POST['depdrop_parents'][0];
            if ($ruangan != null) {
                $api_kamar = $this->restInformasi->get('kunjungan/get-kamar', ['query' => ['ruangan_id' => $ruangan]]);
                $kamar = json_decode($api_kamar->getBody(), True);
                foreach ($kamar['response'] as $key => $value) {
                    $out[] = ['id' => $value['kamarruangan_id'], 'name' => $value['kamarruangan_nokamar']];
                }
                echo json_encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionGetBed()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'][0])) {
            $ids = $_POST['depdrop_parents'];
            $ruangan = empty($ids[0]) ? null : $ids[0];
            $kamar = empty($ids[1]) ? null : $ids[1];

            if ($kamar != null) {
               $api_bed = $this->restInformasi->get('kunjungan/get-bed', ['query' => ['parent_id' => $kamar]]);
               $bed = json_decode($api_bed->getBody(), True);
               foreach ($bed['response'] as $key => $value) {
                    $out[] = ['id' => $value['no_tempattidur'], 'name' => $value['no_tempattidur']];
                }
               echo json_encode(['output' => $out, 'selected' => '']);
               return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }
}