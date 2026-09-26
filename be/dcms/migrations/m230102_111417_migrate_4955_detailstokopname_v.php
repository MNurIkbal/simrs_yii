<?php

use yii\db\Migration;

/**
 * Class m230102_111417_migrate_4955_detailstokopname_v
 */
class m230102_111417_migrate_4955_detailstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."detailstokopname_v";');
        $this->execute("
			CREATE OR REPLACE VIEW public.detailstokopname_v
            AS  SELECT stokopnamedetail.stokopnamedetail_id,
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
    COALESCE(round(stokobatalkes.stok_in::numeric, 3), 0::numeric) - COALESCE(round(stokobatalkes.stok_out::numeric, 3), 0::numeric) + COALESCE(round(stokopnamedetail.volume_sistem::numeric, 3), 0::numeric) AS stok_saatini,
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
           FROM konfigrak_m a
          WHERE a.is_deleted = false) konfigrak_m ON stokopnamedetail.ruangan_id = konfigrak_m.ruangan_id AND stokopnamedetail.obatalkes_id = konfigrak_m.obatalkes_id
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
  WHERE stokopnamedetail.is_active = true AND stokopnamedetail.is_deleted = false ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230102_111417_migrate_4955_detailstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230102_111417_migrate_4955_detailstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
