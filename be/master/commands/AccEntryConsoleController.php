<?php

namespace app\commands;

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
use yii\console\Controller;

class AccEntryConsoleController extends Controller
{
 
    public $modelClass = '';

    public function actions()
    {
        return [
            'extract-master-patient' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterPatient',
                'usingDate' => TRUE,
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-master-productcategory' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterProductCategory'
            ],
            'extract-master-uom' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterUom'
            ],
            'extract-master-ruangan' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterRuangan'
            ],
            'extract-master-partner' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterPartner'
            ],
            'extract-master-tindakanpaket' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterTindakanPaket'
            ],
            'extract-master-obat' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterObat'
            ],
            'extract-master-barang' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterBarang'
            ],
            'extract-master-group-inacbg' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterGroupInacbg'
            ],
            'extract-master-bank' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterBank'
            ],
            'extract-master-edc' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterEdc'
            ],
            'extract-master-paymenttype' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterPaymentType'
            ],
            'extract-saleorder' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\Saleorder',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-saleorderupdate' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\SaleorderUpdate',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-saleorderline-tindakan' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\SaleorderlineTindakan',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-saleorderline-obat' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\SaleorderlineObat',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-saleorderbill' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\Saleorderbill',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-scrollcashier' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\ScrollCashier',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-inpatientdeposit' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\InpatientDeposit',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-patientdebt' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\PatientDebt',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-stockscrap' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\Stockscrap',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-grnreceipt' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\GrnReceipt',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-grnreceiptdetail' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\GrnReceiptDetail',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-grnissue' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\GrnIssue',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-grnissuedetail' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\GrnIssueDetail',
                'paramDate' => date('Y-m-d',strtotime("-1 days"))
            ],
            'extract-master-category-transaction' => [
                'class' => 'app\modules\v1\actions\AccEntry\ExtractConsoleApiAction',
                'components' => 'app\modules\v1\components\accounting\MasterCategoryTransaction'
            ]
        ];
    }
}