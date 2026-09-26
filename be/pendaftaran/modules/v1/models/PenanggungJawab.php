<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penanggungjawab_m".
 *
 * @property int $penanggungjawab_id
 * @property string $pengantar
 * @property string $jenisidentitas
 * @property string $no_identitas
 * @property string $hubungankeluarga
 * @property string $penanggungjawab_nama
 * @property string $penanggungjawab_tempatlahir
 * @property string $penanggungjawab_tgllahir
 * @property string $penanggungjawab_jeniskelamin
 * @property string $penanggungjawab_alamat
 * @property string $penanggungjawab_notelp
 * @property string $penanggungjawab_nohp
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
 * @property int $pasien_id
 *
 * @property PendaftaranT[] $pendaftaranTs
 */
class PenanggungJawab extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'penanggungjawab_nama',
        'no_identitas',
        'penanggungjawab_tempatlahir',
        'penanggungjawab_alamat',
        'penanggungjawab_notelp',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penanggungjawab_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['penanggungjawab_nama','no_identitas','penanggungjawab_tempatlahir','penanggungjawab_alamat',
                'penanggungjawab_notelp','penanggungjawab_tgllahir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['penanggungjawab_alamat', 'additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pengantar', 'no_identitas', 'hubungankeluarga', 'penanggungjawab_nama'], 'string', 'max' => 50],
            [['jenisidentitas', 'penanggungjawab_tempatlahir', 'penanggungjawab_jeniskelamin'], 'string', 'max' => 20],
            [['penanggungjawab_notelp', 'penanggungjawab_nohp'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'pengantar' => 'Pengantar',
            'jenisidentitas' => 'Jenisidentitas',
            'no_identitas' => 'No Identitas',
            'hubungankeluarga' => 'Hubungankeluarga',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'penanggungjawab_tempatlahir' => 'Penanggungjawab Tempatlahir',
            'penanggungjawab_tgllahir' => 'Penanggungjawab Tgllahir',
            'penanggungjawab_jeniskelamin' => 'Penanggungjawab Jeniskelamin',
            'penanggungjawab_alamat' => 'Penanggungjawab Alamat',
            'penanggungjawab_notelp' => 'Penanggungjawab Notelp',
            'penanggungjawab_nohp' => 'Penanggungjawab Nohp',
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
            'pasien_id' => 'Pasien ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasMany(Pendaftaran::className(), ['penanggungjawab_id' => 'penanggungjawab_id']);
    }
}
