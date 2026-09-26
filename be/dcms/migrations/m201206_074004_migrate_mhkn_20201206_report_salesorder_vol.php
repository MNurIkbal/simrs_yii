<?php

use yii\db\Migration;

/**
 * Class m201206_074004_migrate_mhkn_20201206_report_salesorder_vol
 */
class m201206_074004_migrate_mhkn_20201206_report_salesorder_vol extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.report_salesorder_vol;');
        $this->execute("CREATE VIEW \"public\".\"report_salesorder_vol\" AS
             SELECT (to_char((report_salesorder.tgl_proses)::timestamp with time zone, 'YYYY-MM-DD'::text))::date AS periode,
    report_salesorder.revenue_type_layer1,
    report_salesorder.department,
    count(report_salesorder.product) AS product
   FROM report_salesorder
  GROUP BY report_salesorder.revenue_type_layer1, report_salesorder.department, (to_char((report_salesorder.tgl_proses)::timestamp with time zone, 'YYYY-MM-DD'::text))::date
            ;");
            $this->execute('ALTER TABLE public.report_salesorder_vol
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201206_074004_migrate_mhkn_20201206_report_salesorder_vol cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201206_074004_migrate_mhkn_20201206_report_salesorder_vol cannot be reverted.\n";

        return false;
    }
    */
}
