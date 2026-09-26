<?php

namespace Doco\models\pendaftaran;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;

/**
 * This is the model class for table "pendaftaranol_t".
 *
 * @property int $pendaftaranol_id
 * @property int $pendaftaran_id
 * @property string $no_pendaftaranol
 * @property string $tgl_pendaftaranol
 * @property string $jam_kunjungan
 * @property int $pasien_id
 * @property int $carabayar_id carabayar_m
 * @property int $penjamin_id
 * @property int $ruangan_id ruangan_m where instalasi_id=1
 * @property int $pegawai_id pegawai_v kelompokpegawai_id=1
 * @property int $shift_id
 * @property string $no_asuransi
 * @property string $no_rujukan
 * @property int $status_pasien lookup_type='status_pasien'
 * @property int $status_daftar_ol lookup_type='status_daftar_ol'
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
 * @property int $antrian_id
 * @property string $alamat_pasien
 * @property int $referral_doctor_id
 * @property string $email
 * @property string $note
 * @property string $reference_letter
 * @property string $keterangan
 * @property int $jenisidentitas
 * @property int $no_identitas_pasien
 * @property int $namadepan
 * @property string $nama_pasien
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property int $jeniskelamin
 * @property string $benefit_code
 * @property string $transaction_id
 * 
 */
class PendaftaranOnline extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pendaftaranol_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'pegawai_id', 'shift_id', 'status_pasien', 'status_daftar_ol', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'jadwaldokter_id', 'jadwalbukapoli_id', 'jenisidentitas', 'no_identitas_pasien', 'namadepan', 'nama_pasien', 'tempat_lahir', 'tanggal_lahir', 'jeniskelamin'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'pegawai_id', 'shift_id', 'status_pasien', 'status_daftar_ol', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'jadwaldokter_id', 'jadwalbukapoli_id', 'antrian_id', 'jenisidentitas', 'namadepan', 'jeniskelamin'], 'integer'],
            [['tgl_pendaftaranol', 'created_date', 'last_modified_date', 'deleted_date', 'no_telepon_pasien', 'antrian_id', 'alamat_pasien', 'referral_doctor_id', 'email', 'note', 'reference_letter', 'no_rekam_medik', 'jeniskunjungan', 'keterangan', 'nama_pasien', 'tempat_lahir', 'tanggal_lahir', 'is_postranap','estimasidilayani', 'transaction_id', 'benefit_code', 'no_asuransi'], 'safe'],
            [['ruangan_id'], 'required'],
            [['additional_data', 'jam_mulai', 'jam_tutup'], 'string'],
            [['is_deleted', 'is_active', 'is_postranap'], 'boolean'],
            [['no_pendaftaranol', 'jam_kunjungan'], 'string', 'max' => 100],
            [['no_asuransi', 'no_rujukan', 'no_bpjs'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaranol_id' => 'Pendaftaran Online ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaranol' => 'No Pendaftaran Online',
            'tgl_pendaftaranol' => 'Tanggal Pendaftaran Online',
            'jam_kunjungan' => 'Jam Kunjungan',
            'pasien_id' => 'Pasien ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'shift_id' => 'Shift ID',
            'no_asuransi' => 'No Asuransi',
            'no_rujukan' => 'No Rujukan',
            'status_pasien' => 'Status Pasien',
            'status_daftar_ol' => 'Status Daftar Online',
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
            'jadwaldokter_id' => 'Jadwal Dokter ID',
            'jam_mulai' => 'Jam Mulai',
            'jam_tutup' => 'Jam Tutup',
            'antrian_id' => 'Antrian ID',
            'alamat_pasien' => 'Alamat Pasien',
            'referral_doctor_id' => 'Referral Dokter',
            'email' => 'Email',
            'note' => 'Catatan',
            'reference_letter' => 'Surat Rujukan',
            'no_bpjs' => 'No BPJS',
            'no_rekam_medik' => 'No Rekam Medik',
            'jeniskunjungan' => 'Asal Rujukan',
            'keterangan' => 'Keterangan',
            'is_postranap' => 'Post Ranap',
        ];
    }

    public function getPasien($noRm)
    {
        
    }

    public function getLastSequenceJkn($jadwalDokterId, $date)
    {
        $getSlot ="
            SELECT
                b.slot_sequence 
            FROM
                jadwaldokter_m a
                JOIN slotjadwaldokter_m b ON b.jadwaldokter_id = a.jadwaldokter_id
                JOIN pegawai_m c ON c.pegawai_id = a.pegawai_id 
            WHERE
                c.is_active = TRUE 
                AND c.is_deleted = FALSE 
                AND b.jadwaldokter_id = :jadwalDokterId  
                AND b.slot_sequence NOT IN (
                    SELECT
                        b.slot_sequence 
                    FROM
                        pendaftaranol_t a 
                        JOIN antrian_t b ON b.antrian_id = a.antrian_id 
                    WHERE
                        a.tgl_pendaftaranol::DATE = :date
                        AND a.status_daftar_ol != 566
                        AND b.jadwaldokter_id = :jadwalDokterId
                        AND b.slot_sequence IS NOT NULL 
                ) 
            GROUP BY
                b.slot_sequence 
            ORDER BY
                b.slot_sequence";
        
        return Yii::$app->db->createCommand($getSlot)
            ->bindValue(':jadwalDokterId', $jadwalDokterId)
            ->bindValue(':date', $date)
            ->queryOne();
    }

    public function getLastSequenceJknSameDate($jadwalDokterId, $date, $time)
    {
        $getSlot ="
            SELECT
                b.slot_sequence 
            FROM
                jadwaldokter_m a
                JOIN slotjadwaldokter_m b ON b.jadwaldokter_id = a.jadwaldokter_id
                JOIN pegawai_m c ON c.pegawai_id = a.pegawai_id 
            WHERE
                c.is_active = TRUE 
                AND c.is_deleted = FALSE 
                AND b.jadwaldokter_id = :jadwalDokterId  
                AND b.slot_sequence NOT IN (
                    SELECT
                        b.slot_sequence 
                    FROM
                        pendaftaranol_t a 
                        JOIN antrian_t b ON b.antrian_id = a.antrian_id 
                    WHERE
                        a.tgl_pendaftaranol::DATE = :date
                        AND a.status_daftar_ol != 566
                        AND b.jadwaldokter_id = :jadwalDokterId
                        AND b.slot_sequence IS NOT NULL 
                )
            AND b.jam_mulai > :time 
            GROUP BY
                b.slot_sequence 
            ORDER BY
                b.slot_sequence";

        $result = Yii::$app->db->createCommand($getSlot)
            ->bindValue(':jadwalDokterId', $jadwalDokterId)
            ->bindValue(':date', $date)
            ->bindValue(':time', $time)
            ->queryOne();

        return $result;
    }

    public function checkSlotAvailSmh($tanggal, $slotId, $nip)
    {
        $query = "
            SELECT 
            A.no_pendaftaranol,
            A.pegawai_id,
            A.antrian_id,
            b.slot_sequence,
            C.slotjadwaldokter_id,
            d.nomorindukpegawai,
            C.jadwaldokter_id 
        FROM
            pendaftaranol_t A
            JOIN antrian_t b ON b.antrian_id = A.antrian_id
            JOIN slotjadwaldokter_m C ON C.slot_sequence = b.slot_sequence
            JOIN pegawai_m d ON d.pegawai_id = A.pegawai_id 
        WHERE
            A.tgl_pendaftaranol::DATE = :tanggal
            AND C.slotjadwaldokter_id = :slotId 
            AND lower(d.nomorindukpegawai) LIKE :nip 
            AND A.status_daftar_ol IN ( 565, 564 ) 
            AND A.is_active = TRUE 
            AND A.is_deleted = FALSE 
            AND C.jadwaldokter_id = b.jadwaldokter_id
        ";

        return Yii::$app->db->createCommand($query)
        ->bindValue(':tanggal', $tanggal)
        ->bindValue(':slotId', $slotId)
        ->bindValue(':nip', $nip)
        ->queryAll();
    }

    public function checkSlotAvailSmhChangeSchedule($tanggal, $slotId, $nip)
    {
        $query = "
            SELECT A
            .no_pendaftaranol,
            A.pegawai_id,
            A.antrian_id,
            b.slot_sequence,
            C.slotjadwaldokter_id,
            d.nomorindukpegawai,
            C.jadwaldokter_id 
        FROM
            pendaftaranol_t
            A JOIN antrian_t b ON b.antrian_id = A.antrian_id
            JOIN slotjadwaldokter_r C ON C.slot_sequence = b.slot_sequence
            JOIN pegawai_m d ON d.pegawai_id = A.pegawai_id 
        WHERE
            A.tgl_pendaftaranol::DATE = :tanggal
            AND C.slotjadwaldokter_id = :slotId 
            AND lower(d.nomorindukpegawai) LIKE :nip 
            AND A.status_daftar_ol IN ( 565, 564 ) 
            AND A.is_active = TRUE 
            AND A.is_deleted = FALSE 
            AND C.jadwaldokter_id = b.jadwaldokter_id
        ";

        return Yii::$app->db->createCommand($query)
        ->bindValue(':tanggal', $tanggal)
        ->bindValue(':slotId', $slotId)
        ->bindValue(':nip', $nip)
        ->queryAll();
    }

    public function validateSlotJkn($jadwalDokterId, $date, $seq) 
    {
        $qry = "SELECT COUNT ( b.slot_sequence ) FROM pendaftaranol_t a JOIN antrian_t b ON b.antrian_id = a.antrian_id WHERE a.tgl_pendaftaranol::DATE = :tanggal AND b.jadwaldokter_id = :jadwalId AND b.slot_sequence = :sequence AND a.status_daftar_ol != 566 AND a.is_active = TRUE AND b.is_deleted = FALSE GROUP BY b.slot_sequence";
        return Yii::$app->db->createCommand($qry)
        ->bindValue(':tanggal', $date)
        ->bindValue(':jadwalId', $jadwalDokterId)
        ->bindValue(':sequence', $seq)
        ->queryOne();
    }

    public function getJadwalJkn($hari, $dokterJkn, $poliJkn, $jamPraktekMulai, $jamPraktekTutup)
    {
        $qry = "
        SELECT a.jadwaldokter_id,
            a.ruangan_id,
            b.jadwalbukapoli_id,
            b.hari,
            d.kode_ruangan_bpjs,
            c.kode_dokter_bpjs,
            c.pegawai_id,
            c.nama_pegawai,
            to_char(a.jadwaldokter_mulai::time,'HH24:MI') as waktu_mulai,
            to_char(a.jadwaldokter_tutup::time,'HH24:MI') as waktu_tutup,
            d.ruangan_nama
        FROM
            jadwaldokter_m a
            JOIN jadwalbukapoli_m b ON b.jadwalbukapoli_id = a.jadwalbukapoli_id
            JOIN pegawai_m c ON c.pegawai_id = a.pegawai_id
            JOIN ruangan_m d ON d.ruangan_id = a.ruangan_id 
        WHERE
            a.is_active = true 
            AND a.is_deleted = false 
            AND c.is_active = TRUE 
            AND c.is_deleted = FALSE 
            AND b.hari = :hariId
            AND c.kode_dokter_bpjs = :dokter::TEXT 
            AND d.kode_ruangan_bpjs = :poli
            AND a.jadwaldokter_mulai = :jadwaldokter_mulai
            AND a.jadwaldokter_tutup = :jadwaldokter_tutup
        ";

        return Yii::$app->db->createCommand($qry)
        ->bindValue(':hariId', $hari)
        ->bindValue(':dokter', $dokterJkn)
        ->bindValue(':poli', $poliJkn)
        ->bindValue(':jadwaldokter_mulai', $jamPraktekMulai)
        ->bindValue(':jadwaldokter_tutup', $jamPraktekTutup)
        ->queryOne();
    }

    public function cekPendaftaranPasien($nobpjs, $date) 
    {
        $qry = "SELECT * FROM pendaftaranol_t WHERE tgl_pendaftaranol::DATE = :tanggal AND no_bpjs = :nobpjs AND status_daftar_ol != 566 AND is_active = TRUE AND is_deleted = FALSE";
        return Yii::$app->db->createCommand($qry)
        ->bindValue(':tanggal', $date)
        ->bindValue(':nobpjs', $nobpjs)
        ->queryOne();
    }

    public function getDataSlot($slotId, $hari, $nip)
    {
        $query = "
            SELECT
            a.pegawai_id,
            b.slotjadwaldokter_id,
            b.slot_sequence,
            b.jadwaldokter_id,
            c.jadwalbukapoli_id,
            c.hari,
            a.ruangan_id,
            d.nomorindukpegawai,
            b.jam_mulai,
            b.jam_selesai 
        FROM
            jadwaldokter_m a
            INNER JOIN slotjadwaldokter_m b ON b.jadwaldokter_id = A.jadwaldokter_id
            INNER JOIN jadwalbukapoli_m c ON C.jadwalbukapoli_id = A.jadwalbukapoli_id
            INNER JOIN pegawai_m d ON d.pegawai_id = A.pegawai_id 
        WHERE
            ( ( b.slotjadwaldokter_id =:slotId) AND ( c.hari =:hari ) ) 
            AND ( LOWER ( d.nomorindukpegawai ) LIKE :nip ) 
            AND ( ( d.is_active =true ) AND ( d.is_deleted =false ) ) 
        ORDER BY
            b.slot_sequence
        ";

        return Yii::$app->db->createCommand($query)
        ->bindValue(':slotId', $slotId)
        ->bindValue(':hari', $hari)
        ->bindValue(':nip', $nip)
        // ->bindValue(':nip', '%'.$nip.'%')
        ->queryOne();
    }

    public function getDataSlotChangeSchedule($slotId, $hari, $nip)
    {
        $query = "
            SELECT
            a.pegawai_id,
            b.slotjadwaldokter_id,
            b.slot_sequence,
            b.jadwaldokter_id,
            c.jadwalbukapoli_id,
            c.hari,
            a.ruangan_id,
            d.nomorindukpegawai,
            b.jam_mulai,
            b.jam_selesai 
        FROM
            jadwaldokter_m a
            INNER JOIN slotjadwaldokter_r b ON b.jadwaldokter_id = A.jadwaldokter_id
            INNER JOIN jadwalbukapoli_m c ON C.jadwalbukapoli_id = A.jadwalbukapoli_id
            INNER JOIN pegawai_m d ON d.pegawai_id = A.pegawai_id 
        WHERE
            ( ( b.slotjadwaldokter_id =:slotId) AND ( c.hari =:hari ) ) 
            AND ( LOWER ( d.nomorindukpegawai ) LIKE :nip ) 
            AND ( ( d.is_active =true ) AND ( d.is_deleted =false ) ) 
        ORDER BY
            b.slot_sequence
        ";

        return Yii::$app->db->createCommand($query)
        ->bindValue(':slotId', $slotId)
        ->bindValue(':hari', $hari)
        ->bindValue(':nip', $nip)
        ->queryOne();
    }

    public function kuotaJkn($jadwalDokterId, $ruanganId, $tglPendaftaranol)
    {
        $query = "
                SELECT 
                    j.ruangan_nama,
                    j.nama_pegawai,
                    j.jadwaldokter_id,
                    j.kuota_bpjs_offline - (
                        SELECT 
                            count(jadwaldokter_id) 
                        FROM pendaftaranol_t WHERE jadwaldokter_id = :jadwalId 
                        AND ruangan_id = :ruanganId 
                        AND DATE(tgl_pendaftaranol) = :tgl 
                        and carabayar_id = 6 
                        and status_daftar_ol <> 566
                        and jenis_reservasi <> 1102
                    ) as kuota_bpjs_offline,
                    j.kuota_nonbpjs_offline,
                    j.kuota_bpjs_online - (
                        SELECT 
                            count(jadwaldokter_id) 
                        FROM pendaftaranol_t WHERE jadwaldokter_id = :jadwalId 
                        AND ruangan_id = :ruanganId 
                        AND DATE(tgl_pendaftaranol) = :tgl 
                        and carabayar_id = 6 
                        and status_daftar_ol <> 566
                        and jenis_reservasi = 1102
                    ) as kuota_bpjs_online,
                    j.kuota_nonbpjs_online,
                    j.kuota_total,
                    j.kuota_bpjs_online as master_kuota_bpjs_online,
                    j.kuota_nonbpjs_online as master_kuota_nonbpjs_online,
                    j.kuota_bpjs_offline as master_kuota_bpjs_offline  
            FROM infojadwaldokter_v j
            WHERE j.ruangan_id = :ruanganId AND j.jadwaldokter_id = :jadwalId
            GROUP BY  j.ruangan_nama, j.jadwaldokter_id, j.nama_pegawai, j.kuota_bpjs_offline, j.kuota_nonbpjs_offline, j.kuota_total, j.kuota_bpjs_online, j.kuota_nonbpjs_online;
        ";

        return Yii::$app->db->createCommand($query)
        ->bindValue(':jadwalId', $jadwalDokterId)
        ->bindValue(':ruanganId', $ruanganId)
        ->bindValue(':tgl', $tglPendaftaranol)
        ->queryOne();
    }

    public function getEstimasiByJadwal($jadwaldokter_id, $tgl_antrian, $is_bpjs = false, $antrian_id = 0)
    {
        if(empty($jadwaldokter_id)){
            return null;
        }
        
        if ($antrian_id === null || trim($antrian_id) === '') {
            $antrian_id = 0;
        }
        
        $query = "
            SELECT
                jadwaldokter_mulai,
                jadwaldokter_tutup,
                kuota_bpjs_offline,
                kuota_bpjs_online,
                kuota_nonbpjs_offline,
                kuota_nonbpjs_online,
                jumlah_loaddokter
            FROM jadwaldokter_m
            WHERE jadwaldokter_id = :jadwaldokter_id
        ";

        $dataJadwal = Yii::$app->db->createCommand($query)->bindValue(':jadwaldokter_id',$jadwaldokter_id)->queryOne();

        $dataAntrian = Yii::$app->db->createCommand("
            SELECT 
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id=418 or carabayar_id = 6)) as jumlah_antrian_bpjs_online,
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id=418 or carabayar_id = 6)) as jumlah_antrian_bpjs_offline,
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id<>418 or carabayar_id <> 6)) as jumlah_antrian_nonbpjs_online,  
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id<>418 or carabayar_id <> 6)) as jumlah_antrian_nonbpjs_offline
            FROM antrian_t 
            WHERE jenisantrian_id = :jenisantrian_id
            AND antrian_t.jadwaldokter_id = :jadwaldokter_id
            AND tgl_antrian::date = :tgl_antrian
            AND antrian_id <> :antrian_id
            AND antrian_t.is_deleted is false 
        ")
        ->bindValue(':jenisantrian_id',DocoConstants::VAR_JA_P)
        ->bindValue(':jadwaldokter_id',$jadwaldokter_id)
        ->bindValue(':antrian_id',$antrian_id)
        ->bindValue(':tgl_antrian',$tgl_antrian);
        $dataAntrian = $dataAntrian->queryOne();

        $kuota_bpjs_offline = ArrayHelper::getValue($dataJadwal,'kuota_bpjs_offline',0);
        $kuota_bpjs_online= ArrayHelper::getValue($dataJadwal,'kuota_bpjs_online',0);
        $kuota_nonbpjs_offline= ArrayHelper::getValue($dataJadwal,'kuota_nonbpjs_offline',0);
        $kuota_nonbpjs_online= ArrayHelper::getValue($dataJadwal,'kuota_nonbpjs_online',0);
        $kuotajkn = $kuota_bpjs_online + $kuota_bpjs_offline;
        $kuotanonjkn = $kuota_nonbpjs_offline + $kuota_nonbpjs_online;
        $jumlah_antrian_bpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_online',0);
        $jumlah_antrian_bpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_offline',0);
        $jumlah_antrian_nonbpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_online',0);
        $jumlah_antrian_nonbpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_offline',0);
        $sisakuotanonjkn = $kuotanonjkn - ($jumlah_antrian_nonbpjs_online + $jumlah_antrian_nonbpjs_offline);
        $sisakuotajkn = $kuotajkn - ($jumlah_antrian_bpjs_online + $jumlah_antrian_bpjs_offline);

        $jam_mulai = !empty($dataJadwal['jadwaldokter_mulai']) 
                        ? date('H:i', strtotime($dataJadwal['jadwaldokter_mulai'])) : '00:00';
        $jam_tutup = !empty($dataJadwal['jadwaldokter_tutup']) 
                        ? date('H:i', strtotime($dataJadwal['jadwaldokter_tutup'])) : '00:00';

        $tglestimasi = date('Y-m-d', strtotime($tgl_antrian));
        $jamestimasi = date('H:i:s', strtotime($jam_mulai));
        if($is_bpjs){
            $totalAntrian = $kuotajkn;
            $sisaAntrian = $sisakuotajkn;
        }else{
            $totalAntrian = $kuotanonjkn;
            $sisaAntrian = $sisakuotanonjkn;
        }
        
        $totalAntrianPoli = $kuotajkn + $kuotanonjkn;
        $sisaAntrianPoli = $sisakuotajkn + $sisakuotanonjkn;

        $jumlah_loaddokter = ArrayHelper::getValue($dataJadwal,'jumlah_loaddokter');
        $spm = isset($jumlah_loaddokter) && !empty($jumlah_loaddokter) ? $jumlah_loaddokter : 6;
        $waktuestimasi = $tglestimasi . " " . $jamestimasi;
        $noUrut = ($totalAntrian - $sisaAntrian);
        $noUrutPoli = ($totalAntrianPoli - $sisaAntrianPoli);

        /**
         * jika waktu ambil lebih dari waktu mulai praktek
         * maka
         *  jika timeslot yg sudah diambil < waktu ambil antrian
         *  maka
         *      ambil timeslot setelah jam ambil
         * jika tidak 
         *      jam mulai + (spm * nourut)
         */
        $jamSekarang = date('H:i:s');
        $tglSekarang = date('Y-m-d');
        if(strtotime($tglestimasi) > strtotime($tglSekarang)){
            $tglJamSekarang = $tglSekarang . ' ' . $jamSekarang;
        }else{
            $tglJamSekarang = $tglestimasi . ' ' . $jamSekarang;
        }
        $waktuestimasi = $tglestimasi . ' ' . $jamestimasi;
        $urutanAntrianDiambil = $noUrut>0 ? $noUrut-1 : $noUrut;
        $currentTimeSlot = (date_create($waktuestimasi)->getTimestamp()) + (
            (($spm * $urutanAntrianDiambil) * 60)
        );

        $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
            (($spm * $noUrutPoli) * 60)
        );
        
        $urutanSekarang = null;
        if(strtotime($tglJamSekarang) > strtotime($waktuestimasi)){
            if($currentTimeSlot < strtotime($tglJamSekarang)){
                $interval = date_diff(date_create($waktuestimasi),date_create($tglJamSekarang));
                $urutanSekarang = ceil(((($interval->h * 60)+$interval->i) / $spm));

                $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                    (($spm * $urutanSekarang) * 60)
                );
                
                $dataAntrianPoli = Yii::$app->db->createCommand("
                    SELECT 
                        count(antrian_id) as total_antrian
                    FROM antrian_t 
                    WHERE jenisantrian_id = :jenisantrian_id
                    AND antrian_t.jadwaldokter_id = :jadwaldokter_id
                    AND tgl_antrian::date = :tgl_antrian
                    AND estimasidilayani >= :estimasidilayani
                    AND antrian_t.is_deleted is false 
                    AND antrian_id <> :antrian_id
                ")
                ->bindValue(':jenisantrian_id',DocoConstants::VAR_JA_P)
                ->bindValue(':jadwaldokter_id',$jadwaldokter_id)
                ->bindValue(':tgl_antrian', $tgl_antrian)
                ->bindValue(':antrian_id',$antrian_id)
                ->bindValue(':estimasidilayani', $timestampsecond * 1000);
                $dataAntrianPoli = $dataAntrianPoli->queryOne();
                
                if (ArrayHelper::getValue($dataAntrianPoli, 'total_antrian', 0) > 0) {
                    $urutanSekarang += ArrayHelper::getValue($dataAntrianPoli, 'total_antrian') ;
                    // recalculate timestamp dengan urutan terbaru
                    $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                        (($spm * $urutanSekarang) * 60)
                    );
                }
            }
        }

        $sequence = !is_null($urutanSekarang) ? $urutanSekarang+1 : $noUrut+1;

        return [
            'estimasidilayani_insecond' => $timestampsecond,
            'estimasidilayani_inmilisecond' => $timestampsecond * 1000,
            'sequence' => $sequence
        ];
    }

    public function getJknDataReservasi($tgl_kodebooking)
    {
        if (empty($tgl_kodebooking)) {
            $tgl_kodebooking = date('Y-m-d');
        }

        return self::find()
            ->select(['pendaftaranol_id', 'pendaftaran_id', 'no_pendaftaranol as kodebooking', new \yii\db\Expression('"additional_jkn"::json ->> \'payload\' as "payload_jkn"')])
            ->andWhere(['in', 'jenis_reservasi', [DocoConstants::JENIS_RESERVASI_JKN, DocoConstants::JENIS_RESERVASI_SIRS]])
            ->andWhere(['=', new \yii\db\Expression('(tgl_pendaftaranol::date)'), $tgl_kodebooking])
            ->andWhere(['!=', 'status_daftar_ol', DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK])
            ->asArray()->all();
            
    }
}
