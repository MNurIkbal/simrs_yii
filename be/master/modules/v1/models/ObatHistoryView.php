<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obathistory_v".
 *
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property datetime $tgl_obathistory
 * @property double $harga_dasar
 * @property string $keterangan
 * @property string $nama_pegawai
 */
class ObatHistoryView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'obathistory_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'obatalkes_nama', 'tgl_obathistory', 'harga_dasar', 'keterangan', 'nama_pegawai'], 'default', 'value' => null],
            [['obatalkes_nama', 'keterangan', 'nama_pegawai'], 'string'],
            [['obatalkes_id',], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obat Alkes ID',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'tgl_obathistory' => 'Tanggal History',
            'harga_dasar' => 'Harga Dasar',
            'keterangan' => 'Keterangan',
            'nama_pegawai' => 'Nama Pegawai',
        ];
    }
}
