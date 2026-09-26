<?php

/**
 * @author Randy Vianda Putra
 * @todo Form Master Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "pemeriksaanrad_m".
 *
 * @property int $pemeriksaanrad_id
 * @property int $jenispemeriksaanrad_id
 * @property int $daftartindakan_id
 * @property string $pemeriksaanrad_kode
 * @property string $pemeriksaanrad_nama
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
 */
class PemeriksaanRadForm extends \yii\db\ActiveRecord
{
    // Public property
    public $pemeriksaanrad_id;
    public $jenispemeriksaanrad_id;
    public $daftartindakan_id;
    public $pemeriksaanrad_kode;
    public $pemeriksaanrad_nama;
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemeriksaanrad_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['pemeriksaanrad_kode', 'jenispemeriksaanrad_id', 'daftartindakan_id', 'kelompokpemeriksaanrad_id'], 'checkUnique', 'on' => 'insert'],
            // [['jenispemeriksaanrad_id', 'daftartindakan_id', 'kelompokpemeriksaanrad_id'], 'checkUnique', 'on' => 'update'],
            [['jenispemeriksaanrad_id', 'daftartindakan_id', 'kelompokpemeriksaanrad_id'], 'required'],
            [['jenispemeriksaanrad_id', 'daftartindakan_id', 'kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenispemeriksaanrad_id', 'daftartindakan_id', 'kelompokpemeriksaanrad_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pemeriksaanrad_kode'], 'string', 'max' => 10],
            [['pemeriksaanrad_nama'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanrad_id' => Yii::t('fe', 'Pemeriksaan Lab ID'),
            'jenispemeriksaanrad_id' => Yii::t('fe', 'Jenis Pemeriksaan'),
            'daftartindakan_id' => Yii::t('fe', 'Daftar Tindakan'),
            'pemeriksaanrad_kode' => Yii::t('fe', 'Kode'),
            'pemeriksaanrad_nama' => Yii::t('fe', 'Nama Pemeriksaan'),
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
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $pemeriksaanrad_kode = $this->pemeriksaanrad_kode;
        $jenispemeriksaanrad_id = $this->jenispemeriksaanrad_id;
        $daftartindakan_id = $this->daftartindakan_id;
        $kelompokpemeriksaanrad_id = $this->kelompokpemeriksaanrad_id;
        $scenario = $this->scenario;
        $data_pemeriksaan_rad = Yii::$app->cache->get("data-pemeriksaan-rad-{$instalasi}");
        if ($data_pemeriksaan_rad !== false) {
            foreach ($data_pemeriksaan_rad as $value) {
                if ($scenario == 'update') {
                    if ((preg_match("/\b({$jenispemeriksaanrad_id})\b/i", @$value['jenispemeriksaanrad_id'])
                        AND (preg_match("/\b({$daftartindakan_id})\b/i", @$value['daftartindakan_id']))
                        AND (preg_match("/\b({$kelompokpemeriksaanrad_id})\b/i", @$value['kelompokpemeriksaanrad_id']))
                    )) {
                        $this->addError('jenispemeriksaanrad_id', 'Jenis Pemeriksaan Sudah Terpakai');
                        $this->addError('daftartindakan_id', 'Nama Pemeriksaan Sudah Terpakai');
                        $this->addError('kelompokpemeriksaanrad_id', 'Kelompok Pemeriksaan Sudah Terpakai');
                        return false;
                    }
                } elseif ($scenario == 'insert') {
                    if (preg_match("/\b({$pemeriksaanrad_kode})\b/i", @$value['pemeriksaanrad_kode'])) {
                        $this->addError('pemeriksaanrad_kode', 'Kode Sudah Terpakai');
                        return false;
                    }
                    if ((preg_match("/\b({$jenispemeriksaanrad_id})\b/i", @$value['jenispemeriksaanrad_id'])
                        AND (preg_match("/\b({$daftartindakan_id})\b/i", @$value['daftartindakan_id']))
                        AND (preg_match("/\b({$kelompokpemeriksaanrad_id})\b/i", @$value['kelompokpemeriksaanrad_id']))
                    )) {
                        $this->addError('jenispemeriksaanrad_id', 'Jenis Pemeriksaan Sudah Terpakai');
                        $this->addError('daftartindakan_id', 'Nama Pemeriksaan Sudah Terpakai');
                        $this->addError('kelompokpemeriksaanrad_id', 'Kelompok Pemeriksaan Sudah Terpakai');
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
?>