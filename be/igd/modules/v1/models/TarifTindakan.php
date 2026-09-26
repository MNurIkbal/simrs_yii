<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tariftindakan_m".
 *
 * @property int $tariftindakan_id
 * @property int $kelaspelayanan_id
 * @property int $komponentarif_id
 * @property int $daftartindakan_id
 * @property int $jenistarif_id
 * @property int $perdatarif_id
 * @property double $harga_tariftindakan
 * @property int $persendiskon_tindakan
 * @property double $hargadiskon_tindakan
 * @property int $persencyto_tindakan
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
 * @property DaftartindakanM $daftartindakan
 * @property JenistarifM $jenistarif
 * @property KelaspelayananM $kelaspelayanan
 * @property KomponentarifM $komponentarif
 * @property PerdatarifM $perdatarif
 */
class TarifTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariftindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelaspelayanan_id', 'harga_tariftindakan', 'persendiskon_tindakan', 'hargadiskon_tindakan', 'persencyto_tindakan'], 'required'],
            [['kelaspelayanan_id', 'komponentarif_id', 'daftartindakan_id', 'jenistarif_id', 'perdatarif_id', 'persendiskon_tindakan', 'persencyto_tindakan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelaspelayanan_id', 'komponentarif_id', 'daftartindakan_id', 'jenistarif_id', 'perdatarif_id', 'persendiskon_tindakan', 'persencyto_tindakan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['harga_tariftindakan', 'hargadiskon_tindakan'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftartindakanM::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            [['jenistarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenistarifM::className(), 'targetAttribute' => ['jenistarif_id' => 'jenistarif_id']],
            [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            [['komponentarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => KomponentarifM::className(), 'targetAttribute' => ['komponentarif_id' => 'komponentarif_id']],
            [['perdatarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => PerdatarifM::className(), 'targetAttribute' => ['perdatarif_id' => 'perdatarif_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tariftindakan_id' => 'Tariftindakan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'komponentarif_id' => 'Komponentarif ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'jenistarif_id' => 'Jenistarif ID',
            'perdatarif_id' => 'Perdatarif ID',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'hargadiskon_tindakan' => 'Hargadiskon Tindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getDaftartindakan()
    // {
    //     return $this->hasOne(DaftartindakanM::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisTarif()
    {
        return $this->hasOne(JenisTarif::className(), ['jenistarif_id' => 'jenistarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKelasPelayanan()
    {
        return $this->hasOne(KelasPelayanan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getKomponentarif()
    // {
    //     return $this->hasOne(KomponentarifM::className(), ['komponentarif_id' => 'komponentarif_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPerdatarif()
    // {
    //     return $this->hasOne(PerdatarifM::className(), ['perdatarif_id' => 'perdatarif_id']);
    // }
}
