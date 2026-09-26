<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pesanobatdetail_t".
 *
 * @property int $pesanobatdetail_id
 * @property int $sumberdana_id
 * @property int $pesanobatalkes_id
 * @property int $satuankecil_id
 * @property int $obatalkes_id
 * @property double $jumlah_pesan
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
class PesanObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pesanobatdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanobatdetail_id'], 'required'],
            [['pesanobatdetail_id', 'pesanobatalkes_id', 'satuankecil_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesanobatdetail_id', 'pesanobatalkes_id', 'satuankecil_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pesanobatalkes_id', 'satuankecil_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jumlah_pesan'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatdetail_id' => 'Pesanobatdetail ID',
            // 'sumberdana_id' => 'Sumberdana ID',
            'pesanobatalkes_id' => 'Pesanobatalkes ID',
            'satuankecil_id' => 'Satuankecil ID',
            'obatalkes_id' => 'Obatalkes ID',
            'jumlah_pesan' => 'Jumlah Pesan',
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

    public function getObatalkes()
    {
        return $this->hasOne(ObatAlkes::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    public function getList($params, $pesanobatalkes_id)
    {
        $query = self::find()
            ->joinWith(['obatalkes'])
            ->where(['pesanobatalkes_id' => $pesanobatalkes_id]);

        if(isset($params['obatalkes_namalain'])) {
            $query->andFilterWhere(['ILIKE', 'obatalkes_m.obatalkes_namalain', $params['obatalkes_namalain']]);
        }

        return $query;
    }
}
