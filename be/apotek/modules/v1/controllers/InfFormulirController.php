<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-05 16:43:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 18:18:40
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoFormulirStokOpnameView;
use app\modules\v1\models\DetailFormulirStokOpnameView;
use app\modules\v1\models\DetailStokOpnameView;
use app\modules\v1\models\InfoStokOpnameView;
use app\modules\v1\models\InfoStokOpnameDetailView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\StokOpname;
use app\modules\v1\models\StokOpnameDetail;
use app\modules\v1\models\FormulirStokOpname;
use app\modules\v1\models\FormStokOpname;
use app\modules\v1\businessLogic\FormulirStokOpname as BL_FSO;
use SirsCore\businessLogic\StokObatAlkes as BL_SOA;
use SirsCore\features\IntegrasiAkunting;

class InfFormulirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoFormulirStokOpnameView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        $actions['create-stok-opname'] = 'app\modules\v1\actions\InfFormulir\CreateStokOpnameAction';
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new InfoFormulirStokOpnameView;
        $query = InfoFormulirStokOpnameView::find();
        $startDate = date('Y-m-d 00:00:00');
        $endDate = date('Y-m-d 23:59:59');
        if(isset($get['advanced-filter'])){
            $filter = $get['advanced-filter'];
            if(isset($filter['tglformulir'])){
                $tgl = explode('-', $filter['tglformulir']);
                $startDate = $tgl[1].' '.$tgl[0].' '.str_replace(' ', '', $tgl[2]);
                $endDate = str_replace(' ','',$tgl[3]).' '.$tgl[4].' '.$tgl[5];
                $startDate = date('Y-m-d', strtotime($startDate));
                $endDate = date('Y-m-d', strtotime($endDate));
            }
        }
        $query->andWhere(['between', 'tglformulir',$startDate, $endDate]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataFormulir()
    {
        $request = Yii::$app->request;
        $result = $this->getData();
        $result->select(['noformulir']);
        if(!empty($request['term'])){
            $term = strtoupper($request['term']);
            $result->where('noformulir = :formulir',[':formulir'=>$term]);
        }
        return $result->asArray()->all();
    }

    public function actionDataFormulirDetail($id)
    {
        $result = $this->getDataDetail($id)['data'];
        $result->andWhere(['formulirstokopname_id'=>$id]);
        
        return [
            'data' => $result->asArray()->all(),
            'count' => $result->count(),
            'is_formulir' => $this->getDataDetail($id)['is_formulir']
        ];
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

    private function getData($ruangan_id = null)
    {
        $data = InfoFormulirStokOpnameView::find();

        if ($ruangan_id){
            $data->andWhere(['ruangan_id' => $ruangan_id]);
        }

        return $data;
    }

    private function getDataDetail($id = null)
    {
        $stok_opname = null;

        if($id != null) {
            $stok_opname = StokOpname::find()
                ->where(['formulirstokopname_id' => $id])
                ->asArray()
                ->one();
        }

        // is_formulir digunakan sebagai flagging,
        // (true) jika data so belum diproses, pengambilan data dari DetailFormulirStokOpnameView.
        // (false) jika data so sudah diproses, pengambilan data dari DetailStokOpnameView.

        if($stok_opname != null) {
            $data = DetailStokOpnameView::find();
            $is_formulir = false;
        } else {
            $data = DetailFormulirStokOpnameView::find();
            $is_formulir = true;
        }

        $data->orderBy(['rakobat_id' => SORT_ASC, 'obatalkes_nama'=>SORT_ASC]);

        return [
            'data' => $data,
            'is_formulir' => $is_formulir
        ];
    }

    public function actionGetListData()
    {
        try {
            $data_kondisiobat = Lookup::find()->andWhere(['lookup_type' => DocoConstants::VAR_LU_KB])->asArray()->all();
            $data_jenisstokopname = Lookup::find()->andWhere(['lookup_type' => DocoConstants::VAR_LU_JSO])->asArray()->all();

            return [
                'data_kondisiobat' => $data_kondisiobat,
                'data_jenisstokopname' => $data_jenisstokopname,
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionGetDataFormulir()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');

            $result = InfoFormulirStokOpnameView::find();
            if ($id){
                $result->andWhere(['formulirstokopname_id' => $id]);
            }
            $data = $result->asArray()->one();

            return ['data' => $data];
        } catch (Exception $e) {
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
            $data->andWhere(['formulirstokopname_id' => $id]);
            $data_formulir = $data->asArray()->one();

            $print = new DocoPrint;
            $print->attributes = [
                '#periode_stok#' => isset($data_formulir['tglformulir']) ? date('d-M-Y H:i:s', strtotime($data_formulir['tglformulir'])) : '',
                '#nomor_formulir#' => @$data_formulir['noformulir'],
                '#ruangan#' => @$data_formulir['ruangan_nama'],
                '#table_formulir#' => $this->renderPartial('index',[
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
}
