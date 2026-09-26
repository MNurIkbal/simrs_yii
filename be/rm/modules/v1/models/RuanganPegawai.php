<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ruanganpegawai_mp".
 *
 * @property int $ruangan_id
 * @property int $pegawai_id
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
 * @property PegawaiM $pegawai
 * @property RuanganM $ruangan
 */
class RuanganPegawai extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruanganpegawai_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'pegawai_id'], 'required'],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ruangan_id', 'pegawai_id'], 'unique', 'targetAttribute' => ['ruangan_id', 'pegawai_id']],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
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
    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawai_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function getInstalasi()
    {
        return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'instalasi_id'])->via('ruangan');
    }

    public function getList($params, $ruangan_id)
    {
        $query = self::find()
            ->select(['ruanganpegawai_mp.is_active', 'ruanganpegawai_mp.ruangan_id', 'ruanganpegawai_mp.pegawai_id', 'r.ruangan_nama', 'p.nama_pegawai', 'k.kelompokpegawai_nama as kelompok_pegawai'])
            ->leftJoin(Pegawai::tableName(). ' p', 'ruanganpegawai_mp.pegawai_id = p.pegawai_id')
            ->leftJoin(Ruangan::tableName(). ' r', 'ruanganpegawai_mp.ruangan_id = r.ruangan_id')
            ->leftJoin(KelompokPegawai::tableName(). ' k', 'p.kelompokpegawai_id = k.kelompokpegawai_id')
            ->where(['ruanganpegawai_mp.ruangan_id' => $ruangan_id]);

        if(isset($params['nama_pegawai'])) {
            $query->andFilterWhere(['ILIKE', 'nama_pegawai', $params['nama_pegawai']]);
        }

        if(isset($params['kelompokpegawai_id'])) {
            $query->andFilterWhere(['ILIKE', 'kelompokpegawai_nama', $params['kelompokpegawai_id']]);
        }

        return $query;
    }

    public static function getByRuangan($ruangan_id)
    {
        $query = self::find()
            ->select([self::tableName(). '.*', 'i.instalasi_nama', 'r.ruangan_nama'])
            ->leftJoin(Ruangan::tableName(). ' r', 'ruanganpegawai_mp.ruangan_id = r.ruangan_id')
            ->leftJoin(Instalasi::tableName(). ' i', 'r.instalasi_id = i.instalasi_id')
            ->where([self::tableName(). '.ruangan_id' => $ruangan_id]);

        return $query->asArray()->one();
    }
}
