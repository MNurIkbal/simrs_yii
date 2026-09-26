<?php

use yii\db\Migration;

/**
 * Class m210407_074832_improvment_update_view_partial_lab
 */
class m210407_074832_improvment_update_view_partial_lab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TABLE IF EXISTS "public"."hasilpemeriksaanlab_roche_t";
        ');

        $this->execute('
            CREATE TABLE "public"."hasilpemeriksaanlab_roche_t" (
                hasilpemeriksaanlab_roche_id serial8 NOT NULL PRIMARY KEY,
                logid text,
                ts text,
                "key" text,
                data_id text,
                data_reqid text,
                data_key text,
                log text,
                patient_id VARCHAR(30),
                patient_name VARCHAR(200),
                date_of_birth timestamp(6),
                gender VARCHAR(10),
                address TEXT,
                patient_class VARCHAR(30),
                case_no VARCHAR(30),
                order_ctrl VARCHAR(50),
                order_no VARCHAR(50),
                placer_order_no TEXT,
                order_status VARCHAR(30),
                transaction_time timestamp(6),
                result_time timestamp(6),
                result_status VARCHAR(30),
                priority VARCHAR(10),
                set_id VARCHAR(30),
                value_type VARCHAR(30),
                obv_id VARCHAR(30),
                obv_name TEXT,
                "value" TEXT,
                "unit_text" TEXT,
                "ref_range_1" TEXT,
                "ref_range_2" TEXT,
                "abnormal_flag" TEXT,
                "obv_status" TEXT,
                "obv_time" timestamp(6),
                "observer" TEXT,
                "method" TEXT,
                "specimen_type" VARCHAR(50),
                "specimen_name" VARCHAR(100),
                specimen_collection_time timestamp(6),
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
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
            DROP VIEW IF EXISTS "public"."infoorderanlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanlab_v" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
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
                COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
                pasienmasukpenunjang_t.status_periksa,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                    CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
                        WHEN true THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienkirimkeunitlain_t.is_rujukan,
                COALESCE(pemeriksaan.jml_pemeriksaan, (0)::bigint) AS jml_pemeriksaan,
                COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, (0)::bigint) AS jml_pemeriksaan_approve
               FROM (((((((((((pasienkirimkeunitlain_t
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
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
            UNION ALL
             SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
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
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.penjamin_id,
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
                COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
                pasienmasukpenunjang_t.status_periksa,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                    CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
                        WHEN true THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienkirimkeunitlain_t.is_rujukan,
                COALESCE(pemeriksaan.jml_pemeriksaan, (0)::bigint) AS jml_pemeriksaan,
                COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, (0)::bigint) AS jml_pemeriksaan_approve
               FROM ((((((((((((((pasienkirimkeunitlain_t
                 JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
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
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4);
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infoorderanlabdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanlabdetail_v" AS  SELECT permintaankepenunjang_t.permintaankepenunjang_id,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                daftartindakan_m.daftartindakan_nama,
                NULL::character varying AS tipepaket_nama,
                permintaankepenunjang_t.qtypermintaan,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tarif_pelayanan, 
                permintaankepenunjang_t.daftartindakan_id,
                permintaankepenunjang_t.tipepaket_id,
                permintaankepenunjang_t.tarif_cytotindakan,
                permintaankepenunjang_t.satuan_tindakan,
                permintaankepenunjang_t.is_approve,
                permintaankepenunjang_t.tgl_approve
               FROM ((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
              WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pemeriksaanlab_m.is_deleted = false) AND (jenispemeriksaanlab_m.is_deleted = false) AND (daftartindakan_m.is_deleted = false))
            UNION ALL
             SELECT permintaankepenunjang_t.permintaankepenunjang_id,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                concat(tipepaket_m.tipepaket_nama, \'-\', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
                tipepaket_m.tipepaket_nama,
                permintaankepenunjang_t.qtypermintaan,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tarif_pelayanan,
                paketpelayanan_mp.daftartindakan_id,
                permintaankepenunjang_t.tipepaket_id,
                permintaankepenunjang_t.tarif_cytotindakan,
                permintaankepenunjang_t.satuan_tindakan,
                permintaankepenunjang_t.is_approve,
                permintaankepenunjang_t.tgl_approve
               FROM ((((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pemeriksaanlab_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
              WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienkirimkeunitlain_t.is_deleted = false) AND (jenispemeriksaanlab_m.is_deleted = false) AND (daftartindakan_m.is_deleted = false) AND (paketpelayanan_mp.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false));
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
                pasienmasukpenunjang_t.tanggal_verifikasi,
                pendaftaran_t.instalasi_id,
                NULL::text AS received_flag,
                pasienmasukpenunjang_t.is_hasil,
                pasien_m.alamat_pasien,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
                    CASE
                        WHEN (hasil_manual.hasil > 0) THEN true
                        ELSE false
                    END AS is_hasil_manual,
                    CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
                        WHEN true THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienmasukpenunjang_t.additional_data,
                    CASE
                        WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_hasil_bridging
               FROM ((((((((((((((pasienmasukpenunjang_t
                 JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                 JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pegawai_m dokter_perujuk ON ((pasienadmisi_t.pegawai_id = dokter_perujuk.pegawai_id)))
                 LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        count(*) AS hasil
                       FROM (hasilpemeriksaanlabdetail_t
                         JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS jml_hasil,
                        hasilpemeriksaanlab_roche_t.order_no
                       FROM hasilpemeriksaanlab_roche_t
                      GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
              WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
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
                pasienmasukpenunjang_t.status_periksa,
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
                pasienmasukpenunjang_t.tanggal_verifikasi,
                pendaftaran_t.instalasi_id,
                NULL::text AS received_flag,
                pasienmasukpenunjang_t.is_hasil,
                pasien_m.alamat_pasien,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
                    CASE
                        WHEN (hasil_manual.hasil > 0) THEN true
                        ELSE false
                    END AS is_hasil_manual,
                    CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
                        WHEN true THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienmasukpenunjang_t.additional_data,
                    CASE
                        WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_hasil_bridging
               FROM ((((((((((((((pasienmasukpenunjang_t
                 JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
                 LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        count(*) AS hasil
                       FROM (hasilpemeriksaanlabdetail_t
                         JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS jml_hasil,
                        hasilpemeriksaanlab_roche_t.order_no
                       FROM hasilpemeriksaanlab_roche_t
                      GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
              WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
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
                NULL::text AS received_flag,
                pasienmasukpenunjang_t.is_hasil,
                pasien_m.alamat_pasien,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
                    CASE
                        WHEN (hasil_manual.hasil > 0) THEN true
                        ELSE false
                    END AS is_hasil_manual,
                    CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
                        WHEN true THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienmasukpenunjang_t.additional_data,
                    CASE
                        WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_hasil_bridging
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
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        count(*) AS hasil
                       FROM (hasilpemeriksaanlabdetail_t
                         JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS jml_hasil,
                        hasilpemeriksaanlab_roche_t.order_no
                       FROM hasilpemeriksaanlab_roche_t
                      GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
              WHERE ((pendaftaran_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
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
                pasienmasukpenunjang_t.pegawai_id,
                pegawai_m.nama_pegawai AS dokter_penunjang,
                NULL::character varying AS no_rujukan,
                pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
                \'APS\'::character varying AS asalrujukan_nama,
                pasienmasukpenunjang_t.ruanganasal_id,
                ruangan_m.ruangan_nama,
                pasienmasukpenunjang_t.status_periksa,
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
                NULL::text AS received_flag,
                pasienmasukpenunjang_t.is_hasil,
                pasien_m.alamat_pasien,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
                    CASE
                        WHEN (hasil_manual.hasil > 0) THEN true
                        ELSE false
                    END AS is_hasil_manual,
                    CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
                        WHEN true THEN \'Sudah Bayar\'::text
                        ELSE \'Belum Bayar\'::text
                    END AS status_bayar,
                pasienmasukpenunjang_t.additional_data,
                    CASE
                        WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_hasil_bridging
               FROM ((((((((((((((pasienmasukpenunjang_t
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
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
                        count(*) AS hasil
                       FROM (hasilpemeriksaanlabdetail_t
                         JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
                      GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT count(*) AS jml_hasil,
                        hasilpemeriksaanlab_roche_t.order_no
                       FROM hasilpemeriksaanlab_roche_t
                      GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
              WHERE ((ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienmasukpenunjang_t.is_bayar = true));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_074832_improvment_update_view_partial_lab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_074832_improvment_update_view_partial_lab cannot be reverted.\n";

        return false;
    }
    */
}
