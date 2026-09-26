<?php

use yii\db\Migration;

/**
 * Class m220310_125613_migrate_BTS190_antrianjkn_v
 */
class m220310_125613_migrate_BTS190_antrianjkn_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.antrianjkn_v;');
        $this->execute("
            CREATE VIEW \"public\".\"antrianjkn_v\" AS
            SELECT pendaftaranol_t.pendaftaranol_id,
            antrian_t.pendaftaran_id,
            pendaftaranol_t.no_pendaftaranol AS kodebooking,
            antrianjkn_r.jenis_cara_bayar,
            look_jenispasien.lookup_name AS jenispasien,
            antrianjkn_r.nomorkartu,
            pendaftaranol_t.jenisidentitas,
            look_jenisidentitas.lookup_name AS jenisidentitas_nama,
            pendaftaranol_t.no_identitas_pasien,
            pendaftaranol_t.no_telepon_pasien,
            ruangan_m.kode_ruangan_bpjs AS kodepoli,
            ruangan_m.ruangan_nama AS namapoli,
            CASE
            WHEN (pendaftaranol_t.status_pasien = 311) THEN 0
            ELSE 1
            END AS status_pasien,
            look_statuspasien.lookup_name AS status_pasien_nama,
            antrianjkn_r.no_rekam_medik,
            antrianjkn_r.tanggal_periksa,
            pegawai_m.kode_dokter_bpjs AS kodedokter,
            pegawai_m.nama_pegawai AS namadokter,
            concat(to_char((jadwaldokter_m.jadwaldokter_mulai)::interval, 'HH24:MI'::text), '-', to_char((jadwaldokter_m.jadwaldokter_tutup)::interval, 'HH24:MI'::text)) AS jampraktek,
            look_jeniskunjungan.lookup_name AS jeniskunjungan,
            antrianjkn_r.nomorreferensi,
            antrian_t.no_antrian AS nomorantrean,
            antrian_t.temp_urutan AS angkaantrean,
            (jadwaldokter_m.kuota_bpjs_online + jadwaldokter_m.kuota_bpjs_offline) AS kuotajkn,
            (jadwaldokter_m.kuota_nonbpjs_online + jadwaldokter_m.kuota_nonbpjs_offline) AS kuotanonjkn,
            (kuotadokter_r.kuota_bpjs_online + kuotadokter_r_offline.kuota_bpjs_offline) AS sisakuotajkn,
            (kuotadokter_r.kuota_nonbpjs_online + kuotadokter_r_offline.kuota_nonbpjs_offline) AS sisakuotanonjkn,
            antrianjkn_r.keterangan,
            jadwaldokter_m.jumlah_loaddokter AS estimasidilayani,
            antrian_t.antrian_id,
            jadwaldokter_m.jadwaldokter_id,
            slotjadwaldokter_m.jam_mulai,
            slotjadwaldokter_m.jam_selesai
            FROM ((((((((((((pendaftaranol_t
            JOIN ( SELECT a.pendaftaran_id,
            a.no_antrian,
            a.temp_urutan,
            a.antrian_id,
            a.jadwaldokter_id,
            a.slot_sequence
            FROM antrian_t a) antrian_t ON ((pendaftaranol_t.antrian_id = antrian_t.antrian_id)))
            JOIN ( SELECT a.jadwaldokter_id,
            a.ruangan_id,
            a.pegawai_id,
            a.jadwaldokter_mulai,
            a.jadwaldokter_tutup,
            a.kuota_bpjs_online,
            a.kuota_bpjs_offline,
            a.kuota_nonbpjs_online,
            a.kuota_nonbpjs_offline,
            a.jumlah_loaddokter
            FROM jadwaldokter_m a) jadwaldokter_m ON ((antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id)))
            JOIN ( SELECT a.ruangan_id,
            a.kode_ruangan_bpjs,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kode_dokter_bpjs
            FROM pegawai_m a) pegawai_m ON ((jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ( SELECT a.kuota_bpjs_online,
            a.kuota_nonbpjs_online,
            a.jadwaldokter_id,
            a.is_online
            FROM kuotadokter_r a) kuotadokter_r ON (((jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id) AND (kuotadokter_r.is_online = true))))
            JOIN ( SELECT a.kuota_bpjs_offline,
            a.kuota_nonbpjs_offline,
            a.jadwaldokter_id,
            a.is_online
            FROM kuotadokter_r a) kuotadokter_r_offline ON (((jadwaldokter_m.jadwaldokter_id = kuotadokter_r_offline.jadwaldokter_id) AND (kuotadokter_r_offline.is_online = false))))
            JOIN ( SELECT a.pendaftaranol_id,
            a.jenis_cara_bayar,
            a.nomorkartu,
            a.no_rekam_medik,
            a.tanggal_periksa,
            a.nomorreferensi,
            a.keterangan,
            a.jeniskunjungan
            FROM antrianjkn_r a) antrianjkn_r ON ((pendaftaranol_t.pendaftaranol_id = antrianjkn_r.pendaftaranol_id)))
            JOIN ( SELECT a.jadwaldokter_id,
            a.slot_sequence,
            a.jam_mulai,
            a.jam_selesai
            FROM slotjadwaldokter_m a) slotjadwaldokter_m ON (((antrian_t.jadwaldokter_id = slotjadwaldokter_m.jadwaldokter_id) AND (antrian_t.slot_sequence = slotjadwaldokter_m.slot_sequence))))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_jenispasien ON ((antrianjkn_r.jenis_cara_bayar = look_jenispasien.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_jenisidentitas ON (((pendaftaranol_t.jenisidentitas)::integer = look_jenisidentitas.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_statuspasien ON ((pendaftaranol_t.status_pasien = look_statuspasien.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_jeniskunjungan ON ((antrianjkn_r.jeniskunjungan = look_jeniskunjungan.lookup_id)))
            ;");
        $this->execute('
            ALTER TABLE public.antrianjkn_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220310_125613_migrate_BTS190_antrianjkn_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220310_125613_migrate_BTS190_antrianjkn_v cannot be reverted.\n";

        return false;
    }
    */
}
