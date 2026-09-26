<?php

use yii\db\Migration;

/**
 * Class m201206_073551_migrate_mhkn_20201206_report_salesorder_spesialisasi
 */
class m201206_073551_migrate_mhkn_20201206_report_salesorder_spesialisasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.report_salesorder_spesialisasi;');
        $this->execute("CREATE VIEW \"public\".\"report_salesorder_spesialisasi\" AS
             SELECT (to_char((report_salesorder.tgl_proses)::timestamp with time zone, 'YYYY-MM-DD'::text))::date AS periode,
    report_salesorder.spesialisasi,
    count(report_salesorder.product) AS product,
    sum(report_salesorder.total_revenue) AS total
   FROM report_salesorder
  GROUP BY report_salesorder.spesialisasi, report_salesorder.revenue_type_layer1, (to_char((report_salesorder.tgl_proses)::timestamp with time zone, 'YYYY-MM-DD'::text))::date
            ;");
            $this->execute('ALTER TABLE public.report_salesorder_spesialisasi
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201206_073551_migrate_mhkn_20201206_report_salesorder_spesialisasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201206_073551_migrate_mhkn_20201206_report_salesorder_spesialisasi cannot be reverted.\n";

        return false;
    }
    */
}
