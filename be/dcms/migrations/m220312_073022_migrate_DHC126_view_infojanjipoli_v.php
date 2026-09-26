<?php

use yii\db\Migration;

/**
 * Class m220312_073022_migrate_DHC126_view_infojanjipoli_v
 */
class m220312_073022_migrate_DHC126_view_infojanjipoli_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infojanjipoli_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infojanjipoli_v" AS  SELECT \'673\'::text AS transaksi_konsul,
                buatjanjipoli_t.buatjanjipoli_id,
                buatjanjipoli_t.tgl_buatjanji,
                buatjanjipoli_t.antrian_id,
                antrian_t.no_antrian,
                buatjanjipoli_t.pegawai_id,
                pegawai_m.nama_pegawai,
                buatjanjipoli_t.ruangan_id,
                ruangan_m.ruangan_nama,
                buatjanjipoli_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien, 
                pasien_m.alamat_pasien,
                pasien_m.no_telepon_pasien,
                pasien_m.no_mobile_pasien,
                pasien_m.alamatemail,
                fgetnamalookup((buatjanjipoli_t.hari_jadwal)::integer) AS hari,
                buatjanjipoli_t.tgl_jadwal,
                buatjanjipoli_t.is_rencanakontrol,
                fgetnamalookup((buatjanjipoli_t.status_janjipoli)::integer) AS status_janji,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                buatjanjipoli_t.carabayar_id,
                carabayar_m.carabayar_nama,
                buatjanjipoli_t.penjamin_id,
                penjamin_m.penjamin_nama,
                buatjanjipoli_t.keterangan_buatjanji,
                buatjanjipoli_t.by_phone,
                NULL::smallint AS status_approve,
                NULL::character varying AS status_approve_nama,
                r_asal.ruangan_id AS asalpoliklinikkonsul_id,
                r_asal.ruangan_nama AS ruangan_asal,
                pegawai_asal.pegawai_id AS doktermengkonsul_id,
                pegawai_asal.nama_pegawai AS doktermengkonsul,
                pasien_m.tanggal_lahir
               FROM ((((((((((buatjanjipoli_t
                 JOIN pasien_m ON ((buatjanjipoli_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pegawai_m ON ((buatjanjipoli_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((buatjanjipoli_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN antrian_t ON (((buatjanjipoli_t.antrian_id)::integer = antrian_t.antrian_id)))
                 LEFT JOIN pendaftaran_t ON ((buatjanjipoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN carabayar_m ON ((buatjanjipoli_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((buatjanjipoli_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN ruangan_m r_asal ON ((buatjanjipoli_t.ruanganasal_id = r_asal.ruangan_id)))
                 LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                        pegawai_m_1.nama_pegawai
                       FROM pegawai_m pegawai_m_1) pegawai_asal ON ((buatjanjipoli_t.pegawaiasal_id = pegawai_asal.pegawai_id)))
              WHERE ((buatjanjipoli_t.is_active = true) AND (buatjanjipoli_t.is_deleted = false))
            UNION ALL
             SELECT \'672\'::text AS transaksi_konsul,
                konsulpoli_t.konsulpoli_id AS buatjanjipoli_id,
                konsulpoli_t.tgl_konsulpoli AS tgl_buatjanji,
                NULL::text AS antrian_id,
                NULL::text AS no_antrian,
                konsulpoli_t.pegawai_id,
                pegawai_m.nama_pegawai,
                konsulpoli_t.ruangan_id,
                ruangan_tujuan.ruangan_nama,
                konsulpoli_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.alamat_pasien,
                pasien_m.no_telepon_pasien,
                pasien_m.no_mobile_pasien,
                pasien_m.alamatemail,
                NULL::text AS hari,
                konsulpoli_t.tgl_konsulpoli AS tgl_jadwal,
                NULL::boolean AS is_rencanakontrol,
                NULL::text AS status_janji,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                ruangan_tujuan.instalasi_id,
                instalasi_m.instalasi_nama,
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                konsulpoli_t.catatan_dokter_konsul AS keterangan_buatjanji,
                NULL::boolean AS by_phone,
                konsulpoli_t.status_approve,
                fgetnamalookup((konsulpoli_t.status_approve)::integer) AS status_approve_nama,
                konsulpoli_t.asalpoliklinikkonsul_id,
                ruangan_asal.ruangan_nama AS ruangan_asal,
                pendaftaran_t.pegawai_id AS doktermengkonsul_id,
                dok_mengkonsul.nama_pegawai AS doktermengkonsul,
                pasien_m.tanggal_lahir
               FROM (((((((((konsulpoli_t
                 JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN ruangan_m ruangan_tujuan ON ((konsulpoli_t.ruangan_id = ruangan_tujuan.ruangan_id)))
                 JOIN ruangan_m ruangan_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruangan_asal.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220312_073022_migrate_DHC126_view_infojanjipoli_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220312_073022_migrate_DHC126_view_infojanjipoli_v cannot be reverted.\n";

        return false;
    }
    */
}
