<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\IntPurchaseOrder;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\KomponenTarif;
use phpDocumentor\Reflection\Types\Array_;

class AccEntryController extends \Doco\components\DocoActiveController
{
 
    public $modelClass = '';

    public function actions()
    {
        return [
            'extract-master-patient' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterPatient',
                'usingDate' => TRUE,
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-master-productcategory' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterProductCategory'
            ],
            'extract-master-uom' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterUom'
            ],
            'extract-master-ruangan' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterRuangan'
            ],
            'extract-master-partner' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterPartner'
            ],
            'extract-master-tindakanpaket' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterTindakanPaket'
            ],
            'extract-master-obat' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterObat'
            ],
            'extract-master-barang' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterBarang'
            ],
            'extract-master-group-inacbg' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterGroupInacbg'
            ],
            'extract-master-bank' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterBank'
            ],
            'extract-master-edc' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterEdc'
            ],
            'extract-master-paymenttype' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterPaymentType'
            ],
            'extract-saleorder' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\Saleorder',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-saleorderupdate' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\SaleorderUpdate',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-saleorderline-tindakan' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\SaleorderlineTindakan',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-saleorderline-obat' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\SaleorderlineObat',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-saleorderbill' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\Saleorderbill',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-scrollcashier' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\ScrollCashier',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-inpatientdeposit' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\InpatientDeposit',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-patientdebt' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\PatientDebt',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-stockscrap' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\Stockscrap',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-grnreceipt' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\GrnReceipt',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-grnreceiptdetail' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\GrnReceiptDetail',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-grnissue' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\GrnIssue',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-grnissuedetail' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractAction',
                'components' => 'app\modules\v1\components\accounting\GrnIssueDetail',
                'paramDate' => Yii::$app->request->get('date',date('Y-m-d'))
            ],
            'extract-master-category-transaction' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractMasterAction',
                'components' => 'app\modules\v1\components\accounting\MasterCategoryTransaction'
            ]
        ];
    }
}