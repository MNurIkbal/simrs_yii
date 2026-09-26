<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
// use app\modules\v1\models\InfoPasienKepenunjanganView;
use app\modules\v1\models\InfoTagihanPenunjangView;
use app\modules\v1\models\InfoTagihanPenunjangDetailView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\NoCountDataProvider;

class InfPasienKepenunjanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienKepenunjanganView';

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
        $model = new InfoTagihanPenunjangView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
            }
        }
        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        $query->andWhere(['>', 'jumlah_tagihan', '0']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new NoCountDataProvider([
            'query' => $query,
        ]);
    }

    //detail mutasi obat alkes
    public function actionDetail($pendaftaran_id)
    {
        try {
            $model = InfoTagihanPenunjangView::find()
                ->andWhere(['pasienmasukpenunjang_id'=>$pendaftaran_id])
                ->asArray()
                ->one();

            $detail = new InfoTagihanPenunjangDetailView;
            $detail = $detail::find()
                ->andWhere(['pasienmasukpenunjang_id'=>$pendaftaran_id])
                ->asArray()->all();
            
            return [
                'header'=>$model,
                'detail'=>$detail,
            ];
        
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\ErrorException $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'errorInfo'=>$e->getName()
            ];
        } catch(\yii\base\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'errorInfo'=>$e->getName()
            ];
        }

    }

    /**
    * @controller actionPrintDetail 
    * @attribute #data_tagihan# => print 
    * @attribute #tgl_pendaftaran# => tgl_pendaftaran
    * @attribute #tglmasukpenunjang# => tglmasukpenunjang
    * @attribute #no_pendaftaran# => no_pendaftaran
    * @attribute #jeniskasuspenyakit_nama# => jeniskasuspenyakit_nama
    * @attribute #kelaspelayanan_nama# => kelaspelayanan_nama
    * @attribute #dokter# => nama_pegawai
    * @attribute #ruang_pendaftaran# => ruang_pendaftaran
    * @attribute #instalasi_nama# => instalasi_nama
    * @attribute #ruangan_nama# => ruangan_nama
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #carabayar_nama# => carabayar_nama
    * @attribute #penjamin_nama# => penjamin_nama
    * @attribute #jumlah_tagihan# => jumlah_tagihan
    * @attribute #status_lunas# => status_lunas
    **/
    public function actionPrintDetail()
    {
        $pendaftaran_id = $_GET['id'];
        $ruangan = $_GET['ruangan'];
        
        $model = InfoTagihanPenunjangView::find()
            ->andWhere(['pendaftaran_id'=>$pendaftaran_id])
            ->andWhere(['ruangan_id'=>$ruangan])
            ->asArray()
            ->one();
        $headers = [];
        foreach ($model as $field=>$value) {
            if ($field == 'tgl_pendaftaran') {
                $value = date('d-m-Y', strtotime($value));
                $value = DocoHelpers::convertTo224($value);
            }
            $headers['#' . $field . '#'] = $value;
        }

        $details = new InfoTagihanPenunjangDetailView;
        $details = $details::find()
            ->andWhere(['pendaftaran_id'=>$pendaftaran_id])
            ->andWhere(['ruangan_id'=>$ruangan])
            ->asArray()->all();
        
        $detailPerRuangan = [];
        foreach ($details as $key => $detail) {
            $detailPerRuangan[$detail['ruangan_nama']][] = $detail;
        }
        
        $print = new DocoPrint();
        $print->attributes = array_merge(
            $headers, 
            [
                '#status_lunas#' => $model['jumlah_tagihan'] ? 'Belum Lunas' : 'Lunas',
                '#data_tagihan#' => $this->renderPartial('print_detail', get_defined_vars())
            ]
        );
        $print->Output();
    }
    public function actionGetApi()
    {
        $result['ruangan'] = [];
        $result['instalasi'] = [];
        $result['carabayar'] = [];
        $result['penjamin'] = [];
        try{
            $listInstalasi = [DocoConstants::INST_ID_RAD, DocoConstants::INST_ID_LAB, DocoConstants::INST_ID_BEDAH];
            $listInstalasi = implode(',', $listInstalasi);
            $result['ruangan'] = Yii::$app->runAction('v1/allow/get-ruangan', [
                'listInstalasi'=> $listInstalasi,
            ]);
            $result['ruangan'] = $result['ruangan']['response'];
            $result['instalasi'] = Yii::$app->runAction('v1/allow/get-instalasi', ['listInstalasi'=> $listInstalasi,]);
            $result['instalasi'] = $result['instalasi']['response'];
            $result['carabayar'] = Yii::$app->runAction('v1/allow/get-cara-bayar');
            $result['carabayar'] = $result['carabayar']['response'];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin');
            $result['penjamin'] = $result['penjamin']['response'];
            return $result;
        } catch(\Exception $e){
            return $result;
        }
    }
}