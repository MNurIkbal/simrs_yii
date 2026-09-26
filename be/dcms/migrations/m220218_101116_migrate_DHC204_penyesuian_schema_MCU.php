<?php

use yii\db\Migration;

/**
 * Class m220218_101116_migrate_DHC204_penyesuian_schema_MCU
 */
class m220218_101116_migrate_DHC204_penyesuian_schema_MCU extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."gabungpelayanandetail_t" (
                "gabungpelayanandetail_id" serial8 NOT NULL PRIMARY KEY,
                "pendaftaran_id" int4,
                "ref_pendaftaran_id" int4,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4
                );
        ');

        
        $this->execute('
            ALTER TABLE tindakanpelayanan_t ADD IF NOT EXISTS parent_id int4;
        ');     

        $this->execute('
            COMMENT ON COLUMN "public"."tindakanpelayanan_t"."parent_id" IS \'FK ke tindakanpelayanan_t.tindakanpelayanan_id\';
        ');     

        $this->execute('
            DROP INDEX IF EXISTS "public"."gabungpelayanan_pendaftaran_id";
        ');

        $this->execute('
            CREATE INDEX "gabungpelayanan_pendaftaran_id" ON "public"."gabungpelayanandetail_t" USING btree (
                "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');     

        $this->execute('
            DROP INDEX IF EXISTS "public"."gabungpelayanan_ref_pendaftaran_id";
        ');

        $this->execute('
            CREATE INDEX "gabungpelayanan_ref_pendaftaran_id" ON "public"."gabungpelayanandetail_t" USING btree (
                "ref_pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienmcudetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienmcudetail_v" AS  SELECT \'RAD\'::text AS penunjang, 
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                NULL::integer AS konsulpoli_id,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_nama,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
                tindakanpelayanan_t.tindakanpelayanan_id,
                parent.tipepaket_id AS tindakan_paket_id,
                parent.tipepaket_nama,
                tindakanpelayanan_t.daftartindakan_id AS detail_2id,
                daftartindakan_m.daftartindakan_nama AS detail_2,
                tindakanpelayanan_t.daftartindakan_id AS detail_3id,
                daftartindakan_m.daftartindakan_nama AS detail_3,
                pemeriksaanrad_m.pemeriksaanrad_nama AS pemeriksaan,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
                kesimpulanmcu_t.kesimpulan,
                pasienmasukpenunjang_t.ruangan_id,
                NULL::integer AS pemeriksaanspesialismcu_id,
                (pasienmasukpenunjang_t.status_periksa)::integer AS status_id,
                instalasi_m.instalasi_id
               FROM ((((((((((pendaftaran_t
                 JOIN ( SELECT a.pasienmasukpenunjang_id,
                        a.pendaftaran_id,
                        a.ruangan_id,
                        a.pegawai_id,
                        a.status_periksa
                       FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
                 JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a
                      WHERE (a.instalasi_id = 5)) ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                        a.daftartindakan_id,
                        a.tindakanpelayanan_id,
                        a.parent_id,
                        a.dokterpenanggungjawab_id
                       FROM tindakanpelayanan_t a
                      WHERE (a.is_deleted = false)) tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN ( SELECT a.pemeriksaanradiologi_id,
                        a.pemeriksaanrad_nama,
                        a.jenispemeriksaanrad_id,
                        a.daftartindakan_id
                       FROM pemeriksaanrad_m a) pemeriksaanrad_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                 LEFT JOIN ( SELECT a.jenispemeriksaanrad_id,
                        a.jenispemeriksaanrad_nama
                       FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                 LEFT JOIN ( SELECT a.tindakanpelayanan_id,
                        a.daftartindakan_id,
                        a.kesimpulan
                       FROM kesimpulanmcu_t a
                      WHERE (a.is_deleted = false)) kesimpulanmcu_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = kesimpulanmcu_t.tindakanpelayanan_id) AND (tindakanpelayanan_t.daftartindakan_id = kesimpulanmcu_t.daftartindakan_id))))
                 LEFT JOIN ( SELECT a.tindakanpelayanan_id,
                        a.tipepaket_id,
                        tipepaket_m.tipepaket_nama
                       FROM (tindakanpelayanan_t a
                         JOIN tipepaket_m ON ((a.tipepaket_id = tipepaket_m.tipepaket_id)))
                      WHERE ((a.is_deleted = false) AND (a.is_active = true))) parent ON ((tindakanpelayanan_t.parent_id = parent.tindakanpelayanan_id)))
              WHERE (pendaftaran_t.instalasi_id = 21)
            UNION ALL
             SELECT \'LAB\'::text AS penunjang,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                NULL::integer AS konsulpoli_id,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_nama,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
                tindakanpelayanan_t.tindakanpelayanan_id,
                parent.tipepaket_id AS tindakan_paket_id,
                parent.tipepaket_nama,
                tindakanpelayanan_t.daftartindakan_id AS detail_2id,
                daftartindakan_m.daftartindakan_nama AS detail_2,
                tindakanpelayanan_t.daftartindakan_id AS detail_3id,
                daftartindakan_m.daftartindakan_nama AS detail_3,
                pemeriksaanlab_m.pemeriksaanlab_nama AS pemeriksaan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
                kesimpulanmcu_t.kesimpulan,
                pasienmasukpenunjang_t.ruangan_id,
                NULL::integer AS pemeriksaanspesialismcu_id,
                (pasienmasukpenunjang_t.status_periksa)::integer AS status_id,
                instalasi_m.instalasi_id
               FROM ((((((((((pendaftaran_t
                 JOIN ( SELECT b.pasienmasukpenunjang_id,
                        b.pendaftaran_id,
                        b.ruangan_id,
                        b.pegawai_id,
                        b.status_periksa
                       FROM pasienmasukpenunjang_t b) pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
                 JOIN ( SELECT b.ruangan_id,
                        b.ruangan_nama,
                        b.instalasi_id
                       FROM ruangan_m b
                      WHERE (b.instalasi_id = 4)) ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN ( SELECT b.instalasi_id,
                        b.instalasi_nama
                       FROM instalasi_m b) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN ( SELECT b.pasienmasukpenunjang_id,
                        b.daftartindakan_id,
                        b.tindakanpelayanan_id,
                        b.parent_id,
                        b.dokterpenanggungjawab_id
                       FROM tindakanpelayanan_t b
                      WHERE (b.is_deleted = false)) tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT b.pegawai_id,
                        b.nama_pegawai
                       FROM pegawai_m b) pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT b.daftartindakan_id,
                        b.daftartindakan_nama
                       FROM daftartindakan_m b) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN ( SELECT b.pemeriksaanlab_id,
                        b.pemeriksaanlab_nama,
                        b.jenispemeriksaanlab_id,
                        b.daftartindakan_id
                       FROM pemeriksaanlab_m b) pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                 LEFT JOIN ( SELECT b.jenispemeriksaanlab_id,
                        b.jenispemeriksaanlab_nama
                       FROM jenispemeriksaanlab_m b) jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 LEFT JOIN ( SELECT b.tindakanpelayanan_id,
                        b.daftartindakan_id,
                        b.kesimpulan
                       FROM kesimpulanmcu_t b
                      WHERE (b.is_deleted = false)) kesimpulanmcu_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = kesimpulanmcu_t.tindakanpelayanan_id) AND (tindakanpelayanan_t.daftartindakan_id = kesimpulanmcu_t.daftartindakan_id))))
                 LEFT JOIN ( SELECT b.tindakanpelayanan_id,
                        b.tipepaket_id,
                        tipepaket_m.tipepaket_nama
                       FROM (tindakanpelayanan_t b
                         JOIN tipepaket_m ON ((b.tipepaket_id = tipepaket_m.tipepaket_id)))
                      WHERE ((b.is_deleted = false) AND (b.is_active = true))) parent ON ((tindakanpelayanan_t.parent_id = parent.tindakanpelayanan_id)))
              WHERE (pendaftaran_t.instalasi_id = 21)
            UNION ALL
             SELECT \'RJ\'::text AS penunjang,
                NULL::integer AS pasienmasukpenunjang_id,
                konsulpoli_t.konsulpoli_id,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_nama,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                    CASE
                        WHEN (pemeriksaanspesialismcu_t.pendaftaran_id IS NULL) THEN \'BELUM PERIKSA\'::text
                        WHEN (pemeriksaanspesialismcu_t.pendaftaran_id IS NOT NULL) THEN \'SELESAI\'::text
                        ELSE \'-\'::text
                    END AS status,
                tindakanpelayanan_t.tindakanpelayanan_id,
                parent.tipepaket_id AS tindakan_paket_id,
                parent.tipepaket_nama,
                NULL::integer AS detail_2id,
                daftartindakan_m.daftartindakan_nama AS detail_2,
                NULL::integer AS detail_3id,
                NULL::character varying AS detail_3,
                NULL::character varying AS pemeriksaan,
                NULL::character varying AS jenis,
                (pemeriksaanspesialismcu_t.additional_pemeriksaan)::text AS kesimpulan,
                tindakanpelayanan_t.ruangan_id,
                pemeriksaanspesialismcu_t.pemeriksaanspesialismcu_id,
                pemeriksaanspesialismcu_t.pendaftaran_id AS status_id,
                instalasi_m.instalasi_id
               FROM ((((((((pendaftaran_t
                 JOIN ( SELECT c.pendaftaran_id,
                        c.konsulpoli_id,
                        c.ruangan_id,
                        c.status_periksa
                       FROM konsulpoli_t c) konsulpoli_t ON ((pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id)))
                 JOIN ( SELECT c.tindakanpelayanan_id,
                        c.ruangan_id,
                        c.parent_id,
                        c.daftartindakan_id,
                        c.dokterpenanggungjawab_id,
                        c.konsulpoli_id
                       FROM tindakanpelayanan_t c
                      WHERE (c.is_deleted = false)) tindakanpelayanan_t ON ((konsulpoli_t.konsulpoli_id = tindakanpelayanan_t.konsulpoli_id)))
                 LEFT JOIN ( SELECT c.ruangan_id,
                        c.ruangan_nama,
                        c.instalasi_id
                       FROM ruangan_m c) ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN ( SELECT c.instalasi_id,
                        c.instalasi_nama
                       FROM instalasi_m c) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN ( SELECT c.pegawai_id,
                        c.nama_pegawai
                       FROM pegawai_m c) pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT c.daftartindakan_id,
                        c.daftartindakan_nama
                       FROM daftartindakan_m c) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN ( SELECT c.pemeriksaanspesialismcu_id,
                        c.additional_pemeriksaan,
                        c.pendaftaran_id,
                        c.ruangan_id
                       FROM pemeriksaanspesialismcu_t c
                      WHERE (c.is_deleted = false)) pemeriksaanspesialismcu_t ON (((konsulpoli_t.pendaftaran_id = pemeriksaanspesialismcu_t.pendaftaran_id) AND (konsulpoli_t.ruangan_id = pemeriksaanspesialismcu_t.ruangan_id))))
                 LEFT JOIN ( SELECT c.tindakanpelayanan_id,
                        c.tipepaket_id,
                        tipepaket_m.tipepaket_nama
                       FROM (tindakanpelayanan_t c
                         JOIN tipepaket_m ON ((c.tipepaket_id = tipepaket_m.tipepaket_id)))
                      WHERE ((c.is_deleted = false) AND (c.is_active = true))) parent ON ((tindakanpelayanan_t.parent_id = parent.tindakanpelayanan_id)))
              WHERE ((pendaftaran_t.instalasi_id = 21) AND ((konsulpoli_t.status_periksa)::text <> \'3\'::text));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienlab_v" AS  SELECT \'ORDER\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                pendaftaran_t.no_pendaftaran, 
                pasienmasukpenunjang_t.no_masukpenunjang,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasienmasukpenunjang_t.pegawai_id,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                instalasi_m.instalasi_nama AS asalrujukan_nama,
                pasienmasukpenunjang_t.ruanganasal_id,
                ruangan_m.ruangan_nama,
                pasienmasukpenunjang_t.status_periksa,
                fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
                pasienmasukpenunjang_t.no_antrian,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_opd.carabayar_id
                        ELSE penjamin_ipd.carabayar_id
                    END AS carabayar_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_opd.carabayar_nama
                        ELSE carabayar_ipd.carabayar_nama
                    END AS carabayar_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
                        ELSE pasienadmisi_t.penjamin_id
                    END AS penjamin_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_opd.penjamin_nama
                        ELSE penjamin_ipd.penjamin_nama
                    END AS penjamin_nama,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
                        ELSE pasienadmisi_t.kelaspelayanan_id
                    END AS kelaspelayanan_id,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_opd.kelaspelayanan_nama
                        ELSE kelas_ipd.kelaspelayanan_nama
                    END AS kelaspelayanan_nama,
                pendaftaran_t.umur,
                pasien_m.jeniskelamin,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                pasien_m.tanggal_lahir,
                ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                pasienmasukpenunjang_t.pasien_id,
                pasienadmisi_t.pasienadmisi_id,
                pasienmasukpenunjang_t.ruangan_id,
                pasienmasukpenunjang_t.is_bayar,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN \'472\'::character varying
                        WHEN ((pendaftaran_t.status_periksa)::integer = 2) THEN \'471\'::character varying
                        WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 474) THEN \'471\'::character varying
                        WHEN ((pendaftaran_t.status_periksa)::integer = 474) THEN \'471\'::character varying
                        ELSE pasienkirimkeunitlain_t.status_penunjang
                    END AS status_penunjang,
                pasienmasukpenunjang_t.tanggal_verifikasi,
                pendaftaran_t.instalasi_id,
                    CASE
                        WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> \'lisattr\'::text) ->> \'received_flag\'::text)
                        WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> \'lisattr\'::text) ->> \'received_flag\'::text)
                        ELSE NULL::text
                    END AS received_flag,
                pasienmasukpenunjang_t.is_hasil,
                pasien_m.alamat_pasien,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
                    CASE
                        WHEN (hasil_manual.hasil > 0) THEN true
                        ELSE false
                    END AS is_hasil_manual,
                pasienmasukpenunjang_t.additional_data,
                    CASE
                        WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_hasil_bridging,
                COALESCE(tindakanpelayanan.status, \'Batal\'::text) AS status_bayar,
                    CASE
                        WHEN (tindakanpelayanan.status = \'Sudah Bayar\'::text) THEN true
                        ELSE false
                    END AS is_status_bayar,
                    CASE
                        WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar_detail,
                COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
                fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
                pegawai_m.tanda_tangan,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = \'Belum Bayar\'::text)) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) AS pemeriksaan
               FROM (((((((((((((((((((pasienmasukpenunjang_t
                 JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                 LEFT JOIN penjamin_m penjamin_opd ON ((pendaftaran_t.penjamin_id = penjamin_opd.penjamin_id)))
                 LEFT JOIN penjamin_m penjamin_ipd ON ((pasienadmisi_t.penjamin_id = penjamin_ipd.penjamin_id)))
                 LEFT JOIN carabayar_m carabayar_opd ON ((penjamin_opd.carabayar_id = carabayar_opd.carabayar_id)))
                 LEFT JOIN carabayar_m carabayar_ipd ON ((penjamin_ipd.carabayar_id = carabayar_ipd.carabayar_id)))
                 LEFT JOIN kelaspelayanan_m kelas_opd ON ((pendaftaran_t.kelaspelayanan_id = kelas_opd.kelaspelayanan_id)))
                 LEFT JOIN kelaspelayanan_m kelas_ipd ON ((pasienadmisi_t.kelaspelayanan_id = kelas_ipd.kelaspelayanan_id)))
                 LEFT JOIN pegawai_m dokter_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dokter_perujuk.pegawai_id)))
                 LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        count(*) AS hasil
                       FROM (hasilpemeriksaanlabdetail_t
                         JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS jml_hasil,
                        hasilpemeriksaanlab_integrasi_t.order_no
                       FROM hasilpemeriksaanlab_integrasi_t
                      GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Belum Bayar\'::text
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Sudah Bayar\'::text
                                ELSE \'Batal\'::text
                            END AS status
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted = false)
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Belum Bayar\'::text
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Sudah Bayar\'::text
                                ELSE \'Batal\'::text
                            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.cyto_tindakan
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
              WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pendaftaran_t.is_indolab = false))
            UNION ALL
             SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
                pendaftaran_t.no_pendaftaran,
                pasienmasukpenunjang_t.no_masukpenunjang,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasienmasukpenunjang_t.pegawai_id,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                rujukan_t.no_rujukan,
                rujukan_t.asalrujukan_id,
                asalrujukan_m.asalrujukan_nama,
                rujukan_t.rujukandari_id AS ruanganasal_id,
                perujuk_m.namaperujuk AS ruangan_nama,
                pasienmasukpenunjang_t.status_periksa,
                fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
                pasienmasukpenunjang_t.no_antrian,
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pendaftaran_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.umur,
                pasien_m.jeniskelamin,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                pasien_m.tanggal_lahir,
                ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
                pasienmasukpenunjang_t.pasien_id,
                NULL::integer AS pasienadmisi_id,
                pasienmasukpenunjang_t.ruangan_id,
                pasienmasukpenunjang_t.is_bayar,
                pasienkirimkeunitlain_t.status_penunjang,
                pasienmasukpenunjang_t.tanggal_verifikasi,
                pendaftaran_t.instalasi_id,
                    CASE
                        WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> \'lisattr\'::text) ->> \'received_flag\'::text)
                        ELSE NULL::text
                    END AS received_flag,
                pasienmasukpenunjang_t.is_hasil,
                pasien_m.alamat_pasien,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
                    CASE
                        WHEN (hasil_manual.hasil > 0) THEN true
                        ELSE false
                    END AS is_hasil_manual,
                pasienmasukpenunjang_t.additional_data,
                    CASE
                        WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_hasil_bridging,
                COALESCE(tindakanpelayanan.status, \'Batal\'::text) AS status_bayar,
                    CASE
                        WHEN (tindakanpelayanan.status = \'Sudah Bayar\'::text) THEN true
                        ELSE false
                    END AS is_status_bayar,
                    CASE
                        WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar_detail,
                COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
                fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
                pegawai_m.tanda_tangan,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = \'Belum Bayar\'::text)) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) AS pemeriksaan
               FROM ((((((((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                 LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                 LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                 LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        count(*) AS hasil
                       FROM (hasilpemeriksaanlabdetail_t
                         JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS jml_hasil,
                        hasilpemeriksaanlab_integrasi_t.order_no
                       FROM hasilpemeriksaanlab_integrasi_t
                      GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Belum Bayar\'::text
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Sudah Bayar\'::text
                                ELSE \'Batal\'::text
                            END AS status
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted = false)
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Belum Bayar\'::text
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Sudah Bayar\'::text
                                ELSE \'Batal\'::text
                            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.cyto_tindakan
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
              WHERE ((pendaftaran_t.instalasi_id = 4) AND (pendaftaran_t.is_aps = false) AND (pendaftaran_t.is_indolab = false))
            UNION ALL
             SELECT \'APS\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
                pendaftaran_t.no_pendaftaran,
                pasienmasukpenunjang_t.no_masukpenunjang,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pendaftaran_t.pegawai_id,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                NULL::character varying AS no_rujukan,
                pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                \'APS\'::character varying AS asalrujukan_nama,
                pasienmasukpenunjang_t.ruanganasal_id,
                ruangan_m.ruangan_nama,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN \'476\'::character varying
                        ELSE pasienmasukpenunjang_t.status_periksa
                    END AS status_periksa,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN \'BATAL\'::character varying
                        ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
                    END AS status_periksa_nama,
                pasienmasukpenunjang_t.no_antrian,
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pendaftaran_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.umur,
                pasien_m.jeniskelamin,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                pasien_m.tanggal_lahir,
                ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
                pasienmasukpenunjang_t.pasien_id,
                NULL::integer AS pasienadmisi_id,
                pasienmasukpenunjang_t.ruangan_id,
                pasienmasukpenunjang_t.is_bayar,
                    CASE
                        WHEN ((pendaftaran_t.status_periksa)::integer = 628) THEN \'472\'::character varying
                        WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 477) THEN \'471\'::character varying
                        WHEN ((pendaftaran_t.status_periksa)::integer = 2) THEN \'471\'::character varying
                        WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 474) THEN \'471\'::character varying
                        WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 475) THEN \'471\'::character varying
                        WHEN ((pasienmasukpenunjang_t.status_periksa)::integer = 473) THEN \'471\'::character varying
                        ELSE pasienkirimkeunitlain_t.status_penunjang
                    END AS status_penunjang,
                pasienmasukpenunjang_t.tanggal_verifikasi,
                pendaftaran_t.instalasi_id,
                    CASE
                        WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> \'lisattr\'::text) ->> \'received_flag\'::text)
                        WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> \'lisattr\'::text) ->> \'received_flag\'::text)
                        ELSE NULL::text
                    END AS received_flag,
                pasienmasukpenunjang_t.is_hasil,
                pasien_m.alamat_pasien,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
                    CASE
                        WHEN (hasil_manual.hasil > 0) THEN true
                        ELSE false
                    END AS is_hasil_manual,
                pasienmasukpenunjang_t.additional_data,
                    CASE
                        WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_hasil_bridging,
                    CASE
                        WHEN (tindakanpelayanan.pendaftaran_id IS NULL) THEN \'Belum Bayar\'::text
                        ELSE COALESCE(tindakanpelayanan.status, \'Batal\'::text)
                    END AS status_bayar,
                    CASE
                        WHEN (tindakanpelayanan.status = \'Sudah Bayar\'::text) THEN true
                        ELSE false
                    END AS is_status_bayar,
                    CASE
                        WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar_detail,
                COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
                fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
                pegawai_m.tanda_tangan,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = \'Belum Bayar\'::text)) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) AS pemeriksaan
               FROM ((((((((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
                 LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                 LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        count(*) AS hasil
                       FROM (hasilpemeriksaanlabdetail_t
                         JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS jml_hasil,
                        hasilpemeriksaanlab_integrasi_t.order_no
                       FROM hasilpemeriksaanlab_integrasi_t
                      GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Belum Bayar\'::text
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Sudah Bayar\'::text
                                ELSE \'Batal\'::text
                            END AS status
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted = false)
                      GROUP BY tindakanpelayanan_t.pendaftaran_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Belum Bayar\'::text
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Sudah Bayar\'::text
                                ELSE \'Batal\'::text
                            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan.pendaftaran_id)))
                 LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.cyto_tindakan
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
              WHERE ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pendaftaran_t.is_indolab = false) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienlabdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienlabdetail_v" AS  SELECT \'NON_PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id, 
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                tindakanpelayanan_t.tipepaket_id,
                \'\'::character varying AS tipepaket_nama,
                NULL::text AS detail_2,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                ambilsample_t.ambilsample_id,
                tindakanpelayanan_t.qty_tindakan,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasien_id,
                tindakanpelayanan_t.is_deleted,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        ELSE true
                    END AS is_bayar,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Belum Bayar\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Batal Bayar\'::text
                        ELSE \'Sudah Bayar\'::text
                    END AS status_bayar,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN \'BATAL\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'BELUM PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN \'SELESAI\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                        ELSE NULL::text
                    END AS status_periksa,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN 476
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 477
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 473
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 475
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 476
                        ELSE NULL::integer
                    END AS status_periksa_id,
                tindakanpelayanan_t.ruangan_id,
                tindakanpelayanan_t.kelaspelayanan_id,
                tindakanpelayanan_t.penjamin_id,
                tindakanpelayanan_t.carabayar_id,
                tindakanpelayanan_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
                tindakanpelayanan_t.tindakanpelayananasal_id
               FROM ((((((((pendaftaran_t
                 JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                 LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 LEFT JOIN ambilsample_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
                       FROM hasilpemeriksaanlabdetail_t
                      WHERE (hasilpemeriksaanlabdetail_t.is_deleted = false)
                      GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON ((tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id)))
              WHERE (daftartindakan_m.kelompoktindakan_id = 26)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                tindakanpelayanan_t.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                NULL::text AS detail_2,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                ambilsample_t.ambilsample_id,
                tindakanpelayanan_t.qty_tindakan,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasien_id,
                tindakanpelayanan_t.is_deleted,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
                        ELSE true
                    END AS is_bayar,
                    CASE
                        WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Belum Bayar\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'Batal Bayar\'::text
                        ELSE \'Sudah Bayar\'::text
                    END AS status_bayar,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN \'BATAL\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'BELUM PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN \'PERIKSA\'::text
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN \'SELESAI\'::text
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                        ELSE NULL::text
                    END AS status_periksa,
                    CASE
                        WHEN (tindakanpelayanan_t.is_deleted = true) THEN 476
                        WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 477
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 473
                        WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 475
                        WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 476
                        ELSE NULL::integer
                    END AS status_periksa_id,
                tindakanpelayanan_t.ruangan_id,
                tindakanpelayanan_t.kelaspelayanan_id,
                tindakanpelayanan_t.penjamin_id,
                tindakanpelayanan_t.carabayar_id,
                tindakanpelayanan_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
                tindakanpelayanan_t.tindakanpelayananasal_id
               FROM ((((((((((pendaftaran_t
                 JOIN tindakanpelayanan_t ON (((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.parent_id IS NOT NULL))))
                 LEFT JOIN pasienmasukpenunjang_t ON (((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id) AND (pasienmasukpenunjang_t.instalasiasal_id = 4))))
                 LEFT JOIN tipepaket_m ON (((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id) AND (tipepaket_m.is_mcu = false))))
                 JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                 LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 LEFT JOIN ambilsample_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
                       FROM hasilpemeriksaanlabdetail_t
                      WHERE (hasilpemeriksaanlabdetail_t.is_deleted = false)
                      GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON ((tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id)))
              WHERE (daftartindakan_m.kelompoktindakan_id = 26);
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienlaboratorium_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienlaboratorium_v" AS  SELECT header.tipe_pasien,
                header.pendaftaran_id,
                header.pasienmasukpenunjang_id,
                header.pasienkirimkeunitlain_id,
                header.tglmasukpenunjang,
                header.no_pendaftaran,
                header.no_masukpenunjang,
                header.no_rekam_medik,
                header.nama_pasien,
                detail.dokter_id_detail AS pegawai_id,
                detail.dokter_nama_detail AS dokter_penunjang,
                header.no_rujukan,
                header.asalrujukan_id,
                header.asalrujukan_nama,
                header.ruanganasal_id,
                header.ruangan_nama,
                header.status_periksa,
                header.no_antrian,
                header.carabayar_id,
                header.carabayar_nama,
                header.penjamin_id,
                header.penjamin_nama,
                header.kelaspelayanan_id,
                header.kelaspelayanan_nama,
                header.umur,
                header.jeniskelamin,
                header.j_kelamin,
                header.tanggal_lahir,
                header.kuning,
                header.merah,
                header.ungu,
                header.coklat,
                header.tgl_rujukan,
                header.pasien_id,
                header.pasienadmisi_id,
                header.ruangan_id,
                header.is_bayar,
                header.status_penunjang,
                header.catatan_dokterpengirim,
                header.no_telepon_pasien,
                header.dokter_perujuk_id,
                header.dokter_perujuk_nama,
                detail.dokter_nama_detail AS nama_dokter_penunjang,
                header.unit_asal,
                header.nama_diagnosa,
                false AS is_mcu,
                detail.jenispemeriksaanlab_nama,
                detail.tipepaket_nama,
                detail.detail_2,
                detail.daftartindakan_id,
                detail.daftartindakan_kode,
                detail.daftartindakan_nama,
                    CASE
                        WHEN (hasil.is_hasil >= 1) THEN true
                        ELSE false
                    END AS is_hasil,
                detail.tindakanpelayanan_id,
                hasil.tgl_verifikasi,
                header.created_by,
                header.penjamin_kode,
                detail.cyto_tindakan,
                detail.qty_tindakan,
                header.no_identitas_pasien,
                detail.hasilpemeriksaanlab_id,
                detail.status_bayar,
                detail.status_periksa_penunjang,
                detail.status_batal,
                header.groupcarabayar_id,
                header.sepesial_pemeriksaan,
                header.jenis_kelamin_kode,
                    CASE
                        WHEN (hasil.tgl_verifikasi IS NOT NULL) THEN true
                        ELSE false
                    END AS is_selesai,
                hasil.status_pemeriksaan,
                hasil.status_pemeriksaan_id
               FROM ((( SELECT \'ORDER\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pasienmasukpenunjang_t.tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasienmasukpenunjang_t.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                        pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                        instalasi_m.instalasi_nama AS asalrujukan_nama,
                        pasienmasukpenunjang_t.ruanganasal_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        penjamin_m.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pasienadmisi_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pasienadmisi_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        pasienadmisi_t.pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                        true AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM ((((((((((((((pasienmasukpenunjang_t
                         JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                         JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                         JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                      WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
                    UNION ALL
                     SELECT \'ORDER\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pasienmasukpenunjang_t.tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasienmasukpenunjang_t.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                        pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                        instalasi_m.instalasi_nama AS asalrujukan_nama,
                        pasienmasukpenunjang_t.ruanganasal_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        pendaftaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pendaftaran_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        pasienadmisi_t.pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                            CASE
                                WHEN (pendaftaran_t.instalasi_id = 2) THEN true
                                ELSE false
                            END AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM ((((((((((((((pasienmasukpenunjang_t
                         JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                         JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                         JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                      WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
                    UNION ALL
                     SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasienmasukpenunjang_t.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        rujukan_t.no_rujukan,
                        rujukan_t.asalrujukan_id,
                        asalrujukan_m.asalrujukan_nama,
                        rujukan_t.rujukandari_id AS ruanganasal_id,
                        perujuk_m.namaperujuk AS ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        pendaftaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pendaftaran_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        NULL::integer AS pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                        false AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM ((((((((((((((pasienmasukpenunjang_t
                         JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                         LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
                         JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                      WHERE (pendaftaran_t.instalasi_id = 4)
                    UNION ALL
                     SELECT \'APS\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        NULL::character varying AS no_rujukan,
                        pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                        \'APS\'::character varying AS asalrujukan_nama,
                        pasienmasukpenunjang_t.ruanganasal_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        pendaftaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pendaftaran_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        NULL::integer AS pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                        false AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM (((((((((((((((pasienmasukpenunjang_t
                         JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                         JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                         JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                         LEFT JOIN ruangan_m ruangan_pendaftaran ON ((pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id)))
                      WHERE ((ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.is_bayar = true))) header
                 LEFT JOIN ( SELECT \'NON_PAKET\'::text AS jenis,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.tgl_tindakan,
                        jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                        tindakanpelayanan_t.tipepaket_id,
                        \'\'::character varying AS tipepaket_nama,
                        NULL::text AS detail_2,
                        tindakanpelayanan_t.daftartindakan_id,
                        daftartindakan_m.daftartindakan_kode,
                        daftartindakan_m.daftartindakan_nama,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan,
                        tindakanpelayanan_t.qty_tindakan,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasien_id,
                        pegawai_m.pegawai_id AS dokter_id_detail,
                        pegawai_m.nama_pegawai AS dokter_nama_detail,
                        pasienmasukpenunjang_t.created_by,
                        hasilpemeriksaanlab.hasilpemeriksaanlab_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN false
                                ELSE true
                            END AS status_bayar,
                            CASE
                                WHEN (hasilpemeriksaanlab.tindakanpelayanan_id IS NULL) THEN \'BELUM PERIKSA\'::text
                                WHEN (hasilpemeriksaanlab.tindakanpelayanan_id IS NOT NULL) THEN \'SELESAI\'::text
                                WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                                ELSE NULL::text
                            END AS status_periksa_penunjang,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN true
                                ELSE false
                            END AS status_batal
                       FROM (((((((pasienmasukpenunjang_t
                         JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                         JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                         LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                         LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE((permintaankepenunjang_t.dokter_id)::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN ( SELECT hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                                hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                                hasilpemeriksaanlab_t.tindakanpelayanan_id
                               FROM hasilpemeriksaanlab_t
                              WHERE (hasilpemeriksaanlab_t.is_deleted IS FALSE)) hasilpemeriksaanlab ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanlab.tindakanpelayanan_id))))
                      WHERE (daftartindakan_m.kelompoktindakan_id = 26)
                    UNION ALL
                     SELECT \'PAKET\'::text AS jenis,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.tgl_tindakan,
                        jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                        tindakanpelayanan_t.tipepaket_id,
                        tipepaket_m.tipepaket_nama,
                        NULL::text AS detail_2,
                        paketpelayanan_mp.daftartindakan_id,
                        daftartindakan_m.daftartindakan_kode,
                        daftartindakan_m.daftartindakan_nama,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan,
                        tindakanpelayanan_t.qty_tindakan,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasien_id,
                        pegawai_m.pegawai_id AS dokter_id_detail,
                        pegawai_m.nama_pegawai AS dokter_nama_detail,
                        pasienmasukpenunjang_t.created_by,
                        hasilpemeriksaanlab.hasilpemeriksaanlab_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN false
                                ELSE true
                            END AS status_bayar,
                            CASE
                                WHEN (hasilpemeriksaanlab.tindakanpelayanan_id IS NULL) THEN \'BELUM PERIKSA\'::text
                                WHEN (hasilpemeriksaanlab.tindakanpelayanan_id IS NOT NULL) THEN \'SELESAI\'::text
                                WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                                ELSE NULL::text
                            END AS status_periksa_penunjang,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN true
                                ELSE false
                            END AS status_batal
                       FROM (((((((((pasienmasukpenunjang_t
                         JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                         JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                         JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                         LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                         LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE((permintaankepenunjang_t.dokter_id)::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN ( SELECT hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                                hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                                hasilpemeriksaanlab_t.tindakanpelayanan_id
                               FROM hasilpemeriksaanlab_t
                              WHERE (hasilpemeriksaanlab_t.is_deleted IS FALSE)) hasilpemeriksaanlab ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanlab.tindakanpelayanan_id))))
                      WHERE (daftartindakan_m.kelompoktindakan_id = 10)) detail ON ((header.pasienmasukpenunjang_id = detail.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS is_hasil,
                        hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        hasilpemeriksaanlab_t.tindakanpelayanan_id, 
                        pemeriksaanlab_m.daftartindakan_id,
                        hasilpemeriksaanlab_t.tgl_verifikasi,
                        hasilpemeriksaanlab_t.status_pemeriksaan AS status_pemeriksaan_id,
                        fgetnamalookup(hasilpemeriksaanlab_t.status_pemeriksaan) AS status_pemeriksaan
                       FROM (hasilpemeriksaanlab_t
                         LEFT JOIN pemeriksaanlab_m ON ((pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlab_t.pemeriksaanlab_id)))
                      WHERE (hasilpemeriksaanlab_t.is_deleted = false)
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id, hasilpemeriksaanlab_t.tindakanpelayanan_id, pemeriksaanlab_m.daftartindakan_id, hasilpemeriksaanlab_t.tgl_verifikasi, hasilpemeriksaanlab_t.status_pemeriksaan) hasil ON (((header.pasienmasukpenunjang_id = hasil.pasienmasukpenunjang_id) AND (detail.tindakanpelayanan_id = hasil.tindakanpelayanan_id))));

        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanhasillab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanhasillab_v" AS  SELECT \'rujukan\'::text AS tipe,
                hasil_lab.hasilpemeriksaanlab_wynacom_id,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.no_masukpenunjang,
                pasien_m.no_rekam_medik, 
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir AS dateofbirth,
                pendaftaran_t.umur,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
                pasien_m.alamat_pasien,
                ruangan_m.ruangan_id AS lokasi_id,
                ruangan_m.ruangan_nama AS lokasi_nama,
                instalasi_m.instalasi_nama,
                dokter_pengirim.nama_pegawai AS dokter_pengirim,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_transaksi,
                pegawai_m.pegawai_id AS dokter_penunjangid,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tglpermintaankepenunjang AS tglpenunjang,
                permintaankepenunjang_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                hasil_lab.test_group,
                hasil_lab.test_name AS test_nama_lis,
                hasil_lab.result AS hasil,
                hasil_lab.reference_value AS nilai_rujukan,
                hasil_lab.test_units_name AS satuan,
                hasil_lab.authorization_date AS tgl_pemeriksaan,
                pasienmasukpenunjang_t.catatan,
                hasil_lab.authorization_user AS petugas_pemeriksaan,
                hasil_lab.test_method,
                pasienmasukpenunjang_t.is_hasil,
                hasil_last.authorization_user,
                hasil_lab.authorization_date AS tgl_hasil,
                CURRENT_TIMESTAMP AS tgl_cetak,
                hasil_lab.is_print,
                pemeriksaanlab_m.is_exception,
                hasil_lab.lis_test_id,
                hasil_lab.test_flag_sign
               FROM (((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pegawai_m dokter_pengirim ON ((pendaftaran_t.pegawai_id = dokter_pengirim.pegawai_id)))
                 JOIN hasilpemeriksaanlab_wynacom_t hasil_lab ON ((((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_lab.his_reg_no)::text) AND ((daftartindakan_m.daftartindakan_kode)::text = (hasil_lab.his_test_id)::text))))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no,
                        hasilpemeriksaanlab_wynacom_t.authorization_date,
                        hasilpemeriksaanlab_wynacom_t.authorization_user
                       FROM hasilpemeriksaanlab_wynacom_t
                      ORDER BY hasilpemeriksaanlab_wynacom_t.hasilpemeriksaanlab_wynacom_id DESC
                     LIMIT 1) hasil_last ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_last.his_reg_no)::text)))
                 LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
            UNION ALL
             SELECT \'APS\'::text AS tipe,
                hasil_lab.hasilpemeriksaanlab_wynacom_id,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.no_masukpenunjang,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir AS dateofbirth,
                pendaftaran_t.umur,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
                pasien_m.alamat_pasien,
                ruangan_m.ruangan_id AS lokasi_id,
                ruangan_m.ruangan_nama AS lokasi_nama,
                instalasi_m.instalasi_nama,
                dokter_pengirim.nama_pegawai AS dokter_pengirim,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_transaksi,
                pegawai_m.pegawai_id AS dokter_penunjangid,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                tindakanpelayanan_t.cyto_tindakan AS is_cyto,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tglpenunjang,
                daftartindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                hasil_lab.test_group,
                hasil_lab.test_name AS test_nama_lis,
                hasil_lab.result AS hasil,
                hasil_lab.reference_value AS nilai_rujukan,
                hasil_lab.test_units_name AS satuan,
                hasil_lab.authorization_date AS tgl_pemeriksaan,
                pasienmasukpenunjang_t.catatan,
                hasil_lab.authorization_user AS petugas_pemeriksaan,
                hasil_lab.test_method,
                pasienmasukpenunjang_t.is_hasil,
                hasil_last.authorization_user,
                hasil_lab.authorization_date AS tgl_hasil,
                CURRENT_TIMESTAMP AS tgl_cetak,
                hasil_lab.is_print,
                pemeriksaanlab_m.is_exception,
                hasil_lab.lis_test_id,
                hasil_lab.test_flag_sign
               FROM ((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN pegawai_m dokter_pengirim ON ((pendaftaran_t.pegawai_id = dokter_pengirim.pegawai_id)))
                 LEFT JOIN hasilpemeriksaanlab_wynacom_t hasil_lab ON ((((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_lab.his_reg_no)::text) AND ((daftartindakan_m.daftartindakan_kode)::text = (hasil_lab.his_test_id)::text))))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no,
                        hasilpemeriksaanlab_wynacom_t.authorization_date,
                        hasilpemeriksaanlab_wynacom_t.authorization_user
                       FROM hasilpemeriksaanlab_wynacom_t
                      ORDER BY hasilpemeriksaanlab_wynacom_t.hasilpemeriksaanlab_wynacom_id DESC
                     LIMIT 1) hasil_last ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_last.his_reg_no)::text)))
                 LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
              WHERE (pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL);
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infoorderanlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanlab_v" AS  SELECT \'RJRD\'::text AS tipe,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.pendaftaran_id,
                pasienkirimkeunitlain_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                pendaftaran_t.pasien_id,
                pasien_m.no_rekam_medik, 
                pasien_m.nama_pasien,
                pendaftaran_t.umur,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.instalasi_id,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                NULL::character varying AS kamarruangan_nokamar,
                NULL::character varying AS no_tempattidur,
                pendaftaran_t.pegawai_id,
                pegawai_m.nama_pegawai AS dokter_perujuk,
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pasienkirimkeunitlain_t.status_penunjang,
                fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS stat_penunjang,
                pendaftaran_t.kelaspelayanan_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.kunjungan,
                pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
                pasien_m.tanggal_lahir,
                pendaftaran_t.status_pasien,
                carabayar_m.groupcarabayar_id,
                pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
                    CASE
                        WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN true
                        ELSE false
                    END AS is_bayar,
                pasienmasukpenunjang_t.status_periksa,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                    CASE
                        WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienkirimkeunitlain_t.is_rujukan,
                COALESCE(pemeriksaan.jml_pemeriksaan, (0)::bigint) AS jml_pemeriksaan,
                COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, (0)::bigint) AS jml_pemeriksaan_approve,
                COALESCE(tindakanpelayanan.jumlah_tagihan, (0)::double precision) AS jumlah_tagihan,
                COALESCE(tindakan_bayar.jumlah_bayar, (0)::double precision) AS jumlah_bayar,
                fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
                COALESCE(cyto_tindakan.is_cyto, false) AS is_cyto,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan_batal,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM ((permintaankepenunjang_t
                                 JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                                 JOIN daftartindakan_m ON ((pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((permintaankepenunjang_t.is_approve = false) AND (permintaankepenunjang_t.is_deleted = false) AND (permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id))
                            UNION ALL
                             SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = true))) x) AS pemeriksaan,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) AS pemeriksaan_dibatalkan,
                    CASE
                        WHEN (pemeriksaan.jml_pemeriksaan = pemeriksaan_approve.jml_pemeriksaan_approve) THEN true
                        ELSE false
                    END AS is_approved_all
               FROM ((((((((((((((pasienkirimkeunitlain_t
                 JOIN pendaftaran_t ON ((pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                        count(*) AS jml_pemeriksaan
                       FROM permintaankepenunjang_t
                      WHERE (permintaankepenunjang_t.is_deleted IS FALSE)
                      GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id)))
                 LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                        count(*) AS jml_pemeriksaan_approve
                       FROM permintaankepenunjang_t
                      WHERE ((permintaankepenunjang_t.is_deleted IS FALSE) AND (permintaankepenunjang_t.is_approve IS TRUE))
                      GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_tagihan
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false))
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_bayar
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false))
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
                        permintaankepenunjang_t.is_cyto
                       FROM permintaankepenunjang_t
                      WHERE (permintaankepenunjang_t.is_cyto = true)) cyto_tindakan ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
            UNION ALL
             SELECT \'RI\'::text AS tipe,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pendaftaran_t.pendaftaran_id,
                pasienkirimkeunitlain_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                pendaftaran_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pendaftaran_t.umur,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                kelaspelayanan_m.kelaspelayanan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                pasienadmisi_t.pegawai_id,
                pegawai_m.nama_pegawai AS dokter_perujuk,
                penjamin_m.carabayar_id,
                carabayar_m.carabayar_nama,
                pasienadmisi_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pasienkirimkeunitlain_t.status_penunjang,
                fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS stat_penunjang,
                pasienadmisi_t.kelaspelayanan_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pasienadmisi_t.ruangan_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.kunjungan,
                pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
                pasien_m.tanggal_lahir,
                pendaftaran_t.status_pasien,
                carabayar_m.groupcarabayar_id,
                pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
                    CASE
                        WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN true
                        ELSE false
                    END AS is_bayar,
                pasienmasukpenunjang_t.status_periksa,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                    CASE
                        WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienkirimkeunitlain_t.is_rujukan,
                COALESCE(pemeriksaan.jml_pemeriksaan, (0)::bigint) AS jml_pemeriksaan,
                COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, (0)::bigint) AS jml_pemeriksaan_approve,
                COALESCE(tindakanpelayanan.jumlah_tagihan, (0)::double precision) AS jumlah_tagihan,
                COALESCE(tindakan_bayar.jumlah_bayar, (0)::double precision) AS jumlah_bayar,
                fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
                COALESCE(cyto_tindakan.is_cyto, false) AS is_cyto,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan_batal,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM ((permintaankepenunjang_t
                                 JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                                 JOIN daftartindakan_m ON ((pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((permintaankepenunjang_t.is_approve = false) AND (permintaankepenunjang_t.is_deleted = false) AND (permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id))) x) AS pemeriksaan,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM ((permintaankepenunjang_t
                                 JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                                 JOIN daftartindakan_m ON ((pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((permintaankepenunjang_t.is_approve = true) AND (permintaankepenunjang_t.is_deleted = false) AND (permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id))) x) AS pemeriksaan_dibatalkan,
                    CASE
                        WHEN (pemeriksaan.jml_pemeriksaan = pemeriksaan_approve.jml_pemeriksaan_approve) THEN true
                        ELSE false
                    END AS is_approved_all
               FROM (((((((((((((((((pasienkirimkeunitlain_t
                 JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                        count(*) AS jml_pemeriksaan
                       FROM permintaankepenunjang_t
                      WHERE (permintaankepenunjang_t.is_deleted IS FALSE)
                      GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id)))
                 LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                        count(*) AS jml_pemeriksaan_approve
                       FROM permintaankepenunjang_t
                      WHERE ((permintaankepenunjang_t.is_deleted IS FALSE) AND (permintaankepenunjang_t.is_approve IS TRUE))
                      GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_tagihan
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false))
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_bayar
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false))
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
                        permintaankepenunjang_t.is_cyto
                       FROM permintaankepenunjang_t
                      WHERE (permintaankepenunjang_t.is_cyto = true)) cyto_tindakan ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
            UNION ALL
             SELECT \'Penunjang\'::text AS tipe,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id AS pasienkirimkeunitlain_id,
                pendaftaran_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
                pasienmasukpenunjang_t.no_masukpenunjang AS no_rujukan,
                pendaftaran_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pendaftaran_t.umur,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                kelaspelayanan_m.kelaspelayanan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                NULL::character varying AS kamarruangan_nokamar,
                NULL::character varying AS no_tempattidur,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai AS dokter_perujuk,
                penjamin_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_nama,
                    CASE
                        WHEN (( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM (tindakanpelayanan_t
                                     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                                  WHERE ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) IS NULL) THEN \'471\'::character varying
                        ELSE \'472\'::character varying
                    END AS status_penunjang,
                    CASE
                        WHEN (( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                           FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                                   FROM (tindakanpelayanan_t
                                     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                                  WHERE ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))) x) IS NULL) THEN \'DISETUJUI\'::character varying
                        ELSE \'BATAL\'::character varying
                    END AS stat_penunjang,
                pendaftaran_t.kelaspelayanan_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.kunjungan,
                pasienmasukpenunjang_t.ruangan_id AS ruanganpenunjang_id,
                pasien_m.tanggal_lahir,
                pendaftaran_t.status_pasien,
                carabayar_m.groupcarabayar_id,
                pasienmasukpenunjang_t.instalasiasal_id AS instalasipen_id,
                    CASE
                        WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN true
                        ELSE false
                    END AS is_bayar,
                pasienmasukpenunjang_t.status_periksa,
                pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
                    CASE
                        WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienmasukpenunjang_t.is_bayar AS is_rujukan,
                NULL::bigint AS jml_pemeriksaan,
                NULL::bigint AS jml_pemeriksaan_approve,
                NULL::double precision AS jumlah_tagihan,
                NULL::double precision AS jumlah_bayar,
                fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
                NULL::boolean AS is_cyto,
                pendaftaran_t.last_modified_date AS tgl_rujukan_batal,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) = (tindakanpelayanan.status = \'Belum Bayar\'::text)) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))
                            UNION ALL
                             SELECT paket_tindakan.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (((tindakanpelayanan_t
                                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                                 JOIN paketpelayanan_mp ON ((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                                 JOIN daftartindakan_m paket_tindakan ON ((paketpelayanan_mp.daftartindakan_id = paket_tindakan.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id))) x) AS pemeriksaan,
                ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                       FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (tindakanpelayanan_t
                                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))
                            UNION ALL
                             SELECT paket_tindakan.daftartindakan_nama AS pemeriksaanlab_nama
                               FROM (((tindakanpelayanan_t
                                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                                 JOIN paketpelayanan_mp ON ((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                                 JOIN daftartindakan_m paket_tindakan ON ((paketpelayanan_mp.daftartindakan_id = paket_tindakan.daftartindakan_id)))
                              WHERE (tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)) x) AS pemeriksaan_dibatalkan,
                NULL::boolean AS is_approved_all
               FROM (((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Belum Bayar\'::text
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN \'Sudah Bayar\'::text
                                ELSE \'Batal\'::text
                            END AS status,
                        sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_tagihan
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false))
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_bayar
                       FROM tindakanpelayanan_t
                      WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false))
                      GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id)))
              WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[4, 5, 7, 21])) AND ((pasienmasukpenunjang_t.no_masukpenunjang)::text ~~* \'%LAB%\'::text));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."nilaipemeriksaanlabdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."nilaipemeriksaanlabdetail_v" AS  SELECT x.pemeriksaanlab_id,
                x.daftartindakan_id,
                x.daftartindakan_nama,
                x.tipepaket_id,
                x.tipepaket_nama, 
                x.nilairujukan_id,
                x.nama_rujukan,
                x.jenis_kelamin,
                x.jenis_kelamin_nama,
                x.golonganumur_id,
                x.gol_umurlab_nama,
                x.gol_umurlab_minimal,
                x.gol_umurlab_maksimal,
                x.nilai_rujukan,
                x.nilai_min,
                x.nilai_max,
                x.satuanlab_nama,
                x.keterangan,
                x.hasil,
                x.petugaslab_id,
                x.hasilpemeriksaanlabdetail_id,
                x.petugaslab_nama,
                x.samplelab_id,
                x.pasienmasukpenunjang_id,
                x.hasilpemeriksaanlab_id,
                x.is_deleted,
                x.metode,
                x.jenispemeriksaanlab_id,
                x.jenispemeriksaanlab_nama,
                x.no_urut,
                x.is_verifikasi,
                x.tanggal_verifikasi,
                x.petugas_verifikasi,
                x.tipe
               FROM ( SELECT pemeriksaanlab_m.pemeriksaanlab_id,
                        pemeriksaanlab_m.daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        NULL::integer AS tipepaket_id,
                        NULL::character varying AS tipepaket_nama,
                        nilairujukan_m.nilairujukan_id,
                        COALESCE(nilairujukan_m.nama_rujukan, daftartindakan_m.daftartindakan_nama) AS nama_rujukan,
                        nilairujukan_m.jenis_kelamin,
                        fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
                        nilairujukan_m.golonganumur_id,
                        golonganumurlab_m.gol_umurlab_nama,
                        golonganumurlab_m.gol_umurlab_minimal,
                        golonganumurlab_m.gol_umurlab_maksimal,
                        nilairujukan_m.nilai_rujukan,
                        nilairujukan_m.nilai_min,
                        nilairujukan_m.nilai_max,
                        nilairujukan_m.satuan_hasillab AS satuanlab_nama,
                        nilairujukan_m.keterangan,
                        hasilpemeriksaanlabdetail_t.hasil,
                        hasilpemeriksaanlabdetail_t.petugaslab_id,
                        hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
                        petugaslab.nama_pegawai AS petugaslab_nama,
                        ambilsample_t.samplelab_id,
                        ambilsample_t.pasienmasukpenunjang_id,
                        hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                        nilairujukan_m.is_deleted,
                        hasilpemeriksaanlabdetail_t.metode,
                        jenispemeriksaanlab_m.jenispemeriksaanlab_id,
                        jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                        (COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut))::integer AS no_urut,
                        hasilpemeriksaanlabdetail_t.is_verifikasi,
                        hasilpemeriksaanlabdetail_t.tanggal_verifikasi,
                        hasilpemeriksaanlabdetail_t.petugas_verifikasi,
                        \'tindakan\'::text AS tipe
                       FROM ((((((((ambilsample_t
                         JOIN ( SELECT a.pemeriksaanlab_id,
                                a.jenispemeriksaanlab_id,
                                a.daftartindakan_id
                               FROM pemeriksaanlab_m a
                              WHERE ((a.is_deleted = false) AND (a.is_active = true))) pemeriksaanlab_m ON ((ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.daftartindakan_id)))
                         JOIN ( SELECT a.jenispemeriksaanlab_id,
                                a.jenispemeriksaanlab_nama
                               FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                         JOIN ( SELECT a.daftartindakan_id,
                                a.daftartindakan_nama
                               FROM daftartindakan_m a
                              WHERE ((a.is_deleted = false) AND (a.is_active = true))) daftartindakan_m ON ((pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN ( SELECT a.pemeriksaanlab_id,
                                a.golonganumur_id,
                                a.nilairujukan_id,
                                a.nama_rujukan,
                                a.jenis_kelamin,
                                a.nilai_rujukan,
                                a.nilai_min,
                                a.nilai_max,
                                a.satuan_hasillab,
                                a.keterangan,
                                a.is_deleted,
                                a.no_urut
                               FROM nilairujukan_m a
                              WHERE (a.is_active = true)) nilairujukan_m ON ((pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id)))
                         LEFT JOIN ( SELECT a.golonganumurlab_id,
                                a.gol_umurlab_nama,
                                a.gol_umurlab_minimal,
                                a.gol_umurlab_maksimal
                               FROM golonganumurlab_m a) golonganumurlab_m ON ((nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id)))
                         LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
                                a.hasilpemeriksaanlab_id,
                                a.samplelab_id
                               FROM hasilpemeriksaanlab_t a) hasilpemeriksaanlab_t ON (((ambilsample_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
                         LEFT JOIN ( SELECT a.pemeriksaanlab_id,
                                a.nilairujukan_id,
                                a.hasilpemeriksaanlab_id,
                                a.petugaslab_id,
                                a.samplelab_id,
                                a.hasil,
                                a.hasilpemeriksaanlabdetail_id,
                                a.metode,
                                a.no_urut,
                                a.is_verifikasi,
                                a.tanggal_verifikasi,
                                a.petugas_verifikasi
                               FROM hasilpemeriksaanlabdetail_t a
                              WHERE ((a.is_deleted IS FALSE) AND (a.is_active IS TRUE))) hasilpemeriksaanlabdetail_t ON (((pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id) AND (nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id) AND (hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id))))
                         LEFT JOIN ( SELECT a.pegawai_id,
                                a.nama_pegawai
                               FROM pegawai_m a) petugaslab ON ((hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id)))
                      WHERE ((ambilsample_t.is_deleted = false) AND (ambilsample_t.is_active = true))
                    UNION ALL
                     SELECT pemeriksaanlab_m.pemeriksaanlab_id,
                        paketpelayanan_mp.daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        pemeriksaanlab_m.tipepaket_id,
                        tipepaket_m.tipepaket_nama,
                        nilairujukan_m.nilairujukan_id,
                        COALESCE(nilairujukan_m.nama_rujukan, daftartindakan_m.daftartindakan_nama) AS nama_rujukan,
                        nilairujukan_m.jenis_kelamin,
                        fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
                        nilairujukan_m.golonganumur_id,
                        golonganumurlab_m.gol_umurlab_nama,
                        golonganumurlab_m.gol_umurlab_minimal,
                        golonganumurlab_m.gol_umurlab_maksimal,
                        nilairujukan_m.nilai_rujukan,
                        nilairujukan_m.nilai_min,
                        nilairujukan_m.nilai_max,
                        nilairujukan_m.satuan_hasillab AS satuanlab_nama,
                        nilairujukan_m.keterangan,
                        hasilpemeriksaanlabdetail_t.hasil,
                        hasilpemeriksaanlabdetail_t.petugaslab_id,
                        hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
                        petugaslab.nama_pegawai AS petugaslab_nama,
                        ambilsample_t.samplelab_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                        nilairujukan_m.is_deleted,
                        hasilpemeriksaanlabdetail_t.metode,
                        jenispemeriksaanlab_m.jenispemeriksaanlab_id,
                        jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                        (COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut))::integer AS no_urut,
                        hasilpemeriksaanlabdetail_t.is_verifikasi,
                        hasilpemeriksaanlabdetail_t.tanggal_verifikasi,
                        hasilpemeriksaanlabdetail_t.petugas_verifikasi,
                        \'paket\'::text AS tipe
                       FROM ((((((((((((ambilsample_t
                         JOIN pemeriksaanlab_m ON ((ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.tipepaket_id)))
                         JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                         JOIN pasienmasukpenunjang_t ON ((ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                         JOIN tipepaket_m ON ((pemeriksaanlab_m.tipepaket_id = tipepaket_m.tipepaket_id)))
                         JOIN paketpelayanan_mp ON ((pemeriksaanlab_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                         JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN nilairujukan_m ON ((pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id)))
                         LEFT JOIN golonganumurlab_m ON ((nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id)))
                         LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
                         LEFT JOIN hasilpemeriksaanlabdetail_t ON (((pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id) AND (nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id) AND (hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id) AND (hasilpemeriksaanlabdetail_t.is_deleted IS FALSE) AND (hasilpemeriksaanlabdetail_t.is_active IS TRUE))))
                         LEFT JOIN pegawai_m petugaslab ON ((hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id)))
                         LEFT JOIN samplelab_m ON ((hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id)))
                      WHERE ((ambilsample_t.is_deleted = false) AND (ambilsample_t.is_active = true) AND (pemeriksaanlab_m.is_deleted = false) AND (pemeriksaanlab_m.is_active = true) AND (daftartindakan_m.is_deleted = false) AND (daftartindakan_m.is_active = true) AND (nilairujukan_m.is_active = true))) x
              ORDER BY x.no_urut;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienradiologi_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienradiologi_v" AS  SELECT header.tipe_pasien,
                header.pendaftaran_id,
                header.pasienmasukpenunjang_id,
                header.pasienkirimkeunitlain_id,
                header.tglmasukpenunjang,
                header.no_pendaftaran,
                header.no_masukpenunjang,
                header.no_rekam_medik, 
                header.nama_pasien,
                detail.dokter_id_detail AS pegawai_id,
                detail.dokter_nama_detail AS dokter_penunjang,
                header.no_rujukan,
                header.asalrujukan_id,
                header.asalrujukan_nama,
                header.ruanganasal_id,
                header.ruangan_nama,
                header.status_periksa,
                header.no_antrian,
                header.carabayar_id,
                header.carabayar_nama,
                header.penjamin_id,
                header.penjamin_nama,
                header.kelaspelayanan_id,
                header.kelaspelayanan_nama,
                header.umur,
                header.jeniskelamin,
                header.j_kelamin,
                header.tanggal_lahir,
                header.kuning,
                header.merah,
                header.ungu,
                header.coklat,
                header.tgl_rujukan,
                header.pasien_id,
                header.pasienadmisi_id,
                header.ruangan_id,
                header.is_bayar,
                header.status_penunjang,
                header.catatan_dokterpengirim,
                header.no_telepon_pasien,
                header.dokter_perujuk_id,
                header.dokter_perujuk_nama,
                detail.dokter_nama_detail AS nama_dokter_penunjang,
                header.unit_asal,
                header.nama_diagnosa,
                false AS is_mcu,
                detail.jenispemeriksaanrad_nama,
                detail.tipepaket_nama,
                detail.detail_2,
                detail.daftartindakan_id,
                detail.daftartindakan_nama,
                    CASE
                        WHEN (hasil.is_hasil >= 1) THEN true
                        ELSE false
                    END AS is_hasil,
                detail.tindakanpelayanan_id,
                hasil.tgl_verifikasi,
                header.created_by,
                header.penjamin_kode,
                detail.cyto_tindakan,
                detail.qty_tindakan,
                header.no_identitas_pasien,
                detail.hasilpemeriksaanrad_id,
                detail.status_bayar,
                detail.status_periksa_penunjang,
                detail.status_batal,
                header.groupcarabayar_id,
                header.sepesial_pemeriksaan,
                header.jenis_kelamin_kode,
                    CASE
                        WHEN (hasil.tgl_verifikasi IS NOT NULL) THEN true
                        ELSE false
                    END AS is_selesai,
                detail.tindakanpelayananasal_id,
                detail.tgl_ambilfoto,
                detail.tgl_hasilrad
               FROM ((( SELECT \'ORDER\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pasienmasukpenunjang_t.tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasienmasukpenunjang_t.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                        pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                        instalasi_m.instalasi_nama AS asalrujukan_nama,
                        pasienmasukpenunjang_t.ruanganasal_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        penjamin_m.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pasienadmisi_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pasienadmisi_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        pasienadmisi_t.pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                        true AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM ((((((((((((((pasienmasukpenunjang_t
                         JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON (((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pendaftaran_t.is_aps = false))))
                         JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                         JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                         JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                      WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
                    UNION ALL
                     SELECT \'ORDER\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pasienmasukpenunjang_t.tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasienmasukpenunjang_t.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                        pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                        instalasi_m.instalasi_nama AS asalrujukan_nama,
                        pasienmasukpenunjang_t.ruanganasal_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        pendaftaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pendaftaran_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        pasienadmisi_t.pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                            CASE
                                WHEN (pendaftaran_t.instalasi_id = 2) THEN true
                                ELSE false
                            END AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM ((((((((((((((pasienmasukpenunjang_t
                         JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                         JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                         JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                      WHERE ((pasienkirimkeunitlain_t.instalasi_id = 5) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
                    UNION ALL
                     SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasienmasukpenunjang_t.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        rujukan_t.no_rujukan,
                        rujukan_t.asalrujukan_id,
                        asalrujukan_m.asalrujukan_nama,
                        rujukan_t.rujukandari_id AS ruanganasal_id,
                        perujuk_m.namaperujuk AS ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        pendaftaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pendaftaran_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        NULL::integer AS pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                        false AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM ((((((((((((((pasienmasukpenunjang_t
                         JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                         LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
                         JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                      WHERE (pendaftaran_t.instalasi_id = 5)
                    UNION ALL
                     SELECT \'APS\'::text AS tipe_pasien,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
                        pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
                        pendaftaran_t.no_pendaftaran,
                        pasienmasukpenunjang_t.no_masukpenunjang,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai AS dokter_penunjang,
                        NULL::character varying AS no_rujukan,
                        pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                        \'APS\'::character varying AS asalrujukan_nama,
                        pasienmasukpenunjang_t.ruanganasal_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.no_antrian,
                        pendaftaran_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pendaftaran_t.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pendaftaran_t.umur,
                        pasien_m.jeniskelamin,
                        fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                        pasien_m.tanggal_lahir,
                        ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
                        ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
                        ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
                        ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
                        pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
                        pasienmasukpenunjang_t.pasien_id,
                        NULL::integer AS pasienadmisi_id,
                        pasienmasukpenunjang_t.ruangan_id,
                        pasienmasukpenunjang_t.is_bayar,
                        pasienkirimkeunitlain_t.status_penunjang,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        pasien_m.no_telepon_pasien,
                        dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                        concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
                        concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                            CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                                WHEN 0 THEN \'Pendaftaran\'::text
                                ELSE \'Unit\'::text
                            END AS unit_asal,
                        diagnosa.diagnosa_utama AS nama_diagnosa,
                        pasienmasukpenunjang_t.created_by,
                        penjamin_m.penjamin_kode,
                        COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
                        carabayar_m.groupcarabayar_id,
                        false AS sepesial_pemeriksaan,
                        fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode
                       FROM (((((((((((((((pasienmasukpenunjang_t
                         JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                         LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                         JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                         JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                         LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                         JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                         JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                         JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
                         LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                         LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                         LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
                         LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                                    CASE
                                        WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                                        ELSE NULL::json
                                    END AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
                              WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                cppt_t.a_diag_utama AS diagnosa_utama
                               FROM ((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN ( SELECT cppt_t_1.cppt_id,
                                        cppt_t_1.pendaftaran_id,
                                        cppt_t_1.a_diag_utama,
                                        cppt_t_1.a_diag_penyerta
                                       FROM (cppt_t cppt_t_1
                                         JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                                                cppt_last.pendaftaran_id
                                               FROM cppt_t cppt_last
                                              WHERE (cppt_last.is_deleted = false)
                                              GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
                              WHERE (cppt_t.a_diag_utama IS NOT NULL)
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                resumemedisri_t.diag_utama AS diagnosa_utama
                               FROM (((pendaftaran_t pendaftaran_t_1
                                 JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
                                 JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
                                 JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
                              WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
                         LEFT JOIN ruangan_m ruangan_pendaftaran ON ((pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id)))
                      WHERE ((ruang_penunjang.instalasi_id = 5) AND (pendaftaran_t.is_aps = true))) header
                 LEFT JOIN ( SELECT \'NON_PAKET\'::text AS jenis,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.tgl_tindakan,
                        jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                        tindakanpelayanan_t.tipepaket_id,
                        \'\'::character varying AS tipepaket_nama,
                        NULL::text AS detail_2,
                        tindakanpelayanan_t.daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan,
                        tindakanpelayanan_t.qty_tindakan,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasien_id,
                        pegawai_m.pegawai_id AS dokter_id_detail,
                        pegawai_m.nama_pegawai AS dokter_nama_detail,
                        pasienmasukpenunjang_t.created_by,
                        hasilpemeriksaanrad.hasilpemeriksaanrad_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN false
                                ELSE true
                            END AS status_bayar,
                            CASE
                                WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NULL) THEN \'BELUM PERIKSA\'::text
                                WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL) THEN \'SELESAI\'::text
                                WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                                ELSE NULL::text
                            END AS status_periksa_penunjang,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN true
                                ELSE false
                            END AS status_batal,
                        tindakanpelayanan_t.tindakanpelayananasal_id,
                        hasilpemeriksaanrad.tgl_ambilfoto,
                        hasilpemeriksaanrad.tgl_hasilrad
                       FROM (((((((pasienmasukpenunjang_t
                         JOIN tindakanpelayanan_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))))
                         JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                         LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
                         LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE((permintaankepenunjang_t.dokter_id)::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                                hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                                hasilpemeriksaanrad_t.tindakanpelayanan_id,
                                hasilpemeriksaanrad_t.tgl_ambilfoto,
                                hasilpemeriksaanrad_t.tgl_hasilrad
                               FROM hasilpemeriksaanrad_t
                              WHERE (hasilpemeriksaanrad_t.is_deleted IS FALSE)) hasilpemeriksaanrad ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id))))
                      WHERE (daftartindakan_m.kelompoktindakan_id = 10)
                    UNION ALL
                     SELECT \'PAKET\'::text AS jenis,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.tgl_tindakan,
                        jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                        tindakanpelayanan_t.tipepaket_id,
                        tipepaket_m.tipepaket_nama,
                        NULL::text AS detail_2,
                        paketpelayanan_mp.daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan,
                        tindakanpelayanan_t.qty_tindakan,
                        COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
                        pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.pasien_id,
                        pegawai_m.pegawai_id AS dokter_id_detail,
                        pegawai_m.nama_pegawai AS dokter_nama_detail,
                        pasienmasukpenunjang_t.created_by,
                        hasilpemeriksaanrad.hasilpemeriksaanrad_id,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN false
                                ELSE true
                            END AS status_bayar,
                            CASE
                                WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NULL) THEN \'BELUM PERIKSA\'::text
                                WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL) THEN \'SELESAI\'::text
                                WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
                                ELSE NULL::text
                            END AS status_periksa_penunjang,
                            CASE
                                WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
                                WHEN (tindakanpelayanan_t.is_deleted = true) THEN true
                                ELSE false
                            END AS status_batal,
                        tindakanpelayanan_t.tindakanpelayananasal_id,
                        hasilpemeriksaanrad.tgl_ambilfoto,
                        hasilpemeriksaanrad.tgl_hasilrad
                       FROM (((((((((pasienmasukpenunjang_t
                         JOIN tindakanpelayanan_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayananasal_id IS NULL))))
                         JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                         JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                         LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
                         LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE((permintaankepenunjang_t.dokter_id)::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                                hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                                hasilpemeriksaanrad_t.tindakanpelayanan_id,
                                hasilpemeriksaanrad_t.tgl_ambilfoto,
                                hasilpemeriksaanrad_t.tgl_hasilrad
                               FROM hasilpemeriksaanrad_t
                              WHERE (hasilpemeriksaanrad_t.is_deleted IS FALSE)) hasilpemeriksaanrad ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id))))
                      WHERE (daftartindakan_m.kelompoktindakan_id = 10)) detail ON ((header.pasienmasukpenunjang_id = detail.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS is_hasil,
                        hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                        hasilpemeriksaanrad_t.tindakanpelayanan_id,
                        pemeriksaanrad_m.daftartindakan_id,
                        hasilpemeriksaanrad_t.tgl_verifikasi
                       FROM (hasilpemeriksaanrad_t
                         JOIN pemeriksaanrad_m ON ((pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id)))
                      WHERE (hasilpemeriksaanrad_t.is_deleted = false)
                      GROUP BY hasilpemeriksaanrad_t.pasienmasukpenunjang_id, hasilpemeriksaanrad_t.tindakanpelayanan_id, pemeriksaanrad_m.daftartindakan_id, hasilpemeriksaanrad_t.tgl_verifikasi) hasil ON (((header.pasienmasukpenunjang_id = hasil.pasienmasukpenunjang_id) AND (detail.tindakanpelayanan_id = hasil.tindakanpelayanan_id) AND (detail.daftartindakan_id = hasil.daftartindakan_id))));

        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienraddetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienraddetail_v" AS  SELECT \'NON_PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan, 
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                tindakanpelayanan_t.tipepaket_id,
                \'\'::character varying AS tipepaket_nama,
                NULL::text AS detail_2,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                tindakanpelayanan_t.qty_tindakan,
                pasienmasukpenunjang_t.status_periksa,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasien_id,
                tindakanpelayanan_t.tindakanpelayananasal_id
               FROM (((((pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
                 LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
              WHERE (daftartindakan_m.kelompoktindakan_id = 10)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                tindakanpelayanan_t.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                NULL::text AS detail_2,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                tindakanpelayanan_t.qty_tindakan,
                pasienmasukpenunjang_t.status_periksa,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasien_id,
                tindakanpelayanan_t.tindakanpelayananasal_id
               FROM (((((((pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
                 LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
              WHERE (daftartindakan_m.kelompoktindakan_id = 10);
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."hasilpemeriksaanrad_v";
        ');

        $this->execute('
            CREATE VIEW "public"."hasilpemeriksaanrad_v" AS  SELECT \'NON_PAKET\'::text AS jenis, 
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tipepaket_id,
                \'\'::character varying AS tipepaket_nama,
                NULL::text AS detail_2,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                pemeriksaanrad_m.pemeriksaanradiologi_id,
                pemeriksaanrad_m.pemeriksaanrad_nama,
                kelompokpemeriksaanrad_m.nama_kelompok,
                tindakanpelayanan_t.cyto_tindakan,
                hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                hasilpemeriksaanrad_t.no_hasilrad,
                hasilpemeriksaanrad_t.tgl_ambilfoto,
                hasilpemeriksaanrad_t.tgl_uploadhasil,
                hasilpemeriksaanrad_t.tgl_hasilrad,
                hasilpemeriksaanrad_t.kesan,
                hasilpemeriksaanrad_t.kesimpulan,
                hasilpemeriksaanrad_t.penanggungjawab_id,
                penanggungjawab.nama_pegawai AS penanggung_jawab,
                hasilpemeriksaanrad_t.hasil_expertise,
                hasilpemeriksaanrad_t.expertise_id,
                hasilpemeriksaanrad_t.is_hasilkritis,
                pasienmasukpenunjang_t.status_periksa,
                hasilpemeriksaanrad_t.tgl_verifikasi,
                hasilpemeriksaanrad_t.status_pemeriksaan,
                fgetnamalookup(hasilpemeriksaanrad_t.status_pemeriksaan) AS status_pemeriksaan_nama,
                COALESCE(hasilpemeriksaanrad_t.is_deleted, false) AS is_deleted,
                tindakanpelayanan_t.is_deleted AS delete_tindakan,
                COALESCE(hasilpemeriksaanrad_t.is_active, true) AS is_active,
                pasienmasukpenunjang_t.created_date
               FROM (((((((pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanrad_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                 LEFT JOIN hasilpemeriksaanrad_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id))))
                 LEFT JOIN pegawai_m penanggungjawab ON ((hasilpemeriksaanrad_t.penanggungjawab_id = penanggungjawab.pegawai_id)))
                 LEFT JOIN kelompokpemeriksaanrad_m ON ((pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id)))
              WHERE ((daftartindakan_m.kelompoktindakan_id = 10) AND (tindakanpelayanan_t.is_deleted IS FALSE))
            UNION ALL
             SELECT \'PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                NULL::text AS detail_2,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                pemeriksaanrad_m.pemeriksaanradiologi_id,
                pemeriksaanrad_m.pemeriksaanrad_nama,
                kelompokpemeriksaanrad_m.nama_kelompok,
                tindakanpelayanan_t.cyto_tindakan,
                hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                hasilpemeriksaanrad_t.no_hasilrad,
                hasilpemeriksaanrad_t.tgl_ambilfoto,
                hasilpemeriksaanrad_t.tgl_uploadhasil,
                hasilpemeriksaanrad_t.tgl_hasilrad,
                hasilpemeriksaanrad_t.kesan,
                hasilpemeriksaanrad_t.kesimpulan,
                hasilpemeriksaanrad_t.penanggungjawab_id,
                penanggungjawab.nama_pegawai AS penanggung_jawab,
                hasilpemeriksaanrad_t.hasil_expertise,
                hasilpemeriksaanrad_t.expertise_id,
                hasilpemeriksaanrad_t.is_hasilkritis,
                pasienmasukpenunjang_t.status_periksa,
                hasilpemeriksaanrad_t.tgl_verifikasi,
                hasilpemeriksaanrad_t.status_pemeriksaan,
                fgetnamalookup(hasilpemeriksaanrad_t.status_pemeriksaan) AS status_pemeriksaan_nama,
                COALESCE(hasilpemeriksaanrad_t.is_deleted, false) AS is_deleted,
                tindakanpelayanan_t.is_deleted AS delete_tindakan,
                COALESCE(hasilpemeriksaanrad_t.is_active, true) AS is_active,
                pasienmasukpenunjang_t.created_date
               FROM (((((((((pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN pemeriksaanrad_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                 LEFT JOIN hasilpemeriksaanrad_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id) AND (pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id))))
                 LEFT JOIN pegawai_m penanggungjawab ON ((hasilpemeriksaanrad_t.penanggungjawab_id = penanggungjawab.pegawai_id)))
                 LEFT JOIN kelompokpemeriksaanrad_m ON ((pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id)))
              WHERE ((daftartindakan_m.kelompoktindakan_id = 10) AND (tindakanpelayanan_t.is_deleted IS FALSE));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."invoicesudahbayardetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."invoicesudahbayardetail_v" AS  SELECT tagihan.pendaftaran_id,
                tagihan.pelayanan_id,
                tagihan.pasien_id,
                pasien_m.no_rekam_medik,
                    CASE
                        WHEN (pasien_m.nama_pasien IS NULL) THEN (tagihan.nama_pembeli)::character varying
                        ELSE pasien_m.nama_pasien
                    END AS nama_pasien,
                pasien_m.tanggal_lahir,
                tagihan.umur,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
                tagihan.tgl_pendaftaran,
                tagihan.no_pendaftaran,
                tagihan.tindakan_obat_id,
                tagihan.tindakan_obat_nama,
                tagihan.is_obat,
                tagihan.tarif_satuan,
                tagihan.qty,
                tagihan.sub_total,
                tagihan.ruangan_id,
                ruangan_m.ruangan_nama AS ruangan_pelayanan,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama AS instalasi_pelayanan,
                tagihan.tgl_pelayanan,
                tagihan.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tagihan.carabayar_tinpelayanan_id,
                carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
                tagihan.penjamin_tinpelayanan_id,
                penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
                tagihan.kelompoktindakan_id,
                tagihan.kelompoktindakan_nama,
                tagihan.jeniskasuspenyakit_id,
                tagihan.pembayaranpelayanan_id,
                tagihan.biaya_administrasi,
                tagihan.e_collection,
                tagihan.nama_pemrekening,
                tagihan.no_rekening,
                tagihan.carabayar_pelayanan_id,
                tagihan.carabayar_pelayanan,
                tagihan.penjamin_pelayanan_id,
                tagihan.penjamin_pelayanan,
                tagihan.tarif_cyto,
                tagihan.tandabuktibayar_id,
                tagihan.jeniskasuspenyakit_nama,
                tagihan.penjualanresep_id,
                tagihan.is_konsultasi,
                dok_tindakan.nama_pegawai AS dokter_tindakan,
                tagihan.pembayaran_id,
                tagihan.satuan_kecil AS uom,
                tagihan.tarif_dijamin,
                tagihan.tarif_dibayarkan,
                tagihan.groupinacbg_nama,
                tagihan.tarif_diskon,
                tagihan.is_visite,
                tagihan.tarifpenyulit_tindakan,
                tagihan.jenis_racikan,
                tagihan.tindakan_obat_kode,
                tagihan.is_akomodasi
               FROM (((((((( SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
                        daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        daftartindakan_m.kelompoktindakan_id,
                            CASE
                                WHEN (daftartindakan_m.is_konsultasi = true) THEN kelompoktindakan_m.kelompoktindakan_nama
                                ELSE kelompoktindakan_m.kelompoktindakan_nama
                            END AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        daftartindakan_m.is_konsultasi,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        NULL::text AS satuan_kecil,
                        tindakanpelayanan_t.tarif_dijamin,
                        tindakanpelayanan_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        tindakanpelayanan_t.tarif_diskon,
                            CASE
                                WHEN (daftartindakan_m.daftartindakan_id = 99993) THEN true
                                ELSE false
                            END AS is_visite,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        NULL::text AS jenis_racikan,
                        daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
                        daftartindakan_m.is_akomodasi
                       FROM ((((((((((pendaftaran_t
                         LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                         JOIN tindakanpelayanan_t ON (((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.parent_id IS NULL))))
                         JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                         JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                         JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
                         JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
                         JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
                         LEFT JOIN groupinacbg_m ON ((daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id)))
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
                        tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                            CASE
                                WHEN (tipepaket_m.is_mcu IS TRUE) THEN \'kelompok_paket_mcu\'::text
                                ELSE \'kelompok_paket\'::text
                            END AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        NULL::text AS satuan_kecil,
                        tindakanpelayanan_t.tarif_dijamin,
                        tindakanpelayanan_t.tarif_dibayarkan,
                        NULL::character varying AS groupinacbg_nama,
                        tindakanpelayanan_t.tarif_diskon,
                        false AS is_visite,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        NULL::text AS jenis_racikan,
                        tipepaket_m.tipepaket_kode AS tindakan_obat_kode,
                        false AS is_akomodasi
                       FROM ((((((((pendaftaran_t
                         LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                         JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                         JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
                         JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
                         JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
                      WHERE (tindakanpelayanan_t.is_deleted = false)
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                            CASE
                                WHEN (obatalkespasien_t.det = (0)::double precision) THEN obatalkespasien_t.qty_oa
                                WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
                                ELSE obatalkespasien_t.det
                            END AS qty,
                        obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'Drugs & Consumables\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        NULL::bigint AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        satuan_kecil.satuanunit_nama AS satuan_kecil,
                        obatalkespasien_t.tarif_dijamin,
                        obatalkespasien_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        obatalkespasien_t.tarif_diskon,
                        false AS is_visite,
                        0 AS tarifpenyulit_tindakan,
                            CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                                WHEN 0 THEN \'Non Racikan\'::text
                                ELSE \'Racikan\'::text
                            END AS jenis_racikan,
                        obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
                        false AS is_akomodasi
                       FROM ((((((((((pendaftaran_t
                         LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                         JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
                         JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                         JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
                         JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
                         JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
                         JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
                         LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
                         LEFT JOIN groupinacbg_m ON ((obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id)))
                      WHERE (obatalkespasien_t.is_deleted = false)
                    UNION ALL
                     SELECT penjualanresep_t.pendaftaran_id,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        penjualanresep_t.pasien_id,
                        penjualanresep_t.tglresep AS tgl_pendaftaran,
                        penjualanresep_t.noresep AS no_pendaftaran,
                        NULL::character varying AS umur,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                        obatalkespasien_t.qty_oa AS qty,
                        obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'kelompok_obat\'::character varying AS kelompoktindakan_nama,
                        NULL::integer AS jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        NULL::character varying AS jeniskasuspenyakit_nama,
                        obatalkespasien_t.penjualanresep_id,
                        penjualanresep_t.nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        NULL::bigint AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        satuan_kecil.satuanunit_nama AS satuan_kecil,
                        obatalkespasien_t.tarif_dijamin,
                        obatalkespasien_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        obatalkespasien_t.tarif_diskon,
                        false AS is_visite,
                        0 AS tarifpenyulit_tindakan, 
                            CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                                WHEN 0 THEN \'Non Racikan\'::text
                                ELSE \'Racikan\'::text
                            END AS jenis_racikan,
                        obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
                        false AS is_akomodasi
                       FROM ((((((((obatalkespasien_t
                         JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                         JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                         JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
                         JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
                         JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
                         JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
                         LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
                         LEFT JOIN groupinacbg_m ON ((obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id)))
                      WHERE ((penjualanresep_t.jenispenjualan)::integer <> 344)) tagihan
                 LEFT JOIN ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN carabayar_m ON ((tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m dok_tindakan ON ((tagihan.doktertindakan_id = dok_tindakan.pegawai_id)));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infotagihanpasien_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotagihanpasien_v" AS  SELECT tagihan.ref_pendaftaran_id,
                tagihan.pendaftaran_id,
                tagihan.no_pendaftaran,
                tagihan.tgl_pendaftaran,
                tagihan.tgl_pelayanan,
                tagihan.kelompoktindakan_id,
                tagihan.kelompoktindakan_nama,
                tagihan.pelayanan_id,
                tagihan.tindakan_obat_id,
                tagihan.tindakan_obat_nama,
                tagihan.is_obat,
                tagihan.tarif_satuan,
                tagihan.qty,
                tagihan.tarif_cyto,
                tagihan.sub_total,
                tagihan.ruangan_id,
                ruangan_m.ruangan_nama AS ruangan_pelayanan,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama AS instalasi_pelayanan,
                tagihan.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tagihan.carabayar_pelayanan_id,
                carabayar_m.carabayar_nama AS carabayar_pelayanan,
                tagihan.penjamin_pelayanan_id,
                penjamin_m.penjamin_nama AS penjamin_pelayanan,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                tagihan.dokterpenanggungjawab_id,
                dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
                pasien_m.pasien_id,
                pasien_m.no_mobile_pasien,
                pasien_m.alamatemail,
                tagihan.penjamin_pendaftaran_id,
                tagihan.pasienmasukpenunjang_id,
                tagihan.is_deleted,
                carabayar_m.groupcarabayar_id,
                tagihan.penjualanresep_id,
                tagihan.is_valid,
                tagihan.is_cyto,
                tagihan.pasienadmisi_id,
                tagihan.implementasi_id,
                tagihan.jeniskasuspenyakit_id,
                tagihan.discount,
                tagihan.tipepaket_id,
                tagihan.kamarruangan_id,
                tagihan.is_akomodasi,
                tagihan.kamartempattidur_id,
                tagihan.additional_data,
                tagihan.is_konsultasi,
                tagihan.tarifpenyulit_tindakan,
                tagihan.is_overwrite,
                tagihan.harga_origin,
                tagihan.cyto_origin,
                tagihan.penyulit_origin,
                lob.lob_id,
                asuransipasien_m.penjamingrade_id,
                NULL::text AS is_ditagihkan,
                    CASE
                        WHEN ((tagihan.pasienadmisi_id IS NULL) AND (tagihan.instalasi_id <> 2)) THEN \'RAJAL\'::text
                        WHEN ((tagihan.pasienadmisi_id IS NULL) AND (tagihan.instalasi_id = 2)) THEN \'IGD\'::text
                        ELSE \'RANAP\'::text
                    END AS pelayanan
               FROM (((((((((( SELECT pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        tindakanpelayanan_t.tindakansudahbayar_id,
                        tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
                        daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        daftartindakan_m.kelompoktindakan_id,
                        kelompoktindakan_m.kelompoktindakan_nama,
                        pendaftaran_t.pasien_id,
                        tindakanpelayanan_t.dokterpenanggungjawab_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.is_deleted,
                        0 AS penjualanresep_id,
                        tindakanpelayanan_t.is_valid,
                        tindakanpelayanan_t.cyto_tindakan AS is_cyto,
                        tindakanpelayanan_t.pasienadmisi_id,
                        tindakanpelayanan_t.implementasi_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        tindakanpelayanan_t.tarif_diskon AS discount,
                        tindakanpelayanan_t.tipepaket_id,
                        tindakanpelayanan_t.kamarruangan_id,
                        daftartindakan_m.is_akomodasi,
                        tindakanpelayanan_t.kamartempattidur_id,
                        tindakanpelayanan_t.additional_data,
                        daftartindakan_m.is_konsultasi,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        tindakanpelayanan_t.is_overwrite,
                        tindakanpelayanan_t.harga_origin,
                        tindakanpelayanan_t.cyto_origin,
                        tindakanpelayanan_t.penyulit_origin, 
                        pendaftaran_t.asuransipasien_id,
                        pendaftaran_t.instalasi_id
                       FROM (((tindakanpelayanan_t
                         JOIN ( SELECT b.pendaftaran_id,
                                b.no_pendaftaran,
                                b.tgl_pendaftaran,
                                b.pasien_id,
                                b.penjamin_id,
                                b.jeniskasuspenyakit_id,
                                b.asuransipasien_id,
                                b.instalasi_id
                               FROM pendaftaran_t b) pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN ( SELECT b.daftartindakan_id,
                                b.kelompoktindakan_id,
                                b.daftartindakan_nama,
                                b.is_akomodasi,
                                b.is_konsultasi
                               FROM daftartindakan_m b) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         JOIN ( SELECT b.kelompoktindakan_id,
                                b.kelompoktindakan_nama
                               FROM kelompoktindakan_m b) kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.parent_id IS NULL))
                    UNION ALL
                     SELECT gabungtagihan.ref_pendaftaran_id,
                        tindakanpelayanan_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        tindakanpelayanan_t.tindakansudahbayar_id,
                        tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
                        daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        daftartindakan_m.kelompoktindakan_id,
                        kelompoktindakan_m.kelompoktindakan_nama,
                        pendaftaran_t.pasien_id,
                        tindakanpelayanan_t.dokterpenanggungjawab_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.is_deleted,
                        0 AS penjualanresep_id,
                        tindakanpelayanan_t.is_valid,
                        tindakanpelayanan_t.cyto_tindakan AS is_cyto,
                        tindakanpelayanan_t.pasienadmisi_id,
                        tindakanpelayanan_t.implementasi_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        tindakanpelayanan_t.tarif_diskon AS discount,
                        tindakanpelayanan_t.tipepaket_id,
                        tindakanpelayanan_t.kamarruangan_id,
                        daftartindakan_m.is_akomodasi,
                        tindakanpelayanan_t.kamartempattidur_id,
                        tindakanpelayanan_t.additional_data,
                        daftartindakan_m.is_konsultasi,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        tindakanpelayanan_t.is_overwrite,
                        tindakanpelayanan_t.harga_origin,
                        tindakanpelayanan_t.cyto_origin,
                        tindakanpelayanan_t.penyulit_origin,
                        pendaftaran_t.asuransipasien_id,
                        pendaftaran_t.instalasi_id
                       FROM ((((tindakanpelayanan_t
                         JOIN ( SELECT b.pendaftaran_id,
                                b.no_pendaftaran,
                                b.tgl_pendaftaran,
                                b.pasien_id,
                                b.penjamin_id,
                                b.jeniskasuspenyakit_id,
                                b.asuransipasien_id,
                                b.instalasi_id
                               FROM pendaftaran_t b) pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN ( SELECT b.daftartindakan_id,
                                b.kelompoktindakan_id,
                                b.daftartindakan_nama,
                                b.is_akomodasi,
                                b.is_konsultasi
                               FROM daftartindakan_m b) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         JOIN ( SELECT b.kelompoktindakan_id,
                                b.kelompoktindakan_nama
                               FROM kelompoktindakan_m b) kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                         JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                                gabungpelayanandetail_t.ref_pendaftaran_id
                               FROM gabungpelayanandetail_t
                              WHERE (gabungpelayanandetail_t.is_deleted IS FALSE)) gabungtagihan ON ((tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.parent_id IS NULL))
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        tindakanpelayanan_t.tindakansudahbayar_id,
                        tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
                        tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                            CASE
                                WHEN (pendaftaran_t.instalasi_id = 21) THEN 17
                                ELSE NULL::integer
                            END AS kelompoktindakan_id,
                        \'Others\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.pasien_id,
                        tindakanpelayanan_t.dokterpenanggungjawab_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.is_deleted,
                        0 AS penjualanresep_id,
                        tindakanpelayanan_t.is_valid,
                        tindakanpelayanan_t.cyto_tindakan AS is_cyto,
                        tindakanpelayanan_t.pasienadmisi_id,
                        tindakanpelayanan_t.implementasi_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        tindakanpelayanan_t.tarif_diskon AS discount,
                        tindakanpelayanan_t.tipepaket_id,
                        tindakanpelayanan_t.kamarruangan_id,
                        NULL::boolean AS is_akomodasi,
                        tindakanpelayanan_t.kamartempattidur_id,
                        tindakanpelayanan_t.additional_data,
                        NULL::boolean AS is_konsultasi,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        tindakanpelayanan_t.is_overwrite,
                        tindakanpelayanan_t.harga_origin,
                        tindakanpelayanan_t.cyto_origin,
                        tindakanpelayanan_t.penyulit_origin,
                        pendaftaran_t.asuransipasien_id,
                        pendaftaran_t.instalasi_id
                       FROM ((tindakanpelayanan_t
                         JOIN ( SELECT c.pendaftaran_id,
                                c.no_pendaftaran,
                                c.tgl_pendaftaran,
                                c.pasien_id,
                                c.penjamin_id,
                                c.jeniskasuspenyakit_id,
                                c.asuransipasien_id,
                                c.instalasi_id
                               FROM pendaftaran_t c) pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN ( SELECT c.tipepaket_id,
                                c.tipepaket_nama
                               FROM tipepaket_m c) tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.parent_id IS NULL))
                    UNION ALL
                     SELECT gabungtagihan.ref_pendaftaran_id,
                        tindakanpelayanan_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        tindakanpelayanan_t.tindakansudahbayar_id,
                        tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
                        tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                            CASE
                                WHEN (pendaftaran_t.instalasi_id = 21) THEN 17
                                ELSE NULL::integer
                            END AS kelompoktindakan_id,
                        \'Others\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.pasien_id,
                        tindakanpelayanan_t.dokterpenanggungjawab_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.is_deleted,
                        0 AS penjualanresep_id,
                        tindakanpelayanan_t.is_valid,
                        tindakanpelayanan_t.cyto_tindakan AS is_cyto,
                        tindakanpelayanan_t.pasienadmisi_id,
                        tindakanpelayanan_t.implementasi_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        tindakanpelayanan_t.tarif_diskon AS discount,
                        tindakanpelayanan_t.tipepaket_id,
                        tindakanpelayanan_t.kamarruangan_id,
                        NULL::boolean AS is_akomodasi,
                        tindakanpelayanan_t.kamartempattidur_id,
                        tindakanpelayanan_t.additional_data,
                        NULL::boolean AS is_konsultasi,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        tindakanpelayanan_t.is_overwrite,
                        tindakanpelayanan_t.harga_origin,
                        tindakanpelayanan_t.cyto_origin,
                        tindakanpelayanan_t.penyulit_origin,
                        pendaftaran_t.asuransipasien_id,
                        pendaftaran_t.instalasi_id
                       FROM (((tindakanpelayanan_t
                         JOIN ( SELECT c.pendaftaran_id,
                                c.no_pendaftaran,
                                c.tgl_pendaftaran,
                                c.pasien_id,
                                c.penjamin_id,
                                c.jeniskasuspenyakit_id,
                                c.asuransipasien_id,
                                c.instalasi_id
                               FROM pendaftaran_t c) pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN ( SELECT c.tipepaket_id,
                                c.tipepaket_nama
                               FROM tipepaket_m c) tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                                gabungpelayanandetail_t.ref_pendaftaran_id
                               FROM gabungpelayanandetail_t
                              WHERE (gabungpelayanandetail_t.is_deleted IS FALSE)) gabungtagihan ON ((tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id)))
                      WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.parent_id IS NULL))
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        obatalkespasien_t.obatsudahbayar_id,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa,
                            CASE
                                WHEN (obatalkespasien_t.det = (0)::double precision) THEN obatalkespasien_t.qty_oa
                                WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
                                ELSE obatalkespasien_t.det
                            END AS qty,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'Medicine\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.pasien_id,
                        obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        obatalkespasien_t.pasienmasukpenunjang_id,
                        obatalkespasien_t.is_deleted,
                        obatalkespasien_t.penjualanresep_id,
                        NULL::boolean AS is_valid,
                        NULL::boolean AS is_cyto,
                        obatalkespasien_t.pasienadmisi_id,
                        NULL::integer AS implementasi_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        obatalkespasien_t.tarif_diskon AS discount,
                        NULL::integer AS tipepaket_id,
                        NULL::integer AS kamarruangan_id,
                        NULL::boolean AS is_akomodasi,
                        NULL::integer AS kamartempattidur_id,
                        NULL::text AS additional_data,
                        NULL::boolean AS is_konsultasi,
                        0 AS tarifpenyulit_tindakan,
                        obatalkespasien_t.is_overwrite,
                        obatalkespasien_t.harga_origin,
                        0 AS cyto_origin,
                        0 AS penyulit_origin,
                        pendaftaran_t.asuransipasien_id,
                        pendaftaran_t.instalasi_id
                       FROM ((obatalkespasien_t
                         JOIN ( SELECT d.pendaftaran_id,
                                d.no_pendaftaran,
                                d.tgl_pendaftaran,
                                d.pasien_id,
                                d.penjamin_id,
                                d.jeniskasuspenyakit_id,
                                d.asuransipasien_id,
                                d.instalasi_id
                               FROM pendaftaran_t d) pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN ( SELECT d.obatalkes_id,
                                d.obatalkes_nama
                               FROM obatalkes_m d) obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                      WHERE (obatalkespasien_t.is_deleted = false)
                    UNION ALL
                     SELECT gabungtagihan.ref_pendaftaran_id,
                        obatalkespasien_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        obatalkespasien_t.obatsudahbayar_id,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa,
                            CASE
                                WHEN (obatalkespasien_t.det = (0)::double precision) THEN obatalkespasien_t.qty_oa
                                WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
                                ELSE obatalkespasien_t.det
                            END AS qty,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'Medicine\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.pasien_id,
                        obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
                        pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
                        obatalkespasien_t.pasienmasukpenunjang_id,
                        obatalkespasien_t.is_deleted,
                        obatalkespasien_t.penjualanresep_id,
                        NULL::boolean AS is_valid,
                        NULL::boolean AS is_cyto,
                        obatalkespasien_t.pasienadmisi_id,
                        NULL::integer AS implementasi_id,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        obatalkespasien_t.tarif_diskon AS discount,
                        NULL::integer AS tipepaket_id,
                        NULL::integer AS kamarruangan_id,
                        NULL::boolean AS is_akomodasi,
                        NULL::integer AS kamartempattidur_id,
                        NULL::text AS additional_data,
                        NULL::boolean AS is_konsultasi,
                        0 AS tarifpenyulit_tindakan,
                        obatalkespasien_t.is_overwrite,
                        obatalkespasien_t.harga_origin,
                        0 AS cyto_origin,
                        0 AS penyulit_origin,
                        pendaftaran_t.asuransipasien_id,
                        pendaftaran_t.instalasi_id
                       FROM (((obatalkespasien_t
                         JOIN ( SELECT d.pendaftaran_id,
                                d.no_pendaftaran,
                                d.tgl_pendaftaran,
                                d.pasien_id,
                                d.penjamin_id,
                                d.jeniskasuspenyakit_id,
                                d.asuransipasien_id,
                                d.instalasi_id
                               FROM pendaftaran_t d) pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                         JOIN ( SELECT d.obatalkes_id,
                                d.obatalkes_nama
                               FROM obatalkes_m d) obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                         JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                                gabungpelayanandetail_t.ref_pendaftaran_id
                               FROM gabungpelayanandetail_t
                              WHERE (gabungpelayanandetail_t.is_deleted IS FALSE)) gabungtagihan ON ((obatalkespasien_t.pendaftaran_id = gabungtagihan.pendaftaran_id)))
                      WHERE (obatalkespasien_t.is_deleted = false)) tagihan
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama,
                        a.lob_id
                       FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.lob_id
                       FROM instalasi_m a) lob ON ((tagihan.instalasi_id = lob.instalasi_id)))
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama,
                        a.carabayar_id
                       FROM penjamin_m a) penjamin_m ON ((tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id)))
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama,
                        a.groupcarabayar_id
                       FROM carabayar_m a) carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.no_mobile_pasien,
                        a.alamatemail
                       FROM pasien_m a) pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter_dpjp ON ((tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id)))
                 LEFT JOIN ( SELECT a.asuransipasien_id,
                        a.penjamingrade_id
                       FROM asuransipasien_m a) asuransipasien_m ON ((tagihan.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
              WHERE (tagihan.tindakansudahbayar_id IS NULL)
              ORDER BY tagihan.tgl_pelayanan DESC;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienkarcis_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienkarcis_v" AS  SELECT \'non_paket\'::text AS tipe,
                tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.pasien_id,
                pendaftaran_t.instalasi_id, 
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.tgl_pendaftaran,
                instalasi_m.instalasi_nama,
                pendaftaran_t.no_pendaftaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                ruangan_m.ruangan_nama,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                sum((tindakanpelayanan_t.tarif_tindakan)::integer) AS tarif_tindakan
               FROM ((((((((tindakanpelayanan_t
                 JOIN pendaftaran_t ON (((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND ((pendaftaran_t.status_periksa)::text <> \'4\'::text) AND (pendaftaran_t.instalasi_id <> 21))))
                 JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
              WHERE ((daftartindakan_m.kelompoktindakan_id = 17) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true) AND (tindakanpelayanan_t.parent_id IS NULL))
              GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.pasien_id, pendaftaran_t.instalasi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, pendaftaran_t.tgl_pendaftaran, instalasi_m.instalasi_nama, pendaftaran_t.no_pendaftaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien
            UNION ALL
             SELECT \'paket\'::text AS tipe,
                tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.pasien_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.tgl_pendaftaran,
                instalasi_m.instalasi_nama,
                pendaftaran_t.no_pendaftaran,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                ruangan_m.ruangan_nama,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                sum((tindakanpelayanan_t.tarif_tindakan)::integer) AS tarif_tindakan
               FROM ((((((tindakanpelayanan_t
                 JOIN pendaftaran_t ON (((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pendaftaran_t.instalasi_id = 21) AND ((pendaftaran_t.status_periksa)::text <> \'4\'::text))))
                 JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
              WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true) AND (tindakanpelayanan_t.parent_id IS NULL))
              GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.pasien_id, pendaftaran_t.instalasi_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id, pendaftaran_t.tgl_pendaftaran, instalasi_m.instalasi_nama, pendaftaran_t.no_pendaftaran, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienkarcisdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienkarcisdetail_v" AS  SELECT tindakanpelayanan_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran, 
                pendaftaran_t.no_pendaftaran,
                tindakanpelayanan_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                tindakanpelayanan_t.instalasi_id,
                instalasi_m.instalasi_nama,
                tindakanpelayanan_t.carabayar_id,
                carabayar_m.carabayar_nama,
                tindakanpelayanan_t.penjamin_id,
                penjamin_m.penjamin_nama,
                tindakanpelayanan_t.ruangan_id,
                ruangan_m.ruangan_nama,
                tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.tgl_tindakan,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.qty_tindakan,
                (tindakanpelayanan_t.tarif_tindakan)::integer AS tarif_tindakan
               FROM ((((((((tindakanpelayanan_t
                 JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
              WHERE ((daftartindakan_m.kelompoktindakan_id = ANY (ARRAY[17, 19])) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true) AND (tindakanpelayanan_t.parent_id IS NULL))
            UNION ALL
             SELECT tindakanpelayanan_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                tindakanpelayanan_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                tindakanpelayanan_t.instalasi_id,
                instalasi_m.instalasi_nama,
                tindakanpelayanan_t.carabayar_id,
                carabayar_m.carabayar_nama,
                tindakanpelayanan_t.penjamin_id,
                penjamin_m.penjamin_nama,
                tindakanpelayanan_t.ruangan_id,
                ruangan_m.ruangan_nama,
                tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.tgl_tindakan,
                tindakanpelayanan_t.tipepaket_id AS daftartindakan_id,
                tipepaket_m.tipepaket_nama AS daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.qty_tindakan,
                (tindakanpelayanan_t.tarif_tindakan)::integer AS tarif_tindakan
               FROM (((((((tindakanpelayanan_t
                 JOIN pendaftaran_t ON (((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pendaftaran_t.instalasi_id = 21))))
                 JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
              WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true));
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."proses_pembayaranpelayanan"()
                RETURNS "pg_catalog"."trigger" AS $BODY$

                DECLARE
                    paramJson VARCHAR;
                    vTPembayaran FLOAT;
                    vAdmin FLOAT;
                    vNamaBkm VARCHAR;
                    vShifId INTEGER; 
                    vKeterangan VARCHAR;
                    vCaraPembayaran VARCHAR;
                    vTandaBuktiBayarId INTEGER;
                    vPegawaiId INTEGER;
                    vDataObat VARCHAR;
                    vDataTindakan VARCHAR;
                    vIdPendaftaran INTEGER;
                    vJumlahUangMuka INTEGER;
                    vUangDiterima FLOAT;
                    vIdPemakaianUang INTEGER;
                    vUangKembalian FLOAT;
                    vNumber VARCHAR;
                    vPembayaran_id INTEGER;

                    
                BEGIN
                  -- NEW.total_bayartindakan := NEW.total_biayapelayanan - (NEW.total_subsidirs + NEW.total_subsidiasuransi + NEW.total_subsidipemerintah);
                  NEW.total_iurbiaya := NEW.total_biayapelayanan - (NEW.total_subsidirs + NEW.total_subsidiasuransi + NEW.total_subsidipemerintah);
                  NEW.total_terbayar := NEW.total_iurbiaya;
                  NEW.total_sisatagihan = NEW.total_iurbiaya - NEW.total_bayartindakan;
                  vUangKembalian := NEW.total_bayartindakan - NEW.total_iurbiaya;
                  IF (NEW.total_bayartindakan > NEW.total_iurbiaya) THEN
                      NEW.total_sisatagihan = 0;
                  END IF;

                  NEW.statusbayar = 349;
                  NEW.is_lunas = FALSE;
                    
                  IF (NEW.total_iurbiaya > NEW.total_bayartindakan) THEN
                      NEW.total_terbayar := NEW.total_bayartindakan;
                            IF (vCaraPembayaran = \'31\') THEN
                                    NEW.is_lunas = FALSE;
                     END IF;
                END IF;

                  vIdPendaftaran := NEW.pendaftaran_id;
                  paramJson := NEW.additional_data;  
                    vDataObat := paramJson::json->>\'obat\';
                  vDataTindakan := paramJson::json->>\'tindakan\';

                  -- Insert ke  pembayaranpelayanan_t
                  IF (json_array_length(vDataTindakan::json) > 0)  THEN
                            INSERT INTO tindakansudahbayar_t (
                                            pembayaranpelayanan_id,
                                                                        pembayaran_id, 
                                            tindakanpelayanan_id, 
                                            daftartindakan_id, 
                                            ruangan_id,
                                            qty_tindakan,
                                            jmlbiaya_tindakan,
                                            jmlsubsidi_asuransi,
                                            jmlsubsidi_pemerintah,
                                            jmlsubsidi_rs,
                                            jmliur_biaya,
                                            jml_pembebasan,
                                            jmlbayar_tindakan,
                                            jml_sisabayar_tindakan,
                                            tipepaket_id,
                                            pembulatan,
                                            additional_data,
                                            created_by
                         ) SELECT 
                                            NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                                            NEW.pembayaran_id as pembayaran_id, 
                                            tindakanpelayanan_id, 
                                            daftartindakan_id, 
                                            ruangan_id,
                                            qty_tindakan,
                                            jmlbiaya_tindakan,
                                            jmlsubsidi_asuransi,
                                            jmlsubsidi_pemerintah,
                                            jmlsubsidi_rs,
                                            jmliur_biaya,
                                            jml_pembebasan,
                                            jmlbayar_tindakan,
                                            jml_sisabayar_tindakan,
                                            tipepaket_id,
                                            pembulatan,
                                            additional_data,
                              NEW.created_by as created_by
                            FROM json_populate_recordset(null::tindakansudahbayar_t,vDataTindakan::json);
                     -- Setelah insert updatekan ke tindakanpelayanan_t
                    UPDATE tindakanpelayanan_t 
                                SET tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id,
                --                     tarif_tindakan = tindakansudahbayar_t.jmlbiaya_tindakan,  ***--> ini fungsinya buat apa yah?
                --                     tarif_satuan = (tindakansudahbayar_t.additional_data::json->>\'tarif_satuan\')::FLOAT, ***--> ini fungsinya buat apa yah?
                                    tarif_diskon = (tindakansudahbayar_t.additional_data::json->>\'tarif_diskon\')::FLOAT,
                                    keterangantindakan = (tindakansudahbayar_t.additional_data::json->>\'keterangan\')::TEXT,
                                    tarifcyto_tindakan = (tindakansudahbayar_t.additional_data::json->>\'tarif_cyto\')::FLOAT,
                                    tarif_dijamin = ((tindakansudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::FLOAT,
                                    tarif_dibayarkan = ((tindakansudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::FLOAT,
                                    is_valid = TRUE,
                                                        pembayaran_id = tindakansudahbayar_t.pembayaran_id                                      
                                FROM tindakansudahbayar_t 
                    WHERE (tindakanpelayanan_t.tindakansudahbayar_id IS NULL OR tindakanpelayanan_t.tipepaket_id IS NULL)
                                AND tindakansudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id
                        AND (tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id OR tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.parent_id) ;
                    END IF;

                  -- Insert ke  obatsudahbayar_t
                  IF (json_array_length(vDataObat::json) > 0)  THEN
                            INSERT INTO obatsudahbayar_t (
                                            pembayaranpelayanan_id, 
                                            pembayaran_id, 
                                            ruangan_id, 
                                            obatalkes_id, 
                                            obatalkespasien_id, 
                                            qty_obat, 
                                            hargasatuan, 
                                            jmlsubsidi_asuransi, 
                                            jmlsubsidi_pemerintah,
                                            jmlsubsidi_rs,
                                            jmliurbiaya,
                                            jmlbayar_obat,
                                            jmlsisabayar_obat,
                                            pembulatan,
                                                                        created_by,
                                                                        additional_data
                            ) SELECT 
                                            NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                                            NEW.pembayaran_id as pembayaran_id, 
                                            ruangan_id, 
                                            obatalkes_id, 
                                            obatalkespasien_id, 
                                            qty_obat, 
                                            hargasatuan, 
                                            jmlsubsidi_asuransi, 
                                            jmlsubsidi_pemerintah,
                                            jmlsubsidi_rs,
                                            jmliurbiaya,
                                            jmlbayar_obat,
                                            jmlsisabayar_obat,
                                            pembulatan,
                                                                        NEW.created_by as created_by,
                                                                        additional_data
                            FROM json_populate_recordset(null::obatsudahbayar_t,vDataObat::json);
                     -- Setelah insert updatekan ke obat alkes pasien
                    UPDATE obatalkespasien_t 
                            SET obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id,
                                    tarif_diskon = (obatsudahbayar_t.additional_data::json->>\'tarif_diskon\')::FLOAT,
                                    keterangan = (obatsudahbayar_t.additional_data::json->>\'keterangan\')::TEXT,
                                    tarif_dijamin = ((obatsudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::FLOAT,
                          tarif_dibayarkan = ((obatsudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::FLOAT,
                                    pembayaran_id = obatsudahbayar_t.pembayaran_id
                      FROM obatsudahbayar_t 
                            WHERE obatalkespasien_t.obatsudahbayar_id IS NULL 
                                AND obatsudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id
                        AND obatsudahbayar_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id ;
                        -- Update pembayaran Resep
                        UPDATE penjualanresep_t 
                                SET status_bayar = 348
                            FROM obatalkespasien_t
                                JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
                            WHERE obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id 
                                    AND obatsudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
                    END IF;
                   
                  NEW.additional_data := NULL;
                  vAdmin := paramJson::json->>\'biayaadministrasi\';
                  vKeterangan := paramJson::json->>\'sebagaipembayaran_bkm\';
                  vNamaBkm := paramJson::json->>\'darinama_bkm\';
                  vCaraPembayaran := paramJson::json->>\'carapembayaran\';
                  vPegawaiId := paramJson::json->>\'pegawai_id\';
                  vJumlahUangMuka := paramJson::json->>\'jumlah_uangmuka\';
                    vShifId := paramJson::json->>\'shift_id\';

                 -- Ini Status Ketika jaminan kalo perorangan dcheck ada pemakaian uang muka kalo ada insert ke pemakaian uang muka
                 -- dan update uangmuka
                --   vUangDiterima := NEW.total_terbayar;
                --     IF (vCaraPembayaran != \'31\')
                --      THEN
                --             NEW.penggunaan_uangmuka := 0;
                --       INSERT INTO piutangasuransi_t (
                --                         pembayaranpelayanan_id,
                --             penjamin_id,
                --             carabayar_id,
                --             jmlpiutangasuransi,
                --             created_by
                --         ) VALUES (
                --            NEW.pembayaranpelayanan_id,
                --            NEW.penjamin_id,
                --            NEW.carabayar_id,
                --            NEW.total_biayapelayanan,
                --            NEW.created_by
                --         );
                --   ELSE
                --             IF (NEW.penggunaan_uangmuka > 0)
                --                 THEN
                --                         INSERT INTO pemakaianuangmuka_t (
                --                             pembayaranpelayanan_id,
                --                             pendaftaran_id,
                --                             tgl_pemakaian,
                --                             total_uangmuka,
                --                             pemakaian_uangmuka,
                --                             sisa_uangmuka,
                --                             created_by
                --                         ) VALUES (
                --                             NEW.pembayaranpelayanan_id,
                --                             vIdPendaftaran,
                --                             NEW.tgl_pembayaran,
                --                             vJumlahUangMuka,
                --                             NEW.penggunaan_uangmuka,
                --                             (vJumlahUangMuka - NEW.penggunaan_uangmuka),
                --                             NEW.created_by
                --                         ) RETURNING pemakaianuangmuka_id INTO vIdPemakaianUang;
                --                         
                --                         UPDATE bayaruangmuka_t SET
                --                             pemakaianuangmuka_id = vIdPemakaianUang
                --                         WHERE pendaftaran_id = vIdPendaftaran AND pemakaianuangmuka_id IS NULL;
                --             END IF;
                --   END IF; 

                    vUangDiterima := 0;
                    IF (NEW.total_biayapelayanan > NEW.penggunaan_uangmuka)
                     THEN
                                IF (vCaraPembayaran = \'31\')
                                    THEN
                                            vUangDiterima := NEW.total_biayapelayanan -   NEW.penggunaan_uangmuka;
                                    END IF;
                  END IF;
                -- Jika ada pemakaian uang muka
                IF (NEW.penggunaan_uangmuka > 0)
                        THEN
                            IF (NEW.penggunaan_uangmuka > NEW.total_sisatagihan) 
                                THEN
                                        -- Komen
                                        NEW.total_sisatagihan := 0;
                                        vUangKembalian := vUangKembalian + NEW.penggunaan_uangmuka;
                            ELSE
                                        NEW.total_sisatagihan := NEW.total_sisatagihan - NEW.penggunaan_uangmuka;
                            END IF;
                END IF;

                IF (vUangKembalian < 0)
                        THEN
                        vUangKembalian := 0;
                END IF;

                  IF (NEW.total_sisatagihan <= 0) THEN
                            NEW.statusbayar = 348;
                      NEW.is_lunas = TRUE;
                    END IF;


                --  INSERT INTO tandabuktibayar_t (
                --     ruangan_id, 
                --     pembayaranpelayanan_id, 
                --     tglbuktibayar,
                --     darinama_bkm,
                --     sebagaipembayaran_bkm,
                --     jmlpembayaran,
                --     biayaadministrasi,
                --     biayamaterai,
                --     uangditerima,
                --     uangkembalian,
                --     namapemilik_rek,
                --     no_rek,
                --     carapembayaran,
                --     pegawai1_id,
                --     created_by,
                --     shift_id,
                --      pembayaran_id
                --   ) VALUES (
                --     NEW.ruangan_id,
                --     NULL,
                --     NEW.tgl_pembayaran,
                --     vNamaBkm,
                --     vKeterangan,
                --     NEW.total_biayapelayanan,
                --     NEW.biaya_administrasi,
                --     0,
                --     NEW.total_bayartindakan,
                --     vUangKembalian,
                --     NEW.nama_pemrekening,
                --     NEW.no_rekening,
                --     vCaraPembayaran,
                --     vPegawaiId,
                --     NEW.created_by,
                --     vShifId,
                --      NEW.pembayaran_id
                --   ) RETURNING tandabuktibayar_id INTO vTandaBuktiBayarId;
                --     NEW.tandabuktibayar_id := vTandaBuktiBayarId;
                    RETURN NEW;
                END
                $BODY$
                  LANGUAGE plpgsql VOLATILE
                  COST 100
            ');

            $this->execute('
                CREATE OR REPLACE FUNCTION "public"."delete_tindakansudahbayar"()
                RETURNS "pg_catalog"."trigger" AS $BODY$-- author: Rizqi Febian

                            DECLARE

                            varPembayaranPelayananId INT;
                            varPembayaranId INT;
                            varPendaftaranId INT;
                            isDeleted BOOLEAN;
                            dateDeleted DATE; 
                            deletedBy INT;

                            BEGIN
                            varPembayaranPelayananId := NEW.pembayaranpelayanan_id;
                            varPembayaranId := NEW.pembayaran_id;
                            varPendaftaranId := NEW.pendaftaran_id;
                            isDeleted := NEW.is_deleted;
                            dateDeleted := NEW.deleted_date;
                            deletedBy := NEW.deleted_by;

                    IF isDeleted = TRUE THEN
                        
                ----------tandabuktibayar_t-----------------    
                             UPDATE tandabuktibayar_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                    AND is_deleted = false;
                                
                ----------piutangasuransi_t-----------------    
                       UPDATE piutangasuransi_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                    AND is_deleted = false;

                ----------tindakanpelayanan_t-----------------    
                             UPDATE tindakanpelayanan_t 
                                    SET tindakansudahbayar_id  = NULL,
                                            tarif_dijamin = NULL,
                                            tarif_dibayarkan = NULL
                                    FROM (SELECT 
                                                    tindakanpelayanan_id, tindakansudahbayar_id 
                                                FROM tindakansudahbayar_t 
                                                WHERE pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = FALSE
                                                ) as subquery 
                                    WHERE tindakanpelayanan_t.tindakanpelayanan_id = subquery.tindakanpelayanan_id OR subquery.tindakanpelayanan_id = tindakanpelayanan_t.parent_id;
                                    
                ----------obatalkespasien_t-----------------    
                                UPDATE obatalkespasien_t 
                                    SET obatsudahbayar_id  = NULL 
                                    FROM (SELECT 
                                                    obatalkespasien_id, obatsudahbayar_id 
                                                FROM obatsudahbayar_t 
                                                WHERE pembayaranpelayanan_id = varPembayaranPelayananId) as subquery 
                                    WHERE obatalkespasien_t.obatalkespasien_id = subquery.obatalkespasien_id;
                                    
                ----------obatsudahbayar_t-----------------    
                                UPDATE obatsudahbayar_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                 WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                     AND is_deleted = false;
                                     
                ----------tindakansudahbayar_t-----------------    
                                UPDATE tindakansudahbayar_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                 WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                   AND is_deleted = false;
                                     
                 ----------bayaruangmuka_t-----------------    
                                UPDATE bayaruangmuka_t 
                                    SET pemakaianuangmuka_id = NULL, 
                                            jumlah_uangmuka = subquery.jumlahuangmuka 
                                            FROM (SELECT 
                                                            pemakaianuangmuka_id,SUM(pemakaian_uangmuka) as jumlahuangmuka 
                                                        FROM pemakaianuangmuka_t 
                                                        WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                                            AND is_deleted = FALSE 
                                                        GROUP BY pemakaianuangmuka_id, pemakaian_uangmuka) as subquery 
                                            WHERE bayaruangmuka_t.pemakaianuangmuka_id = subquery.pemakaianuangmuka_id;
                                            
                 ----------pemakaianuangmuka_t-----------------    
                        UPDATE pemakaianuangmuka_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                 WHERE pembayaran_id = varPembayaranId 
                                   AND is_deleted = false;

                ----------pendaftaran_t-----------------                     
                                UPDATE pendaftaran_t set status_bayar = 349  
                                 WHERE pendaftaran_id = varPendaftaranId;
                                 
                ----------penjualanresep_t-----------------                  
                                UPDATE penjualanresep_t x
                                   SET status_bayar = 349
                                   FROM obatalkespasien_t AS y,
                                                obatsudahbayar_t z
                                     WHERE x.penjualanresep_id = y.penjualanresep_id
                                         AND y.obatalkespasien_id = z.obatalkespasien_id
                                         AND z.pembayaranpelayanan_id = varPembayaranPelayananId;
                        END IF;

                            RETURN NEW;

                            END
                            $BODY$
                  LANGUAGE plpgsql VOLATILE
                  COST 100;
        ');
        
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tindakansudahbayar_t_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
             
            BEGIN
                    -- INSERT table history tindakanpelayanan_r BILLING(+)
                    INSERT INTO tindakanpelayanan_r (       
                        tindakanpelayanan_id ,
                        shift_id ,
                        kelaspelayanan_id ,
                        kelastanggungan_id ,
                        pasien_id ,
                        rencanaoperasi_id ,
                        instalasi_id ,
                        daftartindakan_id ,
                        alatmedis_id ,
                        tipepaket_id ,
                        tindakansudahbayar_id ,
                        carabayar_id ,
                        pendaftaran_id ,
                        hasilpemeriksaanrad_id ,
                        jeniskasuspenyakit_id ,
                        hasilpemeriksaanrm_id ,
                        ruangan_id ,
                        konsulpoli_id ,
                        pasienmasukpenunjang_id ,
                        hasilpemeriksaanlabdetail_id ,
                        penjamin_id ,
                        pasienadmisi_id ,
                        verifikasitagihan_id ,
                        jurnalrekening_id ,
                        instruksitindakan_id ,
                        tgl_tindakan ,
                        tarif_rsakomodasi ,
                        tarif_medis ,
                        tarif_paramedis ,
                        tarif_bhp ,
                        tarif_satuan ,
                        tarif_tindakan ,
                        tarifcyto_tindakan ,
                        satuan_tindakan ,
                        qty_tindakan ,
                        cyto_tindakan ,
                        dokterpenanggungjawab_id ,
                        dokterpelaksana_id ,
                        dokteranastesi_id ,
                        dokterdelegasi_id ,
                        bidan1_id ,
                        bidan2_id ,
                        perawat1_id ,
                        perawat2_id ,
                        discount_tindakan ,
                        pembebasan_tindakan ,
                        subsidiasuransi_tindakan ,
                        subsidipemerintah_tindakan ,
                        subsisidirumahsakit_tindakan ,
                        uangditerima_tindakan ,
                        keterangantindakan ,
                        pembulatan ,
                        implementasi_id ,
                        is_dilakukan ,
                        pemakaianambulan_id ,
                        is_penatajasa ,
                        additional_riwayat ,
                        is_valid ,
                        kamarruangan_id ,
                        kamartempattidur_id ,
                        penyulit_tindakan ,
                        tarifpenyulit_tindakan ,
                        additional_data ,
                        created_date ,
                        created_by ,
                        keterangan,
                        no_tindakanpelayanan,
                        tarif_dijamin,
                        tarif_dibayarkan,
                                    tarif_diskon,
                                    pembayaran_id,
                                    is_penjaminutama
                    )
                    SELECT  
                        tindakanpelayanan_id ,
                        shift_id ,
                        kelaspelayanan_id ,
                        kelastanggungan_id ,
                        pasien_id ,
                        rencanaoperasi_id ,
                        instalasi_id ,
                        daftartindakan_id ,
                        alatmedis_id ,
                        tipepaket_id ,
                        NEW.tindakansudahbayar_id,
                                    CASE
                                            WHEN (NEW.additional_data::json->>\'carabayar_id\')::INTEGER IS NULL THEN carabayar_id
                                            ELSE (NEW.additional_data::json->>\'carabayar_id\')::INTEGER
                                    END, -- carabayar_id
                        pendaftaran_id ,
                        hasilpemeriksaanrad_id ,
                        jeniskasuspenyakit_id ,
                        hasilpemeriksaanrm_id ,
                        ruangan_id ,
                        konsulpoli_id ,
                        pasienmasukpenunjang_id ,
                        hasilpemeriksaanlabdetail_id ,
                                    CASE
                                            WHEN (NEW.additional_data::json->>\'penjamin_id\')::INTEGER IS NULL THEN penjamin_id
                                            ELSE (NEW.additional_data::json->>\'penjamin_id\')::INTEGER
                                    END, -- penjamin_id
                        pasienadmisi_id ,
                        verifikasitagihan_id ,
                        jurnalrekening_id ,
                        instruksitindakan_id ,
                        tgl_tindakan ,
                        tarif_rsakomodasi ,
                        tarif_medis ,
                        tarif_paramedis ,
                        tarif_bhp ,
                        tarif_satuan ,
                        tarif_tindakan ,
                        tarifcyto_tindakan ,
                        satuan_tindakan ,
                        qty_tindakan ,
                        cyto_tindakan ,
                        dokterpenanggungjawab_id ,
                        dokterpelaksana_id ,
                        dokteranastesi_id ,
                        dokterdelegasi_id ,
                        bidan1_id ,
                        bidan2_id ,
                        perawat1_id ,
                        perawat2_id ,
                        discount_tindakan ,
                        pembebasan_tindakan ,
                        subsidiasuransi_tindakan ,
                        subsidipemerintah_tindakan ,
                        subsisidirumahsakit_tindakan ,
                        uangditerima_tindakan ,
                        keterangantindakan ,
                        pembulatan ,
                        implementasi_id ,
                        is_dilakukan ,
                        pemakaianambulan_id ,
                        is_penatajasa ,
                        additional_riwayat ,
                        is_valid ,
                        kamarruangan_id ,
                        kamartempattidur_id ,
                        penyulit_tindakan ,
                        tarifpenyulit_tindakan ,
                        additional_data ,
                        created_date ,
                        created_by ,
                        \'BILLING\',
                        no_tindakanpelayanan,
                                    CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                    (((NEW.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float / NEW.jmlbiaya_tindakan) * harga_origin
                                    ELSE 
                                    ((NEW.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float
                                    END,

                                    CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                    (((NEW.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float / NEW.jmlbiaya_tindakan) * harga_origin
                                    ELSE 
                                    ((NEW.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float
                                    END,

                                    CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                    ((NEW.additional_data::json->>\'tarif_diskon\')::float / NEW.jmlbiaya_tindakan) * harga_origin
                                    ELSE 
                                    (NEW.additional_data::json->>\'tarif_diskon\')::float
                                    END,

                                                    
                                    NEW.pembayaran_id,
                                    (NEW.additional_data::json->>\'is_penjaminutama\')::BOOLEAN
                FROM tindakanpelayanan_t
                WHERE (tindakanpelayanan_id = NEW.tindakanpelayanan_id OR parent_id = NEW.tindakanpelayanan_id);
                
                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;    
        '); 

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tindakansudahbayar_t_cancel"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                
            DECLARE
                v_keterangan VARCHAR;
                v_tglbatal TIMESTAMP;
                            
            BEGIN
                        SELECT
                            deleted_date
                            INTO 
                            v_tglbatal
                        FROM pembayaranpelayanan_t
                        WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
                        
                    IF(NEW.is_deleted IS TRUE)
                    THEN
                        -- INSERT table history tindakanpelayanan_r menjadi ACCRUAL(+), jika is_deleted=TRUE
                        INSERT INTO tindakanpelayanan_r (       
                            tindakanpelayanan_id ,
                            shift_id ,
                            kelaspelayanan_id ,
                            kelastanggungan_id ,
                            pasien_id ,
                            rencanaoperasi_id ,
                            instalasi_id ,
                            daftartindakan_id ,
                            alatmedis_id ,
                            tipepaket_id ,
                            tindakansudahbayar_id ,
                            carabayar_id ,
                            pendaftaran_id ,
                            hasilpemeriksaanrad_id ,
                            jeniskasuspenyakit_id ,
                            hasilpemeriksaanrm_id ,
                            ruangan_id ,
                            konsulpoli_id ,
                            pasienmasukpenunjang_id ,
                            hasilpemeriksaanlabdetail_id ,
                            penjamin_id ,
                            pasienadmisi_id ,
                            verifikasitagihan_id ,
                            jurnalrekening_id ,
                            instruksitindakan_id ,
                            tgl_tindakan ,
                            tarif_rsakomodasi ,
                            tarif_medis ,
                            tarif_paramedis ,
                            tarif_bhp ,
                            tarif_satuan ,
                            tarif_tindakan ,
                            tarifcyto_tindakan ,
                            satuan_tindakan ,
                            qty_tindakan ,
                            cyto_tindakan ,
                            dokterpenanggungjawab_id ,
                            dokterpelaksana_id ,
                            dokteranastesi_id ,
                            dokterdelegasi_id ,
                            bidan1_id ,
                            bidan2_id ,
                            perawat1_id ,
                            perawat2_id ,
                            discount_tindakan ,
                            pembebasan_tindakan ,
                            subsidiasuransi_tindakan ,
                            subsidipemerintah_tindakan ,
                            subsisidirumahsakit_tindakan ,
                            uangditerima_tindakan ,
                            keterangantindakan ,
                            pembulatan ,
                            implementasi_id ,
                            is_dilakukan ,
                            pemakaianambulan_id ,
                            is_penatajasa ,
                            additional_riwayat ,
                            is_valid ,
                            kamarruangan_id ,
                            kamartempattidur_id ,
                            penyulit_tindakan ,
                            tarifpenyulit_tindakan ,
                            additional_data ,
                            created_date ,
                            created_by ,
                            modified_count ,
                            last_modified_date ,
                            last_modified_by ,
                            is_deleted ,
                            is_active ,
                            deleted_date ,
                            deleted_by ,
                            keterangan,
                            no_tindakanpelayanan,
                                            tarif_dijamin,
                                            tarif_dibayarkan,
                                            tarif_diskon,
                                            pembayaran_id
                            )
                            SELECT
                            tindakanpelayanan_id ,
                            shift_id ,
                            kelaspelayanan_id ,
                            kelastanggungan_id ,
                            pasien_id ,
                            rencanaoperasi_id ,
                            instalasi_id ,
                            daftartindakan_id ,
                            alatmedis_id ,
                            tipepaket_id ,
                            NULL,
                            carabayar_id ,
                            pendaftaran_id ,
                            hasilpemeriksaanrad_id ,
                            jeniskasuspenyakit_id ,
                            hasilpemeriksaanrm_id ,
                            ruangan_id ,
                            konsulpoli_id ,
                            pasienmasukpenunjang_id ,
                            hasilpemeriksaanlabdetail_id ,
                            penjamin_id ,
                            pasienadmisi_id ,
                            verifikasitagihan_id ,
                            jurnalrekening_id ,
                            instruksitindakan_id ,
                            tgl_tindakan ,
                            tarif_rsakomodasi ,
                            tarif_medis ,
                            tarif_paramedis ,
                            tarif_bhp ,
                            tarif_satuan ,
                            tarif_tindakan ,
                            tarifcyto_tindakan ,
                            satuan_tindakan ,
                            qty_tindakan ,
                            cyto_tindakan ,
                            dokterpenanggungjawab_id ,
                            dokterpelaksana_id ,
                            dokteranastesi_id ,
                            dokterdelegasi_id ,
                            bidan1_id ,
                            bidan2_id ,
                            perawat1_id ,
                            perawat2_id ,
                            discount_tindakan ,
                            pembebasan_tindakan ,
                            subsidiasuransi_tindakan ,
                            subsidipemerintah_tindakan ,
                            subsisidirumahsakit_tindakan ,
                            uangditerima_tindakan ,
                            keterangantindakan ,
                            pembulatan ,
                            implementasi_id ,
                            is_dilakukan ,
                            pemakaianambulan_id ,
                            is_penatajasa ,
                            additional_riwayat ,
                            is_valid ,
                            kamarruangan_id ,
                            kamartempattidur_id ,
                            penyulit_tindakan ,
                            tarifpenyulit_tindakan ,
                            additional_data ,
                            created_date ,
                            created_by ,
                            modified_count ,
                            last_modified_date ,
                            last_modified_by ,
                            is_deleted ,
                            is_active ,
                            deleted_date ,
                            deleted_by ,
                            \'ACCRUAL\',
                            no_tindakanpelayanan,
                                            0,
                                            0,
                                            0,
                                            NULL
                            FROM tindakanpelayanan_t
                        WHERE (tindakanpelayanan_id = NEW.tindakanpelayanan_id OR parent_id = NEW.tindakanpelayanan_id);
                    
                    
                        -- INSERT table history tindakanpelayanan_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
                            INSERT INTO tindakanpelayanan_r (       
                                tindakanpelayanan_id ,
                                shift_id ,
                                kelaspelayanan_id ,
                                kelastanggungan_id ,
                                pasien_id ,
                                rencanaoperasi_id ,
                                instalasi_id ,
                                daftartindakan_id ,
                                alatmedis_id ,
                                tipepaket_id ,
                                tindakansudahbayar_id ,
                                carabayar_id ,
                                pendaftaran_id ,
                                hasilpemeriksaanrad_id ,
                                jeniskasuspenyakit_id ,
                                hasilpemeriksaanrm_id ,
                                ruangan_id ,
                                konsulpoli_id ,
                                pasienmasukpenunjang_id ,
                                hasilpemeriksaanlabdetail_id ,
                                penjamin_id ,
                                pasienadmisi_id ,
                                verifikasitagihan_id ,
                                jurnalrekening_id ,
                                instruksitindakan_id ,
                                tgl_tindakan ,
                                tarif_rsakomodasi ,
                                tarif_medis ,
                                tarif_paramedis ,
                                tarif_bhp ,
                                tarif_satuan ,
                                tarif_tindakan ,
                                tarifcyto_tindakan ,
                                satuan_tindakan ,
                                qty_tindakan ,
                                cyto_tindakan ,
                                dokterpenanggungjawab_id ,
                                dokterpelaksana_id ,
                                dokteranastesi_id ,
                                dokterdelegasi_id ,
                                bidan1_id ,
                                bidan2_id ,
                                perawat1_id ,
                                perawat2_id ,
                                discount_tindakan ,
                                pembebasan_tindakan ,
                                subsidiasuransi_tindakan ,
                                subsidipemerintah_tindakan ,
                                subsisidirumahsakit_tindakan ,
                                uangditerima_tindakan ,
                                keterangantindakan ,
                                pembulatan ,
                                implementasi_id ,
                                is_dilakukan ,
                                pemakaianambulan_id ,
                                is_penatajasa ,
                                additional_riwayat ,
                                is_valid ,
                                kamarruangan_id ,
                                kamartempattidur_id ,
                                penyulit_tindakan ,
                                tarifpenyulit_tindakan ,
                                additional_data ,
                                created_date ,
                                created_by ,
                                modified_count ,
                                last_modified_date ,
                                last_modified_by ,
                                is_deleted ,
                                is_active ,
                                deleted_date ,
                                deleted_by ,
                                keterangan,
                                no_tindakanpelayanan,
                                                    tarif_dijamin,
                                                    tarif_dibayarkan,
                                                    tarif_diskon,
                                                    pembayaran_id,                      
                                                    is_penjaminutama
                                )
                                SELECT
                                tindakanpelayanan_id ,
                                shift_id ,
                                kelaspelayanan_id ,
                                kelastanggungan_id ,
                                pasien_id ,
                                rencanaoperasi_id ,
                                instalasi_id ,
                                daftartindakan_id ,
                                alatmedis_id ,
                                tipepaket_id ,
                                NEW.tindakansudahbayar_id ,
                                                    CASE
                                                                    WHEN (OLD.additional_data::json->>\'carabayar_id\')::INTEGER IS NULL THEN carabayar_id
                                                                    ELSE (OLD.additional_data::json->>\'carabayar_id\')::INTEGER
                                                    END, -- carabayar_id
                                pendaftaran_id ,
                                hasilpemeriksaanrad_id ,
                                jeniskasuspenyakit_id ,
                                hasilpemeriksaanrm_id ,
                                ruangan_id ,
                                konsulpoli_id ,
                                pasienmasukpenunjang_id ,
                                hasilpemeriksaanlabdetail_id ,
                                                    CASE
                                                                    WHEN (NEW.additional_data::json->>\'penjamin_id\')::INTEGER IS NULL THEN penjamin_id
                                                                    ELSE (NEW.additional_data::json->>\'penjamin_id\')::INTEGER
                                                    END, -- penjamin_id
                                pasienadmisi_id ,
                                verifikasitagihan_id ,
                                jurnalrekening_id ,
                                instruksitindakan_id ,
                                v_tglbatal ,
                                tarif_rsakomodasi ,
                                tarif_medis ,
                                tarif_paramedis ,
                                tarif_bhp ,
                                tarif_satuan ,
                                -1 * tarif_tindakan ,
                                tarifcyto_tindakan ,
                                satuan_tindakan ,
                                -1 * qty_tindakan ,
                                cyto_tindakan ,
                                dokterpenanggungjawab_id ,
                                dokterpelaksana_id ,
                                dokteranastesi_id ,
                                dokterdelegasi_id ,
                                bidan1_id ,
                                bidan2_id ,
                                perawat1_id ,
                                perawat2_id ,
                                discount_tindakan ,
                                pembebasan_tindakan ,
                                subsidiasuransi_tindakan ,
                                subsidipemerintah_tindakan ,
                                subsisidirumahsakit_tindakan ,
                                uangditerima_tindakan ,
                                keterangantindakan ,
                                pembulatan ,
                                implementasi_id ,
                                is_dilakukan ,
                                pemakaianambulan_id ,
                                is_penatajasa ,
                                additional_riwayat ,
                                is_valid ,
                                kamarruangan_id ,
                                kamartempattidur_id ,
                                penyulit_tindakan ,
                                tarifpenyulit_tindakan ,
                                additional_data ,
                                created_date ,
                                created_by ,
                                modified_count ,
                                last_modified_date ,
                                last_modified_by ,
                                is_deleted ,
                                is_active ,
                                deleted_date ,
                                deleted_by ,
                                \'BILLING CANCEL\',
                                no_tindakanpelayanan,
                                                    
                                    CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                    -1 * ((((OLD.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float / OLD.jmlbiaya_tindakan) * harga_origin)
                                    ELSE 
                                    -1 *((OLD.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float
                                    END,

                                    CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                    -1 * ((((OLD.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float / OLD.jmlbiaya_tindakan) * harga_origin)
                                    ELSE 
                                    -1 * ((OLD.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float
                                    END,

                                    CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                    -1 * (((OLD.additional_data::json->>\'tarif_diskon\')::float / OLD.jmlbiaya_tindakan) * harga_origin)
                                    ELSE 
                                    -1 * (OLD.additional_data::json->>\'tarif_diskon\')::float
                                    END,
                                                        OLD.pembayaran_id,
                                                        (OLD.additional_data::json->>\'is_penjaminutama\')::BOOLEAN
                                FROM tindakanpelayanan_t
                            WHERE (tindakanpelayanan_id = NEW.tindakanpelayanan_id OR parent_id = NEW.tindakanpelayanan_id);
                            
                END IF;
                
                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');  

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."proses_pembayaranpelayanan"()
              RETURNS "pg_catalog"."trigger" AS $BODY$

            DECLARE
                paramJson VARCHAR;
                vTPembayaran FLOAT;
                vAdmin FLOAT;
                vNamaBkm VARCHAR;
                vShifId INTEGER; 
                vKeterangan VARCHAR;
                vCaraPembayaran VARCHAR;
                vTandaBuktiBayarId INTEGER;
                vPegawaiId INTEGER;
                vDataObat VARCHAR;
                vDataTindakan VARCHAR;
                vIdPendaftaran INTEGER;
                vJumlahUangMuka INTEGER;
                vUangDiterima FLOAT;
                vIdPemakaianUang INTEGER;
                vUangKembalian FLOAT;
                vNumber VARCHAR;
                vPembayaran_id INTEGER;

            BEGIN
                -- NEW.total_bayartindakan := NEW.total_biayapelayanan - (NEW.total_subsidirs + NEW.total_subsidiasuransi + NEW.total_subsidipemerintah);
                NEW.total_iurbiaya := NEW.total_biayapelayanan - (NEW.total_subsidirs + NEW.total_subsidiasuransi + NEW.total_subsidipemerintah);
                NEW.total_terbayar := NEW.total_iurbiaya;
                NEW.total_sisatagihan = NEW.total_iurbiaya - NEW.total_bayartindakan;
                vUangKembalian := NEW.total_bayartindakan - NEW.total_iurbiaya;
                IF (NEW.total_bayartindakan > NEW.total_iurbiaya) THEN
                        NEW.total_sisatagihan = 0;
                END IF;

                NEW.statusbayar = 349;
                NEW.is_lunas = FALSE;
                    
                IF (NEW.total_iurbiaya > NEW.total_bayartindakan) 
                THEN
                        NEW.total_terbayar := NEW.total_bayartindakan;
                        IF (vCaraPembayaran = \'31\') 
                        THEN
                            NEW.is_lunas = FALSE;
                        END IF;
                END IF;

                vIdPendaftaran := NEW.pendaftaran_id;
                paramJson := NEW.additional_data;  
                vDataObat := paramJson::json->>\'obat\';
                vDataTindakan := paramJson::json->>\'tindakan\';

                -- Insert ke  pembayaranpelayanan_t
                IF (json_array_length(vDataTindakan::json) > 0)  
                THEN
                    INSERT INTO tindakansudahbayar_t (
                        pembayaranpelayanan_id,
                        pembayaran_id, 
                        tindakanpelayanan_id, 
                        daftartindakan_id, 
                        ruangan_id,
                        qty_tindakan,
                        jmlbiaya_tindakan,
                        jmlsubsidi_asuransi,
                        jmlsubsidi_pemerintah,
                        jmlsubsidi_rs,
                        jmliur_biaya,
                        jml_pembebasan,
                        jmlbayar_tindakan,
                        jml_sisabayar_tindakan,
                        tipepaket_id,
                        pembulatan,
                        additional_data,
                        created_by
                     ) 
                        SELECT 
                            NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                            NEW.pembayaran_id as pembayaran_id, 
                            tindakanpelayanan_id, 
                            daftartindakan_id, 
                            ruangan_id,
                            qty_tindakan,
                            jmlbiaya_tindakan,
                            jmlsubsidi_asuransi,
                            jmlsubsidi_pemerintah,
                            jmlsubsidi_rs,
                            jmliur_biaya,
                            jml_pembebasan,
                            jmlbayar_tindakan,
                            jml_sisabayar_tindakan,
                            tipepaket_id,
                            pembulatan,
                            additional_data,
                            NEW.created_by as created_by
                        FROM json_populate_recordset(null::tindakansudahbayar_t,vDataTindakan::json
                    );
                     
                     -- Setelah insert updatekan ke tindakanpelayanan_t
                    UPDATE tindakanpelayanan_t 
                    SET tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id,
            --  tarif_tindakan = tindakansudahbayar_t.jmlbiaya_tindakan,  ***--> ini fungsinya buat apa yah?
            --  tarif_satuan = (tindakansudahbayar_t.additional_data::json->>\'tarif_satuan\')::FLOAT, ***--> ini fungsinya buat apa yah?
                    keterangantindakan = (tindakansudahbayar_t.additional_data::json->>\'keterangan\')::TEXT,
                    tarifcyto_tindakan = (tindakansudahbayar_t.additional_data::json->>\'tarif_cyto\')::FLOAT,
                    tarif_dijamin = 
                        CASE WHEN parent_id IS NOT NULL
                            THEN    
                                ((((tindakansudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                            ELSE ((tindakansudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float
                        END,
                    tarif_dibayarkan = 
                    CASE WHEN parent_id IS NOT NULL
                    THEN    
                        ((((tindakansudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                    ELSE ((tindakansudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float
                    END,                        
                    tarif_diskon = 
                        CASE WHEN parent_id IS NOT NULL
                        THEN    
                            (((tindakansudahbayar_t.additional_data::json->>\'tarif_diskon\')::FLOAT / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                        ELSE (tindakansudahbayar_t.additional_data::json->>\'tarif_diskon\')::FLOAT
                        END,
                    is_valid = TRUE,
                    pembayaran_id = tindakansudahbayar_t.pembayaran_id                                      
                    FROM tindakansudahbayar_t 
                    WHERE (tindakanpelayanan_t.tindakansudahbayar_id IS NULL OR tindakanpelayanan_t.tipepaket_id IS NULL)
                    AND tindakansudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id
                    AND (tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id OR tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.parent_id) ;
                    END IF;
                    
                -- Insert ke  obatsudahbayar_t
                IF (json_array_length(vDataObat::json) > 0)  
                THEN
                    INSERT INTO obatsudahbayar_t (
                        pembayaranpelayanan_id, 
                        pembayaran_id, 
                        ruangan_id, 
                        obatalkes_id, 
                        obatalkespasien_id, 
                        qty_obat, 
                        hargasatuan, 
                        jmlsubsidi_asuransi, 
                        jmlsubsidi_pemerintah,
                        jmlsubsidi_rs,
                        jmliurbiaya,
                        jmlbayar_obat,
                        jmlsisabayar_obat,
                        pembulatan,
                        created_by,
                        additional_data
                    ) 
                    SELECT 
                        NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                        NEW.pembayaran_id as pembayaran_id, 
                        ruangan_id, 
                        obatalkes_id, 
                        obatalkespasien_id, 
                        qty_obat, 
                        hargasatuan, 
                        jmlsubsidi_asuransi, 
                        jmlsubsidi_pemerintah,
                        jmlsubsidi_rs,
                        jmliurbiaya,
                        jmlbayar_obat,
                        jmlsisabayar_obat,
                        pembulatan,
                        NEW.created_by as created_by,
                        additional_data
                    FROM json_populate_recordset(null::obatsudahbayar_t,vDataObat::json);
                    
                    -- Setelah insert updatekan ke obat alkes pasien
                    UPDATE obatalkespasien_t 
                    SET obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id,
                            tarif_diskon = (obatsudahbayar_t.additional_data::json->>\'tarif_diskon\')::FLOAT,
                            keterangan = (obatsudahbayar_t.additional_data::json->>\'keterangan\')::TEXT,
                            tarif_dijamin = ((obatsudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::FLOAT,
                            tarif_dibayarkan = ((obatsudahbayar_t.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::FLOAT,
                            pembayaran_id = obatsudahbayar_t.pembayaran_id
                    FROM obatsudahbayar_t 
                    WHERE obatalkespasien_t.obatsudahbayar_id IS NULL 
                    AND obatsudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id
                    AND obatsudahbayar_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id ;
                            -- Update pembayaran Resep
                    UPDATE penjualanresep_t 
                    SET status_bayar = 348
                    FROM obatalkespasien_t
                    JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
                    WHERE obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id 
                    AND obatsudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
                END IF;
                 
                NEW.additional_data := NULL;
                vAdmin := paramJson::json->>\'biayaadministrasi\';
                vKeterangan := paramJson::json->>\'sebagaipembayaran_bkm\';
                vNamaBkm := paramJson::json->>\'darinama_bkm\';
                vCaraPembayaran := paramJson::json->>\'carapembayaran\';
                vPegawaiId := paramJson::json->>\'pegawai_id\';
                vJumlahUangMuka := paramJson::json->>\'jumlah_uangmuka\';
                vShifId := paramJson::json->>\'shift_id\';

             -- Ini Status Ketika jaminan kalo perorangan dcheck ada pemakaian uang muka kalo ada insert ke pemakaian uang muka
             -- dan update uangmuka
            --   vUangDiterima := NEW.total_terbayar;
            --     IF (vCaraPembayaran != \'31\')
            --      THEN
            --             NEW.penggunaan_uangmuka := 0;
            --       INSERT INTO piutangasuransi_t (
            --                         pembayaranpelayanan_id,
            --             penjamin_id,
            --             carabayar_id,
            --             jmlpiutangasuransi,
            --             created_by
            --         ) VALUES (
            --            NEW.pembayaranpelayanan_id,
            --            NEW.penjamin_id,
            --            NEW.carabayar_id,
            --            NEW.total_biayapelayanan,
            --            NEW.created_by
            --         );
            --   ELSE
            --             IF (NEW.penggunaan_uangmuka > 0)
            --                 THEN
            --                         INSERT INTO pemakaianuangmuka_t (
            --                             pembayaranpelayanan_id,
            --                             pendaftaran_id,
            --                             tgl_pemakaian,
            --                             total_uangmuka,
            --                             pemakaian_uangmuka,
            --                             sisa_uangmuka,
            --                             created_by
            --                         ) VALUES (
            --                             NEW.pembayaranpelayanan_id,
            --                             vIdPendaftaran,
            --                             NEW.tgl_pembayaran,
            --                             vJumlahUangMuka,
            --                             NEW.penggunaan_uangmuka,
            --                             (vJumlahUangMuka - NEW.penggunaan_uangmuka),
            --                             NEW.created_by
            --                         ) RETURNING pemakaianuangmuka_id INTO vIdPemakaianUang;
            --                         
            --                         UPDATE bayaruangmuka_t SET
            --                             pemakaianuangmuka_id = vIdPemakaianUang
            --                         WHERE pendaftaran_id = vIdPendaftaran AND pemakaianuangmuka_id IS NULL;
            --             END IF;
            --   END IF; 

                    vUangDiterima := 0;
                    IF (NEW.total_biayapelayanan > NEW.penggunaan_uangmuka)
                    THEN
                        IF (vCaraPembayaran = \'31\')
                        THEN
                            vUangDiterima := NEW.total_biayapelayanan -   NEW.penggunaan_uangmuka;
                        END IF;
                    END IF;
                    -- Jika ada pemakaian uang muka
                    IF (NEW.penggunaan_uangmuka > 0)
                    THEN
                        IF (NEW.penggunaan_uangmuka > NEW.total_sisatagihan) 
                    THEN
                        -- Komen
                            NEW.total_sisatagihan := 0;
                            vUangKembalian := vUangKembalian + NEW.penggunaan_uangmuka;
                            ELSE
                            NEW.total_sisatagihan := NEW.total_sisatagihan - NEW.penggunaan_uangmuka;
                        END IF;
                    END IF;

                    IF (vUangKembalian < 0)
                    THEN
                        vUangKembalian := 0;
                    END IF;

                    IF (NEW.total_sisatagihan <= 0) THEN
                        NEW.statusbayar = 348;
                        NEW.is_lunas = TRUE;
                    END IF;
            --  INSERT INTO tandabuktibayar_t (
            --     ruangan_id, 
            --     pembayaranpelayanan_id, 
            --     tglbuktibayar,
            --     darinama_bkm,
            --     sebagaipembayaran_bkm,
            --     jmlpembayaran,
            --     biayaadministrasi,
            --     biayamaterai,
            --     uangditerima,
            --     uangkembalian,
            --     namapemilik_rek,
            --     no_rek,
            --     carapembayaran,
            --     pegawai1_id,
            --     created_by,
            --     shift_id,
            --      pembayaran_id
            --   ) VALUES (
            --     NEW.ruangan_id,
            --     NULL,
            --     NEW.tgl_pembayaran,
            --     vNamaBkm,
            --     vKeterangan,
            --     NEW.total_biayapelayanan,
            --     NEW.biaya_administrasi,
            --     0,
            --     NEW.total_bayartindakan,
            --     vUangKembalian,
            --     NEW.nama_pemrekening,
            --     NEW.no_rekening,
            --     vCaraPembayaran,
            --     vPegawaiId,
            --     NEW.created_by,
            --     vShifId,
            --      NEW.pembayaran_id
            --   ) RETURNING tandabuktibayar_id INTO vTandaBuktiBayarId;
            --     NEW.tandabuktibayar_id := vTandaBuktiBayarId;
                    RETURN NEW;
            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');  

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tindakansudahbayar_t_discount_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
            DECLARE v_tarif_diskon float;
                  v_tarif_dijamin float;

            BEGIN
                  if((NEW.additional_data::json->>\'tarif_diskon\')::float <> 0) THEN 
                        
                   v_tarif_diskon := (NEW.additional_data::json->>\'tarif_diskon\')::float;                                 
                         v_tarif_dijamin := ((NEW.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float;
                         
                                                                
                    -- INSERT table history tindakanpelayanan_r BILLING(+)
                    INSERT INTO tindakanpelayanan_r (       
                        tindakanpelayanan_id ,
                        shift_id ,
                        kelaspelayanan_id ,
                        kelastanggungan_id ,
                        pasien_id ,
                        rencanaoperasi_id ,
                        instalasi_id ,
                        daftartindakan_id ,
                        alatmedis_id ,
                        tipepaket_id ,
                        tindakansudahbayar_id ,
                        carabayar_id ,
                        pendaftaran_id ,
                        hasilpemeriksaanrad_id ,
                        jeniskasuspenyakit_id ,
                        hasilpemeriksaanrm_id ,
                        ruangan_id ,
                        konsulpoli_id ,
                        pasienmasukpenunjang_id ,
                        hasilpemeriksaanlabdetail_id ,
                        penjamin_id ,
                        pasienadmisi_id ,
                        verifikasitagihan_id ,
                        jurnalrekening_id ,
                        instruksitindakan_id ,
                        tgl_tindakan ,
                        tarif_rsakomodasi ,
                        tarif_medis ,
                        tarif_paramedis ,
                        tarif_bhp ,
                        tarif_satuan ,
                        tarif_tindakan ,
                        tarifcyto_tindakan ,
                        satuan_tindakan ,
                        qty_tindakan ,
                        cyto_tindakan ,
                        dokterpenanggungjawab_id ,
                        dokterpelaksana_id ,
                        dokteranastesi_id ,
                        dokterdelegasi_id ,
                        bidan1_id ,
                        bidan2_id ,
                        perawat1_id ,
                        perawat2_id ,
                        discount_tindakan ,
                        pembebasan_tindakan ,
                        subsidiasuransi_tindakan ,
                        subsidipemerintah_tindakan ,
                        subsisidirumahsakit_tindakan ,
                        uangditerima_tindakan ,
                        keterangantindakan ,
                        pembulatan ,
                        implementasi_id ,
                        is_dilakukan ,
                        pemakaianambulan_id ,
                        is_penatajasa ,
                        additional_riwayat ,
                        is_valid ,
                        kamarruangan_id ,
                        kamartempattidur_id ,
                        penyulit_tindakan ,
                        tarifpenyulit_tindakan ,
                        additional_data ,
                        created_date ,
                        created_by ,
                        keterangan,
                        no_tindakanpelayanan,
                        tarif_dijamin,
                        tarif_dibayarkan,
                                    tarif_diskon,
                                    pembayaran_id
                    )
                    SELECT  
                        tindakanpelayanan_id ,
                        shift_id ,
                        kelaspelayanan_id ,
                        kelastanggungan_id ,
                        pasien_id ,
                        rencanaoperasi_id ,
                        instalasi_id ,
                        daftartindakan_id ,
                        alatmedis_id ,
                        tipepaket_id ,
                        NEW.tindakansudahbayar_id,
                        CASE
                            WHEN (NEW.additional_data::json->>\'carabayar_id\')::INTEGER IS NULL THEN carabayar_id
                            ELSE (NEW.additional_data::json->>\'carabayar_id\')::INTEGER
                        END, -- carabayar_id
                        pendaftaran_id ,
                        hasilpemeriksaanrad_id ,
                        jeniskasuspenyakit_id ,
                        hasilpemeriksaanrm_id ,
                        ruangan_id ,
                        konsulpoli_id ,
                        pasienmasukpenunjang_id ,
                        hasilpemeriksaanlabdetail_id ,
                        CASE
                            WHEN (NEW.additional_data::json->>\'penjamin_id\')::INTEGER IS NULL THEN penjamin_id
                            ELSE (NEW.additional_data::json->>\'penjamin_id\')::INTEGER
                        END, -- penjamin_id
                        pasienadmisi_id ,
                        verifikasitagihan_id ,
                        jurnalrekening_id ,
                        instruksitindakan_id ,
                        tgl_tindakan ,
                        0 ,
                        0 ,
                        0 ,
                        0 ,
                        
            --                      0 - v_tarif_diskon ,
                                    0 - CASE WHEN parent_id IS NOT NULL
                                            THEN    
                                                (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                            ELSE 
                                                v_tarif_diskon
                                            END,
                                    
                                    
            --             0 - v_tarif_diskon ,
                                    0 - CASE WHEN parent_id IS NOT NULL
                                            THEN    
                                                (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                            ELSE 
                                                v_tarif_diskon
                                            END,
                        0 ,
                        satuan_tindakan ,
                        1 ,
                        cyto_tindakan ,
                        dokterpenanggungjawab_id ,
                        dokterpelaksana_id ,
                        dokteranastesi_id ,
                        dokterdelegasi_id ,
                        bidan1_id ,
                        bidan2_id ,
                        perawat1_id ,
                        perawat2_id ,
                        0 ,
                        pembebasan_tindakan ,
                        subsidiasuransi_tindakan ,
                        subsidipemerintah_tindakan ,
                        subsisidirumahsakit_tindakan ,
                        uangditerima_tindakan ,
                        keterangantindakan ,
                        0 ,
                        implementasi_id ,
                        is_dilakukan ,
                        pemakaianambulan_id ,
                        is_penatajasa ,
                        additional_riwayat ,
                        is_valid ,
                        kamarruangan_id ,
                        kamartempattidur_id ,
                        penyulit_tindakan ,
                        0 ,
                        additional_data ,
                        created_date ,
                        created_by ,
                        \'DISCOUNT\',
                        no_tindakanpelayanan,
                                    
                                    CASE WHEN v_tarif_dijamin <> 0 
                                        THEN 0 - CASE WHEN parent_id IS NOT NULL
                                                            THEN    
                                                                (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                                            ELSE 
                                                                v_tarif_diskon
                                                            END
                                        ELSE 0 END,
                                                            
                        CASE WHEN v_tarif_dijamin <> 0 
                                            THEN 0 
                                            ELSE 0 - CASE WHEN parent_id IS NOT NULL
                                                                THEN    
                                                                    (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                                                ELSE 
                                                                    v_tarif_diskon
                                                                END
                                     END,
                                    
                                    
                        0::float,
                        NEW.pembayaran_id
                FROM tindakanpelayanan_t
                WHERE (tindakanpelayanan_id = NEW.tindakanpelayanan_id  OR parent_id = NEW.tindakanpelayanan_id);
                END IF;
                
                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        '); 

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tindakansudahbayar_t_discount_cancel"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                
            DECLARE
                    v_keterangan VARCHAR;
                            v_tglbatal TIMESTAMP;
                            v_tarif_diskon float;
                            v_tarif_dijamin float;
                            
            BEGIN
                        SELECT
                            deleted_date
                            INTO 
                            v_tglbatal
                        FROM pembayaranpelayanan_t
                        WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
                        
                    IF(NEW.is_deleted IS TRUE AND (NEW.additional_data::json->>\'tarif_diskon\')::float <> 0)
                    THEN
                        -- INSERT table history tindakanpelayanan_r menjadi ACCRUAL(+), jika is_deleted=TRUE
                       v_tarif_diskon := (NEW.additional_data::json->>\'tarif_diskon\')::float;
                                 v_tarif_dijamin := ((NEW.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float;
                         
                        -- INSERT table history tindakanpelayanan_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
                            INSERT INTO tindakanpelayanan_r (       
                                tindakanpelayanan_id ,
                                shift_id ,
                                kelaspelayanan_id ,
                                kelastanggungan_id ,
                                pasien_id ,
                                rencanaoperasi_id ,
                                instalasi_id ,
                                daftartindakan_id ,
                                alatmedis_id ,
                                tipepaket_id ,
                                tindakansudahbayar_id ,
                                carabayar_id ,
                                pendaftaran_id ,
                                hasilpemeriksaanrad_id ,
                                jeniskasuspenyakit_id ,
                                hasilpemeriksaanrm_id ,
                                ruangan_id ,
                                konsulpoli_id ,
                                pasienmasukpenunjang_id ,
                                hasilpemeriksaanlabdetail_id ,
                                penjamin_id ,
                                pasienadmisi_id ,
                                verifikasitagihan_id ,
                                jurnalrekening_id ,
                                instruksitindakan_id ,
                                tgl_tindakan ,
                                tarif_rsakomodasi ,
                                tarif_medis ,
                                tarif_paramedis ,
                                tarif_bhp ,
                                tarif_satuan ,
                                tarif_tindakan ,
                                tarifcyto_tindakan ,
                                satuan_tindakan ,
                                qty_tindakan ,
                                cyto_tindakan ,
                                dokterpenanggungjawab_id ,
                                dokterpelaksana_id ,
                                dokteranastesi_id ,
                                dokterdelegasi_id ,
                                bidan1_id ,
                                bidan2_id ,
                                perawat1_id ,
                                perawat2_id ,
                                discount_tindakan ,
                                pembebasan_tindakan ,
                                subsidiasuransi_tindakan ,
                                subsidipemerintah_tindakan ,
                                subsisidirumahsakit_tindakan ,
                                uangditerima_tindakan ,
                                keterangantindakan ,
                                pembulatan ,
                                implementasi_id ,
                                is_dilakukan ,
                                pemakaianambulan_id ,
                                is_penatajasa ,
                                additional_riwayat ,
                                is_valid ,
                                kamarruangan_id ,
                                kamartempattidur_id ,
                                penyulit_tindakan ,
                                tarifpenyulit_tindakan ,
                                additional_data ,
                                created_date ,
                                created_by ,
                                modified_count ,
                                last_modified_date ,
                                last_modified_by ,
                                is_deleted ,
                                is_active ,
                                deleted_date ,
                                deleted_by ,
                                keterangan,
                                no_tindakanpelayanan,
                                                    tarif_dijamin,
                                                    tarif_dibayarkan,
                                                    tarif_diskon,
                                                    pembayaran_id
                                )
                                SELECT
                                tindakanpelayanan_id ,
                                shift_id ,
                                kelaspelayanan_id ,
                                kelastanggungan_id ,
                                pasien_id ,
                                rencanaoperasi_id ,
                                instalasi_id ,
                                daftartindakan_id ,
                                alatmedis_id ,
                                tipepaket_id ,
                                NEW.tindakansudahbayar_id ,
                                                    CASE
                                                            WHEN (OLD.additional_data::json->>\'carabayar_id\')::INTEGER IS NULL THEN carabayar_id
                                                            ELSE (OLD.additional_data::json->>\'carabayar_id\')::INTEGER
                                                    END, -- carabayar_id
                                pendaftaran_id ,
                                hasilpemeriksaanrad_id ,
                                jeniskasuspenyakit_id ,
                                hasilpemeriksaanrm_id ,
                                ruangan_id ,
                                konsulpoli_id ,
                                pasienmasukpenunjang_id ,
                                hasilpemeriksaanlabdetail_id ,
                                                    CASE
                                                            WHEN (NEW.additional_data::json->>\'penjamin_id\')::INTEGER IS NULL THEN penjamin_id
                                                            ELSE (NEW.additional_data::json->>\'penjamin_id\')::INTEGER
                                                    END, -- penjamin_id
                                pasienadmisi_id ,
                                verifikasitagihan_id ,
                                jurnalrekening_id ,
                                instruksitindakan_id ,
                                v_tglbatal ,
                                0 ,
                                0 ,
                                0 ,
                                0 ,
                                                    
                                0 - CASE WHEN parent_id IS NOT NULL
                                                            THEN    
                                                                (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                                            ELSE 
                                                                v_tarif_diskon
                                                            END,
                                                            
                                0 - CASE WHEN parent_id IS NOT NULL
                                                            THEN    
                                                                (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                                            ELSE 
                                                                v_tarif_diskon
                                                            END,
                                                            
                                tarifcyto_tindakan ,
                                satuan_tindakan ,
                                -1,
                                cyto_tindakan ,
                                dokterpenanggungjawab_id ,
                                dokterpelaksana_id ,
                                dokteranastesi_id ,
                                dokterdelegasi_id ,
                                bidan1_id ,
                                bidan2_id ,
                                perawat1_id ,
                                perawat2_id ,
                                discount_tindakan ,
                                pembebasan_tindakan ,
                                subsidiasuransi_tindakan ,
                                subsidipemerintah_tindakan ,
                                subsisidirumahsakit_tindakan ,
                                uangditerima_tindakan ,
                                keterangantindakan ,
                                pembulatan ,
                                implementasi_id ,
                                is_dilakukan ,
                                pemakaianambulan_id ,
                                is_penatajasa ,
                                additional_riwayat ,
                                is_valid ,
                                kamarruangan_id ,
                                kamartempattidur_id ,
                                penyulit_tindakan ,
                                tarifpenyulit_tindakan ,
                                additional_data ,
                                created_date ,
                                created_by ,
                                modified_count ,
                                last_modified_date ,
                                last_modified_by ,
                                is_deleted ,
                                is_active ,
                                deleted_date ,
                                deleted_by ,
                                \'DISCOUNT CANCEL\',
                                no_tindakanpelayanan,
                                                    
                                CASE WHEN v_tarif_dijamin <> 0 
                                                        THEN CASE WHEN parent_id IS NOT NULL
                                                                    THEN    
                                                                        (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                                                    ELSE 
                                                                        v_tarif_diskon
                                                                    END     
                                                        ELSE 0 
                                                    END,
                                
                                                    
                                                    CASE WHEN v_tarif_dijamin <> 0 
                                                        THEN 0 
                                                        ELSE CASE WHEN parent_id IS NOT NULL
                                                                    THEN    
                                                                        (v_tarif_diskon / NEW.jmlbiaya_tindakan) * harga_origin
                                                                    ELSE 
                                                                        v_tarif_diskon
                                                                    END 
                                                    END,
                                0,
                                OLD.pembayaran_id 
                                FROM tindakanpelayanan_t
                            WHERE (tindakanpelayanan_id = NEW.tindakanpelayanan_id  OR parent_id = NEW.tindakanpelayanan_id);
                            
                END IF;
                
                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        '); 

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembulatan_payer"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ DECLARE v_daftartindakan_id int;
            v_ruangan_id int;
            v_penjamin_id int;
            v_instalasi_id int;
            v_pegawai_id int;
            v_kelaspelayanan_id int;
            BEGIN

            ---------------------------------- SETUP META DATA TRANSAKSI --------------------------------------------------------
            IF (NEW.pembulatan <> 0) THEN
                
                IF (NEW.pasienadmisi_id IS NOT NULL) THEN
                    SELECT 
                        ruangan_id, 
                        penjamin_id, 
                        3 as instalasi_id, 
                        pegawai_id, 
                        kelaspelayanan_id 
                    INTO 
                        v_ruangan_id, 
                        v_penjamin_id, 
                        v_instalasi_id, 
                        v_pegawai_id, 
                        v_kelaspelayanan_id 
                    FROM 
                      pasienadmisi_t 
                    WHERE 
                      pasienadmisi_t.pasienadmisi_id = NEW.pasienadmisi_id;
                ELSE 
                    SELECT 
                        ruangan_id, 
                        penjamin_id, 
                        instalasi_id, 
                        pegawai_id, 
                       kelaspelayanan_id 
                    INTO 
                      v_ruangan_id, 
                      v_penjamin_id, 
                      v_instalasi_id, 
                      v_pegawai_id, 
                      v_kelaspelayanan_id 
                    FROM 
                      pendaftaran_t 
                    WHERE 
                      pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
                END IF;
            END IF;

             --------------------------> insert pembulatan ke tindakanpelayanan_r <--------------------------------
            IF(NEW.pembulatan <> 0) THEN 
            v_daftartindakan_id := 99990;
            -- daftartindakan_id untuk pembulatan
            INSERT INTO tindakanpelayanan_r (
              pendaftaran_id, daftartindakan_id, 
              tarif_satuan, tarif_tindakan, qty_tindakan, 
              keterangan, ruangan_id, instalasi_id, 
              penjamin_id, kelaspelayanan_id, 
              dokterpenanggungjawab_id, tgl_tindakan, 
              additional_data, created_date, created_by, 
              modified_count, last_modified_date, 
              last_modified_by, is_deleted, is_active, 
              deleted_date, deleted_by, tgl_proses, 
              pembayaran_id, tarif_diskon, tarif_dijamin, 
              tarif_dibayarkan
            ) 
            VALUES 
              (
                NEW.pendaftaran_id, 
                v_daftartindakan_id, 
                NEW.pembulatan, 
                NEW.pembulatan, 
                \'1\', 
                \'BILLING\', 
                v_ruangan_id, 
                v_instalasi_id, 
                v_penjamin_id, 
                v_kelaspelayanan_id, 
                v_pegawai_id, 
                new.created_date, 
                new.additional_data, 
                new.created_date, 
                new.created_by, 
                new.modified_count, 
                new.last_modified_date, 
                new.last_modified_by, 
                new.is_deleted, 
                new.is_active, 
                new.deleted_date, 
                new.deleted_by, 
                new.created_date, 
                new.pembayaran_id, 
                0, 
                NEW.pembulatan, --tarif_dijamin
                0 -- tarif_dibayarkan             
                );
            END IF;
            -------------------------------------------------------------------------------------------------------
            RETURN NEW;
            END $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        '); 

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220218_101116_migrate_DHC204_penyesuian_schema_MCU cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220218_101116_migrate_DHC204_penyesuian_schema_MCU cannot be reverted.\n";

        return false;
    }
    */
}
