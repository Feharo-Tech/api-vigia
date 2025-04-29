<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Produção', 'color' => '#dc2626'],      // Vermelho
            ['name' => 'Homologação', 'color' => '#f59e0b'],   // Amarelo
            ['name' => 'Desenvolvimento', 'color' => '#2563eb'], // Azul
            ['name' => 'Staging', 'color' => '#7c3aed'],       // Roxo
            ['name' => 'Interna', 'color' => '#16a34a'],       // Verde
            ['name' => 'Externa', 'color' => '#0d9488'],       // Ciano
            ['name' => 'Crítica', 'color' => '#b91c1c'],       // Vermelho escuro
            ['name' => 'Beta', 'color' => '#d5dae3'],          // Cinza
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(['name' => $tagData['name']], ['color' => $tagData['color']]);
        }
    }
}
