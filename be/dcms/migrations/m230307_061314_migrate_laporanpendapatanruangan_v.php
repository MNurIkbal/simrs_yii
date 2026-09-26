<?php

use yii\db\Migration;

/**
 * Class m230307_061314_migrate_laporanpendapatanruangan_v
 */
class m230307_061314_migrate_laporanpendapatanruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanpendapatanruangan_v";
        ');

        $this->execute('
        CREATE VIEW "public"."laporanpendapatanruangan_v" AS SELECT
        \'GABUNG\' :: TEXT AS tipe,
        gabung.pendaftaran_id,
        gabung.tgl_pendaftaran,
        gabung.no_pendaftaran,
        gabung.no_rekam_medik,
        gabung.nama_pasien,
        gabung.carabayar_nama,
        gabung.penjamin_nama,
        gabung.nama_pegawai,
        gabung.kelaspelayanan_nama,
        SUM ( gabung.jasa_rumahsakit_tarif ) AS jasa_rumahsakit_tarif,
        SUM ( gabung.jasa_rumahsakit_cyto ) AS jasa_rumahsakit_cyto,
        SUM ( gabung.jasa_rumahsakit ) AS jasa_rumahsakit,
        SUM ( gabung.jasa_layanan_tarif ) AS jasa_layanan_tarif,
        SUM ( gabung.jasa_layanan_cyto ) AS jasa_layanan_cyto,
        SUM ( gabung.jasa_layanan ) AS jasa_layanan,
        SUM ( gabung.jasa_rumahsakit ) + SUM ( gabung.jasa_layanan ) AS total,
        gabung.instalasi_id,
        gabung.ruangan_id,
        gabung.ruangan_nama,
        gabung.carabayar_id,
        gabung.penjamin_id,
        gabung.instalasi_nama,
        \'-\' :: TEXT AS jenis_pemeriksaan,
        \'-\' :: TEXT AS daftartindakan_nama 
    FROM
        (
        SELECT
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pegawai_m.nama_pegawai,
            kelaspelayanan_m.kelaspelayanan_nama,
            SUM ( CASE WHEN tindakankomponen_t.komponentarif_id = 1 THEN tindakankomponen_t.tarif_tindakankomp ELSE 0 :: DOUBLE PRECISION END ) AS jasa_rumahsakit_tarif,
            SUM ( CASE WHEN tindakankomponen_t.komponentarif_id = 1 THEN tindakankomponen_t.tarifcyto_tindakankomp ELSE 0 :: DOUBLE PRECISION END ) AS jasa_rumahsakit_cyto,
            SUM ( CASE WHEN tindakankomponen_t.komponentarif_id = 1 THEN tindakankomponen_t.tarif_tindakankomp + tindakankomponen_t.tarifcyto_tindakankomp ELSE 0 :: DOUBLE PRECISION END ) AS jasa_rumahsakit,
            SUM ( CASE WHEN tindakankomponen_t.komponentarif_id <> 1 THEN tindakankomponen_t.tarif_tindakankomp ELSE 0 :: DOUBLE PRECISION END ) AS jasa_layanan_tarif,
            SUM ( CASE WHEN tindakankomponen_t.komponentarif_id <> 1 THEN tindakankomponen_t.tarifcyto_tindakankomp ELSE 0 :: DOUBLE PRECISION END ) AS jasa_layanan_cyto,
            SUM ( CASE WHEN tindakankomponen_t.komponentarif_id <> 1 THEN tindakankomponen_t.tarif_tindakankomp + tindakankomponen_t.tarifcyto_tindakankomp ELSE 0 :: DOUBLE PRECISION END ) AS jasa_layanan,
            SUM ( CASE WHEN tindakankomponen_t.komponentarif_id IS NOT NULL THEN tindakankomponen_t.tarif_tindakankomp ELSE 0 :: DOUBLE PRECISION END ) AS total,
            tindakanpelayanan_t.instalasi_id,
            tindakanpelayanan_t.ruangan_id,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_id,
            penjamin_m.penjamin_id,
            instalasi_m.instalasi_nama 
        FROM
            tindakankomponen_t
            JOIN (
            SELECT A
                .tindakanpelayanan_id,
                A.ruangan_id,
                A.pendaftaran_id,
                A.carabayar_id,
                A.penjamin_id,
                A.is_deleted,
                A.is_active,
                A.tindakansudahbayar_id,
                A.instalasi_id 
            FROM
                tindakanpelayanan_t A 
            ) tindakanpelayanan_t ON tindakankomponen_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
            JOIN (
            SELECT A
                .pendaftaran_id,
                A.tgl_pendaftaran,
                A.no_pendaftaran,
                A.instalasi_id,
                A.carabayar_id,
                A.penjamin_id,
                A.pasien_id,
                A.pegawai_id,
                A.kelaspelayanan_id,
                A.is_deleted 
            FROM
                pendaftaran_t A 
            ) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN ( SELECT A.pasien_id, A.no_rekam_medik, A.nama_pasien FROM pasien_m A ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ( SELECT A.carabayar_id, A.carabayar_nama FROM carabayar_m A ) carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
            JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
            JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
            JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN ( SELECT ruangan_m_1.ruangan_id, ruangan_m_1.instalasi_id, ruangan_m_1.ruangan_nama FROM ruangan_m ruangan_m_1 ) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
            JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id 
        WHERE
            tindakanpelayanan_t.is_deleted = FALSE 
            AND tindakanpelayanan_t.is_active = TRUE 
            AND tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL 
        GROUP BY
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            pasien_m.pasien_id,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            tindakanpelayanan_t.instalasi_id,
            tindakanpelayanan_t.ruangan_id,
            instalasi_m.instalasi_nama UNION ALL
        SELECT
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pegawai_m.nama_pegawai,
            kelaspelayanan_m.kelaspelayanan_nama,
            0 AS jasa_rumahsakit_tarif,
            0 AS jasa_rumahsakit_cyto,
            0 AS jasa_rumahsakit,
            SUM ( COALESCE ( obatalkespasien_t.hargajual_oa, 0 :: DOUBLE PRECISION ) ) AS jasa_layanan_tarif,
            0 AS jasa_layanan_cyto,
            SUM ( COALESCE ( obatalkespasien_t.hargajual_oa, 0 :: DOUBLE PRECISION ) ) AS jasa_layanan,
            SUM ( COALESCE ( obatalkespasien_t.hargajual_oa, 0 :: DOUBLE PRECISION ) ) AS total,
            pendaftaran_t.instalasi_id,
            obatalkespasien_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            instalasi_m.instalasi_nama 
        FROM
            obatalkespasien_t
            JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
            JOIN penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
            JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
            JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
            JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id 
        WHERE
            obatalkespasien_t.is_deleted = FALSE 
            AND obatalkespasien_t.is_active = TRUE 
            AND obatalkespasien_t.obatsudahbayar_id IS NOT NULL 
        GROUP BY
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            pasien_m.pasien_id,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.instalasi_id,
            obatalkespasien_t.ruangan_id,
            instalasi_m.instalasi_nama 
        ) gabung 
    WHERE
        gabung.instalasi_id <> ALL ( ARRAY [ 4, 5 ] ) 
    GROUP BY
        gabung.pendaftaran_id,
        gabung.tgl_pendaftaran,
        gabung.no_pendaftaran,
        gabung.no_rekam_medik,
        gabung.nama_pasien,
        gabung.carabayar_nama,
        gabung.penjamin_nama,
        gabung.nama_pegawai,
        gabung.kelaspelayanan_nama,
        gabung.instalasi_id,
        gabung.ruangan_id,
        gabung.ruangan_nama,
        gabung.carabayar_id,
        gabung.penjamin_id,
        gabung.instalasi_nama UNION ALL
    SELECT
        \'RADIOLOGI NON_PAKET\' :: TEXT AS tipe,
        pendaftaran_t.pendaftaran_id,
        pendaftaran_t.tgl_pendaftaran,
        pendaftaran_t.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        carabayar_m.carabayar_nama,
        penjamin_m.penjamin_nama,
        COALESCE ( rujukan_t.nama_perujuk, perujuk_m.namaperujuk, dokter_perujuk.nama_pegawai ) AS nama_pegawai,
        kelaspelayanan_m.kelaspelayanan_nama,
        tindakanpelayanan_t.jasa_rumahsakit_tarif,
        tindakanpelayanan_t.jasa_rumahsakit_cyto,
        tindakanpelayanan_t.jasa_rumahsakit,
        tindakanpelayanan_t.jasa_layanan_tarif,
        tindakanpelayanan_t.jasa_layanan_cyto,
        tindakanpelayanan_t.jasa_layanan,
        tindakanpelayanan_t.total,
        instalasi_m.instalasi_id,
        ruangan_m.ruangan_id,
        ruangan_m.ruangan_nama,
        carabayar_m.carabayar_id,
        penjamin_m.penjamin_id,
        instalasi_m.instalasi_nama,
        jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis_pemeriksaan,
        daftartindakan_m.daftartindakan_nama 
    FROM
        pasienmasukpenunjang_t
        JOIN (
        SELECT A
            .pendaftaran_id,
            A.no_pendaftaran,
            A.pasien_id,
            A.rujukan_id,
            A.tgl_pendaftaran,
            COALESCE ( pasienadmisi_t.pegawai_id, A.pegawai_id ) AS pegawai_id,
            COALESCE ( pasienadmisi_t.carabayar_id, A.carabayar_id ) AS carabayar_id,
            COALESCE ( pasienadmisi_t.penjamin_id, A.penjamin_id ) AS penjamin_id,
            COALESCE ( pasienadmisi_t.kelaspelayanan_id, A.kelaspelayanan_id ) AS kelaspelayanan_id 
        FROM
            pendaftaran_t
            A LEFT JOIN ( SELECT a_1.pasienadmisi_id, a_1.pegawai_id, a_1.carabayar_id, a_1.penjamin_id, a_1.kelaspelayanan_id FROM pasienadmisi_t a_1 ) pasienadmisi_t ON A.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id 
        ) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
        JOIN ( SELECT A.pasien_id, A.nama_pasien, A.no_rekam_medik, A.tanggal_lahir FROM pasien_m A ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
        JOIN (
        SELECT A
            .tindakanpelayanan_id,
            A.pasienmasukpenunjang_id,
            A.daftartindakan_id,
            A.dokterpenanggungjawab_id,
            A.tarif_satuan,
            A.qty_tindakan,
            A.tarifcyto_tindakan,
            A.tarif_tindakan,
            A.is_deleted,
            A.instalasi_id,
            A.ruangan_id,
            COALESCE ( tarif_rs.jasa_rumahsakit_tarif, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit_tarif,
            COALESCE ( tarif_rs.jasa_rumahsakit, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit,
            COALESCE ( tarif_rs.jasa_rumahsakit_cyto, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit_cyto,
            COALESCE ( tarifnon_rs.jasa_layanan_tarif, 0 :: DOUBLE PRECISION ) AS jasa_layanan_tarif,
            COALESCE ( tarifnon_rs.jasa_layanan, 0 :: DOUBLE PRECISION ) AS jasa_layanan,
            COALESCE ( tarifnon_rs.jasa_layanan_cyto, 0 :: DOUBLE PRECISION ) AS jasa_layanan_cyto,
            COALESCE ( tarif_rs.jasa_rumahsakit, 0 :: DOUBLE PRECISION ) + COALESCE ( tarifnon_rs.jasa_layanan, 0 :: DOUBLE PRECISION ) AS total 
        FROM
            tindakanpelayanan_t
            A LEFT JOIN (
            SELECT
                tindakankomponen_t.tindakanpelayanan_id,
                SUM ( tindakankomponen_t.tarif_kompsatuan ) AS jasa_rumahsakit_tarif,
                SUM ( tindakankomponen_t.tarif_tindakankomp ) AS jasa_rumahsakit,
                SUM ( tindakankomponen_t.tarifcyto_tindakankomp ) AS jasa_rumahsakit_cyto 
            FROM
                tindakankomponen_t 
            WHERE
                tindakankomponen_t.komponentarif_id = 1 
                AND tindakankomponen_t.is_deleted IS FALSE 
            GROUP BY
                tindakankomponen_t.tindakanpelayanan_id 
            ) tarif_rs ON A.tindakanpelayanan_id = tarif_rs.tindakanpelayanan_id
            LEFT JOIN (
            SELECT
                tindakankomponen_t.tindakanpelayanan_id,
                SUM ( tindakankomponen_t.tarif_kompsatuan ) AS jasa_layanan_tarif,
                SUM ( tindakankomponen_t.tarif_tindakankomp ) AS jasa_layanan,
                SUM ( tindakankomponen_t.tarifcyto_tindakankomp ) AS jasa_layanan_cyto 
            FROM
                tindakankomponen_t 
            WHERE
                tindakankomponen_t.komponentarif_id <> 1 
                AND tindakankomponen_t.is_deleted IS FALSE 
            GROUP BY
                tindakankomponen_t.tindakanpelayanan_id 
            ) tarifnon_rs ON A.tindakanpelayanan_id = tarifnon_rs.tindakanpelayanan_id 
        WHERE
            A.is_deleted IS FALSE 
        ) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
        JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
        JOIN ( SELECT A.daftartindakan_id, A.daftartindakan_nama FROM daftartindakan_m A ) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN ( SELECT A.daftartindakan_id, A.kelompokpemeriksaanrad_id, A.jenispemeriksaanrad_id, A.is_contrast FROM pemeriksaanrad_m A ) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
        JOIN (
        SELECT A
            .pasienmasukpenunjang_id,
            MAX ( A.created_date ) AS tgl_periksa,
            A.tindakanpelayanan_id,
            MAX ( A.tgl_verifikasi ) AS tgl_verifikasi 
        FROM
            hasilpemeriksaanrad_t A 
        WHERE
            A.is_deleted IS FALSE 
            AND A.tgl_verifikasi IS NOT NULL 
        GROUP BY
            A.pasienmasukpenunjang_id,
            A.tindakanpelayanan_id 
        ) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id 
        AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
        LEFT JOIN ( SELECT A.pasienkirimkeunitlain_id, A.catatan_dokterpengirim, A.pegawai_id FROM pasienkirimkeunitlain_t A ) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
        JOIN ( SELECT A.kelompokpemeriksaanrad_id, A.nama_kelompok FROM kelompokpemeriksaanrad_m A ) kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
        JOIN ( SELECT A.jenispemeriksaanrad_id, A.jenispemeriksaanrad_nama FROM jenispemeriksaanrad_m A ) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
        JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
        LEFT JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter_perujuk ON COALESCE ( pasienkirimkeunitlain_t.pegawai_id, pendaftaran_t.pegawai_id ) = dokter_perujuk.pegawai_id
        LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama FROM carabayar_m A ) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
        LEFT JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
        LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
        LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
        LEFT JOIN ( SELECT A.rujukan_id, A.asalrujukan_id, A.rujukandari_id, A.nama_perujuk FROM rujukan_t A ) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
        LEFT JOIN ( SELECT A.asalrujukan_id, A.asalrujukan_nama FROM asalrujukan_m A ) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
        LEFT JOIN ( SELECT A.perujuk_id, A.namaperujuk FROM perujuk_m A ) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id UNION ALL
    SELECT
        \'LAB NON_PAKET\' :: TEXT AS tipe,
        pendaftaran_t.pendaftaran_id,
        hasilpemeriksaanlab_t.tgl_periksa AS tgl_pendaftaran,
        pendaftaran_t.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        carabayar_m.carabayar_nama,
        penjamin_m.penjamin_nama,
        dokter_perujuk.nama_pegawai,
        kelaspelayanan_m.kelaspelayanan_nama,
        tindakanpelayanan_t.jasa_rumahsakit_tarif,
        tindakanpelayanan_t.jasa_rumahsakit_cyto,
        tindakanpelayanan_t.jasa_rumahsakit,
        tindakanpelayanan_t.jasa_layanan_tarif,
        tindakanpelayanan_t.jasa_layanan_cyto,
        tindakanpelayanan_t.jasa_layanan,
        tindakanpelayanan_t.total,
        instalasi_m.instalasi_id,
        ruangan_m.ruangan_id,
        ruangan_m.ruangan_nama,
        carabayar_m.carabayar_id,
        penjamin_m.penjamin_id,
        instalasi_m.instalasi_nama,
        jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis_pemeriksaan,
        daftartindakan_m.daftartindakan_nama 
    FROM
        pasienmasukpenunjang_t
        JOIN (
        SELECT A
            .pendaftaran_id,
            A.no_pendaftaran,
            A.pasien_id,
            COALESCE ( pasienadmisi_t.pegawai_id, A.pegawai_id ) AS pegawai_id,
            COALESCE ( pasienadmisi_t.carabayar_id, A.carabayar_id ) AS carabayar_id,
            COALESCE ( pasienadmisi_t.penjamin_id, A.penjamin_id ) AS penjamin_id,
            COALESCE ( pasienadmisi_t.kelaspelayanan_id, A.kelaspelayanan_id ) AS kelaspelayanan_id 
        FROM
            pendaftaran_t
            A LEFT JOIN ( SELECT a_1.pasienadmisi_id, a_1.pegawai_id, a_1.carabayar_id, a_1.penjamin_id, a_1.kelaspelayanan_id FROM pasienadmisi_t a_1 ) pasienadmisi_t ON A.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id 
        ) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
        JOIN ( SELECT A.pasien_id, A.nama_pasien, A.no_rekam_medik, A.tanggal_lahir FROM pasien_m A ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
        JOIN (
        SELECT A
            .tindakanpelayanan_id,
            A.pasienmasukpenunjang_id,
            A.daftartindakan_id,
            A.dokterpenanggungjawab_id,
            A.tarif_satuan,
            A.qty_tindakan,
            A.tarifcyto_tindakan,
            A.tarif_tindakan,
            A.is_deleted,
            A.instalasi_id,
            A.ruangan_id,
            COALESCE ( tarif_rs.jasa_rumahsakit_tarif, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit_tarif,
            COALESCE ( tarif_rs.jasa_rumahsakit, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit,
            COALESCE ( tarif_rs.jasa_rumahsakit_cyto, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit_cyto,
            COALESCE ( tarifnon_rs.jasa_layanan_tarif, 0 :: DOUBLE PRECISION ) AS jasa_layanan_tarif,
            COALESCE ( tarifnon_rs.jasa_layanan, 0 :: DOUBLE PRECISION ) AS jasa_layanan,
            COALESCE ( tarifnon_rs.jasa_layanan_cyto, 0 :: DOUBLE PRECISION ) AS jasa_layanan_cyto,
            COALESCE ( tarif_rs.jasa_rumahsakit, 0 :: DOUBLE PRECISION ) + COALESCE ( tarifnon_rs.jasa_layanan, 0 :: DOUBLE PRECISION ) AS total 
        FROM
            tindakanpelayanan_t
            A LEFT JOIN (
            SELECT
                tindakankomponen_t.tindakanpelayanan_id,
                SUM ( tindakankomponen_t.tarif_kompsatuan ) AS jasa_rumahsakit_tarif,
                SUM ( tindakankomponen_t.tarif_tindakankomp ) AS jasa_rumahsakit,
                SUM ( tindakankomponen_t.tarifcyto_tindakankomp ) AS jasa_rumahsakit_cyto 
            FROM
                tindakankomponen_t 
            WHERE
                tindakankomponen_t.komponentarif_id = 1 
                AND tindakankomponen_t.is_deleted IS FALSE 
            GROUP BY
                tindakankomponen_t.tindakanpelayanan_id 
            ) tarif_rs ON A.tindakanpelayanan_id = tarif_rs.tindakanpelayanan_id
            LEFT JOIN (
            SELECT
                tindakankomponen_t.tindakanpelayanan_id,
                SUM ( tindakankomponen_t.tarif_kompsatuan ) AS jasa_layanan_tarif,
                SUM ( tindakankomponen_t.tarif_tindakankomp ) AS jasa_layanan,
                SUM ( tindakankomponen_t.tarifcyto_tindakankomp ) AS jasa_layanan_cyto 
            FROM
                tindakankomponen_t 
            WHERE
                tindakankomponen_t.komponentarif_id <> 1 
                AND tindakankomponen_t.is_deleted IS FALSE 
            GROUP BY
                tindakankomponen_t.tindakanpelayanan_id 
            ) tarifnon_rs ON A.tindakanpelayanan_id = tarifnon_rs.tindakanpelayanan_id 
        WHERE
            A.is_deleted IS FALSE 
        ) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
        JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
        JOIN ( SELECT A.daftartindakan_id, A.daftartindakan_nama FROM daftartindakan_m A ) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN ( SELECT A.daftartindakan_id, A.jenispemeriksaanlab_id FROM pemeriksaanlab_m A ) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
        LEFT JOIN ( SELECT A.jenispemeriksaanlab_id, A.jenispemeriksaanlab_nama FROM jenispemeriksaanlab_m A ) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
        JOIN (
        SELECT A
            .pasienmasukpenunjang_id,
            MAX ( A.created_date ) AS tgl_periksa,
            A.tindakanpelayanan_id 
        FROM
            hasilpemeriksaanlab_t A 
        WHERE
            A.is_deleted IS FALSE 
        GROUP BY
            A.pasienmasukpenunjang_id,
            A.tindakanpelayanan_id 
        ) hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id 
        AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanlab_t.tindakanpelayanan_id
        LEFT JOIN ( SELECT A.pasienkirimkeunitlain_id, A.catatan_dokterpengirim, A.pegawai_id FROM pasienkirimkeunitlain_t A ) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
        JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
        LEFT JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter_perujuk ON COALESCE ( pasienkirimkeunitlain_t.pegawai_id, pendaftaran_t.pegawai_id ) = dokter_perujuk.pegawai_id
        LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama FROM carabayar_m A ) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
        LEFT JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
        LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
        LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id UNION ALL
    SELECT
        \'LAB NON_PAKET\' :: TEXT AS tipe,
        pendaftaran_t.pendaftaran_id,
        hasilpemeriksaanlab_t.tgl_periksa AS tgl_pendaftaran,
        pendaftaran_t.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        carabayar_m.carabayar_nama,
        penjamin_m.penjamin_nama,
        dokter_perujuk.nama_pegawai,
        kelaspelayanan_m.kelaspelayanan_nama,
        tindakanpelayanan_t.jasa_rumahsakit_tarif,
        tindakanpelayanan_t.jasa_rumahsakit_cyto,
        tindakanpelayanan_t.jasa_rumahsakit,
        tindakanpelayanan_t.jasa_layanan_tarif,
        tindakanpelayanan_t.jasa_layanan_cyto,
        tindakanpelayanan_t.jasa_layanan,
        tindakanpelayanan_t.total,
        instalasi_m.instalasi_id,
        ruangan_m.ruangan_id,
        ruangan_m.ruangan_nama,
        carabayar_m.carabayar_id,
        penjamin_m.penjamin_id,
        instalasi_m.instalasi_nama,
        jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis_pemeriksaan,
        daftartindakan_m.daftartindakan_nama 
    FROM
        pasienmasukpenunjang_t
        JOIN (
        SELECT A
            .pendaftaran_id,
            A.no_pendaftaran,
            A.pasien_id,
            COALESCE ( pasienadmisi_t.pegawai_id, A.pegawai_id ) AS pegawai_id,
            COALESCE ( pasienadmisi_t.carabayar_id, A.carabayar_id ) AS carabayar_id,
            COALESCE ( pasienadmisi_t.penjamin_id, A.penjamin_id ) AS penjamin_id,
            COALESCE ( pasienadmisi_t.kelaspelayanan_id, A.kelaspelayanan_id ) AS kelaspelayanan_id 
        FROM
            pendaftaran_t
            A LEFT JOIN ( SELECT a_1.pasienadmisi_id, a_1.pegawai_id, a_1.carabayar_id, a_1.penjamin_id, a_1.kelaspelayanan_id FROM pasienadmisi_t a_1 ) pasienadmisi_t ON A.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id 
        ) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
        JOIN ( SELECT A.pasien_id, A.nama_pasien, A.no_rekam_medik, A.tanggal_lahir FROM pasien_m A ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
        JOIN (
        SELECT A
            .tindakanpelayanan_id,
            A.pasienmasukpenunjang_id,
            A.daftartindakan_id,
            A.dokterpenanggungjawab_id,
            A.tarif_satuan,
            A.qty_tindakan,
            A.tarifcyto_tindakan,
            A.tarif_tindakan,
            A.is_deleted,
            A.instalasi_id,
            A.ruangan_id,
            COALESCE ( tarif_rs.jasa_rumahsakit_tarif, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit_tarif,
            COALESCE ( tarif_rs.jasa_rumahsakit, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit,
            COALESCE ( tarif_rs.jasa_rumahsakit_cyto, 0 :: DOUBLE PRECISION ) AS jasa_rumahsakit_cyto,
            COALESCE ( tarifnon_rs.jasa_layanan_tarif, 0 :: DOUBLE PRECISION ) AS jasa_layanan_tarif,
            COALESCE ( tarifnon_rs.jasa_layanan, 0 :: DOUBLE PRECISION ) AS jasa_layanan,
            COALESCE ( tarifnon_rs.jasa_layanan_cyto, 0 :: DOUBLE PRECISION ) AS jasa_layanan_cyto,
            COALESCE ( tarif_rs.jasa_rumahsakit, 0 :: DOUBLE PRECISION ) + COALESCE ( tarifnon_rs.jasa_layanan, 0 :: DOUBLE PRECISION ) AS total 
        FROM
            tindakanpelayanan_t
            A LEFT JOIN (
            SELECT
                tindakankomponen_t.tindakanpelayanan_id,
                SUM ( tindakankomponen_t.tarif_kompsatuan ) AS jasa_rumahsakit_tarif,
                SUM ( tindakankomponen_t.tarif_tindakankomp ) AS jasa_rumahsakit,
                SUM ( tindakankomponen_t.tarifcyto_tindakankomp ) AS jasa_rumahsakit_cyto 
            FROM
                tindakankomponen_t 
            WHERE
                tindakankomponen_t.komponentarif_id = 1 
                AND tindakankomponen_t.is_deleted IS FALSE 
            GROUP BY
                tindakankomponen_t.tindakanpelayanan_id 
            ) tarif_rs ON A.tindakanpelayanan_id = tarif_rs.tindakanpelayanan_id
            LEFT JOIN (
            SELECT
                tindakankomponen_t.tindakanpelayanan_id,
                SUM ( tindakankomponen_t.tarif_kompsatuan ) AS jasa_layanan_tarif,
                SUM ( tindakankomponen_t.tarif_tindakankomp ) AS jasa_layanan,
                SUM ( tindakankomponen_t.tarifcyto_tindakankomp ) AS jasa_layanan_cyto 
            FROM
                tindakankomponen_t 
            WHERE
                tindakankomponen_t.komponentarif_id <> 1 
                AND tindakankomponen_t.is_deleted IS FALSE 
            GROUP BY
                tindakankomponen_t.tindakanpelayanan_id 
            ) tarifnon_rs ON A.tindakanpelayanan_id = tarifnon_rs.tindakanpelayanan_id 
        WHERE
            A.is_deleted IS FALSE 
        ) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
        JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
        JOIN ( SELECT A.daftartindakan_id, A.daftartindakan_nama FROM daftartindakan_m A ) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN ( SELECT A.daftartindakan_id, A.jenispemeriksaanlab_id FROM pemeriksaanlab_m A ) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
        LEFT JOIN ( SELECT A.jenispemeriksaanlab_id, A.jenispemeriksaanlab_nama FROM jenispemeriksaanlab_m A ) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
        JOIN ( SELECT A.order_no FROM hasilpemeriksaanlab_roche_t A GROUP BY A.order_no ) hasilpemeriksaanlab_roche_t ON pasienmasukpenunjang_t.no_masukpenunjang :: TEXT = hasilpemeriksaanlab_roche_t.order_no ::
        TEXT JOIN (
        SELECT A
            .pasienmasukpenunjang_id,
            MAX ( A.created_date ) AS tgl_periksa,
            A.tindakanpelayanan_id 
        FROM
            hasilpemeriksaanlab_t A 
        WHERE
            A.is_deleted IS FALSE 
        GROUP BY
            A.pasienmasukpenunjang_id,
            A.tindakanpelayanan_id 
        ) hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id
        LEFT JOIN ( SELECT A.pasienkirimkeunitlain_id, A.catatan_dokterpengirim, A.pegawai_id FROM pasienkirimkeunitlain_t A ) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
        JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
        LEFT JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter_perujuk ON COALESCE ( pasienkirimkeunitlain_t.pegawai_id, pendaftaran_t.pegawai_id ) = dokter_perujuk.pegawai_id
        LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama FROM carabayar_m A ) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
        LEFT JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
        LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
        LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230307_061314_migrate_laporanpendapatanruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230307_061314_migrate_laporanpendapatanruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
