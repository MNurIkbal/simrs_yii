<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoPemesananObatAlkesView;
use app\modules\v1\models\DetailPemesananObatAlkes;
use app\modules\v1\models\InfoDistribusiObatAlkesView;

use app\modules\v1\models\PesanObatAlkes;
use app\modules\v1\models\PesanObatDetail;
use app\modules\v1\models\KonfigFarmasi;


class InfPemesananObatAlkesController extends DocoActiveController
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
        $actions['get-data'] = [
            'class'=>'app\modules\v1\actions\GetDataDistribusiAction',
            'withUnverified' => $this->checkVerified()
        ];
        return $actions;
    }

    public function actionBatalPemesanan() {
        return Yii::$app->docoPlugin->execute('batal_pemesanan_obat');
    }

    public function actionIndex()
    {
        // $model = new InfoPemesananObatAlkesView;
        $model = new InfoDistribusiObatAlkesView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpemesanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query->andWhere(['between', 'tglpemesanan', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function checkVerified()
    {
        try{
            $model = KonfigFarmasi::findOne(1);
            return $model->is_verifpemesanan;
        }catch(\Exception $e){
            return false;
        }
    }

    public function actionGetDataObat()
    {
        try
        {
            $model = new DetailPemesananObatAlkes;
            $query = $model::find(true);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }catch(\Exception $e)
        {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }catch (\yii\db\Exception $e)
        {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }
    
    public function actionDetailPemesanan($id)
    {

        try {
            $data = \Yii::$app->db->createCommand("
            select tglpemesanan,ruangan_tujuan,instalasi_tujuan,nopemesanan,tglmintadikirim, keterangan_pesan, ruangan_pemesan
            from detailpemesananobatalkes_v
            where pesanobatalkes_id = {$id}")->queryOne();
            return $data;
        } catch (\Exception $e) {
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

    public function actionGetPemesanan($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $pemesanan = InfoDistribusiObatAlkesView::find()
                ->where(["pesanobatalkes_id" => $id])->asArray()->one();

            $pemesanan['pesanobatalkes_id'] = DocoHelpers::encrypt($pemesanan['pesanobatalkes_id']);
            $pemesanan['ruangan_id'] = DocoHelpers::encrypt($pemesanan['ruangan_id']);
            $pemesanan['instalasi_id'] = DocoHelpers::encrypt($pemesanan['instalasi_id']);
            $pemesanan['ruanganpemesan_id'] = DocoHelpers::encrypt($pemesanan['ruanganpemesan_id']);
            $pemesanan['ruangan_pemesan_id'] = DocoHelpers::encrypt($pemesanan['ruangan_pemesan_id']);
            $pemesanan['instalasi_pemesan_id'] = DocoHelpers::encrypt($pemesanan['instalasi_pemesan_id']);
            $pemesanan['statuspesan'] = DocoHelpers::encrypt($pemesanan['statuspesan']);
            $pemesanan['status_verifikasi'] = DocoHelpers::encrypt($pemesanan['status_verifikasi']);
            // $pemesanan['pegawaipemesan_id'] = DocoHelpers::encrypt($pemesanan['pegawaipemesan_id']);
            $pemesanan['status_id'] = DocoHelpers::encrypt($pemesanan['status_id']);

            return $pemesanan;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDataPemesanan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select nopemesanan, ruangan_id from infopemesananobatalkes_v where nopemesanan LIKE '%{$term}%'
            and tglpemesanan BETWEEN '{$start}' AND'{$end}'
            group by nopemesanan, ruangan_id
            order by nopemesanan asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    /**
        * @controller actionCetakDetailPemesananObat
        * @attribute #tanggal_pemesanan# => Tanggal pemesanan
        * @attribute #tanggal_kirim# => Tanggal di kirim
        * @attribute #status_distribusi# => Tanggal di kirim
        * @attribute #no_pemesanan# => nomor pemesanan
        * @attribute #no_mutasi# => nomor pemesanan
        * @attribute #instalasi_ruangan_tujuan# => nomor pemesanan
        * @attribute #instalasi_ruangan_asal# => nomor pemesanan
        * @attribute #tabel_detail# => Untuk Menampilkan tabel detail pemesanan
        **/

    public function actionCetakDetailPemesananObat()
    {
        return Yii::$app->docoPlugin->execute('cetak_detail_pemesanan_obat');
    }
}
