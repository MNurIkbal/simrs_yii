<?php
// Author : Ardi Pratama

namespace Doco\laporan\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\laporan\models\SepuluhBesarPenyakitForm;
use GuzzleHttp\Exception\RequestException;

class SepuluhBesarPenyakitController extends DocoController
{
    protected $_title = "Laporan 10 Besar Penyakit";
    protected $_module = 'laporan/sepuluh-besar-penyakit/';
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
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
        // Init
        $response = $this->_restRm->get('ruangan/ajax-ruangan');
        $body = json_decode($response->getBody(), TRUE);
        $ruangan = $body['response']['data'];
        $ruangan = ArrayHelper::map($ruangan, 'ruangan_nama', 'ruangan_nama');

        $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/list-diagnosa');
        $body = json_decode($response->getBody(), TRUE);
        $diagnosa = $body['response']['data'];
        $diagnosa = ArrayHelper::map($diagnosa, 'diagnosa_kode', 'diagnosa_kode');
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        // $data = [[
        //             'rowNum'=>1,
        //             'dummy_tanggal'=>date('Y-m-d'),
        //             'dummy_norm'=>'00112200',
        //             'dummy_kamar'=>'Umum',
        //             'dummy_ruangan'=>'Basudewa',
        //             'dummy_nama'=>'Asep',
        //             'dummy_status' =>'Belum Dikirim',
        //             'dummy_jumlah' =>rand(5,5),
        //             'dummy_alamat' => 'Jalan Sukahaji No. 42',
        //             'dummy_kelamin' => 'Laki - laki',
        //             'dummy_umur' => '25',
        //             'dummy_jeniskasus' => 'Jantung',
        //             'dummy_kelaspelayanan' => 'Standar',
        //             'dummy_kodediagnosa' => 'A.001',
        //             'dummy_namadiganosa' => 'Broken Heart',
        //             'dummy_klasifikasidiagnosa' => 'Fraktur'
        //         ]];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['diagnosa_id']);
                unset($value['diagnosa_id']);

                $value['rowNum'] = $no;
                $value['jumlah_kasus'] = 1;
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
        $model = new PemesananBarangForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restRm->get('pemesanan-barang/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
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
}
