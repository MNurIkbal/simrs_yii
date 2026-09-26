<?php

namespace Doco\Traits;

use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\models\InfoRiwayatPasienView;
use Doco\models\RencanaOperasiDetail;
use Doco\models\InfoKunjunganRsView;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\models\DokumenUpload;
use Doco\models\Pasien;
use Mpdf\Tag\P;
use Yii;

use Doco\models\RujukanPulang;
use yii\helpers\ArrayHelper;
use Doco\models\AsesmenMedis;

trait HistoryPatientTrait
{

    /**
     * Return data 
     * 
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDataHistoryPatient()
    {
        try {
            $request = Yii::$app->request;
            $norm = $request->get('norm');
            $is_jenis = $request->get('is_jenis');
            $pendaftaran_id = $request->get('pendaftaran_id');

            $connection = Yii::$app->db;
            $sql = "SELECT pasien_id FROM pasien_m WHERE no_rekam_medik = :norm";
            $command = $connection->createCommand($sql);
            $command->bindValue(':norm', $norm);
            $pasien = $command->queryOne();

            $filter  = $request->get('filter');
            $conditions = "";
            if (isset($filter['advancedFilter']['ruangan_pend_id']) && !empty($filter['advancedFilter']['ruangan_pend_id'])) {
                $conditions .= " AND ruangan_id = ".$filter['advancedFilter']['ruangan_pend_id'];
            }

            if (isset($filter['advancedFilter']['dok_rjrd_id']) && !empty($filter['advancedFilter']['dok_rjrd_id'])) {
                $conditions .= " AND pegawai_id = ".$filter['advancedFilter']['dok_rjrd_id'];
            }
            $pendaftaranSql = "SELECT pendaftaran_id FROM pendaftaran_t WHERE pasien_id = :pasien_id $conditions order by pendaftaran_id desc limit :limit";
            $command = $connection->createCommand($pendaftaranSql);
            $command->bindValue(':pasien_id', ArrayHelper::getValue($pasien, 'pasien_id'));
            $command->bindValue(':limit', ArrayHelper::getValue($filter, 'per-page'));
            $pendaftaran = $command->queryAll();
            $pendaftaran = ArrayHelper::getColumn($pendaftaran, 'pendaftaran_id');

            $model = new InfoRiwayatPasienView;
            $query = $model::find();
            $query->where(['pendaftaran_id' => $pendaftaran]);

            if ($is_jenis == 'lab') {
                $query->andWhere(['p_laboratorium' => 1]);
            } elseif ($is_jenis == 'rad') {
                $query->andWhere(['p_radiologi' => 1]);
            }
            $query->orderBy(['pendaftaran_id' => SORT_DESC]);
            if (isset($pendaftaran_id)) {
                $query->select([$pendaftaran_id]);
                $query->groupBy([$pendaftaran_id]);
                $query->having('count(*) > 1');
            }

            $filter  = $request->get('filter');
            $page    = $filter['page'];
            $perpage = $filter['per-page'];
            $offset  = ($page - 1) * $perpage;

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->limit($perpage);
            $record = $query->asArray()->all();
            $totalData = count($record);
            usort($record, function ($a, $b) {
                $tgl_pendaftaran_1 = strtotime($a['tgl_pendaftaran']);
                $tgl_pendaftaran_2 = strtotime($b['tgl_pendaftaran']);
            
                if ($tgl_pendaftaran_1 == $tgl_pendaftaran_2) {
                    return 0;
                }
                return ($tgl_pendaftaran_1 > $tgl_pendaftaran_2) ? -1 : 1;
            });            
            $pendaftaran_ids = ArrayHelper::getColumn($record, 'pendaftaran_id');

            // cek pendaftaran_id mana yang memiliki data rujukan pulang
            if (!empty($pendaftaran_ids)) {
                $inCondition = "(" . implode(",", $pendaftaran_ids) . ")";
                $sql = "SELECT pendaftaran_id FROM rujukanpulang_t WHERE pendaftaran_id in {$inCondition}";
                $command = $connection->createCommand($sql);
                $dataRujukan = $command->queryAll();
                $pendaftaran_ids = ArrayHelper::getColumn($dataRujukan, 'pendaftaran_id');
            }
            
            return [
                'data' => $record,
                'load_more' => $totalData == $perpage ? true : false,
                'dataRujukan' => $pendaftaran_ids,
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

    /**
     * Return data 
     * 
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDataHistorySurgery()
    {
        try {
            $request = Yii::$app->request;
            $norm = $request->get('norm');

            $model = new RencanaOperasiDetail;
            $query = $model::find()
                ->select([
                    'no_orderkeunitlain AS no_pemeriksaan',
                    'tgl_permintaan AS tanggal_permintaan',
                    'kegiatanoperasi_nama AS jenis_pemeriksaan',
                    'operasi_nama AS nama_pemeriksaan',
                    'status',
                    'disetujui_oleh',
                    'tanggal_disetujui',
                    'jam_rencana_mulai',
                    'jam_rencana_selesai',
                    'created_date'
                ])
                ->where(['no_rekam_medik' => $norm])
                ->orderBy(['tgl_permintaan' => SORT_DESC]);

            $filter  = $request->get('filter');
            $page    = $filter['page'];
            $perpage = $filter['per-page'];
            $offset  = ($page - 1) * $perpage;
            $record  = $query->limit($perpage)->offset($offset)->asArray()->all();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $totalRecord = $query->count();

            return [
                'recordsFiltered' => $totalRecord,
                'recordsTotal' => $totalRecord,
                'data' => $record,
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

    /**
     * Return data 
     * 
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionPatientGlobal()
    {
        try {
            $request        = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $pasienadmisi_id = $request->get('pasienadmisi_id', null);
            $ruangan_id = $request->get('ruangan_id', null);

            $model = new InfoKunjunganRsView;
            $query = $model::find()
                ->select([
                    'nama_pasien',
                    'no_rekam_medik',
                    'no_telepon_pasien',
                    'umur',
                    'jenis_kelamin',
                    'penjamin_nama', 
                    new \yii\db\Expression("CASE WHEN is_pasientitipan = true THEN kelas_ditagihkan_nama ELSE kelaspelayanan_nama END AS kelaspelayanan_nama"),
                ])
                ->where(['pendaftaran_id' => $pendaftaran_id]);
            
            if ($pasienadmisi_id !== null) {
                $query->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
            }

            if ($ruangan_id !== null) {
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            }

            return [
                'data' => $query->asArray()->one()
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

    public function actionGetDokumenData()
    {
        $returnOne = false;
        $start = Yii::$app->request->get('start', 0);
        $length = Yii::$app->request->get('length', 10);
        $data = DokumenUpload::find()->select([
            'dokumenupload_t.path',
            'dokumenupload_t.filename',
            'dokumen_m.nama_dokumen',
            'dokumenupload_t.dokumenupload_id'
        ])->where([
            'dokumenupload_t.pendaftaran_id' => Yii::$app->request->get('pendaftaran_id')
        ])
            ->rightJoin('dokumen_m', 'dokumen_m.dokumen_id = dokumenupload_t.dokumen_id');
        if (!empty(Yii::$app->request->get('pasienadmisi_id', null))) {
            $data->andWhere([
                'dokumenupload_t.pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null)
            ]);
        } else {
            $data->andWhere([
                'IS', 'dokumenupload_t.pasienadmisi_id' => new \yii\db\Expression('null')
            ]);
        }

        if (!is_null(Yii::$app->request->get('dokumenupload_id', null))) {
            $data->andWhere([
                '=', 'dokumenupload_t.dokumenupload_id', Yii::$app->request->get('dokumenupload_id', null)
            ]);
            $returnOne = true;
        }
        $countTotal = $data->count();
        $data->orderBy([
            'dokumenupload_t.dokumenupload_id' => SORT_DESC
        ]);
        if (!$returnOne) {
            $data->offset($start)->limit($length);
        }
        return [
            'data' => $returnOne ? $data->asArray()->one() : $data->asArray()->all(),
            'recordsTotal' => $countTotal,
            'recordsFiltered' => $countTotal,
        ];
    }

    public function actionGetDetailDokumen()
    {
        return DokumenUpload::find()->select([
            'dokumenupload_t.path',
            'dokumenupload_t.filename',
            'dokumen_m.nama_dokumen',
            'dokumenupload_t.dokumenupload_id'
        ])
            ->rightJoin('dokumen_m', 'dokumen_m.dokumen_id = dokumenupload_t.dokumen_id')
            ->where([
                'dokumenupload_t.dokumenupload_id' => Yii::$app->request->get('dokumenupload_id')
            ])->asArray()->one();
    }

    public function actionGetDataRujukan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');

        $dataRujukan = RujukanPulang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        return [
            'totalCount' => count($dataRujukan)
        ];
    }

    public function actionGetKonfigNas(){
        
        try {    
            $constantID = new DocoConstansId;
            $konfig = $constantID->actionGetId(DocoConstants::KONFIG_FTP_NAS);
            $konfigftp_nas = $constantID->actionGetAdditional(DocoConstants::KONFIG_FTP_NAS);
            $response = [
                'konfig' => $konfig,
                'konfigftp_nas' => json_decode($konfigftp_nas, true)
            ];
            return $response;
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

    public function actionListAsmedSpesialis()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $pasienAdmisiId = $request->get('pasienadmisi_id');
        $asmed = AsesmenMedis::find()
            ->select(['asesmenmedis_t.asesmenmedis_id', 'lookup_m.lookup_name AS nama_dokumen', 'asesmenmedis_t.tgl_asesmenmedis AS tanggal',
                'lookup_m.lookup_kode', 'asesmenmedis_t.is_dokumen_eklaim', 'asesmenmedis_t.formasesmen_id',
                new \yii\db\Expression('CASE WHEN pegawai_update.nama_pegawai IS NOT NULL THEN pegawai_update.nama_pegawai ELSE pegawai_m.nama_pegawai END AS user_input')])
            ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id=asesmenmedis_t.formasesmen_id')
            ->join('JOIN', 'loginpemakai_k', 'loginpemakai_k.loginpemakai_id=asesmenmedis_t.created_by')
            ->join('JOIN', 'pegawai_m', 'pegawai_m.pegawai_id=loginpemakai_k.pegawai_id')
            ->join('LEFT JOIN', 'loginpemakai_k user_update', 'user_update.loginpemakai_id=asesmenmedis_t.last_modified_by')
            ->join('LEFT JOIN', 'pegawai_m pegawai_update', 'pegawai_update.pegawai_id=user_update.pegawai_id')
            ->where(['pendaftaran_id' => $pendaftaranId, 'pasienadmisi_id' => $pasienAdmisiId])
            ->asArray()->all();

        $infoPasien = (new \yii\db\Query())
            ->select([
                'nama_pasien',
                'alamat_pasien',
                'no_rekam_medik',
                'umur',
            ])
            ->from('infokunjunganrs_v')
            ->where(['pendaftaran_id' => $pendaftaranId]);

        if (!is_null($pasienAdmisiId)) {
            $infoPasien->andWhere(['pasienadmisi_id' => $pasienAdmisiId]);
        }
           
        $infoPasien = $infoPasien->one();

        return [
            'asmed' => $asmed,
            'info_pasien' => $infoPasien,
        ];
    }
}
