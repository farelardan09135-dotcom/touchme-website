<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama jika ada
        Article::truncate();

        Article::create([
            'category' => 'tower',
            'type' => 'sst',
            'title' => 'Self-Support Tower (SST)',
            'slug' => 'tower-sst',
            'content' => 'Self-Support Tower (SST) adalah solusi menara telekomunikasi yang dirancang untuk berdiri kokoh tanpa kawat penyangga, menghadirkan efisiensi lahan dan keandalan struktur dalam satu sistem. Dengan ketinggian hingga 72 meter dan pilihan konfigurasi 3 atau 4 kaki, SST ideal digunakan di area sub-urban maupun rural untuk mendukung cakupan jaringan yang luas serta performa optimal.

Didukung konstruksi baja berkualitas tinggi, Self-Support Tower menawarkan stabilitas, keamanan, dan fleksibilitas untuk ekspansi jaringan di masa depan. Desainnya yang kuat memungkinkan peningkatan kapasitas perangkat secara berkelanjutan, menjadikannya pilihan tepat bagi operator telekomunikasi yang membutuhkan infrastruktur andal, efisien, dan siap menghadapi kebutuhan konektivitas modern.',
        ]);
        
        Article::create([
            'category' => 'tower',
            'type' => 'monopole',
            'title' => 'MONOPOLE',
            'slug' => 'tower-monopole',
            'content' => 'Monopole merupakan solusi menara telekomunikasi modern yang dirancang khusus untuk kebutuhan jaringan di area perkotaan dengan tingkat kepadatan tinggi. Tersedia dalam berbagai kategori ketinggian, mulai dari Easy Macro di bawah 10 meter, Micro Pole (MCP) 12–20 meter, hingga Mini Macro 21–30 meter, Monopole menawarkan fleksibilitas instalasi tanpa mengorbankan performa jaringan.

Dengan desain ramping dan estetis, Monopole sangat ideal digunakan di kawasan urban seperti pusat kota, area komersial, dan permukiman padat penduduk. Dilengkapi dengan fitur transmisi menggunakan antena microwave, solusi ini mampu mendukung kualitas konektivitas yang stabil sekaligus meminimalkan dampak visual lingkungan, menjadikannya pilihan tepat untuk pengembangan jaringan telekomunikasi yang efisien dan berkelanjutan.',
        ]);

        Article::create([
            'category' => 'tower',
            'type' => 'guyedmast',
            'title' => 'GUYED-MAST',
            'slug' => 'tower-guyedmast',
            'content' => 'Guyed Mast adalah jenis menara telekomunikasi berupa tiang pancang yang diperkuat dengan tali baja sebagai penyangga utama, sehingga mampu mencapai ketinggian yang lebih optimal dengan struktur yang efisien. Desain ini menjadikan Guyed Mast solusi ideal untuk pembangunan menara di wilayah dengan kebutuhan cakupan luas namun tidak memerlukan struktur menara berdiri sendiri yang besar.

Umumnya digunakan di daerah dengan kepadatan penduduk rendah hingga area terpencil, Guyed Mast menawarkan efisiensi biaya dan kemudahan instalasi tanpa mengurangi keandalan jaringan. Dengan kebutuhan lahan yang relatif lebih luas untuk sistem penyangga, menara ini sangat cocok untuk mendukung infrastruktur telekomunikasi di area rural, perluasan jaringan, serta peningkatan jangkauan transmisi secara maksimal.',
        ]);

        echo "✅ Successfully seeded 3 tower articles!\n";
    }
}