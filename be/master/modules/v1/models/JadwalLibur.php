<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jadwaldokter_m".
 *
 * @property int $jadwallibur_id
 * @property string $tgl_libur
 * @property string $ket_libur
 * @property bool $is_liburnasional
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
class JadwalLibur extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jadwallibur_m';
    }

    public function rules()
    {
        return [
            [[
                'tgl_libur', 
                'ket_libur',
            ], 'required'],
            [[
                'tgl_libur', 
                'ket_libur',
                'is_liburnasional',
                'created_by',
                'created_date',
                'is_deleted',
                'is_active',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwallibur_id' => 'Jadwal Libur ID',
            'tgl_libur' => 'Tanggal Libur',
            'ket_libur' => 'Keterangan Libur',
            'is_liburnasional' => 'Libur Nasional',
        ];
    }
}
