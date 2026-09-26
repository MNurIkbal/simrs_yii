<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for view "infoadjustmenobat_v".
 *
 * @property int $adjusmenobat_id
 * @property string $no_adjusmen
 * @property string $tgl_adjusmen
 * @property int $jenis_adjusmen
 * @property string $jenis_adjusmen_nama
 * @property int $peg_mengetahui_id
 * @property string $peg_mengetahui_nama
 * @property int $peg_menyetujui_id
 * @property string $peg_menyetujui_nama
 */
class InfoAdjustmentObatView extends \Doco\components\DocoActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoadjusmenobat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusmenobat_id' => 'ID Adjustment Obat',
            'no_adjusmen' => 'No. Adjustment',
            'tgl_adjusmen' => 'Tanggal Adjustment',
            'jenis_adjusmen' => 'ID Jenis Adjustment',
            'jenis_adjusmen_nama' => 'Jenis Adjustment',
            'peg_mengetahui_id' => 'ID Pegawai Mengetahui',
            'peg_mengetahui_nama' => 'Nama Pegawai Mengetahui',
            'peg_menyetujui_id' => 'ID Pegawai Menyetujui',
            'peg_menyetujui_nama' => 'Nama Pegawai Menyetujui'
        ];
    }
}
