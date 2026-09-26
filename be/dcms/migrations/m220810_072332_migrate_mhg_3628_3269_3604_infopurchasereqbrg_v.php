<?php

use yii\db\Migration;

/**
 * Class m220810_072332_migrate_mhg_3628_3269_3604_infopurchasereqbrg_v
 */
class m220810_072332_migrate_mhg_3628_3269_3604_infopurchasereqbrg_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopurchasereqbrg_v";');

        $this->execute("
           CREATE VIEW \"public\".\"infopurchasereqbrg_v\" AS  SELECT purchasereqbrg_t.purchasereqbrg_id,
    purchasereqbrg_t.no_pr,
    purchasereqbrg_t.tgl_pr,
    purchasereqbrg_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereqbrg_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereqbrg_t.reference,
    purchasereqbrg_t.status,
    look_statuspurchase.lookup_name AS status_pr,
    purchasereqbrg_t.is_prcyto,
        CASE
            WHEN purchasereqbrg_t.is_prcyto = true THEN 'Cito'::text
            ELSE 'Reguler'::text
        END AS pr_cyto,
    purchasereqbrg_t.tgl_approve,
    peg_approve.nama_pegawai AS pegawai_approve,
        CASE
            WHEN purchasereqbrg_t.is_admin = true THEN 'Ya'::text
            ELSE 'Tidak'::text
        END AS pr_admin,
        CASE
            WHEN purchasereqbrg_t.is_admin = true THEN true
            ELSE false
        END AS is_admin
   FROM purchasereqbrg_t
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON purchasereqbrg_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON purchasereqbrg_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_approve ON purchasereqbrg_t.peg_approve_id = peg_approve.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuspurchase ON purchasereqbrg_t.status::integer = look_statuspurchase.lookup_id
  WHERE purchasereqbrg_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_072332_migrate_mhg_3628_3269_3604_infopurchasereqbrg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_072332_migrate_mhg_3628_3269_3604_infopurchasereqbrg_v cannot be reverted.\n";

        return false;
    }
    */
}
