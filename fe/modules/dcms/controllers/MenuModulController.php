<?php


namespace Doco\dcms\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use Doco\dcms\models\MenuModulMenu;

class MenuModulController extends DocoController
{

    protected $_title = "Modul";
    public $_module = '/dcms/menu-modul';
    protected $_tmpMenu = [];
    protected $_parentMenu = [];
    protected $_html = '';
    protected $_restMaster;
    
    public $_keyParent;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        try {
            $session = Yii::$app->session;
            $status = $this->_status;

            $response = $this->_restMaster->request('GET', 'menu/');
            $body = json_decode($response->getBody(),true);
            $response = $body['response'];

            $menus = $this->parsingMenu($response);
            $title = $this->_title;
            return $this->render('index',get_defined_vars());
        } catch (RequestException $e) {
            $message = $e->getResponse()->getBody()->getContents();
            throw new \yii\web\ServerErrorHttpException($message);
        }

    }

    public function actionGetController()
    {
        try {
            if (isset($_POST['depdrop_parents'])) {
                $parents = $_POST['depdrop_parents'];
                if ($parents != null) {
                    $id = $parents[0];
                    if (is_array($id)) {
                        $result = [
                            'data_collect' => [],
                            'output' => [],
                            'selected' => null
                        ];
                        $test = [];
                        foreach ($id as $key => $value) {
                            try {
                                $response = Yii::$app->docoRest->{$value}->request('GET','allow/get-list-controllers');
                                $body = json_decode($response->getBody(),true);
                            } catch (RequestException $e) {
                                $body['response'] = [];
                            }
                            $result = ArrayHelper::merge($result, $body['response']);
                        }
                        return DocoHelpers::response($result);
                    } else {
                        if (!empty($id)) {
                            $response = Yii::$app->docoRest->{$id}->request('GET','allow/get-list-controllers');
                            $body = json_decode($response->getBody(),true);
                            return DocoHelpers::response($body['response']);
                        }
                        return DocoHelpers::response([]);
                    }
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionCreate($id_parent = null)
    {
        try {
            $request = Yii::$app->request;
            $model = new MenuModulMenu;
            $title = Yii::t('fe','Tambah Menu');
            $id_parent = DocoHelpers::decrypt($id_parent);
            if ($request->post()) {
                $model->load($request->post());
                $model->menu_nama = $model->menu_namalainnya;
                $model->menu_key = DocoHelpers::encrypt($model->menu_namalainnya);
                $model->kelmenu_id = $id_parent;
                if (!$request->post('list_api')) {
                    $model->controller_name = '-';
                }
                $model->akses = isset($_POST['akses']) ? $_POST['akses'] : [];

                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'menu/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                } else {
                    $response = $model->errors;
                }
                return DocoHelpers::response($response,422,'MenuModulMenu');
            } else {
                $listApi = $this->getListApi();
                return $this->renderAjax('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id_parent)
    {
        try {
            $title = Yii::t('fe','Update Menu');
            $model = new MenuModulMenu;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id_parent = DocoHelpers::decrypt($id_parent);
            $is_update = true;
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'menu/update',[
                                        'query' => ['id' => $id_parent],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    $response['response']['flag'] = 'update';
                } else {
                    $response = $model->errors;
                }
                return DocoHelpers::response($response,422,'MenuModulMenu');
            } else {
                $response = $this->_restMaster->request('GET', 'menu/view',[
                                'query' => ['id' => $id_parent]
                            ]);
                $result = json_decode($response->getBody(),true);
                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    $listApi = $this->getListApi();
                    return $this->renderAjax('form',get_defined_vars());
                } else {
                    return $this->actionCreate();
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }

    }

    public function actionGetListApi($id_menu)
    {
        $request = Yii::$app->request;
        $id_menu = DocoHelpers::decrypt($id_menu);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->request('GET', 'menu/get-child',[
                            'query' => ['id_menu' => $id_menu]
                        ]);
            $body = json_decode($response->getBody(), TRUE);
            $no = $request->get('start',1);
            $groupByService = [];

            foreach ($body['response'] as $val) {
                $secretKey = DocoHelpers::decrypt($val['menu_key']);
                $explode = explode("-", $secretKey);
                // name service
                $ns = isset($explode[0]) ? $explode[0] : '';
                // name Controller
                $nc = isset($explode[1]) ? str_replace(" ", "", $explode[1]) : '';
                $nameOfService = ucfirst($ns) . '-' . ucfirst($nc);
                $groupByService[$nameOfService][] = $val;
            }
            $checkService = [];
            foreach ($groupByService as $key => $value) {
                $no++;
                $service = explode("-", $key);
                $nameOfService = isset($service[0]) ? $service[0] : '';
                $nameOfController = isset($service[1]) ? $service[1] : '';
                $listAction = $nameOfController;
                foreach ($value as $value) {
                    $listAction .= '<li>' . $value['menu_nama'] . '</li>';
                }

                $listAction .= '<input type="hidden" class="list-service" value="'.$nameOfService . '-' . $nameOfController.'">';
                $data[] = [
                    'rowNum' => $no,
                    'service' => $nameOfService,
                    'controller_action' => $listAction
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
    ** untuk Sync ke menu backend  pada menu frontend
    **/
    public function actionSyncListApi($id_menu)
    {
        $request = Yii::$app->request;
        $id_menu = DocoHelpers::decrypt($id_menu);
        $service = $request->post('service');
        if ($request->post() && $service) {
            // Harus Grouping berdassarakan service
            $listOfService = $listFind = [];
            foreach ($service as $val) {
                $nameService = explode("-", $val);
                $service = isset($nameService[0]) ? $nameService[0] : false;
                $controller = isset($nameService[1]) ? $nameService[1] : false;
                if (!in_array($service, $listOfService) && $service) {
                    $listOfService[] = $service;
                }
                $listFind[] = strtolower($service) . '-' . $controller;
            }
            $result = [
                'data_collect' => [],
                'output' => [],
                'selected' => null
            ];
            foreach ($listOfService as $val) {
                try {
                    $serviceName = strtolower($val);
                    $response = Yii::$app->docoRest->{$serviceName}->request('GET','allow/get-list-controllers');
                    $body = json_decode($response->getBody(),true);
                } catch (RequestException $e) {
                    $body['response'] = [];
                }
                $result = ArrayHelper::merge($result, $body['response']);
            }
            $updateApi = [];
            foreach ($listFind as $val) {
                if (isset($result['data_collect'][$val])) {
                    $updateApi[] = $result['data_collect'][$val];
                }
            }

            $response = [];
            try {
                $response = $this->_restMaster->request('POST','menu/sync-list-api',[
                                        'form_params' => [
                                            'akses' => $updateApi
                                        ],
                                        'query' => [
                                            'id_menu' => $id_menu
                                        ]
                                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response(['response' => $response['response']]);
            } catch (Exception $e) {
                return DocoHelpers::response(['message' => $e->getMessage()],422);
            }

        }
    }

    public function actionGroupingMenus($id_parent)
    {
        try {
            $id_parent = DocoHelpers::decrypt($id_parent);
            $request = Yii::$app->request;
            $data_menu = $data_grouping =  [];
            if ($post = $request->post()) {
                $decode = json_decode($post['data_menu'],true);
                $data_menu = is_array($decode) ? $decode : [];
                $response = $this->_restMaster->request('POST', 'menu/grouping-menus',[
                                    'form_params' => [
                                        'data_menu' => $data_menu
                                    ],
                                    'query' => [
                                        'id_parent' => $id_parent
                                    ]
                            ]);
                $response = json_decode($response->getBody(),true);
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetRenderMenus($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restMaster->request('POST', 'menu/view',[
                    'form_params' => [
                        'kelmenu_id' => $id
                    ]
            ]);
            $response = json_decode($response->getBody(),true);
            $this->_keyParent = $id;
            $this->childMenu($response['response']);
            $html = '<ol class="dd-list">';
            $html .= $this->renderMenus();
            $html .= '</ol>';
            return $html;
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDelete($id = null)
    {
        try {
            $id = DocoHelpers::decrypt($id);

            $response = $this->_restMaster->request('DELETE', 'menu/delete',[
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
        }
    }

    public function renderMenus($id_parent = 0)
    {  
        if (!$id_parent) {
            $data = isset($this->_tmpMenu[$id_parent][$this->_keyParent]) 
                ? $this->_tmpMenu[$id_parent][$this->_keyParent] 
                : [];
        } else {
            $data = isset($this->_tmpMenu[$id_parent]) 
                ? $this->_tmpMenu[$id_parent]
                : [];
        }

        $this->_html = $this->renderPartial('render-menu',get_defined_vars());

        return $this->_html;
    }

    public function actionSyncMenu()
    {
        $baseApp = Yii::getAlias('@app');
        $request = Yii::$app->request;
        $ini = @parse_ini_file($baseApp .'/config/env/.api');
        $response['status'] = false;
        if ($ini) {
            $no = 1;
            $response['total_step'] = count($ini);
            $total_progres = 100 / count($ini);
            if (count($ini) >= $request->post('step',0)) {
                foreach ($ini as $key => $value) {
                    try {
                        $scheme = parse_url($value, PHP_URL_SCHEME) .'://';
                        $scheme .= parse_url($value, PHP_URL_HOST);
                        $port = parse_url($value,PHP_URL_PORT);
                        
                        if ($port) {
                            $scheme .= ':' . $port;
                        }

                        if ($no == $request->post('step',0)) {
                            $curl = Yii::$app->docoRest->{$key};
                            $default = [
                                'create-module' => 1
                            ];

                            if ($request->post('step',0) == count($ini)) {
                                $default['flag'] = 1;
                            }

                            $rest = $curl->request('GET', $scheme . '/site/config',[
                                                'query' => $default,
                                        ]);
                            $response['step'] = $no + 1;
                            $response['status'] = true;
                            $response['key'] = $key;
                            $response['progres'] = round($total_progres * $no);
                            break;
                        }
                        $no++;
                    } catch (RequestException $e) {
                        return DocoHelpers::response(['message' => $e->getMessage()],500);
                    }
                }
            }
        }

        return DocoHelpers::response($response);
    }

    private function getListApi()
    {
        $baseApp = Yii::getAlias('@app');
        $ini = @parse_ini_file($baseApp .'/config/env/.api');
        $listOfApi = [
            '' => Yii::t('fe','Pilih')
        ];

        if ($ini) {
            foreach ($ini as $key => $value) {
                $listOfApi[$key] = strtoupper($key);
            }
        }

        return $listOfApi;
    }

    /**
    * @param array $response
    *
    * @return void
    **/

    private function parsingMenu(array $response)
    {
        $parent_menus = $parent_group_menus = [];
        foreach ($response as $value) {
            $val_parent = $value;
            unset($val_parent['menuModul']);
            $parent_menus[$value['kelmenu_id']] = $val_parent;
            $this->childMenu($value['menuModul']);
        }

        $this->_parentMenu = $parent_menus;
    }

    private function childMenu(array $data)
    {
        $data_array = [];
        foreach ($data as $item) {
            if (!$item['groupmenu_id']) {
                $this->_tmpMenu[0][$item['kelmenu_id']][$item['menu_id']] = $item;
            } else {
                $this->_tmpMenu[$item['groupmenu_id']][$item['menu_id']] = $item;
            }
        }

    }
}