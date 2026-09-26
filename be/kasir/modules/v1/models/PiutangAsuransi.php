<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "piutangasuransi_t".
 *
 * @property int $piutangasuransi_id
 * @property int $pembayaranpelayanan_id
 * @property int $penjamin_id
 * @property int $carabayar_id
 * @property double $jmlpiutangasuransi
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
class PiutangAsuransi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'piutangasuransi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pembayaranpelayanan_id', 'penjamin_id', 'carabayar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pembayaranpelayanan_id', 'penjamin_id', 'carabayar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jmlpiutangasuransi'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'piutangasuransi_id' => 'Piutangasuransi ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'penjamin_id' => 'Penjamin ID',
            'carabayar_id' => 'Carabayar ID',
            'jmlpiutangasuransi' => 'Jmlpiutangasuransi',
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
