<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\CaraMasuk;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Suku;
use app\modules\v1\models\AsesmenPerawatRD;
use app\modules\v1\models\AsesmenResikoJatuh;
use app\modules\v1\models\AsesmenKeperawatanRD;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\AsesmenResikoJatuhSydneyRD;
use app\modules\v1\models\AsesmenResikoJatuhDumptyRD;
use app\modules\v1\models\AsesmenResikoJatuhMorseRD;
use app\modules\v1\models\ResikoJatuh;
use app\modules\v1\models\Triase;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoConstants;
use SirsCore\businessLogic\MonitoringTtvLogic;
use Doco\models\ProfilRsView;

class AsesmenKeperawatanController extends DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\AsesmenPerawatRD';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }
    /**
     * Function for handle default data Asesmen Keperawatan
     * 
     * @param Integer pendaftaran_id
     * @return Array
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionIndex()
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(404, 'Data Tidak Ditemukan');
        }
        $getAsesmenKeperawatan = AsesmenPerawatRD::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        $getAsesmenDetail = null;
        if (!is_null($getAsesmenKeperawatan)) {
            if (isset($getAsesmenKeperawatan['pegawaiverifikasigizi_id']) && !empty($getAsesmenKeperawatan['pegawaiverifikasigizi_id'])) {
                $getAsesmenKeperawatan['pegawaiverifikasigizi_nama'] = Pegawai::find()->select(['nama_pegawai'])->where(['pegawai_id' => $getAsesmenKeperawatan['pegawaiverifikasigizi_id']])->scalar();
            }
            $getAsesmenDetail = AsesmenResikoJatuh::find()
                ->where(['asesmenperawatrd_id' => $getAsesmenKeperawatan['asesmenperawatrd_id']])
                ->orderBy([
                    'asesmenrdresikojatuh_id' => SORT_DESC
                ])
                ->limit(3)
                ->asArray()
                ->all();
            for ($i = 0; $i < sizeof($getAsesmenDetail); $i++) {
                $getAsesmenDetail[$i]['skala_nyeri'] = str_replace('-undefined', '', $getAsesmenDetail[$i]['skala_nyeri']);
            }
            $getSuggestData = Pendaftaran::find()
                ->select([
                    'triase_t.keluhan_utama',
                    'triase_t.alergi_obat',
                    'triase_t.alergi_lainnya',
                    'triase_t.hasil_triase as kategori_triase_sehari',
                    'triase_t.pernafasan',
                    'triase_t.sirkulasi',
                    'triase_t.tekanan_darah as tensi',
                    'triase_t.nadi as detak_nadi',
                    'triase_t.suhu as suhu_tubuh',
                    'triase_t.gcseye_id',
                    'triase_t.gcsverbal_id',
                    'triase_t.gcsmotorik_id',
                    'triase_t.hasil_gcs',
                    'triase_t.is_kapitis',
                ])
                ->rightJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
                ->join('JOIN', 'carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
                ->join('LEFT JOIN', 'triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where([
                    'pendaftaran_t.pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            foreach ($getSuggestData as $key => $value) {
                if (empty($getAsesmenKeperawatan[$key])) {
                    $getAsesmenKeperawatan[$key] = $value;
                }
            }
        } else {
            $getAsesmenKeperawatan = Pendaftaran::find()
                ->select([
                    'pasien_m.agama as agama_id',
                    'pasien_m.pekerjaan_id',
                    'pendaftaran_t.transportasi as caramasuk_id',
                    'pasien_m.pendidikan_id',
                    'pendaftaran_t.tgl_pendaftaran as tgl_datang',
                    'pendaftaran_t.tgl_pendaftaran',
                    'pendaftaran_t.pasien_id',
                    'pendaftaran_t.carabayar_id',
                    'carabayar_m.carabayar_nama',
                    'pendaftaran_t.pendaftaran_id',
                    'triase_t.keluhan_utama',
                    'triase_t.alergi_obat',
                    'triase_t.alergi_lainnya',
                    'triase_t.hasil_triase as kategori_triase_sehari',
                    'triase_t.pernafasan',
                    'triase_t.sirkulasi',
                    'triase_t.tekanan_darah as tensi',
                    'triase_t.nadi as detak_nadi',
                    'triase_t.suhu as suhu_tubuh',
                    'triase_t.gcseye_id',
                    'triase_t.gcsverbal_id',
                    'triase_t.gcsmotorik_id',
                    'triase_t.hasil_gcs',
                    'triase_t.is_kapitis',
                ])
                ->rightJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
                ->join('JOIN', 'carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
                ->join('LEFT JOIN', 'triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where([
                    'pendaftaran_t.pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            $getAsesmenKeperawatan['kategori_triase_disaster'] = null;
            if ($getAsesmenKeperawatan['pernafasan'] == 'henti_napas-resusitasi' && $getAsesmenKeperawatan['sirkulasi'] == 'henti_jantung-resusitasi') {
                $getAsesmenKeperawatan['kategori_triase_disaster'] = 'hitam';
            }
            $asmedRecord = AsesmenMedisRD::find()
                ->select([
                    'asesmenmedisrd_t.pendaftaran_id',
                    'asesmenmedisrd_t.riwayat_penyakit_sekarang',
                    'asesmenmedisrd_t.riwayat_penyakit_dahulu',
                    'asesmenmedisrd_t.riwayat_terapi_sebelumnya',
                    'asesmenmedisrd_t.alergi',
                    'asesmenmedisrd_t.created_date as asmed_date',
                ])
                ->join('JOIN', 'pendaftaran_t', 'asesmenmedisrd_t.pendaftaran_id=pendaftaran_t.pendaftaran_id')
                ->andWhere([
                    'pendaftaran_t.pasien_id' => $getAsesmenKeperawatan['pasien_id']
                ])
                ->andWhere([
                    '!=', 'asesmenmedisrd_t.pendaftaran_id', $getAsesmenKeperawatan['pendaftaran_id']
                ])
                ->orderBy([
                    'asesmenmedisrd_t.created_date' => SORT_DESC
                ])
                ->asArray()
                ->one();
            $getAsesmenKeperawatan = array_merge($getAsesmenKeperawatan, (!empty($asmedRecord) ? $asmedRecord : [
                'riwayat_penyakit_sekarang' => null,
                'riwayat_penyakit_dahulu' => null,
                'riwayat_terapi_sebelumnya' => null,
                'alergi' => null,
                'created_date as asmed_date' => null,
            ]));
        }

        // GCS
        $getGcs = Gcs::find()
            ->select([
                'gcs_id as id',
                'gcs_nama as text',
                'gcs_namalainnya',
                'gcs_nilaimin',
                'gcs_nilaimax',
                'is_kapitis',
            ])->orderBy([
                'gcs_nilaimin' => SORT_ASC
            ])
            ->asArray()
            ->all();
        $getMetodeGcs = MetodeGcs::find()
            ->select([
                'metodegcs_id as id',
                'metodegcs_nama as text',
                'metodegcs_singkatan',
                'metodegcs_nilai',
            ])
            ->asArray()
            ->all();
        $metodeGcs = [];
        foreach ($getMetodeGcs as $item => $metode) {
            $metode['text'] = $metode['text'] . " - " . $metode['metodegcs_nilai'];
            $metodeGcs[$metode['metodegcs_singkatan']][] = $metode;
        }

        // MASTER
        $getAgama = $this->getLookupByType(['agama'])
            ->select([
                'lookup_id as id',
                'lookup_name as text',
                'lookup_type',
                'lookup_value',
            ])
            ->orderBy(['lookup_type' => SORT_ASC, 'lookup_urutan' => SORT_ASC])
            ->asArray()
            ->all();
        $getPekerjaan = Pekerjaan::find()
            ->select([
                'pekerjaan_id as id',
                'pekerjaan_nama as text'
            ])
            ->asArray()
            ->all();

        // $getCaraMasuk = CaraMasuk::find()
        //     ->select([
        //         'caramasuk_id as id',
        //         'caramasuk_nama as text',
        //     ])
        //     ->asArray()
        //     ->all();

        $getCaraMasuk = $this->getLookupByType(['transportasi'])
            ->select([
                'lookup_id as id',
                'lookup_name as text',
                'lookup_type',
                'lookup_value',
            ])
            ->orderBy(['lookup_type' => SORT_ASC, 'lookup_urutan' => SORT_ASC])
            ->asArray()
            ->all();
        $getPendidikan = Pendidikan::find()
            ->select([
                'pendidikan_id as id',
                'pendidikan_nama as text',
            ])
            ->asArray()
            ->all();
        $getSuku = Suku::find()
            ->select([
                'suku_id as id',
                'suku_nama as text'
            ])
            ->asArray()
            ->all();
        
        $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');

        return [
            'asesmenkeperawatan' => $getAsesmenKeperawatan,
            'riwayat' => $getAsesmenDetail,
            'gcs' => $getGcs,
            'agama' => $getAgama,
            'metodegcs' => $metodeGcs,
            'pekerjaan' => $getPekerjaan,
            'caramasuk' => $getCaraMasuk,
            'pendidikan' => $getPendidikan,
            'suku' => $getSuku,
            'enable_pulang' => $enable_pulang,
        ];
    }

    /**
     * function for save asesmen keperawatan
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmen()
    {
        $request = Yii::$app->request;
        $dataKeperawatan = $request->post('formdata', []);
        $dataResikoJatuh = isset($dataKeperawatan['resiko_jatuh']) && !empty($dataKeperawatan['resiko_jatuh']) ? $dataKeperawatan['resiko_jatuh'] : [];

        $pendaftaran_id = !isset($dataKeperawatan['pendaftaran_id']) || empty($dataKeperawatan['pendaftaran_id']) ? null : $dataKeperawatan['pendaftaran_id'];
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model = AsesmenKeperawatanRD::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        if (empty($model)) {
            $model = new AsesmenKeperawatanRD;
        }
        $model->ruangan_id = empty($model->ruangan_id) ? Yii::$app->jwt->ruangan_id : $model->ruangan_id;
        $model->attributes = $dataKeperawatan;
        $model->tgl_asesmen = date('Y-m-d H:i:s');
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        MonitoringTtvLogic::feedData($dataKeperawatan, DocoConstants::ASESMEN_KEPERAWATAN, DocoConstants::INSTALASI_RAWAT_DARURAT);

        $asesmenperawatrd_id = $model->getPrimaryKey();
        if (!empty($dataResikoJatuh) && !empty($model->is_nyeri)) {
            $modelResikoJatuh = new AsesmenResikoJatuh;
            $modelResikoJatuh->asesmenperawatrd_id = $asesmenperawatrd_id;
            $modelResikoJatuh->tgl_pengkajian = date('Y-m-d H:i:s');
            $modelResikoJatuh->attributes = $dataResikoJatuh;
            if (!$modelResikoJatuh->save()) {
                $transaction->rollBack();
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Asesmen Keperawatan Berhasil!');
    }

    /**
     * function for save sydney
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveSydney()
    {
        $request     = Yii::$app->request;
        $dataSydney  = $request->post('formdata', []);

        $pendaftaran_id = !isset($dataSydney['pendaftaran_id']) || empty($dataSydney['pendaftaran_id']) ? null : $dataSydney['pendaftaran_id'];

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        AsesmenResikoJatuhSydneyRD::updateAll(['is_deleted' => true], ['pendaftaran_id' => $pendaftaran_id]);
        $model = new AsesmenResikoJatuhSydneyRD;
        $model->attributes = $dataSydney;
        $model->is_mandiri = empty($dataSydney['is_mandiri']) ? '0-tidak' : $dataSydney['is_mandiri'];
        $model->skor_mandiri = empty($dataSydney['skor_mandiri']) ? 0 : $dataSydney['skor_mandiri'];
        $model->is_bantuan_sedikit = empty($dataSydney['is_bantuan_sedikit']) ? '0-tidak' : $dataSydney['is_bantuan_sedikit'];
        $model->skor_bantuan_sedikit = empty($dataSydney['skor_bantuan_sedikit']) ? 0 : $dataSydney['skor_bantuan_sedikit'];
        $model->is_bantuan_nyata = empty($dataSydney['is_bantuan_nyata']) ? '0-tidak' : $dataSydney['is_bantuan_nyata'];
        $model->skor_bantuan_nyata = empty($dataSydney['skor_bantuan_nyata']) ? 0 : $dataSydney['skor_bantuan_nyata'];
        $model->is_bantuan_total = empty($dataSydney['is_bantuan_total']) ? '0-tidak' : $dataSydney['is_bantuan_total'];
        $model->skor_bantuan_total = empty($dataSydney['skor_bantuan_total']) ? 0 : $dataSydney['skor_bantuan_total'];

        $model->is_mobilitas_mandiri = empty($dataSydney['is_mobilitas_mandiri']) ? '0-tidak' : $dataSydney['is_mobilitas_mandiri'];
        $model->skor_mobilitas_mandiri = empty($dataSydney['skor_mobilitas_mandiri']) ? 0 : $dataSydney['skor_mobilitas_mandiri'];
        $model->is_mobilitas_bantuan = empty($dataSydney['is_mobilitas_bantuan']) ? '0-tidak' : $dataSydney['is_mobilitas_bantuan'];
        $model->skor_mobilitas_bantuan = empty($dataSydney['skor_mobilitas_bantuan']) ? 0 : $dataSydney['skor_mobilitas_bantuan'];
        $model->is_kursi_roda = empty($dataSydney['is_kursi_roda']) ? '0-tidak' : $dataSydney['is_kursi_roda'];
        $model->skor_kursi_roda = empty($dataSydney['skor_kursi_roda']) ? 0 : $dataSydney['skor_kursi_roda'];
        $model->is_imobilisasi = empty($dataSydney['is_imobilisasi']) ? '0-tidak' : $dataSydney['is_imobilisasi'];
        $model->skor_imobilisasi = empty($dataSydney['skor_imobilisasi']) ? 0 : $dataSydney['skor_imobilisasi'];

        $model->pendaftaran_id = $pendaftaran_id;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Asesmen Keperawatan Berhasil!');
    }

    /**
     * function for handle show sydney data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetSydney()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }
        $model = AsesmenResikoJatuhSydneyRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['created_date' => SORT_DESC])
            ->asArray()
            ->one();
        return $this->responseJson(200, 'Data Sydney Berhasil didapatkan', $model);
    }

    /**
     * function for save sydney
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveDumpty()
    {
        $request     = Yii::$app->request;
        $dataDumpty  = $request->post('formdata', []);

        $pendaftaran_id = !isset($dataDumpty['pendaftaran_id']) || empty($dataDumpty['pendaftaran_id']) ? null : $dataDumpty['pendaftaran_id'];

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        $model = new AsesmenResikoJatuhDumptyRD;
        $model->attributes     = $dataDumpty;
        $model->pendaftaran_id = $pendaftaran_id;
        $model->tanggal        = date('Y-m-d');;
        $model->jam            = date('H:i');;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Humpty Dumpty Berhasil!');
    }

    /**
     * function for handle show dumpty data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetDumpty()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }
        $model = AsesmenResikoJatuhDumptyRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC])
            ->asArray()
            ->one();
        return $this->responseJson(200, 'Data Dumpty Berhasil didapatkan', $model);
    }

    /**
     * function for handle show history dumpty data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetHistoryDumpty()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }

        $getGroupHistory =  AsesmenResikoJatuhDumptyRD::find()
            ->select(['count(asesmenrdresikojatuhdumpty_id)', 'tanggal'])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->groupBy('tanggal')
            ->orderBy(['tanggal' => SORT_DESC])
            ->asArray()
            ->all();

        $getHistory = AsesmenResikoJatuhDumptyRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC, 'asesmenrdresikojatuhdumpty_id' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'history' => $getHistory,
            'group'   => $getGroupHistory,
        ];
    }

    public function actionResikoJatuhList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = ResikoJatuh::find()
            ->select([
                'resikojatuh_id as id',
                'resikojatuh_nama as text'
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'ilike',
                'resikojatuh_nama',
                $term
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    /**
     * function for save sydney
     * 
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveMorse()
    {
        $request     = Yii::$app->request;
        $dataMorse  = $request->post('formdata', []);

        $pendaftaran_id = !isset($dataMorse['pendaftaran_id']) || empty($dataMorse['pendaftaran_id']) ? null : $dataMorse['pendaftaran_id'];

        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        $model = new AsesmenResikoJatuhMorseRD;
        $model->attributes     = $dataMorse;
        $model->pendaftaran_id = $pendaftaran_id;
        $model->tanggal        = date('Y-m-d');;
        $model->jam            = date('H:i');;
        if (!$model->save()) {
            $transaction->rollBack();
            Yii::error([
                'error-data' => $model->getErrors()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Skala Morse Berhasil!');
    }

    /**
     * function for handle show morse data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetMorse()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }
        $model = AsesmenResikoJatuhMorseRD::find()
            ->select([
                'resikojatuh_m.resikojatuh_nama',
                'asesmenrdresikojatuhmorse_t.*'
            ])
            ->leftJoin('resikojatuh_m', 'resikojatuh_m.resikojatuh_id = asesmenrdresikojatuhmorse_t.resikojatuh_id')
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC])
            ->asArray()
            ->one();
        return $this->responseJson(200, 'Data Morse Berhasil didapatkan', $model);
    }

    /**
     * function for handle show history morse data
     * 
     * @param String var
     * @return JSON
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetHistoryMorse()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
        }

        $getGroupHistory =  AsesmenResikoJatuhMorseRD::find()
            ->select(['count(asesmenrdresikojatuhmorse_id)', 'tanggal'])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->groupBy('tanggal')
            ->orderBy(['tanggal' => SORT_DESC])
            ->asArray()
            ->all();

        $getHistory = AsesmenResikoJatuhMorseRD::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tanggal' => SORT_DESC, 'jam' => SORT_DESC, 'asesmenrdresikojatuhmorse_id' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'history' => $getHistory,
            'group'   => $getGroupHistory,
        ];
    }

    /**
     * @controller actionCetakAskepRd
     * @attribute #no_pendaftaran# => Untuk Menampilkan No Pendaftaran
     * @attribute #no_rm# => Untuk Menampilkan No Rekam Medik
     * @attribute #nama_pasien# => Untuk Menampilkan nama pasien
     * @attribute #jenis_kelamin# => Untuk Menampilkan nama pasien
     * @attribute #tanggal_lahir# => Untuk Menampilkan Jenis Kelamin
     * @attribute #cara_bayar# => Untuk Menampilkan cara Bayar
     * @attribute #penjamin# => Untuk Menampilkan Penjamin
     * @attribute #dokter_pemeriksa# => Untuk Menampilkan Dokter pemeriksa
     * @attribute #perawat# => Untuk Menampilkan Nama Perawat
     * @attribute #tanggal_periksa# => Untuk Menampilkan Tanggal periksa
     * @attribute #keadaan_umum# => Untuk Menampilkan Keadaan Umum
     * @attribute #tekanan_darah# => Untuk Menampilkan Tekanan Darah
     * @attribute #klasifikasitekanandarah# => Untuk Menampilkan Klasifikasi Tekanan Darah
     * @attribute #detak_nadi# => Untuk Menampilkan Detak Nadi
     * @attribute #denyut_jantung# => Untuk Menampilkan Denyut Jantung
     * @attribute #pernafasan# => Untuk Menampilkan Pernafasan
     * @attribute #suhu_tubuh# => Untuk Menampilkan Suhu Tubuh
     * @attribute #tinggi_badan# => Untuk Menampilkan Tinggi Badan
     * @attribute #berat_badan# => Untuk Menampilkan Berat Badan
     * @attribute #massa_index_tubuh# => Untuk Menampilkan massa index tubuh
     * @attribute #kelainan_tubuh# => Untuk Menampilkan Kelainan pada bagian tubuh
     * @attribute #perkusi# => Untuk Menampilkan Perkusi
     * @attribute #auskultasi# => Untuk Menampilkan Auskultasi
     * @attribute #gcs_eye# => Untuk Menampilkan GCS Eye
     * @attribute #metodegcs_eye# => Untuk Menampilkan metode GCS Eye
     * @attribute #nilaigcs_eye# => Untuk Menampilkan nilai GCS Eye
     * @attribute #gcs_verbal# => Untuk Menampilkan GCS Verbal
     * @attribute #metodegcs_verbal# => Untuk Menampilkan metode GCS Verbal
     * @attribute #nilaigcs_verbal# => Untuk Menampilkan nilai GCS Verbal
     * @attribute #gcs_motorik# => Untuk Menampilkan GCS Motorik
     * @attribute #nilaigcs_motorik# => Untuk Menampilkan nilai GCS Motorik
     * @attribute #gcs_is_kapitis# => Untuk Menampilkan is kapitis
     * @attribute #gcs_kategori# => Untuk Menampilkan gsc nama
     * @attribute #hasil_metode_gcs# => Untuk Menampilkan Hasil Metode GCS
     * @attribute #pernapasan_gerakan# => Untuk Menampilkan List PERNAPASAN GERAKAN DADA
     * @attribute #jalan_nafas# => Untuk Menampilkan List JALAN NAFAS DAN PERNAFASAN
     * @attribute #sirkulasi# => Untuk Menampilkan List SIRKULASI
     * @attribute #gambar_anatomi# => Untuk Menampilkan Gambar Anatomi Tubuh
     * @attribute #list_tabel_anatomi# => Untuk Menampilkan List bagian anatomi tubuh
     * @attribute #tanggal_pemeriksaan# => Untuk Menampilkan Tanggal Pemeriksaan
     * @attribute #tgl_cetak# => Untuk Menampilkan Tanggal saat ini dicetak
     * @attribute #imt_kategori# => Untuk Menampilkan imt kategori / bmi_definisi
     * @attribute #umur# => Untuk Menampilkan umur Pasien
     * @attribute #kelaspelayanan_nama# => Untuk Menampilkan kelas pelayanan  Pasien
     * @attribute #tgl_pendaftaran# => Untuk Menampilkan tgl pendaftaran  Pasien
     * @attribute #jeniskasuspenyakit_nama# => Untuk Menampilkan Jenis Penyakit  Pasien
     * @attribute #status_periksa# => Untuk Menampilkan Status Periksa  Pasien
     **/
    public function actionCetakAskepRd($pendaftaran_id)
    {
        $configData = require_once(Yii::$app->basePath . '/config/files/asesmen_keperawatan_rd.php');
        $askepData = AsesmenPerawatRD::find()
            ->select([
                '*',
                'caramasuk.lookup_name as cara_datang',
                'agama_t.lookup_name as agama',
                'eye.metodegcs_nama as e_nama',
                'eye.metodegcs_nilai as e_nilai',
                'motorik.metodegcs_nama as m_nama',
                'motorik.metodegcs_nilai as m_nilai',
                'verbal.metodegcs_nama as v_nama',
                'verbal.metodegcs_nilai as v_nilai',
            ])
            ->leftJoin('pekerjaan_m', 'asesmenperawatrd_t.pekerjaan_id=pekerjaan_m.pekerjaan_id')
            ->leftJoin('pendidikan_m', 'asesmenperawatrd_t.pendidikan_id=pendidikan_m.pendidikan_id')
            ->leftJoin('suku_m', 'asesmenperawatrd_t.suku_id::int = suku_m.suku_id')
            ->leftJoin('lookup_m as caramasuk', 'asesmenperawatrd_t.caramasuk_id=caramasuk.lookup_id')
            ->leftJoin('lookup_m as agama_t', 'asesmenperawatrd_t.agama_id=agama_t.lookup_id')
            ->leftJoin('metodegcs_m as eye', 'asesmenperawatrd_t.gcseye_id=eye.metodegcs_id')
            ->leftJoin('metodegcs_m as motorik', 'asesmenperawatrd_t.gcsmotorik_id=motorik.metodegcs_id')
            ->leftJoin('metodegcs_m as verbal', 'asesmenperawatrd_t.gcsverbal_id=verbal.metodegcs_id')
            ->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        $result = InfoPasienRdV::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        $model = AsesmenPerawatRD::find()->where(compact('pendaftaran_id'))->one();
        if (isset($model['pegawaiverifikasigizi_id']) && !empty($model['pegawaiverifikasigizi_id'])) {
            $model['pegawaiverifikasigizi_nama'] = Pegawai::find()->select(['nama_pegawai'])->where(['pegawai_id' => $model['pegawaiverifikasigizi_id']])->scalar();
        }
        $modelResiko = AsesmenResikoJatuh::find()->where(['asesmenperawatrd_id' => $askepData['asesmenperawatrd_id']])->orderBy(['tgl_pengkajian' => SORT_DESC])->limit(4)->asArray()->all();
        $print = new DocoPrint();
        // Define variables
        $perasaan_klien = $dukungan_sosial = $hubungan_pasien = $keluarga_lain = $keadaan_emosi = $bahasa_dipakai = $media = $transportasi = $orang_merawat = $sarana_kesehatan = $tujuan_pulang = $capilary_refill = $perfusi = $akral = $pendarahan = $pupil = '';

        // Checkbox Anamnesa
        $allo_text = !empty($askepData['asesmen_allo_text']) ? $askepData['asesmen_allo_text'] : '';
        $asesmen_auto = $askepData['asesmen_auto'] == 1 ? '<input checked="checked" type="checkbox" /> Auto Anamnesa' : '<input type="checkbox" /> Auto Anamnesa';
        $asesmen_allo = $askepData['asesmen_allo'] == 1 ? '<input checked="checked" type="checkbox" /> Allo Anamnesa <br>' . $allo_text : '<input type="checkbox" /> Allo Anamnesa';
        $anamnesa = $asesmen_auto . '<br>' . $asesmen_allo;
        // Alergi
        if ($askepData['is_alergi']) {
            $alergi = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['is_alergiobat']) ? $alergi .= '<br>Obat: ' . $askepData['alergi_obat'] : '';
            isset($askepData['is_alergilainnya']) ? $alergi .= '<br>Lainnya: ' . $askepData['alergi_lainnya'] : '';
        } else {
            $alergi = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }

        // A-B-C
        // Airway
        $jalan_napas = '<table style="width: 100%;">';
        foreach ($configData['jalan_napas'] as $key => $value) {
            $jalan_napas .= '<tr>';
            $jalan_napas .= strpos($askepData['jalur_nafas'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
            if ($key == 'bersih_sumbatan') {
                foreach ($configData['jalan_napas_bersih'] as $key => $value) {
                    $jalan_napas .= strpos($askepData['jalan_nafas_bersin'], $key) !== false ? '<tr><td>&nbsp;&nbsp;&nbsp;<input checked="checked" type="checkbox" /> ' . $value . '</td></tr>' : '<tr><td>&nbsp;&nbsp;&nbsp;<input type="checkbox" /> ' . $value . '</td></tr>';
                }
            }
            ($key == 'oksigen' && isset($askepData['jalur_nafas_oksigen'])) ? $jalan_napas .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['jalur_nafas_oksigen'] . ' L/menit</td></tr>' : null;
            $jalan_napas .= '</tr>';
        }
        $jalan_napas .= '</table>';
        // Breathing
        $pernapasan = '<table style="width: 100%;">';
        foreach ($configData['pernapasan'] as $key => $value) {
            $pernapasan .= '<tr>';
            $pernapasan .= strpos($askepData['pernafasan'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
            ($key == 'spontan' && isset($askepData['pernafasan_spontan'])) ? $pernapasan .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['pernafasan_spontan'] . ' x/menit</td></tr>' : null;
            ($key == 'takipnea' && isset($askepData['pernafasan_takipnea'])) ? $pernapasan .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['pernafasan_takipnea'] . ' x/menit</td></tr>' : null;
            ($key == 'gargling' && isset($askepData['pernafasan_gargling'])) ? $pernapasan .= '<tr><td>&nbsp;&nbsp;&nbsp;' . $askepData['pernafasan_gargling'] . ' x/menit</td></tr>' : null;
            $pernapasan .= '</tr>';
        }
        $pernapasan .= '</table>';
        // Circulation
        // Checkbox Capilary Refill
        foreach ($configData['capilary_refill'] as $key => $value) {
            $capilary_refill .= strpos($askepData['capilary_refill'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Perfusi
        foreach ($configData['perfusi'] as $key => $value) {
            $perfusi .= strpos($askepData['perfusi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Akral
        foreach ($configData['akral'] as $key => $value) {
            $akral .= strpos($askepData['akral'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Pendarahan
        if ($askepData['pendarahan'] == 1) {
            $pendarahan = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['pendarahan_cc']) ? $pendarahan .= ' : ' . $askepData['pendarahan_cc'] : '';
        } else {
            $pendarahan = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }

        // EKG
        $jenis_resiko_jatuh = isset($askepData['jenis_resiko_jatuh']) ? 'Form ' . ucwords(str_replace('-', ' ', $askepData['jenis_resiko_jatuh'])) : null;
        $skrining_nyeri = isset($askepData['is_nyeri']) && $askepData['is_nyeri'] ? 'Ya' : 'Tidak';
        // Metode GCS
        $gcsEye = isset($askepData['gcseye_id']) ? $askepData['e_nama'] . ' - ' . $askepData['e_nilai'] : null;
        $gcsVerbal = isset($askepData['gcsverbal_id']) ? $askepData['v_nama'] . ' - ' . $askepData['v_nilai'] : null;
        $gcsMotorik = isset($askepData['gcsmotorik_id']) ? $askepData['m_nama'] . ' - ' . $askepData['m_nilai'] : null;

        // Pupil
        foreach ($configData['pupil'] as $key => $value) {
            $pupil .= strcmp($askepData['pupil'], $key) == 0 ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }

        // Reaksi Pupil
        $reaksi_pupil = '<table style="width: 100%">';
        $reaksi_pupil .= '<tr>';
        foreach ($configData['reaksi_pupil'] as $key => $value) {
            $reaksi_pupil .= strpos($askepData['reaksi_pupil'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
        }
        $reaksi_pupil .= '</tr>';
        $reaksi_pupil .= '<tr>';
        foreach ($configData['reaksi_pupil_lainnya'] as $key => $value) {
            $reaksi_pupil .= strpos($askepData['reaksi_pupil_lainnya'], $key) !== false ? '<td><input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<td><input type="checkbox" /> ' . $value . '</td>';
            $key == 'os' && isset($askepData['pupil_os']) ? $reaksi_pupil .= '<td>' . $askepData['pupil_os'] . '</td>' : null;
            $key == 'od' && isset($askepData['pupil_od']) ? $reaksi_pupil .= '<td>' . $askepData['pupil_od'] . '</td>' : null;
        }
        $reaksi_pupil .= '</tr></table>';

        // Data Psikososial
        // Checkbox Perasaan Klien
        foreach ($configData['perasaan_klien'] as $key => $value) {
            $perasaan_klien .= strpos($askepData['perasaan_klien'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Dukungan Sosial
        foreach ($configData['sosial_support'] as $key => $value) {
            $dukungan_sosial .= strpos($askepData['sosial_support'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Lainnya
        strpos($askepData['sosial_support'], '00') !== false ? $dukungan_sosial .= ': ' . ucwords(substr($askepData['sosial_support'], strpos($askepData['sosial_support'], '00') + 3)) : null;
        // Checkbox Hubungan Pasien
        $selected = explode(',', $askepData['hubungan_pasien']);
        foreach ($configData['hubungan_pasien'] as $key => $value) {
            $checked = in_array($key, $selected) ? ' checked="checked"' : '';
            $hubungan_pasien .= '<input type="checkbox"' . $checked . ' /> ' . $value . ' ';
        }
        // Checkbox Keluarga Lain
        foreach ($configData['keluarga_lain'] as $key => $value) {
            $keluarga_lain .= strpos($askepData['keluarga_lain'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($askepData['keluarga_lain'], '00') !== false ? $keluarga_lain .= ': ' . ucwords(substr($askepData['keluarga_lain'], strpos($askepData['keluarga_lain'], '00') + 3)) : null;
        // Checkbox Keadaan Emosi
        foreach ($configData['keadaan_emosi'] as $key => $value) {
            $keadaan_emosi .= strpos($askepData['keadaan_emosi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Kultural
        $kultural = isset($askepData['suku_id']) ? $askepData['suku_nama'] : null;

        // Dukungan Edukasi
        // Checkbox Bahasa
        foreach ($configData['kebutuhan_edukasi']['bahasa'] as $key => $value) {
            $bahasa_dipakai .= strpos($askepData['bahasa_dipakai'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        isset($askepData['bahasa_dipakai_lainnya']) ? $bahasa_dipakai .= ': ' . ucwords(str_replace(',', ', ', $askepData['bahasa_dipakai_lainnya'])) : null;
        // Dukungan Penerjemah
        $dukungan_penerjemah = isset($askepData['is_penerjemah']) && $askepData['is_penerjemah'] ? 'Ya' : 'Tidak';
        // Checkbox Media
        foreach ($configData['kebutuhan_edukasi']['media'] as $key => $value) {
            $media .= strpos($askepData['media'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        isset($askepData['media_lainnya']) ? $media .= ': ' . ucwords(str_replace(',', ', ', $askepData['media_lainnya'])) : null;

        // Hambatan Edukasi
        $iterHambatan = 0;
        $hambatan_edukasi = '<table style="width: 100%;">';
        $hambatan_edukasi .= '<tr>';
        foreach ($configData['kebutuhan_edukasi']['hambatan'] as $key => $value) {
            $hambatan_edukasi .= $iterHambatan != 6 ? '<td>' : '<tr><td>';
            $hambatan_edukasi .= strpos($askepData['identifikasi_hambatan'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<input type="checkbox" /> ' . $value . '</td>';
            $iterHambatan++;
        }
        $hambatan_edukasi .= '</tr></table>';

        // Kebutuhan Edukasi
        $sistem_rujukan = isset($askepData['is_sistem_rujukan']) && $askepData['is_sistem_rujukan'] ? 'Ya' : 'Tidak';
        $kesediaan_pasien = isset($askepData['is_ketersediaan_pasien']) && $askepData['is_ketersediaan_pasien'] ? 'Ya' : 'Tidak';
        $kemampuan_membaca = isset($askepData['is_kemampuan_membaca']) && $askepData['is_kemampuan_membaca'] ? 'Mampu' : 'Tidak Mampu';
        if ($askepData['is_dibutuhkan_penerjemah']) {
            $kebutuhan_penerjemah = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['penerjemah_bahasa']) ? $kebutuhan_penerjemah .= '<br>' . ucwords($askepData['penerjemah_bahasa']) : '';
        } else {
            $kebutuhan_penerjemah = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }
        if ($askepData['is_hambatan_emotional']) {
            $hambatan_emotional = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            if (isset($askepData['hambatan_emotional_lainnya'])) {
                $he_pendidikan = strpos($askepData['hambatan_emotional_lainnya'], 'pendidikan') !== false ? '<input checked="checked" type="checkbox" /> Tingkat Pendidikan' : '<input type="checkbox" /> Tingkat Pendidikan';
                $he_bahasa = strpos($askepData['hambatan_emotional_lainnya'], 'bahasa') !== false ? '<input checked="checked" type="checkbox" /> Bahasa' : '<input type="checkbox" /> Bahasa';
                $he_fisik =
                    strpos($askepData['hambatan_emotional_lainnya'], 'fisi') !== false ? '<input checked="checked" type="checkbox" /> Keterbatasan Fisik' : '<input type="checkbox" /> Keterbatasan Fisik';
                $hambatan_emotional .= '<br>&emsp;' . $he_pendidikan . '<br>&emsp;' . $he_bahasa . '<br>&emsp;' . $he_fisik;
            }
        } else {
            $hambatan_emotional = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }
        if ($askepData['is_keterbatasan_fisik']) {
            $keterbatasan_fisik = '<input type="checkbox" /> Tidak <br> <input checked="checked" type="checkbox" /> Ya';
            isset($askepData['keterbatasan_fisik']) ? $keterbatasan_fisik .= '<br>' . ucwords($askepData['keterbatasan_fisik']) : '';
        } else {
            $keterbatasan_fisik = '<input checked="checked" type="checkbox" /> Tidak <br> <input type="checkbox" /> Ya';
        }

        // Diagnosa Keperawatan
        $diagnosa_keperawatan = '<ul>';
        if (isset($askepData['diagnosa_keperawatan']) && !empty($askepData['diagnosa_keperawatan'])) {
            $diagKeperawatan = json_decode($askepData['diagnosa_keperawatan'], true);
            foreach ($diagKeperawatan as $keydetail => $val) {
                if (isset($val['text'])) {
                    $diagnosa_keperawatan .= '<li>';
                    $diagnosa_keperawatan .= isset($val['text']) ? (isset($val['kode']) ? $val['kode'] . ' - ' . $val['text'] : $val['text']) : '-';
                    $diagnosa_keperawatan .= '</li>';
                }
            }
        }
        $diagnosa_keperawatan .= '</ul>';

        // Masuk Ke
        $iterMasukKe = 0;
        $masuk_ke = '<table style="width: 100%;">';
        $masuk_ke .= '<tr>';
        foreach ($configData['masuk_ke'] as $key => $value) {
            $masuk_ke .= $iterMasukKe != 5 ? '<td>' : '<tr><td>';
            $masuk_ke .= strpos($askepData['masuk_ke'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . '</td>' : '<input type="checkbox" /> ' . $value . '</td>';
            $iterMasukKe++;
        }
        $masuk_ke .= '</tr></table>';

        // Rencana Pemulangan
        // Checkbox Tujuan Pulang
        foreach ($configData['pemulangan']['tujuan_pulang'] as $key => $value) {
            $tujuan_pulang .= strpos($askepData['tujuan_pulang'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        isset($askepData['tujuan_pulang_lainnya']) ? $tujuan_pulang .= ': ' . ucwords(str_replace(',', ', ', $askepData['tujuan_pulang_lainnya'])) : null;
        // Checkbox Transportasi
        foreach ($configData['pemulangan']['transportasi'] as $key => $value) {
            $transportasi .= strpos($askepData['transportasi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Orang Merawat
        foreach ($configData['pemulangan']['orang_merawat'] as $key => $value) {
            $orang_merawat .= strpos($askepData['orang_merawat'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // Checkbox Sarana Kesehatan
        foreach ($configData['pemulangan']['sarana_kesehatan'] as $key => $value) {
            $sarana_kesehatan .= strpos($askepData['sarana_kesehatan'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }

        $profileRs = $this->getProfileRs();

        // Assign attributes
        $print->attributes = [
            // Asesmen Keperawatan
            '#rs_name#' => ArrayHelper::getValue($profileRs, 'namaRs', '-'),
            '#alamat#' => ArrayHelper::getValue($profileRs, 'alamat', '-'),
            '#kota#' => ArrayHelper::getValue($profileRs, 'kota', '-'),
            '#no_telp#' => ArrayHelper::getValue($profileRs, 'nomor_tlp', '-'),
            '#jenis_keperawatan#' => 'Gawat Darurat',
            '#tgl_datang#' => isset($askepData['tgl_datang']) ? date('d/m/Y H:i:s', strtotime($askepData['tgl_datang'])) : null,
            '#tgl_keluar#' => isset($askepData['tgl_keluar']) ? date('d/m/Y H:i:s', strtotime($askepData['tgl_keluar'])) : null,

            // Identitas Pasien
            '#tinggi_badan#' => isset($askepData['tinggi_badan']) ? $askepData['tinggi_badan'] : null,
            '#berat_badan#' => isset($askepData['berat_badan']) ? $askepData['berat_badan'] : null,
            '#bb_ideal#' => isset($askepData['bb_ideal']) ? $askepData['bb_ideal'] : null,
            '#imt#' => isset($askepData['imt']) ? $askepData['imt'] : null,
            '#ket_imt#' => isset($askepData['ket_imt']) ? $askepData['ket_imt'] : null,
            '#agama#' => isset($askepData['agama']) ? $askepData['agama'] : null,
            '#pendidikan#' => isset($askepData['pendidikan_nama']) ? $askepData['pendidikan_nama'] : null,
            '#pekerjaan#' => isset($askepData['pekerjaan_nama']) ? $askepData['pekerjaan_nama'] : null,
            '#cara_datang#' => isset($askepData['cara_datang']) ? $askepData['cara_datang'] : null,

            // Alasan Masuk IGD
            '#anamnesa#' => isset($anamnesa) ? $anamnesa : ' - ',
            '#keluhan#' => isset($askepData['keluhan']) ? $askepData['keluhan'] : null,
            '#r_penyakitsaatini#' => isset($askepData['r_penyakitsaatini']) ? $askepData['r_penyakitsaatini'] : null,
            '#r_penyakitdahulu#' => isset($askepData['r_penyakitdahulu']) ? $askepData['r_penyakitdahulu'] : null,
            '#r_pengobatan#' => isset($askepData['r_pengobatan']) ? $askepData['r_pengobatan'] : null,
            '#alergi#' => isset($alergi) ? $alergi : null,

            // A-B-C
            '#jalan_napas#' => isset($jalan_napas) ? $jalan_napas : null,
            '#pernapasan#' => isset($pernapasan) ? $pernapasan : null,
            '#tensi#' => isset($askepData['tensi']) ? $askepData['tensi'] : null,
            '#nadi#' => isset($askepData['detak_nadi']) ? $askepData['detak_nadi'] : null,
            '#hasil_nadi#' => isset($askepData['hasil_nadi']) ? $askepData['hasil_nadi'] : null,
            '#capilary_refill#' => isset($capilary_refill) ? $capilary_refill : null,
            '#perfusi#' => isset($perfusi) ? $perfusi : null,
            '#pendarahan#' => isset($pendarahan) ? $pendarahan : null,
            '#akral#' => isset($akral) ? $akral : null,
            '#suhu#' => isset($askepData['suhu_tubuh']) ? $askepData['suhu_tubuh'] : null,

            // Kategori Triase
            '#kategori_sehari#' => isset($askepData['kategori_triase_sehari']) ? ucwords($askepData['kategori_triase_sehari']) : null,
            '#kategori_disaster#' => isset($askepData['kategori_triase_disaster']) ? ucwords($askepData['kategori_triase_disaster']) : null,

            // GCS
            '#gcs_eye#' => isset($gcsEye) ? $gcsEye : null,
            '#gcs_verbal#' => isset($gcsVerbal) ? $gcsVerbal : null,
            '#gcs_motorik#' => isset($gcsMotorik) ? $gcsMotorik : null,
            '#hasil_metode_gcs#' => isset($askepData['hasil_gcs']) ? $askepData['hasil_gcs'] : null,

            // Pupil
            '#pupil#' => isset($pupil) ? $pupil : null,
            '#reaksi_pupil#' => isset($reaksi_pupil) ? $reaksi_pupil : null,

            // EKG
            '#kepala#' => isset($askepData['kepala']) ? ucwords($askepData['kepala']) : null,
            '#abdomen#' => isset($askepData['abdomen']) ? ucwords($askepData['abdomen']) : null,
            '#maksilofacial#' => isset($askepData['maksilofacial']) ? ucwords($askepData['maksilofacial']) : null,
            '#parineum#' => isset($askepData['parineum']) ? ucwords($askepData['parineum']) : null,
            '#tulang_leher#' => isset($askepData['tulan_leher']) ? ucwords($askepData['tulan_leher']) : null,
            '#muskuloskeletal#' => isset($askepData['muskuloskeletal']) ? ucwords($askepData['muskuloskeletal']) : null,
            '#paru_paru#' => isset($askepData['paru_paru']) ? ucwords($askepData['paru_paru']) : null,
            '#extremitas#' => isset($askepData['extremitas']) ? ucwords($askepData['extremitas']) : null,
            // Skrining Nyeri
            '#skrining_nyeri#' => isset($skrining_nyeri) ? $skrining_nyeri : null,
            '#skala_nyeri#' => isset($askepData['is_nyeri']) && $askepData['is_nyeri'] ? $this->renderPartial('_skala_nyeri', compact('model', 'modelResiko', 'configData')) : null,
            // Resiko Jatuh
            '#risiko_jatuh#' => isset($askepData['hasil_resiko_jatuh']) ? $askepData['hasil_resiko_jatuh'] : null,
            '#jenis_risiko_jatuh#' => isset($jenis_resiko_jatuh) ? $jenis_resiko_jatuh : null,
            '#risiko_decubitus#' => isset($askepData['nilai_decubitus']) ? ucwords($askepData['nilai_decubitus']) : null,
            '#luka_bakar#' => isset($askepData['nilai_luka_bakar']) ? ucwords($askepData['nilai_luka_bakar']) : null,

            // Skrining Gizi
            '#skrining_gizi#' => $this->renderPartial('_skrining_gizi', compact('model', 'configData')),

            // Data Psikososial
            '#perasaan_klien#' => isset($perasaan_klien) ? $perasaan_klien : null,
            '#dukungan_sosial#' => isset($dukungan_sosial) ? $dukungan_sosial : null,
            '#hubungan_pasien#' => isset($hubungan_pasien) ? $hubungan_pasien : null,
            '#keluarga_lain#' => isset($keluarga_lain) ? $keluarga_lain : null,
            '#keadaan_emosi#' => isset($keadaan_emosi) ? $keadaan_emosi : null,
            '#kultural#' => isset($kultural) ? $kultural : null,

            // Identifikasi Kebutuhan Edukasi
            // Dukungan Edukasi
            '#bahasa_dipakai#' => isset($bahasa_dipakai) ? $bahasa_dipakai : null,
            '#dukungan_penerjemah#' => isset($dukungan_penerjemah) ? $dukungan_penerjemah : null,
            '#media#' => isset($media) ? $media : null,
            // Hambatan Edukasi
            '#hambatan_edukasi#' => isset($hambatan_edukasi) ? $hambatan_edukasi : null,
            // Kebutuhan Edukasi
            '#sistem_rujukan#' => isset($sistem_rujukan) ? $sistem_rujukan : null,
            '#kebutuhan_penerjemah#' => isset($kebutuhan_penerjemah) ? $kebutuhan_penerjemah : null,
            '#materi#' => isset($askepData['materi']) ? $askepData['materi'] : null,
            '#edukator#' => isset($askepData['edukator']) ? $askepData['edukator'] : null,
            '#hambatan_emotional#' => isset($hambatan_emotional) ? $hambatan_emotional : null,
            '#kesediaan_pasien#' => isset($kesediaan_pasien) ? $kesediaan_pasien : null,
            '#kemampuan_membaca#' => isset($kemampuan_membaca) ? $kemampuan_membaca : null,
            '#keterbatasan_fisik#' => isset($keterbatasan_fisik) ? $keterbatasan_fisik : null,
            '#bahasa#' => isset($askepData['bahasa']) ? $askepData['bahasa'] : null,

            // Diagnosa Keperawatan
            '#diagnosa_keperawatan#' => isset($diagnosa_keperawatan) ? $diagnosa_keperawatan : null,

            // Masuk Ke
            '#masuk_ke#' => isset($masuk_ke) ? $masuk_ke : null,

            // Rencana Pemulangan
            '#tujuan_pulang#' => isset($tujuan_pulang) ? $tujuan_pulang : null,
            '#transportasi#' => isset($transportasi) ? $transportasi : null,
            '#orang_merawat#' => isset($orang_merawat) ? $orang_merawat : null,
            '#sarana_kesehatan#' => isset($sarana_kesehatan) ? $sarana_kesehatan : null,

            '#status_periksa#' => isset($result['status_periksa']) ? $result['status_periksa'] : null,
            '#jeniskasuspenyakit_nama#' => isset($result['jeniskasuspenyakit_nama']) ? $result['jeniskasuspenyakit_nama'] : null,
            '#umur#' => isset($result['umur']) ? $result['umur'] : null,
            '#kelaspelayanan_nama#' => isset($result['kelaspelayanan_nama']) ? $result['kelaspelayanan_nama'] : null,
            '#tgl_pendaftaran#' => isset($result['tgl_pendaftaran']) ? date('d/m/Y H:i:s', strtotime($result['tgl_pendaftaran'])) : null,
            '#poliklinik#' => isset($result['ruangan_nama']) ? $result['ruangan_nama'] : null,
            '#no_pendaftaran#' => isset($result['no_pendaftaran']) ? $result['no_pendaftaran'] : null,
            '#no_rm#' => isset($result['no_rekam_medik']) ? $result['no_rekam_medik'] : null,
            '#nama_pasien#' => isset($result['nama_pasien']) ? $result['nama_pasien'] : null,
            '#jenis_kelamin#' => isset($result['jenis_kelamin']) ? $result['jenis_kelamin'] : null,
            '#tanggal_lahir#' => isset($result['tanggal_lahir']) ? date('d/m/Y  ', strtotime($result['tanggal_lahir'])) : null,
            '#cara_bayar#' => isset($result['carabayar_nama']) ? $result['carabayar_nama'] : null,
            '#penjamin#' => isset($result['penjamin_nama']) ? $result['penjamin_nama'] : null,
            '#dokter_pemeriksa#' => isset($result['dokter_jaga']) ? $result['dokter_jaga'] : null,
            '#tanggal_periksa#' => isset($result['tglperiksafisik']) ? date('d F Y', strtotime($result['tglperiksafisik'])) : null,
            '#tgl_cetak#' => date('d F Y'),
        ];
        // Print output
        $print->Output();
    }

    private function getProfileRs()
    {
        $profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });

        $kota = $namaRs = '';
        $alamat = isset($profilRs['alamatlokasi_rumahsakit']) ? $profilRs['alamatlokasi_rumahsakit'] : '';
        $nomor_tlp = isset($profilRs['no_telp_profilrs']) ? $profilRs['no_telp_profilrs'] : '';

        if (!empty($profilRs['nama_rumahsakit'])) {
          $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
          if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
              $pattern = "KOTA ADM. ";
          }
          elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
              $pattern = "KAB. ADM. ";
          }
          elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
              $pattern = "KAB. ";
          }
          elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
              $pattern = "KOTA ";
          }

          $kota = str_replace($pattern,"", $profilRs['kota']);
        }
          
        return [
          'namaRs' => $namaRs,
          'kota' => $kota,
          'alamat' => $alamat,
          'nomor_tlp' => $nomor_tlp,
        ];
    }
}
