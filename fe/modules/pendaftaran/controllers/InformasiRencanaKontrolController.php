<?php

/**
 * @Author: Naufal
 * @Date:   2018-01-18 16:04:34
 * @Description: Merupakan Controller Informasi Rencana Kontrol yang terdapat pada module pendaftaran
 */

namespace Doco\pendaftaran\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\pendaftaran\models\JanjiPoliForm;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class InformasiRencanaKontrolController extends DocoController
{
    protected $_title = "Rencana kontrol Pasien";
    protected $_module = 'pendaftaran/informasi-rencana-kontrol/';
    protected $_moduleDaftarRajal = 'pendaftaran/daftar-rajal/';
    protected $_page;
    protected $_restPendaftaran;
    protected $_restMaster;
    protected $_id_ruangan;
    protected $_restRajal;

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_id_ruangan = 1; // dummy id_ruangan
        $this->_page = Yii::t('fe', 'Rencana kontrol');
    }

    public function actionIndex()
    {
        $model = new JanjiPoliForm;
        $status = $this->_status;
        $options = $this->_options;
      
        try {
            $response = $this->_restPendaftaran->get('inf-rencana-kontrol/init-index');
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            $jenis = $response['jenis'];
            $status = $response['statusDaftar'];
            $ruangan = $response['poliklinik'];

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
        }
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
            $response = $this->_restPendaftaran->request('get', 'inf-rencana-kontrol/index', [
                'query' => $yiiRestfulParams,
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['buatjanjipoli_id']);
                $value['primary'] = $primaryKey;
                unset($value['buatjanjipoli_id']);

                $namaPasien = $value['nama_pasien'] ? $value['nama_pasien'] : '-';
                $no_pendaftaran = $value['no_pendaftaran'] ? $value['no_pendaftaran'] : '-';
                $noRM = $value['no_rekam_medik'] ? $value['no_rekam_medik'] : '-';
                $poliAsal = $value['ruangan_asal'] ? $value['ruangan_asal'] : '-';
                $dokterAsal = $value['doktermengkonsul'] ? $value['doktermengkonsul'] : '-';
                $poliTujuan = $value['ruangan_nama'] ? $value['ruangan_nama'] : '-';
                $dokterTujuan = $value['nama_pegawai'] ? $value['nama_pegawai'] : '-';
                $caraBayar = $value['carabayar_nama'] ? $value['carabayar_nama'] : '-';
                $penjamin = $value['penjamin_nama'] ? $value['penjamin_nama'] : '-';

                $value['pasien'] = $no_pendaftaran . '<br>' . $noRM . '<br>' . $namaPasien;
                $value['poli_asal'] = $poliAsal . '<br>' . $dokterAsal;
                $value['poli_tujuan'] = $poliTujuan . '<br>' . $dokterTujuan;
                $value['pembayaran'] = $caraBayar . '<br>' . $penjamin;
                $value['is_konsul'] = $value['transaksi_konsul'] == DocoConstants::KONSUL_POLI ? DocoConstants::S_KONSUL_POLI : DocoConstants::S_RENCANA_KONTROL;
                $value['approval'] = '';
                switch ($value['status_approve']) {
                    case DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES:
                        $value['approval'] = 'Belum Diproses';
                        break;
                    case DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI:
                        $value['approval'] = 'Disetujui';
                        break;
                    case  DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK:
                        $value['approval'] = 'Ditolak';
                        break;
                    default:
                        $value['approval'] = 'Belum Diproses';
                        break;
                }
                $value['toggle'] = "";
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

    public function actionUpdate($id = null)
    {
        $status = $this->_status;
        $options = $this->_options;

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah') . ' ' . \Yii::t('fe', $this->_title);
        $model = new JanjiPoliForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restPendaftaran->put('inf-rencana-kontrol/update?id=' . $id, [
                        'form_params' => $model->attributes,
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
            $response = $this->_restPendaftaran->get('inf-rencana-kontrol/view?id=' . $id);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionSetujui($id)
    {
        $id = DocoHelpers::decrypt($id);
        $id_user = DocoHelpers::encrypt($id);

        try {
            $response = $this->_restPendaftaran->put('tra-rencana-kontrol/update?id=' . $id, [
                'form_params' => ["status_janjipoli" => 356],
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil') . " !",
                'text' => \Yii::t('fe', "Status berhasil diubah."),
                'id' => $id_user,
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );

        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ') . " !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah."),
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

    public function actionDitolak($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPendaftaran->put('tra-rencana-kontrol/update?id=' . $id, [
                'form_params' => ["status_janjipoli" => 355],
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil') . " !",
                'text' => \Yii::t('fe', "Status berhasil diubah."),
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ') . " !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah."),
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

    public function actionBatal($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPendaftaran->put('tra-rencana-kontrol/update?id=' . $id, [
                'form_params' => ["status_janjipoli" => 354],
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil') . " !",
                'text' => \Yii::t('fe', "Status berhasil diubah."),
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ') . " !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah."),
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

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restPendaftaran->delete('rencana-kontrol/delete?id=' . $id);
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

    public function actionApprove($id){
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);

        try {
            /* batal disini */
            $response = $this->_restRajal->request('POST', 'inf-konsul-poli/approve', ['form_params'=>
                    [
                        'konsulpoli_id' => $id,
                        'status_approve' => DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI //ambil dari lookup yg sama dengan pendaftaran online
                    ]
                ]);

            $body = json_decode($response->getBody(), true);
            // $return = ['response'=>$body['response']];
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionSearchDokter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $q = $request->get('search');
        $result = [];
        $result['results'] = [];
        $term = isset($q['term']) ? $q['term'] : null;
        
        try {
            $response = $this->_restPendaftaran->get('allow/get-list-dokter', ['query' => ['term' => $term]]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
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

    public function actionShowPopupExcel()
    {
        $title = Yii::t('fe', 'Informasi Rencana Kontrol');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restPendaftaran, [
            'url' => "inf-rencana-kontrol/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Informasi Rencana Kontrol.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec($this->_restPendaftaran,[
            'url' => 'inf-rencana-kontrol/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
