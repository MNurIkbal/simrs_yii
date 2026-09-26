<?php

use yii\db\Migration;

/**
 * Class m200602_014320_migrate_20200602
 */
class m200602_014320_migrate_20200602 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konsulpoli_t" ADD COLUMN "status_approve" int2 DEFAULT 564;');

        $this->execute('ALTER TABLE "public"."konsultasitindakan_t" ALTER COLUMN "qty_tindakan" SET DEFAULT 1;');

        $this->execute('DROP VIEW if exists "public"."tindakanoperasi_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"tindakanoperasi_v\" AS  SELECT tindakanoperasi_mp.timoperasi_id,
    lookup_m.lookup_name AS timoperasi_nama,
    tindakanoperasi_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    lookup_m.lookup_value AS persentase,
    tindakanoperasi_mp.is_active,
    tindakanoperasi_mp.created_date
   FROM ((tindakanoperasi_mp
     JOIN daftartindakan_m ON ((tindakanoperasi_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN lookup_m ON ((tindakanoperasi_mp.timoperasi_id = lookup_m.lookup_id)));");

        $this->execute('DROP VIEW if exists "public"."infoobatalkesexpired_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoobatalkesexpired_v\" AS  SELECT hit.obatalkes_id,
    sum((hit.qtystok_in - hit.qtystok_out)) AS stok,
        CASE
            WHEN (mutasi.status_mutasi = 401) THEN sum((hit.qtystok_in - mutasi.jumlah))
            ELSE sum((hit.qtystok_in - hit.qtystok_out))
        END AS stok_exp,
    mutasi.jumlah,
    mutasi.status_mutasi,
    hit.obatalkes_nama,
    hit.satuankecil_id,
    hit.s_kecil AS satuan_kecil,
    hit.tglkadaluarsa,
    hit.harganetto,
    sum(harga_netto.harga_netto) AS jumlah_harganetto,
    hit.instalasi_nama,
    hit.ruangan_nama,
    hit.periodestokobat_id,
    hit.tglperiodestok_awal AS tglperiodeposting_awal,
    hit.tglperiodestok_akhir AS tglperiodeposting_akhir,
    hit.ruangan_id,
    hit.instalasi_id,
    array_agg(hit.id_stok) AS id_stok,
    hit.nobatch,
    hit.margin,
    hit.ppn,
    hit.disc,
    hit.hn_last,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.hn_min,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.hn_max,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.hn_avg,
    hit.d1 AS hn_avg_margin,
    hit.d2 AS hn_avg_diskon,
    hit.d3 AS hn_avg_margin_diskon,
    hit.d4 AS hn_avg_ppn,
    hit.d5 AS hargajual_avg,
    0 AS hargaygdipakai,
    0 AS harganetto_ygdipakai,
    0 AS hn_margin,
    0 AS hn_diskon,
    0 AS hn_ppn
   FROM ((( SELECT
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            stokobatalkes_t.satuankecil_id,
            satuan_kecil.satuanunit_nama AS s_kecil,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            NULL::text AS periodestokobat_id,
            NULL::text AS tglperiodestok_awal,
            NULL::text AS tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            stokobatalkes_t.nobatch,
            obatalkes_m.harganetto,
            obatalkes_m.hargaterakhir AS hn_last,
            obatalkes_m.hargaminimum AS hn_min,
            obatalkes_m.hargamaksimum AS hn_max,
            obatalkes_m.hargaratarata AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            konfigfarmasi_k.persenmargin AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            NULL::text AS a1,
            NULL::text AS a2,
            NULL::text AS a3,
            NULL::text AS a4,
            NULL::text AS a5,
            NULL::text AS b1,
            NULL::text AS b2,
            NULL::text AS b3,
            NULL::text AS b4,
            NULL::text AS b5,
            NULL::text AS c1,
            NULL::text AS c2,
            NULL::text AS c3,
            NULL::text AS c4,
            NULL::text AS c5,
            NULL::text AS d1,
            NULL::text AS d2,
            NULL::text AS d3,
            NULL::text AS d4,
            NULL::text AS d5
           FROM (((((stokobatalkes_t
             JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN ruangan_m ON ((stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((stokobatalkes_t.satuankecil_id = satuan_kecil.satuanunit_id)))
             JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))) hit
     LEFT JOIN ( SELECT mutasiobatdetail_t.obatalkes_id,
            mutasiobatdetail_t.tgl_kadaluarsa,
            sum(mutasiobatdetail_t.jumlah_mutasi) AS jumlah,
            mutasiobatruangan_t.status_mutasi,
            mutasiobatruangan_t.ruanganasal_id AS ruangan_id
           FROM (mutasiobatdetail_t
             JOIN mutasiobatruangan_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
          GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) mutasi ON (((hit.obatalkes_id = mutasi.obatalkes_id) AND (hit.tglkadaluarsa = mutasi.tgl_kadaluarsa) AND (hit.ruangan_id = mutasi.ruangan_id))))
     LEFT JOIN ( SELECT
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.ruangan_id,
            sum((obatalkes_m.harganetto * (stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))) AS harga_netto
           FROM (stokobatalkes_t
             JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
          GROUP BY
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END, stokobatalkes_t.obatalkes_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id) harga_netto ON (((hit.obatalkes_id = harga_netto.obatalkes_id) AND (hit.tglkadaluarsa = harga_netto.tglkadaluarsa) AND (hit.ruangan_id = harga_netto.ruangan_id))))
  GROUP BY hit.obatalkes_id, mutasi.jumlah, mutasi.status_mutasi, hit.obatalkes_nama, hit.satuankecil_id, hit.s_kecil, hit.tglkadaluarsa, hit.harganetto, hit.instalasi_nama, hit.ruangan_nama, hit.periodestokobat_id, hit.tglperiodestok_awal, hit.tglperiodestok_akhir, hit.ruangan_id, hit.instalasi_id, hit.nobatch, hit.margin, hit.ppn, hit.disc, hit.hn_last, hit.a1, hit.a2, hit.a3, hit.a4, hit.a5, hit.hn_min, hit.b1, hit.b2, hit.b3, hit.b4, hit.b5, hit.hn_max, hit.c1, hit.c2, hit.c3, hit.c4, hit.c5, hit.hn_avg, hit.d1, hit.d2, hit.d3, hit.d4, hit.d5, 0::integer, 0::integer, 0::integer, 0::integer, 0::integer;");

        $this->execute('DROP VIEW if exists "public"."infoobatpemusnahan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoobatpemusnahan_v\" AS  SELECT array_agg(stokobatalkes_t.stokobatalkes_id) AS id_stok,
    stokobatalkes_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    stokobatalkes_t.tglkadaluarsa,
    sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) AS stok,
        CASE
            WHEN (pemusnahan.is_verifikasi = false) THEN (sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) - COALESCE(pemusnahan.jumlah, (0)::double precision))
            ELSE sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))
        END AS stok_exp,
    COALESCE(pemusnahan.jumlah, (0)::double precision) AS jumlah,
    pemusnahan.is_verifikasi,
    obatalkes_m.harganetto,
    sum((obatalkes_m.harganetto * (stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))) AS jumlah_harganetto,
    obatalkes_m.satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    stokobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    NULL::text AS periodestokobat_id,
    NULL::text AS tglperiodeposting_awal,
    NULL::text AS tglperiodeposting_akhir,
    NULL::text AS nobatch,
    NULL::text AS margin,
    NULL::text AS ppn,
    NULL::text AS disc
   FROM (((((stokobatalkes_t
     JOIN obatalkes_m ON ((obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id)))
     JOIN ruangan_m ON ((ruangan_m.ruangan_id = stokobatalkes_t.ruangan_id)))
     JOIN instalasi_m ON ((instalasi_m.instalasi_id = ruangan_m.instalasi_id)))
     LEFT JOIN satuanunit_m ON ((satuanunit_m.satuanunit_id = stokobatalkes_t.satuankecil_id)))
     LEFT JOIN ( SELECT pemusnahanobatdetail_t.obatalkes_id,
            pemusnahanobatdetail_t.tglkadaluarsa,
            sum(pemusnahanobatdetail_t.jumlah) AS jumlah,
            pemusnahanobat_t.is_verifikasi,
            pemusnahanobat_t.ruangan_id
           FROM (pemusnahanobatdetail_t
             JOIN pemusnahanobat_t ON ((pemusnahanobatdetail_t.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id)))
          WHERE ((pemusnahanobat_t.is_verifikasi = false) AND (pemusnahanobat_t.is_deleted = false))
          GROUP BY pemusnahanobatdetail_t.obatalkes_id, pemusnahanobatdetail_t.tglkadaluarsa, pemusnahanobat_t.is_verifikasi, pemusnahanobat_t.ruangan_id) pemusnahan ON (((stokobatalkes_t.obatalkes_id = pemusnahan.obatalkes_id) AND (stokobatalkes_t.tglkadaluarsa = pemusnahan.tglkadaluarsa) AND (stokobatalkes_t.ruangan_id = pemusnahan.ruangan_id))))
  GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama, stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, stokobatalkes_t.tglkadaluarsa, obatalkes_m.harganetto, pemusnahan.jumlah, pemusnahan.is_verifikasi
 HAVING (sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) > (0)::double precision);");

        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
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
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    (cppt_t.a_diag_utama ->> 'text'::text) AS a_diag_utama
   FROM ((((((((((((((((pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true) AND (cppt_t.is_instruksi_pulang = false))))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
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
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama
   FROM (((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.instalasi_id = 12))
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
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
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ruangan_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (ruangan_penunjang.instalasi_id = 12) AND (pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (pendaftaran_t.instalasi_id <> 12));");

        $this->execute('DROP VIEW if exists "public"."infoobatexpired_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoobatexpired_v\" AS  SELECT array_agg(stokobatalkes_t.stokobatalkes_id) AS id_stok,
    stokobatalkes_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    stokobatalkes_t.tglkadaluarsa,
    sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) AS stok,
        CASE
            WHEN (mutasi.status_mutasi = 401) THEN (sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) - COALESCE(mutasi.jumlah, (0)::double precision))
            ELSE sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))
        END AS stok_exp,
    COALESCE(mutasi.jumlah, (0)::double precision) AS jumlah,
    mutasi.status_mutasi,
    obatalkes_m.harganetto,
    sum((obatalkes_m.harganetto * (stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))) AS jumlah_harganetto,
    obatalkes_m.satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    stokobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama
   FROM (((((stokobatalkes_t
     JOIN obatalkes_m ON ((obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id)))
     JOIN ruangan_m ON ((ruangan_m.ruangan_id = stokobatalkes_t.ruangan_id)))
     JOIN instalasi_m ON ((instalasi_m.instalasi_id = ruangan_m.instalasi_id)))
     LEFT JOIN satuanunit_m ON ((satuanunit_m.satuanunit_id = stokobatalkes_t.satuankecil_id)))
     LEFT JOIN ( SELECT mutasiobatdetail_t.obatalkes_id,
            mutasiobatdetail_t.tgl_kadaluarsa,
            sum(mutasiobatdetail_t.jumlah_mutasi) AS jumlah,
            mutasiobatruangan_t.status_mutasi,
            mutasiobatruangan_t.ruanganasal_id AS ruangan_id
           FROM (mutasiobatdetail_t
             JOIN mutasiobatruangan_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
          WHERE (mutasiobatruangan_t.status_mutasi = 401)
          GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) mutasi ON (((stokobatalkes_t.obatalkes_id = mutasi.obatalkes_id) AND (stokobatalkes_t.tglkadaluarsa = mutasi.tgl_kadaluarsa) AND (stokobatalkes_t.ruangan_id = mutasi.ruangan_id))))
  GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama, stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, stokobatalkes_t.tglkadaluarsa, obatalkes_m.harganetto, mutasi.jumlah, mutasi.status_mutasi
 HAVING (sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) > (0)::double precision);");

        $this->execute('DROP VIEW if exists "public"."infopemusnahanobat_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infopemusnahanobat_v\" AS  SELECT pemusnahanobat_t.pemusnahanobat_id,
    pemusnahanobat_t.tglpemusnahan,
    pemusnahanobat_t.nopemusnahan,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    pegawai_menyetujui.nama_pegawai AS pegawai_menyetujui,
    pemusnahanobat_t.total_harganetto,
    pemusnahanobat_t.pegawaimengetahui_id,
    pemusnahanobat_t.pegawaimenyetujui_id,
    pegawai.nama_pegawai,
    pemusnahanobat_t.is_verifikasi,
        CASE
            WHEN (pemusnahanobat_t.is_deleted = true) THEN 'Batal'::text
            WHEN (pemusnahanobat_t.is_verifikasi = true) THEN 'Sudah Verifikasi'::text
            WHEN (pemusnahanobat_t.is_verifikasi = false) THEN 'Belum Verifikasi'::text
            ELSE NULL::text
        END AS status_pemusnahan,
    pemusnahanobat_t.is_deleted
   FROM (((((pemusnahanobat_t
     LEFT JOIN ruangan_m ON ((pemusnahanobat_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m pegawai_mengetahui ON ((pemusnahanobat_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id)))
     LEFT JOIN pegawai_m pegawai_menyetujui ON ((pemusnahanobat_t.pegawaimenyetujui_id = pegawai_menyetujui.pegawai_id)))
     LEFT JOIN pegawai_m pegawai ON ((pemusnahanobat_t.pegawai_id = pegawai.pegawai_id)))
  WHERE (pemusnahanobat_t.is_active = true);");
        
        $this->execute('DROP VIEW if exists "public"."infopasienpenunjang_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infopasienpenunjang_v\" AS  SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    ruangan_m.ruangan_nama AS ruangan_penunjang,
    ruangasal.ruangan_nama AS ruangan_asal,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienmasukpenunjang_t.status_periksa,
    ruangan_m.instalasi_id,
    pendaftaran_t.created_by,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.no_masukpenunjang AS no_pendaftaran,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pegawai_m.nama_pegawai,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruangasal ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id)))
     JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
  WHERE ((pasienmasukpenunjang_t.is_active = true) AND (pasienmasukpenunjang_t.is_deleted = false));");
        
        $this->execute('DROP VIEW if exists "public"."infokonsulpoli_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokonsulpoli_v\" AS  SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    konsulpoli_t.tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.jawaban_konsul,
    pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
    dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
    konsulpoli_t.status_konsul AS status_konsul_id,
    fgetnamalookup((konsulpoli_t.status_konsul)::integer) AS status_konsul,
    konsulpoli_t.status_approve AS status_approve_id,
    fgetnamalookup((konsulpoli_t.status_approve)::integer) AS status_approve
   FROM ((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
  WHERE ((konsulpoli_t.is_active = true) AND (konsulpoli_t.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."infokunjunganrj_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrj_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.kunjungan,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    pendaftaran_t.status_periksa,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa1,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep
   FROM ((((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE (pendaftaran_t.instalasi_id = 1)
UNION
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.tgl_konsulpoli AS tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.kunjungan,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    konsulpoli_t.status_periksa,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    konsulpoli_t.konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status_periksa1,
    konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
    ruanganasal_m.ruangan_nama AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep
   FROM ((((((((((((((((((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruanganasal_m ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565));");
        
        $this->execute('DROP VIEW if exists "public"."infopemusnahanobatdetail_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infopemusnahanobatdetail_v\" AS  SELECT pemusnahanobatdetail_t.pemusnahanobat_id,
    pemusnahanobat_t.nopemusnahan,
    pemusnahanobat_t.tglpemusnahan,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    pegawai_menyetujui.nama_pegawai AS pegawai_menyetujui,
    pegawai_pelaksana.nama_pegawai AS pegawai_pelaksana,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    pemusnahanobatdetail_t.tglkadaluarsa,
    pemusnahanobatdetail_t.jumlah AS stok,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    pemusnahanobatdetail_t.harganetto AS jumlah_harganetto,
    pemusnahanobatdetail_t.is_deleted
   FROM ((((((((pemusnahanobatdetail_t
     JOIN pemusnahanobat_t ON ((pemusnahanobatdetail_t.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id)))
     JOIN obatalkes_m ON ((pemusnahanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN ruangan_m ON ((pemusnahanobat_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m pegawai_mengetahui ON ((pemusnahanobat_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id)))
     LEFT JOIN pegawai_m pegawai_menyetujui ON ((pemusnahanobat_t.pegawaimenyetujui_id = pegawai_menyetujui.pegawai_id)))
     LEFT JOIN pegawai_m pegawai_pelaksana ON ((pemusnahanobat_t.pegawai_id = pegawai_pelaksana.pegawai_id)))
  WHERE (pemusnahanobatdetail_t.is_active = true);");
        
        $this->execute('DROP VIEW if exists "public"."detailpemesananobatalkes_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"detailpemesananobatalkes_v\" AS  SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.keterangan_pesan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama AS obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    pesanobatdetail_t.jumlah_pesan,
    COALESCE(qty_diterima.qty_diterima, (0)::double precision) AS jumlah_diterima,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatdetail_t.mutasiobatdetail_id,
    pesanobatdetail_t.satuankecil_id,
    pesanobatdetail_t.pesanobatdetail_id,
    pesanobatdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    pesanobatdetail_t.jumlah_input AS qty_besar,
    obatalkes_m.harganetto,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.discount,
    pesanobatdetail_t.satuan_pemesanan,
    sat_pemesanan.satuanunit_nama AS satuan_pemesanan_nama,
    pesanobatdetail_t.jumlah_pesan AS qty_kecil,
    pesanobatalkes_t.statuspesan,
    COALESCE(stok_pengirim.qty_tersedia, (0)::double precision) AS stok_pengirim,
    COALESCE(stok_pemesan.qty_tersedia, (0)::double precision) AS stok_pemesan,
    mutasi.jumlah_mutasi,
    mutasi.satuan_kirim,
    COALESCE(mutasi.jumlah_input, (0)::double precision) AS jumlah_input_mutasi
   FROM (((((((((((((pesanobatdetail_t
     JOIN pesanobatalkes_t ON ((pesanobatdetail_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id)))
     JOIN ruangan_m ON ((pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruangpemesan ON ((pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id)))
     JOIN instalasi_m instalasipesan ON ((ruangpemesan.instalasi_id = instalasipesan.instalasi_id)))
     JOIN obatalkes_m ON ((pesanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_besar ON ((pesanobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((pesanobatdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_pemesanan ON ((pesanobatdetail_t.satuan_pemesanan = sat_pemesanan.satuanunit_id)))
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_tersedia
           FROM stokobatalkes_r
          WHERE (stokobatalkes_r.is_deleted = false)) stok_pengirim ON (((pesanobatalkes_t.ruangan_id = stok_pengirim.ruangan_id) AND (pesanobatdetail_t.obatalkes_id = stok_pengirim.obatalkes_id))))
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_tersedia
           FROM stokobatalkes_r
          WHERE (stokobatalkes_r.is_deleted = false)) stok_pemesan ON (((pesanobatalkes_t.ruanganpemesan_id = stok_pemesan.ruangan_id) AND (pesanobatdetail_t.obatalkes_id = stok_pemesan.obatalkes_id))))
     LEFT JOIN ( SELECT mutasiobatdetail_t.pesanobatdetail_id,
            terimamutasiobatdetail_t.jmlterima AS qty_diterima
           FROM ((terimamutasiobatdetail_t
             JOIN mutasiobatdetail_t ON ((terimamutasiobatdetail_t.mutasiobatdetail_id = mutasiobatdetail_t.mutasiobatdetail_id)))
             JOIN mutasiobatruangan_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
          WHERE (mutasiobatruangan_t.status_mutasi = 400)) qty_diterima ON ((pesanobatdetail_t.pesanobatdetail_id = qty_diterima.pesanobatdetail_id)))
     LEFT JOIN ( SELECT mutasiobatdetail_t.pesanobatdetail_id,
            mutasiobatdetail_t.jumlah_input,
            mutasiobatdetail_t.jumlah_mutasi,
            mutasiobatdetail_t.satuankecil_id,
            satuanunit_m.satuanunit_nama AS satuan_kirim
           FROM (mutasiobatdetail_t
             LEFT JOIN satuanunit_m ON ((mutasiobatdetail_t.satuankecil_id = satuanunit_m.satuanunit_id)))) mutasi ON ((pesanobatdetail_t.pesanobatdetail_id = mutasi.pesanobatdetail_id)))
  WHERE ((pesanobatdetail_t.is_active = true) AND (pesanobatdetail_t.is_deleted = false));");
        
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200602_014320_migrate_20200602 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200602_014320_migrate_20200602 cannot be reverted.\n";

        return false;
    }
    */
}
