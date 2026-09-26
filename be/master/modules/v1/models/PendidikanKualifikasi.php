<?php

namespace app\modules\v1\models;

use Yii;

 use app\modules\v1\models\KelompokPegawai;
 use app\modules\v1\models\Pendidikan;
/**
 * This is the model class for table "pendidikankualifikasi_m".
 *
 * @property integer $pendkualifikasi_id
 * @property integer $pendidikan_id
 * @property integer $kelompokpegawai_id
 * @property string $pendkualifikasi_kode
 * @property string $pendkualifikasi_nama
 * @property string $pendkualifikasi_namalainnya
 * @property string $pendkualifikasi_keterangan
 * @property integer $jmlkeblaki
 * @property integer $jmlkebperempuan
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class PendidikanKualifikasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pendidikankualifikasi_m';
    }

    /**
     * @inheritdoc
     */

    //  Validasi XSS di form
    protected $xssProtected = [
        'pendkualifikasi_nama',
        'jmlkeblaki',
        'jmlkebperempuan',
        'pendkualifikasi_keterangan',
        'additional_data',
        'pendkualifikasi_kode',
        'pendkualifikasi_namalainnya',
        'exist'
    ];

    public function rules()
    {
        return [
            [['pendidikan_id', 'kelompokpegawai_id', 'pendkualifikasi_nama'], 'required'],
            [['pendidikan_id', 'kelompokpegawai_id', 'jmlkeblaki', 'jmlkebperempuan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pendkualifikasi_keterangan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pendkualifikasi_kode'], 'string', 'max' => 10],
            [['pendkualifikasi_nama', 'pendkualifikasi_namalainnya'], 'string', 'max' => 100],
            [['kelompokpegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompokPegawai::className(), 'targetAttribute' => ['kelompokpegawai_id' => 'kelompokpegawai_id']],
            [['pendidikan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendidikan::className(), 'targetAttribute' => ['pendidikan_id' => 'pendidikan_id']],
            [['pendkualifikasi_kode'], 'checkUnique'],
        ];
    }

    public function checkUnique($attribute, $params)
    {
        $pendkualifikasi_kode = $this->pendkualifikasi_kode;
        $kelompokpegawai_id = $this->kelompokpegawai_id;
        $query = PendidikanKualifikasi::find()->where([
            'pendkualifikasi_kode' => $pendkualifikasi_kode,
            'kelompokpegawai_id' => $kelompokpegawai_id,
        ]);
        $query->andWhere(['is_deleted' => false]);
        $result = $query->one();

        if (!empty($result)) {
            if ($this->pendkualifikasi_id != $result->pendkualifikasi_id) {
                $this->addError('pendkualifikasi_kode','"'.$pendkualifikasi_kode.'" telah dipergunakan.');
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendkualifikasi_id' => 'Kualifikasi ID',
            'pendidikan_id' => 'Pendidikan',
            'kelompokpegawai_id' => 'Kelompok Pegawai',
            'pendkualifikasi_kode' => 'Kode Kualifikasi',
            'pendkualifikasi_nama' => 'Nama Kualifikasi',
            'pendkualifikasi_namalainnya' => 'Nama Lainnya',
            'pendkualifikasi_keterangan' => 'Keterangan',
            'jmlkeblaki' => 'Jumlah Kebutuhan Laki-laki',
            'jmlkebperempuan' => 'Jumlah Kebutuhan Perempuan',
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
    
    public function getPendidikan()
    {
        return $this->hasOne(Pendidikan::className(), ['pendidikan_id' => 'pendidikan_id']);
    }
    
    public function getKelompokpegawai()
    {
        return $this->hasOne(KelompokPegawai::className(), ['kelompokpegawai_id' => 'kelompokpegawai_id']);
    }
    
    public function extraFields()
    {
        return [
            'pendidikan_m' => function($item){
                return $item->pendidikan;
            },
            'kelompokpegawai_m' => function($item){
                return $item->kelompokpegawai;
            }
        ];
    }
}
