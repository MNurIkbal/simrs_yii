<?php

use yii\db\Migration;

/**
 * Class m200505_082047_migrate_20200504
 */
class m200505_082047_migrate_20200504 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konsulpoli_t" ADD COLUMN "status_konsul" int2 DEFAULT 670;');

        $this->execute('ALTER TABLE "public"."pemusnahanobat_t" ADD COLUMN "is_verifikasi" bool DEFAULT false;');

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
    pemusnahanobat_t.is_verifikasi
   FROM (((((pemusnahanobat_t
     LEFT JOIN ruangan_m ON ((pemusnahanobat_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m pegawai_mengetahui ON ((pemusnahanobat_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id)))
     LEFT JOIN pegawai_m pegawai_menyetujui ON ((pemusnahanobat_t.pegawaimenyetujui_id = pegawai_menyetujui.pegawai_id)))
     LEFT JOIN pegawai_m pegawai ON ((pemusnahanobat_t.pegawai_id = pegawai.pegawai_id)))
  WHERE ((pemusnahanobat_t.is_active = true) AND (pemusnahanobat_t.is_deleted = false));");

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
    fgetnamalookup((konsulpoli_t.status_konsul)::integer) AS status_konsul
   FROM ((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
  WHERE ((konsulpoli_t.is_active = true) AND (konsulpoli_t.is_deleted = false));");

        
        

        $this->execute("
            CREATE VIEW \"public\".\"infoobatpemusnahan_v\" AS  SELECT hit.obatalkes_id,
    sum((hit.qtystok_in - hit.qtystok_out)) AS stok,
        CASE
            WHEN (pemusnahan.is_verifikasi IS FALSE) THEN sum((hit.qtystok_in - pemusnahan.jumlah))
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
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200505_082047_migrate_20200504 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200505_082047_migrate_20200504 cannot be reverted.\n";

        return false;
    }
    */
}
