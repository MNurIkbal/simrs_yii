<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "adjusmenobat_t".
 *
 * @property int $adjusmenobat_id
 * @property string $no_adjusmen
 * @property string $tgl_adjusmen
 * @property int $peg_menyetujui_id
 * @property int $peg_mengetahui_id
 * @property int $ruangan_adjusmen_id
 * @property int $jenis_adjusmen 0 = adjusmen masuk, 1=adjusmen keluar
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
class AdjusmenObatForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */


    public $adjusmenobat_id;
    public $no_adjusmen;
    public $tgl_adjusmen;
    public $peg_menyetujui_id;
    public $peg_mengetahui_id;
    public $ruangan_adjusmen_id;
    public $jenis_adjusmen;
    public $created_by;
    public $modified_count;
    public $last_modified_by;
    public $deleted_by;
    public $additional_data;
    public $is_deleted;
    public $is_active;


    public static function tableName()
    {
        return 'adjusmenobat_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_adjusmen'], 'required', "on" => "def_required", "message" => "{attribute} tidak boleh kosong"],
            [['tgl_adjusmen', 'peg_menyetujui_id', 'peg_mengetahui_id'], 'required', "on" => "peg_required", "message" => "{attribute} tidak boleh kosong"],
            [['adjusmenobat_id', 'peg_menyetujui_id', 'peg_mengetahui_id', 'ruangan_adjusmen_id', 'jenis_adjusmen', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['adjusmenobat_id', 'peg_menyetujui_id', 'peg_mengetahui_id', 'ruangan_adjusmen_id', 'jenis_adjusmen', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_adjusmen', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_adjusmen'], 'string', 'max' => 255],
            [['adjusmenobat_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusmenobat_id' => 'Adjusmenobat ID',
            'no_adjusmen' => 'No Adjusmen',
            'tgl_adjusmen' => 'Tgl Adjusmen',
            'peg_menyetujui_id' => 'Pegawai Menyetujui',
            'peg_mengetahui_id' => 'Pegawai Mengetahui',
            'ruangan_adjusmen_id' => 'Ruangan Adjusmen ID',
            'jenis_adjusmen' => 'Jenis Adjusmen',
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
