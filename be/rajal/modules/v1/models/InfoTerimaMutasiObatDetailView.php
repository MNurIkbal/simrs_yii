<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoterimamutasiobatdetail_v".
 *
 * @property int $terimamutasiobat_id
 * @property int $terimamutasiobatdetail_id
 * @property string $noterimamutasi
 * @property int $ruanganpenerima_id
 * @property string $ruang_penerima
 * @property int $ruanganasal_id
 * @property string $ruang_asal
 * @property int $pegawaipenerima_id
 * @property string $pegawai_penerima
 * @property int $pegawaimengetahui_id
 * @property string $pegawai_mengetahui
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $satuankecil_id
 * @property string $satuanunit_nama
 * @property double $jmlterima
 * @property double $harganettoterima
 * @property double $hargajualterima
 */
class InfoTerimaMutasiObatDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infoterimamutasiobatdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['terimamutasiobat_id', 'terimamutasiobatdetail_id', 'ruanganpenerima_id', 'ruanganasal_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'obatalkes_id', 'satuankecil_id'], 'default', 'value' => null],
            [['terimamutasiobat_id', 'terimamutasiobatdetail_id', 'ruanganpenerima_id', 'ruanganasal_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'obatalkes_id', 'satuankecil_id'], 'integer'],
            [['satuanunit_nama'], 'string'],
            [['jmlterima', 'harganettoterima', 'hargajualterima'], 'number'],
            [['noterimamutasi'], 'string', 'max' => 20],
            [['ruang_penerima', 'ruang_asal', 'pegawai_penerima', 'pegawai_mengetahui'], 'string', 'max' => 50],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'terimamutasiobat_id' => 'Terimamutasiobat ID',
            'terimamutasiobatdetail_id' => 'Terimamutasiobatdetail ID',
            'noterimamutasi' => 'Noterimamutasi',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'ruang_penerima' => 'Ruang Penerima',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruang_asal' => 'Ruang Asal',
            'pegawaipenerima_id' => 'Pegawaipenerima ID',
            'pegawai_penerima' => 'Pegawai Penerima',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'pegawai_mengetahui' => 'Pegawai Mengetahui',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanunit_nama' => 'Satuanunit Nama',
            'jmlterima' => 'Jmlterima',
            'harganettoterima' => 'Harganettoterima',
            'hargajualterima' => 'Hargajualterima',
            'tglkadaluarsa' => 'Tanggal Kadaluarsa',
        ];
    }
}
