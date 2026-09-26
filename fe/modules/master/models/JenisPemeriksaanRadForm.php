<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "jenispemeriksaanrad_m".
 *
 * @property int $jenispemeriksaanrad_id
 * @property string $jenispemeriksaanrad_kode
 * @property string $jenispemeriksaanrad_nama
 * @property string $jenispemeriksaanrad_namalainnya
 * @property int $kelompokpemeriksaanrad_id
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
 *
 * @property PemeriksaanRad[] $pemeriksaanrad
 */
class JenisPemeriksaanRadForm extends \yii\base\Model
{
    // Public property
    public $jenispemeriksaanrad_id;
    public $jenispemeriksaanrad_kode;
    public $jenispemeriksaanrad_nama;
    public $jenispemeriksaanrad_namalain;
    public $kelompokpemeriksaanrad_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    // public static function tableName()
    // {
    //     return 'jenispemeriksaanrad_m';
    // }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['jenispemeriksaanrad_kode', 'jenispemeriksaanrad_nama'], 'checkUnique', 'on' => 'insert'],
            [['jenispemeriksaanrad_nama'], 'checkUnique'],
            [['jenispemeriksaanrad_kode', 'jenispemeriksaanrad_nama', 'kelompokpemeriksaanrad_id'], 'required'],
            [['kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenispemeriksaanrad_kode'], 'string', 'max' => 10],
            [['jenispemeriksaanrad_nama', 'jenispemeriksaanrad_namalain'], 'string', 'max' => 100],
            // [['jenispemeriksaanrad_kode'], 'unique', 'on' => 'insert'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributelabels()
    {
        return [
            'jenispemeriksaanrad_id' => Yii::t('fe', 'Jenis Pemeriksaan rad ID'),
            'jenispemeriksaanrad_kode' => Yii::t('fe', 'Kode'),
            'jenispemeriksaanrad_nama' => Yii::t('fe', 'Jenis Pemeriksaan'),
            'jenispemeriksaanrad_namalain' => Yii::t('fe', 'Nama Lainnya'),
            'kelompokpemeriksaanrad_id' => Yii::t('fe', 'Kelompok Pemeriksaan'),
            'additional_data' => Yii::t('fe', 'Additional Data'),
            'created_date' => Yii::t('fe', 'Created Date'),
            'created_by' => Yii::t('fe', 'Created By'),
            'modified_count' => Yii::t('fe', 'Modified Count'),
            'last_modified_date' => Yii::t('fe', 'Last Modified Date'),
            'last_modified_by' => Yii::t('fe', 'Last Modified By'),
            'is_deleted' => Yii::t('fe', 'Is Deleted'),
            'is_active' => Yii::t('fe', 'Is Active'),
            'deleted_date' => Yii::t('fe', 'Deleted Date'),
            'deleted_by' => Yii::t('fe', 'Deleted By'),
        ];
    }

    public function checkUnique() {
        $jenispemeriksaanrad_nama = $this->jenispemeriksaanrad_nama;
        if (strpos(substr($jenispemeriksaanrad_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('jenispemeriksaanrad_nama', 'Jenis pemeriksaan mengandung spasi di awal kata');
            return false;
        }
        // $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        // $jenispemeriksaanrad_kode = $this->jenispemeriksaanrad_kode;
        // $jenispemeriksaanrad_nama = $this->jenispemeriksaanrad_nama;
        // $scenario = $this->scenario;
        // $data_jenis_pemeriksaan_rad = Yii::$app->cache->get("data-jenis-pemeriksaan-rad-{$instalasi}");
        // if ($data_jenis_pemeriksaan_rad !== false) {
        //     foreach ($data_jenis_pemeriksaan_rad as $value) {
        //         if ($scenario == 'update') {
        //             if (preg_match("/\b({$jenispemeriksaanrad_nama})\b/i", @$value['jenispemeriksaanrad_nama'])
        //                 AND preg_match("/\b({$kelompokpemeriksaanrad_id})\b/i", @$value['kelompokpemeriksaanrad_id'])) {
        //                 $this->addError('jenispemeriksaanrad_nama', 'Jenis Pemeriksaan Sudah Terpakai');
        //                 $this->addError('kelompokpemeriksaanrad_id', 'Kelompok Pemeriksaan Sudah Terpakai');
        //                 return false;
        //             }
        //         } elseif ($scenario == 'insert') {
        //             if (preg_match("/\b({$jenispemeriksaanrad_kode})\b/i", @$value['jenispemeriksaanrad_kode'])) {
        //                 $this->addError('jenispemeriksaanrad_kode', 'Kode Sudah Terpakai');
        //                 return false;
        //             }
        //             if (preg_match("/\b({$jenispemeriksaanrad_nama})\b/i", @$value['jenispemeriksaanrad_nama'])
        //                 AND preg_match("/\b({$kelompokpemeriksaanrad_id})\b/i", @$value['kelompokpemeriksaanrad_id'])) {
        //                 $this->addError('jenispemeriksaanrad_nama', 'Jenis Pemeriksaan Sudah Terpakai');
        //                 $this->addError('kelompokpemeriksaanrad_id', 'Kelompok Pemeriksaan Sudah Terpakai');
        //                 return false;
        //             }
        //         }
        //     }
        // }
        return true;
    }
}
?>