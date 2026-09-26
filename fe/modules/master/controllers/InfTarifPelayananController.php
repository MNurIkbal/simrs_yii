<?php
/**
 * @author: Iqbal Qurahman
 * @description: master layar antrian
**/

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rajal\models\InfoTarifPelayananForm;
use GuzzleHttp\Exception\RequestException;

class InfTarifPelayananController extends DocoController
{
	protected $_title = 'Info Tarif Pelayanan';
    protected $_module = 'inf-tarif-pelayanan/';
    protected $_restMaster;
    protected $_restPendaftaran;
    protected $_restRajal;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
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
        $title = Yii::t('fe', 'Informasi tarif').' '.Yii::$app->docoVars->workspace("modul_alias");
        $listRequest = ['instalasi_id'=> Yii::$app->docoVars->workspace("instalasi_id"), 
                        'ruangan_id'=> Yii::$app->docoVars->workspace("ruangan_id"),
                        'all' => 1];
        $response = $this->_restMaster->post('allow/list-inf-tarif-pelayanan', ['form_params'=>$listRequest]);
        $body = json_decode($response->getBody(), True);
        $listfilter = $body['response']['data'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $listRequest = ['instalasi_id'=>Yii::$app->docoVars->workspace("instalasi_id"), 
                        'ruangan_id'=>Yii::$app->docoVars->workspace("ruangan_id")];

            $response = $this->_restMaster->request('POST', 'inf-tarif-pelayanan/index?'.http_build_query($yiiRestfulParams),[ 'form_params' =>  $listRequest ]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;

                $primary = json_encode([$value['daftartindakan_id'],$value['ruangan_id'], $value['kelaspelayanan_id'], $value['penjamin_id'], $value['tariftindakan_id']]);
                $primaryKey = DocoHelpers::encrypt($primary);
                $value['primary'] = $primaryKey;
                $value['komponen_tarif'] = 'LINKS';

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
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionDetail($id)
    {
	 	$id = json_decode(DocoHelpers::decrypt($id));
        $daftartindakan_id = $id[0];
        $ruangan_id = $id[1];
        $kelaspelayanan_id = $id[2];
        $penjamin_id = $id[3];
        $tariftindakan_id = $id[4];

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Komponen Tarif');

        $response = $this->_restMaster->request('POST', 'inf-tarif-pelayanan/komponen-tarif',[ 
            'form_params' => [
                    'ruangan_id' => $ruangan_id,
                    'daftartindakan_id' => $daftartindakan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'penjamin_id' => $penjamin_id,
                    'tariftindakan_id' => $tariftindakan_id,
                ]
            ]);

        $body = json_decode($response->getBody(), true);
        $data = $body['response'];

    	return $this->renderAjax('detail', get_defined_vars());
    }
}