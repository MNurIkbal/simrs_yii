<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Expression as DbExpression;
use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use Doco\components\DocoActiveController;

class IntegrationOdooController extends DocoActiveController
{

    public $messageBroker = [
        'obat-may-injected' => [
            'services' => [
                'Odoo' => [
                    'Obat' => [
                        'is_injected' => true
                    ]
                ]
            ]
        ],
        'barang-may-injected' => [
            'services' => [
                'Odoo' => [
                    'Barang' => [
                        'is_injected' => true
                    ]
                ]
            ]
        ],
    ];

    public $modelClass = '';

    public function actions()
    {
        $actions['callback-create-obat'] = [
            'class' => 'app\modules\v1\actions\IntegrationOdoo\CallbackCreateAction',
            'modelClassRekap' => 'app\modules\v1\models\ObatAlkesR'
        ];
        $actions['callback-update-obat'] = [
            'class' => 'app\modules\v1\actions\IntegrationOdoo\CallbackCreateAction',
            'modelClassRekap' => 'app\modules\v1\models\ObatAlkesR'
        ];
        $actions['callback-create-uom'] = [
            'class' => 'app\modules\v1\actions\IntegrationOdoo\CallbackCreateAction',
            'modelClassRekap' => 'app\modules\v1\models\SatuanUnitR'
        ];
        $actions['callback-update-uom'] = [
            'class' => 'app\modules\v1\actions\IntegrationOdoo\CallbackCreateAction',
            'modelClassRekap' => 'app\modules\v1\models\SatuanUnitR'
        ];
        $actions['callback-create-supplier'] = [
            'class' => 'app\modules\v1\actions\IntegrationOdoo\CallbackCreateAction',
            'modelClassRekap' => 'app\modules\v1\models\SupplierR'
        ];
        $actions['callback-update-supplier'] = [
            'class' => 'app\modules\v1\actions\IntegrationOdoo\CallbackCreateAction',
            'modelClassRekap' => 'app\modules\v1\models\SupplierR'
        ];
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actionObatMayInjected()
    {
        $total = ObatAlkes::find()
            ->where([
                'IS', 'additional_data', new DbExpression('null')
            ])->count();

        // $total = 0;

        return [
            'status' => $total > 0 ? 200 : 422,
            'value' => $total
        ];
    }

    public function actionBarangMayInjected()
    {
        $total = Barang::find()
            ->where([
                'IS', 'additional_data', new DbExpression('null')
            ])->count();

        return [
            'status' => $total > 0 ? 200 : 422,
            'value' => $total
        ];
    }
}