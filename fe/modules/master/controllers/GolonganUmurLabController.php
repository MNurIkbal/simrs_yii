<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-12 14:56:40
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-17 14:57:54
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\GolonganUmurLabForm;

class GolonganUmurLabController extends DocoController
{
    protected $_title = "Golongan Umur Laboratorium";
    protected $_module = 'master/golongan-umur-lab/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
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
        $status = $this->_status; $options = $this->_options;
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
            $response = $this->_restMaster->get('golongan-umur-lab/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['golonganumurlab_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                unset($value['golonganumurlab_id']);

                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                $tahunMinimal = $this->convertToDay($value['gol_umurlab_minimal']);
                $tahunMaksimal = $this->convertToDay($value['gol_umurlab_maksimal']);
                $value['umur_minimal'] = $tahunMinimal['tahun'].' Tahun '.$tahunMinimal['bulan'].' Bulan '.$tahunMinimal['hari'].' Hari ';
                $value['umur_maksimal'] = $tahunMaksimal['tahun'].' Tahun '.$tahunMaksimal['bulan'].' Bulan '.$tahunMaksimal['hari'].' Hari ';
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

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Lihat').' '.\Yii::t('fe', $this->_title);
        $model = new GolonganOperasiForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('golongan-umur-lab/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new GolonganUmurLabForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            $post = $request->post('GolonganUmurLabForm');
            $convert_tahun_minimal = $post['tahun_minimal'] * 365;
            $convert_bulan_minimal = $post['bulan_minimal'] * 30;
            $convert_tahun_maksimal = $post['tahun_maksimal'] * 365;
            $convert_bulan_maksimal = $post['bulan_maksimal'] * 30;
            $total_minimal = $convert_tahun_minimal + $convert_bulan_minimal + $post['hari_minimal'];
            $total_maksimal = $convert_tahun_maksimal + $convert_bulan_maksimal + $post['hari_maksimal'];
            $model->attributes = $post;
            $model->gol_umurlab_minimal = ($total_minimal > 0) ? $total_minimal : '';
            $model->gol_umurlab_maksimal = ($total_maksimal > 0) ? $total_maksimal : '';
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('golongan-umur-lab/create', [
                        'form_params' => $model->attributes
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
            $model->is_active = 1;
            $arrTahun = $this->getTahun();
            $tahunMin = [];
            $bulanMin = [];
            $hariMin = [];
            $tahunMax = [];
            $bulanMax = [];
            $hariMax = [];
            
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new GolonganUmurLabForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            $post = $request->post('GolonganUmurLabForm');
            $convert_tahun_minimal = $post['tahun_minimal'] * 365;
            $convert_bulan_minimal = $post['bulan_minimal'] * 30;
            $convert_tahun_maksimal = $post['tahun_maksimal'] * 365;
            $convert_bulan_maksimal = $post['bulan_maksimal'] * 30;
            $total_minimal = $convert_tahun_minimal + $convert_bulan_minimal + $post['hari_minimal'];
            $total_maksimal = $convert_tahun_maksimal + $convert_bulan_maksimal + $post['hari_maksimal'];
            $model->attributes = $post;
            $model->gol_umurlab_minimal = $total_minimal;
            $model->gol_umurlab_maksimal = $total_maksimal;
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('golongan-umur-lab/update?id='.$id, [
                        'form_params' => $model->attributes
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
            $response = $this->_restMaster->get('golongan-umur-lab/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response']['query'];
            $model->attributes = $attributes;
            $arrTahun = $this->getTahun();

            $tahunMin = $body['response']['totalHariMin']['tahun'];
            $bulanMin = $body['response']['totalHariMin']['bulan'];
            $hariMin = $body['response']['totalHariMin']['hari'];
            $tahunMax = $body['response']['totalHariMaks']['tahun'];
            $bulanMax = $body['response']['totalHariMaks']['bulan'];
            $hariMax = $body['response']['totalHariMaks']['hari'];

            return $this->renderPartial('form', get_defined_vars());
        }
    }

    private function convertToDay($totalHari)
    {
        $response = $this->_restMaster->get('golongan-umur-lab/convert-to-day?totalHari='.$totalHari);
        $body = json_decode($response->getBody(), TRUE);

        return $body['response'];
    }

    private function getTahun()
    {
        $arrTahun = [];
        $arrBulan = [];
        $arrHari = [];
        for($i=0;$i<200;$i++) {
            $arrTahun[] = $i;
        }
        for($j=0;$j<13;$j++) {
            $arrBulan[] = $j;
        }
        for($k=0;$k<31;$k++) {
            $arrHari[] = $k;
        }
        $tahun = $arrTahun;
        $bulan = $arrBulan;
        $hari = $arrHari;

        return [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'hari' => $hari,
        ];
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('golongan-umur-lab/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", [
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->get('golongan-umur-lab/ubah-status', [
                'query' => [
                    'id' => $id,
                    'is_active' => $status
                ]
            ]);

            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
