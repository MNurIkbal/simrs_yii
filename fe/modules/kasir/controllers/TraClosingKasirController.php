<?php
// Author : Budi

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\kasir\models\ClosingKasir;

class TraClosingKasirController extends DocoController
{
    protected $_title = "Transaksi Closing Kasir";
    protected $_module = '/kasir/tra-closing-kasir/';
    protected $_restKasir; 

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
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
        $options = $this->_options;
        $userIdentity = Yii::$app->session->get('user_identity');
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $pegawai_id = $userIdentity['id_pegawai'];
        $instalasi_id = $activeWorkspace['instalasi_id'];
        $ruangan_id = $activeWorkspace['ruangan_id'];

        $request = Yii::$app->request;
        if ($request->post()) {
            try {

                $response = $this->_restKasir->post('tra-closing-kasir/create', [
                    'form_params' => $request->post()
                ]);
                return DocoHelpers::responseJsonString($response->getBody(), "NoName");
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), "NoName");
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        } else {
            $title = Yii::t('fe', $this->_title);
            $model = new ClosingKasir;
            $ruangan_kasir = $nilai_uang = $list_pegawai_mengetahui = $shift = [];
            try {
                $response = $this->_restKasir->get('tanda-bukti-bayar/get-attributes',[
                        'query' => [
                            'instalasi_id' => $instalasi_id,
                            'ruangan_id' => $ruangan_id
                        ]
                ]);
                $response = json_decode($response->getBody(),true);
                $response = $response['response'];
                $ruangan_kasir = $response['list_ruangan'];
                $nilai_uang = $response['list_nilai_uang'];
                $list_pegawai_mengetahui = $response['list_pegawai_ruangan'];
                $shift = $response['list_shift'];
                $attr_closing = $response['attr_closing'];
                $model->total_tagihan = !empty($attr_closing['jmlpembayaran']) ? $attr_closing['jmlpembayaran'] : 0;
                $model->tunai = !empty($attr_closing['pembayaran_tunai']) ? $attr_closing['pembayaran_tunai'] : 0;
                $model->nontunai = !empty($attr_closing['pembayaran_nontunai']) ? $attr_closing['pembayaran_nontunai'] : 0;
                $model->dijamin = !empty($attr_closing['pembayaran_penjamin']) ? $attr_closing['pembayaran_penjamin'] : 0;
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
            } catch (\Exception $e) {
                Yii::info($e->getMessage());
            }

            return $this->render('index', get_defined_vars());
        }
    }

    public function actionDepListPegawaiRuangan() 
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $ruangan_id = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restKasir->get('allow/list-pegawai-ruangan?ruangan_id='.$ruangan_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response']['data'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $response['pegawai_id'],
                'name' => $response['nama_pegawai']
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
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
            $url = 'tanda-bukti-bayar/index?unpaid=1&'.http_build_query($yiiRestfulParams);
            $response = $this->_restKasir->get($url);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $total_tagihan = 0;
            $tunai = 0;
            $nontunai = 0;
            $dijamin = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tandabuktibayar_id']);
                unset($value['tandabuktibayar_id']);

                $total_tagihan = $total_tagihan + $value['jmlpembayaran'];
                $tunai = $tunai + $value['pembayaran_tunai'];
                $nontunai = $nontunai + $value['pembayaran_nontunai'];
                $dijamin = $dijamin + $value['pembayaran_penjamin'];
                $carabayar_nama = '';
                if(!empty($value['carabayar_nama']) && !empty($value['penjamin_nama'])) {
                    $carabayar_nama = $value['carabayar_nama'].' / '.$value['penjamin_nama'];
                }

                $strmetodebayar = "";
                if(!empty($value['additional_nontunai'])){
                    $additional_nontunai = json_decode($value['additional_nontunai'], true);
                    if(is_array($additional_nontunai)){
                        foreach($additional_nontunai as $row){
                            $no_kartu =  !empty($row['no_kartu']) ? ($row['no_kartu']) : "";
                            $strnokartu = !empty($no_kartu) ? "(".$no_kartu.")" : "";
                            $metode_bayar = !empty($row['metode_bayar']) ? $row['metode_bayar'] : "" ;
                            $metodebayarkartu = $metode_bayar . " "  . $strnokartu;
                            $strmetodebayar = (empty($strmetodebayar) ? " <br> " . $metodebayarkartu : $strmetodebayar . ', ' . $metodebayarkartu );
                        }
                    }
                }

                // Detail Cara Bayar Non Tunai
                $caraBayarNonTunai = '-';
                if(!empty($value['additional_nontunai'])) {
                    $json = json_decode($value['additional_nontunai'], true);
                    $arrNonTunai = array();
                    foreach ($json as $jsonRow => $valJson) {
                        $arrNonTunai[] = $valJson['metode_bayar'].' ('.$valJson['nama_edc'].' || '.$valJson['no_kartu'].')';
                    }
                    $parsingNonTunai = implode('<br>', $arrNonTunai);
                    $caraBayarNonTunai = $parsingNonTunai;
                }

                $penjamin_nama = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : "";
                $strtunai = (!empty($value['pembayaran_tunai']) && $value['pembayaran_tunai'] > 0 ? "Tunai" : "");
                $strnontunai = (!empty($value['pembayaran_nontunai']) && $value['pembayaran_nontunai'] > 0 ? "Non Tunai". $strmetodebayar : "");
                $operatortunainontunai = $value['pembayaran_tunai'] > 0 && $value['pembayaran_nontunai'] > 0 ? " dan " : "";
                $strtunainontunai = "<br>" . $strtunai . $operatortunainontunai . $strnontunai;

                $keterangan = $penjamin_nama . $strtunainontunai;

                $jenisBayar = isset($value['jenis']) ? strtolower($value['jenis']) :'-';

                $value['uangditerima_rupiah'] = DocoHelpers::formatNumber($value['uangditerima']);
                $value['jmlpembayaran_rupiah'] = DocoHelpers::formatNumber($value['jmlpembayaran']);
                $value['pembayaran_tunai_rupiah'] = DocoHelpers::formatNumber($value['pembayaran_tunai']);
                $value['pembayaran_nontunai_rupiah'] = DocoHelpers::formatNumber($value['pembayaran_nontunai']);
                $value['pembayaran_penjamin_rupiah'] = DocoHelpers::formatNumber($value['pembayaran_penjamin']);
                $value['tglbuktibayar'] = date('d-M-Y H:i:s',strtotime($value['tglbuktibayar']));
                $value['carabayar_nama'] = $carabayar_nama;
                $value['no_pendaftaran'] = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] :'-' ;
                $value['nama_pasien'] = isset($value['nama_pasien']) ? $value['nama_pasien'] :'-' ;
                $value['no_rekam_medik'] = isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] :'-' ;
                $value['nama_pasien_rm'] = $value['nama_pasien']. ' / ' .$value['no_rekam_medik'] ;
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $value['total_tagihan'] = $total_tagihan;
                $value['tunai'] = $tunai;
                $value['nontunai'] = $nontunai;
                $value['dijamin'] = $dijamin;
                $value['jenis'] = ucwords(str_replace('_',  ' ', $jenisBayar));
                $value['keterangan'] = isset($value['keterangan']) ? $value['keterangan'] :'-';
                $value['bayar_nontunai'] = $caraBayarNonTunai;
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

    public function actionAddDetailClosing()
    {
        $userId = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $model = new ClosingKasir;
        $model->scenario = 'set-closing';
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        if ($model->validate()) {
            $cacheClosingKasir = Yii::$app->cache->get("closing-kasir-{$userId}");
            
            if ($cacheClosingKasir == false) {
                Yii::$app->cache->set("closing-kasir-{$userId}",[]);
                $cacheClosingKasir = [];
            }

            $totalUang = 0;
            if (isset($cacheClosingKasir[$model->pecahan]['banyakuang'])) {
                $cacheClosingKasir[$model->pecahan]['banyakuang'] += $model->qty;
                $jumlahUang = $model->qty * $cacheClosingKasir[$model->pecahan]['nilaiuang'];
                $cacheClosingKasir[$model->pecahan]['jumlahuang'] += $jumlahUang;
            } else {
                $cacheClosingKasir[$model->pecahan] = [
                    'pecahan' => $model->pecahan,
                    'nilaiuang' => $request->post('pecahan'),
                    'banyakuang' => $model->qty,
                    'jumlahuang' => $request->post('pecahan') * $model->qty,
                ];
            }
            Yii::$app->cache->set("closing-kasir-{$userId}",$cacheClosingKasir,3600);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($cacheClosingKasir);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionDeleteDetailClosing($id)
    {
        $userId = Yii::$app->user->identity->loginpemakai_id;
        $id = DocoHelpers::decrypt($id);
        $cacheClosing = Yii::$app->cache->get("closing-kasir-{$userId}");
        if ($cacheClosing !== false) {
            if (isset($cacheClosing[$id])) {
                unset($cacheClosing[$id]);
                Yii::$app->cache->set("closing-kasir-{$userId}",$cacheClosing);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($id);
    }

    public function actionClearCache()
    {
        $userId = Yii::$app->user->identity->loginpemakai_id;
        Yii::$app->cache->set("closing-kasir-{$userId}",[]);
        return true;
    }

    public function actionGetDetailClosing()
    {
        $userId = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $cacheClosing = Yii::$app->cache->get("closing-kasir-{$userId}");
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cacheClosing !== false) {
            $no = $request->get('start',1);
            usort($cacheClosing, function($a, $b) {
                return $a['nilaiuang'] - $b['nilaiuang'];
            });
            foreach ($cacheClosing as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pecahan']);
                $data[] = [
                    'rowNum' => $no,
                    'nilai' => $value['nilaiuang'],
                    'nilaiuang' => DocoHelpers::formatNumber($value['nilaiuang']),
                    'banyakuang' => DocoHelpers::formatNumber($value['banyakuang']),
                    'jumlahuang' => DocoHelpers::formatNumber($value['jumlahuang']),
                    'jumlah' => $value['jumlahuang'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-detail-closing','id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
        }
       return DocoHelpers::response($result);
    }

    public function actionSave()
    {
        $userId = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $model = new ClosingKasir;
        $model->scenario = 'save-closing';
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        // $model->shift_id = $request->post('shift');
        // var_dump($model->attributes);die;
        if ($model->validate()) {
            $cacheClosing = Yii::$app->cache->get("closing-kasir-{$userId}");
            $model->cache_pembayaran = json_decode($request->post('cache_pembayaran',"{}"),true);
            $model->cache_closing = $cacheClosing;
            $model->total_uang = $request->post('total_uang');
            $model->start_date = $request->post('start_date');
            $model->end_date = $request->post('end_date');
            try {
                $response = $this->_restKasir->post('tanda-bukti-bayar/save',[
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(),true);
                if ($response['metadata']['status'] < 400) {
                    Yii::$app->cache->set("closing-kasir-{$userId}",[]);
                }
                return DocoHelpers::response($response,false,$formName);
            } catch (RequestException $e) {
                return DocoHelpers::response(['message' => $e->getMessage()],500);
            } catch (\Exception $e) {
                return DocoHelpers::response(['message' => $e->getMessage()],500);
            }
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionSaveClosingKasir()
    {
        $request = Yii::$app->request;
        $model = new ClosingKasir;
        $model->load($request->post());

        if ($model->validate()) {
            $response = $this->_restKasir->post('tanda-bukti-bayar/save',[
                'form_params' => $model->attributes
            ]);

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body, 200);
        }else{
            return DocoHelpers::response($model->errors, 422, 'ClosingKasir');
        }
    }

    public function actionCetakClosing($id)
    {
        
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/closing-kasir.pdf";
        $response = $this->_restKasir->get('tanda-bukti-bayar/export-pdf',[
            'query' => [
                'id' => $id
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(),true);
        // return DocoHelpers::response($body);
        return DocoHelpers::previewPdf($path);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Transaksi Closing Kasir Excel';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $userIdentity = Yii::$app->session->get('user_identity');
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $pegawai_id = $userIdentity['id_pegawai'];
        $instalasi_id = $activeWorkspace['instalasi_id'];
        // $ruangan_id = $activeWorkspace['ruangan_id'];

        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        // $session['ruangan_id'] = $ruangan_id;
        $session['pegawai_id'] = $pegawai_id;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "tra-closing-kasir/sync-export-excel",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Transaksi Closing Kasir.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('tra-closing-kasir/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionGetDataTagihan(){
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $instalasi_id = $activeWorkspace['instalasi_id'];
        $ruangan_id = $activeWorkspace['ruangan_id'];

        try {
            $response = $this->_restKasir->get('tanda-bukti-bayar/get-attributes',[
                    'query' => [
                        'instalasi_id' => $instalasi_id,
                        'ruangan_id' => $ruangan_id
                    ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $attr_closing = $response['attr_closing'];
            $attr_closing['jmlpembayaran'] = !empty($attr_closing['jmlpembayaran']) ? $attr_closing['jmlpembayaran'] : 0;
            $attr_closing['pembayaran_tunai'] = !empty($attr_closing['pembayaran_tunai']) ? $attr_closing['pembayaran_tunai'] : 0;
            $attr_closing['pembayaran_nontunai'] = !empty($attr_closing['pembayaran_nontunai']) ? $attr_closing['pembayaran_nontunai'] : 0;
            $attr_closing['pembayaran_penjamin'] = !empty($attr_closing['pembayaran_penjamin']) ? $attr_closing['pembayaran_penjamin'] : 0;
            
            return DocoHelpers::response($attr_closing);
            // return DocoHelpers::response($attr_closing, 200);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

    }
}
