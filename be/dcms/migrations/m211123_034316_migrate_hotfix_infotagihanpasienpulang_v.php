<?php

use yii\db\Migration;

/**
 * Class m211123_034316_migrate_hotfix_infotagihanpasienpulang_v
 */
class m211123_034316_migrate_hotfix_infotagihanpasienpulang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infotagihanpasienpulang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infotagihanpasienpulang_v\" AS
            SELECT gabung.pendaftaran_id,
            gabung.pasienpulang_id,
            gabung.pasienpulangri_id,
            CASE
            WHEN ((gabung.pasienpulangri_id IS NULL) AND (gabung.pasienpulang_id IS NULL)) THEN COALESCE(gabung.tgl_stopakomodasi, gabung.tgl_pendaftaran)
            ELSE COALESCE(gabung.tglpasienpulang, gabung.tgl_pendaftaran)
            END AS tglpasienpulang,
            gabung.no_pendaftaran,
            gabung.instalasi_id,
            gabung.instalasi_nama,
            gabung.ruanganakhir_id AS ruangan_id,
            gabung.ruangan_nama,
            gabung.no_rekam_medik,
            gabung.nama_pasien,
            gabung.carabayar_id,
            gabung.carabayar_nama,
            gabung.penjamin_id,
            gabung.penjamin_nama,
            gabung.jeniskasuspenyakit_nama,
            gabung.status_bayar,
            gabung.kelaspelayanan_nama,
            gabung.nama_pegawai AS dokter,
            COALESCE((round((sum(gabung.total_tindakan))::numeric, 2))::double precision, (0)::double precision) AS total_tindakan,
            COALESCE((round((sum(gabung.total_obat))::numeric, 2))::double precision, (0)::double precision) AS total_obat,
            (COALESCE((round((sum(gabung.total_tindakan))::numeric, 2))::double precision, (0)::double precision) + COALESCE((round((sum(gabung.total_obat))::numeric, 2))::double precision, (0)::double precision)) AS total_tagihan,
            gabung.pegawai_id,
            gabung.photopasien,
            gabung.tanggal_lahir,
            gabung.umur,
            gabung.jeniskelamin,
            gabung.jenis_kelamin,
            gabung.tgl_pendaftaran,
            gabung.is_stopakomodasi,
            gabung.tgl_stopakomodasi,
            gabung.no_sep,
            gabung.status_pulang,
            gabung.tipe
            FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            COALESCE(ruangan_m.instalasi_id, ruangan_asal.instalasi_id) AS instalasi_id,
            COALESCE(instalasi_m.instalasi_nama, instalasi_asal.instalasi_nama) AS instalasi_nama,
            COALESCE(pasienpulang_t.ruanganakhir_id, (pendaftaran_t.ruangan_id)::bigint) AS ruanganakhir_id,
            COALESCE(ruangan_m.ruangan_nama, ruangan_asal.ruangan_nama) AS ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            tindakanpelayanan_t.tarif_tindakan AS total_tindakan,
            NULL::double precision AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            bpjs_t.nosep AS no_sep,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_pulang,
            'a'::text AS tipe
            FROM (((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.tindakansudahbayar_id,
            sum(a.tarif_tindakan) AS tarif_tindakan
            FROM tindakanpelayanan_t a
            WHERE (a.is_deleted = false)
            GROUP BY a.pendaftaran_id, a.tindakansudahbayar_id) tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang,
            a.ruanganakhir_id
            FROM pasienpulang_t a) pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
            FROM ruangan_m a) ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
            FROM ruangan_m a) ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.photopasien,
            a.tanggal_lahir,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.bpjs_id,
            a.nosep
            FROM bpjs_t a) bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            WHERE (((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) OR (pendaftaran_t.instalasi_id = 2))
            UNION ALL
            SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            COALESCE(ruangan_m.instalasi_id, ruangan_asal.instalasi_id) AS instalasi_id,
            COALESCE(instalasi_m.instalasi_nama, instalasi_asal.instalasi_nama) AS instalasi_nama,
            COALESCE(pasienpulang_t.ruanganakhir_id, (pendaftaran_t.ruangan_id)::bigint) AS ruanganakhir_id,
            COALESCE(ruangan_m.ruangan_nama, ruangan_asal.ruangan_nama) AS ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            tindakanpelayanan_t.tarif_tindakan AS total_tindakan,
            NULL::double precision AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            bpjs_t.nosep AS no_sep,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_pulang,
            'b'::text AS tipe
            FROM ((((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT b.pendaftaran_id,
            b.tindakansudahbayar_id,
            sum(b.tarif_tindakan) AS tarif_tindakan
            FROM tindakanpelayanan_t b
            WHERE (b.is_deleted = false)
            GROUP BY b.pendaftaran_id, b.tindakansudahbayar_id) tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            LEFT JOIN ( SELECT b.pasienadmisi_id,
            b.pasienpulang_id,
            b.ruangan_id,
            b.carabayar_id,
            b.penjamin_id,
            b.pegawai_id,
            b.bpjs_id,
            b.status_ranap
            FROM pasienadmisi_t b) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT b.pasienpulang_id,
            b.tglpasienpulang,
            b.carakeluar_id,
            b.ruanganakhir_id
            FROM pasienpulang_t b) pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id <> 5))))
            LEFT JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama,
            b.instalasi_id
            FROM ruangan_m b) ruangan_asal ON ((pasienadmisi_t.ruangan_id = ruangan_asal.ruangan_id)))
            LEFT JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama,
            b.instalasi_id
            FROM ruangan_m b) ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT b.instalasi_id,
            b.instalasi_nama
            FROM instalasi_m b) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT b.instalasi_id,
            b.instalasi_nama
            FROM instalasi_m b) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.no_rekam_medik,
            b.photopasien,
            b.tanggal_lahir,
            b.jeniskelamin
            FROM pasien_m b) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
            FROM carabayar_m b) carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT b.penjamin_id,
            b.penjamin_nama
            FROM penjamin_m b) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT b.jeniskasuspenyakit_id,
            b.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m b) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
            FROM kelaspelayanan_m b) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
            FROM pegawai_m b) pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT b.bpjs_id,
            b.nosep
            FROM bpjs_t b) bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
            WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (pendaftaran_t.is_stopakomodasi = true))
            UNION ALL
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            COALESCE(ruangan_m.instalasi_id, ruangan_asal.instalasi_id) AS instalasi_id,
            COALESCE(instalasi_m.instalasi_nama, instalasi_asal.instalasi_nama) AS instalasi_nama,
            COALESCE(pasienpulang_t.ruanganakhir_id, (pendaftaran_t.ruangan_id)::bigint) AS ruanganakhir_id,
            COALESCE(ruangan_m.ruangan_nama, ruangan_asal.ruangan_nama) AS ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            NULL::double precision AS total_tindakan,
            obatalkespasien_t.hargajual_oa AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            bpjs_t.nosep AS no_sep,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_pulang,
            'c'::text AS tipe
            FROM (((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT c.pendaftaran_id,
            c.obatsudahbayar_id,
            sum(c.hargajual_oa) AS hargajual_oa
            FROM obatalkespasien_t c
            WHERE (c.is_deleted = false)
            GROUP BY c.pendaftaran_id, c.obatsudahbayar_id) obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
            LEFT JOIN ( SELECT c.pasienpulang_id,
            c.tglpasienpulang,
            c.ruanganakhir_id,
            c.carakeluar_id
            FROM pasienpulang_t c) pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama,
            c.instalasi_id
            FROM ruangan_m c) ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama,
            c.instalasi_id
            FROM ruangan_m c) ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
            LEFT JOIN ( SELECT c.instalasi_id,
            c.instalasi_nama
            FROM instalasi_m c) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT c.instalasi_id,
            c.instalasi_nama
            FROM instalasi_m c) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            JOIN ( SELECT c.pasien_id,
            c.nama_pasien,
            c.tanggal_lahir,
            c.photopasien,
            c.no_rekam_medik,
            c.jeniskelamin
            FROM pasien_m c) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT c.carabayar_id,
            c.carabayar_nama
            FROM carabayar_m c) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT c.penjamin_id,
            c.penjamin_nama
            FROM penjamin_m c) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT c.jeniskasuspenyakit_id,
            c.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m c) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ( SELECT c.kelaspelayanan_id,
            c.kelaspelayanan_nama
            FROM kelaspelayanan_m c) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
            FROM pegawai_m c) pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT c.bpjs_id,
            c.nosep
            FROM bpjs_t c) bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            WHERE (((obatalkespasien_t.obatsudahbayar_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) OR ((pendaftaran_t.instalasi_id = 2) AND (pasienpulang_t.carakeluar_id <> 5)))
            UNION ALL
            SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            COALESCE(ruangan_m.instalasi_id, ruangan_asal.instalasi_id) AS instalasi_id,
            COALESCE(instalasi_m.instalasi_nama, instalasi_asal.instalasi_nama) AS instalasi_nama,
            COALESCE(pasienpulang_t.ruanganakhir_id, (pendaftaran_t.ruangan_id)::bigint) AS ruanganakhir_id,
            COALESCE(ruangan_m.ruangan_nama, ruangan_asal.ruangan_nama) AS ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            NULL::double precision AS total_tindakan,
            obatalkespasien_t.hargajual_oa AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            bpjs_t.nosep AS no_sep,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_pulang,
            'd'::text AS tipe
            FROM ((((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT d.pendaftaran_id,
            d.obatsudahbayar_id,
            sum(d.hargajual_oa) AS hargajual_oa
            FROM obatalkespasien_t d
            WHERE (d.is_deleted = false)
            GROUP BY d.pendaftaran_id, d.obatsudahbayar_id) obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
            LEFT JOIN ( SELECT d.pasienadmisi_id,
            d.pasienpulang_id,
            d.ruangan_id,
            d.carabayar_id,
            d.penjamin_id,
            d.pegawai_id,
            d.bpjs_id,
            d.status_ranap
            FROM pasienadmisi_t d) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT d.pasienpulang_id,
            d.tglpasienpulang,
            d.ruanganakhir_id,
            d.carakeluar_id
            FROM pasienpulang_t d) pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id <> 5))))
            LEFT JOIN ( SELECT d.ruangan_id,
            d.ruangan_nama,
            d.instalasi_id
            FROM ruangan_m d) ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT d.ruangan_id,
            d.ruangan_nama,
            d.instalasi_id
            FROM ruangan_m d) ruangan_asal ON ((pasienadmisi_t.ruangan_id = ruangan_asal.ruangan_id)))
            LEFT JOIN ( SELECT d.instalasi_id,
            d.instalasi_nama
            FROM instalasi_m d) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT d.instalasi_id,
            d.instalasi_nama
            FROM instalasi_m d) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            JOIN ( SELECT d.pasien_id,
            d.nama_pasien,
            d.tanggal_lahir,
            d.photopasien,
            d.no_rekam_medik,
            d.jeniskelamin
            FROM pasien_m d) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT d.carabayar_id,
            d.carabayar_nama
            FROM carabayar_m d) carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT d.penjamin_id,
            d.penjamin_nama
            FROM penjamin_m d) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT d.jeniskasuspenyakit_id,
            d.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m d) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN ( SELECT d.kelaspelayanan_id,
            d.kelaspelayanan_nama
            FROM kelaspelayanan_m d) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT d.pegawai_id,
            d.nama_pegawai
            FROM pegawai_m d) pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT d.bpjs_id,
            d.nosep
            FROM bpjs_t d) bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
            WHERE ((obatalkespasien_t.obatsudahbayar_id IS NULL) AND (pendaftaran_t.is_stopakomodasi = true))) gabung
            WHERE (gabung.sudah_bayar IS NULL)
            GROUP BY gabung.kelaspelayanan_nama, gabung.nama_pegawai, gabung.status_bayar, gabung.pendaftaran_id, gabung.pasienpulang_id, gabung.tglpasienpulang, gabung.no_pendaftaran, gabung.instalasi_id, gabung.instalasi_nama, gabung.ruanganakhir_id, gabung.ruangan_nama, gabung.no_rekam_medik, gabung.nama_pasien, gabung.carabayar_id, gabung.carabayar_nama, gabung.penjamin_id, gabung.penjamin_nama, gabung.jeniskasuspenyakit_nama, gabung.pegawai_id, gabung.pasienpulangri_id, gabung.photopasien, gabung.tanggal_lahir, gabung.umur, gabung.jeniskelamin, gabung.jenis_kelamin, gabung.tgl_pendaftaran, gabung.is_stopakomodasi, gabung.tgl_stopakomodasi, gabung.no_sep, gabung.status_pulang, gabung.tipe
            ;");
        $this->execute('
            ALTER TABLE public.infotagihanpasienpulang_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211123_034316_migrate_hotfix_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211123_034316_migrate_hotfix_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }
    */
}
