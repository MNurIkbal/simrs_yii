<?php

namespace app\modules\v1\actions;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoPasienBpjsView;
use app\modules\v1\models\InfoPasienBpjsDiagnosaView;
use app\modules\v1\models\InfoPasienBpjsKlaimView;
use app\modules\v1\models\InfoKlaimInacbg;
use app\modules\v1\models\KoreksiDiagnosaView;
use app\modules\v1\models\KoreksiDiagnosa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KlaimInacbg;
use app\modules\v1\models\KlaimInacbgDetail;
use app\modules\v1\models\KlaimInacbgGroup;
use app\modules\v1\models\SyKunjunganView;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\SyKunjunganDetailView;
use app\modules\v1\models\SyBagian;
use app\modules\v1\models\InfoDiagnosa;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\SyKunjunganAdjusmentDetail;
use app\modules\v1\models\CaraBayar;
use Doco\components\DocoMessages;

class BaseCurrentAction extends Action
{
    protected function getDataFilters()
    {
    }
}
