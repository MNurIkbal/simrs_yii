<?php

namespace app\modules\v1\controllers;

use Doco\components\DocoConstansId;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\web\UploadedFile;
use yii\helpers\Json;
use app\modules\v1\models\InfoPemesananProduksiObatView;
use app\modules\v1\models\InfoPemesananProduksiObatDetailView;
use app\modules\v1\models\InfoProduksiObatAlkesView;
use app\modules\v1\models\InfoProduksiObatAlkesBahanBakuView;
use app\modules\v1\models\InfoProduksiObatAlkesDetailView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PemesananProduksiObat;
use app\modules\v1\models\PemesananProduksiObatDetail;
use app\modules\v1\models\ProduksiObatAlkes;
use app\modules\v1\models\ProduksiObatAlkesBahanBaku;
use app\modules\v1\models\ProduksiObatAlkesDetail;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\StokObatAlkesR;

class InfProduksiObatController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemesananProduksiObatView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        $actions['export-excel'] = 'app\modules\v1\actions\InfProduksiObat\ExportExcelAction';
        $actions['get-data-produksi'] = 'app\modules\v1\actions\InfProduksiObat\GetDataProduksiAction';
        $actions['get-list-produksi'] = 'app\modules\v1\actions\InfProduksiObat\GetListProduksiAction';
        $actions['get-data-expand-produksi'] = 'app\modules\v1\actions\InfProduksiObat\GetExpandProduksiAction';
        $actions['export-excel-produksi'] = 'app\modules\v1\actions\InfProduksiObat\ExportExcelProduksiAction';
        $actions['get-alert-Harga'] = 'app\modules\v1\actions\InfProduksiObat\GetAlertHargaAction';
        $actions['save-produksi'] = 'app\modules\v1\actions\InfProduksiObat\SaveProduksiAction';
        $actions['save-update-harga'] = 'app\modules\v1\actions\InfProduksiObat\SaveUpdateHargaAction';
        $actions['cek-ketersediaan'] = 'app\modules\v1\actions\InfProduksiObat\CekKetersediaanAction';
        return $actions;
    }

    public function actionInitIndex()
    {
        $lookupM = Lookup::find()
            ->select([
                'lookup_id',
                'lookup_name'
            ])
            ->where(['lookup_type' => ['status_pemesananproduksi', 'status_produksi']])
            ->asArray()
            ->all();

        return [
            'statusData' => ArrayHelper::map($lookupM, 'lookup_name', 'lookup_name'),
        ];
    }

    public function actionGetData(){
        $model = new InfoPemesananProduksiObatView;
        $query = $model::find();
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglpemesanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['nopemesanan'])){
                $query->andWhere(['nopemesanan'=>$_GET['advanced-filter']['nopemesanan']]);
                unset($_GET['advanced-filter']['nopemesanan']);
            }
            if(isset($_GET['advanced-filter']['status_pemesanan'])){
                $query->andWhere(['status_pemesanan'=>$_GET['advanced-filter']['status_pemesanan']]);
                unset($_GET['advanced-filter']['status_pemesanan']);
            }
        }
        $query->andWhere(['between', 'tglpemesanan', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionUpdateStatus(){
        $request = Yii::$app->request;
        $request = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pemesanan_produksi = PemesananProduksiObat::find()->where(['pemesananproduksiobat_id' => $request['id']])->one();
            $pemesanan_produksi->status_pemesanan = $request['data'] == 'batal' ? DocoConstants::BATAL_PEMESANAN : DocoConstants::SUDAH_VERIFIKASI_PESANAN;
            if($request['data'] == 'varifikasi'){
                $pemesanan_produksi->tgl_aprove = date('Y-m-d H:i:s');
    
                $pemesananObatDetail = PemesananProduksiObatDetail::find()
                    ->where([
                        'pemesananproduksiobat_id' => $request['id']
                    ])
                    ->asArray()
                    ->all();
    
                /**
                 * Header data.
                 */
                $produkObat = new ProduksiObatAlkes;
                $produkObat->pemesananproduksiobat_id = $request['id'];
                $produkObat->status_produksi = DocoConstants::SUDAH_VERIFIKASI_PESANAN;
                $produkObat->save();
    
                $payloadDetail = [];
                foreach ($pemesananObatDetail as $value) {
                    $payloadDetail[] = [
                        'produksiobatalkes_id' => $produkObat->produksiobatalkes_id,
                        'pemesananproduksiobatdetail_id' => $request['id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'satuankecil_id' => $value['satuan_id'],
                        'qty_produksi' => $value['qty_konversi'],
                        'created_date' => date('Y-m-d H:i:s'),
                        'is_deleted' => false,
                        'is_active' => true
                    ];
    
                }

                if(! empty($payloadDetail)){
                    ProduksiObatAlkesDetail::batchInsert($payloadDetail, true);
                }
            }
            if($pemesanan_produksi->save(false)){
                $transaction->commit();
                return [
                    'status' => 200,
                    'message' => 'Berhasil',
                    'text' => 'Pemesanan Berhasil Dibatalkan'
                ];
            }
            
            $transaction->rollBack();
            return [
                'status' => 422,
                'data' => $pemesanan_produksi->errors
            ];
        } catch (\Throwable $th) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }

    }

    public function actionListObatAlkes()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $term = isset($get['term']) ? $get['term'] : null;
        $page = isset($get['page']) ? $get['page'] : 1;

        $listObat = StokObatAlkesR::find()
            ->select([
                "obatalkes_m.obatalkes_id",
                "obatalkes_m.obatalkes_kode",
                "obatalkes_m.obatalkes_nama",
                "obatalkes_m.satuankecil_id",
                "obatalkes_m.harganetto as harganetto_ygdipakai",
                "satuankonversi_v.satuankecil_id",
                "satuankonversi_v.nilai_konversi",
                "satuankonversi_v.satuan_kecil as satuankecil_nama",
            ])
            ->join("JOIN", "obatalkes_m", "obatalkes_m.obatalkes_id = stokobatalkes_r.obatalkes_id")
            ->leftJoin("satuankonversi_v", "obatalkes_m.obatalkes_id = satuankonversi_v.obatalkes_id")
            ->where([
                'stokobatalkes_r.ruangan_id' => DocoConstants::RUANGAN_GUDANG_FARMASI,
                'obatalkes_m.is_produksi' => false,
                'satuankonversi_v.nilai_konversi' => 1
            ]);

        if ($term) {
            $listObat->andFilterWhere(['ILIKE', 'obatalkes_m.obatalkes_nama', $term]);
        }

        $perpage = 10;
        $limit = 11;

        $offset = ($page - 1) * $perpage;

        $listObat->offset($offset)->limit($limit);
        $obatData = $listObat->asArray()->all();
        $keyObat = [];
        foreach ($obatData as $value) {
            $keyObat[] = $value['obatalkes_id'];
        }
        $result['data'] = [];
        foreach ($obatData as $value) {
            $value['qty_tersedia'] = 0;
            $value['harganetto'] = $value['harganetto_ygdipakai'];
            $result['data'][] = $value;
        }
        return $result;
    }

    public function getSatuanKonversi($id, $is_produksi = false)
    {
        $result = [];
        $satuan = SatuanKonversiView::find()->select([
            'obatalkes_id',
            'satuan_besar',
            'satuanbesar_id',
            'satuankecil_id',
            'nilai_konversi'
        ])->where([
            'jenis'=>'obat',
            'obatalkes_id' => $id,
            'is_active' => 1
        ]);
        $satuan->andWhere(['nilai_konversi' => 1]);

        $satuan = $satuan->asArray()->all();
        foreach ($satuan as $value) :
            $result[$value['obatalkes_id']][] = $value;
        endforeach;
        return $result;
    }

    public function actionGetInfoProduksi(){
        $request = Yii::$app->request;
        $id = $request->get('id');

        $produksi = InfoProduksiObatAlkesView::find()->where(['produksiobatalkes_id' => $id])->asArray()->one();

        return $produksi;
    }
    
    /**
     * Retrieves the list of raw materials and header data for a given production order ID.
     *
     * @param int $pemesananProduksi The ID of the production order.
     * @throws \Throwable If an error occurs during the retrieval process.
     * @return array The header data, list of raw materials, and list of available rooms.
     */
    public function actionDefineMaterial()
    {
        $request = Yii::$app->request;
        try {
            $pemesananProduksi = $request->get('pemesananproduksi_id');
            $bahanProduksiPayload = [];
            $bahanProduksi = InfoProduksiObatAlkesDetailView::find()
                ->where([
                    'pemesananproduksiobatdetail_id' => $pemesananProduksi
                ])
                ->asArray()
                ->all();
            
            $produksidetailId = [];
            if(! empty($bahanProduksi)) {
                foreach ($bahanProduksi as $value) {
                    $produksidetailId[] = $value['produksiobatalkesdetail_id'];
                    $bahanProduksiPayload[] = [
                        "produksiObatId" => $value['produksiobatalkesdetail_id'],
                        "obatAlkesId" => $value['obatalkes_id'],
                        "namaObat" => $value['obatalkes_nama'],
                        "qty" => $value['qty_produksi'],
                        "satuan" => $value['satuan'],
                        "detailObat" => []
                    ];
                }
            }

            $getBahanBaku = $this->getBahanBaku($produksidetailId);
            foreach ($bahanProduksiPayload as $key => $value) {
                $detailObat = [];
                foreach ($getBahanBaku as $dataObat) {
                    if($value['produksiObatId'] == $dataObat['produksiObatId']) {
                        $detailObat[] = $dataObat;
                    }
                }
                $bahanProduksiPayload[$key]['detailObat'] = isset($detailObat) ? $detailObat : [];
            }
            
            $header = ProduksiObatAlkes::find()
                ->select([
                    'pemesananproduksiobat_t.pemesananproduksiobat_id',
                    'produksiobatalkes_t.produksiobatalkes_id',
                    'pemesananproduksiobat_t.nopemesanan',
                    'pemesananproduksiobat_t.tglpemesanan',
                    'pemesananproduksiobat_t.catatan_bahanbaku',
                    'pemesananproduksiobat_t.status_pemesanan',
                    'pemesananproduksiobat_t.instalasi_id',
                    'pemesananproduksiobat_t.ruangan_id',
                    'produksiobatalkes_t.status_produksi',
                    '(SELECT pegawai_m.nama_pegawai
                        FROM pegawai_m
                        WHERE pegawai_m.pegawai_id = pemesananproduksiobat_t.pegawaipemesanan_id
                    ) AS pegawai_pemesanan',
                    '( SELECT lookup_m.lookup_name
                        FROM lookup_m
                        WHERE lookup_m.lookup_id = pemesananproduksiobat_t.status_pemesanan
                    ) AS status_pemesanan_nama',
                    '( SELECT lookup_m.lookup_name
                        FROM lookup_m
                        WHERE lookup_m.lookup_id = produksiobatalkes_t.status_produksi
                    ) AS status_produksi_nama',
                ])
                ->join('JOIN','pemesananproduksiobat_t', 'pemesananproduksiobat_t.pemesananproduksiobat_id = produksiobatalkes_t.pemesananproduksiobat_id')
                ->where([
                    'pemesananproduksiobat_t.pemesananproduksiobat_id' => $pemesananProduksi
                ])
                ->asArray()
                ->one();
            
            $ruangan = RuanganView::find()->select([
                    'ruangan_id',
                    'ruangan_nama',
                    'instalasi_id',
                    'instalasi_nama'
                ])
                ->where(['instalasi_id' => $header['instalasi_id']])
                ->asArray()
                ->all();
                
            return [
                'status' => 200,
                'headerData' => $header,
                'bahanProduksi' => $bahanProduksiPayload,
                'ruangan' => $ruangan,
                'message' => 'Get data berhasil',
            ];
        } catch (\Throwable $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    /**
     * Retrieves the list of raw materials for a given production order detail ID.
     *
     * @param int $produsiobatalkesdetailId The ID of the production order detail.
     * @return array The list of raw materials including IDs, names, quantities, and prices.
     */
    public function getBahanBaku($produsiobatalkesdetailId)
    {
        return InfoProduksiObatAlkesBahanBakuView::find()
                ->select([
                    "produksiobatalkesbahanbaku_id",
                    "produksiobatalkesdetail_id as produksiObatId",
                    "obatalkes_id as obatAlkesId",
                    "obatalkes_nama as namaObat",
                    "satuan_kecil as satuan",
                    "satuankecil_id as satuanId",
                    "qty_obat as qty",
                    "harganetto_satuan as hargaSatuan",
                    "totalharga as hargaNetto"
                ])
                ->where(['produksiobatalkesdetail_id' => $produsiobatalkesdetailId])
                ->orderBy("produksiobatalkesbahanbaku_id", SORT_ASC)
                ->asArray()
                ->all();
    }

    /**
     * Saves the defined materials for a production order.
     *
     * @return array The response containing the status, message, and data.
     * @throws \Throwable If an error occurs during the transaction.
     */
    public function actionSaveDefineMaterial()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            /**
             * Declare variables
             */
            $pemesananId = $request->post('pemesananId');
            $bahanBaku = $request->post('data');
            $ruanganId = $request->post('ruanganId');
            $isEdit = filter_var($request->post('isEdit'), FILTER_VALIDATE_BOOLEAN);

            $headerProduksi = ProduksiObatAlkes::find()->where(['pemesananproduksiobat_id' => $pemesananId])->one();
            $produksiObatKey = ArrayHelper::getValue($post, 'data.produksi_obat');
            $payloadBahanBaku = [];
            $savePayload = [];

            if(! empty($headerProduksi)) {
                $pemesananData = PemesananProduksiObat::find()->where(['pemesananproduksiobat_id' => $pemesananId])->one();

                /**
                 * Update ruangan apabila berubah
                 */
                if(! empty($pemesananData) && $ruanganId != $pemesananData->ruangan_id) {
                    $pemesananData->ruangan_id = $ruanganId;
                    $pemesananData->save();
                }

                if(! empty($produksiObatKey)) {
                    $produksiArray = json_decode($produksiObatKey, true);
                    foreach ($produksiArray as $value) {
                        if(isset($bahanBaku[$value])) {
                            $decodevalue = json_decode($bahanBaku[$value], true);
                            $payloadBahanBaku[] = $decodevalue;
                        }
                    }
                }
                
                $conditionDelete = [];
                foreach ($payloadBahanBaku as $value) {
                    if(is_array($value)) {
                        foreach ($value as $detailValue) {
                            $conditionDelete[] = (int) $detailValue['produksiObatId'];
                            $savePayload[] = [
                                'produksiobatalkesdetail_id' => (int) $detailValue['produksiObatId'],
                                'obatalkes_id' => $detailValue['obatAlkesId'],
                                'qty_obat' => $detailValue['qty'],
                                'satuankecil_id' => $detailValue['satuanId'],
                                'harganetto_satuan' => $detailValue['hargaNetto'],
                                'harganetto' => (float) $detailValue['hargaNetto'] * (int) $detailValue['qty'],
                                'created_date' => date('Y-m-d H:i:s'),
                                'is_deleted' => false,
                                'is_active' => true
                            ];
                        }
                    }
                }
                
                /**
                 * Only status define material
                 */
                if(!$isEdit) {
                    $headerProduksi->status_produksi = DocoConstants::DEFINE_MATERIAL;
                    $headerProduksi->save();
                }

                ProduksiObatAlkesBahanBaku::updateAll([
                    'is_deleted' => true,
                ], [
                    'is_deleted' => false,
                    'produksiobatalkesdetail_id' => $conditionDelete
                ]);
    
                ProduksiObatAlkesBahanBaku::batchInsert($savePayload, true);
                $transaction->commit();

                Yii::$app->cache->delete('cache-produksi-obat-' . $pemesananId.'-'.Yii::$app->user->identity->pegawai_id);
                return [
                    'status' => 200,
                    'message' => 'Prodses define material telah berhasil !',
                    'data' => $payloadBahanBaku
                ];
            }

            \Yii::$app->response->statusCode = 404;
            return [
                'status' => 404,
                'message' => 'Data produksi obat tidak ditemukan',
                'data' => $payloadBahanBaku
            ];
        } catch (\Throwable $th) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionBatalProduksi()
    {
        $request = Yii::$app->request;
        try {
            $pemesananId = $request->post('pemesananproduksi_id');
            $produksi = ProduksiObatAlkes::find()->where(['pemesananproduksiobat_id' => $pemesananId])->one();

            if(! empty($produksi)) {
                if($produksi->status_produksi == DocoConstants::BATAL_PRODUKSI || $produksi->status_produksi == DocoConstants::PRODUKSI) {
                    Yii::$app->response->statusCode = 400;
                    return [
                        'status' => 400,
                        'message' => 'Proses batal produksi obat gagal !',
                    ];
                }

                $produksi->status_produksi = DocoConstants::BATAL_PRODUKSI;
                $produksi->save();
                
                return [
                    'status' => 200,
                    'message' => 'Proses batal produksi obat telah berhasil !',
                ];
            }

            Yii::$app->response->statusCode = 404;
            return [
                'status' => 404,
                'message' => 'Data produksi tidak ditemukan !',
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }
}
