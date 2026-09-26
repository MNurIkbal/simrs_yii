<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokopnamebarang_v".
 *
 * @property int $stokopnamebarang_id
 * @property int $formsobarang_id
 * @property int $ruangan_id
 * @property int $pegmengetahui_id
 * @property int $petugas_id
 * @property int $instalasi_id
 * @property string $jenisstokopname
 * @property string $instalasi_nama
 * @property string $jenis_stokopname
 * @property string $ruangan_nama
 * @property string $tglstokopname
 * @property string $nostokopname
 * @property double $totalharga_fisik
 * @property double $totalharga_sistem
 * @property double $selisih
 */
class InfoStokOpnameBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infostokopnamebarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stokopnamebarang_id', 'formsobarang_id', 'ruangan_id', 'pegmengetahui_id', 'petugas_id', 'instalasi_id'], 'default', 'value' => null],
            [['stokopnamebarang_id', 'formsobarang_id', 'ruangan_id', 'pegmengetahui_id', 'petugas_id', 'instalasi_id'], 'integer'],
            [['jenisstokopname', 'nostokopname', 'noformulir'], 'string'],
            [['tglstokopname, periode_awal, periode_akhir'], 'safe'],
            [['totalharga_fisik', 'totalharga_sistem', 'selisih'], 'number'],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
            [['jenis_stokopname'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokopnamebarang_id' => 'Stokopnamebarang ID',
            'formsobarang_id' => 'Formsobarang ID',
            'ruangan_id' => 'Ruangan ID',
            'pegmengetahui_id' => 'Pegmengetahui ID',
            'petugas_id' => 'Petugas ID',
            'instalasi_id' => 'Instalasi ID',
            'jenisstokopname' => 'Jenisstokopname',
            'instalasi_nama' => 'Instalasi Nama',
            'jenis_stokopname' => 'Jenis Stokopname',
            'ruangan_nama' => 'Ruangan Nama',
            'tglstokopname' => 'Tglstokopname',
            'nostokopname' => 'Nostokopname',
            'totalharga_fisik' => 'Totalharga Fisik',
            'totalharga_sistem' => 'Totalharga Sistem',
            'selisih' => 'Selisih',
        ];
    }
}
