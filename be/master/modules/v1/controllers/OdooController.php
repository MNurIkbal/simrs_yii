<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\ServiceCategory;
use app\modules\v1\models\ServiceGroup;
use app\modules\v1\models\IntTindakanPaketView;
use app\modules\v1\models\IntObat;
use app\modules\v1\models\IntBarang;
use app\modules\v1\models\IntPartner;
use app\modules\v1\models\IntRuanganView;
use app\modules\v1\models\IntSatuanUnitView;
use app\modules\v1\models\SatuanUnitR;

class OdooController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';

    /**
     * Untuk Kebutuhan Integerasi Odoo
     * @var array
     */
    public $messageBroker = [
        'sync-service-category' => [
            'services' => [
                'Odoo' => [
                    'ServiceCategory' => [
                        'payload' => ['id'],
                        'is_sync' => true
                    ]
                ],
            ]
        ],
        'sync-tindakan' => [
            'services' => [
                'Odoo' => [
                    'Tindakan' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-paket' => [
            'services' => [
                'Odoo' => [
                    'Paket' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-kel-tindakan' => [
            'services' => [
                'Odoo' => [
                    'KelompokTindakan' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-kel-obat' => [
            'services' => [
                'Odoo' => [
                    'KelompokObat' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-partner' => [
            'services' => [
                'Odoo' => [
                    'Partner' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-penjamin' => [
            'services' => [
                'Odoo' => [
                    'Penjamin' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-pegawai' => [
            'services' => [
                'Odoo' => [
                    'Pegawai' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-ruangan' => [
            'services' => [
                'Odoo' => [
                    'Ruangan' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-obat' => [
            'services' => [
                'Odoo' => [
                    'Obat' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ],
        'sync-barang' => [
            'services' => [
                'Odoo' => [
                    'Barang' => [
                        'payload' => ['id', 'last_insert'],
                        'is_sync' => true
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-service-category"] = ["GET"];
        $verbs["get-data-tindakan"] = ["GET"];
        $verbs["get-data-obat"] = ["GET"];
        $verbs["get-data-barang"] = ["GET"];
        $verbs["get-service-group"] = ["GET"];
        $verbs["sync-service-category"] = ["POST"];
        $verbs["sync-service-group"] = ["POST"];
        $verbs["sync-tindakan"] = ["POST"];
        $verbs["sync-paket"] = ["POST"];
        $verbs["sync-kel-tindakan"] = ["POST"];
        $verbs["sync-kel-obat"] = ["POST"];
        $verbs["sync-penjamin"] = ["POST"];
        $verbs["sync-pegawai"] = ["POST"];
        $verbs["sync-ruangan"] = ["POST"];
        $verbs["sync-obat"] = ["POST"];
        $verbs["sync-barang"] = ["POST"];
        $verbs["sync-partner"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        return [
            'get-service-category' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new ServiceCategory,
                'selected' => [
                    'servicecategory_id',
                    'servicecategory_nama',
                ],
                'field_search' => [
                    'servicecategory_nama',
                ],
                'orderby' => [
                    ['servicecategory_nama', 'ASC']
                ],
            ],
            'get-service-group' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new ServiceGroup,
                'selected' => [
                    'servicegroup_id',
                    'servicegroup_nama',
                ],
                'field_search' => [
                    'servicegroup_nama',
                ],
                'orderby' => [
                    ['servicegroup_nama', 'ASC']
                ],
            ],
        ];
    }

    public function actionGetDataTindakan($id = null)
    {
        $request = Yii::$app->request;
        $model = new IntTindakanPaketView;
        $query = $model->find();

        if (!empty($id)) {
            $query->andWhere([
                'sync_id_api' => $id
            ]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDataObat($id = null)
    {
        $request = Yii::$app->request;
        $model = new IntObat;
        $query = $model->find();

        if (!empty($id)) {
            $query->andWhere([
                'sync_id_api' => $id
            ]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDataBarang($id = null)
    {
        $request = Yii::$app->request;
        $model = new IntBarang;
        $query = $model->find();

        if (!empty($id)) {
            $query->andWhere([
                'sync_id_api' => $id
            ]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetPartner()
    {
        $request = Yii::$app->request;
        $model = new IntPartner;
        $query = $model->find();

        if (!empty($id)) {
            $query->andWhere([
                'sync_id_api' => $id
            ]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetailPartner($id){
        $model = IntPartner::find();

        $response = [];
        if (!empty($id)) {
            $model->andWhere([
                'sync_id_api' => $id
            ]);

            $response = $model->asArray()->one();
        }

        return [
            'data' => $response
        ];
    }

    public function actionGetRuangan()
    {
        $request = Yii::$app->request;
        $model = new IntRuanganView;
        $query = $model->find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetailRuangan($id){
        $model = IntRuanganView::find();

        $response = [];
        if (!empty($id)) {
            $model->andWhere([
                'sync_id_api' => $id
            ]);

            $response = $model->asArray()->one();
        }

        return [
            'data' => $response
        ];
    }

    public function actionGetUom()
    {
        $request = Yii::$app->request;
        $model = new IntSatuanUnitView;
        $query = $model->find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetailUom($id){
        $model = SatuanUnitR::find();

        $response = [];
        if (!empty($id)) {
            $model->andWhere([
                'id' => $id
            ]);

            $response = $model->asArray()->one();
        }

        return [
            'data' => $response
        ];
    }

    public function actionGetDetailProduct($id)
    {
        $model = IntTindakanPaketView::find();

        $response = [];
        if (!empty($id)) {
            $model->andWhere([
                'sync_id_api' => $id
            ]);

            $response = $model->asArray()->one();
        }

        return [
            'data' => $response
        ];

    }

    public function actionGetDetailObat($id)
    {
        $model = IntObat::find(true);

        $response = [];
        if (!empty($id)) {
            $model->andWhere([
                'sync_id_api' => $id
            ]);

            $response = $model->asArray()->one();
        }

        return [
            'data' => $response
        ];

    }

    public function actionGetDetailBarang($id)
    {
        $model = IntBarang::find(true);

        $response = [];
        if (!empty($id)) {
            $model->andWhere([
                'sync_id_api' => $id
            ]);

            $response = $model->asArray()->one();
        }

        return [
            'data' => $response
        ];

    }

    public function actionSyncServiceCategory()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncServiceGroup()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncTindakan()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncPaket()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncKelTindakan()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncKelObat()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncPartner()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncPenjamin()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncPegawai()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncRuangan()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncUom()
    {
        $ids = isset($_POST['id']) ? (array) $_POST['id'] : [];
        if (!empty($ids)) {
            $model = SatuanUnitR::find()->where(['id' => $ids])->asArray()->all();
            Yii::error($model);
        }
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncObat()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncBarang()
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }
}