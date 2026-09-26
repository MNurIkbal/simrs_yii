<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-06-12 09:43:30
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-06-26 16:50:29
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\RencanaPulang;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\RencanaPulangDetail;
use app\modules\v1\models\RencanaPulangDetailView;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\AsesmenMedis;
use Doco\components\DocoPrint;

class DischargePlanningController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\RencanaPulang';

    public function verbs()
    {
        $verbs = parent::verbs();
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
        $request = Yii::$app->request;
        $model = new RencanaPulang;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $idParent = null;
            $postHeader = $request->post('rencana_pulang');
            $postDetail = $request->post('rencana_pulang_detail');
            
            $model = new RencanaPulang;
            $exist = $this->checkExist($postHeader['pasienadmisi_id']);
            if($exist['exist']) {
                $model = $exist['data'];
            }
            
            $model->attributes = $postHeader;
            $tgl_Rpulang = $model->rencana_pulang;
            
            $model->rencana_pulang = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $tgl_Rpulang)));
            
            if($model->save()) {
                $dataInsert = [];
                $idParent = $model->rencanapulang_id;
                if(isset($postDetail['edukasi_kesehatan'])) {
                    if($idParent != null) {
                        RencanaPulangDetail::updateAll([
                            'is_deleted' => true,
                            'deleted_by' => Yii::$app->user->identity->id
                        ], 'rencanapulang_id = '.$idParent.'');
                    }

                    foreach ($postDetail['edukasi_kesehatan'] as $key => $value) {
                        if($key < 22 && isset($postDetail['edukasi_kesehatan'][31])){
                            $data_post = $postDetail['pemberi_edukasi'][$key];
                            $data_tgl = $postDetail['tgl_edukasi'][$key];
                            $data_ppa = $postDetail['ppa'][$key];
                            $dataInsert[] = [
                                'rencanapulang_id' => $idParent,
                                'edukasi_kesehatan' => 31,
                                'pemberi_edukasi' => $data_post,
                                'tgl_edukasi' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data_tgl))),
                                'ppa' => $data_ppa,
                            ];
                        }else{
                            $modelDetail = new RencanaPulangDetail;
                            $dataInsert[] = [
                                'rencanapulang_id' => $idParent,
                                'edukasi_kesehatan' => $value,
                                'pemberi_edukasi' => $postDetail['pemberi_edukasi'][$key],
                                'tgl_edukasi' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $postDetail['tgl_edukasi'][$key]))),
                                'ppa' => $postDetail['ppa'][$key],
                            ];
                        }
                    }
                    if(isset($postDetail['edukasi_kesehatan'][31])) {
                        if (isset($postDetail['pemberi_edukasi_opsional'])) {
                            foreach ($postDetail['pemberi_edukasi_opsional'] as $key => $value) {
                                $datapost = $postDetail['pemberi_edukasi_opsional'][$key];
                                $datatgl = $postDetail['tgl_edukasi_opsional'][$key];
                                $datappa = $postDetail['ppa_opsional'][$key];
                                $dataInsert[] = [
                                    'rencanapulang_id' => $idParent,
                                    'edukasi_kesehatan' => 31,
                                    'pemberi_edukasi' => $datapost,
                                    'tgl_edukasi' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $datatgl))),
                                    'ppa' => $datappa,
                                ];
                            }
                        }
                    }
                }else{
                    RencanaPulangDetail::updateAll([
                        'is_deleted' => true,
                        'deleted_by' => Yii::$app->user->identity->id
                    ], 'rencanapulang_id = '.$idParent.'');
                }
                
                RencanaPulangDetail::batchInsert($dataInsert, false);

                // update pasienadmisi_t
                $modelAdmisi = PasienAdmisi::findOne($postHeader['pasienadmisi_id']);
                $modelAdmisi->rencana_pulang = $model->rencana_pulang;
                $modelAdmisi->save(false);

                $transaction->commit();
                return [
                    'message' => 'sukses',
                    'id_parent' => $idParent,
                ];
            } else {
                return [
                    'data' => $modelDetail->errors,
                    'status' => 422
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    private function lookupKeperawatan($type)
    {
        $model = new LookupKeperawatan;
        $query = $model->find();

        if ($type){
            $query->where(['lookup_type' => $type]);
        }

        return $query->all();
    }

    private function ruanganDokter($ruangan_id){
        $where = " where 1=1";
        if(!empty($ruangan_id)){
            $where = " AND rp.ruangan_id = {$ruangan_id}";
        }

        $request = Yii::$app->request;
        $query = "SELECT p.pegawai_id,p.nama_pegawai from ruanganpegawai_mp rp 
                  INNER JOIN pegawai_m p ON rp.pegawai_id = p.pegawai_id".$where." and rp.is_deleted = false order by p.nama_pegawai ASC";
        $data = Yii::$app->db->createCommand($query)->queryAll();
        return ArrayHelper::map($data,'pegawai_id','nama_pegawai');
    }

    public function actionGetRequest($type, $ruangan_id, $pasienadmisi_id)
    {
        $query = '';
        $queryDetail = '';

        $query = RencanaPulang::find()->where(['pasienadmisi_id' => $pasienadmisi_id])->one();
        if($query) {
            $queryDetail = RencanaPulangDetail::find()
                ->where(['rencanapulang_id' => $query->rencanapulang_id])
                ->orderBy(['rencanapulangdetail_id' => SORT_ASC])
                ->all();
        }

        $asesmenMedis = AsesmenMedis::find()
        ->where(['pasienadmisi_id' => $pasienadmisi_id])
        ->one();
        
        return [
            'assesment' => $this->lookupKeperawatan($type),
            'dokter_ruangan' => $this->ruanganDokter($ruangan_id),
            'dokter_ruangan' => $this->ruanganDokter(null),
            'detail' => $queryDetail,
            'header' => $query,
            'lanjut_discharge' => $asesmenMedis ? $asesmenMedis->discharge_plan : null
        ];
    }

    public function actionDeleteDischarge($rencanapulang_id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $result = (new RencanaPulang)->delete($rencanapulang_id);
            $resultDetail = (new RencanaPulangDetail)->find()->where(['rencanapulang_id' => $rencanapulang_id])->all();
            if($resultDetail) {
                foreach ($resultDetail as $key => $value) {
                    $resultdata = RencanaPulangDetail::find()->where(['rencanapulang_id' => $value['rencanapulang_id']])->one();
                    $resultdata->delete();
                }
            }
            $transaction->commit();
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkExist($pasienadmisi_id)
    {
        $exist = false;
        $query = RencanaPulang::find()->where(['pasienadmisi_id' => $pasienadmisi_id])->one();
        if($query) {
            $exist = true;
        }

        return [
            'exist' => $exist,
            'data' => $query,
        ];
    }

    /**
    * @controller actionPrintPdf 
    * @attribute #noRm# => no rekam medis
    * @attribute #namaPasien# => nama pasien
    * @attribute #tanggalLahir# => tanggal lahir
    * @attribute #sex# => jenis kelamin
    * @attribute #ruanganKelas# => ruangan di rawat
    * @attribute #dokter# => dokter pemeriksa
    * @attribute #penjamin# => penjamin
    * @attribute #infoPenyakit# => informasi penyakit
    * @attribute #lama_perawatan# => lama perawatan
    * @attribute #tgl_rencana_pulang# => tanggal rencana pulang
    * @attribute #rencana_perawatan# => rencana perawatan
    * @attribute #namaDpjp# => nama dokter dpjp
    * @attribute #rencana_transportasi# => rencana transportasi
    * @attribute #table_detail# => table 
    **/
    public function actionPrintPdf($id)
    {
        $request = Yii::$app->request;
        $mHeader = new RencanaPulang;
        $mHeader = $mHeader::find()->where(['pasienadmisi_id'=>$id])->one();

        $mDetail = new RencanaPulangDetailView;
        $mDetail = $mDetail::find()->where(['rencanapulang_id'=>$mHeader->rencanapulang_id])->orderBy([
            'edukasi_kesehatan' => SORT_ASC
          ])->all();
        
        // identitas pasien 
        $sql = "select a.no_rekam_medik, a.nama_pasien, a.tanggal_lahir, a.pasien_id, 
            c.lookup_name, d.ruangan_nama, e.kelaspelayanan_nama, f.nama_pegawai, g.penjamin_nama
            from pasien_m a 
            inner join pasienadmisi_t b on b.pasien_id = a.pasien_id
            inner join lookup_m c on c.lookup_id = cast(a.jeniskelamin as int)
            inner join ruangan_m d on d.ruangan_id = b.ruangan_id
            inner join kelaspelayanan_m e on e.kelaspelayanan_id = b.kelaspelayanan_id
            inner join pegawai_m f on f.pegawai_id = b.pegawai_id
            inner join penjamin_m g on g.penjamin_id = b.penjamin_id
            where b.pasienadmisi_id={$id} 
        "; 
        $dataPasien = Yii::$app->db->createCommand($sql)->queryOne();
        
        $noRm = $dataPasien['no_rekam_medik'];
        $namaPasien = $dataPasien['nama_pasien'];
        $tanggalLahir = $dataPasien['tanggal_lahir'];
        $sex = $dataPasien['lookup_name'];
        $ruanganKelas = $dataPasien['ruangan_nama'].' / '.$dataPasien['kelaspelayanan_nama'];
        $dokter = $dataPasien['nama_pegawai'];
        $penjamin = $dataPasien['penjamin_nama'];

        // assessment
        $infoPenyakit = $mHeader->info_penyakit;
        $lama_perawatan = $mHeader->lama_perawatan;
        $tgl_rencana_pulang = $mHeader->rencana_pulang;
        $rencana_perawatan = $mHeader->rencana_perawatan;
        $rencana_transportasi = $mHeader->rencana_transportasi;


        $print = new DocoPrint();       
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('print_pdf', ['data' => $mDetail]),
            '#noRm#' => $noRm,
            '#namaPasien#' => $namaPasien,
            '#tanggalLahir#' => $tanggalLahir,
            '#sex#' => $sex,
            '#ruanganKelas#' => $ruanganKelas,
            '#dokter#' => $dokter,
            '#penjamin#' => $penjamin,
            '#infoPenyakit#' => $infoPenyakit,
            '#lama_perawatan#' => $lama_perawatan,
            '#tgl_rencana_pulang#' => $tgl_rencana_pulang,
            '#rencana_perawatan#' => $rencana_perawatan,
            '#rencana_transportasi#' => $rencana_transportasi,
            '#namaDpjp#' => Yii::$app->user->identity->nama_pemakai,
        ];

        $print->Output();
    }
}

