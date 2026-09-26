<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "vitalsign_t".
 *
 * @property int $vitalsign_id
 * @property int $sumberttv_id
 * @property string $sumberttv
 * @property int $jenisttv_id
 * @property string $jenisttv
 * @property int $tingkatkesadaran_id
 * @property string $tingkatkesadaran
 * @property float $sistol
 * @property float $diastol
 * @property float $nadi
 * @property float $respirasi
 * @property float $spo2
 * @property float $suhu
 * @property float $tinggi_badan
 * @property float $berat_badan
 * @property int $gcs_e
 * @property int $gcs_v
 * @property int $gcs_m
 * @property bool $is_active
 * @property bool $is_deleted
 * @property string $created_date
 * @property int $created_by
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property int $modified_count
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $tanggal_ttv
 */

class VitalSign extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vitalsign_t';
    }

    public function rules()
    {
        return [
            [['sumberttv_id', 'sumberttv', 'pasien_id', 'pendaftaran_id'], 'required'],
            [['sumberttv_id', 'jenisttv_id', 'tingkatkesadaran_id', 'created_by', 'last_modified_by', 'modified_count', 'deleted_by', 'pasien_id', 'pendaftaran_id'], 'integer'],
            [['sumberttv', 'jenisttv', 'tingkatkesadaran'], 'string'],
            [['sistol', 'diastol', 'nadi', 'respirasi', 'spo2', 'suhu', 'tinggi_badan', 'berat_badan', 'gcs_e', 'gcs_v', 'gcs_m'], 'number'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date', 'tanggal_ttv'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'vitalsign_id' => Yii::t('app', 'Vital Sign ID'),
            'sumberttv_id' => Yii::t('app', 'Sumber TTV ID'),
            'sumberttv' => Yii::t('app', 'Sumber TTV'),
            'jenisttv_id' => Yii::t('app', 'Jenis TTV ID'),
            'jenisttv' => Yii::t('app', 'Jenis TTV'),
            'tingkatkesadaran_id' => Yii::t('app', 'Tingkat Kesadaran ID'),
            'tingkatkesadaran' => Yii::t('app', 'Tingkat Kesadaran'),
            'sistol' => Yii::t('app', 'Sistol'),
            'diastol' => Yii::t('app', 'Diastol'),
            'nadi' => Yii::t('app', 'Nadi'),
            'respirasi' => Yii::t('app', 'Respirasi'),
            'spo2' => Yii::t('app', 'SpO2'),
            'suhu' => Yii::t('app', 'Suhu'),
            'tinggi_badan' => Yii::t('app', 'Tinggi Badan'),
            'berat_badan' => Yii::t('app', 'Berat Badan'),
            'gcs_e' => Yii::t('app', 'GCS E'),
            'gcs_v' => Yii::t('app', 'GCS V'),
            'gcs_m' => Yii::t('app', 'GCS M'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'tanggal_ttv' => Yii::t('app', 'Tanggal TTV'),
        ];
    }

    public static function getDataById($vitalSignId = null)
    {
        $result = [];

        if(!is_null($vitalSignId)) {
            $result = self::findOne($vitalSignId);
            $result = $result ? $result->toArray() : [];
        }
        Yii::error($result);
        return $result;
    }

    public static function userValidation($vitalSignId, $userId)
    {
        $isValid = false;
        $vitalSign = self::getDataById($vitalSignId);

        if(!empty($vitalSign) && $vitalSign['created_by'] == $userId) {
            $isValid = true;
        }

        return $isValid;
    }

    public static function mapData() {
        // 1. Rajal
        // - Askep : /rajal/asesmen-keperawatan/save-asesmen
        // - Asmed : /rajal/tra-pemeriksaan/create-fisik

        // 2. IGD
        // - Askep : /igd/asesmen-keperawatan/save-asesmen
        // - Asmed : /igd/asesmen-medis/save-medis

        // 3. Ranap
        // - Askep : /ranap/pemeriksaan-rawat-inap/save-asesmen
        // - Asmed : /ranap/asesmen-medis/save-asesmen

        return [
            'asesmen_keperawatan' => [
                'RJ' => [
                    'sistol' => 'td',
                    'diastol' => 'td',
                    'nadi' => 'nadi',
                    'respirasi' => 'rr',
                    'spo2' => 'spo2',
                    'suhu' => 'suhu',
                    'tinggi_badan' => 'tinggi_badan',
                    'berat_badan' => 'berat_badan',
                    'gcs_e' => 'gcs_e',
                    'gcs_v' => 'gcs_v',
                    'gcs_m' => 'gcs_m'
                ],
                'RI' => [
                    'sistol' => 'tensi',
                    'diastol' => 'tensi',
                    'nadi' => 'detak_nadi',
                    'respirasi' => 'pernafasan_spontan',
                    'spo2' => 'spo2',
                    'suhu' => 'suhu_tubuh',
                    'tinggi_badan' => 'tinggi_badan',
                    'berat_badan' => 'berat_badan',
                    'gcs_e' => 'gcseye_id',
                    'gcs_v' => 'gcsverbal_id',
                    'gcs_m' => 'gcsmotorik_id'
                ],
                'RD' => [
                    'sistol' => 'tensi',
                    'diastol' => 'tensi',
                    'nadi' => 'detak_nadi',
                    'respirasi' => 'pernapasan',
                    'spo2' => 'spo2',
                    'suhu' => 'suhu_tubuh',
                    'tinggi_badan' => 'tinggi_badan',
                    'berat_badan' => 'berat_badan',
                    'gcs_e' => 'gcseye_id',
                    'gcs_v' => 'gcsverbal_id',
                    'gcs_m' => 'gcsmotorik_id'
                ]
            ],
            'asesmen_medis' => [
                'RJ' => [
                    'sistol' => 'tekanandarah',
                    'diastol' => 'tekanandarah',
                    'nadi' => 'detaknadi',
                    'respirasi' => 'pernapasan',
                    'spo2' => 'spo2',
                    'suhu' => 'suhutubuh',
                    'tinggi_badan' => 'tinggibadan_cm',
                    'berat_badan' => 'beratbadan_kg',
                    'gcs_e' => 'gcs_eye',
                    'gcs_v' => 'gcs_verbal',
                    'gcs_m' => 'gcs_motorik'
                ],
                'RI' => [
                    'sistol' => 'td_systolic',
                    'diastol' => 'td_diastolic',
                    'nadi' => 'detak_nadi',
                    'respirasi' => 'pernapasan',
                    'spo2' => 'spo2',
                    'suhu' => 'suhu_tubuh',
                    'tinggi_badan' => 'tinggi_badan',
                    'berat_badan' => 'berat_badan',
                    'gcs_e' => 'gcs_eye',
                    'gcs_v' => 'gcs_verbal',
                    'gcs_m' => 'gcs_motorik'
                ],
                'RD' => [
                    'sistol' => 'tekanandarah',
                    'diastol' => 'tekanandarah',
                    'nadi' => 'nadi',
                    'respirasi' => 'pernapasan',
                    'spo2' => 'saturasi_o2',
                    'suhu' => 'suhu',
                    'tinggi_badan' => 'tinggi_badan',
                    'berat_badan' => 'berat_badan',
                    'gcs_e' => 'gcseye_id',
                    'gcs_v' => 'gcsverbal_id',
                    'gcs_m' => 'gcsmotorik_id'
                ]
            ]
        ];
    }
}
