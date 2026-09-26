<?php

use yii\db\Migration;

/**
 * Class m220810_072550_migrate_mhg_3628_3269_3604_infopurchasereq_v
 */
class m220810_072550_migrate_mhg_3628_3269_3604_infopurchasereq_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopurchasereq_v";');

        $this->execute("
           CREATE VIEW \"public\".\"infopurchasereq_v\" AS SELECT purchasereq_t.purchasereq_id,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereq_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereq_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereq_t.reference,
    purchasereq_t.status,
    purchasereq_status.lookup_name AS status_pr,
    purchasereq_t.is_prcyto,
        CASE
            WHEN purchasereq_t.is_prcyto = true THEN 'Cito'::text
            ELSE 'Reguler'::text
        END AS pr_cyto,
    purchasereq_t.tgl_approve,
    peg_approve.nama_pegawai AS pegawai_approve,
        CASE
            WHEN purchasereq_t.is_admin = true THEN 'Ya'::text
            ELSE 'Tidak'::text
        END AS pr_admin,
        CASE
            WHEN purchasereq_t.is_admin = true THEN true
            ELSE false
        END AS is_admin,
    purchasereq_t.is_consignment,
        CASE
            WHEN purchasereq_t.is_consignment = true THEN 'Ya'::text
            ELSE 'Tidak'::text
        END AS pr_consignment
   FROM purchasereq_t
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON purchasereq_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON purchasereq_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_approve ON purchasereq_t.peg_approve_id = peg_approve.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) purchasereq_status ON purchasereq_t.status = purchasereq_status.lookup_id
  WHERE purchasereq_t.is_deleted = false ; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_072550_migrate_mhg_3628_3269_3604_infopurchasereq_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_072550_migrate_mhg_3628_3269_3604_infopurchasereq_v cannot be reverted.\n";

        return false;
    }
    */
}
