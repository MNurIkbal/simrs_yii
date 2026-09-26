<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\db\Query;

use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Payterm;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoPoView;
use app\modules\v1\models\InfoPoDetailView;
use app\modules\v1\models\InfoSatuanKonversi;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Barang;

use app\modules\v1\models\PoManual;
use app\modules\v1\models\PoManualDetail;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\LaporanPurchaseOrderOutstandingView;
use Doco\components\constans\StatusPenerimaan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\Services\InternalService;

class InfoPurchaseOrderController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPoView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        $verbs["save"] = ["POST","PUT"];
        $verbs["delete"] = ["DELETE"];
        $verbs["cetak-rincian"] = ["GET"];
        $verbs["cetak-rincian-kop"] = ["GET"];
        $verbs["process-sync-rincian"] = ["GET"];
        $verbs["send-file-zip"] = ["GET", "POST"];
        $verbs["download-zip"] = ["GET"];
        $verbs["get-data-excel"] = ["GET"];
        $verbs["sync-export-excel"] = ["GET"];
        $verbs["drop-file"] = ["GET","POST"];
        $verbs["download-file"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['save']);
        unset($actions['view']);
        unset($actions['delete']);
        $action = [
            'save'                      => 'app\modules\v1\actions\InfoPurchaseOrder\SaveAction',
            'get-log-activity'          => 'app\modules\v1\actions\Allow\GetLogActivityAction',
            'laporan-po-outstanding'    => 'app\modules\v1\actions\InfoPurchaseOrder\LaporanPOOutstandingAction',
            'export-excel-po-outstanding' => 'app\modules\v1\actions\InfoPurchaseOrder\ExportExcelPOOutstandingAction',
            'laporan-analisa-po'    => 'app\modules\v1\actions\InfoPurchaseOrder\LaporanAnalisaPOAction',
            'export-excel-analisa-po' => 'app\modules\v1\actions\InfoPurchaseOrder\ExportExcelAnalisaPOAction',
            'split-po'                  => 'app\modules\v1\actions\InfoPurchaseOrder\SplitPOAction',
            'save-split-po'                  => 'app\modules\v1\actions\InfoPurchaseOrder\SaveSplitPOAction',
            'sync-export-excel-analisa-po'   => 'app\modules\v1\actions\InfoPurchaseOrder\SyncExportExcelAnalisaPoAction',
            'merge-po' => 'app\modules\v1\actions\InfoPurchaseOrder\MergePOAction',
            'process-sync-rincian' => 'app\modules\v1\actions\InfoPurchaseOrder\ProcessSyncRincianAction',
            'send-file-zip' => 'app\modules\v1\actions\InfoPurchaseOrder\SendFileZipAction',
            'download-zip' => 'app\modules\v1\actions\InfoPurchaseOrder\DownloadZipAction',
        ];
        return array_merge($actions, $action);
    }

    public function dateFilter($query, $request, $dateKey) {
        $advanced_filter = $request->get('advanced-filter');
        $start = $end = '01-01-01';
        if (empty(ArrayHelper::getValue($advanced_filter, 'tgl_pr')) && empty(ArrayHelper::getValue($advanced_filter, 'tgl_po'))) {
            $query->andWhere(['between', 'tgl_pr', $start, $end]);
        } else {
            if (!empty(ArrayHelper::getValue($advanced_filter, 'tgl_pr'))) {
                $explodePr = explode(" - ", $advanced_filter['tgl_pr']);
                if (count($explodePr) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explodePr[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explodePr[1]));
                }
                $query->andWhere(['between', 'tgl_pr', $start, $end]);
            } 
            if (!empty(ArrayHelper::getValue($advanced_filter, 'tgl_po'))) {
                $explodePo = explode(" - ", $advanced_filter['tgl_po']);
                if (count($explodePo) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explodePo[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explodePo[1]));
                }
                $query->andWhere(['between', 'tgl_po', $start, $end]);
            }
        }
    }

    public function actionIndex()
    {
        try {
            $query = $this->getData();

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    private function getData()
    {
        $filter = [];
        $pegawai_validasi = '';
        $start = $start_validasi =  date('Y-m-d 00:00:00');
        $end = $end_validasi = date('Y-m-d 23:59:00');
        $filter_tgl_po = false;
        $filter_tgl_buat_po = true;

        $pegawai_validasi = '';
        $poCito = $poAdmin = $poConsignment = null; 

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];

            if (isset($advancedFilter['tanggal_po_awal']) && isset($advancedFilter['tanggal_po_akhir'])) {
                $start = $advancedFilter['tanggal_po_awal'];
                $end = $advancedFilter['tanggal_po_akhir'];

                unset($advancedFilter['tanggal_po_awal']);
                unset($advancedFilter['tanggal_po_akhir']);
            }
            if (isset($advancedFilter['tanggal_po_awal_validasi']) && isset($advancedFilter['tanggal_po_akhir_validasi'])) {
                $start_validasi = $advancedFilter['tanggal_po_awal_validasi'];
                $end_validasi = $advancedFilter['tanggal_po_akhir_validasi'];

                unset($advancedFilter['tanggal_po_awal_validasi']);
                unset($advancedFilter['tanggal_po_akhir_validasi']);
                $filter_tgl_po = true;
            }

            if (isset($advancedFilter['pegawai_validasi'])){
                $pegawai_validasi = $advancedFilter['pegawai_validasi'];
            }

            if(isset($_GET['advanced-filter']['nomor'])) {
                $nomor = $_GET['advanced-filter']['nomor'];
            }

            if(isset($_GET['advanced-filter']['no_transaksi'])) {
                $no_transaksi = $_GET['advanced-filter']['no_transaksi'];
            }

            if (isset($advancedFilter['po_cito']) || isset($advancedFilter['po_admin']) || isset($advancedFilter['po_consigment'])) {
                $poCito = ArrayHelper::getValue($advancedFilter, 'po_cito');
                $poAdmin = ArrayHelper::getValue($advancedFilter, 'po_admin');
                $poConsignment = ArrayHelper::getValue($advancedFilter, 'po_consigment');

                unset($advancedFilter['po_cito']);
                unset($advancedFilter['po_admin']);
                unset($advancedFilter['po_consigment']);
            }

            foreach ($advancedFilter as $key => $value) {
                if(in_array($key, ['tanggal_po_awal', 'tanggal_po_akhir','pegawai_validasi', 'nomor', 'no_transaksi'])) {
                    continue;
                }


                $filter = array_merge($filter, [$key => $value]);
            }
        }

        $select = InfoPoView::columns();
        $groupBy = InfoPoView::columns();

        array_push($select, 'array_to_string(array_agg(distinct nomor),\', \') AS nomor');

        $model = new InfoPoView;
        $query = $model::find()->select($select);

        if (isset($advancedFilter['pegawai_validasi'])){
            $query->andWhere(['ILIKE', 'LOWER(pegawai_validasi)', strtolower($pegawai_validasi)]);
        }

        if (isset($advancedFilter['nomor'])){
            $query->andWhere(['ILIKE', 'LOWER(nomor)', strtolower($nomor)]);
        }

        if (isset($advancedFilter['no_transaksi'])){
            $query->andWhere(['ILIKE', 'LOWER(no_transaksi)', strtolower($no_transaksi)]);
        }

        if ($filter_tgl_buat_po) {
            $query->andWhere(['between', 'tanggal_buat_po', $start, $end]);
        }
        if ($filter_tgl_po) {
            $query->andWhere(['between', 'tanggal_po', $start_validasi, $end_validasi]);
        }

        if ($poCito) {
            $query->andWhere(['ILIKE', 'LOWER(po_cito)', strtolower($poCito)]);
        }
        if ($poAdmin) {
            $query->andWhere(['ILIKE', 'LOWER(po_admin)', strtolower($poAdmin)]);
        }
        if ($poConsignment) {
            $query->andWhere(['ILIKE', 'LOWER(po_consigment)', strtolower($poConsignment)]);
        }

        $query->andWhere($filter);

        $query->groupBy($groupBy);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionGetAttributes()
    {
        $status = Lookup::find()->where([
            'lookup_type' => 'status_penerimaan_po',
        ])->all();

        $pay = Payterm::find()->where([
            'is_active' => true
        ])->all();

        return [
            'status_po' => $status,
            'payterm' => $pay
        ];
    }

    /**
    * @controller actionCetakPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode RO
    * @attribute #nama_pegawai# => pegawai
    */
    public function actionCetakPdf()
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];

            if (isset($advancedFilter['tanggal_po_awal']) &&
                isset($advancedFilter['tanggal_po_akhir'])) {
                $start = $advancedFilter['tanggal_po_awal'];
                $end = $advancedFilter['tanggal_po_akhir'];
            }
        }

        if(isset($_GET['ruangan_id'])) {
            $connection = Yii::$app->db;
            $ruangan_id = $_GET['ruangan_id'];
            $jabatan_id = DocoConstants::VAR_J_K_R;

            $pegawai = $connection->createCommand("
                SELECT *
                FROM pegawai_v
                WHERE ruangan_id = {$ruangan_id}
                AND jabatan_id = {$jabatan_id}
            ")->queryOne();
        }

        $query = $this->getData();
        $data = $query->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => date('d M Y',strtotime($start)) . ' s/d ' . date('d M Y',strtotime($end)),
            '#nama_pegawai#' => isset($pegawai['nama_pegawai']) ? $pegawai['nama_pegawai'] : null,
            '#tanggal_cetak#' => date('d M Y H:i:s'),
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $data,
            ]),
        ];
        $print->Output();
    }

    /**
    * @controller actionCetakRincian
    * @attribute #nomor_po# => Untuk menampilkan Nomor PO
    * @attribute #tanggal_po# => Untuk menampilkan tanggal PO
    * @attribute #paymen_term# => Untuk menampilkan paymen Term
    * @attribute #pajak# => Untuk menampilkan Pajak
    * @attribute #supplier# => Untuk menampilkan nama Supplier
    * @attribute #alamat_supplier# => Untuk menampilkan alamat Supplier
    * @attribute #no_telp# => Untuk menampilkan no telpon supplier
    * @attribute #no_fax# => Untuk menampilkan no fax supplier
    * @attribute #rencana_terima# => Untuk menampilkan tanggal rencana terima
    * @attribute #ruangan# => Untuk menampilkan nama ruangan
    * @attribute #tabel# => Untuk menampilkan data detail PO
    * @attribute #catatan1# => Untuk menampilkan catatan 1
    * @attribute #catatan2# => Untuk menampilkan catatan 2
    * @attribute #dibuat_oleh# => Untuk menampilkan dibuat oleh
    * @attribute #menyetujui# => Untuk menampilkan Menyetujui
    * @attribute #mengetahui# => Untuk menampilkan Mengetahui
    */
    public function actionCetakRincian()
    {
        $this->cetakPOCounterLog();
        return Yii::$app->docoPlugin->execute('cetak_rincian_po');
    }

    /**
    * @controller actionCetakRincianKop
    * @attribute #nomor_po# => Untuk menampilkan Nomor PO
    * @attribute #tanggal_po# => Untuk menampilkan tanggal PO
    * @attribute #paymen_term# => Untuk menampilkan paymen Term
    * @attribute #pajak# => Untuk menampilkan Pajak
    * @attribute #supplier# => Untuk menampilkan nama Supplier
    * @attribute #alamat_supplier# => Untuk menampilkan alamat Supplier
    * @attribute #no_telp# => Untuk menampilkan no telpon supplier
    * @attribute #no_fax# => Untuk menampilkan no fax supplier
    * @attribute #rencana_terima# => Untuk menampilkan tanggal rencana terima
    * @attribute #ruangan# => Untuk menampilkan nama ruangan
    * @attribute #tabel# => Untuk menampilkan data detail PO
    * @attribute #catatan1# => Untuk menampilkan catatan 1
    * @attribute #catatan2# => Untuk menampilkan catatan 2
    * @attribute #dibuat_oleh# => Untuk menampilkan dibuat oleh
    * @attribute #menyetujui# => Untuk menampilkan Menyetujui
    * @attribute #mengetahui# => Untuk menampilkan Mengetahui
    */
    public function actionCetakRincianKop()
    {
        $this->cetakPOCounterLog();
        return Yii::$app->docoPlugin->execute('cetak_rincian_po_kop');
    }

   /**
    * temporary feature, should use print log feature in the feature
    */
    protected function cetakPOCounterLog()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;

        $id = is_array($request->get('id')) ? $request->get('id') : [$request->get('id')];
        $type_po = is_array($request->get('type_po')) ? $request->get('type_po') : [$request->get('type_po')];

        $request_merge = array_map(function($a, $b) {
              return ['id' => $a, 'type_po' => $b];
          }, $id, $type_po);

        $request_indexing = ArrayHelper::index($request_merge, null, 'type_po');

        $transaction = $connection->beginTransaction();
        try {
            foreach ($request_indexing as $key => $value) {
                $validasi_id = ArrayHelper::getColumn($value, 'id');
                $validasi_id = count($validasi_id) > 1 ? $validasi_id : $validasi_id[0];
                switch ($key) {
                    case DocoConstants::JENIS_OBAT:
                        ValidasiPoObat::updateAll(['tgl_tercetak' => date('Y-m-d H:i:s')], ['=', 'validasipoobat_id', $validasi_id]);
                        break;
                    case DocoConstants::JENIS_BARANG:
                        ValidasiPoBarang::updateAll(['tgl_tercetak' => date('Y-m-d H:i:s')], ['=', 'validasipobarang_id', $validasi_id]);
                        break;
                    default:
                        $model = null;
                        break;
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage(), 1  );
        }
    }

    public function actionExportExcel()
    {
        $title = 'Informasi Purchase Order';

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];

            if (isset($advancedFilter['tanggal_po_awal']) &&
                isset($advancedFilter['tanggal_po_akhir'])) {
                $start = $advancedFilter['tanggal_po_awal'];
                $end = $advancedFilter['tanggal_po_akhir'];
            }
        }

        $query = $this->getData();
        $data = $query->all();

        $result = [];
        $grandTotal = 0;

        foreach ($data as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal PO')] = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value['tanggal_buat_po']);
            $newValue[\Yii::t('app', 'Tanggal Validasi PO')] = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value['tanggal_po']);
            $newValue[\Yii::t('app', 'No Transaksi')] = $value['nomor'];
            $newValue[\Yii::t('app', 'Asal Transaksi')] = isset(DocoConstants::$statusAsal[$value['asal_transaksi']])
                                        ? DocoConstants::$statusAsal[$value['asal_transaksi']] : null;
            $newValue[\Yii::t('app', 'Nomor PO')] = $value['is_validasi'] ? $value['no_transaksi'] : null;
            $newValue[\Yii::t('app', 'Supplier')] = $value['supplier_nama'];
            $newValue[\Yii::t('app', 'Payment Term')] = $value['payment_term'];
            $newValue[\Yii::t('app', 'Total Harga PO')] = $value['total_harga_po'];
            $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Status Penerimaan')] = $value['stat_penerimaan'];
            $newValue[\Yii::t('app', 'Pegawai Validasi')] = $value['pegawai_validasi'];
            $newValue[\Yii::t('app', 'Tanggal Cetak PO')] = isset($value['tgl_cetak_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value['tgl_cetak_po']) : '';
            $newValue[\Yii::t('app', 'Cito ')] = ArrayHelper::getValue($value, 'po_cito');
            $newValue[\Yii::t('app', 'Admin')] = ArrayHelper::getValue($value, 'po_admin');
            $newValue[\Yii::t('app', 'Consigment')] = ArrayHelper::getValue($value, 'po_consigment');
            
            $result[$key] = $newValue;

            $grandTotal += $value['total_harga_po'];
            // $totalKey = $key + 1;
        }

        // if someone know the better way to do this, please tell me (Lukman)
        // $totalArr = [
        //     "Tanggal PO" => null,
        //     "No Transaksi" => null,
        //     "Asal Transaksi" => null,
        //     "Nomor PO" => null,
        //     "Supplier" => null,
        //     "Grand Total" => "Grand Total",
        //     "Total Harga PO" => $grandTotal
        // ];

        // $result[$totalKey] = $totalArr;

        $header = [];
        $header['Tanggal cetak'] = (date('d M Y H:i:s'));
        $header['Periode Transakasi'] = ((date('d M Y',strtotime($start)). " - ". date('d M Y', strtotime($end))));

        if (!empty($advancedFilter['nomor'])) {
            $header['Nomor PO'] = @$value['nomor'];
        }

        if (!empty($advancedFilter['asal_transaksi'])) {
            $header['Asal Transakasi'] = isset(DocoConstants::$statusAsal[@$value['asal_transaksi']])
                                        ? DocoConstants::$statusAsal[@$value['asal_transaksi']] : null;
        }

        if (!empty($advancedFilter['no_transaksi'])) {
            $header['No Transakasi'] = @$value['no_transaksi'];
        }

        if (!empty($advancedFilter['supplier_id'])) {
            $header['Supplier'] = @$value['supplier_nama'];
        }

        if (!empty($advancedFilter['payterm_id'])) {
            $header['Payment Term'] = @$value['payment_term'];
        }

        if (!empty($advancedFilter['ruangan_id'])) {
            $header['Ruangan'] = @$value['ruangan_nama'];
        }

        if (!empty($advancedFilter['lookup_id'])) {
            $header['Status Penerimaan'] = @$value['stat_penerimaan'];
        }

        if (!empty($advancedFilter['po_cito'])) {
            $header['Cito'] = ArrayHelper::getValue($advancedFilter, 'po_cito');
        }
        if (!empty($advancedFilter['po_admin'])) {
            $header['Admin'] = ArrayHelper::getValue($advancedFilter, 'po_admin');
        }
        if (!empty($advancedFilter['po_consigment'])) {
            $header['Consigment'] = ArrayHelper::getValue($advancedFilter, 'po_consigment');
        }

        $options = [
            "uploadPath" => "./uploads",
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'date'
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'date'
                ]
            ],
            "totalCount" => [
                'title' => 'Grand Total',
                'columnLabel' => "G",
                'columnValue' => "I",
                'value' => !empty($grandTotal) ? $grandTotal : 0,

            ]
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options,[],[],true);

        $filePath->save('php://output');
        die;
    }

    public function actionGetDetail($id, $type_po)
    {
        $payterm = Payterm::find()->where([
            'is_active' => true
        ])->all();

        $pajak = Pajak::find()->where([
            'is_active' => true
        ])->all();

        $supplier = Supplier::find()->select(['supplier_id','supplier_nama'])->where(['is_active'=>true])->all();

        $header = InfoPoView::find()->where([
            'transaksi_id' => $id,
            'type_po' => $type_po
        ])->one();

        $detail = InfoPoDetailView::find()->where([
            'transaksi_id' => $id,
            'jenis' => $type_po
        ])->orderBy([
            'nomor' => SORT_ASC,
            'obat_barang_id' => SORT_ASC
        ])->all();

        $filterItem = [];
        $is_obat = 'obat';
        foreach ($detail as $value) {
            if (empty($value['is_obat'])) {
                $is_obat = 'barang';
            }
            $filterItem[] = $value['obat_barang_id'];
        }

        $satuanKonversi = InfoSatuanKonversi::find()->where([
            'jenis' => $is_obat,
            'obatalkes_id' => $filterItem,
            'is_active' => true,
            'is_deleted' => false,
        ])->all();

        $master_harga = [];
        if($type_po == DocoConstants::JENIS_OBAT){
            $obatalkes_master = ObatAlkes::find()->select([
                    'obatalkes_id', 'obatalkes_nama', 'harganetto'
                ])->where([
                    'IN', 'obatalkes_id', $filterItem
                ])->asArray()->all();

            foreach ($obatalkes_master as $key => $value) {
                $master_harga[$value['obatalkes_id']] = $value['harganetto'];
            }
        }else if($type_po == DocoConstants::JENIS_BARANG){
            $barang_master = Barang::find()->select([
                    'barang_id', 'barang_nama', 'barang_harganetto'
                ])->where([
                    'IN', 'barang_id', $filterItem
                ])->asArray()->all();

            foreach ($barang_master as $key => $value) {
                $master_harga[$value['barang_id']] = $value['barang_harganetto'];
            }
        }

        $konversi = $hasilKonversi = $labelKonversi = [];
        $nilaiDefault = [];
        foreach ($satuanKonversi as $value) {
            $konversi[$value['obatalkes_id']][$value['satuankonversi_id']] = $value['satuan_besar'];
            if ($value['satuanbesar_id'] == $value['satuankecil_id']) {
                $nilaiDefault[$value['obatalkes_id']] = $value['satuankonversi_id'];
            }
            $hasilKonversi[$value['obatalkes_id']][$value['satuankonversi_id']] = $value['nilai_konversi'];
            $detailKonversi[$value['obatalkes_id']][$value['satuankonversi_id']] = '1 '.$value['satuan_besar'].' = '.$value['nilai_konversi'].' '.$value['satuan_kecil'];
            $labelKonversi[$value['obatalkes_id']][$value['satuankonversi_id']] = ["kecil" => $value['satuan_kecil'], "besar" => $value['satuan_besar']];
        }

        return [
            'header' => $header,
            'detail' => $detail,
            'payterm' => $payterm,
            'pajak' => $pajak,
            'supplier' => $supplier,
            'konversi' => $konversi,
            'master_harga' => $master_harga,
            'hasil_konversi' => $hasilKonversi,
            'label_konversi' => $labelKonversi,
            'detail_konversi' => $detailKonversi,
            'nilai_default' => $nilaiDefault
        ];
    }

    public function actionSaveBak($id, $type_po)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            switch ($type_po) {
                case DocoConstants::JENIS_OBAT:
                    $model = ValidasiPoObat::find()->where([
                        'validasipoobat_id' => $id
                    ])->one();
                    $no_po = $model->no_poobat;
                    $detailTabel = ValidasiPoObatDetail::getTableSchema()->name;
                    $primarykey = 'validasipoobatdetail_id';
                    break;
                case DocoConstants::JENIS_BARANG:
                    $model = ValidasiPoBarang::find()->where([
                        'validasipobarang_id' => $id
                    ])->one();
                    $no_po = $model->no_pobarang;
                    $detailTabel = ValidasiPoBarangDetail::getTableSchema()->name;
                    $primarykey = 'validasipobarangdetail_id';
                    break;
                default:
                    Yii::$app->response->statusCode = 500;
                    return ['message' => 'Tipe PO tidak boleh kosong'];
                    break;
            }
            $list_data = $request->post('list_data','{}');
            $list_data = json_decode($list_data,true);
            $sub_total = $total_discount = 0;
            if (is_array($list_data)) {
                foreach ($list_data as $key => $value) {
                    if (!empty($detailTabel) && !empty($primarykey)) {
                        $attributes = [
                            'harga' => $value['harga'],
                            'discount' => $value['discount'],
                            'discount_rp' => $value['discount_rp'],
                            'jumlah' => $value['total_harga'],
                            'qty_input' => $value['qty'],
                            'additional_data' => json_encode([
                                'qty_sekarang' => $value['nilai_konversi']
                            ])
                        ];
                        $sub_total += $value['total_harga'];
                        $total_discount += $value['discount_rp'];
                        if ($value['is_obat']) {
                            $attributes['s_konversiobt_id'] = $value['satuan_id'];
                        } else {
                            $attributes['s_konversibrg_id'] = $value['satuan_id'];
                        }
                        $condition = [
                            $primarykey => $value['id_detail']
                        ];
                        $connection->createCommand()->update($detailTabel, $attributes,$condition)->execute();
                    }
                }
            }
            $tgl_rencana = $request->post('tgl_rencanaterima');
            $model->diorder_oleh = $request->post('diorder_oleh');
            $model->tgl_rencanaterima = !empty($tgl_rencana) ? date('Y-m-d',strtotime($tgl_rencana)) : null;
            $model->payterm_id = $request->post('payterm_id');
            $model->pajak_id = $request->post('pajak_id');
            $model->peg_mengetahui_id = $request->post('peg_mengetahui_id');
            $model->peg_menyetujui_id = $request->post('peg_menyetujui_id');
            $model->catatan1 = $request->post('catatan1');
            $model->catatan2 = $request->post('catatan2');
            $model->sub_total = $sub_total;
            $model->total_discount = $total_discount;

            $pajak = null;
            if ($model->pajak_id) {
                $pajak = Pajak::find()->where([
                    'is_active' => true,
                    'pajak_id' => $model->pajak_id
                ])->one();
            }
            $pajakPersen = !empty($pajak->pajak_persen) ? $pajak->pajak_persen : 0;
            $model->ppn_persen = $pajakPersen;
            $model->ppn_nilai = ($pajakPersen / 100) * $model->sub_total;
            $model->total = $model->sub_total + $model->ppn_nilai;
            $is_validasi = $request->post('is_validasi');
            if (!empty($is_validasi)) {
                $model->tgl_validasi = date('Y-m-d H:i:s');
                $model->is_validasi = true;
            }
            if ($model->save()) {
                $transaction->commit();
                if ($is_validasi) {
                    return [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Pesanan berhasil divalidasi dengan no PO ' . $no_po,
                        'no_po' => $no_po
                    ];
                }
                return [
                    'message' => 'Data berhasil disimpan'
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
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete($id, $type_po)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $cancelNote = $request->post('catatan', '');
        $currentTime = date('Y-m-d H:i:s', time());
        try {
            switch ($type_po) {
                case DocoConstants::JENIS_OBAT:
                    $data = ValidasiPoObat::getDefaultData();
                    $currentId = $data->by;
                    $currentDate = $data->date;
                    $model = ValidasiPoObat::updateAll([
                        'status_penerimaan' => DocoConstants::STATUS_BATAL_PO,
                        'catatan' => $cancelNote,
                        'tgl_batal_po' => $currentTime,
                        'last_modified_date' => $currentDate,
                        'last_modified_by' => $currentId
                    ],[
                        'validasipoobat_id' => $id
                    ]);
                    $detailTabel = (new ValidasiPoObatDetail)->delete([
                        'validasipoobat_id' => $id
                    ]);
                    break;
                case DocoConstants::JENIS_BARANG:
                    $data = ValidasiPoBarang::getDefaultData();
                    $currentId = $data->by;
                    $currentDate = $data->date;
                    $model = ValidasiPoBarang::updateAll([
                        'status_penerimaan' => DocoConstants::STATUS_BATAL_PO,
                        'catatan' => $cancelNote,
                        'tgl_batal_po' => $currentTime,
                        'last_modified_date' => $currentDate,
                        'last_modified_by' => $currentId
                    ],[
                        'validasipobarang_id' => $id
                    ]);
                    $detailTabel = (new ValidasiPoBarangDetail)->delete([
                        'validasipobarang_id' => $id
                    ]);
                    break;
                default:
                    Yii::$app->response->statusCode = 500;
                    return ['message' => 'Tipe PO tidak boleh kosong'];
                    break;
            }
            $transaction->commit();
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dibatalkan'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function getDataExcel($param)
    {       
        $model = new LaporanPurchaseOrderOutstandingView;
        $dateFilter = 'tgl_po_dibuat';
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($param['advanced-filter'])) {
            $advancedFilter = $param['advanced-filter'];
            if(!empty($advancedFilter['tgl_po_dibuat'])) {
                $explode = explode(" - ", $advancedFilter['tgl_po_dibuat']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
        }
        $query->andWhere(['between', $dateFilter, $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataExcel($getData)->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPurchaseOrderExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportPurchaseOrder' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadLaporanPurchaseOrderExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost)
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;

            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/laporan_purchase_order_outstanding.xlsx';

        if (file_exists($fileName))
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            unlink($filename);
            die();
        }
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $fileName = $rootPath.'/'.$no_request;

        if (file_exists($fileName))
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }

    public function actionValidasi($id, $type_po)
    {
        return $this->runAction('save', ['id' => $id, 'type_po' => $type_po]);
    }
}
