<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-13 13:27:44
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-13 13:28:31
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "skrining_covid_t".
 *
 * @property int $racikan_id
 * @property string $racikan_nama
 * @property string $racikan_singkatan
 * @property double $tarif_service
 * @property double $persen_service
 * @property double $biaya_kemasan
 * 
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class SkriningCovidRajal extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skrining_covid_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['skrining_covid_id','pendaftaran_id','demam','batuk_pilek_nyeri_tenggorokan','sesak_napas',
            'riwayat_luar_negeri','riwayat_dalam_negeri','resiko_kontak_pasien_covid','kontak_tatap_muka',
            'kontak_fisik','kontak_perawatan_tanpa_apd','swab_positif','tgl_swab_positif','swab_negatif',
            'tgl_swab_negatif','suspek','terkonfirmasi','petugas_pemeriksa','additional_data','created_date', 
            'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['demam','batuk_pilek_nyeri_tenggorokan','sesak_napas','riwayat_luar_negeri',
            'riwayat_dalam_negeri','resiko_kontak_pasien_covid','kontak_tatap_muka','kontak_fisik','kontak_perawatan_tanpa_apd',
            'swab_positif','tgl_swab_positif','swab_negatif','tgl_swab_negatif','suspek','terkonfirmasi','petugas_pemeriksa',
            'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'skrining_covid_id' => 'Skrining Covid Id',
            'pendaftaran_id' => 'Pendaftaran Id',
            //  GEJALA
            'demam' => 'Demam ≥38 /riwayat demam',
            'batuk_pilek_nyeri_tenggorokan' => 'Batuk/Pilek/nyeri tenggerokan',
            'sesak_napas' => 'Sesak nafas/Pneumonia (ringan-berat)',
            // FAKTOR RESIKO
            'riwayat_luar_negeri' => 'Memiliki riwayat perjalanan 14 hari terakhir atau tinggal di luar negeri yang melaporkan transmisi lokal',
            'riwayat_dalam_negeri' => 'Memiliki riwayat perjalanan 14 hari terakhir atau tinggal di area transmisi lokal di Indonesia',
            'resiko_kontak_pasien_covid' => 'Pada 14 hari terakhir sebelum timbul gejala memiliki riwayat kontak dengan kasus konfirmasi atau probable COVID-19',
            // KONTRAK ERAT
            'kontak_tatap_muka' => 'Kontak tatap muka/berdekatan dengan kasus probable/konfirmasi dalam radius 1 meter dan kurun waktu ≥ 15 menit',
            'kontak_fisik' => 'Sentuhan fisik langsung dengan kasus probable/konfirmasi (seperti bersalaman,berpegangan tangan dan lain-lain)',
            'kontak_perawatan_tanpa_apd' => 'Orang yang memberikan perawatan langsung pada kasus probable/konfirmasi tanpa menggunakan APD yang sesuai standar',
            // HASIL SWAB
            'swab_positif' => 'Pernah melakukan swab (PCR/TCM) 14 hari terakhir dengan hasil <b>positif</b> Jika YA,Tanggal berapa',
            'tgl_swab_positif' => 'Tanngal Swab Positif',
            'swab_negatif' => 'Pernah melakukan swab (PCR/TCM) 14 hari terakhir dengan hasil <b>Negatif</b> Jika YA, Tanggal berapa',
            'tgl_swab_negatif' => 'Taggal Swab Negatif',
            'suspek' => 'SUSPEK',
            'terkonfirmasi' => 'Terkonfirmasi',
            'petugas_pemeriksa' => 'Petugas Pemeriksa',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
