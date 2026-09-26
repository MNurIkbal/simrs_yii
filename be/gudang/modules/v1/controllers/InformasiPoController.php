<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoPoView;
use app\modules\v1\models\InfoPoDetailView;
use app\modules\v1\models\PegawaiView;

use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\PenerimaanObat;
use app\modules\v1\models\PenerimaanObatDetail;
use app\modules\v1\models\PenerimaanObatDoc;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\PenerimaanBarang;
use app\modules\v1\models\PenerimaanBarangDetail;
use app\modules\v1\models\PenerimaanBarangDoc;

use app\modules\v1\models\InfoPenerimaanObat;
use app\modules\v1\models\InfoPenerimaanObatDetail;

use app\modules\v1\models\InfoPenerimaanBarang;
use app\modules\v1\models\InfoPenerimaanBarangDetail;

use app\modules\v1\repositories\StatusPenerimaanPoRepository;

class InformasiPoController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPoView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        $verbs["detail"] = ["GET"];
        $verbs["save"] = ["POST","PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPoView;
        $query = $model::find()->where([
            'type_po' => 'obat',
            'is_validasi' => true
        ]);

        $query->andWhere(['!=','status_penerimaan',DocoConstants::STATUS_BATAL_PO]);

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($advancedFilter['tanggal_po'])) {
                $exp = explode(' - ', $advancedFilter['tanggal_po']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $advancedFilter['tanggal_po_awal'] = $tgl_awal_format;
                $advancedFilter['tanggal_po_akhir'] = $tgl_akhir_format;
                $tgl_awal = $advancedFilter['tanggal_po_awal'];
                $tgl_akhir = $advancedFilter['tanggal_po_akhir'];
                unset($_GET['advanced-filter']['tanggal_po']);
            }

            if (isset($advancedFilter['stat_penerimaan'])) {
                $status_penerimaan = $advancedFilter['stat_penerimaan'];
                $query->andWhere(['status_penerimaan' => $status_penerimaan]);
                unset($_GET['advanced-filter']['stat_penerimaan']);
            }
        }

        $query->andWhere(['between', 'tanggal_po', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetail($id, $type)
    {
        $header = InfoPoView::find()->where([
            'transaksi_id' => $id,
            'type_po' => $type
        ])->one();

        $detail = InfoPoDetailView::find()->where([
            'transaksi_id' => $id,
            'jenis' => $type
        ])->all();

        return [
            'header' => $header,
            'detail' => $detail
        ];
    }

    public function actionBarang()
    {
        $model = new InfoPoView;
        $query = $model::find()->where([
            'type_po' => 'barang',
            'is_validasi' => true
        ]);

        $query->andWhere(['!=','status_penerimaan',DocoConstants::STATUS_BATAL_PO]);

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $tgl_rencanaterima_awal = date('Y-m-d 00:00:00');
        $tgl_rencanaterima_akhir = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];

            if (isset($advancedFilter['tanggal_po'])) {
                $exp = explode(' - ', $advancedFilter['tanggal_po']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $advancedFilter['tanggal_po_awal'] = $tgl_awal_format;
                $advancedFilter['tanggal_po_akhir'] = $tgl_akhir_format;
                $tgl_awal = $advancedFilter['tanggal_po_awal'];
                $tgl_akhir = $advancedFilter['tanggal_po_akhir'];
                unset($_GET['advanced-filter']['tanggal_po']);
            }

            if (isset($advancedFilter['stat_penerimaan']))
            {
                $status_penerimaan = $advancedFilter['stat_penerimaan'];
                $query->andWhere(['status_penerimaan' => $status_penerimaan]);
                unset($_GET['advanced-filter']['stat_penerimaan']);
            }

            if (isset($advancedFilter['status_verifikasi']))
            {
                $status_penerimaan = $advancedFilter['status_verifikasi'];
                $query->andWhere(['status_penerimaan' => $status_penerimaan]);
                unset($_GET['advanced-filter']['status_verifikasi']);
            }
        }

        $query->andWhere(['between', 'tanggal_po', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
    {
        $status_penerimaan = Lookup::find()->where([
            'is_active' => true,
            'lookup_type' => 'status_penerimaan_po'
        ])->all();

        return [
            'status_penerimaan' => ArrayHelper::map($status_penerimaan, 'lookup_id', 'lookup_name'),
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoPoView;
        $query = $model::find()->where([
            'type_po' => 'obat',
            'is_validasi' => true
        ]);

        $query->andWhere(['!=','status_penerimaan',DocoConstants::STATUS_BATAL_PO]);

        $title = 'Informasi Purchase Order';

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        $supplier_nama = '';
        $obatalkes_nama = '';
        $lookup_name = '';
        $arraySupplier = [];
        $arrayObat = [];
        $arrayStatus = [];
        $arrayTglTerima = [];
        $arrayNomor = [];

        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tanggal_po'])) {
                $exp = explode(' - ', $advancedFilters['tanggal_po']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $advancedFilters['tanggal_po_awal'] = $tgl_awal_format;
                $advancedFilters['tanggal_po_akhir'] = $tgl_akhir_format;
                $tgl_awal = $advancedFilters['tanggal_po_awal'];
                $tgl_akhir = $advancedFilters['tanggal_po_akhir'];
            }
            if(isset($advancedFilters['supplier_nama'])) {
                $supplier_nama = $advancedFilters['supplier_nama'];
                $arraySupplier = [
                    Yii::t('app', "Supplier") => $supplier_nama
                ];
            }

            if(isset($advancedFilters['status_penerimaan'])) {
                $status_penerimaan = $advancedFilters['status_penerimaan'];
                $lookup = Lookup::findOne($status_penerimaan);
                $lookup_name = $lookup->lookup_name;
                $query->andWhere(['status_penerimaan' => $status_penerimaan]);
                $arrayStatus = [
                    Yii::t('app', "Status") => $lookup_name
                ];
            }
            if(isset($advancedFilters['no_transaksi'])) {
                $nomor = $advancedFilters['no_transaksi'];
                $arrayNomor = [
                    Yii::t('app', "Nomor PO") => $nomor
                ];
            }
        }

        $periode = [Yii::t('app', "Tanggal PO") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];

        $additional = array_merge($arraySupplier, $arrayObat, $arrayStatus, $arrayNomor);
        $header = array_merge($periode, $additional);
        $query->andWhere(['between', 'tanggal_po', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = [];
        foreach ($query->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal PO')] = date('d M Y', strtotime($value['tanggal_po']));
            $newValue[\Yii::t('app', 'Nomor PO')] = $value['no_transaksi'];
            $newValue[\Yii::t('app', 'Total Harga PO')] = $value['total_harga_po'];
            $newValue[\Yii::t('app', 'Supplier')] = $value['supplier_nama'];
            $newValue[\Yii::t('app', 'Rencana Terima')] = !empty($value['tgl_rencanaterima'])
                ? date('d M Y',strtotime($value['tgl_rencanaterima'])) : '-';
            $newValue[\Yii::t('app', 'Status')] = $value['stat_penerimaan'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #obatalkes_nama# => nama obat
    * @attribute #nomor# => nomor po
    * @attribute #supplier_nama# => supplier
    * @attribute #status# => status penerimaan
    * @attribute #pegawai_nama# => pegawai
    * @attribute #title# => title
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $title = 'Informasi Purchase Order Obat';
        $get = $request->get();
        $model = new InfoPoView;
        $query = $model::find()->where([
            'type_po' => 'obat',
            'is_validasi' => true
        ]);

        $query->andWhere(['!=','status_penerimaan',DocoConstants::STATUS_BATAL_PO]);

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $obatalkes_nama = '-';
        $supplier_nama = '-';
        $nomor = '-';
        $status = '-';

        if ($request->get('advanced-filter')) {
            $advancedFilters = $request->get('advanced-filter');
            if (isset($advancedFilters['tanggal_po'])) {
                $exp = explode(' - ', $advancedFilters['tanggal_po']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $advancedFilters['tanggal_po_awal'] = $tgl_awal_format;
                $advancedFilters['tanggal_po_akhir'] = $tgl_akhir_format;
                $tgl_awal = $advancedFilters['tanggal_po_awal'];
                $tgl_akhir = $advancedFilters['tanggal_po_akhir'];
            }
            if (isset($advancedFilters['supplier_nama'])) {
                $supplier_nama = $advancedFilters['supplier_nama'];
            }

            if (isset($advancedFilters['obat_barang_nama'])) {
                $obatalkes_nama = $advancedFilters['obat_barang_nama'];
            }

            if (isset($advancedFilters['status_penerimaan'])) {
                $status_penerimaan = $advancedFilters['status_penerimaan'];
                $lookup = Lookup::findOne($status_penerimaan);
                $status = $lookup->lookup_name;
                $query->andWhere(['status_penerimaan' => $status_penerimaan]);
                unset($_GET['advanced-filter']['status_penerimaan']);
            }
            if (isset($advancedFilters['no_transaksi'])) {
                $nomor = $advancedFilters['no_transaksi'];
            }
        }

        $query->andWhere(['between', 'tanggal_po', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $pegawai = PegawaiView::find()
            ->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])
            ->one();

        $pegawai_nama = '';
        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir)),
            '#obatalkes_nama#' => $obatalkes_nama,
            '#supplier_nama#' => $supplier_nama,
            '#status#' => $status,
            '#nomor#' => $nomor,
            '#pegawai_nama#' => $pegawai_nama,
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->all(),
            ]),
        ];

        $print->Output();
    }

    public function actionExportExcelBarang()
    {
        $request = Yii::$app->request;
        $model = new InfoPoView;
        $query = $model::find()->where([
            'type_po' => 'barang',
            'is_validasi' => true
        ]);

        $title = 'Informasi Purchase Order Barang';
        $query->andWhere(['!=','status_penerimaan',DocoConstants::STATUS_BATAL_PO]);

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $tgl_rencanaterima_awal = date('Y-m-d 00:00:00');
        $tgl_rencanaterima_akhir = date('Y-m-d 23:59:59');

        $supplier_nama = '';
        $barang_nama = '';
        $lookup_name = '';
        $arraySupplier = [];
        $arrayObat = [];
        $arrayStatus = [];
        $arrayTglTerima = [];
        $arrayNomor = [];

        if (isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];

            if (isset($advancedFilters['tanggal_po'])) {
                $exp = explode(' - ', $advancedFilters['tanggal_po']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $advancedFilters['tanggal_po_awal'] = $tgl_awal_format;
                $advancedFilters['tanggal_po_akhir'] = $tgl_akhir_format;
                $tgl_awal = $advancedFilters['tanggal_po_awal'];
                $tgl_akhir = $advancedFilters['tanggal_po_akhir'];
                unset($_GET['advanced-filter']['tanggal_po']);
            }
            if (isset($advancedFilters['supplier_nama'])) {
                $supplier_nama = $advancedFilters['supplier_nama'];
                $arraySupplier = [
                    Yii::t('app', "Supplier") => $supplier_nama
                ];
            }

            if (isset($advancedFilters['status_penerimaan'])) {
                $status_penerimaan = $advancedFilters['status_penerimaan'];
                $lookup = Lookup::findOne($status_penerimaan);
                $lookup_name = $lookup->lookup_name;
                $query->andWhere(['status_penerimaan' => $status_penerimaan]);
                $arrayStatus = [
                    Yii::t('app', "Status") => $lookup_name
                ];
            }
            if (isset($advancedFilters['nomor'])) {
                $nomor = $advancedFilters['nomor'];
                $query->andWhere(['ILIKE', 'nomor', $nomor]);
                $arrayNomor = [
                    Yii::t('app', "Nomor PO") => $nomor
                ];
            }
        }

        $periode = [ Yii::t('app', "Tanggal PO") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $additional = array_merge($arraySupplier, $arrayObat, $arrayStatus, $arrayNomor);
        $header = array_merge($periode, $additional);
        $query->andWhere(['between', 'tanggal_po', $tgl_awal, $tgl_akhir]);

        $result = [];
        foreach ($query->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal PO')] = date('d M Y', strtotime($value['tanggal_po']));
            $newValue[\Yii::t('app', 'Nomor PO')] = $value['no_transaksi'];
            $newValue[\Yii::t('app', 'Total Harga PO')] = $value['total_harga_po'];
            $newValue[\Yii::t('app', 'Supplier')] = $value['supplier_nama'];
            $newValue[\Yii::t('app', 'Rencana Terima')] = !empty($value['tgl_rencanaterima'])
            ? date('d M Y',strtotime($value['tgl_rencanaterima'])) : date('d M Y');
            $newValue[\Yii::t('app', 'Status')] = $value['stat_penerimaan'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdfBarang
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #title# => title
    * @attribute #barang_nama# => nama barang
    * @attribute #nomor# => nomor po
    * @attribute #supplier_nama# => supplier
    * @attribute #status# => status penerimaan
    * @attribute #pegawai_nama# => pegawai
    */
    public function actionExportPdfBarang()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $title = 'Informasi Purchase Order Barang';
        $get = $request->get();
        $model = new InfoPoView;
        $query = $model::find()->where([
            'type_po' => 'barang',
            'is_validasi' => true
        ]);

        $query->andWhere(['!=','status_penerimaan',DocoConstants::STATUS_BATAL_PO]);

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $tgl_rencanaterima_awal = date('Y-m-d 00:00:00');
        $tgl_rencanaterima_akhir = date('Y-m-d 23:59:59');

        $barang_nama = '-';
        $supplier_nama = '-';
        $nomor = '-';
        $status = '-';

        if($request->get('advanced-filter')) {
            $advancedFilters = $request->get('advanced-filter');
            if (isset($advancedFilters['tanggal_po'])) {
                $exp = explode(' - ', $advancedFilters['tanggal_po']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $tgl_awal = $tgl_awal_format;
                $tgl_akhir = $tgl_akhir_format;
                unset($_GET['advanced-filter']['tanggal_po']);
            }

            if (isset($advancedFilters['supplier_nama'])) {
                $supplier_nama = $advancedFilters['supplier_nama'];

            }

            if(isset($advancedFilters['obat_barang_nama'])) {
                $barang_nama = $advancedFilters['obat_barang_nama'];
            }

            if (isset($advancedFilters['status_penerimaan'])) {
                $status_penerimaan = $advancedFilters['status_penerimaan'];
                $lookup = Lookup::findOne($status_penerimaan);
                $status = $lookup->lookup_name;
                $query->andWhere(['status_penerimaan' => $status_penerimaan]);
                unset($_GET['advanced-filter']['status_penerimaan']);
            }

            if(isset($advancedFilters['no_transaksi'])) {
                $nomor = $advancedFilters['no_transaksi'];
            }
        }

        $query->andWhere(['between', 'tanggal_po', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $pegawai = PegawaiView::find()
            ->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])
            ->one();

        $pegawai_nama = '';
        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir)),
            '#barang_nama#' => $barang_nama,
            '#supplier_nama#' => $supplier_nama,
            '#status#' => $status,
            '#nomor#' => $nomor,
            '#pegawai_nama#' => $pegawai_nama,
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak_barang', [
                'data' => $query->all(),
            ]),
        ];

        $print->Output();
    }

    public function actionSave($id, $type)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            switch ($type) {
                case DocoConstants::JENIS_OBAT:
                    $model = new PenerimaanObat;
                    $detail = new PenerimaanObatDetail;
                    $upload = new PenerimaanObatDoc;
                    $validasi = ValidasiPoObat::find()->where([
                        'validasipoobat_id' => $id
                    ])->one();
                    $attrValid = 'validasipoobat_id';
                    $primaryKey = 'penerimaanobat_id';
                    $penerimaanDetailId = 'penerimaanobatdetail_id';
                    $tableReturDetail = 'returpenerimaanobatdetail_t';
                    $itemId = 'obatalkes_id';
                    $detailId = 'validasipoobatdetail_id';
                    $validasiDetail = 'validasipoobatdetail_t';
                    $konversiId = 's_konversiobt_id';
                    $noPo = 'no_poobat';
                    $no_suratjalan = PenerimaanObat::find()->where([
                        'no_suratjalan' => $request->post('no_suratjalan')
                    ])->one();
                    $no_faktur = PenerimaanObat::find()->where([
                        'no_faktur' => $request->post('no_faktur')
                    ])->one();
                    break;
                case DocoConstants::JENIS_BARANG:
                    $model = new PenerimaanBarang;
                    $detail = new PenerimaanBarangDetail;
                    $upload = new PenerimaanBarangDoc;
                    $validasi = ValidasiPoBarang::find()->where([
                        'validasipobarang_id' => $id
                    ])->one();
                    $attrValid = 'validasipobarang_id';
                    $primaryKey = 'penerimaanbarang_id';
                    $penerimaanDetailId = 'penerimaanbarangdetail_id';
                    $tableReturDetail = 'returpenerimaanbarangdetail_t';
                    $itemId = 'barang_id';
                    $detailId = 'validasipobarangdetail_id';
                    $validasiDetail = 'validasipobarangdetail_t';
                    $konversiId = 's_konversibrg_id';
                    $noPo = 'no_pobarang';
                    $no_suratjalan = PenerimaanBarang::find()->where([
                        'no_suratjalan' => $request->post('no_suratjalan')
                    ])->one();
                    $no_faktur = PenerimaanBarang::find()->where([
                        'no_faktur' => $request->post('no_faktur')
                    ])->one();
                    break;
                default:
                    Yii::$app->response->statusCode = 500;
                    return ['message' => 'Parammeter tidak valid'];
                    break;
            }

            if (empty($validasi)) {
                return [
                    'status' => 422,
                    'text' => 'Transaksi tidak dikenali',
                    'title' => 'Proses Gagal!'
                ];
            }

            if (!empty($validasi->is_verifikasi)) {
                $noPo = $validasi->{$noPo};
                return [
                    'status' => 422,
                    'text' => "Masih ada transaksi penerimaan pada No PO {$noPo} yang belum diverif di informasi penerimaan",
                    'title' => 'Proses Gagal!'
                ];
            }

            if(!empty($no_suratjalan)) {
                $noSurat = $request->post('no_suratjalan');
                return [
                    'status' => 422,
                    'text' => "Sudah terdapat transaksi dengan nomor surat jalan {$noSurat}",
                    'title' => 'Proses Gagal!'
                ];
            }

            if(!empty($no_faktur)) {
                $noFaktur = $request->post('no_faktur');
                return [
                    'status' => 422,
                    'text' => "Sudah terdapat transaksi dengan nomor faktur {$noFaktur}",
                    'title' => 'Proses Gagal!'
                ];
            }

            $ruanganId = Yii::$app->jwt->ruangan_id;
            $tglSuratJalan = $request->post('tgl_suratjalan',null);
            $tglSuratJalan = !empty($tglSuratJalan) ? date('Y-m-d H:i:s',strtotime($tglSuratJalan)) : null;

            $model->{$attrValid} = $id;
            $model->tgl_penerimaan = date('Y-m-d H:i:s');
            $model->supplier_id = $request->post('supplier_id');
            $model->no_suratjalan = $request->post('no_suratjalan');
            $model->tgl_suratjalan = $tglSuratJalan;
            $model->no_faktur = $request->post('no_faktur');
            $model->no_faktur_sementara = $request->post('no_faktur_sementara');
            $model->diterima_oleh = $request->post('diterima_oleh');
            $model->ruanganpenerima_id = $ruanganId;
            $model->peg_mengetahui = $request->post('peg_mengetahui');
            $model->peg_menyetujui = $request->post('peg_menyetujui');
            $model->catatan = $request->post('catatan');
            $model->is_verifikasi = false;

            if($type == DocoConstants::JENIS_OBAT) {
                $model->is_consigment = isset($validasi->is_consigment) ? $validasi->is_consigment : false;
            }

            if(empty($model->no_faktur_sementara) && empty($model->no_faktur)) {
                return [
                    'status' => 422,
                    'text' => "No Faktur / No Faktur Sementara harus di isi",
                    'title' => 'Proses Gagal!'
                ];
            }

            if ($model->save()) {
                $idParent = $model->{$primaryKey};
                $listData = $request->post('list_data',"{}");
                $listUpload = $request->post('file_upload',"{}");
                $dataDetail = json_decode($listData,true);
                $dataUpload = json_decode($listUpload,true);
                $tmpData = $tmpFile = [];

                /** Menyimpan Data ke file upload **/
                foreach ($dataUpload as $key => $value) {
                    $tmpFile[] = [
                        $primaryKey => $idParent,
                        'upload_berkas' => $value['upload_berkas'],
                        'catatan_berkas' => $value['catatan_berkas']
                    ];
                }

                if (!empty($tmpFile)) {
                    $upload::batchInsert($tmpFile);
                }

                /** Menyimpan ke penerimaan detail **/
                $totalPoBalance = 0;
                $idDetail = [];
                $idPenerimaanDetail = [];
                foreach ($dataDetail as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $attr) {
                            $qtyTerima = $attr['qty_diterima'];
                            $poBalance = $attr['po_balance'];
                            if ($qtyTerima > $poBalance) {
                                $transaction->rollBack();
                                return [
                                    'status' => 422,
                                    'text' => 'Penerimaan tidak boleh melebihi PO Balance',
                                    'title' => 'Proses Gagal!'
                                ];
                            }
                            $totalPoBalance += $poBalance;
                            array_push($idDetail, $attr['id_detail']);
                            array_push($idPenerimaanDetail, $idParent);
                            // if (empty($attr['qty_diterima'])) continue;
                            $jumlah = $qtyTerima * $attr['harga'];
                            $row = [
                                $primaryKey => $idParent,
                                $detailId => $attr['id_detail'],
                                $itemId => $attr['obat_barang_id'],
                                'qty_po' => $attr['qty_input'],
                                'po_balance' => $attr['po_balance'],
                                'qty_diterima' => $qtyTerima,
                                $konversiId => $attr['konversi_id'],
                                'tgl_kadaluarsa' => !empty($attr['tgl_kadaluarsa'])
                                    ? date('Y-m-d',strtotime($attr['tgl_kadaluarsa'])) : DocoConstants::DEFAULT_EXPIRED,
                                'no_batch' => !empty($attr['no_batch']) ? $attr['no_batch'] : null,
                                'harga' => $attr['harga'],
                                'discount' => $attr['discount'],
                                'discount_rp' => $attr['discount_rp'],
                                'jumlah' => $jumlah,
                                'keterangan' => !empty($attr['keterangan']) ? $attr['keterangan'] : null,
                            ];

                            if($qtyTerima > 0) {
                                $tmpData[] = $row;
                            }
                        }
                    }
                }

                $detail::batchInsert($tmpData);
                $validasi->status_penerimaan = StatusPenerimaanPoRepository::getStatusPenerimaan($detail::tableName(), $detailId, $idDetail, $validasiDetail, $attrValid, $id, $penerimaanDetailId, $tableReturDetail);
                $validasi->is_verifikasi = true;
                $validasi->peg_penerima_id = $model->diterima_oleh;
                if ($validasi->save()) {
                    $transaction->commit();
                    $getData = $model::find()->where([
                        $primaryKey => $idParent
                    ])->one();
                }

                return [
                    'no_penerimaan' => $getData->no_penerimaan,
                    'id_parent' => DocoHelpers::encrypt($idParent),
                    'type' => $type
                ];
            } else {
                return [
                    'status' => 422,
                    'data' => $model->errors
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            Yii::error($e->getMessage());
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

   /**
    * @controller actionCetakPenerimaan
    * @attribute #no_penerimaan# => Untuk menampilkan no penerimaan
    * @attribute #no_suratjalan# => Untuk menampilkan nomor surat jalan
    * @attribute #tgl_penerimaan# => Untuk menampilkan tanggal penerimaan
    * @attribute #tgl_suratjalan# => Untuk menampilkan tanggal surat jalan
    * @attribute #nama_supplier# => Untuk menampilkan nama supplier
    * @attribute #no_faktur# => Untuk menampilkan nomor faktur
    * @attribute #no_po# => Untuk menampilkan nomor purchase order
    * @attribute #catatan# => Untuk manampilkan catatan
    * @attribute #penerima_nama# => Untuk menampilkan nama penerima
    * @attribute #mengetahui_nama# => Untuk menampilkan nama pegawai mengetahui
    * @attribute #menyetujui_nama# => Untuk menampilkan nama pegawai menyetujui
    * @attribute #nama_ruangan# => Untuk menampilkan nama ruangan
    * @attribute #tabel_detail# => Untuk menampilkan data purchase order
    */
    public function actionCetakPenerimaan($id)
    {
        $print = new DocoPrint;

        $model = InfoPenerimaanObat::find()->where([
            'penerimaanobat_id' => $id
        ])->one();

        $detail = InfoPenerimaanObatDetail::find()->where([
            'penerimaanobat_id' => $id
        ])->all();

        $print->attributes = [
            '#no_penerimaan#' => !empty($model->no_penerimaan) ? $model->no_penerimaan : null,
            '#no_suratjalan#' => !empty($model->no_suratjalan) ? $model->no_suratjalan : null,
            '#tgl_penerimaan#' => !empty($model->tgl_penerimaan) ? date('d-M-Y',strtotime($model->tgl_penerimaan)) : null,
            '#tgl_suratjalan#' => !empty($model->tgl_suratjalan) ? date('d-M-Y',strtotime($model->tgl_suratjalan)) :null,
            '#nama_supplier#' => !empty($model->supplier_nama) ? $model->supplier_nama : null,
            '#no_faktur#' => !empty($model->no_faktur) ? $model->no_faktur : null,
            '#no_po#' => !empty($model->no_poobat) ? $model->no_poobat : null,
            '#catatan#' => !empty($model->catatan) ? $model->catatan : null,
            '#penerima_nama#' => !empty($model->menerima) ? $model->menerima : null,
            '#mengetahui_nama#' => !empty($model->mengetahui) ? $model->mengetahui : null,
            '#menyetujui_nama#' => !empty($model->menyetujui) ? $model->menyetujui : null,
            '#nama_ruangan#' => !empty($model->nama_ruangan) ? $model->nama_ruangan : null,
            '#tabel_detail#' => $this->renderPartial('_detailObat',[
                'detail' => $detail
            ]),
        ];
        $print->Output();
    }

   /**
    * @controller actionCetakPenerimaanBarang
    * @attribute #no_penerimaan# => Untuk menampilkan no penerimaan
    * @attribute #no_suratjalan# => Untuk menampilkan nomor surat jalan
    * @attribute #tgl_penerimaan# => Untuk menampilkan tanggal penerimaan
    * @attribute #tgl_suratjalan# => Untuk menampilkan tanggal surat jalan
    * @attribute #nama_supplier# => Untuk menampilkan nama supplier
    * @attribute #no_faktur# => Untuk menampilkan nomor faktur
    * @attribute #no_po# => Untuk menampilkan nomor purchase order
    * @attribute #catatan# => Untuk manampilkan catatan
    * @attribute #penerima_nama# => Untuk menampilkan nama penerima
    * @attribute #mengetahui_nama# => Untuk menampilkan nama pegawai mengetahui
    * @attribute #menyetujui_nama# => Untuk menampilkan nama pegawai menyetujui
    * @attribute #nama_ruangan# => Untuk menampilkan nama ruangan
    * @attribute #tabel_detail# => Untuk menampilkan data purchase order
    */
    public function actionCetakPenerimaanBarang($id)
    {
        $print = new DocoPrint;

        $model = InfoPenerimaanBarang::find()->where([
            'penerimaanbarang_id' => $id
        ])->one();

        $detail = InfoPenerimaanBarangDetail::find()->where([
            'penerimaanbarang_id' => $id
        ])->all();

        $print->attributes = [
            '#no_penerimaan#' => !empty($model->no_penerimaan) ? $model->no_penerimaan : null,
            '#no_suratjalan#' => !empty($model->no_suratjalan) ? $model->no_suratjalan : null,
            '#tgl_penerimaan#' => !empty($model->tgl_penerimaan) ? date('d-M-Y',strtotime($model->tgl_penerimaan)) : null,
            '#tgl_suratjalan#' => !empty($model->tgl_suratjalan) ? date('d-M-Y',strtotime($model->tgl_suratjalan)) :null,
            '#nama_supplier#' => !empty($model->supplier_nama) ? $model->supplier_nama : null,
            '#no_faktur#' => !empty($model->no_faktur) ? $model->no_faktur : null,
            '#no_po#' => !empty($model->no_pobarang) ? $model->no_pobarang : null,
            '#catatan#' => !empty($model->catatan) ? $model->catatan : null,
            '#penerima_nama#' => !empty($model->menerima) ? $model->menerima : null,
            '#mengetahui_nama#' => !empty($model->mengetahui) ? $model->mengetahui : null,
            '#menyetujui_nama#' => !empty($model->menyetujui) ? $model->menyetujui : null,
            '#nama_ruangan#' => !empty($model->nama_ruangan) ? $model->nama_ruangan : null,
            '#tabel_detail#' => $this->renderPartial('_detailObat',[
                'detail' => $detail
            ]),
        ];
        $print->Output();
    }

    public function actionGetPenerimaanTerakhir($no_po = "", $type = "")
    {
        $model = new InfoPenerimaanObat;
        $model_barang = new InfoPenerimaanBarang;

        $type = DocoHelpers::decrypt($type);

        switch ($type) {
            case 'obat':
                $query = $model->find()->where([
                    "no_poobat" => $no_po,
                    "is_verifikasi" => 0
                ])->one();

                $field_id = "penerimaanobat_id";
            break;

            case 'barang':
                $query = $model_barang->find()->where([
                    "no_pobarang" => $no_po,
                    "is_verifikasi" => 0
                ])->one();

                $field_id = "penerimaanbarang_id";
            break;

            default:
                return [
                    "text" => "Type tidak ditemukan",
                    "data" => [
                        "type" => $type
                    ]
                ];
            break;
        }

        if (is_null($query)) {
            $response = [
                "text" => "data tidak ditemukan",
                "penerimaan_id" => null
            ];
        }else{
            $id_penerimaan = $query->$field_id;
            $response = [
                "text" => "Transaksi belum diverifikasi",
                "penerimaan_id" => DocoHelpers::encrypt($id_penerimaan),
            ];
        }

        return $response;
    }
}
