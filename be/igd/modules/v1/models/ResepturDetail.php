<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-20 16:34:10
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "resepturdetail_t".
 *
 * @property int $resepturdetail_id
 * @property int $obatalkes_id
 * @property int $racikan_id
 * @property int $satuankecil_id
 * @property int $reseptur_id
 * @property string $r
 * @property int $rke
 * @property int $kekuatan_reseptur
 * @property string $satuankekuatan
 * @property double $qty_reseptur
 * @property double $hargasatuan_reseptur
 * @property double $harganetto_reseptur
 * @property double $hargajual_reseptur
 * @property string $etiket
 * @property int $iter
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
 * @property int $signa_id
 * @property string $status_implementasi
 *
 * @property ResepturT $reseptur
 */
class ResepturDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $qty_konversi;

    public static function tableName()
    {
        return 'resepturdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'racikan_id', 'satuankecil_id', 'reseptur_id', 'qty_reseptur'], 'required'],
            [['obatalkes_id', 'racikan_id', 'satuankecil_id', 'reseptur_id', 'rke', 'kekuatan_reseptur', 'iter', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'signa_id'], 'default', 'value' => null],
            [['obatalkes_id', 'racikan_id', 'satuankecil_id', 'reseptur_id', 'rke', 'kekuatan_reseptur', 'iter', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'signa_id'], 'integer'],
            [['qty_reseptur', 'hargasatuan_reseptur', 'harganetto_reseptur', 'hargajual_reseptur'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'status_implementasi', 'qty_konversi', 'signa'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['r'], 'string', 'max' => 2],
            [['satuankekuatan'], 'string', 'max' => 20],
            [['etiket', 'status_implementasi'], 'string', 'max' => 2000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'resepturdetail_id' => 'Resepturdetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'racikan_id' => 'Racikan ID',
            'satuankecil_id' => 'Satuankecil ID',
            'reseptur_id' => 'Reseptur ID',
            'r' => 'R',
            'rke' => 'Rke',
            'kekuatan_reseptur' => 'Kekuatan Reseptur',
            'satuankekuatan' => 'Satuankekuatan',
            'qty_reseptur' => 'Qty Reseptur',
            'hargasatuan_reseptur' => 'Hargasatuan Reseptur',
            'harganetto_reseptur' => 'Harganetto Reseptur',
            'hargajual_reseptur' => 'Hargajual Reseptur',
            'etiket' => 'Etiket',
            'iter' => 'Iter',
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
            'signa_id' => 'Signa ID',
            'status_implementasi' => 'Status Implementasi',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReseptur()
    {
        return $this->hasOne(ResepturT::className(), ['reseptur_id' => 'reseptur_id']);
    }
}