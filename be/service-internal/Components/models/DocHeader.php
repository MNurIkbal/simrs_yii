<?php

namespace Integrasi\Components\models;

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
 * @property string $template_header
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class DocHeader extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'docheader_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profilrs_id', 'kertas_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['profilrs_id', 'kertas_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama_header', 'logo_kiri', 'logo_kanan', 'konten', 'template_header', 'additional_data'], 'string'],
            [['flag_berulang', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kode_header', 'tinggi_logo_kanan', 'panjang_logo_kanan', 'tinggi_logo_kiri', 'panjang_logo_kiri'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
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
            'tinggi_logo_kanan' => 'Tinggi Logo Kanan',
            'panjang_logo_kanan' => 'Panjang Logo Kanan',
            'tinggi_logo_kiri' => 'Tinggi Logo Kiri',
            'panjang_logo_kiri' => 'Panjang Logo Kiri',
            'template_header' => 'Template Header',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
