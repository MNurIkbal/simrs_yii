<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kamarruangan_m".
 *
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
 * @property string $kamarruangan_nokamar
 * @property int $kamarruangan_jenis lookup_type='jenis_kamar'
 * @property string $kamarruangan_deskripsi
 * @property string $kamarruangan_image
 * @property string $keterangan_kamar lookup_type='keterangan_kamar'
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
 * @property double $jumlah_tt
 * @property int $kamaruangan_tipe 0=fixed, 1=fleksibel
 * @property int $jeniskasuspenyakit_id
 * @property int $klasifikasikamar_id
 * @property int $kamarruangan_kode
 *
 */
class KamarRuangan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    
    protected $xssProtected = [
        'kamarruangan_nokamar', 
        'kamarruangan_deskripsi',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kamarruangan_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'kamarruangan_nokamar', 'kamarruangan_jenis','jeniskasuspenyakit_id'], 'required'],
            // [['kamarruangan_nokamar','ruangan_id'], 'checkUniqueCase'],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamaruangan_tipe'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamaruangan_tipe','jeniskasuspenyakit_id', 'klasifikasikamar_id'], 'integer'],
            [['kamarruangan_deskripsi', 'kamarruangan_image', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'kamarruangan_deskripsi', 'kamarruangan_kode'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kamarruangan_kode'], 'unique'],
            [['jumlah_tt'], 'number'],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['keterangan_kamar'], 'string', 'max' => 50],
        ];
    }

    public function checkUniqueCase($attribute, $params)
    {
        $kamarruangan_nokamar = strtoupper($this->kamarruangan_nokamar);
        $ruangan_id = $this->ruangan_id;
        $kelaspelayanan_id = $this->kelaspelayanan_id;

        $query = KamarRuangan::find()->where([
            'kamarruangan_nokamar' => $this->kamarruangan_nokamar
        ]);
        $query->andWhere(['is_deleted' => false]);
        $query->andWhere(['ruangan_id' => $ruangan_id]);
        // $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
        $resKamar =  $query->one();

        $ruangan = Ruangan::findOne($ruangan_id);

        if (!empty($resKamar)) {
            if ($this->kamarruangan_id != $resKamar->kamarruangan_id) {
                if (!empty($ruangan)) {
                    $this->addError('kamarruangan_nokamar','"'.$kamarruangan_nokamar.'" telah dipergunakan di ruangan '.$ruangan->ruangan_nama.'.');
                } else {
                    $this->addError('kamarruangan_nokamar','"'.$kamarruangan_nokamar.'" telah dipergunakan.');
                }

                return false;
            }
        }

        return true;
    }

    public function getKettempattidur()
    {
        return $this->hasOne(KetTempatTidur::className(), ['kamarruangan_jenis' => 'kamarruangan_jenis']);
    }

    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => 'Kamarruangan ID',
            'ruangan_id' => 'Ruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kamarruangan_nokamar' => 'Nama Kamar',
            'kamarruangan_jenis' => 'Kamarruangan Jenis',
            'kamarruangan_deskripsi' => 'Kamarruangan Deskripsi',
            'kamarruangan_image' => 'Kamarruangan Image',
            'keterangan_kamar' => 'Keterangan Kamar',
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
            'jumlah_tt' => 'Jumlah Tt',
            'kamaruangan_tipe' => 'Kamaruangan Tipe',
            'jeniskasuspenyakit_id' => 'Jenis Kasus Penyakit',
            'klasifikasikamar_id' => 'Klasifikasi Kamar',
            'kamarruangan_kode' => 'Kamar Ruangan Kode'

        ];
    }
}
