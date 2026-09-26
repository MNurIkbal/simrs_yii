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

class PemesananBarangController extends DocoController
{
    protected $_title = "Laporan Pemesanan Barang";
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
        $ruangan = ['0'=>'Gudang Farmasi'];
        $no_pesanan = ['0'=>'PES01'];
        
        return $this->render('index', get_defined_vars());
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
            $response = $this->_restRm->get('pemesanan-barang/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesanbarang_id']);
                unset($value['pesanbarang_id']);

                $value['aksi']  = '<table class="no-border"><tr>';
                $value['aksi'] .= '<td>&nbsp;'.Html::button(
                    '<i class="fa fa-eye" aria-hidden="true"></i>', [
                        'class' => 'btn btn-info btn-xs data-view',
                        'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Lihat'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-file-excel-o" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-green btn-xs data-export',
                        'action' => Url::home().$this->_module.'export?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Ekspor'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-crimson btn-xs data-print',
                        'action' => Url::home().$this->_module.'print?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Cetak'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::button(
                    '<i class="fa fa-pencil" aria-hidden="true"></i>', [
                        'class' => 'btn btn-dark-turquise btn-xs data-update',
                        'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip",
                        'data-original-title' => \Yii::t('fe', 'Ubah'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '<td>&nbsp;'.Html::a(
                    '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::home().$this->_module.'delete?id='.$primaryKey,
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Hapus'),
                    ]
                ).'&nbsp;</td>';
                $value['aksi'] .= '</tr></table>';

                // $value['is_subsidiasuransi'] = DocoHelpers::labelYesNo($value['is_subsidiasuransi']);
                // $value['is_subsidipemerintah'] = DocoHelpers::labelYesNo($value['is_subsidipemerintah']);
                // $value['is_subsidirs'] = DocoHelpers::labelYesNo($value['is_subsidirs']);
                // $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
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
