<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "invoicekso_v".
 *
 * @property int $tandabuktibayar_id
 * @property string $no_invoice
 * @property string $no_pendaftaran
 * @property string $nama_pasien
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property double $jmlpembayaran
 * @property bool $is_kso
 */
class InvoiceksoView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoicekso_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tandabuktibayar_id'], 'default', 'value' => null],
            [['tandabuktibayar_id'], 'integer'],
            [['jmlpembayaran'], 'number'],
            [['is_kso'], 'boolean'],
            [['no_invoice', 'nama_pasien', 'ruangan_nama'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['instalasi_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tandabuktibayar_id' => 'Tandabuktibayar ID',
            'no_invoice' => 'No Invoice',
            'no_pendaftaran' => 'No Pendaftaran',
            'nama_pasien' => 'Nama Pasien',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'jmlpembayaran' => 'Jmlpembayaran',
            'is_kso' => 'Is Kso',
        ];
    }
}
