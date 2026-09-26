<?php

use yii\db\Migration;

/**
 * Class m211109_023209_migrate_US1738_Improveuipasienrujukanlab
 */
class m211109_023209_migrate_US1738_Improveuipasienrujukanlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infoorderanlab_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infoorderanlab_v\" AS
            SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
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
            WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
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
            WHERE ((permintaankepenunjang_t.is_deleted = false) AND (permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id))) x) AS pemeriksaan
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
            WHEN ((tindakan_bayar.jumlah_bayar <> (0)::double precision) AND (tindakan_bayar.jumlah_bayar IS NOT NULL)) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
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
            WHERE ((permintaankepenunjang_t.is_deleted = false) AND (permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id))) x) AS pemeriksaan
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
        ;");
        $this->execute('
            ALTER TABLE public.infoorderanlab_v OWNER TO postgres;
        ');

        $this->execute('DROP VIEW if exists public.infopasienlab_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienlab_v\" AS
            SELECT 'ORDER'::text AS tipe_pasien,
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
            ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
            ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
            ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
            ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienmasukpenunjang_t.tanggal_verifikasi,
            pendaftaran_t.instalasi_id,
            CASE
            WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
            END AS received_flag,
            pasienmasukpenunjang_t.is_hasil,
            pasien_m.alamat_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
            CASE
            WHEN (hasil_manual.hasil > 0) THEN true
            ELSE false
            END AS is_hasil_manual,
            pasienmasukpenunjang_t.additional_data,
            CASE
            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
            ELSE false
            END AS is_hasil_bridging,
            COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
            CASE
            WHEN (tindakanpelayanan.status = 'Sudah Bayar'::text) THEN true
            ELSE false
            END AS is_status_bayar,
            CASE
            WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
            END AS status_bayar_detail,
            COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
            fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
            FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id))) x) AS pemeriksaan
            FROM ((((((((((((((((pasienmasukpenunjang_t
            JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
            JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN pegawai_m dokter_perujuk ON ((pasienadmisi_t.pegawai_id = dokter_perujuk.pegawai_id)))
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
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END AS status
            FROM tindakanpelayanan_t
            WHERE (tindakanpelayanan_t.is_deleted = false)
            GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
            FROM tindakanpelayanan_t
            WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
            WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.is_indolab = false))
            UNION ALL
            SELECT 'ORDER'::text AS tipe_pasien,
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
            ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
            ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
            ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
            ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienmasukpenunjang_t.tanggal_verifikasi,
            pendaftaran_t.instalasi_id,
            CASE
            WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
            END AS received_flag,
            pasienmasukpenunjang_t.is_hasil,
            pasien_m.alamat_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
            CASE
            WHEN (hasil_manual.hasil > 0) THEN true
            ELSE false
            END AS is_hasil_manual,
            pasienmasukpenunjang_t.additional_data,
            CASE
            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
            ELSE false
            END AS is_hasil_bridging,
            COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
            CASE
            WHEN (tindakanpelayanan.status = 'Sudah Bayar'::text) THEN true
            ELSE false
            END AS is_status_bayar,
            CASE
            WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
            END AS status_bayar_detail,
            COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
            fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
            FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id))) x) AS pemeriksaan
            FROM ((((((((((((((((pasienmasukpenunjang_t
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
            hasilpemeriksaanlab_integrasi_t.order_no
            FROM hasilpemeriksaanlab_integrasi_t
            GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
            LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END AS status
            FROM tindakanpelayanan_t
            WHERE (tindakanpelayanan_t.is_deleted = false)
            GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
            FROM tindakanpelayanan_t
            WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
            WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.is_indolab = false))
            UNION ALL
            SELECT 'RUJUKAN RS'::text AS tipe_pasien,
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
            ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
            ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
            ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
            ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienmasukpenunjang_t.tanggal_verifikasi,
            pendaftaran_t.instalasi_id,
            CASE
            WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
            END AS received_flag,
            pasienmasukpenunjang_t.is_hasil,
            pasien_m.alamat_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
            CASE
            WHEN (hasil_manual.hasil > 0) THEN true
            ELSE false
            END AS is_hasil_manual,
            pasienmasukpenunjang_t.additional_data,
            CASE
            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
            ELSE false
            END AS is_hasil_bridging,
            COALESCE(tindakanpelayanan.status, 'Batal'::text) AS status_bayar,
            CASE
            WHEN (tindakanpelayanan.status = 'Sudah Bayar'::text) THEN true
            ELSE false
            END AS is_status_bayar,
            CASE
            WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
            END AS status_bayar_detail,
            COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
            fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
            FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id))) x) AS pemeriksaan
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
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END AS status
            FROM tindakanpelayanan_t
            WHERE (tindakanpelayanan_t.is_deleted = false)
            GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
            LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
            FROM tindakanpelayanan_t
            WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
            WHERE ((pendaftaran_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.is_aps = false) AND (pendaftaran_t.is_indolab = false))
            UNION ALL
            SELECT 'APS'::text AS tipe_pasien,
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
            'APS'::character varying AS asalrujukan_nama,
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
            ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
            ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
            ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
            ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienmasukpenunjang_t.tanggal_verifikasi,
            pendaftaran_t.instalasi_id,
            CASE
            WHEN (pasienmasukpenunjang_t.additional_data IS NOT NULL) THEN (((pasienmasukpenunjang_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
            END AS received_flag,
            pasienmasukpenunjang_t.is_hasil,
            pasien_m.alamat_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS dokter_perujuk_nama_w_gelar,
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
            WHEN (tindakanpelayanan.pendaftaran_id IS NULL) THEN 'Belum Bayar'::text
            ELSE COALESCE(tindakanpelayanan.status, 'Batal'::text)
            END AS status_bayar,
            CASE
            WHEN (tindakanpelayanan.status = 'Sudah Bayar'::text) THEN true
            ELSE false
            END AS is_status_bayar,
            CASE
            WHEN (pasienmasukpenunjang_t.is_bayar = true) THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
            END AS status_bayar_detail,
            COALESCE(cyto_tindakan.cyto_tindakan, false) AS is_cyto,
            fgetkodelookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin_kode,
            pegawai_m.tanda_tangan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglmasukpenunjang_riwayat,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
            FROM ( SELECT daftartindakan_m.daftartindakan_nama AS pemeriksaanlab_nama
            FROM (tindakanpelayanan_t
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id))) x) AS pemeriksaan
            FROM ((((((((((((((((pasienmasukpenunjang_t
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
            hasilpemeriksaanlab_integrasi_t.order_no
            FROM hasilpemeriksaanlab_integrasi_t
            GROUP BY hasilpemeriksaanlab_integrasi_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
            LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END AS status
            FROM tindakanpelayanan_t
            WHERE (tindakanpelayanan_t.is_deleted = false)
            GROUP BY tindakanpelayanan_t.pendaftaran_id,
            CASE
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN 'Sudah Bayar'::text
            ELSE 'Batal'::text
            END) tindakanpelayanan ON ((pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan.pendaftaran_id)))
            LEFT JOIN ( SELECT DISTINCT ON (tindakanpelayanan_t.pasienmasukpenunjang_id, tindakanpelayanan_t.cyto_tindakan) tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.cyto_tindakan
            FROM tindakanpelayanan_t
            WHERE ((tindakanpelayanan_t.cyto_tindakan = true) AND (tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.is_active = true))) cyto_tindakan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = cyto_tindakan.pasienmasukpenunjang_id)))
            WHERE ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pendaftaran_t.is_indolab = false) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
            ;");
        $this->execute('
            ALTER TABLE public.infopasienlab_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211109_023209_migrate_US1738_Improveuipasienrujukanlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211109_023209_migrate_US1738_Improveuipasienrujukanlab cannot be reverted.\n";

        return false;
    }
    */
}
