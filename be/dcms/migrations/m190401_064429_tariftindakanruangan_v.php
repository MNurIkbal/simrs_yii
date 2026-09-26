<?php

use yii\db\Migration;

/**
 * Class m190401_064429_tariftindakanruangan_v
 */
class m190401_064429_tariftindakanruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW tariftindakanruangan_v AS 
             SELECT kategoritindakan_m.kategoritindakan_id,
                kategoritindakan_m.kategoritindakan_nama,
                kelompoktindakan_m.kelompoktindakan_id,
                kelompoktindakan_m.kelompoktindakan_nama,
                daftartindakan_m.daftartindakan_kode,
                daftartindakan_m.daftartindakan_nama,
                daftartindakan_m.daftartindakan_namalainnya,
                daftartindakan_m.daftartindakan_katakunci,
                perdatarif_m.perdatarif_id,
                perdatarif_m.perdanama_sk,
                perdatarif_m.perda_no,
                perdatarif_m.perda_tgl,
                perdatarif_m.perda_tentang,
                perdatarif_m.ditetapkan_oleh,
                perdatarif_m.tempat_ditetapkan,
                jenistarif_m.jenistarif_id,
                jenistarif_m.jenistarif_nama,
                tariftindakan_m.tariftindakan_id,
                komponentarif_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.hargadiskon_tindakan,
                tariftindakan_m.persencyto_tindakan,
                jeniskelas_m.jeniskelas_id,
                jeniskelas_m.jeniskelas_nama,
                kelaspelayanan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                kelaspelayanan_m.kelaspelayanan_namalainnya,
                daftartindakan_m.daftartindakan_id,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_nama
               FROM daftartindakan_m
                 JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                 JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                 JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                 JOIN tariftindakan_m ON daftartindakan_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 LEFT JOIN jenistarif_m ON tariftindakan_m.jenistarif_id = jenistarif_m.jenistarif_id
                 LEFT JOIN jenistarifpenjamin_mp ON jenistarif_m.jenistarif_id = jenistarifpenjamin_mp.jenistarif_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN jeniskelas_m ON kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id
                 LEFT JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                 LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
              WHERE tariftindakan_m.komponentarif_id = 6 AND perdatarif_m.is_active = true AND daftartindakan_m.is_active = true AND daftartindakan_m.is_deleted = false AND tariftindakan_m.is_deleted = false
              GROUP BY kategoritindakan_m.kategoritindakan_id, kategoritindakan_m.kategoritindakan_nama, kelompoktindakan_m.kelompoktindakan_id, kelompoktindakan_m.kelompoktindakan_nama, daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama, daftartindakan_m.daftartindakan_namalainnya, daftartindakan_m.daftartindakan_katakunci, perdatarif_m.perdatarif_id, perdatarif_m.perdanama_sk, perdatarif_m.perda_no, perdatarif_m.perda_tgl, perdatarif_m.perda_tentang, perdatarif_m.ditetapkan_oleh, perdatarif_m.tempat_ditetapkan, jenistarif_m.jenistarif_id, jenistarif_m.jenistarif_nama, tariftindakan_m.tariftindakan_id, komponentarif_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persendiskon_tindakan, tariftindakan_m.hargadiskon_tindakan, tariftindakan_m.persencyto_tindakan, jeniskelas_m.jeniskelas_id, jeniskelas_m.jeniskelas_nama, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, kelaspelayanan_m.kelaspelayanan_namalainnya, daftartindakan_m.daftartindakan_id, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama;
        ');
        
        $this->execute('
            ALTER TABLE tariftindakanruangan_v
              OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_064429_tariftindakanruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_064429_tariftindakanruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
