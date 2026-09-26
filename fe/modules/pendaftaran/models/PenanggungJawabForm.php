<?php

namespace app\modules\pendaftaran\models;

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
class PenanggungJawabForm extends \yii\base\Model
{
    
    public $penanggungjawab_id;
    public $pengantar;
    public $jenisidentitas;
    public $no_identitas;
    public $hubungankeluarga;
    public $penanggungjawab_nama;
    public $penanggungjawab_tempatlahir;
    public $penanggungjawab_tgllahir;
    public $penanggungjawab_jeniskelamin;
    public $penanggungjawab_alamat;
    public $penanggungjawab_notelp;
    public $penanggungjawab_nohp;
    public $penanggungjawab_umur;
    public $pasien_id;
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
            [['pengantar', 'penanggungjawab_nama'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['penanggungjawab_tgllahir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
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
            'jenisidentitas' => 'Jenis Identitas',
            'no_identitas' => 'No Identitas',
            'hubungankeluarga' => 'Hubungan Keluarga',
            'penanggungjawab_nama' => 'Penanggung Jawab Nama',
            'penanggungjawab_tempatlahir' => 'Tempat Lahir',
            'penanggungjawab_tgllahir' => 'Tanggal Lahir',
            'penanggungjawab_jeniskelamin' => 'Jenis Kelamin',
            'penanggungjawab_alamat' => 'Alamat',
            'penanggungjawab_notelp' => 'No Telepon',
            'penanggungjawab_nohp' => 'No Hp',
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
            'penanggungjawab_umur' => 'Umur',
        ];
    }
}
