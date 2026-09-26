<?php 
/*

author: Rizqi Fitrianto
date: 03-01-2018
desc: master profil user

*/

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\master\models\ProfilUserForm;
use Doco\master\models\RubahPasswordForm;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;



class ProfilUserController extends DocoController
{

	protected $_title = "Master :: Profil Pengguna";
    protected $_module = 'master/profil-user/';
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
        $result_nama = $this->_restMaster->request('GET', 'profil-user/get-pengguna');
        $data_nama = json_decode($result_nama->getBody(), true);    
        return $this->render('index', get_defined_vars());
    }
    public function actionUpdate($id){
        try {
            $title = "Update Profil User";
            $model = new ProfilUserForm;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if($request->post()){                                
                $model->load($request->post());                                
                if($model->validate()){                                
                    $image = UploadedFile::getInstance($model, "photouser");                    
                    if(!empty($image)){         
                                                          
                        $ext = end(explode(".", $image->name));
                        $model->photouser = Yii::$app->security->generateRandomString().".{$ext}";
                        $path = \Yii::getAlias('@webroot');
                        if($image->saveAs($path.'/media/user-foto/'.$model->photouser)){
                            $response = $this->_restMaster->request('POST', 'profil-user/update',[
                                'query'=> ['id'=>$id],
                                'form_params'=>$model->attributes,
                            ]);        
                        }                                                          
                    }else{   

                        $response = $this->_restMaster->request('POST', 'profil-user/update',[
                            'query'=> ['id'=>$id],
                            'form_params'=>$model->attributes,
                        ]);
                    }                                                                
                                        
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response, false, true);
                }else{
                    $errors = DocoHelpers::parseError($model->errors, 'ProfilUserForm');
                    return DocoHelpers::response(['response'=>[
                                                    'data'=>$errors
                                                    ]
                                                ], 422);
                }

            }else{                
                $result = $this->find($id);                       
                if(isset($result['response'])){  
                    $result_jabatan = $this->_restMaster->request('GET', 'profil-user/get-jabatan');
                    $data_jabatan = json_decode($result_jabatan->getBody(), true);                    
                    $result['response']['jabatan_id'] = $result['response']['jabatan']['jabatan_id'];
                    $model->attributes = $result['response'];                    
                    //return $this->renderAjax('form', get_defined_vars());
                    return $this->renderPartial('form', get_defined_vars(), true);
                }
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message'=>$e->getMessage()]);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message'=>$e->getMessage()]);
        }
    }

    /*
    *author: Rizqi Fitrianto
    *date: 09-Januari-2018
    *desc: aksi untuk rubah kata sandi user
    */
    public function actionUbahPassword(){
        try{
            $title = "Ubah Kata Sandi";
            $model = new RubahPasswordForm;
            $request  = Yii::$app->request;
            
            if($request->post()){
                $model->load($request->post());
                if($model->validate()){   
                     $response = $this->_restMaster->request('POST', 'profil-user/rubah-password',[
                                        'form_params' => $model->attributes
                                ]);                     
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                }else{
                    $errors = DocoHelpers::parseError($model->errors, 'RubahPasswordForm');
                    return DocoHelpers::response(['response'=>[
                                                    'data'=>$errors
                                                    ]
                                                ], 422);
                }
            }else{                
               return $this->render('form-password', get_defined_vars());
            }
        }catch(Exception $e){
            return DocoHelpers::response(['message'=>$e->getMessage()]);
        }catch (RequestException $e){
            return DocoHelpers::response(['message'=>$e->getMessage()]);
        }
    }

    public function actionGetData()
    {       
        try {
            $request = Yii::$app->request;
            $post = $request->post();                                    
            $response = $this->_restMaster->request('POST','profil-user/', ['form_params' => $post]);
            $row = [];
            $body = json_decode($response->getBody(), TRUE);                
            $no = $request->post('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['loginpemakai_id']);
                unset($value['loginpemakai_id']);

                $value['aksi']  = Html::button(
                    'Ubah', [
                        'class' => 'btn btn-success btn-xs btn-block data-update',
                        'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop'
                    ]
                );                         
                $value['rowNum'] = $no;
                $value['ruangan_nama'] = "";
                $value['jabatan_nama'] = "";
                if(isset($value['ruangan'])){
                    $value['ruangan_nama'] = $value['ruangan']['ruangan_nama'];
                    unset($value['ruangan']);
                }
                if(isset($value['jabatan'])){
                    $value['jabatan_nama'] = $value['jabatan']['jabatan_nama'];
                    unset($value['jabatan']);
                }
                
                $row[$key] = $value;

            }
            
            $result['data'] = $row;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
    public function find($id)
    {
        try {                    
            $request = Yii::$app->request;            
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'profil-user/view',[
                            'query' => ['id' => $id ]
                        ]);                        
            return json_decode($response->getBody(),true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

}

?>