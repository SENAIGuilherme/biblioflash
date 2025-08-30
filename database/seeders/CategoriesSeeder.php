<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nome' => 'Ficção', 'descricao' => 'Livros de ficção em geral', 'cor' => '#FF6B6B'],
            ['nome' => 'Romance', 'descricao' => 'Livros de romance e relacionamentos', 'cor' => '#FF8E8E'],
            ['nome' => 'Mistério', 'descricao' => 'Livros de mistério e suspense', 'cor' => '#4ECDC4'],
            ['nome' => 'Fantasia', 'descricao' => 'Livros de fantasia e mundos imaginários', 'cor' => '#45B7D1'],
            ['nome' => 'Ficção Científica', 'descricao' => 'Livros de ficção científica e futurismo', 'cor' => '#96CEB4'],
            ['nome' => 'Biografia', 'descricao' => 'Biografias e autobiografias', 'cor' => '#FFEAA7'],
            ['nome' => 'História', 'descricao' => 'Livros sobre história e eventos históricos', 'cor' => '#DDA0DD'],
            ['nome' => 'Autoajuda', 'descricao' => 'Livros de desenvolvimento pessoal', 'cor' => '#98D8C8'],
            ['nome' => 'Técnico', 'descricao' => 'Livros técnicos e especializados', 'cor' => '#F7DC6F'],
            ['nome' => 'Infantil', 'descricao' => 'Livros para crianças', 'cor' => '#FFB6C1'],
            ['nome' => 'Juvenil', 'descricao' => 'Livros para jovens', 'cor' => '#87CEEB'],
            ['nome' => 'Poesia', 'descricao' => 'Livros de poesia e literatura', 'cor' => '#DEB887'],
            ['nome' => 'Drama', 'descricao' => 'Livros dramáticos e teatrais', 'cor' => '#CD853F'],
            ['nome' => 'Aventura', 'descricao' => 'Livros de aventura e ação', 'cor' => '#FF7F50'],
            ['nome' => 'Educação', 'descricao' => 'Livros educacionais e didáticos', 'cor' => '#20B2AA']
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'nome' => $category['nome'],
                'slug' => Str::slug($category['nome']),
                'descricao' => $category['descricao'],
                'cor' => $category['cor'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
