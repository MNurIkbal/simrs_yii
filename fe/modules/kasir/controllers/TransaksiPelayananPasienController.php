<?php


/**
* @author yaya
**/

namespace Doco\kasir\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoConstants;

use Doco\kasir\models\TransaksiPelayananForm;
use Doco\kasir\models\PelayananKasirForm;
use GuzzleHttp\Exception\RequestException;

class TransaksiPelayananPasienController extends DocoController
{
    protected $_title = "Pelayanan Pasien";
    protected $_module = 'kasir/transaksi-pelayanan-pasien/';
    protected $_restKasir;
    protected $allowAction = [
        '*'
    ];

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
        $title = $this->_title;
        $model = new TransaksiPelayananForm;
        return $this->render('index',get_defined_vars());
    }

    public function actionGetNoPendaftaran()
    {
        $request = Yii::$app->request;
        $get = $request->get('q');
        try {
            $response = $this->_restKasir->get('transaksi-pelayanan-pasien/get-no-pendaftaran',[
                'query' => $get
            ]);
            $response = json_decode($response->getBody(),true);
            $data = [];

            foreach ($response['response'] as $key => $value) {
                $data[] = [
                    'id' => DocoHelpers::encrypt($value['pendaftaran_id']),
                    'text' => $value['no_rekam_medik'] . ' - ' . $value['no_pendaftaran'] . ' - ' . $value['nama_pasien']
                ];
            }

            $return = [
                'result' => $data,
                'total_count' => count($data),
                'incomplete_results' => false,
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'text' => $e->getMessage()
            ],422);
        }
    }

    public function actionGetDetailTransaksi()
    {
        $request = Yii::$app->request;
        $model = new TransaksiPelayananForm;
        $id_pendaftaran = DocoHelpers::decrypt($request->get('id_pendaftaran'));
        try {
            $response = $this->_restKasir->get('transaksi-pelayanan-pasien/get-data-pendaftaran',[
                'query' => [
                    'id' => $id_pendaftaran
                ]
            ]);

            $response = json_decode($response->getBody(),true);
            $model->attributes = isset($response['response']['info']) ? $response['response']['info'] : [];
            $model->detail_tagihan = isset($response['response']['tagihan']) ? $response['response']['tagihan'] : [];
            $id = DocoHelpers::encrypt($id_pendaftaran);
            $konfigSistem = isset($response['response']['konfig_sistem']) ? $response['response']['konfig_sistem'] : [];
            return $this->renderPartial('detail',get_defined_vars());
        } catch (RequestException $e) {
            $detail = $konfigSistem = [];
        }
    }

    public function actionFormPelayanan($id, $instalasi_terakhir = null, $ruangan_terakhir = null)
    {
        $title = 'Tambah Pelayanan Pasien';
        $model = new PelayananKasirForm;
        try {
            $response = $this->_restKasir->get('transaksi-pelayanan-pasien/get-attributes',[
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $kelasPelayanan = isset($response['response']['kelas_pelayanan'])
                    ? ArrayHelper::map($response['response']['kelas_pelayanan'],'kelaspelayanan_id','kelaspelayanan_nama') : [];
            $instalasi = isset($response['response']['instalasi'])
                    ? ArrayHelper::map($response['response']['instalasi'],'instalasi_id','instalasi_nama') : [];
            $tglPendaftaran = isset($response['response']['info_pasien']['tgl_pendaftaran'])
                    ? $response['response']['info_pasien']['tgl_pendaftaran'] : null;
        } catch (RequestException $e) {
            $kelasPelayanan = $instalasi = [];
            $tglPendaftaran = date('d-m-Y');
        }
        return $this->renderAjax('form',get_defined_vars());
    }

    public function actionAddPelayanan($id)
    {
        $request = Yii::$app->request;
        $model = new PelayananKasirForm;
        $model->load($request->post());
        if ($model->validate()) {
            try {
                $response = $this->_restKasir->post('transaksi-pelayanan-pasien/validasi-tindakan',[
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => DocoHelpers::decrypt($id)
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response, false, 'PelayananKasirForm');
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'title' => 'Proses Gagal !',
                    'message' => 'Terjadi kesalahan pada sistem.',
                    'error' => $e->getMessage()
                ],422);
            }
        } else {
            return DocoHelpers::response($model->errors,422,'PelayananKasirForm');
        }
    }

    public function actionSimpan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $model = new TransaksiPelayananForm;
        $model->load($request->post());

        $model->ruangan_pelakhir_id = $request->post('ruangan_pelakhir_id');
        $model->pendaftaran_id = $request->post('pendaftaran_id');
        $model->pasienadmisi_id = $request->post('pasienadmisi_id');
        $model->pasien_id = $request->post('pasien_id');
        $model->nama_pasien = $request->post('nama_pasien');
        $model->jumlah_uangmuka = $request->post('uang_muka');
        $model->instalasi_id = $request->post('instalasi_id');
        $model->status_pasien = $request->post('status_pasien');
        $model->detail_tagihan = $request->post('add_tindakan');

        // return DocoHelpers::response($model->attributes,422);
        try {
            $response = $this->_restKasir->post('transaksi-pelayanan-pasien/save',[
                'form_params' => $model->attributes,
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response, false, 'PelayananKasirForm');
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'title' => 'Proses Gagal !',
                'message' => 'Terjadi kesalahan pada sistem.',
                'error' => $e->getMessage()
            ],422);
        }
    }

    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $get = $request->get('term');
        try {
            $response = $this->_restKasir->get('transaksi-pelayanan-pasien/get-dokter',[
                'query' => [
                    'term' => $get
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $data = [];
            foreach ($response['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama']
                ];
            }
            $return = [
                'result' => $data,
                'total_count' => count($data),
                'incomplete_results' => false,
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'text' => $e->getMessage()
            ],422);
        }
    }

    public function actionGetRuangan($selected = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = $selected;
        try {

            $response = $this->_restKasir->get('allow/list-ruangan',[
                'query' => [
                    'instalasi_id' => $parent_label
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value){
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            }

            if ($body["response"]['count'] == 1) {
                $result['selected'] = $result['output'][0];
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetTindakan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $response = $this->_restKasir->get('transaksi-pelayanan-pasien/get-tindakan',[
                'query' => $get
            ]);
            $response = json_decode($response->getBody(),true);
            $data = [];
            foreach ($response['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['tindakan_paket_id'],
                    'text' => $value['nama_tindakan_paket']
                ];
            }
            $return = [
                'result' => $data,
                'total_count' => count($data),
                'incomplete_results' => false,
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        }
    }

}