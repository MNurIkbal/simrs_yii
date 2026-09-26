<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2019-01-08 16:42:20
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-02-23 15:31:28
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
use app\modules\bankdarah\models\PesanDarahForm;
use app\modules\bankdarah\models\PesanDarahDetailForm;
use yii\helpers\VarDumper;

class PemesananDarahRuanganController extends DocoController
{
	protected $_title;
    protected $_restBankDarah;
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_module = '/bankdarah/pemesanan-darah-ruangan/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Pemesanan Darah Ruangan');
        $this->_restBankDarah = Yii::$app->docoRest->bankdarah;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $model = new PesanDarahForm;
        $modelDetail = new PesanDarahDetailForm;
        $dataRequest = $this->getRequest($this->_ruangan_id);
        $ruangan_nama = Yii::$app->docoVars->workspace("ruangan_name");
        $model->ruanganpemesan_id = $ruangan_nama;
        $modelDetail->jumlah = 1;
        return $this->render('index', get_defined_vars());
    }

    private function getRequest($ruangan_id)
    {
        try {
            $request = $this->_restBankDarah->request('GET', 'pemesanan-darah-ruangan/get-data-request', [
                'query' => [
                    'ruangan_id' => $ruangan_id,
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $result = $response['response'];
            return $result;
        } catch (Exception $e) {
            $result['dokter'] = [];
            $result['jenis_darah'] = [];
            $result['metode_pengambilan'] = [];
            return $result;
        } catch (RequestException $e) {
            $result['dokter'] = [];
            $result['jenis_darah'] = [];
            $result['metode_pengambilan'] = [];
            return $result;
        }
    }

    private function getJenisDarah($jenisdarah_id)
    {
        try {
            $request = $this->_restBankDarah->request('GET', 'pemesanan-darah-ruangan/get-jenis-darah', [
                'query' => [
                    'jenisdarah_id' => $jenisdarah_id,
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $result = $response['response'];
            return $result;
        } catch (Exception $e) {
            $result['dokter'] = [];
            $result['jenis_darah'] = [];
            $result['metode_pengambilan'] = [];
            return $result;
        } catch (RequestException $e) {
            $result['dokter'] = [];
            $result['jenis_darah'] = [];
            $result['metode_pengambilan'] = [];
            return $result;
        }
    }

    public function actionSetListItem()
    {
        $request = Yii::$app->request;
        $model = new PesanDarahDetailForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        if ($model->validate()) {
            $dataDetail = $request->post($formName);
            $userLogin = Yii::$app->user->identity->loginpemakai_id;
            $dataJenisDarah = $this->getJenisDarah($dataDetail['jenisdarah_id']);
            
            $cachePmi = Yii::$app->cache->get("pemesanan-darah-ruangan-{$userLogin}");
            if ($cachePmi == false) {
                Yii::$app->cache->set("pemesanan-darah-ruangan-{$userLogin}",[]);
                $cachePmi = [];
            }

            $keyUnique = $model->jenisdarah_id;
            $keyUnique = DocoHelpers::encrypt($keyUnique);
            if (!isset($cachePmi[$keyUnique])) {
                $cachePmi[$keyUnique] = [];
            }
            
            $cachePmi[$keyUnique] = [
                'primaryKey' => $keyUnique,
                'jenisdarah_nama' => $dataJenisDarah['jenisdarah_nama'],
                'jenisdarah_id' => $dataJenisDarah['jenisdarah_id'],
                'tgl_mintakirim' => date('d-M-Y', strtotime($dataDetail['tgl_mintakirim'])),
                'wkt_mintakirim' => $dataDetail['wkt_mintakirim'],
                'jumlah' => $dataDetail['jumlah'],
                'harga' => $dataJenisDarah['harga'],
                'subTotal' => $dataDetail['jumlah'] * $dataJenisDarah['harga'],
            ];
            
            $cachePmi = Yii::$app->cache->set("pemesanan-darah-ruangan-{$userLogin}",$cachePmi,3600);
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
        $cachePmi = Yii::$app->cache->get("pemesanan-darah-ruangan-{$userLogin}");
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
        $cachePmi = Yii::$app->cache->get("pemesanan-darah-ruangan-{$userLogin}");
        if ($cachePmi !== false) {
            if (isset($cachePmi[$id])) {
                unset($cachePmi[$id]);
                Yii::$app->cache->set("pemesanan-darah-ruangan-{$userLogin}",$cachePmi);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionSave()
    {
        $cache = Yii::$app->cache;
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cachePmi = $cache->get("pemesanan-darah-ruangan-{$userLogin}");
        $response['response'] = [];
        $newCache = [];
        $codeHttp = 422;
        $model = new PesanDarahForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $post = Yii::$app->request->post();
        $model->dokter_id = $post['dokter_id'];
        $model->pasien_id = $post['pasien_id'];
        $model->metode_pengambilan = $post['metode_pengambilan'];
        $model->total_harga = $post['total_harga'];
        $model->total_kantongdarah = $post['total_kantongdarah'];
        $model->golongandarah_id = $post['golongandarah_id'];
        if($model->validate()) {
            if ($cachePmi) {
                try {
                    $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
                    $pasien_id = Yii::$app->request->post("pasien_id");
                    $pendaftaran_id = Yii::$app->request->post("pendaftaran_id");
                    $pasienadmisi_id = Yii::$app->request->post("pasienadmisi_id");
                    $dokter_id = Yii::$app->request->post("dokter_id");
                    $metode_pengambilan = Yii::$app->request->post("metode_pengambilan");
                    $indikasi_transfusi = Yii::$app->request->post("indikasi_transfusi");
                    $total_harga = Yii::$app->request->post("total_harga");
                    $total_kantongdarah = Yii::$app->request->post("total_kantongdarah");
                    $riwayat_transfusi = Yii::$app->request->post("riwayat_transfusi");
                    $riwayat_kehamilan = Yii::$app->request->post("riwayat_kehamilan");
                    $keterangan = Yii::$app->request->post("keterangan");
                    $golongandarah_id = Yii::$app->request->post("golongandarah_id");

                    $newCache = [
                        'ruangan_id' => $ruangan_id,
                        'pasien_id' => $pasien_id,
                        'pendaftaran_id' => $pendaftaran_id,
                        'pasienadmisi_id' => $pasienadmisi_id,
                        'dokter_id' => $dokter_id,
                        'metode_pengambilan' => $metode_pengambilan,
                        'indikasi_transfusi' => $indikasi_transfusi,
                        'riwayat_transfusi' => $riwayat_transfusi,
                        'riwayat_kehamilan' => $riwayat_kehamilan,
                        'keterangan' => $keterangan,
                        'golongandarah_id' => $golongandarah_id,
                        'total_harga' => $total_harga,
                        'total_kantongdarah' => $total_kantongdarah,
                        'data' => json_encode($cachePmi)
                    ];

                    $result = $this->_restBankDarah->post('pemesanan-darah-ruangan/save',[
                        'form_params' => $newCache
                    ]);
                    $result = json_decode($result->getBody(),true);
                    // return DocoHelpers::response($result);
                    if($result['metadata']['status'] == 200) {
                        $cache->delete("pemesanan-darah-ruangan-{$userLogin}");
                    }
                    $response['response'] = $result;
                    $codeHttp = 200;
                    $idParent = isset($result['response']['id_parent']) 
                                    ? $result['response']['id_parent'] 
                                    : null;

                    $no_pesandarah = isset($result['response']['no_pesandarah']) 
                                    ? $result['response']['no_pesandarah'] 
                                    : null;

                    $newCache['id_parent'] = DocoHelpers::encrypt($idParent);
                    $response['response'] = [
                        'text' => 'Pemesanan Darah Ruangan berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'id_parent' => $idParent,
                        'no_pesandarah' => $no_pesandarah
                    ];
                } catch (RequestException $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalah pada sistem';
                    $response['response']['messageError'] = $e->getMessage();
                }
            }
        }
        else {
            $errors = DocoHelpers::parseError($model->errors,'PesanDarahForm');
            return DocoHelpers::response([
                'response' => [
                    'data' => $errors
                ]
            ],422);
        }
        return DocoHelpers::response($response,$codeHttp);
    }

    public function actionCetak($no_pesandarah)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $ruangan_nama = Yii::$app->docoVars->workspace("ruangan_name");
            $path = Yii::getAlias("@download") . "/pemesanan-darah-ruangan.pdf";
            $response = $this->_restBankDarah->get('pemesanan-darah-ruangan/export-pdf?no_pesandarah='.$no_pesandarah.'&ruangan_nama='.$ruangan_nama,
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

    public function actionSearchPasien()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $dokter_id = $request->get('dokter_id');
            $result = $this->_restBankDarah->get('pemesanan-darah-ruangan/get-data-pasien', [
                        'query' => [
                            'term' => $request->get('term'),
                            'dokter_id' => $dokter_id,
                            'ruangan_id' => $this->_ruangan_id,
                        ]
                    ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $diagnosa = isset($value['diagnosa']) ? json_decode($value['diagnosa'], true) : "";
                $diagnosa = !empty($diagnosa) ? $diagnosa['text'] : "-";
                $response[] = [
                    'id' => $value['pasien_id'],
                    'text' => $value['no_rekam_medik'].' - '.$value['nama_pasien'],
                    'nama_pasien' => $value['nama_pasien'],
                    'no_rekam_medik' => $value['no_rekam_medik'],
                    'jenis_kelamin' => $value['jenis_kelamin'],
                    'diagnosa' => $diagnosa,
                    'kadar_hb' => ($value['kadar_hb']) ? $value['kadar_hb'] : '-',
                    'umur' => ($value['umur']) ? $value['umur'] : '-',
                    'pendaftaran_id' => $value['pendaftaran_id'],
                    'pasienadmisi_id' => $value['pasienadmisi_id'],
                    'golongandarah_id' => ($value['golongandarah_id']) ? $value['golongandarah_id'] : '-',
                    'golongandarah_nama' => ($value['golongandarah_nama']) ? $value['golongandarah_nama'] : '-',
                ];
            }

        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }
}