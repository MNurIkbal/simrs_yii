<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obathistory_r".
 *
 * @property int $obathistory_id
 * @property datetime $tgl_obathistory
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property double $harga_dasar
 * @property int $keterangan
 * @property int $last_modified_by
 */
class ObatHistoryR extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'obathistory_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obathistory_id', 'tgl_obathistory', 'obatalkes_id', 'obatalkes_nama', 'harga_dasar', 'keterangan', 'last_modified_by'], 'default', 'value' => null],
            [['obatalkes_nama'], 'string'],
            [['obathistory_id', 'obatalkes_id', 'keterangan', 'last_modified_by'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'obathistory_id' => 'Obat History ID',
            'tgl_obathistory' => 'Tanggal History',
            'obatalkes_id' => 'Obat Alkes ID',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'harga_dasar' => 'Harga Dasar',
            'keterangan' => 'Keterangan',
            'last_modified_by' => 'Terakhir Diperbaharui Oleh',
        ];
    }
}
