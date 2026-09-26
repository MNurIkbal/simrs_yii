<?php
// Author : Naufal Ziyad L
// Modified By: Ardi Pratama

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\components\traits\GolonganUmurTrait;
use app\modules\master\components\traits\CaraMasukTrait;
use app\modules\master\components\behaviors\MyBehavior;
use app\modules\master\models\CaraMasukForm;
use app\modules\master\models\GolonganUmurForm;

class IdentitasController extends DocoController
{
    protected $_title = "Master :: Identitas";
    protected $_module = 'master/identitas';
    protected $_restMaster;

    use GolonganUmurTrait,CaraMasukTrait;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions()
    {
        return [
            'get-data-masuk' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'identitas/get-cara-masuk',
                'module' => $this->_module,
                'keyField' => 'caramasuk_id'
            ],
            'get-data-umur' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'identitas/get-gol-umur',
                'module' => $this->_module,
                'keyField' => 'golonganumur_id'
            ],
            'update-cara-masuk' => [
                'class' => 'app\modules\master\components\actions\UpdateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceUpdateAction' => 'identitas/update-cara-masuk',
                'serviceViewAction' => 'identitas/view-cara-masuk',
                'module' => $this->_module,
                'modelForm' => new CaraMasukForm,
                'viewForm' => 'cara-masuk/form'
            ],
            'update-gol-umur' => [
                'class' => 'app\modules\master\components\actions\UpdateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceUpdateAction' => 'identitas/update-gol-umur',
                'serviceViewAction' => 'identitas/view-gol-umur',
                'module' => $this->_module,
                'modelForm' => new GolonganUmurForm,
                'viewForm' => 'gol-umur/form'
            ],
            'delete-gol-umur' => [
                'class' => 'app\modules\master\components\actions\DeleteModalAction',
                'serviceName' => $this->_restMaster,
                'serviceDeleteAction' => 'identitas/delete-gol-umur',
            ],
            'delete-cara-masuk' => [
                'class' => 'app\modules\master\components\actions\DeleteModalAction',
                'serviceName' => $this->_restMaster,
                'serviceDeleteAction' => 'identitas/delete-cara-masuk',
            ],
            'create-cara-masuk' => [
                'class' => 'app\modules\master\components\actions\CreateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceCreateAction' => 'identitas/create-cara-masuk',
                'modelForm' => new CaraMasukForm,
                'viewForm' => 'cara-masuk/form'
            ],
            'create-gol-umur' => [
                'class' => 'app\modules\master\components\actions\CreateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceCreateAction' => 'identitas/create-gol-umur',
                'modelForm' => new GolonganUmurForm,
                'viewForm' => 'gol-umur/form'
            ]
        ];
    }

    public function _getStatus()
    {
        return $this->_status;
    }

    public function _getOptions()
    {
        return $this->_options;
    }

    public function actionIndex()
    {
        $status = $this->_status;
        return $this->render('index', get_defined_vars());
    }

    public function actionUnknownId()
    {
        return $this->renderPartial('unknown_id');
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $type = $request->get('type');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "";
        try {
            $response = $this->_restMaster->get('identitas/export-excel?type=' . $type . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
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

    public function actionCetakCaraMasuk()
    {
        $request = Yii::$app->request;
        $cache = [];
        $path = Yii::getAlias("@download") . "/cara-masuk.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('identitas/cetak-cara-masuk?'. http_build_query($yiiRestfulParams), [
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

    public function actionCetakGolongan()
    {
        $request = Yii::$app->request;
        $cache = [];
        $path = Yii::getAlias("@download") . "/golongan.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('identitas/cetak-golongan?'. http_build_query($yiiRestfulParams), [
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
}
