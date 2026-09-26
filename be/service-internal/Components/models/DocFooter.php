<?php

namespace Integrasi\Components\models;

use Yii;

/**
 * This is the model class for table "docfooter_k".
 *
 * @property int $docfooter_id
 * @property int $profilrs_id
 * @property string $kode_footer
 * @property string $nama_footer
 * @property string $gambar_kiri
 * @property string $gambar_kanan
 * @property int $kertas_id
 * @property string $konten
 * @property bool $flag_berulang
 * @property string $template_footer
 */
class DocFooter extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'docfooter_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['docfooter_id'], 'required'],
            [['docfooter_id', 'profilrs_id', 'kertas_id'], 'default', 'value' => null],
            [['docfooter_id', 'profilrs_id', 'kertas_id'], 'integer'],
            [['nama_footer', 'gambar_kiri', 'gambar_kanan', 'konten', 'template_footer'], 'string'],
            [['flag_berulang'], 'boolean'],
            [['kode_footer'], 'string', 'max' => 255],
            [['docfooter_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'docfooter_id' => 'Docfooter ID',
            'profilrs_id' => 'Profilrs ID',
            'kode_footer' => 'Kode Footer',
            'nama_footer' => 'Nama Footer',
            'gambar_kiri' => 'Gambar Kiri',
            'gambar_kanan' => 'Gambar Kanan',
            'kertas_id' => 'Kertas ID',
            'konten' => 'Konten',
            'flag_berulang' => 'Flag Berulang',
            'template_footer' => 'Template Footer',
        ];
    }
}
