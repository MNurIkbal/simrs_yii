<?php

namespace Doco\models;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\models\JadwalDokter;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "antrian_t".
 *
 * @property int $antrian_id
 * @property int $ruangan_id
 * @property int $pendaftaran_id
 * @property string $tgl_antrian
 * @property string $no_antrian
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
 * @property int $pasien_id
 * @property int $pegawai_id
 * @property int $jadwaldokter_id
 * @property bool $is_konsulpoli
 * @property int $jenisantrian_id
 * @property bool $is_online

 */
class AntrianKonsul extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antrian_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['ruangan_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id', 'pegawai_id','jadwaldokter_id', 'jenisantrian_id', 'is_online'], 'default', 'value' => null],
            [['ruangan_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id', 'pegawai_id','jadwaldokter_id', 'jenisantrian_id', 'is_online'], 'integer'],
            [['instalasi_id','tgl_antrian', 'created_date', 'last_modified_date', 'deleted_date', 'skip_kuota'], 'safe'],
            [['is_konsulpoli', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['no_antrian'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'antrian_id' => 'Antrian ID',
            'ruangan_id' => 'Ruangan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'tgl_antrian' => 'Tgl Antrian',
            'no_antrian' => 'No Antrian',
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
            'pasien_id' => 'Pasien ID',
            'penjamin_id' => 'Penjamin ID',
            'pegawai_id' => 'Pegawai ID',
            'jadwaldokter_id' => 'Jadwal Dokter ID',
            'is_konsulpoli' => 'Is Konsul Poli',
        ];
    }

    public function checkPoli($data,$startDate,$endDate )
    {
        foreach ($data as $k => $val) {
            $jadwal_kuota = self::find()
            ->where(['jadwaldokter_id' => $val['jadwaldokter_id']])
            ->andWhere(['between', 'tgl_antrian',$startDate, $endDate])
            ->count();
            $temp_kuota = $val['kuota_online'] - $jadwal_kuota;
            $data[$k]['temp_kuota'] = $temp_kuota < 0 ? 0 : $temp_kuota;

        }
        return $data;
    }

    public static function checkJadwal($params = [])
    {
        $date_now = is_null($params['date']) ? DocoHelpers::getTanggalIndonesia(date('Y-m-d')) : DocoHelpers::getTanggalIndonesia(date('Y-m-d', strtotime($params['date'])));
        $mapp_hari = DocoConstants::$look_hari;
        $jam = date('H:i:s');
        $hari = isset($mapp_hari[$date_now['urutan_hari']]) ? $mapp_hari[$date_now['urutan_hari']] : 0;

        $result = JadwalDokter::find()
            ->select([
                'jadwaldokter_m.jadwaldokter_id',
                'jadwaldokter_m.pegawai_id',
                'jadwaldokter_m.kuota_online',
                'pegawai_m.nama_pegawai',
                'jadwaldokter_m.jadwaldokter_mulai',
                'jadwaldokter_m.jadwaldokter_tutup',

            ])
            ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = jadwaldokter_m.pegawai_id')
            ->innerJoin('jadwalbukapoli_m', 'jadwalbukapoli_m.jadwalbukapoli_id = jadwaldokter_m.jadwalbukapoli_id')
            ->where(['jadwaldokter_m.is_deleted' => FALSE])
            ->andwhere(['jadwaldokter_m.pegawai_id' => $params['dokter_id']])
            ->andwhere(['jadwalbukapoli_m.hari' => $hari])
            ->andWhere(['jadwalbukapoli_m.ruangan_id' => $params['ruangan_id']]);
            
            if (!isset($params['with_self_pegawai']) && $params['with_self_pegawai'] == false) {
                $result = $result->andWhere(['NOT IN', 'pegawai_m.pegawai_id', $params['pegawai_id']]);
            }

            $result = $result->groupBy('jadwaldokter_m.pegawai_id,pegawai_m.nama_pegawai, jadwaldokter_m.kuota_online, jadwaldokter_m.jadwaldokter_id')
            ->asArray()
            ->all();

        foreach ($result as $k => $val) {
            $jadwal_kuota = self::find()
            ->where(['jadwaldokter_id' => $val['jadwaldokter_id']])
            ->andWhere(['between', 'tgl_antrian',$params['startDate'], $params['endDate']])
            ->andWhere(['jenisantrian_id'=>312])
            ->count();
            $temp_kuota = $val['kuota_online'] - $jadwal_kuota;
            $result[$k]['temp_kuota'] = $temp_kuota < 0 ? 0 : $temp_kuota;
        }

        $data = [
            'data-jadwal' => $result
        ];
        return $data;
    }
}
