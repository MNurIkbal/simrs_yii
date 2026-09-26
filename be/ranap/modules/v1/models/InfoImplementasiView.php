<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoimplementasi_v".
 *
 * @property string $tipe_implementasi
 * @property int $implementasi_id
 * @property int $instruksi_id
 * @property string $catatan_implementasi
 * @property int $tindakanpelayanan_id
 * @property string $tgl_implementasi
 * @property int $daftartindakan_id
 * @property string $implementasi
 * @property string $paket
 * @property string $tindakan
 * @property double $qty
 * @property bool $cyto
 * @property string $perawat1_id
 * @property int $perawat2_id
 * @property string $perawat_1
 * @property string $perawat_2
 */
class InfoImplementasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoimplementasi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe_implementasi', 'catatan_implementasi', 'implementasi', 'paket', 'tindakan'], 'string'],
            [['implementasi_id', 'instruksi_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'perawat1_id', 'perawat2_id'], 'default', 'value' => null],
            [['implementasi_id', 'instruksi_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'perawat1_id', 'perawat2_id'], 'integer'],
            [['tgl_implementasi'], 'safe'],
            [['qty'], 'number'],
            [['cyto'], 'boolean'],
            [['perawat_1', 'perawat_2'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe_implementasi' => 'Tipe Implementasi',
            'implementasi_id' => 'Implementasi ID',
            'instruksi_id' => 'Instruksi ID',
            'catatan_implementasi' => 'Catatan Implementasi',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'tgl_implementasi' => 'Tgl Implementasi',
            'daftartindakan_id' => 'Daftartindakan ID',
            'implementasi' => 'Implementasi',
            'paket' => 'Paket',
            'tindakan' => 'Tindakan',
            'qty' => 'Qty',
            'cyto' => 'Cyto',
            'perawat1_id' => 'Perawat1 ID',
            'perawat2_id' => 'Perawat2 ID',
            'perawat_1' => 'Perawat 1',
            'perawat_2' => 'Perawat 2',
        ];
    }
}
