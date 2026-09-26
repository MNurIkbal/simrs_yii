<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Kertas;

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
class DocHeader extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    // public $rowNum;
    public $logokiri;
    public $logokanan;
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
            // [['docheader_id'], 'required'],
            [['profilrs_id', 'kertas_id'], 'default', 'value' => null],
            [['docheader_id', 'profilrs_id', 'kertas_id'], 'integer'],
            [['kode_header', 'nama_header', 'logo_kiri', 'logo_kanan', 'konten'], 'string'],
            [['flag_berulang'], 'boolean'],
            [['template_header','flag_berulang'], 'safe'],
            // [['tinggi_logo_kanan', 'panjang_logo_kanan', 'tinggi_logo_kiri', 'panjang_logo_kiri'], 'string', 'max' => 255],
            [['docheader_id'], 'unique'],
            [['kode_header'], 'chkKodeHeader'],
            [['nama_header'], 'chkNamaHeader'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'docheader_id' => 'Docheader ID',
            'profilrs_id' => 'Profilrs ID',
            'kode_header' => 'Kode Header',
            'nama_header' => 'Nama Header',
            'logo_kiri' => 'Logo Kiri',
            'logo_kanan' => 'Logo Kanan',
            'kertas_id' => 'Kertas ID',
            'konten' => 'Konten',
            'flag_berulang' => 'Flag Berulang',
            // 'tinggi_logo_kanan' => 'Tinggi Logo Kanan',
            // 'panjang_logo_kanan' => 'Panjang Logo Kanan',
            // 'tinggi_logo_kiri' => 'Tinggi Logo Kiri',
            // 'panjang_logo_kiri' => 'Panjang Logo Kiri',
            'template_header' => 'Template'
        ];
    }

    public function getKertas()
    {
        return $this->hasOne(Kertas::className(), ['kertas_id' => 'kertas_id']);
    }

    public function chkKodeHeader()
    {
        $kode_header = $this->kode_header;
        $model = self::find(true)->where(['LOWER (kode_header)'=>strtolower($this->kode_header)])->one();
        if(!empty($model) && ($model->docheader_id != $this->docheader_id)){
            $this->addError("kode_header","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNamaHeader()
    {
        $nama_header = $this->nama_header;
        $model = self::find(true)->where(['LOWER (nama_header)'=>strtolower($this->nama_header)])->one();
        if(!empty($model) && ($model->docheader_id != $this->docheader_id)){
            $this->addError("nama_header","Nama Sudah Dipakai");
            return false;
        }
    
        return true;
    }
}
