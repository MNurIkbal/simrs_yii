<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jadwaldokter_m".
 *
 * @property int $jadwaldokter_id
 * @property int $pegawai_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property string $jadwaldokter_tgl ga dipake
 * @property string $jadwaldokter_waktupelayanan
 * @property string $jadwaldokter_mulai
 * @property string $jadwaldokter_tutup
 * @property int $maximumantrian
 * @property int $shift_id
 * @property int $jadwalbukapoli_id
 * @property int $jadwaldokter_hari
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
 * @property int $kuota_online
 * @property bool $is_loaddokter
 * @property int $jumlah_loaddokter
 * @property InstalasiM $instalasi
 * @property PegawaiM $pegawai
 * @property RuanganM $ruangan
 * @property int $kuota_total
 * @property int $kuota_bpjs_total
 * @property int $kuota_nonbpjs_total
 * @property int $kuota_bpjs_offline
 * @property int $kuota_nonbpjs_offline
 * @property int $kuota_bpjs_online
 * @property int $kuota_nonbpjs_online
 */
class JadwalDokter extends \Doco\components\DocoActiveRecord
{
    public function scenarios()
    {
        return [
            'create' => ['pegawai_id','instalasi_id','ruangan_id','jadwaldokter_tgl','jadwaldokter_waktupelayanan','jadwaldokter_mulai','jadwaldokter_tutup','maximumantrian','jadwalbukapoli_id','additional_data','created_date','created_by','modified_count','last_modified_date','last_modified_by','is_deleted','is_active','is_loaddokter','jumlah_loaddokter','deleted_date','deleted_by','kuota_online','notifikasi_id', 'kuota_total', 'kuota_bpjs_total', 'kuota_nonbpjs_total', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'is_bersedia'],
            'update' => ['jadwaldokter_id','pegawai_id','instalasi_id','ruangan_id','jadwaldokter_tgl','jadwaldokter_waktupelayanan','jadwaldokter_mulai','jadwaldokter_tutup','maximumantrian','jadwalbukapoli_id','additional_data','created_date','created_by','modified_count','last_modified_date','last_modified_by','is_deleted','is_active','is_loaddokter','jumlah_loaddokter','deleted_date','deleted_by','kuota_online','notifikasi_id', 'kuota_total', 'kuota_bpjs_total', 'kuota_nonbpjs_total', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'is_bersedia'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jadwaldokter_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'instalasi_id', 'ruangan_id', 'maximumantrian', 'shift_id', 'jadwalbukapoli_id', 'jadwaldokter_hari', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kuota_online', 'kuota_total', 'kuota_bpjs_total', 'kuota_nonbpjs_total', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online'], 'default', 'value' => null],
            [['pegawai_id', 'instalasi_id', 'ruangan_id', 'maximumantrian', 'shift_id', 'jadwalbukapoli_id', 'jadwaldokter_hari', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kuota_online', 'kuota_total', 'kuota_bpjs_total', 'kuota_nonbpjs_total', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online'], 'integer'],
            [['instalasi_id', 'ruangan_id'], 'required'],
            [['jadwaldokter_tgl', 'jadwaldokter_mulai', 'jadwaldokter_tutup', 'created_date', 'last_modified_date', 'deleted_date', 'kuota_total', 'kuota_bpjs_total', 'kuota_nonbpjs_total', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'is_bersedia'], 'safe'],
            [['additional_data'], 'string'],
            [['jumlah_loaddokter'], 'integer'],
            [['is_deleted', 'is_active','is_loaddokter', 'is_bersedia'], 'boolean'],
            [['jadwaldokter_waktupelayanan'], 'string', 'max' => 50],
            [['jadwaldokter_mulai','jadwaldokter_tutup'],'cekJadwalDokterAktif'],
            // [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => InstalasiM::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            // [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }
   
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jadwaldokter_id' => 'Jadwaldokter ID',
            'pegawai_id' => 'Pegawai ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'jadwaldokter_tgl' => 'Jadwaldokter Tgl',
            'jadwaldokter_waktupelayanan' => 'Jadwaldokter Waktupelayanan',
            'jadwaldokter_mulai' => 'Jadwaldokter Mulai',
            'jadwaldokter_tutup' => 'Jadwaldokter Tutup',
            'maximumantrian' => 'Maximumantrian',
            'shift_id' => 'Shift ID',
            'jadwalbukapoli_id' => 'Jadwalbukapoli ID',
            'jadwaldokter_hari' => 'Jadwaldokter Hari',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'jumlah_loaddokter' => 'Jumlah / Menit',
            'is_loaddokter' => 'is_loaddokter',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'kuota_online' => 'Kuota Online',
            'kuota_total' => 'Kuota Total',
            'kuota_bpjs_total' => 'Kuota Bpjs Total',
            'kuota_nonbpjs_total' => 'Kuota Non Bpjs Total',
            'kuota_bpjs_offline' => 'Kuota Non Bpjs Offline',
            'kuota_nonbpjs_offline' => 'Kuota Non Bpjs Offline',
            'kuota_bpjs_online' => 'Kuota Non Bpjs Online',
            'kuota_nonbpjs_online' => 'Kuota Non Bpjs Online',
            'is_bersedia' => 'Bersedia Appointment',
        ];
    }

    public function cekJadwalDokterAktif($attribute,$params)
    {
        $query = "
                SELECT 
                    jadwal.jadwaldokter_id,
                    hari_lookup.lookup_name as hari_nama, 
                    to_char(jadwal.jadwaldokter_mulai,'HH24:MI')as jam_mulai, 
                    to_char(jadwal.jadwaldokter_tutup,'HH24:MI')as jam_tutup
                FROM jadwaldokter_m jadwal
                JOIN jadwalbukapoli_m bukapoli ON jadwal.jadwalbukapoli_id = bukapoli.jadwalbukapoli_id
                JOIN lookup_m hari_lookup ON bukapoli.hari = hari_lookup.lookup_id
                WHERE 
                    jadwal.pegawai_id = '{$this->pegawai_id}' 
                    AND bukapoli.jadwalbukapoli_id = '{$this->jadwalbukapoli_id}'
                    AND jadwal.is_active = true 
                    AND jadwal.is_deleted = false
            ";
        if($this->scenario == 'update'){
            $query .= "
                 AND jadwaldokter_id <> '{$this->jadwaldokter_id}'
            ";
        }
        $data = Yii::$app->db->createCommand($query)->queryAll();

        if(is_array($data) && count($data)>0 && $this->is_bersedia == false){
            $jadwaldokter_mulai = strtotime($this->jadwaldokter_mulai);
            $jadwaldokter_tutup = strtotime($this->jadwaldokter_tutup);
            foreach ($data as $k_data => $v_data) {
                $jam_mulai = strtotime($v_data['jam_mulai']);
                $jam_tutup = strtotime($v_data['jam_tutup']);
                if( 
                    (($jadwaldokter_mulai >= $jam_mulai) && ($jadwaldokter_mulai < $jam_tutup)) ||
                    (($jadwaldokter_tutup > $jam_mulai) && ($jadwaldokter_tutup <= $jam_tutup)) ||
                    (($jadwaldokter_mulai >= $jam_mulai) && ($jadwaldokter_tutup <= $jam_tutup)) ||
                    (($jadwaldokter_mulai <= $jam_mulai) && ($jadwaldokter_tutup >= $jam_tutup)) 
                    // //ada jadwal buka baru di tengah2 jam buka dan jam tutup db
                    // (($jadwaldokter_mulai >= $jam_mulai) && ($jadwaldokter_mulai <= $jam_tutup)) || 
                    // //jadwal jam tutup baru = jam tutup db
                    // ($jadwaldokter_tutup == $jam_tutup) || 
                    // //ada jadwal db di dalam jadwal baru
                    // (($jadwaldokter_mulai <= $jam_mulai) && ($jadwaldokter_tutup >= $jam_tutup)) ||
                    // //ada jam tutup baru  di tengah2 jam buka dan jam tutup db
                    // (($jadwaldokter_tutup > $jam_mulai) && ($jadwaldokter_tutup <= $jam_tutup))
                ){
                    $this->addError($attribute,'Telah Ada Jadwal Pada Rentang Jam Ini');
                }
            }
        }
    }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getInstalasi()
    // {
    //     return $this->hasOne(InstalasiM::className(), ['instalasi_id' => 'instalasi_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPegawai()
    // {
    //     return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getRuangan()
    // {
    //     return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    public function getJadwal()
    {
        $baseQuery = "
            SELECT jadwaldokter_m.jadwaldokter_id,
            jadwaldokter_m.ruangan_id,
            jadwaldokter_m.instalasi_id,
            jadwaldokter_m.pegawai_id,
            ruangan_m.ruangan_nama,
            pegawai_m.nama_pegawai,
            CONCAT(
                    gelarDepan.lookup_value, 
                    ' ',
                    pegawai_m.nama_pegawai,
                    ' ',
                    gelarBelakang.gelarbelakang_nama 
            ) as nama_pegawai_lengkap,
            jadwaldokter_m.jadwaldokter_hari,
            concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS Waktu,
            jadwaldokter_m.maximumantrian AS kuota,
            to_char(jadwaldokter_m.jadwaldokter_mulai, 'HH24:MI') as waktu_mulai,
            to_char(jadwaldokter_m.jadwaldokter_tutup, 'HH24:MI') as waktu_selesai,
            jadwaldoktertambahan_m.kuota_penambahan,
            jadwaldokter_m.maximumantrian::double precision + jadwaldoktertambahan_m.kuota_penambahan::double precision AS total_kuota,
            hari.lookup_name AS hari,
            jadwalbukapoli_m.hari AS hari_jadwalbuka,
            jadwaldokter_m.kuota_online AS kuota_online,
            jadwaldokter_m.jadwaldokter_tgl,
            pegawai_m.dokter_id,
            ruangan_m.poliklinik_id,
                CASE
                    WHEN jadwalbukapoli_m.shift_id IS NULL THEN 0
                    ELSE jadwalbukapoli_m.shift_id
                END AS shift_id,
            shift_m.shift1_id,
            jadwaldokter_m.notifikasi_id,
            notifikasi_m.judul_temp,
            notifikasi_m.notifikasi,
            COALESCE(kuotadokter_r.kuota_tersedia, 0::real) AS kuota_tersedia,
            COALESCE(jadwaldokter_m.kuota_nonbpjs_online, 0::real) AS kuota_nonbpjs_online,
            jadwaldokter_m.is_active,
            kuotadokter_r.kuotadokter_id,
            jadwaldokter_m.jadwalbukapoli_id,
            COALESCE(offline.kuota_tersedia, 0::real) AS kuotaoffline_tersedia,
            jadwaldokter_m.kuota_total,
            jadwaldokter_m.is_bersedia,
            jadwaldokter_m.jumlah_loaddokter,
            pegawai_m.nomorindukpegawai as kode_dokter,
            jadwaldokter_m.is_bersedia as type
            FROM jadwaldokter_m
            JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
            JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
            JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
            LEFT JOIN lookup_m gelarDepan ON pegawai_m.gelardepan::integer = gelarDepan.lookup_id
            LEFT JOIN gelarbelakang_m gelarBelakang ON pegawai_m.gelarbelakang::integer = gelarBelakang.gelarbelakang_id  
            LEFT JOIN jadwaldoktertambahan_m ON jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id
            JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
            LEFT JOIN lookup_m hari ON jadwalbukapoli_m.hari = hari.lookup_id
            LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
            LEFT JOIN notifikasi_m ON jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id
            LEFT JOIN kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online = true
            LEFT JOIN kuotadokter_r offline ON jadwaldokter_m.jadwaldokter_id = offline.jadwaldokter_id AND offline.is_online = false
            WHERE jadwaldokter_m.is_deleted = false AND pegawai_m.is_deleted = false AND pegawai_m.is_active = true
        ";
        return $baseQuery;
    }

    public function getJadwalCountReservasi($tgl_kunjungan)
    {
        $baseQuery = "
            SELECT jadwaldokter_m.jadwaldokter_id,
            jadwaldokter_m.ruangan_id,
            jadwaldokter_m.instalasi_id,
            jadwaldokter_m.pegawai_id,
            ruangan_m.ruangan_nama,
            pegawai_m.nama_pegawai,
            CONCAT(
                    gelarDepan.lookup_value, 
                    ' ',
                    pegawai_m.nama_pegawai,
                    ' ',
                    gelarBelakang.gelarbelakang_nama 
            ) as nama_pegawai_lengkap,
            jadwaldokter_m.jadwaldokter_hari,
            concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS Waktu,
            jadwaldokter_m.maximumantrian AS kuota,
            to_char(jadwaldokter_m.jadwaldokter_mulai, 'HH24:MI') as waktu_mulai,
            to_char(jadwaldokter_m.jadwaldokter_tutup, 'HH24:MI') as waktu_selesai,
            jadwaldoktertambahan_m.kuota_penambahan,
            jadwaldokter_m.maximumantrian::double precision + jadwaldoktertambahan_m.kuota_penambahan::double precision AS total_kuota,
            hari.lookup_name AS hari,
            jadwalbukapoli_m.hari AS hari_jadwalbuka,
            jadwaldokter_m.kuota_online AS kuota_online,
            jadwaldokter_m.jadwaldokter_tgl,
            pegawai_m.dokter_id,
            ruangan_m.poliklinik_id,
                CASE
                    WHEN jadwalbukapoli_m.shift_id IS NULL THEN 0
                    ELSE jadwalbukapoli_m.shift_id
                END AS shift_id,
            shift_m.shift1_id,
            jadwaldokter_m.notifikasi_id,
            notifikasi_m.judul_temp,
            notifikasi_m.notifikasi,
            ((CASE WHEN konfigsystem_k.is_reservasi THEN jadwaldokter_m.kuota_online ELSE jadwaldokter_m.kuota_total END) - COALESCE(pendaftaranol_t.kuota_terpakai, 0::real)) AS kuota_tersedia,
            jadwaldokter_m.is_active,
            kuotadokter_r.kuotadokter_id,
            jadwaldokter_m.jadwalbukapoli_id,
            COALESCE(offline.kuota_tersedia, 0::real) AS kuotaoffline_tersedia,
            jadwaldokter_m.kuota_total,
            jadwaldokter_m.is_bersedia,
            jadwaldokter_m.jumlah_loaddokter,
            pegawai_m.nomorindukpegawai as kode_dokter,
            jadwaldokter_m.is_bersedia as type
            FROM jadwaldokter_m
            JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
            JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
            JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
            LEFT JOIN lookup_m gelarDepan ON pegawai_m.gelardepan::integer = gelarDepan.lookup_id
            LEFT JOIN gelarbelakang_m gelarBelakang ON pegawai_m.gelarbelakang::integer = gelarBelakang.gelarbelakang_id  
            LEFT JOIN jadwaldoktertambahan_m ON jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id
            LEFT JOIN (
                SELECT jadwaldokter_id, count(*) kuota_terpakai FROM pendaftaranol_t
                WHERE is_deleted = false AND is_active = true AND status_daftar_ol != 566 AND date(tgl_pendaftaranol) = '$tgl_kunjungan'
                GROUP BY jadwaldokter_id
            ) AS pendaftaranol_t ON pendaftaranol_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
            JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
            LEFT JOIN lookup_m hari ON jadwalbukapoli_m.hari = hari.lookup_id
            LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
            LEFT JOIN notifikasi_m ON jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id
            LEFT JOIN kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online = true
            LEFT JOIN kuotadokter_r offline ON jadwaldokter_m.jadwaldokter_id = offline.jadwaldokter_id AND offline.is_online = false
            LEFT JOIN konfigsystem_k ON 1 = 1
            WHERE jadwaldokter_m.is_deleted = false AND pegawai_m.is_deleted = false AND pegawai_m.is_active = true
        ";
        return $baseQuery;
    }
}
