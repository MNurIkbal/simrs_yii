<?php

namespace Doco\gudang\controllers;

/**
* @author yaya
* @since 24 Jan 2019
*/

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\gudang\components\access\BatalPemesananBarangAccess as BatalPemesanan;

class InformasiPemesananBarangKeluarController extends DocoController
{
    public $_title = "Informasi Pemesanan Barang Keluar";
    protected $_module = '/gudang/informasi-pemesanan-barang-keluar';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_title = Yii::t("fe", $this->_title);
    }

    public function actionIndex()
    {
        $instalasi = $ruangan = [];
        $title = $this->_title;
        try {
            $response = $this->_restGudang->get('informasi-pemesanan-barang-keluar/get-fillter',[]);
            $response = json_decode($response->getBody(),true);
            $instalasi = $response['response']['instalasi'];
            $ruangan = $response['response']['ruangan'];
            $nopemesanan = $response['response']['nopemesanan'];
            $status = $response['response']['statusdistribusi'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['ruangan_pemesan_id'] = $ruangan_id;

        if(isset($filter['advanced-filter']['instalasi_ruangan_tujuan'])) {
            $filter['advanced-filter']['ruangantujuan_id'] = $filter['advanced-filter']['instalasi_ruangan_tujuan'];
        }

        $draw = $request->get('draw', 1);
        $data = [];
        
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('informasi-pemesanan-barang-keluar', [
                'query' => http_build_query($filter)
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesanbarang_id']);
                unset($value['pesanbarang_id']);
                $value['tgl_pesanbarang'] = $value['tgl_pesanbarang'] == null ? '-' : date("j M Y", strtotime($value['tgl_pesanbarang']));
                $value['tgl_mutasibarang'] = $value['tgl_mutasibarang'] == null ? '-' : date("j M Y", strtotime($value['tgl_mutasibarang']));
                $value['tglterima'] = $value['tglterima'] == null ? '-' : date("j M Y", strtotime($value['tglterima']));
                $value['instalasi_ruangan_tujuan'] = $value['instalasi_tujuan'] . ' - ' . $value['ruangan_tujuan'];
                $value['status_pengiriman'] = $value['status_pengiriman'] == '-' ? $value['status_distribusi'] : $value['status_pengiriman'];

                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionDelete($id)
    {

        try {
            $response = $this->_restGudang->delete('informasi-pemesanan-barang-keluar/delete',[
               'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],422);
        } 
    }

    public function actionGetRuangan($assign_id = "")
    {
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?instalasi_id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGudang->get('allow/get-ruangan'. $params);
            $body = json_decode($response->getBody(), True);
            
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],422);
        }
    }

    public function actionPreview($id)
    {
        $title = $this->_title;
        $data = (object) [];
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('informasi-pemesanan-barang-keluar/get-detail',[
                    'query' => [
                        'id' => $id
                    ]
                ]
            );
            $response = json_decode($response->getBody(),true);
            $data = (object) $response['response']['data'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }
        
        $encrypted_id = DocoHelpers::encrypt($id);
        $btn_toolbar = [
            'back',
            'pdf' => [
                'type' => 'link',
                'attributes' => [
                    'data-options' => 'link',
                    'class' => 'btn btn-info btn-labeled btn-xs data-print',
                    'id' => 'cetak-pdf',
                    'url' => '/gudang/informasi-pemesanan-barang-keluar/cetak-detail?id=' . $encrypted_id
                ]
            ]
        ];
        
        if((new BatalPemesanan)->check($data->statuspesan, DocoConstants::BATAL_PESAN_PEMESAN)){
            $btn_toolbar['batal'] = [
                'type' => 'button',
                'title' => 'Batal Pesan',
                'icon' => 'fa fa-close',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'batal-pemesanan',
                    'data-url' => '/gudang/informasi-pemesanan-barang-keluar/batal-pemesanan?id='.$encrypted_id
                ]
            ];
        }

        return $this->render('preview',get_defined_vars());
    }

    public function actionBatalPemesanan($id) {
        $id = DocoHelpers::decrypt($id);
        $url = 'informasi-pemesanan-barang-keluar/batal-pemesanan';
        return (new BatalPemesanan)->batal($id, $url);
    }

    public function actionGetDetail($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('informasi-pemesanan-barang-keluar/get-data-detail', [
                        'query' => $filter
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $data = [];

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $data[$key] = [
                    "rowNum" => $no,
                    "barang_nama" => isset($value["barang_nama"]) ? $value["barang_nama"] : " - ",
                    "qty_pesan" => (isset($value["qty_besar"]) && isset($value["satuan_besar"])) 
                            ?  $value["qty_besar"]." ".$value["satuan_besar"] : " - ",
                    "qty_konversi" => (isset($value["qty_kecil"]) && isset($value["satuan_kecil"])) 
                            ?  $value["qty_kecil"] . " " .$value["satuan_kecil"] : " - ",
                    "qty_terima" => (isset($value["jumlah_diterima"]) && isset($value["satuan_kirim"])) 
                            ?  $value["jumlah_diterima"]." ".$value["satuan_kirim"] : " - ",
                ];
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCetakDetail($id)
    {
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/informasi-pemesanan-obat-alkes.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            if(Yii::$app->report->enabled) {
                $urlReport = 'permintaan_barang';
                $query = [
                    'id' => $id
                ];
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query,
                    'manualRender'=>function() use($query,$path){
                        $response = $this->_restGudang->post('informasi-pemesanan-barang-keluar/cetak-pdf',[
                            'query' => $query,
                            'save_to' => $path
                        ]);
    
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = $this->_restGudang->post('informasi-pemesanan-barang-keluar/cetak-pdf', [
                'query' => [
                    'id' => $id
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPenerimaan($id)
    {
        try {
            $title = "Penerimaan Barang";
            $pesanbarang_id = DocoHelpers::decrypt($id);
            $mutasi = $this->_restGudang->request('GET', 'informasi-pemesanan-barang-keluar/get-detail', [
                            'query' => ['id' => $pesanbarang_id ]
                        ]);
            $body = json_decode($mutasi->getBody(), True);
            $response = $body['response'];
            
            $request = Yii::$app->request;
            if ($request->isPost) {
                
                $response = $this->_restGudang->post('informasi-pemesanan-barang-keluar/proses-terima', [
                    'form_params' => [
                        'pesanbarang_id' => $pesanbarang_id
                    ]
                ]);

                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response);
                
            } else {
                return $this->render('penerimaan',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDetailPenerimaan($id)
    {
        $request = Yii::$app->request;

        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['id'] = DocoHelpers::decrypt($id);
        $draw = $request->get('draw', 1);
        $result = [];
        $data = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try{
            $title = "Penerimaan Barang";
            $mutasi = $this->_restGudang->request('GET', 'informasi-pemesanan-barang-keluar/get-data-detail', [
                            'query' => http_build_query($filter)
                        ]);
            $body = json_decode($mutasi->getBody(), True);

            $response_data = ArrayHelper::getValue($body,'response.data',[]);
            $no=0;
            foreach ($response_data as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }   
    }
}
