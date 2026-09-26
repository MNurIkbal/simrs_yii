<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use app\modules\v1\models\FormStokOpname;
use app\modules\v1\models\FormulirStokOpname;
use app\modules\v1\models\InfoStokOpnameView;
use app\modules\v1\models\InfoStokOpnameDetailView;
use app\modules\v1\models\DetailFormulirStokOpnameView;
use app\modules\v1\models\InfoFormulirStokOpnameView;
use app\modules\v1\models\StokOpnameDetail;
use app\modules\v1\models\StokOpname;
use app\modules\v1\entities\StokOpnameDetail as EntSoDetail;
use app\modules\v1\models\InfoFormulirInfoStokOpnameView;
use SirsCore\businessLogic\StokObatAlkes as BL_SOA;
use app\modules\v1\businessLogic\StokOpnameObat as LogicSO;
use app\modules\v1\components\traits\InfFormulirTrait;
use app\modules\v1\models\KonfigFarmasi;

class InfStokOpnameController extends DocoActiveController
{
    use InfFormulirTrait;

    public $modelClass = 'app\modules\v1\models\InfoStokOpnameView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        $verbs["detail"] = ["GET"];
        $verbs["save"] = ["POST"];
        $verbs["get-info-detail"] = ["GET"];
        $verbs["export-pdf"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        $actions['create-stok-opname'] = 'app\modules\v1\actions\InfFormulir\CreateStokOpnameAction';
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoStokOpnameView;
        $query = $model::find(true);
        //$query = InfoStokOpnameView::find();
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglstokopname'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglstokopname']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglstokopname']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['status_verifikasi'])) {
                $query->andWhere(['is_verifikasi' => $_GET['advanced-filter']['status_verifikasi']]);
            }
        }
        $query->andWhere(['between', 'tglstokopname', $start, $end]);
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSave() {
        $request = Yii::$app->request;
        $post = $request->post();
        $formulirstokopname_id = $post['formulirstokopname_id'];
        $infoStok = InfoStokOpnameView::find()->where([
            'formulirstokopname_id' => $formulirstokopname_id
        ])->asArray()->one();

        if (!empty($infoStok) && !empty($infoStok['stokopname_id'])) {
            $result = (new LogicSO)->update($formulirstokopname_id);
        } else {
            $result = (new LogicSO)->execute($formulirstokopname_id, $infoStok);
        }
        return $result;
    }

    public function actionDetail($parent_id)
    {
        $request = Yii::$app->request;

        $limit = $request->get('length', 0);
        $offset = $request->get('start', 0);

        $model = new InfoStokOpnameDetailView;
        $query = $model::find();
        $query->andWhere(['stokopname_id'=>$parent_id])
              ->orderBy([
                'rakobat_nama' => SORT_ASC, 
                'laci' => SORT_ASC, 
                'obatalkes_nama' => SORT_ASC
              ]);

        if($limit > 0) {
            $query->limit($limit);
        }

        if($offset > 0) {
            $query->offset($offset);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $pagination = $request->get('pagination', 1);

        $count = $query->count();
        $pageCount = ceil($count / $limit);

        if($pagination == 1) {
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } else {
            return [
                'data' => $query->asArray()->all(),
                "_meta" => [
                    "totalCount" =>  $count,
                    "pageCount" =>  $pageCount,
                    "perPage" => $limit
                ]
            ];
        }
        
    }
    
    public function actionGetHeaderDetail($id, $type = 0)
    {
        try {
            $model = new InfoStokOpnameView;
            $result = [];
            $result = $model::find()->where(['stokopname_id'=>$id])->asArray()->one();

            $so_detail = (new EntSoDetail)->loadViewById($id);
            if(!$result['is_verifikasi']) {
                $weighted_avg = $so_detail->calcWeightedAvgTotal($type);
                $total_wa_fisik = $weighted_avg['total_wa_fisik'];
                $total_wa_sistem = $weighted_avg['total_wa_sistem'];
            } else {
                $total_wa_fisik = $result['total_weighted_avg_fisik'];
                $total_wa_sistem = $result['total_weighted_avg_sistem'];
            }

            $selisih_wa = $total_wa_fisik - $total_wa_sistem;

            //total weighted avg; need improve jika tabel sudah ditambah kolom untuk simpan weighted avg
            $arr['total_weighted_avg_fisik'] = $total_wa_fisik;
            $arr['total_weighted_avg_sistem'] = $total_wa_sistem;
            $arr['selisih_weighted_avg'] = $selisih_wa;
            return array_merge($result, $arr);
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }

    public function actionDataNostok(){
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataStok();
        $result->select(['stokopname_id','nostokopname']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['ILIKE','nostokopname',$term]);
        }
        return $result->asArray()->all();
    }

    public function dataStok(){
        $data = InfoStokOpnameView::find();
        return $data;
    }

    public function actionGetInfoDetail()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');

            $result = $this->getData();
            $result->andWhere(['formulirstokopname_id'=>$id]);
            $data = $result->asArray()->one();

            return [
                'data' => $data,
                'count' => $result->count()
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #periode_stok# => Untuk Menampilkan Data Periode stok
    * @attribute #nomor_formulir# => Untuk Menampilkan Nomor formulir stok opname
    * @attribute #nomor_so# => Untuk Menampilkan Nomor stok opname
    * @attribute #totalharga_sistem# => Untuk menampilkan total sistem
    * @attribute #totalharga_fisik# => Untuk menampilkan total sistem
    * @attribute #totalstok_sistem# => Untuk menampilkan total fisik
    * @attribute #totalstok_fisik# => Untuk menampilkan total fisik
    * @attribute #jenis_stok# => Untuk menampilkan jenis so
    * @attribute #selisih_harga# => Untuk menampilkan selisih harga
    * @attribute #selisih_stok# => Untuk menampilkan selisih stok
    * @attribute #ruangan# => Untuk menapilkan ruangan
    * @attribute #table_formulir# => Untuk menampilkan tabel formulir
    * @attribute #tgl_stokopname# => Untuk menampilkan tanggal stok opname
    * @attribute #total_wa_fisik# => Untuk menampilkan total wa fisik
    * @attribute #total_wa_sistem# => Untuk menampilkan total wa sistem
    * @attribute #selisih_wa# => Untuk menampilkan selisih stok opname
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        // type 0 = weighted_avg
        // type 1 = base_price
        $configBasePriceVal = $request->get('type', null);
        $result = [];

        $model = new InfoStokOpnameView;
        $find = $model::find()->where(['stokopname_id'=>$id])->asArray()->one();
        $result = $find;

        $getDetail = InfoStokOpnameDetailView::find()
            ->where(['stokopname_id'=>$id])
            ->orderBy([
                'rakobat_nama' => SORT_ASC, 
                'laci' => SORT_ASC, 
                'obatalkes_nama' => SORT_ASC,
                'tglkadaluarsa' => SORT_ASC
            ])
            ->asArray()
            ->all();

        $totalstok_fisik = $totalstok_sistem = 0;
        $tglstok_awal = $tglstok_akhir = null;

        $arr = [];
        foreach ($getDetail as $idx => $value) {
            $totalstok_fisik += $value['volume_fisik'];
            $totalstok_sistem += $value['volume_sistem'];

            if(empty($tglperiodestok_awal)) {
                $tglstok_awal = date('d M Y', strtotime($value['tglperiodestok_awal']));
            }

            if(empty($tglstok_akhir)) {
                $tglstok_akhir = date('d M Y', strtotime($value['tglperiodestok_akhir']));
            }
            
            $getDetail[$idx]['volume_sistem'] = !empty($value['volume_sistem']) ? $value['volume_sistem'] : '0';
            $getDetail[$idx]['volume_fisik'] = !empty($value['volume_fisik']) ? $value['volume_fisik'] : '0';
            $getDetail[$idx]['stok_sistem'] = !empty($value['stok_sistem']) ? $value['stok_sistem'] : '0';
            $getDetail[$idx]['stok_selisih'] = !empty($value['stok_selisih']) ? $value['stok_selisih'] : '0';
            $getDetail[$idx]['laci'] = !empty($value['laci']) ? $value['laci'] : 'Tanpa Rak';
            $getDetail[$idx]['kondisibarang_nama'] = !empty($value['kondisibarang_nama']) ? $value['kondisibarang_nama'] : '-';
            $getDetail[$idx]['selisih'] = $value['volume_fisik'] - $value['volume_sistem'];
        }
        
        $totalstok_selisih = $totalstok_fisik - $totalstok_sistem;

        $so_detail = (new EntSoDetail)->loadViewById($id);
        if(!$find['is_verifikasi']) {
            $weighted_avg = $so_detail->calcWeightedAvgTotal($configBasePriceVal);
            $total_weighted_avg_fisik = $weighted_avg['total_wa_fisik'];
            $total_weighted_avg_sistem = $weighted_avg['total_wa_sistem'];
        } else {
            $total_weighted_avg_fisik = $result['total_weighted_avg_fisik'];
            $total_weighted_avg_sistem = $result['total_weighted_avg_sistem'];
        }

        $selisih_wa = $total_weighted_avg_fisik - $total_weighted_avg_sistem;

        $total_sistem = isset($find['totalharga_sistem']) ? $find['totalharga_sistem'] : 0;
        $total_fisik = isset($find['totalharga_fisik']) ? $find['totalharga_fisik'] : 0;

        $print = new DocoPrint;
        $print->attributes = [
            '#periode_stok#' => isset($find['tglformulir']) ? date('d-M-Y', strtotime($find['tglformulir'])) : '-',
            '#tgl_stokopname#' => isset($find['tglstokopname']) ? date('d-M-Y', strtotime($find['tglstokopname'])) : '-',
            '#nomor_formulir#' => @$find['noformulir'],
            '#nomor_so#' => @$find['nostokopname'],
            '#ruangan#' => @$find['ruangan_nama'],
            '#table_formulir#' => $this->renderPartial('index',[
                'data' => $getDetail,
                'type' => $configBasePriceVal
            ]),
            '#totalharga_sistem#' => DocoHelpers::rupiahDisplay($total_sistem),
            '#totalharga_fisik#' => DocoHelpers::rupiahDisplay($total_fisik),
            '#totalstok_sistem#' => number_format($totalstok_sistem,2,',','.'),
            '#totalstok_fisik#' => number_format($totalstok_fisik,2,',','.'),
            '#jenis_stok#' => isset($find['jenisstokopname']) ? ($find['jenisstokopname'] == 'P') ? 'Penyesuaian' : 'Stok Awal' : '',
            '#selisih_harga#' => DocoHelpers::rupiahDisplay(abs($total_sistem - $total_fisik)),
            '#selisih_stok#' => DocoHelpers::formatNumber($totalstok_selisih),
            '#total_wa_fisik#' => 'Rp. ' . number_format($total_weighted_avg_fisik,2,',','.') ,
            '#total_wa_sistem#' => 'Rp. ' . number_format($total_weighted_avg_sistem,2,',','.') ,
            '#selisih_wa#' => 'Rp. ' . number_format($selisih_wa,2,',','.') 
        ];

        $print->Output();
    }

    public function actionVerifikasi()
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $request = Yii::$app->request;
            $id = $request->get('id',null);

            $stokOpname = StokOpname::find()->where(['stokopname_id'=>$id])->one();
            if(is_null($stokOpname)){
                throw new \Exception("Stok Opname ID Tidak Ditemukan", 1);
            }

            if($stokOpname->is_verifikasi) {
                throw new \Exception("Stok opname sudah diverifikasi", 1);
            }

            $stokOpnameDetail = (new EntSoDetail)->loadViewById($id);
            $weighted_avg = $stokOpnameDetail->calcWeightedAvgTotal($stokOpname->is_verifikasi);
            $tglImplementasiSesuaiVerif = $this->tglImplementasiSesuaiVerif($id);
            $tgl_implementasi = $tglImplementasiSesuaiVerif['config'] ? date('Y-m-d H:i:s') : $tglImplementasiSesuaiVerif['tgl_implementasi'];

            $stokOpname->is_verifikasi = TRUE;
            $stokOpname->tglverifikasi = date('Y-m-d H:i:s');
            $stokOpname->tgl_implementasi = $tgl_implementasi;
            $stokOpname->pegawaiverifikasi_id = Yii::$app->jwt->user->pegawai_id;
            $stokOpname->total_weighted_avg_fisik = $weighted_avg['total_wa_fisik'];
            $stokOpname->total_weighted_avg_sistem = $weighted_avg['total_wa_sistem'];
            $stokOpname->save();
            $stokOpnameDetail->updateWeightedAverage($tglImplementasiSesuaiVerif, $stokOpname->ruangan_id);

            $hitungStok = BL_SOA::updateStokOpname($id, $stokOpname->ruangan_id, false, $tglImplementasiSesuaiVerif['config'] ? null : $tglImplementasiSesuaiVerif['tgl_implementasi']);
            if(is_array($hitungStok)) {
                $transaction->rollBack();
                throw new \Exception($hitungStok['message'], 1);
            }

            $transaction->commit();
            return $this->responseJson(200, 'Verifikasi stok opname berhasil');
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'text' => $e->getMessage()]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'text' => $e->getMessage()]);
        }
    }

    public function actionInfStokFormulirOpname()
    {
        $model = new InfoFormulirInfoStokOpnameView;
        $query = $model::find(true);
        //$query = InfoStokOpnameView::find();
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start_formulir = date('Y-m-d 00:00:00');
        $end_formulir = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglformulir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglformulir']);
                if(count($explode) == 2) {
                    $start_formulir = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end_formulir = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglformulir']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['status_verifikasi'])) {
                if($_GET['advanced-filter']['status_verifikasi'] == '2'){
                    $query->andWhere(['stokopname_id' => null]);
                }
                else if($_GET['advanced-filter']['status_verifikasi'] == '0'){
                    $query->andWhere(['or',
                       ['is_verifikasi' => false],
                       ['and',['not', ['stokopname_id' => null]],['is_verifikasi' => null]]
                   ]);
                }
                else if($_GET['advanced-filter']['status_verifikasi'] == '1'){
                    $query->andWhere(['is_verifikasi' => true]);
                }
            }
        }
       
        $query->andWhere(['between', 'tglformulir', $start_formulir, $end_formulir]);
        
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionPdfFormulirStokOpname
    * @attribute #periode_stok# => Untuk Menampilkan Data Periode stok
    * @attribute  #nomor_formulir# => Untuk Menampilkan Nomor formulir stok opname
    * @attribute #ruangan# => Untuk menapilkan ruangan
    * @attribute #table_formulir# => Untuk menampilkan tabel formulir
    **/
    public function actionPdfFormulirStokOpname()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $data = DetailFormulirStokOpnameView::find();
            $data->andWhere(['formulirstokopname_id' => $id]);
            $data->orderBy(['obatalkes_nama'=>SORT_ASC,'tglkadaluarsa'=>SORT_ASC]);
            $data_detail = $data->asArray()->all();

            $data = InfoFormulirStokOpnameView::find();
            $data->andWhere(['formulirstokopname_id' => $id])
            ;
            $data_formulir = $data->asArray()->one();

            $print = new DocoPrint;
            $print->attributes = [
                '#periode_stok#' => isset($data_formulir['tglformulir']) ? date('d-M-Y H:i:s', strtotime($data_formulir['tglformulir'])) : '',
                '#nomor_formulir#' => @$data_formulir['noformulir'],
                '#ruangan#' => @$data_formulir['ruangan_nama'],
                '#table_formulir#' => $this->renderPartial('formulir',[
                    'data' => @$data_detail,
                ]),
            ];
            $print->Output();
        } catch (\RequestException $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * Fungsi delete formulir stok opname
     * @param integer $id = formulirstokopname_id
     * @return array, message/model validate errors
     *
     */
    public function actionDeleteFormulir($id)
    {
        try {
            $connection  = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            $modelFormulir = FormulirStokOpname::findOne($id);
            $modelFormulirDetail = FormStokOpname::findAll(['formulirstokopname_id' => $modelFormulir->formulirstokopname_id]);

            if ($modelFormulir && $modelFormulirDetail) {
                // FormulirStokOpname::deleteAll(['formulirstokopname_id' => $id]);
                // FormStokOpname::deleteAll(['formulirstokopname_id' => $id]);
                $modelFormulir->is_deleted   = true;
                $modelFormulir->is_active    = false;
                $modelFormulir->deleted_date = date('Y-m-d H:i:s');
                $modelFormulir->deleted_by   = Yii::$app->user->identity->id;
                $modelFormulir->save(false);

                FormStokOpname::updateAll([
                    'is_deleted'   => true,
                    'is_active'    => false,
                    'deleted_date' => date('Y-m-d H:i:s'),
                    'deleted_by'   => Yii::$app->user->identity->id,
                ], 'formulirstokopname_id = '.$modelFormulir->formulirstokopname_id);

                $transaction->commit();
                $res = [
                    'text' => 'Data Berhasil Dihapus',
                    'title' => 'Proses Berhasil !'
                ];
            } else {
                $transaction->rollBack();
                $res = [
                    'text' => 'Terjadi Kesalahan',
                    'title' => 'Proses Gagal !'
                ];
            }

            return $res;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'text' => 'Terjadi Kesalahan',
                'title' => 'Proses Gagal !'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'text' => 'Terjadi Kesalahan',
                'title' => 'Proses Gagal !'
            ];
        }
    }

    /**
    * @controller actionPdfTransaksiStokOpname
    * @attribute #periode_stok# => Untuk Menampilkan Data Periode stok
    * @attribute  #nomor_formulir# => Untuk Menampilkan Nomor formulir stok opname
    * @attribute  #nostokopname# => Untuk Menampilkan Nomor formulir stok opname
    * @attribute #ruangan# => Untuk menapilkan ruangan
    * @attribute #table_formulir# => Untuk menampilkan tabel formulir
    * @attribute #total_sistem# => Untuk menampilkan total sistem
    * @attribute #total_fisik# => Untuk menampilkan total fisik
    * @attribute #jenis_stok# => Untuk menampilkan jenis so
    * @attribute #selisih# => Untuk menampilkan selisih
    * @attribute #totalstok_fisik# => Untuk menampilkan total stok fisik
    * @attribute #totalstok_sistem# => Untuk menampilkan total stok sistem
    * @attribute #selisih_stok# => Untuk menampilkan selisih sistem
    **/
    public function actionPdfTransaksiStokOpname()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $data = InfoStokOpnameDetailView::find();
        $data->andWhere(['stokopname_id' => $id]);
        $data->orderBy(['obatalkes_nama'=>SORT_ASC,'tglkadaluarsa'=>SORT_ASC]);
        $data_detail = $data->asArray()->all();
        $totalstok_sistem = $totalstok_fisik = $totalstok_selisih = 0;
        foreach ($data_detail as $val) {
            $totalstok_sistem += $val['volume_sistem'];
            $totalstok_fisik += $val['volume_fisik'];
        }
        $totalstok_selisih = $totalstok_fisik - $totalstok_sistem;
        $data = InfoStokOpnameView::find();
        $data->andWhere(['stokopname_id' => $id]);
        $data_formulir = $data->asArray()->one();
        $total_sistem = isset($data_formulir['totalharga_sistem']) ? $data_formulir['totalharga_sistem'] : 0;
        $total_fisik = isset($data_formulir['totalharga_fisik']) ? $data_formulir['totalharga_fisik'] : 0;
        $print = new DocoPrint;
        $print->attributes = [
            '#periode_stok#' => isset($data_formulir['tglstokopname']) ? date('d-M-Y', strtotime($data_formulir['tglstokopname'])) : '',
            '#tgl_formulir#' => !empty($data_formulir['tglformulir']) ? date('d-M-Y', strtotime($data_formulir['tglformulir'])) : '',
            '#nomor_formulir#' => @$data_formulir['noformulir'],
            '#nostokopname#' => @$data_formulir['nostokopname'],
            '#ruangan#' => @$data_formulir['ruangan_nama'],
            '#table_formulir#' => $this->renderPartial('transaksi',[
                'data' => @$data_detail,
            ]),
            '#total_sistem#' => DocoHelpers::rupiahDisplay($total_sistem),
            '#total_fisik#' => DocoHelpers::rupiahDisplay($total_fisik),
            '#jenis_stok#' => isset($data_formulir['jenisstokopname']) ? ($data_formulir['jenisstokopname'] == 'P') ? 'Penyesuaian' : 'Stok Awal' : '',
            '#selisih#' => DocoHelpers::rupiahDisplay(abs($total_sistem - $total_fisik)),
            '#totalstok_fisik#'=>DocoHelpers::formatNumber($totalstok_fisik),
            '#totalstok_sistem#'=>DocoHelpers::formatNumber($totalstok_sistem),
            '#selisih_stok#'=>DocoHelpers::formatNumber($totalstok_selisih),
        ];
        $print->Output();
    }

    public function tglImplementasiSesuaiVerif($id) {
        $formulirSo = new InfoStokOpnameView;
        $formulirSo = $formulirSo::find()->where(['stokopname_id'=>$id])->asArray()->one();
        if($formulirSo && date('Y-m-d') == date('Y-m-d', strtotime($formulirSo['tglformulir']))){
            $tgl_implementasi = date('Y-m-d H:i:s');
        }else{
            $tgl_implementasi = date('Y-m-d 23:59:59', strtotime($formulirSo['tglformulir']));
        }
        
        $config = KonfigFarmasi::find()->select(['is_tgl_implementasi_sesuai_verif'])->asArray()->one();

        return [
            'tgl_implementasi' => $tgl_implementasi,
            'config' => !empty($config['is_tgl_implementasi_sesuai_verif']) ? $config['is_tgl_implementasi_sesuai_verif'] : false,
        ];
    }
}
