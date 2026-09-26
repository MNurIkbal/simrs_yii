<?php 

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\models\AsesmenPerawatRDHistory;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\CaraMasuk;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Suku;
use app\modules\v1\models\AsesmenResikoJatuh;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\ResikoJatuh;
use app\modules\v1\models\Triase;

use app\modules\v1\models\AsesmenResikoJatuhSydneyRD;
use app\modules\v1\models\AsesmenResikoJatuhDumptyRD;
use app\modules\v1\models\AsesmenResikoJatuhMorseRD;

use app\modules\v1\models\Pegawai;
use yii\db\Expression;

class HistoryAssesmenKeperawatanController extends DocoActiveController
{
	
	public $modelClass = 'app\modules\v1\models\AsesmenPerawatRDHistory';

	public function init()
    {
        parent::init();
    }

    // Verbs
    public function verbs()
    {
        // Parent
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    // Acions
    public function actions()
    {
        // Parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);

        // Return
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $offset = $request->get('start');
            $limit = $request->get('length');

            $rows = (new \yii\db\Query())
                ->select([
                    new Expression('COALESCE("lastModifiedBy".nama_pegawai, "createdBy".nama_pegawai) as nama_pegawai'),
                    'a.*' 
                ])
                ->from('asesmenperawatrd_r a')
                ->join('LEFT JOIN', 'loginpemakai_k createdLogin', '"createdLogin".loginpemakai_id = a.created_by')
                ->join('LEFT JOIN', 'loginpemakai_k modifiedLogin', '"modifiedLogin".loginpemakai_id = a.last_modified_by')
                ->join('LEFT JOIN', 'pegawai_m createdBy', '"createdBy".pegawai_id = "createdLogin".pegawai_id')
                ->join('LEFT JOIN', 'pegawai_m lastModifiedBy', '"lastModifiedBy".pegawai_id = "modifiedLogin".pegawai_id')
                ->where(['pendaftaran_id' => $pendaftaran_id]);
            
            $rows->orderBy(['a.id' => SORT_DESC, 'a.tgl_asesmen' => SORT_DESC]);

            return new ActiveDataProvider([
                'query' => $rows,
                'pagination' => [
                    'page' => ($offset / $limit) ,
                    'pageSize' => $limit,
                ]
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
	}

    public function actionDetailAsesmen()
    {
        $id = Yii::$app->request->get('id', null);
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(404, 'Data Tidak Ditemukan');
        }
        $getAsesmenKeperawatan = AsesmenPerawatRDHistory::find()->where(['id' => $id])->asArray()->one();
        $getAsesmenDetail = null;
        if (!is_null($getAsesmenKeperawatan)) {
            $getAsesmenDetail = AsesmenResikoJatuh::find()
                ->where(['asesmenperawatrd_id' => $getAsesmenKeperawatan['asesmenperawatrd_id']])
                ->andWhere(['<=','tgl_pengkajian', $getAsesmenKeperawatan['tgl_asesmen']])
                ->orderBy([
                    'asesmenrdresikojatuh_id' => SORT_DESC
                ])
                ->limit(3)->asArray()->all();
            if (empty($getAsesmenDetail)) {
                $getAsesmenDetail = AsesmenResikoJatuh::find()
                ->where(['asesmenperawatrd_id' => $getAsesmenKeperawatan['asesmenperawatrd_id']])
                ->orderBy([
                    'asesmenrdresikojatuh_id' => SORT_ASC
                ])
                ->limit(1)->asArray()->all();
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
                ->join('JOIN','carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
                ->join('LEFT JOIN','triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where([
                    'pendaftaran_t.pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            foreach ($getSuggestData as $key => $value) {
                if(empty($getAsesmenKeperawatan[$key])){
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
                ->join('JOIN','carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
                ->join('LEFT JOIN','triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where([
                    'pendaftaran_t.pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            $getAsesmenKeperawatan['kategori_triase_disaster'] = null;
            if( $getAsesmenKeperawatan['pernafasan'] == 'henti_napas-resusitasi' && $getAsesmenKeperawatan['sirkulasi'] == 'henti_jantung-resusitasi') {
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
            $metode['text'] = $metode['text']." - ".$metode['metodegcs_nilai'];
            $metodeGcs[$metode['metodegcs_singkatan']][] = $metode;
        }

        $jenisResiko = $this->getJenisResiko($pendaftaran_id);

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

        $getCaraMasuk = $this->getLookupByType('transportasi')
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
            'jenisResiko' => $jenisResiko
        ];
    }

    public function getJenisResiko($pendaftaran_id) {
        $jenis = "";
        $sydneyRD = AsesmenResikoJatuhSydneyRD::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->all();
        $dumptyRD = AsesmenResikoJatuhDumptyRD::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->all();
        $morseRD = AsesmenResikoJatuhMorseRD::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->all();

        $sydneyRDCount = !empty($sydneyRD) ? count($sydneyRD) : 0;
        $dumptyRDCount = !empty($dumptyRD) ? count($dumptyRD) : 0;
        $morseRDCount = !empty($morseRD) ? count($morseRD) : 0;
 
        if ($sydneyRDCount > 0 && $dumptyRDCount > 0 && $morseRDCount > 0) {
            $jenis = 'morse';
        }
        else if ($sydneyRDCount > 0 && $dumptyRDCount > 0) {
            $jenis = 'humpty-dumpty';
        }
        else if ($sydneyRDCount > 0) {
            $jenis = 'sydney';
        }
        return $jenis;
    }
}