<?php

use yii\db\Migration;

/**
 * Class m190924_025203_optimize_view_11
 */
class m190924_025203_optimize_view_11 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*laporanpasienigd_v_del*/
$this->execute(' DROP VIEW if exists public.laporanpasienigd_v_del;');

/*inpostoperasi1_v*/
$this->execute('DROP VIEW if exists public.inpostoperasi1_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.inpostoperasi1_v AS 
 SELECT inpostoperasi_t.inpostoperasi_id,
    inpostoperasi_t.pasienmasukpenunjang_id,
    timoperasi_t.timoperasi_id,
    timoperasi_t.posisi_tim,
    fgetnamalookup(timoperasi_t.posisi_tim) AS posisi,
    timoperasi_t.pegawai_id,
    pegawai_m.nama_pegawai
   FROM inpostoperasi_t
     JOIN timoperasi_t ON inpostoperasi_t.inpostoperasi_id = timoperasi_t.inpostoperasi_id
     JOIN pegawai_m ON timoperasi_t.pegawai_id = pegawai_m.pegawai_id;
            ");

$this->execute('ALTER TABLE public.inpostoperasi1_v
  OWNER TO postgres;');

/*inpostoperasi2_v*/
$this->execute('DROP VIEW if exists public.inpostoperasi2_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.inpostoperasi2_v AS 
 SELECT pelayanan.inpostoperasi_id,
    pelayanan.pasienmasukpenunjang_id,
    pelayanan.tindakanpelayanan_id,
    pelayanan.daftartindakan_id,
    pelayanan.daftartindakan_nama,
    pelayanan.cyto_tindakan,
    pelayanan.golonganoperasi_nama,
    pelayanan.jenis_luka,
    pelayanan.jenisanastesi_nama
   FROM ( SELECT inpostoperasi_t.inpostoperasi_id,
            inpostoperasi_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.cyto_tindakan,
            golonganoperasi_m.golonganoperasi_nama,
            NULL::character varying AS jenis_luka,
            NULL::character varying AS jenisanastesi_nama
           FROM inpostoperasi_t
             JOIN tindakanpelayanan_t ON inpostoperasi_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
             JOIN operasi_m ON permintaankepenunjang_t.operasi_id = operasi_m.operasi_id
             JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
          WHERE daftartindakan_m.kelompoktindakan_id = 20
        UNION ALL
         SELECT inpostoperasi_t.inpostoperasi_id,
            inpostoperasi_t.pasienmasukpenunjang_id,
            pelayananoperasi_t.pelayananoperasi_id,
            pelayananoperasi_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            pelayananoperasi_t.is_cyto,
            golonganoperasi_m.golonganoperasi_nama,
            fgetnamalookup(pelayananoperasi_t.jenis_luka) AS jenis_luka,
            jenisanastesi_m.jenisanastesi_nama
           FROM inpostoperasi_t
             JOIN pelayananoperasi_t ON inpostoperasi_t.inpostoperasi_id = pelayananoperasi_t.inpostoperasi_id
             JOIN daftartindakan_m ON pelayananoperasi_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN golonganoperasi_m ON pelayananoperasi_t.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
             JOIN jenisanastesi_m ON pelayananoperasi_t.jenisanastesi_id = jenisanastesi_m.jenisanastesi_id) pelayanan
  ORDER BY pelayanan.pasienmasukpenunjang_id;");

$this->execute('ALTER TABLE public.inpostoperasi2_v
  OWNER TO postgres;');

/*jabatan_v*/
$this->execute('DROP VIEW if exists public.jabatan_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.jabatan_v AS 
 SELECT jabatan_m.jabatan_id,
    jabatan_m.kelompokjabatan_id,
    fgetnamalookup(jabatan_m.kelompokjabatan_id) AS kelompok_jabatan,
    jabatan_m.indexing_id,
    jabatan_m.jabatan_nama,
    jabatan_m.jabatan_singkatan,
    jabatan_m.is_active,
    jabatan_m.is_deleted
   FROM jabatan_m;");

$this->execute('ALTER TABLE public.jabatan_v
  OWNER TO postgres;
');

/*jadwalbukapoli_v*/
$this->execute('DROP VIEW if exists public.jadwalbukapoli_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.jadwalbukapoli_v AS 
 SELECT jadwalbukapoli_m.jadwalbukapoli_id,
    jadwalbukapoli_m.ruangan_id,
    ruangan_m.ruangan_nama,
    jadwalbukapoli_m.waktu_pelayanan,
    jadwalbukapoli_m.jam_mulai,
    jadwalbukapoli_m.jam_tutup,
    jadwalbukapoli_m.maxantrian_poli AS maxantiran_poli,
    jadwalbukapoli_m.hari,
    fgetnamalookup(jadwalbukapoli_m.hari) AS hari_nama,
    ruangan_m.instalasi_id,
    jadwalbukapoli_m.is_active,
    shift_m.shift_id,
    shift_m.shift_nama,
    jadwalbukapoli_m.maxantrian_poli,
    jadwalbukapoli_m.kuota_online
   FROM jadwalbukapoli_m
     JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
  WHERE jadwalbukapoli_m.is_deleted IS FALSE;");

$this->execute('ALTER TABLE public.jadwalbukapoli_v
  OWNER TO postgres;');

/*obatalkes_v*/
$this->execute('DROP VIEW if exists public.obatalkes_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.obatalkes_v AS 
 SELECT hit.obatalkes_id,
    hit.obatalkes_nama,
    hit.jenisobatalkes_id,
    hit.jenisobatalkes_nama,
    hit.ven_id,
    hit.ven,
    hit.groupinacbg_id,
    hit.lead_time,
    hit.avg_usage,
    hit.min_order,
    hit.max_order,
    hit.nilai_ro,
    hit.margin,
    hit.ppn,
    hit.disc,
    hit.hn_last::integer AS hn_last,
    hit.a1::integer AS hn_last_margin,
    hit.a2::integer AS hn_last_diskon,
    hit.a3::integer AS hn_last_margin_diskon,
    hit.a4::integer AS hn_last_ppn,
    hit.a5::integer AS hargajual_last,
    hit.hn_min::integer AS hn_min,
    hit.b1::integer AS hn_min_margin,
    hit.b2::integer AS hn_min_diskon,
    hit.b3::integer AS hn_min_margin_diskon,
    hit.b4::integer AS hn_min_ppn,
    hit.b5::integer AS hargajual_min,
    hit.hn_max::integer AS hn_max,
    hit.c1::integer AS hn_max_margin,
    hit.c2::integer AS hn_max_diskon,
    hit.c3::integer AS hn_max_margin_diskon,
    hit.c4::integer AS hn_max_ppn,
    hit.c5::integer AS hargajual_max,
    hit.hn_avg::integer AS hn_avg,
    hit.d1::integer AS hn_avg_margin,
    hit.d2::integer AS hn_avg_diskon,
    hit.d3::integer AS hn_avg_margin_diskon,
    hit.d4::integer AS hn_avg_ppn,
    hit.d5::integer AS hargajual_avg,
    hit.harga_jual::integer AS hargaygdipakai,
    hit.harganetto::integer AS harganetto_ygdipakai,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.hn_max::integer
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.hn_min::integer
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.hn_avg::integer
            ELSE hit.hn_last::integer
        END AS harga_sugesstion,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_max, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_min, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_avg, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
            ELSE
            CASE COALESCE(hit.harganetto, 0::double precision) - COALESCE(hit.hn_last, 0::double precision)
                WHEN 0 THEN 0
                ELSE 1
            END
        END AS selisih,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c1::integer
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b1::integer
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d1::integer
            ELSE hit.a1::integer
        END AS hn_margin,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c2::integer
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b2::integer
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d2::integer
            ELSE hit.a2::integer
        END AS hn_diskon,
        CASE
            WHEN hit.hargaygdigunakan::text = 'MAX'::text THEN hit.c4::integer
            WHEN hit.hargaygdigunakan::text = 'MIN'::text THEN hit.b4::integer
            WHEN hit.hargaygdigunakan::text = 'AVG'::text THEN hit.d4::integer
            ELSE hit.a4::integer
        END AS hn_ppn,
    hit.satuankecil_id,
    hit.satuankecil_nama,
    hit.group_jenisobat
   FROM ( SELECT obatalkes_m.obatalkes_id,
            obatalkes_m.obatalkes_nama,
            obatalkes_m.jenisobatalkes_id,
            jenisobatalkes_m.jenisobatalkes_nama,
            obatalkes_m.ven AS ven_id,
            fgetnamalookup(obatalkes_m.ven) AS ven,
            obatalkes_m.harganetto,
            obatalkes_m.groupinacbg_id,
            obatalkes_m.lead_time,
            obatalkes_m.avg_usage,
            obatalkes_m.min_order,
            obatalkes_m.max_order,
            obatalkes_m.nilai_ro,
            obatalkes_m.hargaterakhir AS hn_last,
            obatalkes_m.hargaminimum AS hn_min,
            obatalkes_m.hargamaksimum AS hn_max,
            obatalkes_m.hargaratarata AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            fgetpersenmargin(obatalkes_m.harganetto) AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision AS a1,
            (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a2,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS a3,
            (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a4,
            obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision - (obatalkes_m.hargaterakhir + obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS a5,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision AS b1,
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b2,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS b3,
            (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b4,
            obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision - (obatalkes_m.hargaminimum + obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS b5,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision AS c1,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c2,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS c3,
            (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c4,
            obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision - (obatalkes_m.hargamaksimum + obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS c5,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision AS d1,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d2,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS d3,
            (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d4,
            obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision - (obatalkes_m.hargaratarata + obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS d5,
            obatalkes_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuankecil_nama,
            obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS harga_jual,
            jenisobatalkes_m.group_jenisobat
           FROM obatalkes_m
             JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false) hit;");

$this->execute('ALTER TABLE public.obatalkes_v
  OWNER TO postgres;');

/*kettempattidur_V*/
$this->execute('DROP VIEW if exists public.kettempattidur_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.kettempattidur_v AS 
 SELECT kettempattidur_m.kettempattidur_id,
    kettempattidur_m.kettempattidur_nama,
    kettempattidur_m.kettempattidur_warna,
    kettempattidur_m.kode_warna,
    kettempattidur_m.rgb,
    kettempattidur_m.is_kosong AS status_kosong,
        CASE
            WHEN kettempattidur_m.is_kosong IS TRUE THEN 'Kosong'::text
            ELSE 'Isi'::text
        END AS is_kosong,
    kettempattidur_m.kamarruangan_jenis AS jenis,
    fgetnamalookup(kettempattidur_m.kamarruangan_jenis) AS jenis_kamar,
        CASE
            WHEN kettempattidur_m.is_active IS TRUE THEN 'Aktif'::text
            ELSE 'Tidak Aktif'::text
        END AS is_active
   FROM kettempattidur_m
  WHERE kettempattidur_m.is_deleted = false;");

$this->execute('ALTER TABLE public.kettempattidur_v
  OWNER TO postgres;
');

/*konfigantrian_v*/
$this->execute('DROP VIEW if exists public.konfigantrian_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.konfigantrian_v AS 
 SELECT konfigantrian_m.konfigantrian_id,
    konfigantrian_m.layarantrian_id,
    konfigantrian_m.jenisantrian_id,
    konfigantrian_m.fungsiantrian_id,
    konfigantrian_m.carabayar_id,
    fgetnamalookup(konfigantrian_m.jenisantrian_id) AS jenis_antrian,
    fgetnamalookup(konfigantrian_m.fungsiantrian_id) AS fungsi_antrian,
    fungsi_antrian.lookup_value,
    carabayar_m.carabayar_nama,
    konfigantrian_m.is_active AS is_default,
    carabayar_m.is_penjamin,
    konfigantrian_m.kode_antrian,
    konfigantrian_m.instalasi_id,
    instalasi_m.instalasi_nama,
    carabayar_m.groupcarabayar_id,
    fgetnamalookup(konfigantrian_m.groupcarabayar_id) AS group_carabayar,
    konfigantrian_m.penomoran_id,
    konfigantrian_m.klasifikasipasien_id,
    klasifikasipasien_m.klasifikasipasien_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    konfigantrian_m.groupcarabayar_id AS group_id
   FROM konfigantrian_m
     LEFT JOIN lookup_m fungsi_antrian ON konfigantrian_m.fungsiantrian_id = fungsi_antrian.lookup_id
     LEFT JOIN carabayar_m ON konfigantrian_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN instalasi_m ON konfigantrian_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ruangan_m ON konfigantrian_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN klasifikasipasien_m ON konfigantrian_m.klasifikasipasien_id = klasifikasipasien_m.klasifikasipasien_id
  WHERE konfigantrian_m.is_deleted = false AND (konfigantrian_m.jenisantrian_id = ANY (ARRAY[176, 177, 178, 179, 312]));
");

$this->execute('ALTER TABLE public.konfigantrian_v
  OWNER TO postgres;');

/*konfigantrianfarmasi_v*/
$this->execute('DROP VIEW if exists public.konfigantrianfarmasi_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.konfigantrianfarmasi_v AS 
 SELECT konfigantrian_m.konfigantrian_id,
    layarantrian_m.layarantrian_id,
    konfigantrian_m.jenisantrian_id,
    konfigantrian_m.fungsiantrian_id,
    konfigantrian_m.carabayar_id,
    layarantrian_m.layarantrian_nama,
    fgetnamalookup(konfigantrian_m.jenisantrian_id) AS jenis_antrian,
    fungsi_antrian.lookup_name AS fungsi_antrian,
    fungsi_antrian.lookup_value,
    carabayar_m.carabayar_nama,
    konfigantrian_m.is_default,
    carabayar_m.is_penjamin,
    konfigantrian_m.kode_antrian,
    konfigantrian_m.instalasi_id,
    instalasi_m.instalasi_nama,
    konfigantrian_m.groupcarabayar_id,
    fgetnamalookup(konfigantrian_m.groupcarabayar_id) AS group_carabayar
   FROM konfigantrian_m
     LEFT JOIN layarantrian_m ON konfigantrian_m.layarantrian_id = layarantrian_m.layarantrian_id
     LEFT JOIN lookup_m fungsi_antrian ON konfigantrian_m.fungsiantrian_id = fungsi_antrian.lookup_id
     LEFT JOIN carabayar_m ON konfigantrian_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN instalasi_m ON konfigantrian_m.instalasi_id = instalasi_m.instalasi_id
  WHERE konfigantrian_m.is_active = true AND konfigantrian_m.is_deleted = false AND konfigantrian_m.jenisantrian_id = 176;
");

$this->execute('ALTER TABLE public.konfigantrianfarmasi_v
  OWNER TO postgres;');

/*infojadwaldokter_v*/
$this->execute('DROP VIEW if exists public.infojadwaldokter_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infojadwaldokter_v AS 
 SELECT jadwaldokter_m.jadwaldokter_id,
    jadwaldokter_m.ruangan_id,
    jadwaldokter_m.instalasi_id,
    jadwaldokter_m.pegawai_id,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai,
    jadwaldokter_m.jadwaldokter_hari,
    concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS \"Waktu\",
    jadwaldokter_m.maximumantrian AS kuota,
    jadwaldokter_m.jadwaldokter_mulai AS waktu_mulai,
    jadwaldokter_m.jadwaldokter_tutup AS waktu_selesai,
    jadwaldoktertambahan_m.kuota_penambahan,
    jadwaldokter_m.maximumantrian::double precision + jadwaldoktertambahan_m.kuota_penambahan::double precision AS total_kuota,
    fgetnamalookup(jadwalbukapoli_m.hari) AS hari,
    jadwalbukapoli_m.hari AS hari_jadwalbuka,
    jadwaldokter_m.kuota_online,
    jadwaldokter_m.jadwaldokter_tgl,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
        CASE
            WHEN jadwalbukapoli_m.shift_id IS NULL THEN 0
            ELSE jadwalbukapoli_m.shift_id
        END AS shift_id,
    shift_m.shift1_id,
    jadwaldokter_m.notifikasi_id,
    notifikasi_m.judul_temp,
    notifikasi_m.notifikasi,
    COALESCE(kuotadokter_r.kuota_tersedia, 0::real) AS kuota_tersedia,
    jadwaldokter_m.is_active,
    kuotadokter_r.kuotadokter_id,
    jadwaldokter_m.jadwalbukapoli_id
   FROM jadwaldokter_m
     JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN jadwaldoktertambahan_m ON jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id
     JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
     LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
     LEFT JOIN notifikasi_m ON jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id
     LEFT JOIN kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online
  WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true;");

$this->execute('ALTER TABLE public.infojadwaldokter_v
  OWNER TO postgres;
');

/*laporanformsobarang_v*/
$this->execute('DROP VIEW if exists public.laporanformsobarang_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.laporanformsobarang_v AS 
 SELECT formsobarang_t.formsobarang_id,
    formsobarang_t.stokopnamebarang_id,
    formsobarang_t.ruangan_id,
    ruangan_m.instalasi_id,
    formsobarang_t.tglformulir,
    formsobarang_t.noformulir,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    stokopnamebarang_t.nostokopname,
    stokopnamebarang_t.tglstokopname,
    stokopnamebarang_t.totalharga_fisik,
    fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer) AS jenis_so,
    stokopnamebarang_t.totalharga_sistem,
    formsobarang_t.total_harganetto,
    min(periodestokbarang_m.tglperiodestok_awal) AS periode_awal,
    max(periodestokbarang_m.tglperiodestok_akhir) AS periode_akhir
   FROM formsobarang_t
     LEFT JOIN stokopnamebarang_t ON stokopnamebarang_t.stokopnamebarang_id = formsobarang_t.stokopnamebarang_id
     JOIN ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN formsobarangdetail_t ON formsobarang_t.formsobarang_id = formsobarangdetail_t.formsobarang_id
     LEFT JOIN periodestokbarang_m ON formsobarangdetail_t.periodestok_id = periodestokbarang_m.periodestokbarang_id
  WHERE formsobarang_t.is_active = true AND formsobarang_t.is_deleted = false
  GROUP BY formsobarang_t.formsobarang_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, stokopnamebarang_t.nostokopname, stokopnamebarang_t.tglstokopname, stokopnamebarang_t.totalharga_fisik, stokopnamebarang_t.totalharga_sistem, (fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer)), formsobarang_t.stokopnamebarang_id, formsobarang_t.ruangan_id, formsobarang_t.tglformulir, formsobarang_t.noformulir, formsobarang_t.total_harganetto, periodestokbarang_m.tglperiodestok_akhir, periodestokbarang_m.tglperiodestok_awal;
");

$this->execute('ALTER TABLE public.laporanformsobarang_v
  OWNER TO postgres;');

/*infopasienlab_v*/
$this->execute('DROP VIEW if exists public.infopasienlab_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.infopasienlab_v AS 
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
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
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
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
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
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
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
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL;
");

$this->execute('ALTER TABLE public.infopasienlab_v
  OWNER TO postgres;
');

/*laporanmalnutrisi_v*/
$this->execute('DROP VIEW if exists public.laporanmalnutrisi_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.laporanmalnutrisi_v AS 
 SELECT data.pendaftaran_id,
    data.pasienadmisi_id,
    data.tgl_pendaftaran,
    data.no_pendaftaran,
    data.nama_pasien,
    data.jenis_kelamin,
    data.pegawai_id,
    data.nama_pegawai,
    data.jeniskasuspenyakit_id,
    data.jeniskasuspenyakit_nama,
    data.ruangan_id,
    data.ruangan_nama,
    data.kamarruangan_id,
    data.kamarruangan_nokamar,
    data.kamartempattidur_id,
    data.no_tempattidur,
    data.tglpasienpulang,
    data.lama_rawat,
    data.k_1,
    data.k_2,
    data.k_3,
    data.k_4,
    data.k_5,
    data.k_6,
    data.no_rekam_medik,
        CASE
            WHEN data.k_1 = 1 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_2 = 1 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_3 = 1 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_4 = 1 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_5 = 1 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_6 = 1 THEN 1
            ELSE 0
        END AS tinggi,
        CASE
            WHEN data.k_1 = 2 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_2 = 2 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_3 = 2 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_4 = 2 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_5 = 2 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_6 = 2 THEN 1
            ELSE 0
        END AS sedang,
        CASE
            WHEN data.k_1 = 3 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_2 = 3 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_3 = 3 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_4 = 3 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_5 = 3 THEN 1
            ELSE 0
        END +
        CASE
            WHEN data.k_6 = 3 THEN 1
            ELSE 0
        END AS rendah
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pasienadmisi_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienadmisi_t.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar,
            pasienadmisi_t.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.lama_rawat,
                CASE
                    WHEN asesmenawalgizi_t.kategori_bb = 45 THEN 1
                    WHEN asesmenawalgizi_t.kategori_bb = 46 THEN 2
                    ELSE 3
                END AS k_1,
                CASE
                    WHEN asesmenawalgizi_t.kategori_fisik = 45 THEN 1
                    WHEN asesmenawalgizi_t.kategori_fisik = 46 THEN 2
                    ELSE 3
                END AS k_2,
                CASE
                    WHEN asesmenawalgizi_t.kategori_hubungan = 45 THEN 1
                    WHEN asesmenawalgizi_t.kategori_hubungan = 46 THEN 2
                    ELSE 3
                END AS k_3,
                CASE
                    WHEN asesmenawalgizi_t.kategori_asupanmkn = 45 THEN 1
                    WHEN asesmenawalgizi_t.kategori_asupanmkn = 46 THEN 2
                    ELSE 3
                END AS k_4,
                CASE
                    WHEN asesmenawalgizi_t.kategori_fungsional = 45 THEN 1
                    WHEN asesmenawalgizi_t.kategori_fungsional = 46 THEN 2
                    ELSE 3
                END AS k_5,
                CASE
                    WHEN asesmenawalgizi_t.kategori_gastrointestinal = 45 THEN 1
                    WHEN asesmenawalgizi_t.kategori_gastrointestinal = 46 THEN 2
                    ELSE 2
                END AS k_6,
            pasien_m.no_rekam_medik
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN asesmenawalgizi_t ON pasienadmisi_t.pasienadmisi_id = asesmenawalgizi_t.pasienadmisi_id) data;
");

$this->execute('ALTER TABLE public.laporanmalnutrisi_v
  OWNER TO postgres;');

/*laporanmalnutrisi_bckp_v*/
$this->execute('DROP VIEW if exists public.laporanmalnutrisi_bckp_v;');
$this->execute("
    CREATE OR REPLACE VIEW public.laporanmalnutrisi_bckp_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pasienadmisi_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    pasienadmisi_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.tglpasienpulang,
    pasienpulang_t.lama_rawat,
        CASE
            WHEN asesmenawalgizi_t.kategori_bb = 45 THEN 'T'::text
            WHEN asesmenawalgizi_t.kategori_bb = 46 THEN 'S'::text
            ELSE 'R'::text
        END AS k_1,
        CASE
            WHEN asesmenawalgizi_t.kategori_fisik = 45 THEN 'T'::text
            WHEN asesmenawalgizi_t.kategori_fisik = 46 THEN 'S'::text
            ELSE 'R'::text
        END AS k_2,
        CASE
            WHEN asesmenawalgizi_t.kategori_hubungan = 45 THEN 'T'::text
            WHEN asesmenawalgizi_t.kategori_hubungan = 46 THEN 'S'::text
            ELSE 'R'::text
        END AS k_3,
        CASE
            WHEN asesmenawalgizi_t.kategori_asupanmkn = 45 THEN 'T'::text
            WHEN asesmenawalgizi_t.kategori_asupanmkn = 46 THEN 'S'::text
            ELSE 'R'::text
        END AS k_4,
        CASE
            WHEN asesmenawalgizi_t.kategori_fungsional = 45 THEN 'T'::text
            WHEN asesmenawalgizi_t.kategori_fungsional = 46 THEN 'S'::text
            ELSE 'R'::text
        END AS k_5,
        CASE
            WHEN asesmenawalgizi_t.kategori_gastrointestinal = 45 THEN 'T'::text
            WHEN asesmenawalgizi_t.kategori_gastrointestinal = 46 THEN 'S'::text
            ELSE 'R'::text
        END AS k_6,
    pasien_m.no_rekam_medik
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN asesmenawalgizi_t ON pasienadmisi_t.pasienadmisi_id = asesmenawalgizi_t.pasienadmisi_id;");

$this->execute('ALTER TABLE public.laporanmalnutrisi_bckp_v
  OWNER TO postgres;
');

/*infopasienrskoreksi_v*/
$this->execute('DROP VIEW if exists public.infopasienrskoreksi_v;');

$this->execute("
    CREATE OR REPLACE VIEW public.infopasienrskoreksi_v AS 
 SELECT gabung.jenis_rawat,
    gabung.pendaftaran_id,
    gabung.pasienadmisi_id,
    gabung.tgl_pendaftaran,
    gabung.no_pendaftaran,
    gabung.pasien_id,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.jenis_kelamin,
    gabung.carabayar_id,
    gabung.carabayar_nama,
    gabung.penjamin_id,
    gabung.penjamin_nama,
    gabung.jeniskasuspenyakit_id,
    gabung.jeniskasuspenyakit_nama,
    gabung.instalasi_id,
    gabung.instalasi_nama,
    gabung.ruangan_id,
    gabung.ruangan_nama,
    gabung.dokter_dpjp_id,
    gabung.dokter_dpjp,
    gabung.status_verifikasi,
    gabung.status_verif,
    gabung.pasienpulang_id,
    gabung.status_periksa,
    gabung.umur,
    gabung.tanggal_lahir,
    gabung.kelaspelayanan_id,
    gabung.kelaspelayanan_nama,
    gabung.photopasien,
    gabung.tglpasienpulang,
    gabung.status_pengajuanklaim,
    gabung.no_identitas_pasien
   FROM ( SELECT 'RJ-RD'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verif,
            pendaftaran_t.pasienpulang_id,
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasien_m.photopasien,
            pasienpulang_t.tglpasienpulang,
            pengajuanklaim_t.status_pengajuanklaim,
            pasien_m.no_identitas_pasien
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN pegawai_m dokter_dpjp ON pendaftaran_t.pegawai_id = dokter_dpjp.pegawai_id
             JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
             LEFT JOIN pengajuanklaim_t ON pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id
          WHERE pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])
        UNION ALL
         SELECT 'RI'::text AS jenis_rawat,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.pegawai_id AS dokter_dpjp_id,
            dokter_dpjp.nama_pegawai AS dokter_dpjp,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS status_verif,
            pasienadmisi_t.pasienpulang_id,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasien_m.photopasien,
            pasienpulang_t.tglpasienpulang,
            pengajuanklaim_t.status_pengajuanklaim,
            pasien_m.no_identitas_pasien
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pegawai_m dokter_dpjp ON pasienadmisi_t.pegawai_id = dokter_dpjp.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN pengajuanklaimdetail_t ON pasienadmisi_t.pasienadmisi_id = pengajuanklaimdetail_t.pasienadmisi_id AND pengajuanklaimdetail_t.is_deleted = false
             LEFT JOIN pengajuanklaim_t ON pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id) gabung
  GROUP BY gabung.jenis_rawat, gabung.pendaftaran_id, gabung.pasienadmisi_id, gabung.tgl_pendaftaran, gabung.no_pendaftaran, gabung.pasien_id, gabung.no_rekam_medik, gabung.nama_pasien, gabung.jenis_kelamin, gabung.carabayar_id, gabung.carabayar_nama, gabung.penjamin_id, gabung.penjamin_nama, gabung.jeniskasuspenyakit_id, gabung.jeniskasuspenyakit_nama, gabung.instalasi_id, gabung.instalasi_nama, gabung.ruangan_id, gabung.ruangan_nama, gabung.dokter_dpjp_id, gabung.dokter_dpjp, gabung.status_verifikasi, gabung.status_verif, gabung.pasienpulang_id, gabung.status_periksa, gabung.umur, gabung.tanggal_lahir, gabung.kelaspelayanan_id, gabung.kelaspelayanan_nama, gabung.photopasien, gabung.tglpasienpulang, gabung.status_pengajuanklaim, gabung.no_identitas_pasien;
");

$this->execute('ALTER TABLE public.infopasienrskoreksi_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190924_025203_optimize_view_11 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190924_025203_optimize_view_11 cannot be reverted.\n";

        return false;
    }
    */
}
