<?php

use yii\db\Migration;

/**
 * Class m201124_100049_migrate_20201124_infostokobatdetail_v
 */
class m201124_100049_migrate_20201124_infostokobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."infostokobatdetail_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infostokobatdetail_v\" AS  SELECT proses.obatalkes_id,
    sum(proses.qtystok_in - proses.qtystok_out) AS stok_sistem,
    proses.obatalkes_nama,
    proses.tglkadaluarsa,
    proses.nobatch,
    proses.harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokobat_id,
    proses.tglperiodestok_awal AS tglperiodeposting_awal,
    proses.tglperiodestok_akhir AS tglperiodeposting_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.sop_obatalkes_id,
    proses.periodestok_nama
   FROM ( SELECT
                CASE
                    WHEN stokobatalkes_t.stokobatalkesasal_id IS NULL THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.nobatch,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            obatalkes_m.harganetto AS harganetto2,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokobatalkes_r.periodestokobat_id,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            formstokopname_t.obatalkes_id AS sop_obatalkes_id,
            formstokopname_t.stokopnamedetail_id AS sop_stokopnamedetail_id,
            periodestokobat_m.periodestok_nama,
            konfigfarmasi_k.hargaygdigunakan,
            obatalkes_m.hargamaksimum,
            obatalkes_m.hargaminimum,
            obatalkes_m.hargaratarata,
                CASE
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MAX'::text THEN obatalkes_m.hargamaksimum
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'MIN'::text THEN obatalkes_m.hargaminimum
                    WHEN konfigfarmasi_k.hargaygdigunakan::text = 'AVG'::text THEN obatalkes_m.hargaratarata
                    ELSE obatalkes_m.harganetto
                END AS harganetto
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
                  WHERE formstokopname_t_1.stokopnamedetail_id IS NULL) formstokopname_t ON stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id
             JOIN stokobatalkes_r ON stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id AND stokobatalkes_r.is_periode = true
             LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
          WHERE formstokopname_t.formstokopname_id IS NULL) proses
  GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.tglkadaluarsa, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.periodestokobat_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.sop_obatalkes_id, proses.periodestok_nama, proses.nobatch;");

         $this->execute('ALTER TABLE "public"."infostokobatdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_100049_migrate_20201124_infostokobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_100049_migrate_20201124_infostokobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
