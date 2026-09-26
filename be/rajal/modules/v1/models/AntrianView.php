<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-17 13:34:37
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-17 13:35:22
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "antrian_v".
 *
 * @property int $antrian_id
 * @property string $no_antrian
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $pendaftaran_id
 * @property int $layarantrian_id
 * @property string $layarantrian_nama
 * @property int $loket_id
 * @property string $loket_nama
 * @property double $panggilan_ke
 * @property string $tgl_antrian
 * @property int $status_antrian
 * @property string $stat_antrian
 * @property int $status_pasien
 * @property string $stat_pasien
 */
class AntrianView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'antrian_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['antrian_id', 'pasien_id', 'ruangan_id', 'carabayar_id', 'penjamin_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'status_antrian', 'status_pasien'], 'default', 'value' => null],
            [['antrian_id', 'pasien_id', 'ruangan_id', 'carabayar_id', 'penjamin_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'status_antrian', 'status_pasien'], 'integer'],
            [['panggilan_ke'], 'number'],
            [['tgl_antrian'], 'safe'],
            [['stat_antrian'], 'string'],
            [['no_antrian'], 'string', 'max' => 6],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama', 'loket_nama'], 'string', 'max' => 50],
            [['layarantrian_nama'], 'string', 'max' => 100],
            [['stat_pasien'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'antrian_id' => 'Antrian ID',
            'no_antrian' => 'No Antrian',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'pendaftaran_id' => 'Pendaftaran ID',
            'layarantrian_id' => 'Layarantrian ID',
            'layarantrian_nama' => 'Layarantrian Nama',
            'loket_id' => 'Loket ID',
            'loket_nama' => 'Loket Nama',
            'panggilan_ke' => 'Panggilan Ke',
            'tgl_antrian' => 'Tgl Antrian',
            'status_antrian' => 'Status Antrian',
            'stat_antrian' => 'Stat Antrian',
            'status_pasien' => 'Status Pasien',
            'stat_pasien' => 'Stat Pasien',
        ];
    }
}
