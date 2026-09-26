<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Kertas;
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
     * @inheritdoc
     */
    public $gambarkiri;
    public $gambarkanan;
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
            [['kode_footer'], 'chkKodeFooter'],
            [['nama_footer'], 'chkNamaFooter'],
        ];
    }

    /**
     * @inheritdoc
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

    public function getKertas()
    {
        return $this->hasOne(Kertas::className(), ['kertas_id' => 'kertas_id']);
    }

    public function chkKodeFooter()
    {
        $kode_footer = $this->kode_footer;
        $model = self::find(true)->where(['LOWER (kode_footer)'=>strtolower($this->kode_footer)])->one();
        if(!empty($model) && ($model->docfooter_id != $this->docfooter_id)){
            $this->addError("kode_footer","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaFooter()
    {
        $nama_footer = $this->nama_footer;
        $model = self::find(true)->where(['LOWER (nama_footer)'=>strtolower($this->nama_footer)])->one();
        if(!empty($model) && ($model->docfooter_id != $this->docfooter_id)){
            $this->addError("nama_footer","Nama Sudah Dipakai");
            return false;
        }
    
        return true;
    }
}
