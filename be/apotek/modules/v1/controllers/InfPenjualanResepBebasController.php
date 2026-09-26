<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-05 11:07:56
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-05-18 09:30:35
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;

use app\modules\v1\models\InfoPenjualanResep;
use app\modules\v1\models\InfoPenjualanResepDetailView;

use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\StokObatAlkes;

use app\components\ApotekComponent;
use app\components\CopyResepComponent;

class InfPenjualanResepBebasController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemesananObatAlkesView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPenjualanResep;
        $query = $model::find(true);
        $query->select(['tglpenjualan','noresep','totalhargajual','nama_pembeli','no_pendaftaran','carabayar_nama','penjamin_nama','totalhargajual','penjualanresep_id','jenispenjualan','biayaadministrasi','pembulatanharga','jasadokterresep','totaltarifservice','status_pembayaran']);
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpenjualan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpenjualan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpenjualan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query->andWhere(['between', 'tglpenjualan', $start, $end]);
        $query->andWhere(['=','jenispenjualan', DocoConstants::PENJUALAN_RESEP_BEBAS]);
        $query->orderBy(['noresep'=> SORT_DESC ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionDataResep()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataResep();
        $result->select(['noresep','noresep']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(noresep)',$term]);
        }
        $result->andWhere(['=','jenispenjualan',DocoConstants::PENJUALAN_RESEP_BEBAS]);
        return $result->asArray()->all();
    }
    public function dataResep()
    {
        $data = InfoPenjualanResep::find();
        return $data;
    }
    public function actionDataObat()
    {
        $model = new InfoPenjualanResepDetailView;
        $query = $model::find(true);
        $query->select(['obatalkes_nama','obatalkes_namalain','hargajual_oa','hargasatuan_oa','ppn_persen','qty_oa','obatalkes_id','ruangan_id','signa_oa','rke','racikan_id',"additional_data", "biayaadministrasi", "totaltarifservice", "pembulatanharga"]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['additional_data'=>SORT_ASC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionDataDetail($id)
    {
        $query =  InfoPenjualanResepDetailView::find()->where(['penjualanresep_id' => $id]);
        //new code
        //add several column for view
        //12-03-2018
        $query->select(['pendaftaran_id','noresep','no_pendaftaran','nama_pembeli','nama_pegawai','instalasi_nama','ruangan_nama','carabayar_id','pasien_id','penjamin_id','pegawai_id','iter','carabayar_nama','penjamin_nama','tglpenjualan']);
        return $query->asArray()->one();
    }


    public function actionDeleteResep(){
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $id = $post['id'];
            return CopyResepComponent::deleteResep($id);
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }
     /**
     * @Author: Rizqi Fitrianto
     * @Date:   2018-03-13
     * @Add by:   Rizqi Fitrianto
     * @todo: action for copy resep, params needed
     * penjualanresep_id, noresep, nopendaftaran
     */
    public function actionCopyResep()
    {
        try{
            $request = Yii::$app->request;
             if($request->post()){
                $post = $request->post();
                $dataObat = $post['dataObat'];
                $penjualanresep_id = $post['penjualanresep_id'];
                $noresep = $post['noresep'];
                $pendaftaran_id = $post['pendaftaran_id'];
                $result = CopyResepComponent::copyResep($penjualanresep_id, $noresep, $pendaftaran_id, $dataObat);
                if($result['status'] == 422){
                    return $result;
                }else{
                    return $response['response'] = [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Data berhasil di simpan.'
                       ];
                }
            }

        }catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionPrintResep
    * @attribute #dataTable# => Menampilkan data table
    * @attribute #noresep# => Menampilkan data noresep
    * @attribute #no_pendaftaran# => Menampilkan data No Pendaftaran
    * @attribute #nama_pembeli# => Menampilkan data Nama Pasien
    * @attribute #dokter_resep# => Menampilkan data Nama Pegawai Dokter Resep
    * @attribute #tglpenjualan# => Menampilkan data Tanggal Penjualan
    * @attribute #caraBayar# => Menampilkan data Cara Bayar
    * @attribute #penjamin# => Menampilkan data Nama Penjamin
    * @attribute #iter# => Menampilkan data Iter
    **/

    public function actionPrintResep()
    {
        try{
            $request = Yii::$app->request;
            $id = $request->get('id');
            $noresep = $request->get('noresep');
            $query =  InfoPenjualanResep::find()->where(['penjualanresep_id' => $id]);
            $dataHeader = $query->asArray()->one();

            $model = new InfoPenjualanResepDetailView;
            $query_obat = $model::find(true);
            $query_obat->select(['obatalkes_nama','hargajual_oa','hargasatuan_oa','ppn_persen','qty_oa','obatalkes_id','ruangan_id','rke','racikan_id','signa_oa', "biayaadministrasi", "totaltarifservice", "pembulatanharga"]);
            $query_obat->where(['penjualanresep_id' => $id]);
            $query_obat->orderBy(['obatalkes_nama'=>SORT_DESC]);
            $data_obat = $query_obat->asArray()->all();

            $data_detail = [];
            $res_dataObat = [];
            $totalharga = 0;
            foreach ($data_obat as $key => $value) {
                $jenisRacikan = ($value['racikan_id'] == 1) ? Yii::t('app', 'Racikan') : Yii::t('app', 'Non Racikan');
                $newData['jenis_racikan'] = $jenisRacikan;
                $newData['rke'] = ($value['rke'] == 0 || empty($value['rke']) ) ? '-' : $value['rke'] ;
                $newData['obatalkes_nama'] = $value['obatalkes_nama'];
                $newData['hargajual_oa'] = DocoHelpers::formatNumber($value['hargasatuan_oa']);
                $subtotal = ($value['hargasatuan_oa'])*$value['qty_oa'];
                $newData['signa_oa'] = !empty($value['signa_oa']) ? $value['signa_oa'] : '-';
                $newData['qty'] = $value['qty_oa'];
                $newData['sub_total'] = DocoHelpers::formatNumber($subtotal);
                $totalharga += $subtotal;
                $res_dataObat[] = $newData;
            }
            $biaya_admin = $value["biayaadministrasi"];
            $jasa_racik = $value["totaltarifservice"];
            $pembulatan = $value["pembulatanharga"];
            $total_tagihan = array_sum([$biaya_admin,$jasa_racik,$pembulatan,$totalharga]);

            $totalharga = DocoHelpers::formatNumber($totalharga);
            $print = new DocoPrint();
            $print->attributes = [
                '#dataTable#' => $this->renderPartial('index',
                    [
                        'data_obat'=>$res_dataObat,
                        'totalharga'=>$totalharga,
                        'biaya_admin' => DocoHelpers::formatNumber($biaya_admin),
                        'jasa_racik' => DocoHelpers::formatNumber($jasa_racik),
                        'pembulatan' => DocoHelpers::formatNumber($pembulatan),
                        'total_tagihan' => DocoHelpers::formatNumber($total_tagihan),
                    ]),
                '#noresep#' => !empty($dataHeader['noresep']) ? $dataHeader['noresep'] : '-',
                '#nama_pembeli#' => !empty($dataHeader['nama_pembeli']) ? $dataHeader['nama_pembeli'] : '-',
                '#tglpenjualan#' => !empty($dataHeader['tglpenjualan']) ? date('d M Y', strtotime($dataHeader['tglpenjualan'])) : '-',
                '#dokter_resep#' => !empty($dataHeader['nama_pegawai']) ? $dataHeader['nama_pegawai'] : '-',
                '#caraBayar#'=>!empty($dataHeader['carabayar_nama']) ? $dataHeader['carabayar_nama'] : '-',
                '#penjamin#'=>!empty($dataHeader['penjamin_nama']) ? $dataHeader['penjamin_nama'] : '-',
                '#iter#'=>!empty($dataHeader['iter']) ? $dataHeader['iter'] : 0,
            ];
            $print->Output();
        } catch (\Yii\db\Exception $e) {
             throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actiondoIntegrate($noresep)
    {
        return $this->integrate($noresep);
    }
}


