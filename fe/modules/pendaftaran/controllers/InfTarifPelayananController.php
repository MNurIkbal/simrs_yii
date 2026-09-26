<?php
/**
 * @author: arief saputra
 * @description: master layar antrian
**/

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rajal\models\InfoTarifPelayananForm;
use GuzzleHttp\Exception\RequestException;

class InfTarifPelayananController extends DocoController
{
    protected $_module = 'inf-tarif-pelayanan/';
    protected $_title = 'Informasi Tarif Pelayanan';
    protected $_restMaster;
    protected $_restPendaftaran;
    protected $_restRajal;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
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
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);
        try{
            $getListFilter = $this->_restPendaftaran->get('allow/list-filter-tarif-pelayanan');
            $dataFilter = json_decode($getListFilter->getBody(),TRUE)['response'];

        } catch (RequestException $e) {
            $dataFilter['instalasi'] = [];
            $dataFilter['ruangan'] = [];
            $dataFilter['penjamin'] = [];
            $dataFilter['kategori_tindakan'] = [];
            $dataFilter['kelas_pelayanan'] = [];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $number = $request->get('start',1);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-tarif-pelayanan/index?'.http_build_query($yiiRestfulParams), 
                                                    ['form_params' => []
                                                ]);
            $body = json_decode($response->getBody(), True);
            $dataResponse = $body['response']['data'];
            foreach ($dataResponse as $key => $value) {
                $number++;

                $primary = json_encode([$value['tariftindakan_id'], $value['ruangan_id'],  $value['daftartindakan_id'] ]);
                $primaryKey = DocoHelpers::encrypt($primary);
                $value['primary'] = $primaryKey;
                $value['harga_tariftindakan'] = str_replace('Rp.', '', DocoHelpers::rupiahDisplay($value['harga_tariftindakan']) );
                $value['instalasi_ruangan'] = $value['instalasi_nama'].'<br>'.$value['ruangan_nama'];
                $value['kelompok_kategori_tindakan'] = $value['kelompoktindakan_nama'].'<br>'.$value['kategoritindakan_nama'];
                $value['daftartindakan_nama_kelaspelayanan_nama'] = $value['daftartindakan_nama'].'<br>'.$value['kelaspelayanan_nama'];
                $value['komponen_tarif'] = 'LINKS';
                $value['rowNum'] = $number;

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

    public function actionDetail($id)
    {
        $id = json_decode(DocoHelpers::decrypt($id));
        $tariftindakan_id = $id[0];
        $ruangan_id = $id[1];
        $daftartindakan_id = $id[2];
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Detail Komponen Tarif');
        try {
            $response = $this->_restPendaftaran->get('inf-tarif-pelayanan/get-komponen-tarif',[ 
                'query' => [
                    'tariftindakan_id' => $tariftindakan_id,
                    'ruangan_id' => $ruangan_id,
                    'daftartindakan_id' => $daftartindakan_id,
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            $header = $body['response']['header'];
            $data = $body['response']['data'];
        } catch (Exception $e) {
            $header = [];
            $data = [];
        }
        return $this->renderAjax('detail', get_defined_vars());
    }
}
