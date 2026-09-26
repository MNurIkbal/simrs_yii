<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokkuotadokter_t".
 *
 * @property int $stokkuotadokter_id
 * @property int $jadwaldokter_id
 * @property int $jadwaldoktertambahan_id
 * @property int $antrian_id
 * @property int $batalantrian_id
 * @property int $konsulpoli_id
 * @property int $batalkonsolpoli_id
 * @property int $buatjanjipoli_id
 * @property int $bataljanjipoli_id
 * @property string $tgltransaksi_in
 * @property string $tgltransaksi_out
 * @property double $kuota_in
 * @property double $kuota_out
 * @property bool $flag
 * @property int $kuotaasal_id
 * @property bool $is_online
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
 * @property double $kuota_bpjs_online
 * @property double $kuota_nonbpjs_online
 * @property double $kuota_bpjs_offline
 * @property double $kuota_nonbpjs_offline
 * @property double $kuota_out_bpjs
 * @property double $kuota_out_nonbpjs
 */
class StokKuotaDokter extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokkuotadokter_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwaldokter_id', 'jadwaldoktertambahan_id', 'antrian_id', 'batalantrian_id', 'konsulpoli_id', 'batalkonsolpoli_id', 'buatjanjipoli_id', 'bataljanjipoli_id', 'kuotaasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'tgltransaksi_in', 'tgltransaksi_out'], 'default', 'value' => null],
            [['jadwaldokter_id', 'jadwaldoktertambahan_id', 'antrian_id', 'batalantrian_id', 'konsulpoli_id', 'batalkonsolpoli_id', 'buatjanjipoli_id', 'bataljanjipoli_id', 'kuotaasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgltransaksi_in', 'tgltransaksi_out', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kuota_in', 'kuota_out', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'kuota_nonbpjs_offline', 'kuota_bpjs_offline', 'kuota_out_bpjs', 'kuota_out_nonbpjs'], 'default', 'value' => 0],
            [['kuota_in', 'kuota_out', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'kuota_nonbpjs_offline', 'kuota_bpjs_offline', 'kuota_out_bpjs', 'kuota_out_nonbpjs'], 'number'],
            [['flag', 'is_online', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokkuotadokter_id' => 'Stokkuotadokter ID',
            'jadwaldokter_id' => 'Jadwaldokter ID',
            'jadwaldoktertambahan_id' => 'Jadwaldoktertambahan ID',
            'antrian_id' => 'Antrian ID',
            'batalantrian_id' => 'Batalantrian ID',
            'konsulpoli_id' => 'Konsulpoli ID',
            'batalkonsolpoli_id' => 'Batalkonsolpoli ID',
            'buatjanjipoli_id' => 'Buatjanjipoli ID',
            'bataljanjipoli_id' => 'Bataljanjipoli ID',
            'tgltransaksi_in' => 'Tgltransaksi In',
            'tgltransaksi_out' => 'Tgltransaksi Out',
            'kuota_in' => 'Kuota In',
            'kuota_out' => 'Kuota Out',
            'flag' => 'Flag',
            'kuotaasal_id' => 'Kuotaasal ID',
            'is_online' => 'Is Online',
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
