<?php

namespace Doco\models\radiologi;

use Yii;
use Doco\Repositories\HasilPemeriksaanRadViewRepositories;
/**
 * This is the model class for table "hasilpemeriksaanrad_v".
 *
 * @property string $jenis
 * @property int $tindakanpelayanan_id
 * @property int $pasienmasukpenunjang_id
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property bool $cyto_tindakan
 * @property string $no_hasilrad
 * @property string $tgl_ambilfoto
 * @property string $tgl_uploadhasil
 * @property string $tgl_hasilrad
 * @property string $kesan
 * @property string $kesimpulan
 * @property int $penanggungjawab_id
 * @property string $penanggung_jawab
 */
class HasilPemeriksaanRadView extends \Doco\components\ActiveRepositories
{
    public $_repositori = HasilPemeriksaanRadViewRepositories::class;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanrad_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis', 'tipepaket_nama', 'kesan', 'kesimpulan'], 'string'],
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'tipepaket_id', 'daftartindakan_id', 'penanggungjawab_id'], 'default', 'value' => null],
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'tipepaket_id', 'daftartindakan_id', 'penanggungjawab_id'], 'integer'],
            [['cyto_tindakan'], 'boolean'],
            [['tgl_ambilfoto', 'tgl_uploadhasil', 'tgl_hasilrad'], 'safe'],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['no_hasilrad'], 'string', 'max' => 255],
            [['penanggung_jawab'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis' => 'Jenis',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'cyto_tindakan' => 'Cyto Tindakan',
            'no_hasilrad' => 'No Hasilrad',
            'tgl_ambilfoto' => 'Tgl Ambilfoto',
            'tgl_uploadhasil' => 'Tgl Uploadhasil',
            'tgl_hasilrad' => 'Tgl Hasilrad',
            'kesan' => 'Kesan',
            'kesimpulan' => 'Kesimpulan',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'penanggung_jawab' => 'Penanggung Jawab',
        ];
    }
}
