<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 11:25:10
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-24 15:15:25
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "jenispemeriksaanlab_m".
 *
 * @property int $jenispemeriksaanlab_id
 * @property string $jenispemeriksaanlab_kode
 * @property string $jenispemeriksaanlab_nama
 * @property string $jenispemeriksaanlab_namalainnya
 * @property int $kelompokpemeriksaanlab_id
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
 * @property PemeriksaanLab[] $pemeriksaanLab
 */
class JenisPemeriksaanLabForm extends \yii\base\Model
{
    // Public property
    public $jenispemeriksaanlab_id;
    public $jenispemeriksaanlab_kode;
    public $jenispemeriksaanlab_nama;
    public $jenispemeriksaanlab_namalainnya;
    public $kelompokpemeriksaanlab_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['jenispemeriksaanlab_kode', 'jenispemeriksaanlab_nama'], 'checkUnique', 'on' => 'insert'],
            [['jenispemeriksaanlab_nama'], 'checkUnique'],
            [['jenispemeriksaanlab_kode', 'jenispemeriksaanlab_nama', 'kelompokpemeriksaanlab_id'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['kelompokpemeriksaanlab_id'], 'default', 'value' => null],
            [['kelompokpemeriksaanlab_id'], 'integer'],
            [['jenispemeriksaanlab_kode'], 'string', 'max' => 10],
            [['jenispemeriksaanlab_nama', 'jenispemeriksaanlab_namalainnya'], 'string', 'max' => 100],
            // [['jenispemeriksaanlab_kode'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenispemeriksaanlab_id' => Yii::t('fe', 'Jenis Pemeriksaan Lab ID'),
            'jenispemeriksaanlab_kode' => Yii::t('fe', 'Kode'),
            'jenispemeriksaanlab_nama' => Yii::t('fe', 'Jenis Pemeriksaan'),
            'jenispemeriksaanlab_namalainnya' => Yii::t('fe', 'Nama Lainnya'),
            'kelompokpemeriksaanlab_id' => Yii::t('fe', 'Kelompok Pemeriksaan'),
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
        $jenispemeriksaanlab_nama = $this->jenispemeriksaanlab_nama;
        if (strpos(substr($jenispemeriksaanlab_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('jenispemeriksaanlab_nama', 'Jenis pemeriksaan mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}
?>