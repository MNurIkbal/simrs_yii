<?php 

namespace Doco\ranap\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\components\Traits\HistoryPatientTrait;
use app\components\Traits\Pelayanan\TerraMedikTrait;

class RiwayatPasienController extends DocoController
{
    use HistoryPatientTrait;
    use TerraMedikTrait;

    protected $_title = 'Riwayat Pasien';
    protected $_module = '/ranap/riwayat-pasien';
    protected $_restRanap;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->restGeneral = $this->_restRanap;
        $this->type = 'RI';
    }
}