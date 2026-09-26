<?php

use yii\db\Migration;

/**
 * Class m211019_053914_migrate_US1832_pendaftaranpenunjangbsl
 */
class m211019_053914_migrate_US1832_pendaftaranpenunjangbsl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanpenunjangbsl_v;');
        $this->execute('
            CREATE VIEW "public"."laporanpenunjangbsl_v" AS
            SELECT row_number() OVER (ORDER BY pasienmasukpenunjang_t.pasienmasukpenunjang_id) AS "No.",
            NULL::text AS "Emp. No",
            pasien_m.no_rekam_medik AS "No. Medical Record",
            pasien_m.nama_pasien AS "Nama Lengkap",
            pasien_m.alamat_pasien AS "Alamat",
            to_char((pasien_m.tanggal_lahir)::timestamp with time zone, \'\'\'dd/mm/yyyy\'::text) AS "Tanggal Lahir",
            "left"((pendaftaran_t.umur)::text, 8) AS "Usia",
            fgetvaluelookup((pasien_m.jeniskelamin)::integer) AS "Jenis Kelamin",
            fgetvaluelookup((pasien_m.statusperkawinan)::integer) AS "Status Perkawainan",
            NULL::text AS "Departemen",
            NULL::text AS " Posisi/Bagian",
            pemeriksaan.tindakan AS "Jenis Medical Check Up/Nama Tindakan",
            \'Mayapada Hospital Kuningan\'::text AS "Nama Perusahaan",
            to_char((pasienmasukpenunjang_t.tglmasukpenunjang)::timestamp with time zone, \'\'\'dd/mm/yyyy\'::text) AS "Tanggal Pemeriksaan",
            NULL::text AS passport,
            pasien_m.alamatemail AS "Email",
            pasien_m.no_telepon_pasien AS "Mobile Phone",
            pasien_m.no_identitas_pasien AS "NIK",
            kabupaten_m.kabupaten_nama AS "Kota/Kabupaten",
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_pemeriksaan,
            kelurahan_m.kode_kelurahan AS "Kode Kelurahan",
            negara_m.kode_negara AS "Kode Negara",
            pendaftaran_t.no_pendaftaran AS "Admission Number",
            to_char(((pasienmasukpenunjang_t.tglmasukpenunjang)::time without time zone)::interval, \'HH24:MI\'::text) AS "Waktu Pemeriksaan"
            FROM (((((((((pasienmasukpenunjang_t
            LEFT JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN negara_m ON ((pasien_m.negara_id = negara_m.negara_id)))
            JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            daftartindakan_m.daftartindakan_nama AS tindakan
            FROM ((tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            JOIN pemeriksaanlab_m ON (((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id) AND (pemeriksaanlab_m.is_deleted = false))))
            WHERE (tindakanpelayanan_t.is_deleted = false)
            UNION ALL
            SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            daftartindakan_m.daftartindakan_nama AS tindakan
            FROM (((tindakanpelayanan_t
            JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
            JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            JOIN pemeriksaanlab_m ON (((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id) AND (pemeriksaanlab_m.is_deleted = false))))
            WHERE (tindakanpelayanan_t.is_deleted = false)) pemeriksaan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = pemeriksaan.pasienmasukpenunjang_id)))
            WHERE (pasienmasukpenunjang_t.is_deleted = false)
            ;');
        $this->execute('
            ALTER TABLE public.laporanpenunjangbsl_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211019_053914_migrate_US1832_pendaftaranpenunjangbsl cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211019_053914_migrate_US1832_pendaftaranpenunjangbsl cannot be reverted.\n";

        return false;
    }
    */
}
