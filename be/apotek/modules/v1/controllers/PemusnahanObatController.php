<?php

/**
 * @author: yaya
 * @since 6 June 2018
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\cache\Cache;

use app\modules\v1\models\DetailPemesananObatAlkes;
use app\modules\v1\models\InfoObatExpired;
use app\modules\v1\models\InfoPemusnahanObat;
use app\modules\v1\models\InfoPemusnahanObatDetail;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\MutasiObatDetail;
use app\modules\v1\models\MutasiObatRuangan;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PemusnahanObat;
use app\modules\v1\models\PemusnahanObatDetail;
use app\modules\v1\models\PesanObatAlkes;
use app\modules\v1\models\PesanObatDetail;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\DetailMutasiObatAlkesView;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\businessLogic\StokObatAlkes as BLStokObatAlkes;

use app\modules\v1\models\forms\PemusnahanForm;
use app\modules\v1\entities\ObatExpire;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

use Doco\Services\InternalService;

class PemusnahanObatController extends DocoActiveController
{
    public $modelClass = PemusnahanObat::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["GET"];
        $verbs["get-options"] = ["GET"];
        $verbs["save"] = ["POST"];
        $verbs["print"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['save']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoObatExpired;
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $expiry_range = "+1 month";

            $lookup_range_tanggal = Lookup::find()
                ->where(["lookup_type" => "range_bulan"])
                ->select("lookup_name, lookup_value, lookup_id")
                ->asArray()->all();

            $range_date = ArrayHelper::map($lookup_range_tanggal, "lookup_id", "lookup_value");

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tglkadaluarsa'])) {
                    $selected_range = $_GET['advanced-filter']['tglkadaluarsa'];
                    if (array_key_exists($selected_range, $range_date)) {
                        $expiry_range = $range_date[$selected_range];
                    }
                    unset($_GET['advanced-filter']['tglkadaluarsa']); // Unset Advanced Filter  date range
                }

                if(isset($_GET['advanced-filter']['obatalkes_nama'])) {
                    $query->andWhere(['ILIKE','obatalkes_nama',$_GET['advanced-filter']['obatalkes_nama']]);
                }

                if(isset($_GET['advanced-filter']['instalasi_id'])) {
                    $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);
                }

                if(isset($_GET['advanced-filter']['ruangan_id'])) {
                    $query->andWhere(['ruangan_id' => $_GET['advanced-filter']['ruangan_id']]);
                }
            }
            $query->andWhere(['<=', 'tglkadaluarsa', date('Y-m-d', strtotime($expiry_range))]);
            $query->andWhere(['>', 'stok_exp', 0]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetOptions()
    {
        return [
            'instalasi' => Cache::getListInstalasi(),
            'ruangan' => Cache::getListRuangan(),
            'date_range' => Cache::getListRangeTanggal(),
        ];
    }

    public function actionView($id)
    {
        $cond = [
            'pemusnahanobat_id' => $id
        ];
        return [
            'header' => InfoPemusnahanObat::find()->where($cond)->one(),
            'detail' => InfoPemusnahanObatDetail::find()->where($cond)->all(),
        ];
    }

    public function actionGetPegawai()
    {
        $request = Yii::$app->request;
        $ruangan = Yii::$app->jwt->ruangan_id;
        $get = $request->get();
        $result = PegawaiView::find()->select([
            'pegawai_id',
            'nama_pegawai',
            'nomorindukpegawai'
        ]);
        $result->groupBy([
            'pegawai_id',
            'nama_pegawai',
            'nomorindukpegawai'
        ]);
        $result->orderBy(['nama_pegawai'=>SORT_ASC]);
        if (!empty($ruangan)) {
            $result->where([
                'ruangan_id' => $ruangan
            ]);
        }
        if (isset($get['term'])) {
           $result->andFilterWhere(['OR',
               ['ILIKE', 'nama_pegawai', $get['term']],
               ['ILIKE', 'nomorindukpegawai', $get['term']]
           ]);
           return $result->limit(10)->all();
        }
        return $result->all();
    }


    /**
    * @controller actionPrint
    * @attribute #table_pemusnahan# => Untuk Menampilkan Tabel pemakaian obat alkes
    * @attribute #no_pemusnahan# => untuk menampilkan nomor pemakaian
    * @attribute #tgl_pemusnahan# => untuk menampilkan Tanggal pemakaian
    * @attribute #instalasi# => untuk menampilkan Ruangan pemakaian
    * @attribute #ruangan# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_menyetujui# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_mengetahui# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_retur# => untuk menampilkan Ruangan pemakaian
    **/
    public function actionPrint($id)
    {
        $request = Yii::$app->request;
        $print = new DocoPrint();

        $header = InfoPemusnahanObat::find()->where([
            'pemusnahanobat_id' => $id
        ])->asArray()->one();

        $query = InfoPemusnahanObatDetail::find()->where([
            'pemusnahanobat_id' => $id
        ])->asArray()->all();


        $print->attributes = [
            '#table_pemusnahan#' => $this->renderPartial('index',[
                'detail' => $query
            ]),
            '#no_pemusnahan#' => $header['nopemusnahan'],
            '#tgl_pemusnahan#' => date('d-m-Y',strtotime($header['tglpemusnahan'])),
            '#instalasi#' => $header['instalasi_nama'],
            '#ruangan#' => $header['ruangan_nama'],
            '#pegawai_menyetujui#' => $header['pegawai_menyetujui'],
            '#pegawai_mengetahui#' => $header['pegawai_mengetahui'],
            '#pegawai_retur#' => $header['nama_pegawai'],
        ];
        $print->Output();

    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $user = Yii::$app->jwt;
        $transaction = $connection->beginTransaction();
        $parentId = null;
        try {
            $model = new PemusnahanForm;
            $model->attributes = $request->post();
            if ($model->validate()) {
                $tanggal = $request->post('tanggal_pemusnahan',null);
                $tanggal = !empty($tanggal) ? date('Y-m-d',strtotime($tanggal)) : date('Y-m-d');
                $pemusnahan = new PemusnahanObat;
                $pemusnahan->keterangan = "Pemusnahan Obat";
                $pemusnahan->ruangan_id = $user->ruangan_id;
                $pemusnahan->pegawai_id = $user->user->pegawai_id;
                $pemusnahan->pegawaimengetahui_id = $request->post('pegawai_mengetahui');
                $pemusnahan->pegawaimenyetujui_id = $request->post('pegawai_meyetujui');
                $pemusnahan->tglpemusnahan = $tanggal .' '. date("H:i:s");
                $pemusnahan->total_harganetto = $request->post('total_netto',0);
                if ($pemusnahan->save(false)) {
                    $detail = [];
                    $detailItems = $request->post('detail',[]);
                    $parentId = $pemusnahan->pemusnahanobat_id;
                    $obatalkesId = [];
                    foreach ($detailItems as $key => $value) {
                        $detail[] = [
                            'pemusnahanobat_id' => $parentId,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'jumlah' => $value['stok'],
                            'tglkadaluarsa' => $value['tglkadaluarsa'],
                            'nobatch' => $value['nobatch'],
                            'kondisibarang' => 'Kadaluarsa',
                            'harganetto' => $value['jumlah_harganetto'],
                            'additional_data' => json_encode([
                                'id_stok' => $value['id_stok'],
                                'ruangan_id' => $value['ruangan_id'],
                                'satuankecil_id' => $value['satuankecil_id']
                            ])
                        ];
                        if(!in_array($value['obatalkes_id'], $obatalkesId)){
                            $obatalkesId[] = $value['obatalkes_id'];
                        }
                    }
                    $getDataPemesanan = DetailPemesananObatAlkes::find()->where(['obatalkes_id'=>$obatalkesId, 'ruangan_id'=>$user->ruangan_id, 'statuspesan'=>DocoConstants::STATUS_PESAN_BELUM_DIKIRIM])->select(['pesanobatalkes_id'])->groupBy(['pesanobatalkes_id'])->asArray()->all();
                    if(count($getDataPemesanan)):
                        $deletedId = [];
                        foreach ($getDataPemesanan as $key => $value) {
                            if(!in_array($value['pesanobatalkes_id'], $deletedId)){
                                $deletedId[] = $value['pesanobatalkes_id'];
                            }
                        }
                        $now = date('Y-m-d H:i:s');
                        $pesanId = '('.implode(',', $deletedId).')';
                        // (new PemusnahanObatDetail)->delete([
                        //     'pesanobatalkes_id' => $deletedId
                        // ]);
                        $query = "UPDATE pesanobatalkes_t SET is_deleted = true, deleted_by = {$user->user->pegawai_id}, deleted_date = '{$now}' WHERE pesanobatalkes_id IN {$pesanId}";
                        $updateHeader = $connection->createCommand($query)->execute();
                        $queryDetail = "UPDATE pesanobatdetail_t SET is_deleted = true, deleted_by = {$user->user->pegawai_id}, deleted_date = '{$now}' WHERE pesanobatalkes_id IN {$pesanId}";
                        $updateDetail = $connection->createCommand($queryDetail)->execute();
                    endif;
                    PemusnahanObatDetail::batchInsert($detail, false);
                    $getPemusnahan = PemusnahanObat::find()->where(['pemusnahanobat_id'=>$parentId])->asArray()->one();
                    $transaction->commit();
                    return [
                        'message' => "success",
                        'parent_id' => DocoHelpers::encrypt($parentId),
                        'nopemusnahan' => isset($getPemusnahan['nopemusnahan']) ? $getPemusnahan['nopemusnahan'] : '',
                    ];
                }
            }

            return [
                'status' => 422,
                'data' => $model->errors
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
    public function actionSaveMutasi()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $user = Yii::$app->jwt;
        $transaction = $connection->beginTransaction();
        $parentId = null;
        $totalharganettomutasi = 0;
        $konfigFarmasi = KonfigFarmasi::find(1)->asArray()->one();
        $disc = !is_null($konfigFarmasi) && isset($konfigFarmasi['persen_diskon']) ? $konfigFarmasi['persen_diskon'] : 0;
        try {
            $post = $request->post();
            $detailObat = $post['detail'];
            $totalharganettomutasi = array_sum(array_map(function($item) {
                return $item['harganetto'] * $item['stok_exp'];
            }, $detailObat));
            $model = new MutasiObatRuangan;
            $model->tglmutasioa = $post['tglmutasi'];
            $model->ruanganasal_id = $post['ruangan_id'];
            $model->ruangantujuan_id = $post['ruangan_penerima_id'];
            $model->totalharganettomutasi = $totalharganettomutasi;
            $model->status_mutasi = DocoConstants::STATUS_MUTASI_DIKIRIM;
            $model->pegawaimenyetujui_id = @$post['pegawai_mengetahui'];
            $model->pegawaimengetahui_id = @$post['pegawai_mengetahui'];
            $stokOut = [];
            if($model->validate() && $model->save()){
                $parentId = $model->mutasiobatruangan_id;
                $detailMutasi = $updateStok = [];
                foreach ($detailObat as $key => $value) {
                    $detailMutasi[] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'mutasiobatruangan_id' => $parentId,
                        'jumlah_mutasi' => $value['stok_exp'],
                        'jumlah_pesan' => 0,
                        'harga_jualsatuan' => 0,
                        'persen_discount' => 0,
                        'harga_netto' => $value['harganetto'],
                        'total_harga' => $value['harganetto'] * $value['stok_exp'],
                        'tgl_kadaluarsa' => $value['tglkadaluarsa'],
                        'satuanbesar_id'=>$value['satuankecil_id'],
                        'jumlah_input'=>$value['stok_exp'],
                        'cost'=>$value['cost_wa']
                    ];

                    /*
                    * belum potong stok
                    * potong stok ada di penerimaan
                    if(!isset($updateStok[$value['obatalkes_id']])){
                        $updateStok[$value['obatalkes_id']]['qty_out'] = $value['stok'];
                        $updateStok[$value['obatalkes_id']]['stokobatalkesasal_id'] = DocoHelpers::decrypt($key);
                    }else{
                        $updateStok[$value['obatalkes_id']]['qty_out'] += $value['stok'];
                    }
                    */
                }
                MutasiObatDetail::batchInsert($detailMutasi);

                $tanggalBerlaku = date('Y-m-d');
                // Mencari Metode
                $konfig = Yii::$app->db->createCommand("
                    SELECT metodeantrian FROM konfigfarmasi_k
                    WHERE tglberlaku >= '{$tanggalBerlaku}'
                    AND konfigfarmasi_aktif = true
                    AND is_active = true
                ")->queryOne();

                $currentMetode = BLStokObatAlkes::FEFO;
                if ($konfig) {
                    $currentMetode = isset($konfig['metodeantrian'])
                                        ? strtoupper($konfig['metodeantrian']) : BLStokObatAlkes::FEFO;
                }

                $mDetail = new DetailMutasiObatAlkesView;
                $dataobat = $mDetail::find(true)->where([
                    'mutasiobatruangan_id' => $parentId
                ])->asArray()->all();

                /*
                    * belum potong stok
                    * potong stok ada di penerimaan
                foreach ($dataobat as $key => $value) {
                    $stokOut[$value['obatalkes_id']] = [
                        'ruangan_id' => $post['ruangan_penerima_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'mutasiobatdetail_id' => $value['mutasiobatdetail_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'qty_satuanpakai' => $updateStok[$value['obatalkes_id']]['qty_out']
                    ];
                }

                if ($currentMetode === BLStokObatAlkes::FEFO) {
                    $methode = BLStokObatAlkes::methodeFEFO($stokOut, $tanggalBerlaku);
                } else {
                   $methode = BLStokObatAlkes::methodeFIFO($stokOut, $tanggalBerlaku);
                }
                */

                $getMutasi = MutasiObatRuangan::find()->where(['mutasiobatruangan_id' => $parentId])->asArray()->one();
                $transaction->commit();
                return [
                        'message' => "success",
                        'parent_id' => $parentId,
                        'nomutasioa' => isset($getMutasi['nomutasioa']) ? $getMutasi['nomutasioa'] : '',
                    ];
            }

            return [
                'status' => 422,
                'data' => $model->errors
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

    public function actionSyncExportExcel() {
        $request = Yii::$app->request;
        $getData = $request->get();
        $headerOwner = $request->getHeaders()->get('X-Owner');
        $auth_token = $request->getHeaders()->get('Authorization');

        if(isset($getData['page'])) unset($getData['page']);
        if(isset($getData['per-page'])) unset($getData['per-page']);

        $countData = $this->getLaporanExcel()->count();
        $fetchLimit = 50;
        $randomStr = isset($getData['randomStr']) ? $getData['randomStr'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanObatAlkesExpired' => [
                    'token' => $auth_token,
                    'xOwner' => $headerOwner,
                    'unique_str' => $randomStr,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcelLaporanObatAlkesExpired' => [
                    'token' => $auth_token,
                    'xOwner' => $headerOwner,
                    'unique_str' => $randomStr,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadExcelLaporanObatAlkesExpired' => [
                    'token' => $auth_token,
                    'xOwner' => $headerOwner,
                    'unique_str' => $randomStr,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randomStr' => $randomStr,
            'countData' => $countData,
        ];
    }

    public function actionDownloadFileExcel() {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $filename = $dir.'/Laporan Obatalkes Expired.xlsx';

        if(file_exists($filename)) {
            $file = basename($filename);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($filename);
            die();
        }
    }

    private function getLaporanExcel() {
        $model = new InfoObatExpired;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $expiry_range = "+1 month";

        $lookup_range_tanggal = Lookup::find()
            ->where(["lookup_type" => "range_bulan"])
            ->select("lookup_name, lookup_value, lookup_id")
            ->asArray()->all();

        $range_date = ArrayHelper::map($lookup_range_tanggal, "lookup_id", "lookup_value");

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglkadaluarsa'])) {
                $selected_range = $_GET['advanced-filter']['tglkadaluarsa'];
                if (array_key_exists($selected_range, $range_date)) {
                    $expiry_range = $range_date[$selected_range];
                }
                unset($_GET['advanced-filter']['tglkadaluarsa']); // Unset Advanced Filter  date range
            }

            if(isset($_GET['advanced-filter']['obatalkes_nama'])) {
                $query->andWhere(['ILIKE','obatalkes_nama', $_GET['advanced-filter']['obatalkes_nama']]);
            }

            if(isset($_GET['advanced-filter']['instalasi_id'])) {
                $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);
            }

            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                $query->andWhere(['ruangan_id' => $_GET['advanced-filter']['ruangan_id']]);
            }
        }
        $query->andWhere(['<=', 'tglkadaluarsa', date('Y-m-d', strtotime($expiry_range))]);
        $query->andWhere(['>', 'stok_exp', 0]);

        $rest_filter = DocoRestActiveFilter::advancedFilter($model, $query);

        return $rest_filter;
    }

    public function actionDropFile() {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);

        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;

            $path = 'uploads/'.$filePath;

            if(!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path.'/'.$model->file;

            if($files->saveAs($nameFile)) {
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

}
