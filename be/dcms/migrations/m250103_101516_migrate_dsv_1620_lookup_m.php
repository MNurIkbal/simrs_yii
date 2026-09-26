<?php

use yii\db\Migration;

/**
 * Class m250103_101516_migrate_dsv_1620_lookup_m
 */
class m250103_101516_migrate_dsv_1620_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            2193,
            2194,
            2195,
            2196,
            2197,
            2206,
            2207,
            2208
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data") VALUES 
            (2193, \'form_asmed_ranap\', \'Asesmen Medis Pasien Menular/Infeksius\', \'Asesmen Medis Pasien Menular/Infeksius\', 1, \'inspeksius\', NULL),
            (2194, \'form_asmed_ranap\', \'Asesmen Medis Pasien dengan Sistem Imunologi Terganggu\', \'Asesmen Medis Pasien dengan Sistem Imunologi Terganggu\', 2, \'imunologi\', NULL),
            (2195, \'form_asmed_ranap\', \'Asesmen Medis Pasien Geriatri\', \'Asesmen Medis Pasien Geriatri\', 3, \'geriatri\', NULL),
            (2196, \'form_asmed_ranap\', \'Asesmen Medis Pasien Korban Kekerasan\', \'Asesmen Medis Pasien Korban Kekerasan\', 4, \'kekerasan\', NULL),
            (2197, \'form_asmed_ranap\', \'Asesmen Medis Pasien Sakit Terminal\', \'Asesmen Medis Pasien Korban Kekerasan\', 5, \'terminal\', NULL),
            (2206, \'form_asmed_ranap\', \'Asesmen Medis Pasien Nyeri Kronik\', \'Asesmen Medis Pasien Nyeri Kronik\', 5, \'kronik\', \'\'),
            (2207, \'form_asmed_ranap\', \'Asesmen Medis Pasien Neonatus/Bayi Rawat Inap\', \'Asesmen Medis Pasien Neonatus/Bayi Rawat Inap\', 1, \'neonatus\', \'\'),
            (2208, \'form_asmed_ranap\', \'Asesmen Medis Kecanduan Obat Terlarang\', \'Asesmen Medis Kecanduan Obat Terlarang\', NULL, \'kecanduan\', NULL);

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250103_101516_migrate_dsv_1620_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250103_101516_migrate_dsv_1620_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
