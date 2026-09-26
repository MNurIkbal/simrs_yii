<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-08 14:33:26
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-08 14:37:34
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "permintaanretur_v".
 *
 * @property int $permintaanretur_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_permintaanretur
 * @property string $tgl_permintaanretur
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $pegawairetur_id
 * @property string $nama_pegawai
 * @property string $status
 */
class PermintaanReturView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permintaanretur_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaanretur_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'pegawairetur_id'], 'default', 'value' => null],
            [['permintaanretur_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'pegawairetur_id'], 'integer'],
            [['tgl_permintaanretur'], 'safe'],
            [['status'], 'string'],
            [['no_permintaanretur'], 'string', 'max' => 255],
            [['ruangan_nama', 'nama_pegawai'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaanretur_id' => 'Permintaanretur ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_permintaanretur' => 'No Permintaanretur',
            'tgl_permintaanretur' => 'Tgl Permintaanretur',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawairetur_id' => 'Pegawairetur ID',
            'nama_pegawai' => 'Nama Pegawai',
            'status' => 'Status',
        ];
    }
}
