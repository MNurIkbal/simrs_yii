<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Pemusnahan Obat Alkes
 * @copyright 10 January 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\InfoPemusnahanObatView;
use app\modules\v1\models\InfoPemusnahanObatDetailView;
use app\modules\v1\models\PemusnahanObat;
use app\modules\v1\models\PemusnahanObatDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\entities\Pemusnahan;
use SirsCore\businessLogic\StokObatAlkes as BLStokObatAlkes;

class InfPemusnahanObatController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemusnahanObatView';

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
        $actions['batal-pemusnahan'] = 'app\modules\v1\actions\InfPemusnahanObat\BatalPemusnahanAction';
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPemusnahanObatView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglpemusnahan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemusnahan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemusnahan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['between', 'tglpemusnahan', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataNoPemusnahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataPemusnahan();
        $result->select(['nopemusnahan','nopemusnahan']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(nopemusnahan)',$term]);
        }
        return $result->asArray()->all();
    }

    public function dataPemusnahan()
    {
        $data = InfoPemusnahanObatView::find();
        return $data;
    }

    public function actionDataPemusnahan()
    {
        $model = new InfoPemusnahanObatDetailView;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataDetail($id)
    {
        $query =  InfoPemusnahanObatView::find(true)->where(['pemusnahanobat_id' => $id]);

        return $query->asArray()->one();
    }

    /**
    * @controller actionPrintPemusnahan
    * @attribute #data_pemusnahan# => print
    * @attribute #no_pemusnahan# => untuk menampilkan nomor pemakaian
    * @attribute #tgl_pemusnahan# => untuk menampilkan Tanggal pemakaian
    * @attribute #instalasi# => untuk menampilkan Ruangan pemakaian
    * @attribute #ruangan# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_menyetujui# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_mengetahui# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_retur# => untuk menampilkan Ruangan pemakaian
    **/

    public function actionPrintPemusnahan()
    {
        $id = Yii::$app->request->get('id');
        $nopemusnahan = Yii::$app->request->get('nopemusnahan');
        $query =  InfoPemusnahanObatView::find()->where(['pemusnahanobat_id' => $id]);
        $header = $query->asArray()->one();
        $model = new InfoPemusnahanObatDetailView;
        $query_pemusnahan = $model::find(true);
        $query_pemusnahan->where(['pemusnahanobat_id' => $id]);
        $query_pemusnahan->orderBy(['obatalkes_nama' => SORT_ASC, 'tglkadaluarsa' => SORT_ASC]);
        $data_pemusnahan = $query_pemusnahan->asArray()->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#data_pemusnahan#' => $this->renderPartial('index', [
                'data_pemusnahan' => $data_pemusnahan,
                'header' => $header
            ]),
            '#no_pemusnahan#' => !empty($header['nopemusnahan']) ? $header['nopemusnahan'] : "-",
            '#tgl_pemusnahan#' => !empty($header['tglpemusnahan']) ? date('d M Y H:i:s',strtotime($header['tglpemusnahan'])) : "-",
            '#pegawai_pelaksana#' => !empty($header['nama_pegawai']) ? $header['nama_pegawai'] : "-",
            '#pegawai_mengetahui#' => !empty($header['pegawai_mengetahui']) ? $header['pegawai_mengetahui'] : "-",
            '#status#' => $header['is_verifikasi'] ? "Sudah Verifikasi" : "Belum Verifikasi"
        ];
        $print->Output();
    }

    public function actionDeletePemusnahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $id = $post['id'];
            (new PemusnahanObat)->delete($id);
            (new PemusnahanObatDetail)->delete(['pemusnahanobat_id' => $id]);
            return [
                'message' => 'Data berhasil di hapus'
            ];
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

    public function actionVerifikasiPemusnahan($pemusnahanobat_id) {
        try {
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $model = PemusnahanObat::findOne($pemusnahanobat_id);
            $model->is_verifikasi = true;

            if($model->save()){
                $dataPemusnahan = PemusnahanObatDetail::find()->where(['pemusnahanobat_id' => $pemusnahanobat_id])->asArray()->all();
                Pemusnahan::PotongStokObat($dataPemusnahan);
                $transaction->commit();

                return [
                    'status' => 200,
                    'message' => 'Berhasil',
                    'text' => 'Status verifikasi berhasil di update'
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
}
