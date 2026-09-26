<?php 
// Author : Budi

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
// use app\modules\rm\models\PasienForm;
// use app\modules\rm\models\DokRekamMedisForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class InfPemakaianBarangController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/inf-pemakaian-barang/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Informasi Pemakaian Barang');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api_barang = $this->_restRm->get('barang/index');
        $barang = json_decode($api_barang->getBody(), True);
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
            $response = $this->_restRm->get('inf-pemakaian-barang/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                
                $primaryKey = DocoHelpers::encrypt($value['pemakaianbarangdetail_id']);
                $value['tgl_pemakaianbarang'] = DocoHelpers::display_label($value['tgl_pemakaianbarang'], true, 
                    date('d M Y', strtotime($value['tgl_pemakaianbarang'])));

                // $value['jeniskelamin'] = DocoHelpers::display_label(($value['jenis_kelamin'])); 
                // $value['umur'] = DocoHelpers::getUmur($value['tanggal_lahir'], true).' tahun';
                $value['aksi'] = Html::a(
                    '<i class="fa fa-pencil" aria-hidden="true"></i>', $this->_module.'update?id='.DocoHelpers::decrypt($primaryKey), [
                        'class' => 'btn btn-dark-turquise btn-xs',
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Edit'),
                    ]
                );

                $value['rowNum'] = $no;
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

    public function actionGetDataBarang()
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
            $response = $this->_restRm->get('barang/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['barang_nama'],
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);
                $value['rowNum'] = $no;
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

    public function actionSearch($tipe = NULL)
    {        

        return $this->renderPartial('search', get_defined_vars());
    }
}