<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "satuankonversibrg_m".
 *
 * @property int $satuankonversibrg_id
 * @property int $satuanbesar_id
 * @property int $satuankecil_id
 * @property double $nilai_konversi
 * @property int $barang_id
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
class SatuanKonversiBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'satuankonversibrg_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[/*'satuanbesar_id'*/ 'satuankecil_id', 'nilai_konversi', 'barang_id'], 'required'],
            [['satuanbesar_id', 'satuankecil_id', 'barang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['satuankonversibrg_id', 'satuanbesar_id', 'satuankecil_id', 'barang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nilai_konversi'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['satuanbesar_id'], 'checkUnique'],
            [['nilai_konversi'], 'number', 'min' => 1],
            // [['satuankonversibrg_id', 'satuanbesar_id', 'satuankecil_id', 'barang_id'], 'unique', 'targetAttribute' => ['satuankonversibrg_id', 'satuanbesar_id', 'satuankecil_id', 'barang_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'satuankonversibrg_id' => 'Satuankonversibrg ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuankecil_id' => 'Satuankecil ID',
            'nilai_konversi' => 'Nilai Konversi',
            'barang_id' => 'Barang ID',
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

    public function checkUnique()
    {
        $satuanbesar_id = $this->satuanbesar_id;
        $model = self::find()->where([
            'satuanbesar_id' => $satuanbesar_id,
            'satuankecil_id' => $this->satuankecil_id,
            'barang_id' => $this->barang_id
        ])->one();
        if (!empty($model) && $model->satuankonversibrg_id != $this->satuankonversibrg_id) {
            $this->addError('satuanbesar_id', 'Satuan besar sudah di pakai pada barang ini');
        }
    }
}
