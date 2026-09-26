<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Sunarko
 * @Date:    2018-06-25 18:43:42
 * @Last Modified by:
 * @Last Modified time:
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\InfoPasienPindah;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class InfPasienPindahController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienPindah';
    protected $_title = 'Informasi Pasien';

    public function verbs()
    {
        $verbs = parent::verbs();
        //$verbs["index"] = ["POST", "GET"];
        //$verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        //unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new InfoPasienPindah;
        $query = $model::find();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pindahkamar'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pindahkamar']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pindahkamar']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['between', 'tgl_pindahkamar', $start, $end]);
        
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataPendaftaran()
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
        $sql = "select pendaftaran_id, no_pendaftaran from infodatapendaftaran_v where no_pendaftaran LIKE '%{$term}%'
            group by pendaftaran_id, no_pendaftaran
            order by no_pendaftaran asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataRekamMedik()
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
        $sql = "select no_rekam_medik from infodatapendaftaran_v where no_rekam_medik LIKE '%{$term}%'
            group by no_rekam_medik
            order by no_rekam_medik asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNamaPasien()
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
        $sql = "select nama_pasien from infodatapendaftaran_v where UPPER( nama_pasien ) LIKE '%{$term}%'
            group by nama_pasien
            order by nama_pasien asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNamaDokter()
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
        $sql = "select nama_pegawai from dokter_v where UPPER( nama_pegawai ) LIKE '%{$term}%'
            group by nama_pegawai
            order by nama_pegawai asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataKasusPenyakit()
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
        $sql = "select jeniskasuspenyakit_nama from jeniskasuspenyakit_m where UPPER( jeniskasuspenyakit_nama ) LIKE '%{$term}%'
            group by jeniskasuspenyakit_nama
            order by jeniskasuspenyakit_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNamaRuangan()
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
        $sql = "select ruangan_nama from ruangan_m where UPPER( ruangan_nama ) LIKE '%{$term}%'
            group by ruangan_nama
            order by ruangan_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetDataKamar()
    {
        $request = Yii::$app->request;
        
        $model = new KamarRuanganView;
        $query = $model::find();
        if($request->get('jeniskasuspenyakit_id')){
            $query->andWhere(['jeniskasuspenyakit_id'=>$request->get('jeniskasuspenyakit_id')]);
        }
        if($request->get('kelaspelayanan_id')){
            $query->andWhere(['kelaspelayanan_id'=>$request->get('kelaspelayanan_id')]);
        }
        if($request->get('ruangan_id')){
            $query->andWhere(['ruangan_id'=>$request->get('ruangan_id')]);
        }

        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return [
            'data'=>$query->asArray()->all(),
            'list-ruangan' => $query->select(['ruangan_id','ruangan_nama','kamarruangan_nokamar'])->distinct()->orderBy(['ruangan_nama'=>SORT_ASC,'kamarruangan_nokamar'=>SORT_ASC])->all()
        ];
    }

}
