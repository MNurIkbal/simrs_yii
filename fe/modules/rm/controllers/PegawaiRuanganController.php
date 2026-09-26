<?php
/**
 * @author [Budi] 
 * @date 8 Januari 2018
 * @desc Controller Master Pegawai Ruangan
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\RuanganPegawaiForm;
use GuzzleHttp\Exception\RequestException;
use app\modules\rm\models\PegawaiForm;
use yii\helpers\Json;

class PegawaiRuanganController extends DocoController
{
    protected $_title;
    protected $_module = '/rm/pegawai-ruangan/';
    protected $_restRm;
    protected $_workspace;
    protected $_ruangan_id;
    protected $_ruangan_nama;
    protected $_request;
    protected $_session;

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Pegawai ruangan');
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_workspace = Yii::$app->session->get('active_workspace');
        $this->_ruangan_id = $this->_workspace['ruangan_id'];
        $this->_ruangan_nama = $this->_workspace['ruangan_name'];
        $this->_request = Yii::$app->request;
        $this->_session = Yii::$app->session;
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
        $ruangan_id = $this->_ruangan_id;
        $url_add = $this->_module.'proses';
        $url_ubah = $this->_module.'proses?edit=1';

        $api_kelompok_pegawai = $this->_restRm->get('pegawai-ruangan/list-kelompok-pegawai');
        $kelompok_pegawai = json_decode($api_kelompok_pegawai->getBody(), True);
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData($ruangan_id = NULL, $is_return = false, $is_edit = false)
    {
        try {
            $post = $this->_request->post();
            $row = [];

            $response = $this->_restRm->request('POST', 'pegawai-ruangan/index', [
                'query' => ['ruangan_id' => $ruangan_id],
                'form_params' => $post
            ]);
        
            $body = json_decode($response->getBody(),TRUE);
            $no = $this->_request->post('start',1);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $ruangan_id = DocoHelpers::encrypt($value['ruangan_id']);
                $pegawai_id = DocoHelpers::encrypt($value['pegawai_id']);
                $return_data_aktif = '';
                if($is_edit) {
                    $tombol = Html::button('<i class="fa fa-trash"></i>', [
                        'title' => Yii::t('app', 'Hapus'),
                        'action' => Url::to([$this->_module .'delete', 'ruangan_id' => $ruangan_id, 'pegawai_id' => $pegawai_id]),
                        'data-tooltip' => 'tooltip',
                        'data-placement' => 'bottom',
                        'class' => 'btn btn-danger btn-xs data-delete'
                    ]);
                } else {
                    if ($value['is_active'] == 1){
                        $return_data_aktif .= Html::button('<i class="fa fa-times"></i>', [
                            'title' => Yii::t('fe', 'Non aktif'),
                            'action' => Url::to([$this->_module .'aktifasi', 
                                'ruangan_id' => $ruangan_id, 
                                'pegawai_id' => $pegawai_id,
                                'default' => 0
                            ]),
                            'data-popup' => "tooltip",
                            'data-placement' => 'bottom',
                            'data-confirm-message' => Yii::t('fe', 'Confirm Non Aktif'),
                            'class' => 'btn btn-warning btn-xs data-aktifasi'
                        ]);
                    }
                    else {
                        $return_data_aktif .= Html::button('<i class="fa fa-check"></i>', [
                            'title' => Yii::t('fe', 'Aktifkan'),
                            'action' => Url::to([$this->_module .'aktifasi', 
                                'ruangan_id' => $ruangan_id, 
                                'pegawai_id' => $pegawai_id,
                                'default' => 1
                            ]),
                            'data-popup' => "tooltip",
                            'data-placement' => 'bottom',
                            'data-confirm-message' => Yii::t('fe', 'Confirm Aktif'),
                            'class' => 'btn btn-success btn-xs data-aktifasi'
                        ]);
                    }

                    $tombol = Html::button('<i class="fa fa-trash"></i>', [
                        'title' => Yii::t('app', 'Hapus'),
                        'action' => Url::to([$this->_module .'delete', 'ruangan_id' => $ruangan_id, 'pegawai_id' => $pegawai_id]),
                        'data-tooltip' => 'tooltip',
                        'data-placement' => 'bottom',
                        'class' => 'btn btn-danger btn-xs data-delete'
                    ]).'&nbsp;&nbsp;'.$return_data_aktif;
                }
                
                $value['aksi'] = $tombol;
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $this->_request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];

            if($is_return) {
                return $return;
            } else {
                return DocoHelpers::response($return);
            }
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException('');
        } 
        catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException('');
        }
    }

    public function actionGetDataPegawai()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restRm->request('POST', 'pegawai/index',[
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
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }

        
    }

    public function actionProses($edit = 0)
    {
        try {
            $edit = $edit;
            $title = ($edit == 1) ? Yii::t('fe', 'Edit pegawai ruangan') : Yii::t('fe', 'Tambah pegawai ruangan');
            $model = new RuanganPegawaiForm();
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $url_search = $this->_module.'list-pegawai';
            $url_search_popup = $this->_module.'popup';
            $workspace = $this->_workspace;

            if ($this->_request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    try {
                        $response = $this->_restRm->post('pegawai-ruangan/create', [
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
            } 
            else {
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionListPegawai($q = null) 
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '', 'kelompok_pegawai' => '']];

        if (!is_null($q)) {
            $query = $this->_restRm->get('pegawai-ruangan/list-pegawai', [
                'query' => ['q' => $q] 
            ]);
        
            $data = json_decode($query->getBody(), true);

            $out['results'] =  array_values($data['response']);
        } 

        return $out;
    }

    public function actionPopup()
    {
        $title = Yii::t('fe', 'Pegawai');
        $ruangan_id = $this->_ruangan_id;
        return $this->renderPartial('popup', get_defined_vars());
    }

    public function actionAddSession()
    {
        try {
            $post = $this->_request->post();
            $ruangan_pegawai = $post['RuanganPegawaiForm'];
            
            if ($this->_session->has('pegawai_ruangan')) {
                $session_pegawai = $this->_session->get('pegawai_ruangan');
                $post_pegawai_id = $post['RuanganPegawaiForm']['pegawai_id'];
                foreach ($session_pegawai as $key => $value) {
                    if(in_array($post_pegawai_id, $value)) {
                        return DocoHelpers::response(['message' => Yii::t('fe', 'Data pegawai sudah ada')], 500);
                    }
                }
            }
            
            $session_pegawai[] = [
                'ruangan_id' => $ruangan_pegawai['ruangan_id'],
                'ruangan_nama' => $ruangan_pegawai['nama_ruangan'],
                'pegawai_id' => $ruangan_pegawai['pegawai_id'],
                'nama_pegawai' => $ruangan_pegawai['nama_pegawai'],
                'kelompok_pegawai' => $ruangan_pegawai['kelompok_pegawai'],
            ];

            $session_pegawai = $this->_session->set('pegawai_ruangan', $session_pegawai);

            return $this->actionGetDataSession($session_pegawai);

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } 
        catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataSession($is_return = false)
    {
        try {
            if ($this->_session->has('pegawai_ruangan')){
                $session_pegawai = $this->_session->get('pegawai_ruangan');
            }else{
                return DocoHelpers::dataTabelsException();
            }

            $data_tables = $this->generateTableSession($session_pegawai, $is_return);

            return DocoHelpers::response($data_tables);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException('');
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException('');
        }
    }

    private function generateTableSession($session_pegawai = [], $is_return = false)
    {
        try {
            $row = [];
            $no = 0;
            foreach ($session_pegawai as $key => $value) {
                $no++;
                $tombol = Html::button('<i class="fa fa-trash"></i>', [
                    'value' => $key,
                    'action' => $this->_module.'delete-session?id='.DocoHelpers::encrypt($key),
                    'title' => Yii::t('app', 'Hapus'),
                    'data-tooltip' => 'tooltip',
                    'class' => 'btn btn-danger btn-xs delete-session'
                ]);

                $value['aksi'] = $tombol;
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $this->_request->post('draw', 1),
                'recordsTotal' => count($row),
                'recordsFiltered' => count($row)
            ];

            if($is_return) {
                return $return;
            } else {
                return DocoHelpers::response($return);
            }
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionSave()
    {
        $errors = Yii::t('fe', 'Terdapat kesalahan');
        try {
            $session_pegawai = [];
            if ($this->_session->has('pegawai_ruangan')){
                $session_pegawai = $this->_session->get('pegawai_ruangan');
            } else{
                return DocoHelpers::responseTemplate(500, Yii::t('fe', 'Error'), $errors);
            }

            // $pesan_sukses_simpan = ['pesan_sukses_simpan' => Yii::t('fe', 'Sukses simpan')];
            // $session_pegawai = array_merge($session_pegawai, $pesan_sukses_simpan);
            // var_dump($session_pegawai);die;
            $response = $this->_restRm->post('pegawai-ruangan/create', [
                    'form_params' => $session_pegawai,
            ]);

            $body = json_decode($response->getBody(), true);

            if ($this->_session->has('pegawai_ruangan')){
                $this->_session->remove('pegawai_ruangan');
            }
            
            return DocoHelpers::response($body, false, true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionDelete($ruangan_id, $pegawai_id)
    {
        $ruangan_id = DocoHelpers::decrypt($ruangan_id);
        $pegawai_id = DocoHelpers::decrypt($pegawai_id);

        try {
            $response = $this->_restRm->delete('pegawai-ruangan/delete?ruangan_id='.$ruangan_id.'&pegawai_id='.$pegawai_id);
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

    public function actionDeleteSession($id)
    {
        try {
            
            $id = DocoHelpers::decrypt($id);
            if ($this->_session->has('pegawai_ruangan')){
                $session_pegawai = $this->_session->get('pegawai_ruangan');
                unset($session_pegawai[$id]);
                $this->_session->set('pegawai_ruangan', $session_pegawai);
                $data['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil dihapus'
                ];
                return DocoHelpers::response($data);
            }
            
        } catch (Exception $e) {
            
        }
    }

    public function actionGetDataAll()
    {
        if ($this->_session->has('pegawai_ruangan')) {
            $return_real_pegawai_ruangan = $this->actionGetData($this->_ruangan_id, true, true);
            $return_session = $this->actionGetDataSession(true);
            
            $merge_data = array_merge($return_real_pegawai_ruangan['data'], $return_session['data']);

            $return = [
                'data' => $merge_data,
                'recordsTotal' => $return_real_pegawai_ruangan['recordsTotal'] + $return_session['recordsTotal'],
                'recordsFiltered' => $return_real_pegawai_ruangan['recordsFiltered'] + $return_session['recordsFiltered'],
            ];

            return DocoHelpers::response($return);
        } else{
            return $this->actionGetData($this->_ruangan_id, false, true);
        }
    }

    public function actionGetDataAjax()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRm->request('GET', 'pegawai-ruangan/ajax');
            $row = [];
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_pegawai' => $body['response']['data-pegawai'],
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionAktifasi($ruangan_id, $pegawai_id, $default)
    {
        $ruangan_id = DocoHelpers::decrypt($ruangan_id);
        $pegawai_id = DocoHelpers::decrypt($pegawai_id);

        try {
            $response = $this->_restRm->put('pegawai-ruangan/update?ruangan_id='.$ruangan_id.'&pegawai_id='.$pegawai_id, [
                        'form_params' => ['is_active' => $default]
                    ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil diubah'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}