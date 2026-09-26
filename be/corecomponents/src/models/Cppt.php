<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-05 11:42:14
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-08-13 13:37:40
 */

namespace Doco\models;

use Yii;
use Doco\components\DocoConstants;

/**
 * This is the model class for table "cppt_t".
 *
 * @property int $cppt_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $ruangan_id
 * @property string $kamar_tempattidur formatnya : [no_kamar]-[no_tempattidur]
 * @property string $tgl_cppt
 * @property string $subject
 * @property string $object
 * @property int $a_diag_utama
 * @property string $a_diag_penyerta
 * @property string $planning
 * @property bool $is_instruksi_pulang
 * @property int $pegawai_id
 * @property string $catatan_dokter
 * @property string $catatan_perawat
 * @property string $instruksi
 * @property int $pemberi_instruksi_id
 * @property bool $is_verifikasi
 * @property int $pegawai_verifikasi_id
 * @property string $tgl_verifikasi
 * @property int $kamarruangan_id
 * @property int $kamartempattidur_id
 */
class Cppt extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'cppt_t';
	}

    public function scenarios()
    {
        return [
            'soap' => ['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama'],
            'auto' => ['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama','is_active']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama'], 'required','on' => 'soap'],
            [['pasien_id', 'ruangan_id', 'pegawai_id', 'ruangan_id', 'instruksi', 'pemberi_instruksi_id'], 'required','on' => 'verbalorder'],	
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'a_diag_utama', 'pegawai_id', 'pemberi_instruksi_id', 'pegawai_verifikasi_id', 'kamarruangan_id', 'kamartempattidur_id'], 'default', 'value' => null],
            [['cppt_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'pegawai_id', 'pemberi_instruksi_id', 'pegawai_verifikasi_id', 'kamarruangan_id', 'kamartempattidur_id'], 'integer'],
            [['tgl_cppt', 'tgl_verifikasi', 'referred_id'], 'safe'],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama','is_active'],'safe','on'=>'auto'],
            [['subject', 'object', 'planning', 'catatan_dokter', 'catatan_perawat', 'instruksi'], 'string'],
            [['is_instruksi_pulang', 'is_verifikasi'], 'boolean'],
            [['kamar_tempattidur'], 'string', 'max' => 255],
            [['cppt_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cppt_id' => 'Cppt ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'ruangan_id' => 'Ruangan ID',
            'kamar_tempattidur' => 'Kamar Tempattidur',
            'tgl_cppt' => 'Tgl Cppt',
            'subject' => 'Subject',
            'object' => 'Object',
            'a_diag_utama' => 'A Diag Utama',
            'a_diag_penyerta' => 'A Diag Penyerta',
            'planning' => 'Planning',
            'is_instruksi_pulang' => 'Is Instruksi Pulang',
            'pegawai_id' => 'Pegawai ID',
            'catatan_dokter' => 'Catatan Dokter',
            'catatan_perawat' => 'Catatan Perawat',
            'instruksi' => 'Instruksi',
            'pemberi_instruksi_id' => 'Pemberi Instruksi ID',
            'is_verifikasi' => 'Is Verifikasi',
            'pegawai_verifikasi_id' => 'Pegawai Verifikasi ID',
            'tgl_verifikasi' => 'Tgl Verifikasi',
            'kamarruangan_id' => 'Kamarruangan ID',
            'kamartempattidur_id' => 'Kamar tempat tidur ID',
        ];
    }

	/**
     * Function for integrate visite dokter to billing
     * Array $payload
     * Required params:
     * - pendaftaran_id
     * - pasienadmisi_id
     * - pegawai_id
     * - tgl_cppt
     * 
     * Conditions:
     * 1. pegawai input cppt adalah dokter
     * 2. belum ada tindakan Visit Dokter Spesialis yg tercatat di billing pada tanggal cppt yg terinput
     * 3. data pasien tersedia di view infopasienri_v, pasien belum dipulangkan, pasien belum stop akomodasi
     * 
     * Usage:
     * - CpptController@soapCreateOrUpdate
     * - AllowController@actionCreateSoap
     */
    public function addVisiteDokter($payload)
    {
        $dataPasien = (new \app\modules\v1\models\InfoPasienRanap)->find()->select([
            'pasienadmisi_id',
            'no_pendaftaran',
            'kelaspelayanan_id',
            'penjamin_id',
            'is_stopakomodasi',
            'pasienpulang_id'
        ])
        ->andWhere([
            'pendaftaran_id' => $payload['pendaftaran_id'],
            'pasienadmisi_id' => $payload['pasienadmisi_id'],
        ])->asArray()->one();

        $checkDokter = Pegawai::find()->select([
            'pegawai_id', 'spesialis_id'
        ])->where([
            'pegawai_id' => $payload['pegawai_id']
        ])->asArray()->one();
        
        $visitDokterSlug = DocoConstants::VISIT_DOKTER_SLUG;
        if ( is_null($checkDokter['spesialis_id']) ) {
            $visitDokterSlug = DocoConstants::VISIT_DOKTER_JAGA_SLUG;
        }
        if(empty($dataPasien) || !is_null($dataPasien['pasienpulang_id']) || $dataPasien['is_stopakomodasi']){
            Yii::error([
                'msg' => 'maaf data pasien kosong, atau pasienpulang dah ada, atau is_stopakomodasi nya true',
                'data' => compact('dataPasien')
            ]);
            return true;
        }
        $visitDokterCache = (new \Doco\models\ConstantsId)->find()->select([
            'kode_id'
        ])->where([
            'kode_transaksi' => $visitDokterSlug
        ])->asArray()->one();
        
        $getTindakanPelayanan = \app\modules\v1\models\TindakanPelayanan::find()->andWhere([
            'daftartindakan_id' => $visitDokterCache['kode_id'],
            'pendaftaran_id' => $payload['pendaftaran_id'],
            'pasienadmisi_id' => $payload['pasienadmisi_id'],
            'dokterpenanggungjawab_id' => $payload['pegawai_id'],
        ])
        ->andWhere([
            'between', 'tgl_tindakan', date('Y-m-d 00:00:00', strtotime($payload['tgl_cppt'])), date('Y-m-d 23:59:59', strtotime($payload['tgl_cppt']))
        ])->asArray()->one();
        if( empty($getTindakanPelayanan) ){
            Yii::error([
                'msg' => 'nah kalo masuk sini, berarti tindakan pelayanan nya belom ada buat hari ini',
                'data' => compact('dataPasien')
            ]);
            $params['registrationData'] = $dataPasien;
            $params['registrationData']['tgl_transaksi'] = date('Y-m-d H:i:s', strtotime($payload['tgl_cppt']));
            (new \Doco\Services\KasirService)->tagihan($params['registrationData'], [
                [
                    'daftartindakan_id' => $visitDokterCache['kode_id'],
                    'qty' => 1,
                    'dokter_id' => (int) $payload['pegawai_id'],
                ]
            ]);
        }
        return true;
    }

    /**
     * Function getOrCreate
     * this function used for get last cppt_id depends on pendaftaran_id, pasienadmisi_id and pegawai_id, if the patient didn't have one, then this function will create new cppt
     * 
     * notes:
     * this function for now can only handle case for ipd, for ema and opd, hopefully u can update this function and remove this notes, tq
     * 
     * @param Array $payload['pendaftaran_id'] => patient pendaftaran_id
     * @param Array $payload['pasienadmisi_id'] => patient pasienadmisi_id, this params used if patient is from ipd / ranap
     * @param Array $payload['pegawai_id'] => patient pegawai_id
     * @return Array , ex:
     * {
     *      "cppt_id": 2678,
     *      "pendaftaran_id": 9541,
     *      "pasienadmisi_id": 1355,
     *      "pegawai_id": 1
     * }
     * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
     * A product of PT. Citraraya Nusatama
     * Powered by Sirs
     */
    public static function getOrCreate($payload = [])
    {
        $getCppt = Cppt::find()->select([
            'cppt_id',
            'pendaftaran_id',
            'pasienadmisi_id',
            'pegawai_id'
        ])->andWhere([
            'pendaftaran_id' => $payload['pendaftaran_id'],
            'pegawai_id' => $payload['pegawai_id']
        ]);

        if ( isset($payload['pasienadmisi_id']) ) {
            $getCppt->andWhere([
                'pasienadmisi_id' => $payload['pasienadmisi_id']
            ]);
        }

        if ( isset($payload['tgl_cppt']) ) {
            $tglStart = date('Y-m-d 00:00:00', strtotime($payload['tgl_cppt']));
            $tglEnd = date('Y-m-d 23:59:00', strtotime($payload['tgl_cppt']));
            $getCppt->andWhere([
                'BETWEEN', 'tgl_cppt', $tglStart, $tglEnd
            ]);
        }

        $getCppt = $getCppt->orderBy([
            'cppt_id' => SORT_DESC
        ])->asArray()->one();
        if ( !empty($getCppt) ) {
            return $getCppt;
        } else {
            return self::createCppt($payload);
        }
        
        return false;
    }

    /**
     * Function createCppt
     * this function used for create cppt
     * 
     * notes:
     * this function for now can only handle case for ipd. for ema and opd, hopefully u can update this function and remove this notes, tq
     * 
     * @param Array $payload['pendaftaran_id'] => patient pendaftaran_id
     * @param Array $payload['pasienadmisi_id'] => patient pasienadmisi_id, this params used if patient is from ipd / ranap
     * @param Array $payload['pegawai_id'] => patient pegawai_id
     * @param Array $payload['instalasi_id'] => patient instalasi_id, u can get the instalasi_id from jwt
     * @param Array $payload['ruangan_id'] => patient ruangan_id, u can get the ruangan_id from jwt
     * @return Array ex:
     * {
     *      "cppt_id": 2678,
     *      "pendaftaran_id": 9541,
     *      "pasienadmisi_id": 1355,
     *      "pegawai_id": 1
     * }
     * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
     * A product of PT. Citraraya Nusatama
     * Powered by Sirs
     */
    public static function createCppt($payload)
    {
        $strip = '-';
        $valDiag['text'] = $strip;
        $valDiagPenyerta = [];
        $valDiagPenyerta[] = $valDiag;
        $patientData = [];
        if ( $payload['instalasi_id'] == DocoConstants::INST_ID_RI ) {
            $patientData = (new \Doco\models\InfoKunjunganRiView)->find()->select([
                'pendaftaran_id',
                'pasienadmisi_id',
                'instalasi_id',
                'pasien_id',
                'ruangan_id',
                'kamarruangan_id',
                'kamartempattidur_id',
                'kamarruangan_nokamar',
                'no_tempattidur'
            ])->andWhere([
                'pendaftaran_id' => $payload['pendaftaran_id'],
                'pasienadmisi_id' => $payload['pasienadmisi_id'],
            ])->asArray()->one();
        }
        $model = new Cppt;
        $model->scenario = 'soap';
        $model->pendaftaran_id = $patientData['pendaftaran_id'];
        $model->pasienadmisi_id = $patientData['pasienadmisi_id'];
        $model->pegawai_id = $payload['pegawai_id'];
        $model->pasien_id = $patientData['pasien_id'];
        $model->ruangan_id = $patientData['ruangan_id'];
        $model->tgl_cppt = date('Y-m-d H:i:00', strtotime($payload['tgl_cppt']) );
        $model->subject = $strip;
        $model->object = $strip;
        $model->planning = $strip;
        $model->a_diag_utama = $valDiag;
        $model->a_diag_penyerta = $valDiagPenyerta;
        if ( $payload['instalasi_id'] == DocoConstants::INST_ID_RI ) {
            $model->kamarruangan_id = $patientData['kamarruangan_id'];
            $model->kamartempattidur_id = $patientData['kamartempattidur_id'];
            $model->kamar_tempattidur = $patientData['kamarruangan_nokamar'].' | '.$patientData['no_tempattidur'];
        }
        
        if ( !$model->save() ) {
            Yii::error([
                'model-error' => $model->errors
            ]);
            throw new \yii\db\Exception("Failed Save CPPT", 1);
        }

        return [
            'cppt_id' => $model->getPrimaryKey(),
            'pendaftaran_id' => $model->pendaftaran_id,
            'pasienadmisi_id' => $model->pasienadmisi_id,
            'pegawai_id' => $model->pegawai_id
        ];
    }
}
?>