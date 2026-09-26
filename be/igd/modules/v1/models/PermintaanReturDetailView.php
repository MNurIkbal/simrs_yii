<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-08 14:40:20
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-08 14:41:34
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "permintaanreturdetail_v".
 *
 * @property int $permintaanretur_id
 * @property int $permintaanreturdetail_id
 * @property int $obatalkespasien_id
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $signa
 * @property string $signa_nama
 * @property int $qty_retur
 * @property string $alasan
 * @property int $returresep_id
 * @property int $returresepdetail_id
 * @property double $qty_approve
 * @property double $hargasatuan
 * @property double $total
 * @property string $tgl_approve
 * @property string $alasan_retur
 */
class PermintaanReturDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permintaanreturdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaanretur_id', 'permintaanreturdetail_id', 'obatalkespasien_id', 'obatalkes_id', 'signa', 'qty_retur', 'returresep_id', 'returresepdetail_id'], 'default', 'value' => null],
            [['permintaanretur_id', 'permintaanreturdetail_id', 'obatalkespasien_id', 'obatalkes_id', 'signa', 'qty_retur', 'returresep_id', 'returresepdetail_id'], 'integer'],
            [['alasan', 'alasan_retur'], 'string'],
            [['qty_approve', 'hargasatuan', 'total'], 'number'],
            [['tgl_approve'], 'safe'],
            [['obatalkes_nama', 'signa_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaanretur_id' => 'Permintaanretur ID',
            'permintaanreturdetail_id' => 'Permintaanreturdetail ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'signa' => 'Signa',
            'signa_nama' => 'Signa Nama',
            'qty_retur' => 'Qty Retur',
            'alasan' => 'Alasan',
            'returresep_id' => 'Returresep ID',
            'returresepdetail_id' => 'Returresepdetail ID',
            'qty_approve' => 'Qty Approve',
            'hargasatuan' => 'Hargasatuan',
            'total' => 'Total',
            'tgl_approve' => 'Tgl Approve',
            'alasan_retur' => 'Alasan Retur',
        ];
    }
}
