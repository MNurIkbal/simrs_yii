<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-05 16:43:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 18:18:40
 */

namespace app\modules\v1\components\traits;

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
use Doco\models\LookupTransaksi;
use app\modules\v1\models\StokOpname;
use app\modules\v1\models\StokOpnameDetail;
use app\modules\v1\models\FormulirStokOpname;
use app\modules\v1\models\FormStokOpname;
use app\modules\v1\businessLogic\FormulirStokOpname as BL_FSO;
use SirsCore\businessLogic\StokObatAlkes as BL_SOA;
use SirsCore\features\IntegrasiAkunting;
use yii\helpers\ArrayHelper;

// class InfFormulirController extends DocoActiveController
trait InfFormulirTrait
{
    public function actionGetDataStok()
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
        $raw_data = $result->asArray()->all();
        $detail = [];

        // set file to be used, configurable in lookuptransaksi_m; kode_transaksi = cetak_form_so
        $lookup_trx = LookupTransaksi::find()
            ->select(['kode_transaksi', 'kode_id'])
            ->where(['kode_transaksi' => 'cetak_form_so'])->one();

        if (isset($lookup_trx)) {
            $get_file = Lookup::find()
                ->select(['lookup_id', 'lookup_name', 'lookup_value'])
                ->where(['lookup_id' => $lookup_trx['kode_id']])->one();
        }

        if (isset($get_file['lookup_value']) && $get_file['lookup_value'] != 'default') {
            ArrayHelper::multisort($raw_data, ['obatalkes_nama', 'tglkadaluarsa'], [SORT_ASC, SORT_ASC]);
            $detail = $raw_data;
        } else {
            $detail = $raw_data;
        }
        
        return [
            'data' => $detail,
            'count' => $result->count(),
            'is_formulir' => $this->getDataDetail($id)['is_formulir']
        ];
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

        $data->orderBy([
            'rakobat_nama' => SORT_ASC,
            'laci' => SORT_ASC,
            'obatalkes_nama' => SORT_ASC,
            'tglkadaluarsa' => SORT_ASC
        ]);

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
}
