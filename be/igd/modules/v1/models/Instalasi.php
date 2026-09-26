<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "instalasi_m".
 *
 * @property int $instalasi_id
 * @property int $riwayatruangan_id
 * @property string $instalasi_nama
 * @property string $instalasi_namalainnya
 * @property string $instalasi_singkatan
 * @property string $instalasi_lokasi
 * @property bool $instalasirujukaninternal
 * @property bool $instalasi_adakamar
 * @property string $instalasi_image
 * @property bool $is_penunjang
 * @property int $profilers_id
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
 * @property AlatmedisM[] $alatmedisMs
 * @property ProfilrumahsakitM $profilers
 * @property JadwaldokterM[] $jadwaldokterMs
 * @property KomponentarifinstalasiMp[] $komponentarifinstalasiMps
 * @property KomponentarifM[] $komponentarifs
 * @property LokasipenyimpananM[] $lokasipenyimpananMs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PermintaanpembelianT[] $permintaanpembelianTs
 * @property RekeninguangmukaM[] $rekeninguangmukaMs
 * @property RuanganM[] $ruanganMs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property UangmukabayarrekM[] $uangmukabayarrekMs
 */
class Instalasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instalasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riwayatruangan_id', 'profilers_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['riwayatruangan_id', 'profilers_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['instalasi_nama', 'instalasi_singkatan'], 'required'],
            [['instalasirujukaninternal', 'instalasi_adakamar', 'is_penunjang', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['instalasi_nama', 'instalasi_namalainnya', 'instalasi_lokasi'], 'string', 'max' => 50],
            [['instalasi_singkatan'], 'string', 'max' => 5],
            [['instalasi_image'], 'string', 'max' => 200],
            // [['profilers_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfilrumahsakitM::className(), 'targetAttribute' => ['profilers_id' => 'profilrs_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => 'Instalasi ID',
            'riwayatruangan_id' => 'Riwayatruangan ID',
            'instalasi_nama' => 'Instalasi Nama',
            'instalasi_namalainnya' => 'Instalasi Namalainnya',
            'instalasi_singkatan' => 'Instalasi Singkatan',
            'instalasi_lokasi' => 'Instalasi Lokasi',
            'instalasirujukaninternal' => 'Instalasirujukaninternal',
            'instalasi_adakamar' => 'Instalasi Adakamar',
            'instalasi_image' => 'Instalasi Image',
            'is_penunjang' => 'Is Penunjang',
            'profilers_id' => 'Profilers ID',
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

    public function listInstalasi()
    {
        $query = self::find()->where(['is_active' => 1])->all();

        return $query;
    }
}
