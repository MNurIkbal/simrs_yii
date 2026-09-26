<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\master\processes;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Doco\master\models\KamarForm;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class KamarIndexProcess extends \app\components\DocoBaseProcessExtension
{

    protected $_title = "Kamar";
    protected $_module = '/master/kamar';
    protected $_restMaster;
    protected $_subMenuTitleKamar = "Kamar";
    protected $_subMenuTitleKlasifikasiKamar = "Mappingan Klasifikasi Kamar";

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    protected function processFlow($controller)
    {
    	$title = $this->_title;
        $_subMenuTitleKamar = $this->_subMenuTitleKamar;
        $_subMenuTitleKlasifikasiKamar = $this->_subMenuTitleKlasifikasiKamar;
        // echo "<pre>";var_dump($data_ruangan);die();
        return $controller->render('@app/modules/master/processes/kamar/index', get_defined_vars());
    }

    private function getData($id='')
    {
        try {
            if ($id) {
                $response = $this->_restMaster->request('GET', 'kamar/generate-api?id='.$id);
            }else{
                $response = Yii::$app->docoRest->master->request('GET', 'kamar/generate-api');
            }
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_ruangan' => $body['response']['data-ruangan'],
                'data_jenis_kamar' => $body['response']['data-jenis-kamar'],
                'data_pelayanan' => $body['response']['data-pelayanan'],
                'data_kasus_penyakit' => $body['response']['data-kasus-penyakit'],
            ];

            return $return;
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}