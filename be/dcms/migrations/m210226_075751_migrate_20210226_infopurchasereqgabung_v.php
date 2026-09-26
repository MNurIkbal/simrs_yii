<?php

use yii\db\Migration;

/**
 * Class m210226_075751_migrate_20210226_infopurchasereqgabung_v
 */
class m210226_075751_migrate_20210226_infopurchasereqgabung_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqgabung_v\" AS  SELECT 'OBAT'::text AS tipe,
    purchasereq_t.purchasereq_id,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereq_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereq_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereq_t.reference,
    purchasereq_t.status,
    fgetnamalookup(purchasereq_t.status::integer) AS status_pr,
    purchasereq_t.is_prcyto,
        CASE
            WHEN purchasereq_t.is_prcyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS pr_cyto
   FROM purchasereq_t
     JOIN ruangan_m ON purchasereq_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON purchasereq_t.pegawai_id = pegawai_m.pegawai_id
  WHERE purchasereq_t.is_deleted = false
UNION ALL
 SELECT 'BARANG'::text AS tipe,
    purchasereqbrg_t.purchasereqbrg_id AS purchasereq_id,
    purchasereqbrg_t.no_pr,
    purchasereqbrg_t.tgl_pr,
    purchasereqbrg_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereqbrg_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereqbrg_t.reference,
    purchasereqbrg_t.status,
    fgetnamalookup(purchasereqbrg_t.status::integer) AS status_pr,
    purchasereqbrg_t.is_prcyto,
        CASE
            WHEN purchasereqbrg_t.is_prcyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS pr_cyto
   FROM purchasereqbrg_t
     JOIN ruangan_m ON purchasereqbrg_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON purchasereqbrg_t.pegawai_id = pegawai_m.pegawai_id
  WHERE purchasereqbrg_t.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."infopurchasereqgabung_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_075751_migrate_20210226_infopurchasereqgabung_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_075751_migrate_20210226_infopurchasereqgabung_v cannot be reverted.\n";

        return false;
    }
    */
}
