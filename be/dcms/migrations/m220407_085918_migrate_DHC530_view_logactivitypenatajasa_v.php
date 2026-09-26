<?php

use yii\db\Migration;

/**
 * Class m220407_085918_migrate_DHC530_view_logactivitypenatajasa_v
 */
class m220407_085918_migrate_DHC530_view_logactivitypenatajasa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."logactivitypenatajasa_v";
        ');

        $this->execute('
            CREATE VIEW "public"."logactivitypenatajasa_v" AS  SELECT tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
                tindakanpelayanan_t.created_date,
                daftartindakan_m.daftartindakan_nama AS keterangan,
                \'Tambah Tindakan\'::character varying AS tipe,
                tindakanpelayanan_t.keterangantindakan AS alasan, 
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                1 AS track
               FROM (((tindakanpelayanan_t
                 JOIN loginpemakai_k ON ((tindakanpelayanan_t.created_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN daftartindakan_m ON (((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id) AND (daftartindakan_m.is_akomodasi IS FALSE))))
              WHERE ((tindakanpelayanan_t.is_penatajasa IS TRUE) AND (tindakanpelayanan_t.parent_id IS NULL))
            UNION ALL
             SELECT tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
                tindakanpelayanan_t.created_date,
                tipepaket_m.tipepaket_nama AS keterangan,
                \'Tambah Paket\'::character varying AS tipe,
                tindakanpelayanan_t.keterangantindakan AS alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                1 AS track
               FROM (((tindakanpelayanan_t
                 JOIN loginpemakai_k ON ((tindakanpelayanan_t.created_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
              WHERE ((tindakanpelayanan_t.is_penatajasa IS TRUE) AND (tindakanpelayanan_t.parent_id IS NULL))
            UNION ALL
             SELECT tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
                tindakanpelayanan_t.deleted_date AS created_date,
                daftartindakan_m.daftartindakan_nama AS keterangan,
                \'Hapus Tindakan\'::character varying AS tipe,
                tindakanpelayanan_t.alasan_batal AS alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                2 AS track
               FROM (((tindakanpelayanan_t
                 JOIN loginpemakai_k ON ((tindakanpelayanan_t.deleted_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN daftartindakan_m ON (((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id) AND (daftartindakan_m.is_akomodasi IS FALSE))))
              WHERE (tindakanpelayanan_t.is_penatajasa IS TRUE)
            UNION ALL
             SELECT tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
                tindakanpelayanan_t.deleted_date AS created_date,
                tipepaket_m.tipepaket_nama AS keterangan,
                \'Hapus Paket\'::character varying AS tipe,
                tindakanpelayanan_t.alasan_batal AS alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                2 AS track
               FROM (((tindakanpelayanan_t
                 JOIN loginpemakai_k ON ((tindakanpelayanan_t.deleted_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
              WHERE (tindakanpelayanan_t.is_penatajasa IS TRUE)
            UNION ALL
             SELECT pendaftaranpenjamin_t.pendaftaran_id,
                pendaftaranpenjamin_t.pendaftaranpenjamin_id AS transaksi_id,
                pendaftaranpenjamin_t.created_date,
                pendaftaranpenjamin_t.penjamin_nama AS keterangan,
                \'Tambah Penjamin\'::character varying AS tipe,
                pendaftaranpenjamin_t.alasan_batal AS alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                3 AS track
               FROM ((pendaftaranpenjamin_t
                 JOIN loginpemakai_k ON ((pendaftaranpenjamin_t.created_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
            UNION ALL
             SELECT pendaftaranpenjamin_t.pendaftaran_id,
                pendaftaranpenjamin_t.pendaftaranpenjamin_id AS transaksi_id,
                pendaftaranpenjamin_t.deleted_date AS created_date,
                pendaftaranpenjamin_t.penjamin_nama AS keterangan,
                \'Hapus Penjamin\'::character varying AS tipe,
                pendaftaranpenjamin_t.alasan_batal AS alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                4 AS track
               FROM ((pendaftaranpenjamin_t
                 JOIN loginpemakai_k ON ((pendaftaranpenjamin_t.deleted_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
            UNION ALL
             SELECT tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
                historipenatajasa_r.tgl_perubahan AS created_date,
                daftartindakan_m.daftartindakan_nama AS keterangan,
                tipeperubahan_m.tipeperubahan_nama AS tipe,
                historipenatajasa_r.alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                5 AS track
               FROM (((((historipenatajasa_r
                 JOIN ( SELECT a.tindakanpelayanan_id,
                        a.daftartindakan_id,
                        a.pendaftaran_id
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON ((historipenatajasa_r.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id)))
                 JOIN loginpemakai_k ON ((historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN daftartindakan_m ON (((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id) AND (daftartindakan_m.is_akomodasi IS TRUE))))
                 LEFT JOIN tipeperubahan_m ON ((historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id)))
            UNION ALL
             SELECT tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
                historipenatajasa_r.tgl_perubahan AS created_date,
                daftartindakan_m.daftartindakan_nama AS keterangan,
                tipeperubahan_m.tipeperubahan_nama AS tipe,
                historipenatajasa_r.alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                6 AS track
               FROM (((((historipenatajasa_r
                 JOIN ( SELECT a.tindakanpelayanan_id,
                        a.daftartindakan_id,
                        a.pendaftaran_id,
                        a.is_penatajasa
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON ((historipenatajasa_r.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id)))
                 JOIN loginpemakai_k ON ((historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN daftartindakan_m ON (((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id) AND (daftartindakan_m.is_akomodasi IS FALSE))))
                 LEFT JOIN tipeperubahan_m ON ((historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id)))
              WHERE (tindakanpelayanan_t.is_penatajasa IS FALSE)
            UNION ALL
             SELECT tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
                historipenatajasa_r.tgl_perubahan AS created_date,
                tipepaket_m.tipepaket_nama AS keterangan,
                tipeperubahan_m.tipeperubahan_nama AS tipe,
                historipenatajasa_r.alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                6 AS track
               FROM (((((historipenatajasa_r
                 JOIN ( SELECT a.tindakanpelayanan_id,
                        a.tipepaket_id,
                        a.pendaftaran_id,
                        a.is_penatajasa
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON ((historipenatajasa_r.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id)))
                 JOIN loginpemakai_k ON ((historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 LEFT JOIN tipeperubahan_m ON ((historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id)))
              WHERE (tindakanpelayanan_t.is_penatajasa IS FALSE)
            UNION ALL
             SELECT obatalkespasien_t.pendaftaran_id,
                obatalkespasien_t.obatalkespasien_id AS transaksi_id,
                COALESCE(historipenatajasa_r.tgl_perubahan, historipenatajasa_r.created_date) AS created_date,
                obatalkes_m.obatalkes_nama AS keterangan,
                tipeperubahan_m.tipeperubahan_nama AS tipe,
                historipenatajasa_r.alasan,
                loginpemakai_k.loginpemakai_id,
                loginpemakai_k.nama_pemakai,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai,
                7 AS track
               FROM (((((historipenatajasa_r
                 JOIN ( SELECT a.obatalkespasien_id,
                        a.obatalkes_id,
                        a.pendaftaran_id
                       FROM obatalkespasien_t a) obatalkespasien_t ON ((historipenatajasa_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id)))
                 JOIN loginpemakai_k ON ((historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id)))
                 JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN tipeperubahan_m ON ((historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220407_085918_migrate_DHC530_view_logactivitypenatajasa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_085918_migrate_DHC530_view_logactivitypenatajasa_v cannot be reverted.\n";

        return false;
    }
    */
}
