<?php
// Author : sunarko

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KelompokDiagnosaForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class KelompokDiagnosaController extends DocoController
{
    protected $_title = "Master :: KelompokDiagnosa";
    protected $_module = 'master/kelompok-diagnosa/';
    protected $_restMaster;
    protected $allowAction = [
        'index',
        'get-data',
        'create',
        'update',
        'delete',
        'export-excel',
        'export-pdf',
        'download-file',
        'find'
    ];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_status = [
            Yii::t('fe', 'Tidak aktif'),
            Yii::t('fe', 'Aktif')
        ];
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
        $status = $this->_status;
        return $this->renderPartial('index', get_defined_vars());
    }

    public function actionGetData()
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $post = $request->post();
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $yiiRestfulParams['advanced-filter']['is_deleted']=false;
      $draw = $request->get('draw', 1);
      $data = [];

      $result = [];
      $result['data'] = $data;
      $result['draw'] = $draw;
      $result['recordsTotal'] = 0;
      $result['recordsTotal'] = 0;

      try {
          $response = $this->_restMaster->get('kelompok-diagnosa/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
          $body = json_decode($response->getBody(), True);
          $no = $request->get('start',1);
          foreach ($body['response']["data"] as $key => $value) {
              $no++;
              $primaryKey = DocoHelpers::encrypt($value['kelompokdiagnosa_id']);
              $value['primary'] = $primaryKey;
              //unset($value['kelompokdiagnosa_id']);
              $value['rowNum'] = $no;
              $value['is_active'] = DocoHelpers::isActive($value['is_active']);
              $data[$key] = $value;
          }

          $result['data'] = $data;
          $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
          $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

          return $result;
      } catch (RequestException $e) {
          return DocoHelpers::dataTabelsException($e->getMessage());
      } catch (\Exception $e) {
          return DocoHelpers::dataTabelsException($e->getMessage());
      }
    }

    public function actionCreate()
    {
      try {
          $title = Yii::t('fe', 'Tambah Kelompok Diagnosa');
          $model = new KelompokDiagnosaForm;

          $status = $this->_status;
          $request = Yii::$app->request;

          if ($request->post()) {
              $model->load($request->post());

              if ($model->validate()) {
                  $response = $this->_restMaster->request('POST', 'kelompok-diagnosa/create',[
                                      'form_params' => $model->attributes
                              ]);
                  $response = json_decode($response->getBody(),true);
                  return DocoHelpers::response($response,false,true);
              } else {
                  $errors = DocoHelpers::parseError($model->errors,'KelompokDiagnosaForm');
                  return DocoHelpers::response([
                          'response' => [
                              'data' => $errors
                          ]
                      ],422);
              }
          } else {
              return $this->render('form',get_defined_vars());
          }
      } catch (RequestException $e) {
          return DocoHelpers::response(['message' => $e->getMessage()],500);
      } catch (\Exception $e) {
          return DocoHelpers::response(['message' => $e->getMessage()],500);
      }
    }

    public function actionUpdate($id)
    {
        try {
            $title = 'Ubah Kelompok Diagnosa';
            $model = new KelompokDiagnosaForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelompok-diagnosa/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokDiagnosaForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $result = $this->find($id);
                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    return $this->render('form',get_defined_vars());
                } else {
                    return $this->actionCreate();
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionDelete($id)
    {
      try {
          $id = DocoHelpers::decrypt($id);
          $response = $this->_restMaster->request('POST', 'kelompok-diagnosa/delete',[
                          'query' => ['id' => $id ]
                      ]);
          $response = json_decode($response->getBody(),true);
          $response['response'] = [
              'title' => 'Proses Berhasil !',
              'text' => 'Data berhasil dihapus',
              'return'=>'tabel.reload()'
          ];
          return DocoHelpers::response($response);
      } catch (RequestException $e) {
          return DocoHelpers::response(['message' => $e->getMessage()],500);
      } catch (\Exception $e) {
          return DocoHelpers::response(['message' => $e->getMessage()],500);
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
            $response = $this->_restMaster->get('kasus-penyakit-diagnosa/export-excel?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
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

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $cache = [];
        $path = Yii::getAlias("@download") . "/kasus-penyakit-diagnosa.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('jenis-kasus-penyakit/export-pdf?'. http_build_query($yiiRestfulParams), [
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

    public function find($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'kelompok-diagnosa/view',
                [
                    'query' => ['id' => $id ]
                ]
            );
            return json_decode($response->getBody(),true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
