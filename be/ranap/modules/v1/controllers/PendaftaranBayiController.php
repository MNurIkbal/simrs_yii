<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-03-08 14:22:55
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-08 16:02:41
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoKunjunganRi;

class PendaftaranBayiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';

    public function actionCariRmIbu()
    {
        $params = Yii::$app->request;
        $term = $params->get('term','');
        $page = $params->get('page',0);
        $limit = $params->get('limit',5);
        $offset = $params->get('offset',0);
        try {
            $getData = InfoKunjunganRi::find();
            if($term){
                $getData->andWhere(['ILIKE', 'no_rekam_medik', $term]);
                $getData->orWhere(['ILIKE', 'nama_pasien', $term]);
            }
            return $getData->offset($offset)->limit($limit)->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }
    public function actionPackPendaftaranBayi(){
        try {
            // get all lookup by request
            $listRequestLookup = [
                'jenis_identitas',
                'nama_depan',
                'jenis_kelamin',
                'status_perkawinan',
                'warga_negara',
                'agama',
                'pengantar',
                'golongan_darah',
                'status_rekammedik',
                'keadaan_masuk',
                'transportasi',
                'hubungan_keluarga',
                'hubungan'
            ];
            $lookup = $this->listLookup($listRequestLookup);

            // get all master by request
            $listRequestMaster = [
                'pendidikan' => 'Pendidikan',
                'pekerjaan' => 'Pekerjaan',
                'propinsi' => 'Propinsi',
                'suku' => 'Suku',
            ];
            $master = $this->getListMaster($listRequestMaster);

            // get ruangan by instalasi
            if (isset($get['instalasi_id'])) {
                $ruangan = $this->getRuanganByJson($get['instalasi_id']);
            }

            // get cara bayar
            if (isset($get['default'])) {
                $cara_bayar = $this->actionListCaraBayar($get['default']);
            }

            // get asal rujukan
            $asal_rujukan = $this->actionListAsalRujukan();

            // get kelas pelayanan
            $kelas_pelayanan = $this->actionGetListKelasPelayanan();

            // get karcis
            $karcis = $this->actionListKarcis();

            // jenis kasus penyakit
            // $modelJenisKasus = new JenisKasusPenyakit;
            // $queryJenisKasus = $modelJenisKasus::find(true);
            // $queryJenisKasus = DocoRestActiveFilter::advancedFilter($modelJenisKasus, $queryJenisKasus);
            // $queryJenisKasus = new ActiveDataProvider([
            //     'query' => $queryJenisKasus,
            // ]);
            $jeniskasus = $this->actionListPenyakit(false);

            // dokter PJ
            $modelDokter = new DokterView;
            $queryDokter = $modelDokter::find()->asArray()->all();

            // penjamin
            $modelPenjamin = new Penjamin;
            $queryPenjamin = $modelPenjamin::find();
            $queryPenjamin = DocoRestActiveFilter::advancedFilter($modelPenjamin, $queryPenjamin);
            $queryPenjamin = new ActiveDataProvider([
                'query' => $queryPenjamin,
            ]);

            $instalasi = $this->actionListInstalasi('true');

            $result = [
                'lookup' => $lookup,
                'master' => $master,
                'ruangan' => $ruangan,
                'cara_bayar' => $cara_bayar,
                'asal_rujukan' => $asal_rujukan,
                'kelas_pelayanan' => $kelas_pelayanan,
                'karcis' => $karcis,
                'jeniskasus' => $jeniskasus,//$queryJenisKasus->getModels(),
                'dokter' => $queryDokter,
                'penjamin' => $queryPenjamin->getModels(),
                'instalasi' => $instalasi
            ];

            return $result;
            return $result;
        } catch (Exception $e) {
            throw new \Exception("Terjadi Kesalahan", 1);
        }
    }
}