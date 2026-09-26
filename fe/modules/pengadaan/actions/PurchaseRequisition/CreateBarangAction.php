<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\web\Response;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\PurchaseRequisitionForm;
use GuzzleHttp\Exception\RequestException;

class CreateBarangAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $moduleAlias = Yii::$app->docoVars->workspace('modul_alias');
        $modulePath = Yii::$app->docoVars->workspace('url');
        $model = new PurchaseRequisitionForm;
        $model->tgl_pr = date('d M Y');
        $model->instalasi_ruangan = Yii::$app->docoVars->workspace('instalasi_name')." - "
            .Yii::$app->docoVars->workspace('ruangan_name');
        $model->ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $model->pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $model->nama_pegawai = Yii::$app->docoVars->user('nama_pegawai');
        $dataUser = [
            'id' => Yii::$app->user->identity->id_pegawai,
            'text' => Yii::$app->user->identity->nama_pegawai
        ];

        $is_large_unit_pr = $this->controller->getKonfigFarmasi();

        return $this->controller->render('create-barang', get_defined_vars());
    }
}
