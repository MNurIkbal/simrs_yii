<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Mutasi
 * @copyright 17 January 2018 aweutist
 * last edited by Rizqi Fitrianto
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;
use Doco\apotek\models\PenerimaanObatForm;
use Doco\apotek\components\traits\MutasiKeluarTrait;

class InformasiMutasiController extends DocoController
{
    use MutasiKeluarTrait;
    protected $_title = "Informasi Mutasi Obat Alkes";
    protected $_module = '/apotek/informasi-retur';
    protected $_restApotek;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_title = Yii::t("fe", "Informasi Mutasi Obat Alkes");

    }

    public function actions() {
        return [
            'penerimaan' => 'Doco\apotek\actions\InformasiMutasi\PenerimaanAction',
            'penerimaan-pesanan' => 'Doco\apotek\actions\InformasiMutasi\PenerimaanPesananAction',
            'detail-penerimaan' => 'Doco\apotek\actions\InformasiMutasi\DetailPenerimaanAction',
            'detail-penerimaan-pesanan' => 'Doco\apotek\actions\InformasiMutasi\DetailPenerimaanPesananAction'
        ];
    }

    public function actionObatAlkes()
    {
        $title = Yii::t("fe", "Informasi Mutasi Obat Alkes Masuk");
        $model = new InformasiForm;
        try {

            $response = $this->_restApotek->get('inf-mutasi-obatalkes/get-api');
            $body = json_decode($response->getBody(), TRUE);
            $instalasi = $body['response']['instalasi'];
            $ruangan = $body['response']['ruangan'];
        } catch (Exception $e) {
            $instalasi = [];
            $ruangan = [];
        }

        return $this->render('obat-alkes', get_defined_vars());
    }
    //fungsi buat ambil data informasi mutasi obat alkes
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_tujuan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restApotek->get('inf-mutasi-obatalkes/index?'.http_build_query($yiiRestfulParams),['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $value['primary'] = $primaryKey;
                $value['type'] = DocoHelpers::encrypt('masuk');
                unset($value['mutasiobatruangan_id']);
                $value['tglmutasioa'] = date("j M Y", strtotime($value['tglmutasioa']));

                $value['rowNum'] = $no;
                $value['tglmutasioa'] = date('d-M-Y',strtotime($value['tglmutasioa']));
                $value['tgl_terima'] = empty($value['tgl_terima']) ? "-" : date('d-M-Y',strtotime($value['tgl_terima']));
                $value['instalasi_ruangan'] = $value['instalasi_asal'] ." - ". $value['ruangan_asal'];
                $value['reference'] = empty($value['reference']) ? "-" : $value['reference'];
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
    //fungsi buat ambil data pegawai terus disimpen di modal
    public function actionGetDataPegawai()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];

        try {
            $response = $this->_restApotek->get('inf-mutasi-obatalkes/get-data-pegawai?'.http_build_query($yiiRestfulParams),['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);
                $value['primary'] = $primaryKey;
                unset($value['pegawai_id']);
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $primaryKey,
                    'data-key'=> $primaryKey,
                    'data-label'=>$value['nama_pegawai'],
                    'title' => \Yii::t('fe', 'Klik'),
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

    public function actionDetail($id)
    {
        $title = 'Detail Mutasi Obat Alkes';
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $typedec = isset($_GET['type']) ? DocoHelpers::decrypt($_GET['type']) : 'masuk';
        $header = $detail = [];
        if($typedec == 'masuk'):
            $breadcrumb = "Informasi Mutasi Obat Alkes Masuk";
        else:
            $breadcrumb = "Informasi Mutasi Obat Alkes Keluar";
        endif;
        try {
            $response = $this->_restApotek->get('inf-mutasi-obatalkes/detail', ['query'=>['mutasiobatruangan_id' => $id]]);

            $response = json_decode($response->getBody(), true);
            $header = $response['response']['header'];
            $detail = $response['response']['detail'];

            $data = [];
            $data_obatalkes = [];
            $no = 1;
            foreach ($detail as $key => $value) {
                $data['rowNum'] = $no;
                if(!empty($value['satuanbesar_id'])) {
                    $satuanbesar_id = $value['satuanbesar_id'];
                } else {
                    $satuanbesar_id = $value['satuanmutasi_id'];
                }
                $konversi = $this->actionGetKonversi($satuanbesar_id, $value['satuankecil_id']);

                $data['obatalkes_nama'] = $value['obatalkes_nama'];
                $data['jumlah_input'] = $value['jumlah_input'];
                $data['jumlah_pesan'] = $value['jumlah_pesan'];
                $data['jumlah_mutasi'] = $value['jumlah_mutasi'];
                $data['satuanbesar_nama'] = $value['satuanbesar_nama'];
                $data['satuankecil_nama'] = $value['satuankecil_nama'];
                $data['satuan_mutasi'] = $value['satuan_mutasi'];
                $data['expired'] = !empty($value['expired']) ? date("d M Y", strtotime($value['expired'])) : "-";

                $data_obatalkes[$value['mutasiobatdetail_id']] = $data;
                $no++;
            }
        } catch (RequestException $e) {
            $data_obatalkes = [];
            $error = json_decode($e->getResponse()->getBody(),true);
            return DocoHelpers::response(['message' => $e->getMessage()],422);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
        return $this->render('detail', get_defined_vars());
    }

    // public function actionPenerimaan($id, $nomutasioa = "")
    // {
    // }

    //action buat triger modal
    public function actionSearch($tipe = NULL)
    {
        if($tipe == 'mengetahui') {
            $path = 'search_mengetahui';
        } elseif($tipe == 'menyetujui') {
            $path = 'search_menyetujui';
        } else {
            $path = 'search_mengetahui';
        }

        return $this->renderAjax($path, get_defined_vars());
    }

    public function actionGetDataDetail($id, $pesanan = "")
    {
        $id = DocoHelpers::decrypt($id);
        if(!empty($pesanan)){
            $pesanan = DocoHelpers::decrypt($pesanan);
        }
        // $body = $this->getDetail($id, $pesanan);
        try{
            if(empty($pesanan)) {
                $response = $this->_restApotek->get('inf-mutasi-obatalkes/detail?mutasiobatruangan_id='.$id,
                    [
                        'form_params' => []
                    ]
                );
            } else {
                $response = $this->_restApotek->get('inf-mutasi-obatalkes/detail-mutasi?advanced-filter[mutasiobatruangan_id]='.$id.'&advanced-filter[pesanobatalkes_id]='.$pesanan ,['form_params'=>[]]);
            }
            $body = json_decode($response->getBody(), true);
            if(!empty($pesanan)) {
                $body = $body['response']['data'];
            } else {
                $body = $body['response']['detail'];
            }

            $request = Yii::$app->request;
            $no = 0;
            $data = [];
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        
            foreach ($body as $key => $value) {


                $no++;
                $primaryKey = isset($value['mutasiobatdetail_id']) ? DocoHelpers::encrypt($value['mutasiobatdetail_id']) : DocoHelpers::encrypt($value['pesanobatalkes_id']);
                $primary = isset($value['mutasiobatdetail_id']) ? DocoHelpers::encrypt($value['mutasiobatdetail_id']) : DocoHelpers::encrypt($value['pesanobatalkes_id']);
                unset($value['mutasiobatdetail_id']);
                $value['rowNum'] = $no;
                
                if(empty($value['jumlah_input']) || is_null($value['jumlah_input']) || $value['jumlah_input'] == 0){
                    $jum_mutasi =0;
                    $value['konv'] = 0;
                }else{
                    $konv = isset($value['mutasiobatdetail_id']) ? ($value['jumlah_pesan'] / $value['jumlah_input']) : 1;
                    $jum_mutasi = isset($value['jumlah_mutasi']) ? $value['jumlah_mutasi'] / $konv : 0;
                    $value['konv'] = $konv;
                }
                $value['jumlah_mutasi'] = isset($value['jumlah_mutasi']) ? DocoHelpers::formatNumber($jum_mutasi) : "-";
                $value['jumlah_input'] = isset($value['jumlah_input']) ? DocoHelpers::formatNumber($value['jumlah_input']) : DocoHelpers::formatNumber($value['qty_besar']);
                $value['satuanbesar_nama'] = isset($value['satuanbesar_nama']) ? $value['satuanbesar_nama'] : $value['satuan_besar'];
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
            return $result;
        }
    }

    public function getDetail($id, $pesanan)
    {
        
        return $body;
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('ruangan?advanced-filter[instalasi_id]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_nama'],
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

    public function actionGetDataNomutasi()
    {
        if(isset($_GET['q']) && !empty($_GET['q'])) {
            $response = $this->_restApotek->request('POST', 'inf-mutasi-obatalkes/data-nomutasi',[
                            'form_params'=>['term'=>$_GET['q']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $id = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $data[] = ['id'=>$id,'text'=>$value['nomutasioa']];

            }
            $total = count($body['response']);
            $return = ['result'=>$data];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetDataNomutasi2()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'inf-mutasi-obatalkes/data-nomutasi2',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                //$id = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $data[] = ['id'=>$value['nomutasioa'],'text'=>$value['nomutasioa']];
            }
            $total = count($body['response']);
             $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetPegawai()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        if(isset($_GET['q']) && !empty($_GET['q'])){
            try {

                $response = $this->_restApotek->request('POST', 'inf-mutasi-obatalkes/data-pegawai',[
                                'form_params'=>['term'=>$_GET['q'], 'ruangan_id' => $ruangan_id],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $id = DocoHelpers::encrypt($value['pegawai_id']);
                    $data[] = ['id'=>$id,'text'=>$value['nama_pegawai']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data];
                return DocoHelpers::response($return);
            } catch (Exception $e) {
                return DocoHelpers::response($e->getMessage());
            }
        }
    }

    public function actionDeleteMutasi($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restApotek->request('DELETE', 'inf-mutasi-obatalkes/delete-mutasi',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPrint($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-mutasi-obat-alkes.pdf";
        try {
            $post = $request->post();
            $response = $this->_restApotek
                        ->post('inf-mutasi-obatalkes/print',
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

    public function actionPrintPenerimaan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/informasi-penerimaan-obat-alkes.pdf";
        try {
            $post = $request->post();
            $response = $this->_restApotek
                        ->post('inf-mutasi-obatalkes/print-penerimaan',
                        [
                            'query' => [
                                'id' => $id
                            ],
                            'save_to' => $path
                        ]);
            // return DocoHelpers::response($response);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetKonversi($satuanbesar_id, $satuankecil_id)
    {
        if($satuanbesar_id == null) {
            $satuanbesar_id = $satuankecil_id;
        }
        $response = $this->_restApotek->get('inf-mutasi-obatalkes/get-konversi', ['query' => [
            'satuanbesar_id' => $satuanbesar_id,
            'satuankecil_id' => $satuankecil_id
        ]]);

        $response = json_decode($response->getBody(), true);

        return $response['response'];
    }

}