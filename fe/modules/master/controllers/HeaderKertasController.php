<?php
// Author : JohnDoe

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\DocHeaderForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class HeaderKertasController extends DocoController
{
    protected $_title = 'Master Header Kertas';
    protected $_module = 'master/header-kertas/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
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
        // $status = $this->_status;
        // $options = $this->_options;
        $title = $this->_title;
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
            $response = $this->_restMaster->get('header-kertas/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = $value['docheader_id'];
                $value['rowNum'] = $no;
                $value['logokiri'] = Html::img('/'.$value['logo_kiri'], ['width' => '50px', 'height' => '50px']);
                $value['logokanan'] = Html::img('/'.$value['logo_kanan'], ['width' => '50px', 'height' => '50px']);
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

    public function actionCreate()
    {
        try {
            $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
            $model = new DocHeaderForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $request = Yii::$app->request;
            if ($request->post()) {
                $model->load($request->post());
                $post = $request->post('DocHeaderForm');
                $image = '';
                $path_logo_kiri = '';
                $path_logo_kanan = '';
                $logo = '';

                if(preg_match_all('/src\s*=\s*"(.+?)"/', $post['template_header']['new'], $output)) {
                    $image = $output;
                    $logo = [];
                    foreach ($image[0] as $key => $value) {
                        $exp = explode('src="'.Url::home(true).'', $value);
                        list($src, $path) = $exp;
                        list($filePath, $filename) = explode('uploads/logo/', $path);
                        $logo[] = str_replace('"', '', $path.'*'.$filename);
                    }

                    if(isset($logo[0])){
                        list($path_logo_kiri, $filename_logo_kiri) = explode('*', $logo[0]);
                    }
                    if(isset($logo[1])){
                        list($path_logo_kanan, $filename_logo_kanan) = explode('*', $logo[1]);
                    }
                }

                $model->attributes = $post;
                $model->logo_kiri = $path_logo_kiri;
                $model->logo_kanan = $path_logo_kanan;
                $model->template_header = $post['template_header']['new'];
                $model->logo = $logo;
                if ($model->validate()) {
                    $response = $this->_restMaster->post('header-kertas/create', [
                        'form_params' => $model
                    ]);
                    // var_dump(json_decode($response->getBody(),TRUE));exit;
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } else {
                    return DocoHelpers::response($model->errors,422, 'DocHeaderForm');
                }
            } else {
                $api = $this->_restMaster->get('header-kertas/generate-api');
                $api = json_decode($api->getBody(), True);
                $template_header = '';
                return $this->render('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
            $model = new DocHeaderForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $request = Yii::$app->request;
            if ($request->post()) {
                $model->load($request->post());
                $post = $request->post('DocHeaderForm');

                $isUrlUpsert = false;
                preg_match_all('/src\s*=\s*"(.+?)"/', $post['template_header']['old'], $oldTemp);
                preg_match_all('/src\s*=\s*"(.+?)"/', $post['template_header']['new'], $newTemp);
                if ($oldTemp[0] != $newTemp[0]) {
                    $isUrlUpsert = true;
                    $image = $newTemp;
                    $logo = [];
                    $path_logo_kiri = '';
                    $path_logo_kanan = '';

                    foreach ($image[0] as $key => $value) {
                        $exp = explode('src="'.Url::home(true).'', $value);
                        if (!isset($exp[1])) {
                            throw new \Exception('harap tidak menggunakan exkternal link!');
                        }
                        list($src, $path) = $exp;
                        list($filePath, $filename) = explode('uploads/logo/', $path);
                        $logo[] = str_replace('"', '', $path.'*'.$filename);
                    }

                    if(isset($logo[0])){
                        list($path_logo_kiri, $filename_logo_kiri) = explode('*', $logo[0]);
                    }
                    if(isset($logo[1])){
                        list($path_logo_kanan, $filename_logo_kanan) = explode('*', $logo[1]);
                    }
                }

                $model->attributes = $post;
                $model->template_header = $post['template_header']['new'];
                if ($isUrlUpsert) {
                    $model->logo_kiri = $path_logo_kiri;
                    $model->logo_kanan = $path_logo_kanan;
                    $model->logo = $logo;
                }
                
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'header-kertas/update',[
                                    'query' => ['id' => $id],
                                    'form_params' => $model->attributes
                                ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'DocHeaderForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $response = $this->_restMaster->request('GET', 'header-kertas/view',[
                            'query' => ['id' => $id ]
                        ]);
                $body = json_decode($response->getBody(),true);
                $model->attributes = $body['response'];
                $api = $this->_restMaster->get('header-kertas/generate-api');
                $api = json_decode($api->getBody(), True);
                $template_header = $body['response']['template_header'];
                return $this->render('form', get_defined_vars());
                
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionUploads()
    {       
        $funcNum = $_REQUEST['CKEditorFuncNum'];

        if($_FILES['upload']) {

            if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name']))) 
            {
                $message = Yii::t('app', "Please Upload an image.");
            }

            else if ($_FILES['upload']["size"] == 0 OR $_FILES['upload']["size"] > 5*1024*1024)
            {
                $message = Yii::t('app', "The image should not exceed 5MB.");
            }

            else if (($_FILES['upload']["type"] != "image/jpg") 
                    AND ($_FILES['upload']["type"] != "image/jpeg") 
                    AND ($_FILES['upload']["type"] != "image/png"))
            {
                $message = Yii::t('app', "The image type should be JPG , JPEG Or PNG.");
            }

            else if (!is_uploaded_file($_FILES['upload']["tmp_name"]))
            {

                $message = Yii::t('app', "Upload Error, Please try again.");
            }

            else {
                $random = rand(0123456789, 9876543210);
                $extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
                // $name = date("m-d-Y-h-i-s", time())."-".$random.'.'.$extension;
                $name = $_FILES['upload']['name']; 
                $folder = 'uploads';  
                $pathUplod = $folder.'/logo/';
                if (!is_dir($pathUplod)) {
                    mkdir($pathUplod, 0777, true);
                }
                $url = Yii::$app->urlManager->createAbsoluteUrl($pathUplod.$name);
                move_uploaded_file( $_FILES['upload']['tmp_name'], $pathUplod.$name );
                $message = Yii::t('app', "Upload Berhasil");
          }

          $session = Yii::$app->session->set('pathLogo', $pathUplod.$name);
          echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("'
               .$funcNum.'", "'.$url.'", "'.$message.'" );</script>';

        }
    }

    public function actionGetHeader()
    {
        $profilrs_id = Yii::$app->request->post('profilrs_id');

        try {
            $response = $this->_restMaster->get('header-kertas/get-header?profilrs_id='.$profilrs_id);
            return DocoHelpers::responseJsonString($response->getBody(), '');

        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), '');
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function find($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'header-kertas/view',[
                            'query' => ['id' => $id ]
                        ]);
            $body = json_decode($response->getBody(),true);
            
            return $body;
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionDelete($id)
    {
        try {
            $response = $this->_restMaster->request('DELETE', 'header-kertas/delete',[
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
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        // Path
        $path = Yii::getAlias("@download") . "/list-header-kertas.pdf";
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        // Try catch
        try {
            // Request
            $request = $this->_restMaster->get('header-kertas/export-pdf?nama_usercetak='.$nama_usercetak.'&id_usercetak='.$id_usercetak.'&'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        $url = "";
        try {
            $response = $this->_restMaster->get('header-kertas/export-excel?'.http_build_query($yiiRestfulParams));
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
