<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-06 15:43:32
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 14:32:37
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use app\modules\v1\models\InfoPenjualanResep;
use app\modules\v1\models\InfoPenjualanResepDetailView;
use app\modules\v1\models\InformasiResepturView;

use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\Pendaftaran;

use Doco\components\DocoPrint;
use app\components\ApotekComponent;
use app\components\CopyResepComponent;

class InfPenjualanResepRumahsakitController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemesananObatAlkesView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["delete"] = ["POST", "GET"];
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
        $query->select(['tglpenjualan','totalhargajual','noresep','nama_pasien','no_pendaftaran','carabayar_nama','penjamin_nama','totalhargajual','penjualanresep_id','jenispenjualan','no_antrian','status_pembayaran']);
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

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
        $query->andWhere(['=','jenispenjualan', DocoConstants::PENJUALAN_RESEP_RS]);
        /**
         * End Special Condition date range
        **/
        
        // return [$start, $end];

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
        $result->andWhere(['=','jenispenjualan',DocoConstants::PENJUALAN_RESEP_RS]);
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
        $query->select(['obatalkes_nama','hargasatuan_oa','ppn_persen','qty_oa','obatalkes_id','ruangan_id','signa_oa','rke','racikan_id']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();        
        $result = $this->dataResep();       
        $result->select(['pendaftaran_id','no_pendaftaran']);
        if(!empty($post['term'])){          
            $term = $post['term'];                     
            $result->where(['ILIKE','LOWER(no_pendaftaran)',$term]);
        }                
        $result->andWhere(['=','ruangan_id', $post['ruangan_id']]);
        $result->groupBy(['pendaftaran_id','no_pendaftaran']);
        return $result->asArray()->all();
    }
    public function dataPendaftaran(){
        $data = Pendaftaran::find();
        return $data;
    }
    public function actionDataDetail($id)
    {
        try {
            
            $query =  InfoPenjualanResepDetailView::find()->where(['penjualanresep_id' => $id]);
            $query->select(['carabayar_nama','penjamin_nama','pendaftaran_id','noresep','no_pendaftaran','nama_pasien','nama_pegawai','ruangan_id', 'carabayar_id','pasien_id','penjamin_id','pegawai_id','iter','instalasi_nama','ruangan_nama','pegawai_reseptur']);
            return $query->asArray()->one();
        } catch (\Exception $e) {
            return [];
        } catch (\RequestException $e) {
            return [];
        }
    }

    public function actionDataDetailUnit($id)
    {
        try {
            
            $query =  InformasiResepturView::find()->where(['penjualanresep_id' => $id]);
            $query->select(['carabayar_nama','penjamin_nama','pendaftaran_id','noresep','no_pendaftaran','nama_pasien','nama_pegawai','instalasi_reseptur','ruangan_reseptur','ruangan_id', 'carabayar_id','pasien_id','penjamin_id','pegawai_id','iter','noresep_penjualan','iter_penjualan']);
            return $query->asArray()->one();
        } catch (\Exception $e) {
            return [];
        } catch (\RequestException $e) {
            return [];
        }
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
     * @Date:   2018-03-12
     * @Add by:   Rizqi Fitrianto
     * @todo: action for copy resep, params needed
     * penjualanresep_id, noresep, nopendaftaran
     */
    public function actionCopyResep()
    {
        try {
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
    * @controller actionPrintDetail 
    * @attribute #dataTable# => Menampilkan data table
    * @attribute #noresep# => Menampilkan data noresep
    * @attribute #no_pendaftaran# => Menampilkan data No Pendaftaran
    * @attribute #nama_pasien# => Menampilkan data Nama Pasien
    * @attribute #dokter_resep# => Menampilkan data Nama Pegawai Dokter Resep
    * @attribute #instalasi_nama# => Menampilkan data Nama Instalasi
    * @attribute #ruangan_nama# => Menampilkan data Nama Ruangan
    * @attribute #caraBayar# => Menampilkan data Cara Bayar
    * @attribute #penjamin# => Menampilkan data Nama Penjamin
    * @attribute #iter# => Menampilkan data Iter
    **/
    public function actionPrintDetail()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $query =  InfoPenjualanResepDetailView::find()->where(['penjualanresep_id' => $id]);
            $query->select(['carabayar_nama','penjamin_nama','pendaftaran_id','noresep','no_pendaftaran','nama_pasien','nama_pegawai','instalasi_nama','ruangan_nama','carabayar_id','pasien_id','penjamin_id','pegawai_id','iter','obatalkes_nama','hargasatuan_oa','ppn_persen','qty_oa','obatalkes_id','pegawai_reseptur','racikan_id','rke','signa_oa']);
            $query->orderBy(['additional_data'=>SORT_ASC]);
            $data = $query->asArray()->all();
            $getHeader = $this->actionDataDetail($id);

            
            $data_detail = [];
            $data_obat = [];
            $totalharga = 0;
            foreach ($data as $key => $value) {
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
                $data_obat[] = $newData;
            }
            $totalharga = DocoHelpers::formatNumber($totalharga);
            $result = ['detail'=>$data_detail, 'data_obat'=>$data_obat, 'totalharga'=>$totalharga, 'getHeader'=>$getHeader];
            $print = new DocoPrint();
            $print->attributes = [
                '#dataTable#' => $this->renderPartial('index',[
                    'data_obat'=>$data_obat,
                    'totalharga'=>$totalharga,
                    // 'getHeader'=>$getHeader
                    ]),
                '#noresep#' => !empty($getHeader['noresep']) ? $getHeader['noresep'] : '-',
                '#no_pendaftaran#' => !empty($getHeader['no_pendaftaran']) ? $getHeader['no_pendaftaran'] : '-',
                '#nama_pasien#' => !empty($getHeader['nama_pasien']) ? $getHeader['nama_pasien'] : '-',
                '#dokter_resep#' => !empty($getHeader['pegawai_reseptur']) ? $getHeader['pegawai_reseptur'] : '-',
                '#instalasi_nama#'=>!empty($getHeader['instalasi_nama']) ? $getHeader['instalasi_nama'] : '-',
                '#ruangan_nama#'=>!empty($getHeader['ruangan_nama']) ? $getHeader['ruangan_nama'] : '-',
                '#caraBayar#'=>!empty($getHeader['carabayar_nama']) ? $getHeader['carabayar_nama'] : '-',
                '#penjamin#'=>!empty($getHeader['penjamin_nama']) ? $getHeader['penjamin_nama'] : '-',
                '#iter#'=>!empty($getHeader['iter']) ? $getHeader['iter'] : 0,
            ];
            $print->Output();

        } catch (\Yii\db\Exception $e) {
             throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }

    }

}