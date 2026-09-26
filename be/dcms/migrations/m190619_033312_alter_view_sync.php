<?php

use yii\db\Migration;

/**
 * Class m190619_033312_alter_view_sync
 */
class m190619_033312_alter_view_sync extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
     ALTER TABLE public.sync_category OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_collection OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_collection_de OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_datesync OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_pasien OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_pasien_copy OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_paymentmethod OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_pengeluaranobat OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_pengeluaranobat_de OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_penjamin OWNER TO postgres;
        ');

         $this->execute('
    ALTER TABLE public.sync_supplier OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_tindakan OWNER TO postgres;
        ');

         $this->execute('
     ALTER TABLE public.sync_tindakan_de OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190619_033312_alter_view_sync cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190619_033312_alter_view_sync cannot be reverted.\n";

        return false;
    }
    */
}
