<?php

use yii\db\Migration;

/**
 * Class m220622_072950_migrate_VCS257_infopurchasereqgabung_v
 */
class m220622_072950_migrate_VCS257_infopurchasereqgabung_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopurchasereqgabung_v";');
        $this->execute("CREATE VIEW \"public\".\"infopurchasereqgabung_v\" AS  SELECT 'OBAT'::text AS tipe,
        purchasereq_t.purchasereq_id,
        purchasereq_t.no_pr,
        purchasereq_t.tgl_pr,
        purchasereq_t.ruangan_id,
        ruangan_m.ruangan_nama AS ruangan,
        purchasereq_t.pegawai_id,
        pegawai_m.nama_pegawai AS pegawai,
        purchasereq_t.reference,
        purchasereq_t.status,
        look_statuspr.lookup_name,
        purchasereq_t.is_prcyto,
            CASE
                WHEN purchasereq_t.is_prcyto = true THEN 'Cyto'::text
                ELSE 'Non Cyto'::text
            END AS pr_cyto,
        purchasereq_t.tgl_approve,
        peg_approve.nama_pegawai AS pegawai_approve,
        purchasereq_t.created_date
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
         JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_statuspr ON purchasereq_t.status::integer = look_statuspr.lookup_id
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
        look_statuspr.lookup_name,
        purchasereqbrg_t.is_prcyto,
            CASE
                WHEN purchasereqbrg_t.is_prcyto = true THEN 'Cyto'::text
                ELSE 'Non Cyto'::text
            END AS pr_cyto,
        purchasereqbrg_t.tgl_approve,
        peg_approve.nama_pegawai AS pegawai_approve,
        purchasereqbrg_t.created_date
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
         JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_statuspr ON purchasereqbrg_t.status::integer = look_statuspr.lookup_id
      WHERE purchasereqbrg_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220622_072950_migrate_VCS257_infopurchasereqgabung_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_072950_migrate_VCS257_infopurchasereqgabung_v cannot be reverted.\n";

        return false;
    }
    */
}
