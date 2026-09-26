<?php

namespace app\modules\master\models;

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
class DocFooterForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */

    public $docfooter_id;
    public $profilrs_id;
    public $kode_footer;
    public $nama_footer;
    public $gambar_kiri;
    public $gambar_kanan;
    public $kertas_id;
    public $konten;
    public $flag_berulang;
    public $template_footer;
    public $gambar;

    public static function tableName()
    {
        return 'docfooter_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profilrs_id', 'kertas_id'], 'default', 'value' => null],
            [['docfooter_id', 'profilrs_id', 'kertas_id'], 'integer'],
            [['nama_footer', 'gambar_kiri', 'gambar_kanan', 'konten', 'template_footer'], 'string'],
            [['flag_berulang'], 'boolean'],
            [['kode_footer'], 'string', 'max' => 255],
            [['docfooter_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'docfooter_id' => Yii::t('fe','Docfooter ID'),
            'profilrs_id' => Yii::t('fe','Jenis Footer'),
            'kode_footer' => Yii::t('fe','Kode Footer'),
            'nama_footer' => Yii::t('fe','Nama Footer'),
            'gambar_kiri' => Yii::t('fe','Gambar Kiri'),
            'gambar_kanan' => Yii::t('fe','Gambar Kanan'),
            'kertas_id' => Yii::t('fe','Kertas ID'),
            'konten' => Yii::t('fe','Konten'),
            'flag_berulang' => Yii::t('fe','Footer Berulang'),
            'template_footer' => Yii::t('fe','Template Footer'),
        ];
    }
}
