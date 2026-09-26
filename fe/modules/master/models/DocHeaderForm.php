<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "docheader_k".
 *
 * @property int $docheader_id
 * @property int $profilrs_id
 * @property string $kode_header
 * @property string $nama_header
 * @property string $logo_kiri
 * @property string $logo_kanan
 * @property int $kertas_id
 * @property string $konten
 * @property bool $flag_berulang
 * @property string $tinggi_logo_kanan
 * @property string $panjang_logo_kanan
 * @property string $tinggi_logo_kiri
 * @property string $panjang_logo_kiri
 */
class DocHeaderForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */

    public $docheader_id;
    public $profilrs_id;
    public $kode_header;
    public $nama_header;
    public $logo_kiri;
    public $logo_kanan;
    public $kertas_id;
    public $konten;
    public $flag_berulang;
    public $template_header;
    public $logo;
    
    public static function tableName()
    {
        return 'docheader_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profilrs_id', 'kertas_id'], 'default', 'value' => null],
            [['docheader_id', 'profilrs_id', 'kertas_id'], 'integer'],
            [['kode_header', 'nama_header', 'logo_kiri', 'logo_kanan', 'konten'], 'string'],
            [['flag_berulang'], 'boolean'],
            [['docheader_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'docheader_id' => Yii::t('fe','Docheader ID'),
            'profilrs_id' => Yii::t('fe','Jenis Header'),
            'kode_header' => Yii::t('fe','Kode Header'),
            'nama_header' => Yii::t('fe','Nama Header'),
            'logo_kiri' => Yii::t('fe','Logo Kiri Atas'),
            'logo_kanan' => Yii::t('fe','Logo Kanan Atas'),
            'kertas_id' => Yii::t('fe','Jenis Kertas'),
            'konten' => Yii::t('fe','Konten'),
            'flag_berulang' => Yii::t('fe','Header Berulang'),
            'template_header' => Yii::t('fe','Template Header')
        ];
    }
}
