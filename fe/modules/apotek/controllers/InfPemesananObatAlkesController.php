<?php
// Author : Ramdhan Nurrachman

namespace Doco\apotek\controllers;

use app\components\DHtml;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\components\access\BatalPemesananAccess as BatalPemesanan;

class InfPemesananObatAlkesController extends DocoController
{
    protected $_title = 'Informasi Pemesanan Obat Alkes Masuk';
    protected $_module = 'apotek/inf-pemesanan-obat-alkes/';
    protected $_restApotek; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
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
        $title = \Yii::t('fe', $this->_title);
        $response = $this->_restApotek->get('allow/get-api');
        $body = json_decode($response->getBody(), true);
        $resMaster = $body['response']['master'];

        $instalasi = ArrayHelper::map($resMaster['instalasi'], 'instalasi_id', 'instalasi_nama');
        $ruangan = [];
        foreach ($resMaster['ruangan'] as $row_ruangan) {
            $ruangan[$row_ruangan['ruangan_id']] = $instalasi[$row_ruangan['instalasi_id']] ." - ". $row_ruangan['ruangan_nama'] ;
        }

        $lookup_status = DocoConstants::STATUS_DISTRIBUSI;
        $userIdentity = Yii::$app->session->get('user_identity');
        $pegawaiId = ArrayHelper::getValue($userIdentity, 'id_pegawai');
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (!isset($yiiRestfulParams['advanced-filter']['tglpemesanan'])) {
            $yiiRestfulParams['advanced-filter']['tglpemesanan'] = date('d-M-Y', strtotime('-30 days'))." - ".date('d-M-Y');
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan = Yii::$app->docoVars->workspace("ruangan_id");
            $yiiRestfulParams['advanced-filter']['instalasi_id'] = $instalasi;
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan;
            $response = $this->_restApotek->get('inf-pemesanan-obat-alkes/get-data?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesanobatalkes_id']);
                $value['ruangan_pemesan'] = $value['instalasi_pemesan']." - ".$value['ruangan_pemesan'];
                $value['tglpemesanan'] = date("j M Y", strtotime($value['tglpemesanan']));
                $value['tglmutasioa'] = is_null($value['tglmutasioa']) ? "-" : date("j M Y", strtotime($value['tglmutasioa']));
                $value['tglterima'] = is_null($value['tglterima']) ? "-" : date("j M Y", strtotime($value['tglterima']));
                $value['reference'] = is_null($value['reference']) ? "-" : $value['reference'];

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

    public function actionCancel($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restApotek->get('inf-pemesanan-obat-alkes/cancel?id='.$id);
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

    public function actionGetDataPemesanan($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restApotek->get('inf-pemesanan-obat-alkes/get-no-pemesanan?nopemesanan='.$q);
            $body = json_decode($response->getBody(), True);
            $response = $body['response'];
            $result['results'][] = [
                'id' => $response['nopemesanan'],
                'text' => $response['nopemesanan']
            ];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try{
            $response = $this->_restApotek->post('inf-pemesanan-obat-alkes/delete-pemesanan', ['form_params'=>['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
    public function actionView($id)
    {
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restApotek->get('inf-pemesanan-obat-alkes/detail-pemesanan',['query'=>['id'=>$id]]);
        $ruangan_nama = Yii::$app->docoVars->workspace("ruangan_name");
        $body = json_decode($response->getBody(), true);
        $data = $body['response'];
        $data["tglpemesanan"] = date("d-M-Y", strtotime($data["tglpemesanan"]));
        $data["tglmintadikirim"] = date("d-M-Y", strtotime($data["tglmintadikirim"]));
        $title = 'Detail Pemesanan Obat Alkes';

        return $this->render('view', get_defined_vars());
    }

    public function actionDetail($id)
    {
        $title = DHtml::getTitleMenu();
        $response = $this->_restApotek->get('inf-pemesanan-obat-alkes/get-pemesanan',[
            'query' => [
                'id' => $id
            ]
        ]);
        $body = json_decode($response->getBody(), true);

        $data = $body['response'];
        $data['tglpemesanan'] = date('d M Y', strtotime($data['tglpemesanan']));
        $data['tglmutasioa'] = empty($data['tglmutasioa']) ? "-" :
            date('d M Y', strtotime($data['tglmutasioa']));
        $data['nomutasioa'] = empty($data['nomutasioa']) ? "-" : $data['nomutasioa'];
        $data['pengirim'] = empty($data['pengirim']) ? "-" : $data['pengirim'];
        $data['statuspesan'] = DocoHelpers::decrypt($data['statuspesan']);
        $data['status_verifikasi'] = DocoHelpers::decrypt($data['status_verifikasi']);
        $userIdentity = Yii::$app->session->get('user_identity');
        $pegawaiId = ArrayHelper::getValue($userIdentity, 'id_pegawai');
        $btn_toolbar = [
            'back',
            'pdf' => [
                'type' => 'link',
                'attributes' => [
                    'data-options' => 'link',
                    'class' => 'btn btn-info btn-labeled btn-xs data-print',
                    'id' => 'btn-print',
                    'url' => '/reports/viewer/detail-distribusi?id='.DocoHelpers::decrypt($id).'&uid='.$pegawaiId
                ]
            ]
        ];
        
        if((new BatalPemesanan)->check($data['statuspesan'], DocoConstants::BATAL_PESAN_TUJUAN)){
            $btn_toolbar['batal'] = [
                'type' => 'button',
                'title' => 'Batal Pesan',
                'icon' => 'fa fa-close',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'batal-pemesanan',
                    'data-url' => '/apotek/informasi-obat-alkes-keluar/batal-pemesanan?id='.$id
                ]
            ];
        }

        return $this->render('detail', compact('title', 'btn_toolbar', 'data'));
    }

    public function actionBatalPemesanan($id) {
        $id = DocoHelpers::decrypt($id);
        $url = 'inf-pemesanan-obat-alkes/batal-pemesanan';
        return (new BatalPemesanan)->batal($id, $url);
    }

    public function actionDataDetail($id)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['pesanobatalkes_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $response = $this->_restApotek->get('inf-pemesanan-obat-alkes/get-data-obat?'.http_build_query($yiiRestfulParams));
        $body = json_decode($response->getBody(), True);
        $no = $request->get('start',1);
        foreach ($body['response']['data'] as $key => $value) {
            $qty_konversi = $value['jumlah_pesan'] / ($value['qty_besar'] > 0 ? $value['qty_besar'] : 1);
            $qty_konversi = $qty_konversi > 0 ? $qty_konversi : 1;
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['pesanobatdetail_id']);
            $value['rowNum'] = $no; $value['primary'] = $primaryKey;
            $value['jumlah_mutasi'] = empty($value['jumlah_mutasi']) ? "-" : $value['jumlah_mutasi'] / $qty_konversi;
            $value['satuan_kirim'] = empty($value['satuan_kirim']) ? "-" : $value['satuan_besar'];
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }

    public function actionGetDataObat($id)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['pesanobatalkes_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-pemesanan-obat-alkes/get-data-obat?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $satuanBesarId = $value['satuanbesar_id'];
                $satuanKecilId = $value['satuankecil_id'];
                $satuanPesanId = $value['satuan_pemesanan'];
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $value['satuanbesar_nama'] = "";
                $value['satuanbesar_qty'] = "";
                $value['satuankecil_nama'] = "";
                $value['satuankecil_qty'] = "";
                $value['qty_pesan'] = ($satuanPesanId == $satuanBesarId) ? $value['qty_besar'] : $value['jumlah_pesan'];
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


    public function actionGetInstalasi($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/get-instalasi-by?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['instalasi_id'],
                    'name' => $value['instalasi_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetRuangan($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/get-ruangan'. $params);
            $body = json_decode($response->getBody(), True);

            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPemesanan()
    {
        try {

            if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
                $tgl_pemesanan = '';

                if (isset($_GET['z'])) {
                    $tgl_pemesanan = $_GET['z'];
                }
                $response = $this->_restApotek->request('POST', 'inf-pemesanan-obat-alkes/get-data-pemesanan',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'date' => $tgl_pemesanan],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = [
                        'data_ruangan' => $value['ruangan_id'],
                        'id' => $value['nopemesanan'],
                        'text' => $value['nopemesanan']
                    ];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionCetakDetail($id)
    {
        $path = Yii::getAlias("@download") . "/pemesanan-obat-alkes-masuk.pdf";
        try {
            $response = $this->_restApotek->post('inf-pemesanan-obat-alkes/cetak-detail-pemesanan-obat', [
                'query' => [
                    'id' => $id
                ],
                'form_params' => [],
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
