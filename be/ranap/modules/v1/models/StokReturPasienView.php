<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-07 13:57:13
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-07 13:57:54
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokreturpasien_v".
 *
 * @property int $stokobatpasien_id
 * @property int $obatalkespasien_id
 * @property int $penjualanresep_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $noresep
 * @property int $obatalkes_id
 * @property string $nama_obat
 * @property string $signa
 * @property int $stok_retur
 * @property int $stok_retur_sisa
 * @property double $hargasatuan_oa
 * @property double $hargajual_oa
 * @property bool $is_retur
 */
class StokReturPasienView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokreturpasien_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stokobatpasien_id', 'obatalkespasien_id', 'penjualanresep_id', 'pendaftaran_id', 'pasienadmisi_id', 'obatalkes_id', 'stok_retur', 'stok_retur_sisa'], 'default', 'value' => null],
            [['stokobatpasien_id', 'obatalkespasien_id', 'penjualanresep_id', 'pendaftaran_id', 'pasienadmisi_id', 'obatalkes_id', 'stok_retur', 'stok_retur_sisa'], 'integer'],
            [['noresep'], 'string'],
            [['hargasatuan_oa', 'hargajual_oa'], 'number'],
            [['is_retur'], 'boolean'],
            [['nama_obat', 'signa'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokobatpasien_id' => 'Stokobatpasien ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'noresep' => 'Noresep',
            'obatalkes_id' => 'Obatalkes ID',
            'nama_obat' => 'Nama Obat',
            'signa' => 'Signa',
            'stok_retur' => 'Stok Retur',
            'stok_retur_sisa' => 'Stok Retur Sisa',
            'hargasatuan_oa' => 'Hargasatuan Oa',
            'hargajual_oa' => 'Hargajual Oa',
            'is_retur' => 'Is Retur',
        ];
    }
}
