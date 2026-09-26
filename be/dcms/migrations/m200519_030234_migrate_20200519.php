<?php

use yii\db\Migration;

/**
 * Class m200519_030234_migrate_20200519
 */
class m200519_030234_migrate_20200519 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
            CREATE VIEW \"public\".\"infoobatpemusnahan_v\" AS  SELECT hit.obatalkes_id,
    sum((hit.qtystok_in - hit.qtystok_out)) AS stok,
        CASE
            WHEN (pemusnahan.is_verifikasi IS FALSE) THEN (sum((hit.qtystok_in - hit.qtystok_out)) - pemusnahan.jumlah)
            ELSE sum((hit.qtystok_in - hit.qtystok_out))
        END AS stok_exp,
    pemusnahan.jumlah,
    pemusnahan.is_verifikasi,
    hit.obatalkes_nama,
    hit.satuankecil_id,
    hit.s_kecil AS satuan_kecil,
    hit.tglkadaluarsa,
    hit.harganetto,
    (hit.harganetto * sum((hit.qtystok_in - hit.qtystok_out))) AS jumlah_harganetto,
    hit.instalasi_nama,
    hit.ruangan_nama,
    NULL::text AS periodestokobat_id,
    NULL::text AS tglperiodeposting_awal,
    NULL::text AS tglperiodeposting_akhir,
    hit.ruangan_id,
    hit.instalasi_id,
    hit.id_stok,
    hit.nobatch,
    hit.margin,
    hit.ppn,
    hit.disc
   FROM (( SELECT
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
            konfigfarmasi_k.hargaygdigunakan
           FROM (((((stokobatalkes_t
             JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN ruangan_m ON ((stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((stokobatalkes_t.satuankecil_id = satuan_kecil.satuanunit_id)))
             JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))) hit
     LEFT JOIN ( SELECT pemusnahanobatdetail_t.obatalkes_id,
            pemusnahanobatdetail_t.tglkadaluarsa,
            sum(pemusnahanobatdetail_t.jumlah) AS jumlah,
            pemusnahanobat_t.is_verifikasi,
            pemusnahanobat_t.ruangan_id
           FROM (pemusnahanobatdetail_t
             JOIN pemusnahanobat_t ON ((pemusnahanobatdetail_t.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id)))
          GROUP BY pemusnahanobatdetail_t.obatalkes_id, pemusnahanobatdetail_t.tglkadaluarsa, pemusnahanobat_t.is_verifikasi, pemusnahanobat_t.ruangan_id) pemusnahan ON (((hit.obatalkes_id = pemusnahan.obatalkes_id) AND (hit.tglkadaluarsa = pemusnahan.tglkadaluarsa) AND (hit.ruangan_id = pemusnahan.ruangan_id))))
  GROUP BY hit.obatalkes_id, hit.obatalkes_nama, hit.satuankecil_id, hit.s_kecil, hit.tglkadaluarsa, hit.harganetto, hit.instalasi_nama, hit.ruangan_nama, NULL::text, hit.ruangan_id, hit.instalasi_id, hit.id_stok, hit.nobatch, hit.margin, hit.ppn, hit.disc, pemusnahan.jumlah, pemusnahan.is_verifikasi;");
         
         $this->execute('DROP VIEW if exists "public"."infoobatexpired_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infoobatexpired_v\" AS  SELECT array_agg(stokobatalkes_t.stokobatalkes_id) AS id_stok,
    stokobatalkes_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    stokobatalkes_t.tglkadaluarsa,
    sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) AS stok,
        CASE
            WHEN (mutasi.status_mutasi = 401) THEN (sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) - COALESCE(mutasi.jumlah, (0)::double precision))
            WHEN (mutasi.status_mutasi = 400) THEN sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))
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
          GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) mutasi ON (((stokobatalkes_t.obatalkes_id = mutasi.obatalkes_id) AND (stokobatalkes_t.tglkadaluarsa = mutasi.tgl_kadaluarsa) AND (stokobatalkes_t.ruangan_id = mutasi.ruangan_id))))
  GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama, stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, stokobatalkes_t.tglkadaluarsa, obatalkes_m.harganetto, mutasi.jumlah, mutasi.status_mutasi
 HAVING (sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) > (0)::double precision);");

         $this->execute('DROP VIEW if exists "public"."inforesepdetail_v";');

         $this->execute("
            CREATE VIEW \"public\".\"inforesepdetail_v\" AS  SELECT 'reseptur'::text AS jenis,
    resepturdetail_t.resepturdetail_id,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS penjualanresep_id,
    resepturdetail_t.reseptur_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasien_id,
    resepturdetail_t.obatalkes_id,
    resepturdetail_t.satuankecil_id,
    resepturdetail_t.racikan_id,
    resepturdetail_t.signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    reseptur_t.noresep,
    reseptur_t.tglreseptur,
    racikan_m.racikan_nama,
    resepturdetail_t.r,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama,
    resepturdetail_t.qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    resepturdetail_t.hargasatuan_reseptur AS hargajual_satuan,
    resepturdetail_t.hargajual_reseptur AS totalharga_jual,
    resepturdetail_t.etiket,
    resepturdetail_t.iter,
    signaobat_m.signa_nama,
    reseptur_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    rotd_t.interaksi,
    rotd_t.duplikasi,
    rotd_t.dosisi AS dosis,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    (((resepturdetail_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((resepturdetail_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    (0)::double precision AS biayaadministrasiresep,
    (0)::double precision AS totalhargajualresep,
    (0)::double precision AS totaltagihanresep,
    NULL::character varying AS nama_pembeli,
    resepturdetail_t.qty_reseptur AS qty_oa
   FROM ((((((((((((resepturdetail_t
     JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     LEFT JOIN rotd_t ON ((resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id)))
     LEFT JOIN pegawai_m ON ((rotd_t.pegawairotd_id = rotd_t.pegawairotd_id)))
     LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
  WHERE ((resepturdetail_t.is_deleted = false) AND (resepturdetail_t.is_active = true))
UNION ALL
 SELECT 'resep'::text AS jenis,
    NULL::integer AS resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkespasien_t.satuankecil_id,
    obatalkespasien_t.racikan_id,
    NULL::integer AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    penjualanresep_t.noresep,
    penjualanresep_t.tglresep AS tglreseptur,
    racikan_m.racikan_nama,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::integer AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    signaobat_m.signa_nama,
    penjualanresep_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    NULL::character varying AS interaksi,
    NULL::character varying AS duplikasi,
    NULL::character varying AS dosis,
    NULL::character varying AS alergi,
    NULL::character varying AS kontradiksi,
    NULL::character varying AS review_note,
    NULL::timestamp without time zone AS wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    obatalkespasien_t.is_deleted,
    obatalkespasien_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data AS additional_reseptur,
    (((obatalkespasien_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((obatalkespasien_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    penjualanresep_t.biayaadministrasi AS biayaadministrasiresep,
    penjualanresep_t.totalhargajual AS totalhargajualresep,
    (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihanresep,
    penjualanresep_t.nama_pembeli,
    obatalkespasien_t.qty_oa
   FROM ((((((((((obatalkespasien_t
     JOIN penjualanresep_t ON (((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id) AND (penjualanresep_t.reseptur_id IS NULL))))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN ruangan_m ruangan_tujuan ON ((penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.is_active = true));
            ");

         $this->execute('DROP VIEW if exists "public"."infopasienoperasidetail_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasidetail_v\" AS  SELECT 'NON_PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    golonganoperasi_m.golonganoperasi_nama,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    tindakanpelayanan_t.tipepaket_id,
    ''::character varying AS tipepaket_nama,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    operasi_m.operasi_id,
    golonganoperasi_m.golonganoperasi_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id,
    jenisoperasi.golonganoperasi_nama AS jenis_operasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
     JOIN operasi_m ON ((permintaankepenunjang_t.operasi_id = operasi_m.operasi_id)))
     LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
     LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
     LEFT JOIN inpostoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id)))
     LEFT JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
     LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
     LEFT JOIN pegawai_m ON ((inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id)))
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    golonganoperasi_m.golonganoperasi_nama,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    operasi_m.operasi_id,
    golonganoperasi_m.golonganoperasi_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id,
    jenisoperasi.golonganoperasi_nama AS jenis_operasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit
   FROM ((((((((((((pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
     JOIN operasi_m ON ((permintaankepenunjang_t.operasi_id = operasi_m.operasi_id)))
     LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
     LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
     LEFT JOIN inpostoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id)))
     LEFT JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
     LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
     LEFT JOIN pegawai_m ON ((inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id)));");

       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200519_030234_migrate_20200519 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200519_030234_migrate_20200519 cannot be reverted.\n";

        return false;
    }
    */
}
