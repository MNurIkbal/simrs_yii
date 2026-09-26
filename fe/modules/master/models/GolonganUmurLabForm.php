<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "golonganumurlab_m".
 *
 * @property int $golonganumurlab_id
 * @property string $gol_umurlab_nama
 * @property string $gol_umurlab_namalainnya
 * @property string $gol_umurlab_minimal
 * @property string $gol_murlab_maksimal
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
class GolonganUmurLabForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    
    public $tahun_minimal;
    public $bulan_minimal;
    public $hari_minimal;
    public $tahun_maksimal;
    public $bulan_maksimal;
    public $hari_maksimal;
    public $golonganumurlab_id;
    public $gol_umurlab_nama;
    public $gol_umurlab_namalainnya;
    public $gol_umurlab_minimal;
    public $gol_umurlab_maksimal;
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
    public function rules()
    {
        return [
            [['gol_umurlab_nama','gol_umurlab_minimal', 'gol_umurlab_maksimal'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['gol_umurlab_minimal', 'gol_umurlab_maksimal'], 'number'],
            [['additional_data'], 'string'],
            [['gol_umurlab_nama'], 'trimWhitespace'],
            [['gol_umurlab_namalainnya'], 'trimWhitespaceNamalain'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['gol_umurlab_nama', 'gol_umurlab_namalainnya'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'golonganumurlab_id' => 'Golonganumurlab ID',
            'gol_umurlab_nama' => 'Nama Golongan',
            'gol_umurlab_namalainnya' => 'Nama Lainnya',
            'gol_umurlab_minimal' => 'Golongan Umur Minimal',
            'gol_umurlab_maksimal' => 'Golongan Umur Maksimal',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => Yii::t('fe', 'Aktif'),
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
    public function trimWhitespace(){
        $gol_umurlab_nama = $this->gol_umurlab_nama;
        if (strpos(substr($gol_umurlab_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('gol_umurlab_nama', 'Nama golongan mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
    public function trimWhitespaceNamalain(){
        $gol_umurlab_namalainnya = $this->gol_umurlab_namalainnya;
        if (!empty($this->gol_umurlab_namalainnya) && strpos(substr($gol_umurlab_namalainnya, 0, 1), ' ') !== FALSE) {
            $this->addError('gol_umurlab_namalainnya', 'Nama lainnya mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}
