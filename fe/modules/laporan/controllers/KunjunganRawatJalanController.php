<?php
// Author : Ardi Pratama

namespace Doco\laporan\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
// use app\modules\informasi\models\PemesananBarangForm;
use GuzzleHttp\Exception\RequestException;

class KunjunganRawatJalanController extends DocoController
{
    protected $_title = "Laporan Kunjungan Rawat Jalan";
    protected $_module = 'laporan/pemesanan-barang/';
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
        // $status = $this->_status; $options = $this->_options;
        $instalasi = ['0'=>'Rekam Medik'];
        $ruangan = ['0'=>'Poli Anak','Poli Jantung'];
        $no_pesanan = ['0'=>'PES01'];
        $penjamin = ['Mandiri','PNS'];
        $carabayar = ['Tunai','BPJS'];
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $data = [[
                    'rowNum'=>1,
                    'dummy_tanggal'=>date('Y-m-d'),
                    'dummy_norm'=>'00112200',
                    'dummy_nopendaftaran' => '01231241241251',
                    'dummy_kamar'=>'Umum',
                    'dummy_ruangan'=>'Basudewa',
                    'dummy_nama'=>'Asep',
                    'dummy_status' =>'Belum Dikirim',
                    'dummy_jumlah' =>rand(5,5),
                    'dummy_alamat' => 'Jalan Sukahaji No. 42',
                    'dummy_kelamin' => 'Laki - laki',
                    'dummy_umur' => '25',
                    'dummy_golumur' => 'Dewasa',
                    'dummy_jeniskasus' => 'Jantung',
                    'dummy_kelaspelayanan' => 'Standar',
                    'dummy_agama' => 'islam',
                    'dummy_statuskawin' => 'Belum Menikah',
                    'dummy_pekerjaan' => 'Pegawai Swasta',
                    'dummy_kota' => 'Bandung',
                    'dummy_kunjungan' => 'Baru',
                    'dummy_poly' => 'Poliklinik Jantung',
                    'dummy_carabayar' => 'Tunai'
                ]];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            // $response = $this->_restRm->get('pemesanan-barang/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            // $body = json_decode($response->getBody(), True);
            // $no = $request->get('start',1);
            // foreach ($body['response']['data'] as $key => $value) {
            //     $no++;
            //     $primaryKey = DocoHelpers::encrypt($value['pesanbarang_id']);
            //     unset($value['pesanbarang_id']);

            //     $value['rowNum'] = $no;
            //     $data[$key] = $value;
            // }

            // $result['data'] = $data;
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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

    public function actionCreate()
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new PemesananBarangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restRm->post('pemesanan-barang/create', [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restRm->get('instalasi');
            $body = json_decode($response->getBody(), TRUE);
            $instalasi = [];
            foreach ($body['response']['data'] as $value) {
                $primaryKey = DocoHelpers::encrypt($value['instalasi_id']);
                $value['idx_instalasi'] = $primaryKey;
                unset($value['instalasi_id']);
                array_push($instalasi, $value); 
            }
            $response = $this->_restRm->get('ruangan');
            $body = json_decode($response->getBody(), TRUE);
            $ruangan = [];
            foreach ($body['response']['data'] as $value) {
                $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
                $value['idx_ruangan'] = $primaryKey;
                unset($value['ruangan_id']);
                array_push($ruangan, $value); 
            }
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new PemesananBarangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restRm->put('pemesanan-barang/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restRm->get('instalasi');
            $body = json_decode($response->getBody(), TRUE);
            $instalasi = [];
            foreach ($body['response']['data'] as $value) {
                $primaryKey = DocoHelpers::encrypt($value['instalasi_id']);
                $value['idx_instalasi'] = $primaryKey;
                unset($value['instalasi_id']);
                array_push($instalasi, $value); 
            }
            $response = $this->_restRm->get('ruangan');
            $body = json_decode($response->getBody(), TRUE);
            $ruangan = [];
            foreach ($body['response']['data'] as $value) {
                $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
                $value['idx_ruangan'] = $primaryKey;
                unset($value['ruangan_id']);
                array_push($ruangan, $value); 
            }
            $response = $this->_restRm->get('pemesanan-barang/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->barang_nama = $model->barang_m['barang_nama'];
            $model->qty_pesan = $model->pesanbarangdetail_t['qty_pesan'];
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRm->delete('pemesanan-barang/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", [
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
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

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRm->put('pemesanan-barang/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message,  
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
