<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 17:02:34
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-26 09:16:23
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "pemeriksaanlab_m".
 *
 * @property int $pemeriksaanlab_id
 * @property int $jenispemeriksaanlab_id
 * @property int $daftartindakan_id
 * @property string $pemeriksaanlab_kode
 * @property string $pemeriksaanlab_nama
 * @property int $kelompokpemeriksaanlab_id
 * @property string $nama_kelompok
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
 * @property DetailhasilpemeriksaanlabT[] $detailhasilpemeriksaanlabTs
 * @property KlasifikasiatpM[] $klasifikasiatpMs
 * @property DaftartindakanM $daftartindakan
 * @property JenispemeriksaanlabM $jenispemeriksaanlab
 */
class PemeriksaanLabForm extends \yii\base\Model
{
    // Public property
    public $pemeriksaanlab_id;
    public $jenispemeriksaanlab_id;
    public $daftartindakan_id;
    public $pemeriksaanlab_kode;
    public $pemeriksaanlab_nama;
    public $kelompokpemeriksaanlab_id;
    public $nama_kelompok;
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
    public $is_exception;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['pemeriksaanlab_kode', 'jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id'], 'checkUnique', 'on' => 'insert'],
            // [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id'], 'checkUnique', 'on' => 'update'],
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id'], 'required'],
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenispemeriksaanlab_id', 'daftartindakan_id', 'kelompokpemeriksaanlab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active', 'is_exception'], 'boolean'],
            [['pemeriksaanlab_kode'], 'string'],
            [['pemeriksaanlab_nama'], 'string', 'max' => 500],
            [['nama_kelompok'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanlab_id' => Yii::t('fe', 'Pemeriksaan Lab ID'),
            'jenispemeriksaanlab_id' => Yii::t('fe', 'Jenis Pemeriksaan'),
            'daftartindakan_id' => Yii::t('fe', 'Daftar Tindakan'),
            'pemeriksaanlab_kode' => Yii::t('fe', 'Kode'),
            'pemeriksaanlab_nama' => Yii::t('fe', 'Nama Pemeriksaan'),
            'kelompokpemeriksaanlab_id' => Yii::t('fe', 'Kelompok Pemeriksaan'),
            'nama_kelompok' => Yii::t('fe', 'Kelompok Pemeriksaan Nama'),
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
            'is_exception' => Yii::t('fe', 'Exception Test'),
        ];
    }

    public function checkUnique() {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $pemeriksaanlab_kode = $this->pemeriksaanlab_kode;
        $jenispemeriksaanlab_id = $this->jenispemeriksaanlab_id;
        $daftartindakan_id = $this->daftartindakan_id;
        $kelompokpemeriksaanlab_id = $this->kelompokpemeriksaanlab_id;
        $scenario = $this->scenario;
        $data_pemeriksaan_lab = Yii::$app->cache->get("data-pemeriksaan-lab-{$instalasi}");
        if ($data_pemeriksaan_lab !== false) {
            foreach ($data_pemeriksaan_lab as $value) {
                if ($scenario == 'update') {
                    if ((preg_match("/\b({$jenispemeriksaanlab_id})\b/i", @$value['jenispemeriksaanlab_id'])
                        AND (preg_match("/\b({$daftartindakan_id})\b/i", @$value['daftartindakan_id']))
                        AND (preg_match("/\b({$kelompokpemeriksaanlab_id})\b/i", @$value['kelompokpemeriksaanlab_id']))
                    )) {
                        $this->addError('jenispemeriksaanlab_id', 'Jenis Pemeriksaan Sudah Terpakai');
                        $this->addError('daftartindakan_id', 'Nama Pemeriksaan Sudah Terpakai');
                        $this->addError('kelompokpemeriksaanlab_id', 'Kelompok Pemeriksaan Sudah Terpakai');
                        return false;
                    }
                } elseif ($scenario == 'insert') {
                    if (preg_match("/\b({$pemeriksaanlab_kode})\b/i", @$value['pemeriksaanlab_kode'])) {
                        $this->addError('pemeriksaanlab_kode', 'Kode Sudah Terpakai');
                        return false;
                    }
                    if ((preg_match("/\b({$jenispemeriksaanlab_id})\b/i", @$value['jenispemeriksaanlab_id'])
                        AND (preg_match("/\b({$daftartindakan_id})\b/i", @$value['daftartindakan_id']))
                        AND (preg_match("/\b({$kelompokpemeriksaanlab_id})\b/i", @$value['kelompokpemeriksaanlab_id']))
                    )) {
                        $this->addError('jenispemeriksaanlab_id', 'Jenis Pemeriksaan Sudah Terpakai');
                        $this->addError('daftartindakan_id', 'Nama Pemeriksaan Sudah Terpakai');
                        $this->addError('kelompokpemeriksaanlab_id', 'Kelompok Pemeriksaan Sudah Terpakai');
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
?>