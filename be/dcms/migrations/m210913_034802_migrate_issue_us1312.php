<?php

use yii\db\Migration;

/**
 * Class m210913_034802_migrate_issue_us1312
 */
class m210913_034802_migrate_issue_us1312 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infostokobatrakdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokobatrakdetail_v\" AS  SELECT x.obatalkes_id,
    x.stok_sistem,
    x.obatalkes_nama,
    x.harganetto,
    x.instalasi_nama,
    x.ruangan_nama,
    x.ruangan_id,
    x.instalasi_id,
    x.sop_obatalkes_id,
    x.rakobat_nama,
    x.rakobat_id,
    x.laciobat_id,
    x.obatalkes_kode,
    x.is_consigment,
    x.laci,
    x.jenisobatalkes_nama
   FROM ( SELECT proses.obatalkes_id,
            sum(proses.qtystok_in - proses.qtystok_out) AS stok_sistem,
            proses.obatalkes_nama,
            proses.harganetto,
            proses.instalasi_nama,
            proses.ruangan_nama,
            proses.ruangan_id,
            proses.instalasi_id,
            proses.sop_obatalkes_id,
            proses.rakobat_nama,
            proses.rakobat_id,
            proses.laciobat_id,
            proses.obatalkes_kode,
            proses.is_consigment,
            proses.laci,
            proses.jenisobatalkes_nama
           FROM ( SELECT
                        CASE
                            WHEN stokobatalkes_t.stokobatalkesasal_id IS NULL THEN stokobatalkes_t.stokobatalkes_id
                            ELSE stokobatalkes_t.stokobatalkesasal_id
                        END AS id_stok,
                    stokobatalkes_t.obatalkes_id,
                    stokobatalkes_t.qtystok_in,
                    stokobatalkes_t.qtystok_out,
                    stokobatalkes_t.tglkadaluarsa,
                    obatalkes_m.obatalkes_nama,
                    obatalkes_m.harganetto AS harganetto2,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_nama,
                    stokobatalkes_r.periodestokobat_id,
                    ruangan_m.ruangan_id,
                    instalasi_m.instalasi_id,
                    formstokopname_t.obatalkes_id AS sop_obatalkes_id,
                    formstokopname_t.stokopnamedetail_id AS sop_stokopnamedetail_id,
                    konfigfarmasi_k.hargaygdigunakan,
                    obatalkes_m.hargamaksimum,
                    obatalkes_m.hargaminimum,
                    obatalkes_m.hargaratarata,
                        CASE
                            WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MAX'::text THEN obatalkes_m.hargamaksimum
                            WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MIN'::text THEN obatalkes_m.hargaminimum
                            WHEN konfigfarmasi_k.hargaygdigunakan::text = 'AVG'::text THEN obatalkes_m.hargaratarata
                            ELSE obatalkes_m.harganetto
                        END AS harganetto,
                    COALESCE(laci.rak_id::bigint, rak.rakobat_id) AS rakobat_id,
                    COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
                    COALESCE(laci.rakobat_id, rak.rakobat_id) AS laciobat_id,
                    COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
                    obatalkes_m.obatalkes_kode,
                    obatalkes_m.is_consigment,
                    jenisobatalkes_m.jenisobatalkes_nama
                   FROM stokobatalkes_t
                     JOIN ( SELECT a.obatalkes_id,
                            a.obatalkes_nama,
                            a.harganetto,
                            a.hargamaksimum,
                            a.hargaminimum,
                            a.hargaratarata,
                            a.obatalkes_kode,
                            a.is_consigment,
                            a.jenisobatalkes_id
                           FROM obatalkes_m a) obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
                     LEFT JOIN ( SELECT a.jenisobatalkes_id,
                            a.jenisobatalkes_nama
                           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.formstokopname_id,
                            a.obatalkes_id,
                            a.is_deleted,
                            a.ruangan_id,
                            a.stokopnamedetail_id
                           FROM formstokopname_t a
                          WHERE a.stokopnamedetail_id IS NULL AND a.is_deleted = false) formstokopname_t ON stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id
                     JOIN ( SELECT a.obatalkes_id,
                            a.ruangan_id,
                            a.is_periode,
                            a.stokobatr_id,
                            a.periodestokobat_id
                           FROM stokobatalkes_r a) stokobatalkes_r ON stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id AND stokobatalkes_r.is_periode = true
                     LEFT JOIN ( SELECT a.stokobatr_id,
                            a.rakobat_id
                           FROM konfigrak_m a) konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id
                     LEFT JOIN ( SELECT a.rakobat_id,
                            a.rakobat_nama
                           FROM rakobat_m a) rak ON konfigrak_m.rakobat_id = rak.rakobat_id
                     LEFT JOIN ( SELECT a.rakobat_id,
                            a.parentrakobat_id AS rak_id,
                            rak_1.rakobat_nama AS rak,
                            a.rakobat_nama
                           FROM rakobat_m a
                             JOIN rakobat_m rak_1 ON a.parentrakobat_id = rak_1.rakobat_id
                          WHERE a.parentrakobat_id IS NOT NULL) laci ON konfigrak_m.rakobat_id = laci.rakobat_id
                     JOIN ( SELECT a.is_deleted,
                            a.hargaygdigunakan
                           FROM konfigfarmasi_k a) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                  WHERE formstokopname_t.formstokopname_id IS NULL) proses
          GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.ruangan_id, proses.instalasi_id, proses.sop_obatalkes_id, proses.rakobat_nama, proses.rakobat_id, proses.laciobat_id, proses.obatalkes_kode, proses.is_consigment, proses.laci, proses.jenisobatalkes_nama) x
  WHERE x.stok_sistem > 0::double precision;
");
        $this->execute('DROP VIEW if exists "public"."detailformulirstokopname_v";');

        $this->execute("
            CREATE VIEW \"public\".\"detailformulirstokopname_v\" AS  SELECT formstokopname_t.formstokopname_id,
    formstokopname_t.formulirstokopname_id,
    formstokopname_t.volume_stok AS stok_sistem,
    formstokopname_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    formstokopname_t.nobatch,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN formstokopname_t.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
    obatalkes_m.harganetto,
    formstokopname_t.stokobatalkes_id,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama,
    obatalkes_m.obatalkes_kode,
    COALESCE(laci.rak_id::bigint, rak.rakobat_id) AS rakobat_id,
    COALESCE(laci.rak, rak.rakobat_nama) AS rak,
    COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
    COALESCE(laci.rakobat_id, rak.rakobat_id) AS laciobat_id,
    COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    stokobatalkes_r.qty_sisa AS stok_saatini,
    stokobatalkes.stok_in,
    stokobatalkes.stok_out,
    jenisobatalkes_m.jenisobatalkes_nama
   FROM formstokopname_t
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.obatalkes_namalain,
            a.obatalkes_kode,
            a.harganetto,
            a.satuankecil_id,
            a.satuanbesar_id,
            a.jenisobatalkes_id
           FROM obatalkes_m a) obatalkes_m ON formstokopname_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.stokobatalkes_id,
            a.tglkadaluarsa
           FROM stokobatalkes_t a) stokobatalkes_t ON formstokopname_t.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            a.qty_sisa
           FROM stokobatalkes_r a) stokobatalkes_r ON formstokopname_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND formstokopname_t.ruangan_id = stokobatalkes_r.ruangan_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            a.rakobat_id
           FROM konfigrak_m a) konfigrak_m ON formstokopname_t.ruangan_id = konfigrak_m.ruangan_id AND formstokopname_t.obatalkes_id = konfigrak_m.obatalkes_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.rakobat_nama
           FROM rakobat_m a) rak ON konfigrak_m.rakobat_id = rak.rakobat_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.parentrakobat_id AS rak_id,
            rak_1.rakobat_nama AS rak,
            a.rakobat_nama
           FROM rakobat_m a
             JOIN rakobat_m rak_1 ON a.parentrakobat_id = rak_1.rakobat_id
          WHERE a.parentrakobat_id IS NOT NULL) laci ON konfigrak_m.rakobat_id = laci.rakobat_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a2.satuanunit_id,
                    a2.satuanunit_nama
                   FROM satuanunit_m a2) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true
          GROUP BY a.obatalkes_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom ON obatalkes_m.obatalkes_id = uom.obatalkes_id AND obatalkes_m.satuanbesar_id = uom.satuanbesar_id AND obatalkes_m.satuankecil_id = uom.satuankecil_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            sum(a.qtystok_in) AS stok_in,
            sum(a.qtystok_out) AS stok_out,
            form_so.formulirstokopname_id
           FROM stokobatalkes_t a
             LEFT JOIN ( SELECT a1.created_date,
                    a1.formulirstokopname_id,
                    a1.ruangan_id
                   FROM formulirstokopname_t a1) form_so ON a.created_date > form_so.created_date
          WHERE a.ruangan_id = form_so.ruangan_id
          GROUP BY a.obatalkes_id, form_so.formulirstokopname_id, a.ruangan_id) stokobatalkes ON stokobatalkes.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes.formulirstokopname_id = formstokopname_t.formulirstokopname_id AND stokobatalkes.ruangan_id = formstokopname_t.ruangan_id
  WHERE formstokopname_t.is_active = true AND formstokopname_t.is_deleted = false;
");

        $this->execute('DROP VIEW if exists "public"."detailstokopname_v";');

        $this->execute("
            CREATE VIEW \"public\".\"detailstokopname_v\" AS  SELECT stokopnamedetail.stokopnamedetail_id,
    stokopnamedetail.formstokopname_id,
    stokopnamedetail.formulirstokopname_id,
    stokopnamedetail.volume_sistem AS stok_sistem,
    stokopnamedetail.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_kode,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
    obatalkes_m.harganetto,
    stokopnamedetail.stokobatalkes_id,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN stokopnamedetail.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    stokopnamedetail.volume_fisik AS stok_fisik,
    COALESCE(laci.rak_id::bigint, rak.rakobat_id) AS rakobat_id,
    COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
    COALESCE(laci.rakobat_id, rak.rakobat_id) AS laciobat_id,
    COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    stokobatalkes_r.qty_sisa AS stok_saatini,
    stokobatalkes.stok_in,
    stokobatalkes.stok_out,
    stokopnamedetail.revisi_stok AS stok_revisi,
    jenisobatalkes_m.jenisobatalkes_nama
   FROM ( SELECT stokopnamedetail_t.stokopnamedetail_id,
            stokopnamedetail_t.formstokopname_id,
            stokopnamedetail_t.satuankecil_id,
            stokopnamedetail_t.sumberdana_id,
            stokopnamedetail_t.stokopname_id,
            stokopnamedetail_t.obatalkes_id,
            stokopnamedetail_t.volume_fisik,
            stokopnamedetail_t.volume_sistem,
            stokopnamedetail_t.hargasatuan,
            stokopnamedetail_t.jumlahharga,
            stokopnamedetail_t.harganetto,
            stokopnamedetail_t.jumlahnetto,
            stokopnamedetail_t.tglkadaluarsa,
            stokopnamedetail_t.kondisibarang,
            stokopnamedetail_t.tglperiksafisik,
            stokopnamedetail_t.jmlselisihstok,
            stokopnamedetail_t.stokobatalkes_id,
            stokopnamedetail_t.additional_data,
            stokopnamedetail_t.created_date,
            stokopnamedetail_t.created_by,
            stokopnamedetail_t.modified_count,
            stokopnamedetail_t.last_modified_date,
            stokopnamedetail_t.last_modified_by,
            stokopnamedetail_t.is_deleted,
            stokopnamedetail_t.is_active,
            stokopnamedetail_t.deleted_date,
            stokopnamedetail_t.deleted_by,
            stok_opname.ruangan_id,
            stok_opname.formulirstokopname_id,
            stokopnamedetail_t.revisi_stok
           FROM stokopnamedetail_t
             JOIN ( SELECT a.stokopname_id,
                    a.ruangan_id,
                    a.formulirstokopname_id
                   FROM stokopname_t a) stok_opname ON stokopnamedetail_t.stokopname_id = stok_opname.stokopname_id) stokopnamedetail
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.satuankecil_id,
            a.satuanbesar_id,
            a.obatalkes_namalain,
            a.obatalkes_kode,
            a.harganetto,
            a.jenisobatalkes_id
           FROM obatalkes_m a) obatalkes_m ON stokopnamedetail.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT a.stokopname_id
           FROM stokopname_t a) stokopname_t ON stokopnamedetail.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN ( SELECT a.stokobatalkes_id,
            a.tglkadaluarsa
           FROM stokobatalkes_t a) stokobatalkes_t ON stokopnamedetail.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            a.rakobat_id
           FROM konfigrak_m a) konfigrak_m ON stokopnamedetail.ruangan_id = konfigrak_m.ruangan_id AND stokopnamedetail.obatalkes_id = konfigrak_m.obatalkes_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.rakobat_nama
           FROM rakobat_m a) rak ON konfigrak_m.rakobat_id = rak.rakobat_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.parentrakobat_id AS rak_id,
            rak_1.rakobat_nama AS rak,
            a.rakobat_nama
           FROM rakobat_m a
             JOIN rakobat_m rak_1 ON a.parentrakobat_id = rak_1.rakobat_id
          WHERE a.parentrakobat_id IS NOT NULL) laci ON konfigrak_m.rakobat_id = laci.rakobat_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            a.qty_sisa
           FROM stokobatalkes_r a) stokobatalkes_r ON stokopnamedetail.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokopnamedetail.ruangan_id = stokobatalkes_r.ruangan_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN satuanunit_m uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true
          GROUP BY a.obatalkes_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom ON obatalkes_m.obatalkes_id = uom.obatalkes_id AND obatalkes_m.satuanbesar_id = uom.satuanbesar_id AND obatalkes_m.satuankecil_id = uom.satuankecil_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            sum(a.qtystok_in) AS stok_in,
            sum(a.qtystok_out) AS stok_out,
            form_so.formulirstokopname_id
           FROM stokobatalkes_t a
             LEFT JOIN ( SELECT a1.created_date,
                    a1.formulirstokopname_id,
                    a1.ruangan_id
                   FROM formulirstokopname_t a1) form_so ON a.created_date > form_so.created_date
          WHERE a.ruangan_id = form_so.ruangan_id
          GROUP BY a.obatalkes_id, form_so.formulirstokopname_id, form_so.ruangan_id) stokobatalkes ON stokobatalkes.obatalkes_id = stokopnamedetail.obatalkes_id AND stokobatalkes.formulirstokopname_id = stokopnamedetail.formulirstokopname_id
  WHERE stokopnamedetail.is_active = true AND stokopnamedetail.is_deleted = false;
");


        $this->execute('DROP VIEW if exists "public"."infostokopnamedetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokopnamedetail_v\" AS  SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    stokopname_t.stokopname_id,
    stokopname_t.tglstokopname,
    stokopname_t.nostokopname,
    stokopnamedetail_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    stokopnamedetail_t.tglkadaluarsa,
    COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS volume_fisik,
    stokopnamedetail_t.volume_sistem,
    COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) * stokopnamedetail_t.harganetto AS harga_netto_fisik,
    stokopnamedetail_t.volume_sistem * stokopnamedetail_t.harganetto AS harga_netto_sistem,
    COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS selisih_jumlah,
    stokopnamedetail_t.jmlselisihstok * stokopnamedetail_t.harganetto AS selisih_harganetto,
    petugas1.pegawai_id AS petugas1_id,
    petugas1.nomorindukpegawai AS petugas1_nip,
    petugas1.noidentitas AS petugas1_noidentitas,
    petugas1.gelardepan AS petugas1_gelardepan,
    petugas1.nama_pegawai AS petugas1_nama,
    petugas1.gelarbelakang_nama AS petugas1_gelarbelakang,
    petugas2.pegawai_id AS petugas2_id,
    petugas2.nomorindukpegawai AS petugas2_nip,
    petugas2.noidentitas AS petugas2_noidentitas,
    petugas2.gelardepan AS petugas2_gelardepan,
    petugas2.nama_pegawai AS petugas2_nama,
    petugas2.gelarbelakang_nama AS petugas2_gelarbelakang,
    pegawaimengetahui.pegawai_id AS pegawaimengetahui_id,
    pegawaimengetahui.nomorindukpegawai AS pegawaimengetahui_nip,
    pegawaimengetahui.noidentitas AS pegawaimengetahui_noidentitas,
    pegawaimengetahui.gelardepan AS pegawaimengetahui_gelardepan,
    pegawaimengetahui.nama_pegawai AS pegawaimengetahui_nama,
    pegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang,
    obatalkes_m.obatalkes_nama,
    fgetnamalookup(stokopnamedetail_t.kondisibarang::integer) AS kondisibarang_nama,
    NULL::text AS tglperiodestok_awal,
    NULL::text AS tglperiodestok_akhir,
        CASE
            WHEN stokopname_t.is_verifikasi = true THEN stokopnamedetail_t.stok_akhir
            ELSE stok.qty_sisa
        END AS stok_sistem,
        CASE
            WHEN stokopname_t.is_verifikasi = true THEN stokopnamedetail_t.selisih_akhir
            ELSE COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stok.qty_sisa
        END AS stok_selisih,
    stokopnamedetail_t.stokopnamedetail_id,
    stokopnamedetail_t.harganetto,
    stokopnamedetail_t.stokobatalkes_id,
    COALESCE(stokopnamedetail_t.weighted_avg, obatalkes_m.harganetto, logasetobat.weighted_avg::double precision, nulllogasetobat.harga_weighted_avg) AS weighted_avg,
    COALESCE(stokopnamedetail_t.weighted_avg, obatalkes_m.harganetto, logasetobat.weighted_avg::double precision, nulllogasetobat.harga_weighted_avg) * (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) AS selisih_weighted_avg,
    COALESCE(laci.rak_id::bigint, rak.rakobat_id) AS rakobat_id,
    COALESCE(laci.rak, rak.rakobat_nama) AS rak,
    COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
    COALESCE(laci.rakobat_id, rak.rakobat_id) AS laciobat_id,
    COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
    jenisobatalkes_m.jenisobatalkes_nama
   FROM stokopnamedetail_t
     JOIN ( SELECT a.stokopname_id,
            a.formulirstokopname_id,
            a.petugas1_id,
            a.mengetahui_id,
            a.ruangan_id,
            a.tglstokopname,
            a.nostokopname,
            a.is_verifikasi
           FROM stokopname_t a
          WHERE a.is_active = true AND a.is_deleted = false) stokopname_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN ( SELECT a.formulirstokopname_id,
            a.tglformulir,
            a.noformulir
           FROM formulirstokopname_t a) formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.obatalkes_namalain,
            a.harganetto,
            a.jenisobatalkes_id
           FROM obatalkes_m a) obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.gelarbelakang,
            a.nomorindukpegawai,
            a.noidentitas,
            a.gelardepan,
            gelar_belakang.gelarbelakang_nama
           FROM pegawai_m a
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelar_belakang ON a.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.gelarbelakang,
            a.nomorindukpegawai,
            a.noidentitas,
            a.gelardepan,
            gelar_belakang.gelarbelakang_nama
           FROM pegawai_m a
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelar_belakang ON a.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.gelarbelakang,
            a.nomorindukpegawai,
            a.noidentitas,
            a.gelardepan,
            gelar_belakang.gelarbelakang_nama
           FROM pegawai_m a
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelar_belakang ON a.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.obatalkes_id,
            a.ruangan_id
           FROM konfigrak_m a) konfigrak_m ON stokopnamedetail_t.obatalkes_id = konfigrak_m.obatalkes_id AND stokopname_t.ruangan_id = konfigrak_m.ruangan_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.rakobat_nama
           FROM rakobat_m a) rak ON konfigrak_m.rakobat_id = rak.rakobat_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.parentrakobat_id AS rak_id,
            rak_1.rakobat_nama AS rak,
            a.rakobat_nama
           FROM rakobat_m a
             JOIN rakobat_m rak_1 ON a.parentrakobat_id = rak_1.rakobat_id
          WHERE a.parentrakobat_id IS NOT NULL) laci ON konfigrak_m.rakobat_id = laci.rakobat_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.rakobat_nama,
            a.parentrakobat_id,
            rak_1.rakobat_nama AS rak
           FROM rakobat_m a
             LEFT JOIN ( SELECT a1.rakobat_id,
                    a1.rakobat_nama
                   FROM rakobat_m a1) rak_1 ON a.parentrakobat_id = rak_1.rakobat_id) rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            a.qty_sisa
           FROM stokobatalkes_r a) stok ON stokopnamedetail_t.obatalkes_id = stok.obatalkes_id AND stokopname_t.ruangan_id = stok.ruangan_id
     LEFT JOIN ( SELECT DISTINCT ON (a.obatalkes_id) a.logasetobat_id,
            a.obatalkes_id,
            COALESCE(a.weighted_avg, 0::numeric) AS weighted_avg
           FROM logasetobat_r a) logasetobat ON stokopnamedetail_t.obatalkes_id = logasetobat.obatalkes_id
     LEFT JOIN ( SELECT stokobatalkes_t.obatalkes_id,
            sum(stok_1.harga_asset / stok_1.qty_asset) AS harga_weighted_avg
           FROM stokobatalkes_t
             LEFT JOIN ( SELECT penerimaan_obat.harga * stok_obat.qty_asset AS harga_asset,
                    penerimaan_obat.obatalkes_id,
                    stok_obat.qty_asset
                   FROM ( SELECT penerimaanobatdetail_t.obatalkes_id,
                            penerimaanobatdetail_t.harga /
                                CASE
                                    WHEN penerimaanobatdetail_t.qty_diterima = 0 THEN 1
                                    ELSE NULL::integer
                                END::double precision AS harga
                           FROM penerimaanobatdetail_t
                        UNION ALL
                         SELECT penerimaansuppdetail_t.obatalkes_id,
                            penerimaansuppdetail_t.harga_netto /
                                CASE
                                    WHEN penerimaansuppdetail_t.qty_kecil = 0 THEN 1
                                    ELSE NULL::integer
                                END::double precision AS harga
                           FROM penerimaansuppdetail_t) penerimaan_obat
                     LEFT JOIN ( SELECT sum(st_ob.qtystok_in - st_ob.qtystok_out) AS qty_asset,
                            st_ob.obatalkes_id
                           FROM stokobatalkes_t st_ob
                          GROUP BY st_ob.obatalkes_id) stok_obat ON penerimaan_obat.obatalkes_id = stok_obat.obatalkes_id) stok_1 ON stokobatalkes_t.obatalkes_id = stok_1.obatalkes_id
          GROUP BY stokobatalkes_t.obatalkes_id) nulllogasetobat ON stokopnamedetail_t.obatalkes_id = nulllogasetobat.obatalkes_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210913_034802_migrate_issue_us1312 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210913_034802_migrate_issue_us1312 cannot be reverted.\n";

        return false;
    }
    */
}
