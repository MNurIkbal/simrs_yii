<?php
// Author : Budi

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\modules\gudang\models\TerimaMutasiBarangForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class InfMutasiBarangController extends DocoController
{
    protected $_title = "Informasi Mutasi Barang";
    protected $_module = 'gudang/inf-mutasi-barang/';
    protected $_restGudang; 
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang; 
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';
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
        
        $response = $this->_restMaster->get('allow/list-instalasi');
        $body = json_decode($response->getBody(), TRUE);
        $instalasi = $body['response']['data'];
        $module = $this->_module;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $ruangan_id = $this->_ruangan_id;
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

        $yiiRestfulParams['advanced-filter']['ruangan_tujuan_id'] = $ruangan_id;

        try {
            $response = $this->_restGudang->get('inf-mutasi-barang/index?ruangan_tujuan_id='.$ruangan_id.'&'. http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['mutasibarang_id']);
                unset($value['mutasibarang_id']);
                $value['tgl_mutasibarang'] = date("j M Y", strtotime($value['tgl_mutasibarang']));
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
            $response = $this->_restGudang->get('inf-mutasi-barang/cancel?id='.$id);
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

    public function actionDetail($id)
    {
        $title = 'Detail Mutasi Barang';
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restGudang->request('GET', 'inf-mutasi-barang/view', [
                            'query' => ['id' => $id ]
                        ]);

            $response = json_decode($response->getBody(),true);
            $response['response']['tgl_pesanbarang'] = date('d M Y', strtotime($response['response']['tgl_pesanbarang']));
            $response['response']['tgl_mutasibarang'] = date('d M Y', strtotime($response['response']['tgl_mutasibarang']));
            $data = (object) $response['response'];
            return $this->render('view', get_defined_vars());
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionGetDataDetail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if(!isset($yiiRestfulParams['advanced-filter']['ruanganpemesan_id'])){
            $yiiRestfulParams['advanced-filter']['ruanganpemesan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }  
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('inf-mutasi-barang/detail?id=' . $id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['mutasibarangdetail_id']);
                unset($value['mutasibarangdetail_id']);
                $value['qty_pesan'] = $value['qty_dipesan'] . ' ' . $value['satuanbesar_nama'];
                $value['qty_konversi'] = $value['qty_mutasi'] . ' ' . $value['satuankecil_nama'];
                $value['qty_mutasi'] = $value['qty_input'] . ' ' . $value['satuanbesar_nama'];
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
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

    private function getKonversi($satuan_besar, $satuan_kecil, $qty_mutasi)
    {
        $response = $this->_restGudang->get('inf-mutasi-barang/get-konversi?satuanbesar_id=' . $satuan_besar . '&satuankecil_id=' . $satuan_kecil . '&qty_mutasi=' . $qty_mutasi, ['form_params' => []]);

        $body = json_decode($response->getBody(), True);

        return $body['response'];
    }

    public function actionPenerimaan($id)
    {
        try {
            $title = "Penerimaan Barang";
            $url_search = '/'.$this->_module.'pegawai-mengetahui';
            $url_search2 = '/'.$this->_module.'pegawai-menyetujui';
            $model = new TerimaMutasiBarangForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $id = DocoHelpers::decrypt($id);
            $mutasi = $this->_restGudang->request('GET', 'inf-mutasi-barang/penerimaan', [
                            'query' => ['id' => $id ]
                        ]);
            $body = json_decode($mutasi->getBody(), True);
            $response = $body['response'];
            
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post('TerimaMutasiBarangForm');
                $model->attributes = $post;
                
                if ($model->validate()) {
                    $response = $this->_restGudang->post('inf-mutasi-barang/create', [
                        'form_params' => $post,
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,$formName);
                } else {
                    return DocoHelpers::response($model->errors,422,$formName);
                }
            } else {
                return $this->render('penerimaan',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPegawaiMengetahui()
    {
        $title = Yii::t('fe', 'Pegawai');
        $ruangan_id = $this->_ruangan_id;
        return $this->renderPartial('search', get_defined_vars());
    }

    public function actionPegawaiMenyetujui()
    {
        $title = Yii::t('fe', 'Pegawai');
        $ruangan_id = $this->_ruangan_id;
        return $this->renderPartial('search2', get_defined_vars());
    }

    public function actionGetDataPegawai()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restGudang->request('POST', 'pegawai/index',[
            'form_params' => $post
        ]);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);
                $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                    'class' => 'btn btn-success btn-xs select-pegawai',
                    'data-pegawai' => $value['pegawai_id'],
                    'data-tooltip' => 'tooltip',
                    'title' => Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
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

    public function actionGetDataPegawai2()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restGudang->request('POST', 'pegawai/index',[
            'form_params' => $post
        ]);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);
                $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                    'class' => 'btn btn-success btn-xs select-pegawai2',
                    'data-pegawai2' => $value['pegawai_id'],
                    'data-tooltip' => 'tooltip',
                    'title' => Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
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

    public function actionPrint($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-mutasi-barang.pdf";
        try {
            $post = $request->post();
            $response = $this->_restGudang
                        ->post('inf-mutasi-barang/print',
                        [
                            'query' => ['id'=>$id],
                            'form_params' => $post,
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
