<?php

use yii\db\Migration;

/**
 * Class m210616_102618_migrate_3735_ImprovementFormdanTransaksiSO
 */
class m210616_102618_migrate_3735_ImprovementFormdanTransaksiSO extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."stokopnamedetail_t" 
  ADD COLUMN if not exists "revisi_stok" float8,
  ADD COLUMN if not exists"weighted_avg" float8;');

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
    x.obatalkes_kode
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
            proses.obatalkes_kode
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
                    rakobat_m.rakobat_nama,
                        CASE
                            WHEN rakobat_m.parentrakobat_id IS NOT NULL THEN rakobat_m.parentrakobat_id::bigint
                            ELSE rakobat_m.rakobat_id
                        END AS rakobat_id,
                    rakobat_m.rakobat_id AS laciobat_id,
                    obatalkes_m.obatalkes_kode
                   FROM stokobatalkes_t
                     JOIN obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT formstokopname_t_1.formstokopname_id,
                            formstokopname_t_1.stokopnamedetail_id,
                            formstokopname_t_1.obatalkes_id,
                            formstokopname_t_1.formulirstokopname_id,
                            formstokopname_t_1.volume_stok,
                            formstokopname_t_1.periodestok_id,
                            formstokopname_t_1.ruangan_id,
                            formstokopname_t_1.additional_data,
                            formstokopname_t_1.created_date,
                            formstokopname_t_1.created_by,
                            formstokopname_t_1.modified_count,
                            formstokopname_t_1.last_modified_date,
                            formstokopname_t_1.last_modified_by,
                            formstokopname_t_1.is_deleted,
                            formstokopname_t_1.is_active,
                            formstokopname_t_1.deleted_date,
                            formstokopname_t_1.deleted_by,
                            formstokopname_t_1.nobatch,
                            formstokopname_t_1.stokobatalkes_id,
                            formstokopname_t_1.tglkadaluarsa
                           FROM formstokopname_t formstokopname_t_1
                          WHERE formstokopname_t_1.stokopnamedetail_id IS NULL) formstokopname_t ON stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id AND formstokopname_t.is_deleted IS FALSE
                     JOIN stokobatalkes_r ON stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id AND stokobatalkes_r.is_periode = true
                     LEFT JOIN konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id
                     LEFT JOIN rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
                     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                  WHERE formstokopname_t.formstokopname_id IS NULL) proses
          GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.ruangan_id, proses.instalasi_id, proses.sop_obatalkes_id, proses.rakobat_nama, proses.rakobat_id, proses.laciobat_id, proses.obatalkes_kode) x
  WHERE x.stok_sistem > 0::double precision;");

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
    rakobat_m.rakobat_nama,
    obatalkes_m.obatalkes_kode,
        CASE
            WHEN rakobat_m.parentrakobat_id IS NOT NULL THEN rakobat_m.parentrakobat_id::bigint
            ELSE rakobat_m.rakobat_id
        END AS rakobat_id,
    rakobat_m.rakobat_id AS laciobat_id,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    stokobatalkes_r.qty_sisa AS stok_saatini,
    stokobatalkes.stok_in,
    stokobatalkes.stok_out
   FROM formstokopname_t
     JOIN obatalkes_m ON formstokopname_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN stokobatalkes_t ON formstokopname_t.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN stokobatalkes_r ON formstokopname_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND formstokopname_t.ruangan_id = stokobatalkes_r.ruangan_id
     LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN konfigrak_m ON formstokopname_t.ruangan_id = konfigrak_m.ruangan_id AND formstokopname_t.obatalkes_id = konfigrak_m.obatalkes_id
     LEFT JOIN rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON obatalkes_m.obatalkes_id = uom.obatalkes_id AND obatalkes_m.satuanbesar_id = uom.satuanbesar_id AND obatalkes_m.satuankecil_id = uom.satuankecil_id
     LEFT JOIN ( SELECT st.obatalkes_id,
            sum(st.qtystok_in) AS stok_in,
            sum(st.qtystok_out) AS stok_out,
            form_so.formulirstokopname_id,
            st.ruangan_id
           FROM stokobatalkes_t st
             LEFT JOIN ( SELECT formulirstokopname_t.created_date,
                    formulirstokopname_t.formulirstokopname_id
                   FROM formulirstokopname_t) form_so ON st.created_date > form_so.created_date
          GROUP BY st.obatalkes_id, form_so.formulirstokopname_id, st.ruangan_id) stokobatalkes ON stokobatalkes.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes.formulirstokopname_id = formstokopname_t.formulirstokopname_id AND stokobatalkes.ruangan_id = formstokopname_t.ruangan_id
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
    rakobat_m.rakobat_nama,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN stokopnamedetail.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    stokopnamedetail.volume_fisik AS stok_fisik,
        CASE
            WHEN rakobat_m.parentrakobat_id IS NOT NULL THEN rakobat_m.parentrakobat_id::bigint
            ELSE rakobat_m.rakobat_id
        END AS rakobat_id,
    rakobat_m.rakobat_id AS laciobat_id,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    stokobatalkes_r.qty_sisa AS stok_saatini,
    stokobatalkes.stok_in,
    stokobatalkes.stok_out,
    stokopnamedetail.revisi_stok AS stok_revisi
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
            stokopname_t_1.ruangan_id,
            stokopname_t_1.formulirstokopname_id,
            stokopnamedetail_t.revisi_stok
           FROM stokopnamedetail_t
             JOIN stokopname_t stokopname_t_1 ON stokopnamedetail_t.stokopname_id = stokopname_t_1.stokopname_id) stokopnamedetail
     JOIN obatalkes_m ON stokopnamedetail.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN stokopname_t ON stokopnamedetail.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN stokobatalkes_t ON stokopnamedetail.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN konfigrak_m ON stokopnamedetail.ruangan_id = konfigrak_m.ruangan_id AND stokopnamedetail.obatalkes_id = konfigrak_m.obatalkes_id
     LEFT JOIN rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     LEFT JOIN stokobatalkes_r ON stokopnamedetail.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokopnamedetail.ruangan_id = stokobatalkes_r.ruangan_id
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON obatalkes_m.obatalkes_id = uom.obatalkes_id AND obatalkes_m.satuanbesar_id = uom.satuanbesar_id AND obatalkes_m.satuankecil_id = uom.satuankecil_id
     LEFT JOIN ( SELECT st.obatalkes_id,
            sum(st.qtystok_in) AS stok_in,
            sum(st.qtystok_out) AS stok_out,
            form_so.formulirstokopname_id
           FROM stokobatalkes_t st
             LEFT JOIN ( SELECT formulirstokopname_t.created_date,
                    formulirstokopname_t.formulirstokopname_id
                   FROM formulirstokopname_t) form_so ON st.created_date > form_so.created_date
          GROUP BY st.obatalkes_id, form_so.formulirstokopname_id) stokobatalkes ON stokobatalkes.obatalkes_id = stokopnamedetail.obatalkes_id AND stokobatalkes.formulirstokopname_id = stokopnamedetail.formulirstokopname_id
  WHERE stokopnamedetail.is_active = true AND stokopnamedetail.is_deleted = false;
");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210616_102618_migrate_3735_ImprovementFormdanTransaksiSO cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210616_102618_migrate_3735_ImprovementFormdanTransaksiSO cannot be reverted.\n";

        return false;
    }
    */
}
