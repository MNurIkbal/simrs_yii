<?php 

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\web\Response;
use app\components\DHtml;
use app\components\DocoDatatableHelper;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\apotek\models\TransaksiNoWorkListForm;

class WorklistFarmasiController extends DocoController {
    public $_title = "Worklist Farmasi";
    public $_restApotek;

    public function init() {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actions() {
        $path = "Doco\apotek\actions\WorklistFarmasi";

        return [
            'search-no-resep'       => $path . '\SearchNoResepAction',
            'search-pegawai'        => $path . '\SearchPegawaiAction',
            'get-data-worklist'     => $path . '\GetDataWorklistAction',
            'get-history-worklist'  => $path . '\GetHistoryWorklistAction',
        ];
    }

    public function actionIndex() {
        $title = $this->_title;
        $model = new TransaksiNoWorkListForm;
        return $this->render('index', get_defined_vars());
    }
}
