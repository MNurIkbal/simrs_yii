<?php
/**
 * @author: arief saputra
 * @description: master untuk CRUD Layar Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\InforinciantagihaPasienView;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class InfRincianTagihanPasienController extends \Doco\components\DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InforinciantagihaPasienView';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-layarantrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            
            $get = $request->get();
            $page = (isset($get['page'])) ? $get['page']: 1;
            $perPage = (isset($get['per-page'])) ? $get['per-page']: 10;

            $offset = ($page - 1) * $perPage;

            $result = $this->getData()
                ->limit($request->post('length',$perPage))
                ->offset($request->post('start',$offset));

            $result->select(['DATE(tgl_pendaftaran) AS tgl_daftar','pendaftaran_id','no_pendaftaran','no_rekam_medik','nama_pasien','carabayar_nama','penjamin_nama','nama_pegawai','kelaspelayanan_nama','jeniskasuspenyakit_nama','SUM ( tarif_tindakan ) AS total_tagihan', 'statusbayar_nama']);
        	
        	$result->groupby(['tgl_daftar','pendaftaran_id','no_pendaftaran','no_rekam_medik','nama_pasien','carabayar_nama','penjamin_nama','nama_pegawai','kelaspelayanan_nama','jeniskasuspenyakit_nama','statusbayar_nama']);

            if ($indexing = $request->post('tindakanruangan')) {
                $result->andFilterWhere(['ILIKE', 't.daftartindakan_id', $indexing]);
            }

            $status = $request->post('is_active');
            if($status) {
                $status = $status ? true : false;
                $result->andWhere(['t.is_active' => $status]);
            }

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }
            else{
            	$result->orderby(['tgl_daftar' => 'asc']);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id = null)
    {
        $result = $this->getData();
        $pendaftaran_id = $_GET['pendaftaran_id'];

        $result->andWhere(['pendaftaran_id' => $pendaftaran_id]);
        $result->orderby(['tgl_tindakan' => 'asc']);

        // return $result->one();
        return $result->asArray()->all();
    }

    public function getData()
    {
    	$data = InforinciantagihaPasienView::find();
    	return $data;
    }
}