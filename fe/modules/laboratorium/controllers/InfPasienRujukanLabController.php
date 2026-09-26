<?php

/**
 * @author Sigit
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
namespace Doco\laboratorium\controllers;

use Yii;
use app\components\DocoController;
use app\components\Traits\TindakanPenunjangTrait;
use app\modules\laboratorium\components\traits\RujukanTrait;
use app\modules\laboratorium\components\traits\PasienLabTrait;
use app\modules\laboratorium\components\traits\SpecimentTrait;
use app\modules\laboratorium\components\traits\HasilLabTrait;
use app\modules\laboratorium\components\traits\InputHasilTrait;
use app\modules\laboratorium\components\traits\RiwayatTrait;
use app\modules\laboratorium\components\traits\BatalTrait;
use app\modules\laboratorium\components\traits\OrderObatTrait;
use app\modules\laboratorium\components\traits\UploadDokumenTrait;

class InfPasienRujukanLabController extends DocoController
{
    use TindakanPenunjangTrait;
    use RujukanTrait;
    use PasienLabTrait;
    use SpecimentTrait;
    use HasilLabTrait;
    use InputHasilTrait;
    use RiwayatTrait;
    use BatalTrait;
    use OrderObatTrait;
    use UploadDokumenTrait;
    
    protected $_restMaster;
    protected $_restKasir;
    protected $_restLab;
    protected $backendUrl;
    protected $serviceRest;
    protected $_title = "Informasi Pasien Laboratorium";

    public function init()
    {
        parent::init();

        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restLab = Yii::$app->docoRest->laboratorium;
        $this->serviceRest = Yii::$app->docoRest->laboratorium; 
        $this->backendUrl = 'inf-pasien-rujukan-lab';

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
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }
}