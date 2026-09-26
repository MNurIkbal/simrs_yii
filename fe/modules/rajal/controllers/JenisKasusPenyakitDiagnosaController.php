<?php

/**
 * @Author: afil
 * @Date:   2018-01-09 13:47:17
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 16:29:41
 * @Description: 
 */
namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rajal\models\JenisKasusPenyakitForm;
use app\modules\rajal\models\KasusPenyakitDiagnosaForm;
use app\modules\rajal\models\DiagnosaForm;
use GuzzleHttp\Exception\RequestException;

class JenisKasusPenyakitDiagnosaController extends DocoController
{
    protected $_title = "Master :: Jenis kasus penyakit diagnosa";
    protected $_module = '/rajal/jenis-kasus-penyakit-diagnosa';
    protected $_restRajal;
    protected $_id_ruangan;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
    }

    public function actionIndex()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $status = $this->_status;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $post['ruangan_id'] = $this->_id_ruangan;

            $response = $this->_restRajal->request('POST', 'kasus-penyakit-diagnosa/index', [
                            'form_params' => $post
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->post('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $id_ruangan = DocoHelpers::encrypt($value['ruangan_id']);
                $id_jeniskasuspenyakit = DocoHelpers::encrypt($value['jeniskasuspenyakit_id']);

                $value['aksi'] = $this->getAksi($value, $request->get('action'));

                unset($value['ruangan_id']);
                unset($value['jeniskasuspenyakit_id']);
                
                $value['status'] = DocoHelpers::isActive($value['is_active']);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Tambah data');
        $sub_title = Yii::t('fe', 'Kasus penyakit diagnosa');
        $id_ruangan = $this->_id_ruangan;
        $action = 'create';
        $session = Yii::$app->session;
        $modelKasuspenyakitdiagnosa = new KasusPenyakitDiagnosaForm;

        $data_kasus = $this->getJenisKasusPenyakit();
        $data_diagnosa = $this->getDiagnosa();

        $form_name = substr(strrchr(get_class($modelKasuspenyakitdiagnosa), "\\"), 1);

        if ($request->post()) {
            $modelKasuspenyakitdiagnosa->load($request->post());

            if ($modelKasuspenyakitdiagnosa->validate()) {
                try {
                    $response = $this->addSessionKasus($modelKasuspenyakitdiagnosa->attributes);

                    return DocoHelpers::responseTemplate(200, 'OK');
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($modelKasuspenyakitdiagnosa->errors, $form_name);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            // unset session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session->remove('kasus_penyakit_diagnosa');
            }

            return $this->render('form', get_defined_vars());
        }
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah data');
        $sub_title = Yii::t('fe', 'Kasus penyakit diagnosa');
        $id_ruangan = $this->_id_ruangan;
        $action = 'update';
        $session = Yii::$app->session;
        $modelKasuspenyakitdiagnosa = new KasusPenyakitDiagnosaForm;

        $data_kasus = $this->getJenisKasusPenyakit();
        $data_diagnosa = $this->getDiagnosa();

        $form_name = substr(strrchr(get_class($modelKasuspenyakitdiagnosa), "\\"), 1);

        if ($request->post()) {
            $modelKasuspenyakitdiagnosa->load($request->post());
            if ($modelKasuspenyakitdiagnosa->validate()) {
                try {
                    $response = $this->addSessionKasus($modelKasuspenyakitdiagnosa->attributes);

                    return DocoHelpers::responseTemplate(200, 'OK');
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($modelKasuspenyakitdiagnosa->errors, $form_name);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            // unset session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session->remove('kasus_penyakit_diagnosa');
            }

            return $this->render('form', get_defined_vars());
        }
    }

    public function actionDelete($id_diagnosa, $id_jeniskasuspenyakit)
    {
        $id_diagnosa = DocoHelpers::decrypt($id_diagnosa);
        $id_jeniskasuspenyakit = DocoHelpers::decrypt($id_jeniskasuspenyakit);

        try {
            $response = $this->_restRajal->delete('kasus-penyakit-diagnosa/delete?id_diagnosa='.$id_diagnosa.'&id_jeniskasuspenyakit='.$id_jeniskasuspenyakit);
            $response = json_decode($response->getBody(),true);

            return DocoHelpers::responseTemplate(
                    200, 
                    Yii::t('fe', 'message_hapus'), 
                    [], 
                    ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_sukses_hapus')]
                );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionNonActive($id_diagnosa, $id_jeniskasuspenyakit, $default)
    {
        $id_diagnosa = DocoHelpers::decrypt($id_diagnosa);
        $id_jeniskasuspenyakit = DocoHelpers::decrypt($id_jeniskasuspenyakit);

        try {
            $response = $this->_restRajal->put('kasus-penyakit-diagnosa/update?id_diagnosa='.$id_diagnosa.'&id_jeniskasuspenyakit='.$id_jeniskasuspenyakit, [
                        'form_params' => ['is_active' => $default]
                    ]);
            $response = json_decode($response->getBody(),true);

            return DocoHelpers::responseTemplate(
                    200, 
                    Yii::t('fe', 'message_batal'), 
                    [], 
                    ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_ubah')]
                );
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataSession()
    {
        $session = Yii::$app->session;

        try {
            // get/set session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session_kasus = $session->get('kasus_penyakit_diagnosa');
                
                $data_tables = $this->generateTableKasus($session_kasus);
                return DocoHelpers::response($data_tables);
            }else{
                echo DocoHelpers::dataTabelsException('');
            }

        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException('');
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException('');
        }
    }

    public function actionSaveDataKasus()
    {
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $modelKasuspenyakitdiagnosa = new KasusPenyakitDiagnosaForm;

            // get/set session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session_kasus = $session->get('kasus_penyakit_diagnosa');
            }else{
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }

            $data_kasus = [];
            foreach ($session_kasus as $key => $value) {
                array_push($data_kasus, array_values($value));
            }

            $response = $this->_restRajal->post('kasus-penyakit-diagnosa/create', [
                    'form_params' => $session_kasus
                ]);
            $response = json_decode($response->getBody(), true);

            // unset session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session->remove('kasus_penyakit_diagnosa');
            }
            
            return DocoHelpers::response($response, false, true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetDataAll()
    {
        $session = Yii::$app->session;

        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $post['ruangan_id'] = $this->_id_ruangan;
            $response = $this->_restRajal->request('POST', 'kasus-penyakit-diagnosa/index', [
                            'form_params' => $post
                        ]);

            $rows = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->post('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $row['jeniskasuspenyakit_nama'] = $value['jeniskasuspenyakit_nama'];
                $row['diagnosa_nama'] = $value['diagnosa_kodenama'];
                $row['aksi'] = $this->getAksi($value, $request->get('action'));
                
                array_push($rows, $row);
            }

            $return = [
                'data' => $rows,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }

        try {
            // get/set session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session_kasus = $session->get('kasus_penyakit_diagnosa');
            }else{
                return DocoHelpers::response($return);
            }

            $data_tables = $this->generateTableKasus($session_kasus);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException('');
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException('');
        }
 
        $merge_data = array_merge($return['data'], $data_tables['data']);
        $merge_recordstotal = $return['recordsTotal'] + $data_tables['recordsTotal'];
        $merge_recordsfiltered = $return['recordsFiltered'] + $data_tables['recordsFiltered'];

        $return['data'] = $merge_data;
        $return['recordsTotal'] = $merge_recordstotal;
        $return['recordsFiltered'] = $merge_recordsfiltered;

        return DocoHelpers::response($return);
    }

    public function actionBatalDataKasus()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $diagnosa = DocoHelpers::decrypt($request->get('diagnosa'));
            $kasus = DocoHelpers::decrypt($request->get('kasus'));

            // get/set session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session_kasus = $session->get('kasus_penyakit_diagnosa');

                $temp_kasus = [];
                foreach ($session_kasus as $key => $value) {
                    if (($value['diagnosa_id'] == $diagnosa) && ($value['jeniskasuspenyakit_id'] == $kasus)){
                        unset($session_kasus[$key]);
                    }
                }

                $session->set('kasus_penyakit_diagnosa', $session_kasus);
            }else{
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }
            
            return DocoHelpers::responseTemplate(
                    200, 
                    Yii::t('fe', 'message_batal'), 
                    [], 
                    ['title' => 'Berhasil!', 'text' => Yii::t('fe', 'message_batal')]
                );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     *
     * private function
     *
     */

    private function getJenisKasusPenyakit()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('jenis-kasus-penyakit/ajax?ruangan_id='.$this->_id_ruangan);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);

            $return = $body['response']['data-kasus'];
            return $return;
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    
    private function getAksi($data, $action = 'index')
    {
        $id_ruangan = DocoHelpers::encrypt($data['ruangan_id']);
        $id_diagnosa = DocoHelpers::encrypt($data['diagnosa_id']);
        $id_jeniskasuspenyakit = DocoHelpers::encrypt($data['jeniskasuspenyakit_id']);

        switch ($action) {
            case 'create':
                $return_data = Html::a(
                    '<i class="fa fa-trash"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::to([$this->_module .'/delete', 'id_diagnosa' => $id_diagnosa, 'id_jeniskasuspenyakit' => $id_jeniskasuspenyakit]),
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Hapus'),
                    ]
                );
                break;

            case 'update':
                $return_data = Html::a(
                    '<i class="fa fa-trash"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::to([$this->_module .'/delete', 'id_diagnosa' => $id_diagnosa, 'id_jeniskasuspenyakit' => $id_jeniskasuspenyakit]),
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Hapus'),
                    ]
                );
                break;

            default:
                $return_data = Html::a(
                    '<i class="fa fa-trash"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::to([$this->_module .'/delete', 'id_diagnosa' => $id_diagnosa, 'id_jeniskasuspenyakit' => $id_jeniskasuspenyakit]),
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Hapus'),
                    ]
                );

                $return_data .= '&nbsp;&nbsp;';

                if ($data['status_aktif'] == 1){
                    $return_data .= Html::a(
                        '<i class="fa fa-times-circle-o"></i>', '#', [
                            'class' => 'btn btn-danger btn-xs data-aktifasi',
                            'action' => Url::to([$this->_module .'/non-active', 'id_diagnosa' => $id_diagnosa, 'id_jeniskasuspenyakit' => $id_jeniskasuspenyakit, 'default' => 0]),
                            'data-popup' => "tooltip",
                            'data-placement' => 'bottom',
                            'data-original-title' => Yii::t('fe', 'Non aktifkan'),
                        ]
                    );
                }else{
                    $return_data .= Html::a(
                        '<i class="fa fa-check-square-o"></i>', '#', [
                            'class' => 'btn btn-success btn-xs data-aktifasi',
                            'action' => Url::to([$this->_module .'/non-active', 'id_diagnosa' => $id_diagnosa, 'id_jeniskasuspenyakit' => $id_jeniskasuspenyakit, 'default' => 1]),
                            'data-popup' => "tooltip",
                            'data-placement' => 'bottom',
                            'data-original-title' => Yii::t('fe', 'Aktifkan'),
                        ]
                    );
                }
                break;
        }

        return $return_data;
    }

    private function getDiagnosa()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('diagnosa/ajax');
            $row = [];
            $body = json_decode($response->getBody(),TRUE);

            $return = $body['response']['data-diagnosa'];
            return $return;
        } catch (RequestException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function generateTableKasus($datas = [])
    {
        // get data
        $request = Yii::$app->request;

        // get data from db
        $response_jeniskasuspenyakit = $this->_restRajal->get('jenis-kasus-penyakit/index');
        $response_diagnosa = $this->_restRajal->get('diagnosa/ajax');
        $body_jeniskasuspenyakit = json_decode($response_jeniskasuspenyakit->getBody(),TRUE);
        $body_diagnosa = json_decode($response_diagnosa->getBody(),TRUE);
        $data_jeniskasuspenyakit = $body_jeniskasuspenyakit['response']['data'];
        $data_diagnosa = $body_diagnosa['response']['data-diagnosa'];

        $data_tables = [];
        $no = $request->post('start',1);
        foreach ($datas as $data) {
            $id_diagnosa = DocoHelpers::encrypt($data['diagnosa_id']);
            $id_jeniskasuspenyakit = DocoHelpers::encrypt($data['jeniskasuspenyakit_id']);

            foreach ($data_jeniskasuspenyakit as $key_kp => $value_kp) {
                if ($data['jeniskasuspenyakit_id'] == $value_kp['jeniskasuspenyakit_id']){
                    $data_table['jeniskasuspenyakit_nama'] = $value_kp['jeniskasuspenyakit_nama'];
                    break;
                }
            }

            foreach ($data_diagnosa as $key_d => $value_d) {
                if ($data['diagnosa_id'] == $value_d['diagnosa_id']){
                    $data_table['diagnosa_nama'] = $value_d['diagnosa_nama'];
                    break;
                }
            }

            $data_table['aksi'] = Html::button(
                    '<i class="fa fa-times"></i>', [
                        'class' => 'btn btn-indian-red btn-xs delete-kasus',
                        'action' => '/rajal/jenis-kasus-penyakit-diagnosa/batal-data-kasus?diagnosa='.$id_diagnosa.'&kasus='.$id_jeniskasuspenyakit,
                        'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Batal'),
                    ]
                );

            array_push($data_tables, $data_table);
        }

        $return = [
            'data' => $data_tables,
            'draw' => $request->post('draw'),
            'recordsTotal' => count($data_tables),
            'recordsFiltered' => count($data_tables)
        ];

        return $return;
    }

    private function addSessionKasus($dataModel = false)
    {
        $session = Yii::$app->session;

        try {
            // get/set session
            if ($session->has('kasus_penyakit_diagnosa')){
                $session_kasus = $session->get('kasus_penyakit_diagnosa');
            }

            $session_kasus[] = [
                'diagnosa_id' => $dataModel['diagnosa_id'],
                'jeniskasuspenyakit_id' => $dataModel['jeniskasuspenyakit_id'],
            ];
            $session->set('kasus_penyakit_diagnosa', $session_kasus);

            return DocoHelpers::responseTemplate(200,
                '');
        } catch (Exception $e) {
            return DocoHelpers::responseTemplate(500,
                $e);
        }


    }
}