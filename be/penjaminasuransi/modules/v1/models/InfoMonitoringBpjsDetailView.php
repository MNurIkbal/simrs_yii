<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomonitoringbpjsdetail_v".
 *
 * @property string $jenis
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property int $monitorbpjs_id
 * @property string $kelompok
 * @property int $groupinacbg_id
 * @property string $groupinacbg_nama
 * @property string $tgl_tindakan
 * @property string $daftartindakan_nama
 * @property double $qty_tindakan
 * @property double $tarif_satuan
 * @property double $tarifcyto_tindakan
 * @property double $jml_tarif
 */
class InfoMonitoringBpjsDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infomonitoringbpjsdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis', 'daftartindakan_nama'], 'string'],
            [['pendaftaran_id', 'pasienadmisi_id', 'monitorbpjs_id', 'groupinacbg_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'monitorbpjs_id', 'groupinacbg_id'], 'integer'],
            [['tgl_tindakan'], 'safe'],
            [['qty_tindakan', 'tarif_satuan', 'tarifcyto_tindakan', 'jml_tarif'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['kelompok'], 'string', 'max' => 100],
            [['groupinacbg_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis' => 'Jenis',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'monitorbpjs_id' => 'Monitorbpjs ID',
            'kelompok' => 'Kelompok',
            'groupinacbg_id' => 'Groupinacbg ID',
            'groupinacbg_nama' => 'Groupinacbg Nama',
            'tgl_tindakan' => 'Tgl Tindakan',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'qty_tindakan' => 'Qty Tindakan',
            'tarif_satuan' => 'Tarif Satuan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'jml_tarif' => 'Jml Tarif',
        ];
    }
}
