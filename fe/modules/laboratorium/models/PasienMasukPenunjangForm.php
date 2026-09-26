<?php

namespace app\modules\laboratorium\models;

use Yii;

/**
 * This is the model class for table "pasienmasukpenunjang_t".
 *
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property int $kelaspelayanan_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pasienadmisi_id
 * @property int $pegawai_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $ruanganasal_id
 * @property string $no_masukpenunjang
 * @property string $tglmasukpenunjang
 * @property string $no_antrian
 * @property string $kunjungan lookup_type='kunjungan'
 * @property string $status_periksa lookup_type='status_periksa'
 * @property bool $panggil_antrian
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
 * @property int $instalasiasal_id
 */
class PasienMasukPenunjangForm extends \yii\base\Model
{
        public $pasienmasukpenunjang_id;
        public $pasienkirimkeunitlain_id;
        public $kelaspelayanan_id;
        public $jeniskasuspenyakit_id;
        public $pasienadmisi_id;
        public $pegawai_id;
        public $catatan_dokterpengirim;
        public $ruangan_id;
        public $pasien_id;
        public $pendaftaran_id;
        public $ruanganasal_id;
        public $no_masukpenunjang;
        public $tglmasukpenunjang;
        public $no_antrian;
        public $kunjungan;
        public $status_periksa;
        public $panggil_antrian;
        public $additional_data;
        public $instalasiasal_id;
        


    /**
     * {@inheritdoc}
     */
    public static function tableName () {
        return 'pasienmasukpenunjang_t';
    }

    protected $xssProtected = [
        'catatan_dokterpengirim'
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catatan_dokterpengirim'], 'safe'],
            [['pegawai_id'], 'required'],
            [['catatan_dokterpengirim'], 'string', 'max' => 1500],
            [['catatan_dokterpengirim','pegawai_id'], 'safe'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawai_id' => 'Pegawai ID',
            'catatan_dokterpengirim' => 'Catatan Dokter Pengirim',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'ruanganasal_id' => 'Ruanganasal ID',
            'no_masukpenunjang' => 'No Masukpenunjang',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_antrian' => 'No Antrian',
            'kunjungan' => 'Kunjungan',
            'status_periksa' => 'Status Periksa',
            'panggil_antrian' => 'Panggil Antrian',
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
            'instalasiasal_id' => 'Instalasiasal ID',
        ];
    }
}
