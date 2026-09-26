<?php
//Author: Ardi Pratama

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;

use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\helpers\Html;

// Using model

use app\modules\v1\models\Pegawai;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\BagianTubuh;
use app\modules\v1\models\BagianTubuhDetail;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\LookupKeperawatan;

use app\modules\v1\payload\AssessmentDokter;

// Class
class AsesmenDokterController extends DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\AsesmenMedisRD';

    // Verbs
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['save-asesmen-dokter'] = ["POST"];
        return $verbs;
    }

    // Acions
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionBundleDataAsesmenDokter()
    {
        try{
            $params = Yii::$app->request;
            $pendaftaran_id = $params->get('pendaftaran_id',0);
            $pasien_id = $params->get('pasien_id',0);

            $anatomi = [];
            $asesmen = AsesmenMedisRD::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
            if(!is_null($asesmen) && isset($asesmen['asesmenmedisrd_id'])){
                $anatomi = $this->getAnatomi($asesmen['asesmenmedisrd_id']);
            }
            $gcs = $this->getDataGcs();
            
            $riwayat = AsesmenMedisRD::find()
            ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id')
            ->andWhere(['pendaftaran_t.pasien_id'=>$pasien_id])
            ->andWhere(['!=', 'asesmenmedisrd_t.pendaftaran_id', $pendaftaran_id])
            ->all();

            $result =  [
                'data-asesmen-dokter' => $asesmen,
                'data-anatomi' => $anatomi,
                'data-jenisasesmen' => ArrayHelper::map(LookupKeperawatan::find()->where(['lookup_type'=>'jenis_asmen_perawat'])->all(),'lookupkeperawatan_id','lookup_name'),
                'history-asesmen-dokter' => $riwayat
            ];

            $result = array_merge($result,$gcs);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
            $result = [];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
            $result = [];
        }
        return $result;
    }

    public function getAnatomi($asesmenmedisrd_id)
    {
        try {
            $anatomiTubuh = PeriksaTubuh::find()->select([
                    'periksatubuh_t.bagiantubuh_id',
                    'periksatubuh_t.bagiantubuhdetail_id',
                    'periksatubuh_t.catatan_tubuh',
                    'periksatubuh_t.koordinat_x',
                    'periksatubuh_t.koordinat_y',
                    'periksatubuh_t.counters',
                    'periksatubuh_t.created_date',
                    'bagian' => 'bagiantubuh_m.namabagtubuh',
                    'bagianDetail' => 'bagiantubuhdetail_m.nama_bagiantubuh'
                ])->joinWith(
                    [
                        'bagianTubuh' => function ($query) {
                            $query->select([
                                'bagiantubuh_m.bagiantubuh_id'
                            ]);
                        },
                        'bagianTubuhDetail' => function ($query) {
                            $query->select([
                                'bagiantubuhdetail_m.bagiantubuhdetail_id'
                            ]);
                        },
                    ]
                )->where([
                        'asesmenmedisrd_id' => $asesmenmedisrd_id
                ])->orderBy(['counters'=>SORT_ASC])->asArray()->all();
            $result = $anatomiTubuh;
        } catch(\Exception $e) {
            $result = ['message'=>$e->getMessage()];
        }
        return $result;
    }

    public function getDataGcs()
    {
        $data_gcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_MASTER, Gcs::find()->orderBy(['gcs_nilaimin'=>SORT_ASC]));
        $data_metodegcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_METODE, MetodeGcs::find());
        // $data_metodegcs = MetodeGcs::find()->all();
        $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
        $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
        $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
        $data_bagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_BAGIANTUBUH, BagianTubuh::find());
        $data_getdetailbagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_DETAILBAGIANTUBUH, BagianTubuhDetail::find());
        $data_listgcs = [];
        $data_detailbagiantubuh = [];

        foreach ($data_metodegcs as $key => $value) {
            if (!$value['metodegcs_nilai']){
                continue;
            }
            
            $value['nama_and_nilai'] = $value['metodegcs_nama'] . ' - ' . $value['metodegcs_nilai'];
            if ($value['metodegcs_singkatan'] == $gcsindicator_eye){
                $data_listgcs['eye'][] = $value;
            }elseif ($value['metodegcs_singkatan'] == $gcsindicator_verbal){
                $data_listgcs['verbal'][] = $value;
            }elseif ($value['metodegcs_singkatan'] == $gcsindicator_motorik){
                $data_listgcs['motorik'][] = $value;
            }
        }
        foreach ($data_getdetailbagiantubuh as $k => $v) {
            $data_detailbagiantubuh[$v['bagiantubuh_id']][$v['bagiantubuhdetail_id']] = $v['nama_bagiantubuh'];
        }

        return [
            'data-gcs' => $data_gcs,
            'data-listgcs' => $data_listgcs,
            'data-bagiantubuh' => $data_bagiantubuh,
            'data-detailbagiantubuh' => $data_detailbagiantubuh,
        ];
    }

    public function actionSaveAsesmenDokter()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $asesmen = $request->post('AsesmenMedisRDForm');
        $anatomi = $request->post('anatomi');
        $result = [];
        $payload = new AssessmentDokter;
        $payload->attributes = $asesmen;
        if (!$payload->validate()) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }
        if (isset($asesmen['asesmenmedisrd_id']) && $asesmen['asesmenmedisrd_id'] != ''){
            $model = AsesmenMedisRD::find()->where(['asesmenmedisrd_id'=>$asesmen['asesmenmedisrd_id']])->one();
        } else {
            $model = new AsesmenMedisRD;
        }
        $model->attributes = $asesmen;
        if(isset($asesmen['diagnosakerja_id'])){
            $valDiag= [];
            $splitVal = explode('_', $asesmen['diagnosakerja_id']);
            if($splitVal && count($splitVal)>1){
                $valDiag['id'] = $splitVal[0];
                $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                if(isset($data_diag['diagnosa_kode'])){
                    $valDiag['kode'] = $data_diag['diagnosa_kode'];
                    $valDiag['nama'] = $data_diag['diagnosa_nama'];
                }
                $valDiag['text'] = $splitVal[1];
            }else{
                $valDiag['text'] = $asesmen['diagnosakerja_id'];
            }
            $model->diagnosakerja_id = ($valDiag);
            unset($asesmen['diagnosakerja_id']);
         }
        if(isset($asesmen['riwayat_dahulu']) && is_array($asesmen['riwayat_dahulu'])){
            $valueRiwayatDahulu = [];
            foreach ($asesmen['riwayat_dahulu'] as $key_riwayat) {
                $valDahulu = [];
                $splitVal = explode('_', $key_riwayat);
                if($splitVal && count($splitVal)>1){
                    $valDahulu['id'] = $splitVal[0];
                    $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                    if(isset($data_diag['diagnosa_kode'])){
                        $valDahulu['kode'] = $data_diag['diagnosa_kode'];
                        $valDahulu['nama'] = $data_diag['diagnosa_nama'];
                    }
                    $valDahulu['text'] = $splitVal[1];
                }else{
                    $valDahulu['text'] = $key_riwayat; 
                }
                $valueRiwayatDahulu[] = $valDahulu;
            }
            $model->riwayat_dahulu = null;
            $model->riwayat_dahulu = ($valueRiwayatDahulu);
            unset($asesmen['riwayat_dahulu']);
        }

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {

            if(!$model->save()){
                throw new \yii\db\Exception('Gagal Simpan', $model->getErrors(),422);
                
            }

            if($anatomi){
                $dataAnatomi = [
                    'anatomi' => $anatomi,
                    'data_pasien' => [
                        'pendaftaran_id' => $asesmen['pendaftaran_id'],
                        'asesmenmedisrd_id' => $model->getPrimaryKey(),
                    ]
                ];
                $save_anatomi = $this->saveAnatomi($dataAnatomi);
                if(!$save_anatomi){
                    $transaction->rollBack();
                    $result = [
                        'message' => 'Terjadi Kesalahan1!',
                        'status' => 500,
                    ];
                }
            }
            $transaction->commit();
            return [
                'message' => 'Data Berhasil di simpan',
                'status' => 200
            ];
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 422;
            $transaction->rollBack();
            $result = [
                    'message' => 'Terjadi Kesalahan2!',
                    'status' => 500,
                ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $result = [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo,
                'status' => 422
            ];
        }
        
        return $result;
    }

    public function saveAnatomi($anatomi)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $anatomi['data_pasien']['pendaftaran_id'];
        $asesmenmedisrd_id = $anatomi['data_pasien']['asesmenmedisrd_id'];

        $data_bagiantubuh = json_decode($anatomi['anatomi'], true);
        $model = new PeriksaTubuh;
        if ($asesmenmedisrd_id) {
            $count = 0;
            $data_insert_periksatubuh = [];
            foreach ($data_bagiantubuh as $key => $value) {
                $data_insert_periksatubuh_temp = [];
                $data_insert_periksatubuh_temp['asesmenmedisrd_id'] = $asesmenmedisrd_id;
                $data_insert_periksatubuh_temp['bagiantubuh_id'] = $value['bagiantubuh_id'];
                $data_insert_periksatubuh_temp['catatan_tubuh'] = $value['catatan_tubuh'];
                $data_insert_periksatubuh_temp['koordinat_y'] = $value['koordinat_y'];
                $data_insert_periksatubuh_temp['koordinat_x'] = $value['koordinat_x'];
                $data_insert_periksatubuh_temp['counters'] = $value['counters'];
                $data_insert_periksatubuh_temp['created_date'] = isset($value['created_date']) ? $value['created_date'] : '';
                $data_insert_periksatubuh_temp['bagiantubuhdetail_id'] = isset($value['bagiantubuhdetail_id']) ? $value['bagiantubuhdetail_id'] : null;
                $data_insert_periksatubuh[] = $data_insert_periksatubuh_temp;
            }

            Yii::$app->db->createCommand("
                    DELETE FROM periksatubuh_t WHERE asesmenmedisrd_id = $asesmenmedisrd_id
                ")->execute();
            if ($data_insert_periksatubuh){
                $list_columns = [
                    'asesmenmedisrd_id',
                    'bagiantubuh_id',
                    'catatan_tubuh',
                    'koordinat_y',
                    'koordinat_x',
                    'counters',
                    'created_date',
                    'bagiantubuhdetail_id'
                ];
                foreach ($data_insert_periksatubuh as $i_periksatubuh) {
                    $mPeriksaTubuh = new PeriksaTubuh;
                    $mPeriksaTubuh->asesmenmedisrd_id = $i_periksatubuh['asesmenmedisrd_id'];
                    $mPeriksaTubuh->bagiantubuh_id = $i_periksatubuh['bagiantubuh_id'];
                    $mPeriksaTubuh->catatan_tubuh = $i_periksatubuh['catatan_tubuh'];
                    $mPeriksaTubuh->koordinat_y = $i_periksatubuh['koordinat_y'];
                    $mPeriksaTubuh->koordinat_x = $i_periksatubuh['koordinat_x'];
                    $mPeriksaTubuh->counters = $i_periksatubuh['counters'];
                    $mPeriksaTubuh->created_date = $i_periksatubuh['created_date'];
                    $mPeriksaTubuh->bagiantubuhdetail_id = $i_periksatubuh['bagiantubuhdetail_id'];
                    if(!$mPeriksaTubuh->save()){
                        throw new \yii\db\Exception('Gagal Simpan', $mPeriksaTubuh->getErrors(),422);
                        
                    }
                }
                // Yii::$app->db->createCommand()
                //     ->batchInsert(PeriksaTubuh::tableName(), $list_columns, $data_insert_periksatubuh)
                //     ->execute();
            }

            return [
                'message' => 'Data Berhasil di simpan',
                'status' => 200
            ];
        }else{
            return [
                'message' => 'Data fisik kosong',
                'status' => 500
            ];
        }
        
    }

    /**
    * @controller actionCetakPdfAsesmenDokter
    * @attribute #tgl_cetak# => tgl_cetak
    * @attribute #nama_user# => nama_user
    * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
    * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
    * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
    * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
    * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
    * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #inf_umur# => Informasi Pasien: Umur
    * @attribute #inf_dokterjaga# => Informasi Pasien: Dokter Jaga
    * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
    * @attribute #inf_nokamar# => Informasi Pasien: No Kamar
    * @attribute #inf_nobed# => Informasi Pasien: No Bed
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
    * @attribute #inf_ruangan# => Informasi Pasien: Ruangan
    * @attribute #asesmen_dokternama# => Asesmen: Nama Dokter
    * @attribute #asesmen_tglasesmen# => Asesmen: Tanggal Asesmen
    * @attribute #asesmen_jenisasesmen# => Asesmen: Jenis Asesmen
    * @attribute #asesmen_riwayat# => Asesmen: Riwayat
    * @attribute #asesmen_riwayatdahulu# => Asesmen: Riwayat Dahulu
    * @attribute #asesmen_diagnosakerja# => Asesmen: Diagnosa Kerja
    * @attribute #gcs_eye# => Asesmen: GCS Eye
    * @attribute #gcs_verbal# => Asesmen: GCS Verbal
    * @attribute #gcs_motorik# => Asesmen: GCS Motorik
    * @attribute #gcs_hasil# => Asesmen: Hasil GCS
    * @attribute #gcs_nilai# => Asesmen: Nilai GCS
    * @attribute #ket_kapitis# => Asesmen: Kapitis
    * @attribute #tabel_anatomi# => Asesmen: Tabel Anatomi
    * @attribute #gambar_bagiantubuh# => Asesmen: Bagian Tubuh
    * @attribute #status_periksa# => Statu Periksa
    * @attribute #history_penyakit# => menampilkan data History Penyakit
    **/
    public function actionCetakPdfAsesmenDokter()
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $modelHeader = new InfoPasienRdV;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id'=>$request->get('pendaftaran_id')
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $nama_usercetak = $request->get('nama_usercetak','');
        $id_usercetak = $request->get('id_usercetak',0);

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id'=>$id_usercetak])->asArray()->one();
        
        if(is_null($mNamaPegawai)){
            $nama_user = $nama_usercetak;
        }else{
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }


        $diagnosa_kerja = new Expression("
                diagnosakerja_id::json->>'text' AS diagnosa_kerja");
        $riwayat_dahulu = new Expression("CASE WHEN riwayat_dahulu IS NULL THEN array_to_json(null::character varying[]) ELSE
                array_to_json(ARRAY(select json_array_elements(riwayat_dahulu::json)->>'text' from asesmenmedisrd_t inasesmen where inasesmen.asesmenmedisrd_id = asesmenmedisrd_t.asesmenmedisrd_id) ) END AS riwayat_dahulu");
        $queryCetak = (new \yii\db\Query())
                ->select([
                    'asesmenmedisrd_t.asesmenmedisrd_id',
                    'pegawai_m.nama_pegawai AS dokter_nama',
                    'asesmenmedisrd_t.tgl_asesmen',
                    'asesmenmedisrd_t.riwayat',
                    'gcseye.metodegcs_nama AS gcseye_nama',
                    'gcsverbal.metodegcs_nama AS gcsverbal_nama',
                    'gcsmotorik.metodegcs_nama AS gcsmotorik_nama',
                    'asesmenmedisrd_t.hasil_gcs',
                    'asesmenmedisrd_t.jumlah_gcs',
                    'jenisasesmen.lookup_name AS jenisasesmen_nama',
                    'asesmenmedisrd_t.is_kapitis',
                    $diagnosa_kerja,
                    $riwayat_dahulu,
                    'status_periksa.lookup_name AS status_periksa',
                ])
                ->from('asesmenmedisrd_t')
                ->leftJoin('pegawai_m','asesmenmedisrd_t.dokter_id = pegawai_m.pegawai_id')
                ->leftJoin('metodegcs_m gcseye','gcseye.metodegcs_id = asesmenmedisrd_t.gcseye_id')
                ->leftJoin('metodegcs_m gcsverbal','gcsverbal.metodegcs_id = asesmenmedisrd_t.gcsverbal_id')
                ->leftJoin('metodegcs_m gcsmotorik','gcsmotorik.metodegcs_id = asesmenmedisrd_t.gcsmotorik_id')
                ->leftJoin('lookupkeperawatan_m jenisasesmen','jenisasesmen.lookupkeperawatan_id = asesmenmedisrd_t.jenis_asmenperawat')
                ->leftJoin('pendaftaran_t pendaftaran','pendaftaran.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id')
                ->leftJoin('lookup_m status_periksa','status_periksa.lookup_id = pendaftaran.status_periksa::INT')
                ->where(['asesmenmedisrd_t.pendaftaran_id' => $pendaftaran_id]);
        $asesmen_dokter = $queryCetak->one();
        $data_anatomi = [];
        if($asesmen_dokter){
            $data_anatomi = $this->getAnatomi($asesmen_dokter['asesmenmedisrd_id']);
        }

        $riwayat_dahulu_json = isset($asesmen_dokter['riwayat_dahulu']) ? json_decode($asesmen_dokter['riwayat_dahulu'],TRUE) : [];
        $riwayat_dahulu_text = implode(',', $riwayat_dahulu_json);

        $img_tubuh = Html::img('@web/bagian_tubuh.jpg', ['class'=>'img-responsive']);
        $is_kapitis = false;
        if(isset($asesmen_dokter['is_kapitis']) && $asesmen_dokter['is_kapitis'] == true){
            $is_kapitis = true;
        }
        $print = new DocoPrint();

        $riwayat = AsesmenMedisRD::find()
            ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = asesmenmedisrd_t.pendaftaran_id')
            ->andWhere(['pendaftaran_t.pasien_id'=>$resultHeader['pasien_id'] ])
            ->andWhere(['!=', 'asesmenmedisrd_t.pendaftaran_id', $pendaftaran_id])
            ->asArray()->all();

        $historyPenyakit = [];
        if (isset($riwayat) && !empty($riwayat)) {
            foreach ($riwayat as $key => $value) {
                $value = json_decode($value['riwayat_dahulu'], true);

                if (!in_array($value, $historyPenyakit)) {
                    $historyPenyakit[] = $value;
                }
            }
        }

        $viewHistoryPenyakit = '<ul>';
        if (isset($historyPenyakit) && !empty($historyPenyakit)) {
            foreach ($historyPenyakit as $key => $value) {
                if ($value) {
                    foreach ($value as $each) {
                        $viewHistoryPenyakit .= "<li>" . $each['text'] . "</li>";
                    }
                }
            }
        } 
        $viewHistoryPenyakit .=  '</ul>';
        // echo "<pre>";var_dump($viewHistoryPenyakit);die();
        $print->attributes = [
            '#asesmen_dokternama#' => $asesmen_dokter ? @$asesmen_dokter['dokter_nama'] : '',
            '#asesmen_tglasesmen#' => $asesmen_dokter ? ($asesmen_dokter['tgl_asesmen'] ? date('d F Y', strtotime($asesmen_dokter['tgl_asesmen'])) : '') : '',
            '#asesmen_jenisasesmen#' => $asesmen_dokter ? @$asesmen_dokter['jenisasesmen_nama'] : '',
            '#asesmen_riwayat#' => $asesmen_dokter ? @$asesmen_dokter['riwayat'] : '',
            '#asesmen_riwayatdahulu#' => $riwayat_dahulu_text,
            '#asesmen_diagnosakerja#' => $asesmen_dokter ? @$asesmen_dokter['diagnosa_kerja'] : '',
            '#gcs_eye#' => $asesmen_dokter ? @$asesmen_dokter['gcseye_nama'] : '',
            '#gcs_verbal#' => $asesmen_dokter ? @$asesmen_dokter['gcsverbal_nama'] : '',
            '#gcs_motorik#' => $asesmen_dokter ? @$asesmen_dokter['gcsmotorik_nama'] : '',
            '#gcs_hasil#' => $asesmen_dokter ? @$asesmen_dokter['hasil_gcs'] : '',
            '#gcs_nilai#' => $asesmen_dokter ? @$asesmen_dokter['jumlah_gcs'] : '',
            '#ket_kapitis#' => $is_kapitis == true ? 'Ya': 'Tidak',
            '#gambar_bagiantubuh#' => $this->renderPartial('cetakan_bagian_tubuh',['img_tubuh'=>$img_tubuh]),
            '#tabel_anatomi#' => $this->renderPartial('cetakan_tabel_anatomi',['data_anatomi'=>$data_anatomi]),
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d F Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : '',
            // '#status_periksa#' => $resultHeader ? $resultHeader['status_periksa'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d F Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_dokterjaga#' => $resultHeader ? $resultHeader['dokter_jaga'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? $resultHeader['kelaspelayanan_nama'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#' => $resultHeader ? $resultHeader['carabayar_nama'] : '',
            '#inf_ruangan#' => $resultHeader ? $resultHeader['ruangan_nama'] : '',
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
            '#history_penyakit#' => $this->renderPartial('_cetak_history_penyakit_pdf', [
                    'viewHistoryPenyakit' => $viewHistoryPenyakit,
                ]),
            '#status_periksa#' => $asesmen_dokter ? @$asesmen_dokter['status_periksa'] : '',
        ];
        $print->Output();
    }
}