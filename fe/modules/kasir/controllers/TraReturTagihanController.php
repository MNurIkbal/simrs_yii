<?php
// Author : Budi

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\kasir\models\InfoPasienSudahBayarForm;
use GuzzleHttp\Exception\RequestException;

class TraReturTagihanController extends DocoController
{
	protected $_title = "Transaksi Retur Tagihan Pasien";
    protected $_module = 'kasir/tra-closing-kasir/';
    protected $_restKasir; protected $_restMaster;

	public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function beforeAction()
    {
        return true;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex($id)
    {
        // Init
        $status = $this->_status; $options = $this->_options;
        $userIdentity = Yii::$app->session->get('user_identity');
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $pegawai_id = $userIdentity['id_pegawai'];
        $ruangan_id = $activeWorkspace['ruangan_id'];
        
        $request = Yii::$app->request;
        $title = Yii::t('fe', $this->_title);
        $model = new InfoPasienSudahBayarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        $_POST['InfoPasienSudahBayarForm']['returbayarpelayanan_t']['ruangan_id'] = $ruangan_id;
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restKasir->post('tra-retur-tagihan/create?id='.$id, [
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
        } else {
            $response = $this->_restKasir->get('lap-pasien-sudah-bayar/view?expand=pendaftaran_t,pembayaranpelayanan_t,tandabuktibayar_t,returbayarpelayanan_t,bank_m&id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->tgl_pendaftaran = date("j M Y", strtotime($model->tgl_pendaftaran));
            $model->pembayaranpelayanan_t['tgl_pembayaran'] = date("j M Y", strtotime($model->pembayaranpelayanan_t['tgl_pembayaran']));
            $model->returbayarpelayanan_t['tandabuktibayar_id'] = $model->tandabuktibayar_id;

            return $this->render('index', get_defined_vars());
        }
    }
}