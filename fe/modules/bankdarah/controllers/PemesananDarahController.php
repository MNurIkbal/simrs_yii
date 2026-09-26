<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2019-01-08 16:42:20
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-02-23 14:19:59
 */
namespace Doco\bankdarah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use app\modules\bankdarah\models\PesanDarahPmiForm;
use app\modules\bankdarah\models\PesanDarahPmiDetailForm;

class PemesananDarahController extends DocoController
{
	protected $_title;
    protected $_restBankDarah;
    protected $_restMaster;
    protected $_module = '/bankdarah/pemesanan-darah/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Pemesanan Darah PMI');
        $this->_restBankDarah = Yii::$app->docoRest->bankdarah;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $model = new PesanDarahPmiForm;
        $modelDetail = new PesanDarahPmiDetailForm;

        $modelDetail->rhesus = true;
        $dataRequest = $this->getRequest();
        $modelDetail->qty_pesan = 1;
        return $this->render('index', get_defined_vars());
    }

    private function getRequest($jenisdarah_id = null, $golongandarah_id = null)
    {
        try {
            $request = $this->_restBankDarah->request('GET', 'pemesanan-darah/get-data-request', [
                'query' => [
                    'jenisdarah_id' => $jenisdarah_id,
                    'golongandarah_id' => $golongandarah_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $result = $response['response'];

            return $result;
        } catch (Exception $e) {
            $result['pmi'] = [];
            $result['jenis_darah'] = [];
            $result['gol_darah'] = [];
            return $result;
        } catch (RequestException $e) {
            $result['pmi'] = [];
            $result['jenis_darah'] = [];
            $result['gol_darah'] = [];
            return $result;
        }
    }

    public function actionGetDataPmi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        try {
            $request = Yii::$app->request;
            $supplier_id = $request->get('supplier_id');
            $request = $this->_restBankDarah->request('GET', 'pemesanan-darah/get-data-pmi', [
                'query' => [
                    'supplier_id' => $supplier_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $result = $response['response'];

            return $result;
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
    }

    public function actionSetListItem()
    {
        $request = Yii::$app->request;
        $model = new PesanDarahPmiDetailForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        if ($model->validate()) {
            $dataDetail = $request->post($formName);
            $userLogin = Yii::$app->user->identity->loginpemakai_id;
            $dataTransaksi = $this->getRequest($dataDetail['jenisdarah_id'], $dataDetail['golongandarah_id']);
            $cachePmi = Yii::$app->cache->get("pemesanan-darah-pmi-{$userLogin}");
            if ($cachePmi == false) {
                Yii::$app->cache->set("pemesanan-darah-pmi-{$userLogin}",[]);
                $cachePmi = [];
            }

            $keyUnique = $model->jenisdarah_id."-".$model->golongandarah_id.'-'.$model->rhesus.'-'.$model->tgl_mintakirim.'-'.$model->wkt_mintakirim;

            $keyUnique = DocoHelpers::encrypt($keyUnique);
            if (!isset($cachePmi[$keyUnique])) {
                $cachePmi[$keyUnique] = [];
            }
            
            $cachePmi[$keyUnique] = [
                'primaryKey' => $keyUnique,
                'jenisdarah_nama' => $dataTransaksi['dataJenisDarah']['jenisdarah_nama'],
                'jenisdarah_id' => $dataTransaksi['dataJenisDarah']['jenisdarah_id'],
                'golongandarah_id' => $dataTransaksi['dataGolDar']['lookup_id'],
                'golongandarah_nama' => $dataTransaksi['dataGolDar']['lookup_name'],
                'rhesus' => ($dataDetail['rhesus'] == 1) ? "Positif" : "Negatif",
                'tgl_mintakirim' => date('d-M-Y', strtotime($dataDetail['tgl_mintakirim'])),
                'wkt_mintakirim' => $dataDetail['wkt_mintakirim'],
                'jumlah' => $dataDetail['qty_pesan'],
                'harga' => $dataTransaksi['dataJenisDarah']['harga'],
                'subTotal' => $dataDetail['qty_pesan'] * $dataTransaksi['dataJenisDarah']['harga'],
            ];
            
            $cachePmi = Yii::$app->cache->set("pemesanan-darah-pmi-{$userLogin}",$cachePmi,3600);
            
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionGetListItem()
    {
        $request = Yii::$app->request;
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cachePmi = Yii::$app->cache->get("pemesanan-darah-pmi-{$userLogin}");
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cachePmi !== false) {
            $no = $request->get('start',1);
            foreach ($cachePmi as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $subTotal2 = $value['subTotal'];
                $data[] = [
                    'rowNum' => $no,
                    'jenisdarah_nama' => $value['jenisdarah_nama'],
                    'golongandarah_nama' => $value['golongandarah_nama'],
                    'rhesus' => $value['rhesus'],
                    'tgl_mintakirim' => $value['tgl_mintakirim'].' '.$value['wkt_mintakirim'],
                    'wkt_mintakirim' => $value['wkt_mintakirim'],
                    'jumlah' => $value['jumlah'],
                    'harga' => DocoHelpers::formatNumber($value['harga']),
                    'subTotal' => DocoHelpers::formatNumber($value['subTotal']),
                    'subTotal2' => $subTotal2,
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'class' => 'btn btn-danger btn-sm delete',
                            'action' => Url::to([$this->_module .'delete-list-item','id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
        }

        return DocoHelpers::response($result);
    }

    public function actionDeleteListItem($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cachePmi = Yii::$app->cache->get("pemesanan-darah-pmi-{$userLogin}");
        if ($cachePmi !== false) {
            if (isset($cachePmi[$id])) {
                unset($cachePmi[$id]);
                Yii::$app->cache->set("pemesanan-darah-pmi-{$userLogin}",$cachePmi);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionDeleteAllListItem()
    {
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cachePmi = Yii::$app->cache->get("pemesanan-darah-pmi-{$userLogin}");
        Yii::$app->cache->delete("pemesanan-darah-pmi-{$userLogin}");

        $response['response'] = [];
        return DocoHelpers::response($response);
    }

    public function actionSave()
    {
        $cache = Yii::$app->cache;
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cachePmi = $cache->get("pemesanan-darah-pmi-{$userLogin}");
        $response['response'] = [];
        $newCache = [];
        $codeHttp = 422;
        $model = new PesanDarahPmiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $post = Yii::$app->request->post();
        $model->supplier_id = $post['supplier_id'];
        $model->total_harga = $post['total_harga'];
        $model->total_kantongdarah = $post['total_kantongdarah'];
        if($model->validate()) {
            if ($cachePmi) {
                try {
                    $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
                    $supplier_id = Yii::$app->request->post("supplier_id");
                    $total_harga = Yii::$app->request->post("total_harga");
                    $total_kantongdarah = Yii::$app->request->post("total_kantongdarah");
                    $newCache = [
                        'supplier_id' => $supplier_id,
                        'ruangan_id' => $ruangan_id,
                        'total_harga' => $total_harga,
                        'total_kantongdarah' => $total_kantongdarah,
                        'data' => json_encode($cachePmi)
                    ];
                    $result = $this->_restBankDarah->post('pemesanan-darah/save',[
                        'form_params' => $newCache
                    ]);
                    $result = json_decode($result->getBody(),true);
                    // return DocoHelpers::response($result);
                    // dump($result);die;
                    if($result['metadata']['status'] == 200) {
                        $cache->delete("pemesanan-darah-pmi-{$userLogin}");
                    }
                    $response['response'] = $result;
                    $codeHttp = 200;
                    $idParent = isset($result['response']['id_parent']) 
                                    ? $result['response']['id_parent'] 
                                    : null;

                    $no_pesandarahpmi = isset($result['response']['no_pesandarahpmi']) 
                                    ? $result['response']['no_pesandarahpmi'] 
                                    : null;

                    $newCache['id_parent'] = DocoHelpers::encrypt($idParent);
                    $response['response'] = [
                        'text' => 'Pemesanan Darah PMI berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'id_parent' => $idParent,
                        'no_pesandarahpmi' => $no_pesandarahpmi
                    ];
                } catch (RequestException $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalah pada sistem';
                    $response['response']['messageError'] = $e->getMessage();
                }
            }
        }
        else {
            $errors = DocoHelpers::parseError($model->errors,'PesanDarahPmiForm');
            return DocoHelpers::response([
                'response' => [
                    'data' => $errors
                ]
            ],422);
        }
        
        return DocoHelpers::response($response,$codeHttp);
    }

    public function actionCetak($no_pesandarahpmi)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $namaRs = Yii::$app->docoVars->identity("nama_rumahsakit");
            $alamatRs = Yii::$app->docoVars->identity("alamatlokasi_rumahsakit");
            $path = Yii::getAlias("@download") . "/pemesanan-darah.pdf";
            $response = $this->_restBankDarah->get('pemesanan-darah/export-pdf?no_pesandarahpmi='.$no_pesandarahpmi.'&nama_rs='.$namaRs.'&alamat_rs='.$alamatRs,
            [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}