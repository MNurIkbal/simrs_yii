<?php


namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\CaraBayarForm;
use GuzzleHttp\Exception\RequestException;
use Doco\dcms\models\PeranPenggunaForm;

class PeranPenggunaController extends DocoController
{
    protected $_title = "Peran Pengguna";
    protected $_module = 'peran-pengguna/';
    protected $_restMaster;
    protected $_restDcms;
    protected $_statusActive;
    public $_html = '';

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restDcms = Yii::$app->docoRest->dcms;
        $this->_statusActive = [1 => 'Aktif', 0 => 'Tidak Aktif'];
    }

    public function actionIndex()
    {
        // Init
        $status = $this->_statusActive; 
        $options = $this->_options;
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {

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
            $response = $this->_restDcms->get('peran-pengguna?'.http_build_query($yiiRestfulParams), 
                                [
                                    'form_params' => []
                                ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['peranpengguna_id']);
                $value['primary'] = $primaryKey;
                unset($value['peranpengguna_id']);

                /* $value['aksi'] = Html::a(
                        '<i class="fa fa-pencil" aria-hidden="true"></i>',
                            Url::to([$this->_module. 'update','id' => $primaryKey])
                            , [
                            'class' => 'btn btn-dark-turquise btn-xs data-update',
                            'style' => 'margin-right:5px',
                            'data-popup' => "tooltip",
                            'data-original-title' => \Yii::t('fe', 'Ubah'),
                        ]
                    );
                $value['aksi'] .= Html::a(
                    '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                        'class' => 'btn btn-danger btn-xs data-delete',
                        'action' => Url::to([$this->_module .'delete','id' => $primaryKey]),
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Hapus'),
                    ]
                );*/

                // $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['is_active'] = ($value['peranpengguna_aktif']) ? 'Aktif' : 'Tidak Aktif';
                $value['rowNum'] = $no;
                $value['module_nama'] = isset($value['modul']['modul_namalainnya']) 
                                        ? $value['modul']['modul_namalainnya'] : '';
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

    public function actionCreate()
    {
        try {
            $model = new PeranPenggunaForm;
            $request = Yii::$app->request;
            $id = null;
            if ($request->post()) {
                $model->load($request->post());
                $post = $model->attributes;
                $post['action_akses'] = $request->post('action_akses');
                $kelompokmenu_id = $request->post('kelompokmenu_id',[]);
                $kel_id = [];
                foreach ($kelompokmenu_id as $value) {
                    if(!empty($value)) {
                        $kel_id[] = $value;
                    }
                }

                $post['kelompokmenu_id'] = $kel_id;
                if ($model->validate()) {
                    $response = $this->_restDcms->post('peran-pengguna/create', 
                            [
                                'form_params' => $post
                            ]
                        );
                    $response = json_decode($response->getBody(),true);
                } else {
                    $response = $model->errors;
                }
                return DocoHelpers::response($response,422,'PeranPenggunaForm');
            } else {
                $title = 'Tambah ' . $this->_title;
                $response = $this->_restDcms->get('peran-pengguna/create', 
                        [
                            'form_params' => []
                        ]
                    );
                $response = json_decode($response->getBody(),true);
                $options_modul = isset($response['response']['modul']) 
                                    ? $response['response']['modul']
                                    : [];
                $is_active = $this->_statusActive;
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id = null)
    {
        try {
            $model = new PeranPenggunaForm;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                $post = $model->attributes;
                $post['action_akses'] = $request->post('action_akses');
                $kelompokmenu_id = $request->post('kelompokmenu_id',[]);
                $kel_id = [];
                foreach ($kelompokmenu_id as $value) {
                    if(!empty($value)) {
                        $kel_id[] = $value;
                    }
                }

                $post['kelompokmenu_id'] = $kel_id;
                if ($model->validate()) {
                    $response = $this->_restDcms->post('peran-pengguna/update', 
                            [
                                'form_params' => $post,
                                'query' => ['id' => $id],
                            ]
                        );
                    $response = json_decode($response->getBody(),true);
                } else {
                    $response = $model->errors;
                }
                return DocoHelpers::response($response,422,'PeranPenggunaForm');
            } else {
                $title = Yii::t('fe', 'Ubah ') . $this->_title;
                $response = $this->_restDcms->get('peran-pengguna/update', 
                        [
                            'query' => ['id' => $id],
                        ]
                    );
                $response = json_decode($response->getBody(),true);

                $options_modul = $response['response']['modul'];
                $peran_pengguna = $response['response']['peran_pengguna'];
                $peran_pengguna['is_exception'] = $peran_pengguna ? 1 : 0;
                $model->attributes = $peran_pengguna;
                $is_active = $this->_statusActive;
                return $this->render('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['error' => $e->getMessage()],500);
        }
    }

    public function actionDelete($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restDcms->request('DELETE', 'peran-pengguna/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => isset($response['response']['title']) 
                                ? $response['response']['title'] 
                                : 'Proses Berhasil !',
                'text' => isset($response['response']['text']) 
                                ? $response['response']['text'] 
                                : 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionLoadContent($id_modul,$id_parent = null)
    {
        try {
            $response = $this->_restDcms->get('peran-pengguna/get-menus', 
                    [
                        'query' => [
                            'id_modul' => $id_modul,
                            'id_parent' => $id_parent
                        ],
                    ]
                );
            $response = json_decode($response->getBody(),true);
            $data_menu = isset($response['response']['kelompok_menu']) 
                                ? $response['response']['kelompok_menu']
                                : [];
            $akses_pengguna = isset($response['response']['akses_pengguna']) 
                                ? $response['response']['akses_pengguna']
                                : [];
            $akses_pengguna = array_keys($akses_pengguna);
        } catch (RequestException $e) {
           $data_menu = $akses_pengguna = [];
        }
        return $this->renderPartial('role-menu', get_defined_vars());
    }

    public function renderMenu(array $data, array $akses_pengguna)
    {
        foreach ($data as $key => $value) {
            if (empty($value['modul_id']) && isset($value['item'])) {
                $this->renderMenu($value['item'],$akses_pengguna);
            } else {
                $this->_html .= $this->renderPartial('render-tr',get_defined_vars());
                if (isset($value['item']) && count($value['item'])) {
                    $this->renderMenu($value['item'],$akses_pengguna);
                }
            }
        }

        return $this->_html;
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $cache = [];
        $path = Yii::getAlias("@download") . "/peran-pengguna.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restDcms->get('peran-pengguna/export-pdf?'. http_build_query($yiiRestfulParams), [
                'form_params' => $cache,
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) { 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $type = $request->get('type');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "";
        try {
            $response = $this->_restDcms->get('peran-pengguna/export-excel?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            return $this->downloadFile($body["response"]);
        } catch (RequestException $e) {
            return $e;
        } catch (\Exception $e) {
            return $e;
        }
    }

    private function downloadFile($filename)
    {
        $file = basename($filename);
        $fp = fopen($file, 'w');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}