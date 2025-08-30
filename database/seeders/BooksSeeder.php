<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            // Literatura Brasileira
            [
                'titulo' => 'Dom Casmurro',
                'autor' => 'Machado de Assis',
                'editora' => 'Companhia das Letras',
                'isbn' => '9788535902777',
                'category_id' => 1,
                'descricao' => 'Clássico da literatura brasileira que narra a história de Bentinho e Capitu.',
                'paginas' => 256,
                'ano_publicacao' => 1899,
                'idioma' => 'Português',
                'preco' => 29.90,
                'quantidade_total' => 5,
                'quantidade_disponivel' => 5,
                'localizacao' => 'Estante A1'
            ],
            [
                'titulo' => 'O Cortiço',
                'autor' => 'Aluísio Azevedo',
                'editora' => 'Ática',
                'isbn' => '9788508133024',
                'category_id' => 1,
                'descricao' => 'Romance naturalista que retrata a vida em um cortiço no Rio de Janeiro.',
                'paginas' => 304,
                'ano_publicacao' => 1890,
                'idioma' => 'Português',
                'preco' => 24.90,
                'quantidade_total' => 3,
                'quantidade_disponivel' => 3,
                'localizacao' => 'Estante A2'
            ],
            [
                'titulo' => 'Memórias Póstumas de Brás Cubas',
                'autor' => 'Machado de Assis',
                'editora' => 'Penguin Classics',
                'isbn' => '9788563560070',
                'category_id' => 1,
                'descricao' => 'Romance narrado por um defunto autor que conta sua vida.',
                'paginas' => 208,
                'ano_publicacao' => 1881,
                'idioma' => 'Português',
                'preco' => 32.90,
                'quantidade_total' => 4,
                'quantidade_disponivel' => 4,
                'localizacao' => 'Estante A3'
            ],
            [
                'titulo' => 'O Guarani',
                'autor' => 'José de Alencar',
                'editora' => 'Martin Claret',
                'isbn' => '9788572327429',
                'category_id' => 1,
                'descricao' => 'Romance indianista sobre o amor entre Peri e Ceci.',
                'paginas' => 352,
                'ano_publicacao' => 1857,
                'idioma' => 'Português',
                'preco' => 27.90,
                'quantidade_total' => 3,
                'quantidade_disponivel' => 2,
                'localizacao' => 'Estante A4'
            ],
            
            // Ficção Internacional
            [
                'titulo' => '1984',
                'autor' => 'George Orwell',
                'editora' => 'Companhia das Letras',
                'isbn' => '9788535914849',
                'category_id' => 2,
                'descricao' => 'Distopia clássica sobre um regime totalitário.',
                'paginas' => 416,
                'ano_publicacao' => 1949,
                'idioma' => 'Português',
                'preco' => 44.90,
                'quantidade_total' => 4,
                'quantidade_disponivel' => 2,
                'localizacao' => 'Estante B1'
            ],
            [
                'titulo' => 'O Grande Gatsby',
                'autor' => 'F. Scott Fitzgerald',
                'editora' => 'Companhia das Letras',
                'isbn' => '9788535925814',
                'category_id' => 2,
                'descricao' => 'Romance sobre a Era do Jazz e o sonho americano.',
                'paginas' => 208,
                'ano_publicacao' => 1925,
                'idioma' => 'Português',
                'preco' => 39.90,
                'quantidade_total' => 6,
                'quantidade_disponivel' => 4,
                'localizacao' => 'Estante B2'
            ],
            [
                'titulo' => 'Cem Anos de Solidão',
                'autor' => 'Gabriel García Márquez',
                'editora' => 'Record',
                'isbn' => '9788501061805',
                'category_id' => 2,
                'descricao' => 'Obra-prima do realismo mágico sobre a família Buendía.',
                'paginas' => 432,
                'ano_publicacao' => 1967,
                'idioma' => 'Português',
                'preco' => 49.90,
                'quantidade_total' => 5,
                'quantidade_disponivel' => 3,
                'localizacao' => 'Estante B3'
            ],
            [
                'titulo' => 'O Apanhador no Campo de Centeio',
                'autor' => 'J.D. Salinger',
                'editora' => 'Editora do Autor',
                'isbn' => '9788582850046',
                'category_id' => 2,
                'descricao' => 'Romance sobre a adolescência e alienação social.',
                'paginas' => 272,
                'ano_publicacao' => 1951,
                'idioma' => 'Português',
                'preco' => 42.90,
                'quantidade_total' => 4,
                'quantidade_disponivel' => 4,
                'localizacao' => 'Estante B4'
            ],
            
            // Fantasia
            [
                'titulo' => 'Harry Potter e a Pedra Filosofal',
                'autor' => 'J.K. Rowling',
                'editora' => 'Rocco',
                'isbn' => '9788532511010',
                'category_id' => 3,
                'descricao' => 'Primeiro livro da série Harry Potter sobre um jovem bruxo.',
                'paginas' => 264,
                'ano_publicacao' => 1997,
                'idioma' => 'Português',
                'preco' => 39.90,
                'quantidade_total' => 8,
                'quantidade_disponivel' => 6,
                'localizacao' => 'Estante C1'
            ],
            [
                'titulo' => 'O Senhor dos Anéis: A Sociedade do Anel',
                'autor' => 'J.R.R. Tolkien',
                'editora' => 'Martins Fontes',
                'isbn' => '9788533613379',
                'category_id' => 3,
                'descricao' => 'Primeira parte da épica jornada pela Terra Média.',
                'paginas' => 576,
                'ano_publicacao' => 1954,
                'idioma' => 'Português',
                'preco' => 59.90,
                'quantidade_total' => 6,
                'quantidade_disponivel' => 4,
                'localizacao' => 'Estante C2'
            ],
            [
                'titulo' => 'As Crônicas de Nárnia: O Leão, a Feiticeira e o Guarda-Roupa',
                'autor' => 'C.S. Lewis',
                'editora' => 'Martins Fontes',
                'isbn' => '9788533619814',
                'category_id' => 3,
                'descricao' => 'Aventura fantástica no mundo mágico de Nárnia.',
                'paginas' => 208,
                'ano_publicacao' => 1950,
                'idioma' => 'Português',
                'preco' => 34.90,
                'quantidade_total' => 7,
                'quantidade_disponivel' => 5,
                'localizacao' => 'Estante C3'
            ],
            
            // Ficção Científica
            [
                'titulo' => 'Duna',
                'autor' => 'Frank Herbert',
                'editora' => 'Aleph',
                'isbn' => '9788576570646',
                'category_id' => 4,
                'descricao' => 'Épico de ficção científica sobre política, religião e ecologia.',
                'paginas' => 688,
                'ano_publicacao' => 1965,
                'idioma' => 'Português',
                'preco' => 69.90,
                'quantidade_total' => 4,
                'quantidade_disponivel' => 2,
                'localizacao' => 'Estante D1'
            ],
            [
                'titulo' => 'Fundação',
                'autor' => 'Isaac Asimov',
                'editora' => 'Aleph',
                'isbn' => '9788576570004',
                'category_id' => 4,
                'descricao' => 'Primeiro livro da série Fundação sobre psicohistória.',
                'paginas' => 256,
                'ano_publicacao' => 1951,
                'idioma' => 'Português',
                'preco' => 44.90,
                'quantidade_total' => 5,
                'quantidade_disponivel' => 3,
                'localizacao' => 'Estante D2'
            ],
            [
                'titulo' => 'Neuromancer',
                'autor' => 'William Gibson',
                'editora' => 'Aleph',
                'isbn' => '9788576570370',
                'category_id' => 4,
                'descricao' => 'Marco do cyberpunk sobre hackers e inteligência artificial.',
                'paginas' => 304,
                'ano_publicacao' => 1984,
                'idioma' => 'Português',
                'preco' => 49.90,
                'quantidade_total' => 3,
                'quantidade_disponivel' => 1,
                'localizacao' => 'Estante D3'
            ],
            
            // Romance
            [
                'titulo' => 'Orgulho e Preconceito',
                'autor' => 'Jane Austen',
                'editora' => 'Penguin Classics',
                'isbn' => '9788563560216',
                'category_id' => 5,
                'descricao' => 'Romance clássico sobre Elizabeth Bennet e Mr. Darcy.',
                'paginas' => 424,
                'ano_publicacao' => 1813,
                'idioma' => 'Português',
                'preco' => 39.90,
                'quantidade_total' => 6,
                'quantidade_disponivel' => 4,
                'localizacao' => 'Estante E1'
            ],
            [
                'titulo' => 'Me Chame Pelo Seu Nome',
                'autor' => 'André Aciman',
                'editora' => 'Intrínseca',
                'isbn' => '9788551003435',
                'category_id' => 5,
                'descricao' => 'Romance sobre o despertar do primeiro amor.',
                'paginas' => 256,
                'ano_publicacao' => 2007,
                'idioma' => 'Português',
                'preco' => 42.90,
                'quantidade_total' => 4,
                'quantidade_disponivel' => 2,
                'localizacao' => 'Estante E2'
            ],
            
            // Mistério/Suspense
            [
                'titulo' => 'O Código Da Vinci',
                'autor' => 'Dan Brown',
                'editora' => 'Arqueiro',
                'isbn' => '9788580410181',
                'category_id' => 6,
                'descricao' => 'Thriller sobre símbolos religiosos e sociedades secretas.',
                'paginas' => 432,
                'ano_publicacao' => 2003,
                'idioma' => 'Português',
                'preco' => 44.90,
                'quantidade_total' => 5,
                'quantidade_disponivel' => 3,
                'localizacao' => 'Estante F1'
            ],
            [
                'titulo' => 'Assassinato no Expresso do Oriente',
                'autor' => 'Agatha Christie',
                'editora' => 'L&PM',
                'isbn' => '9788525432071',
                'category_id' => 6,
                'descricao' => 'Clássico mistério com o detetive Hercule Poirot.',
                'paginas' => 256,
                'ano_publicacao' => 1934,
                'idioma' => 'Português',
                'preco' => 32.90,
                'quantidade_total' => 4,
                'quantidade_disponivel' => 2,
                'localizacao' => 'Estante F2'
            ],
            
            // Não-ficção
            [
                'titulo' => 'Sapiens: Uma Breve História da Humanidade',
                'autor' => 'Yuval Noah Harari',
                'editora' => 'L&PM',
                'isbn' => '9788525432072',
                'category_id' => 7,
                'descricao' => 'Análise da evolução da espécie humana.',
                'paginas' => 464,
                'ano_publicacao' => 2011,
                'idioma' => 'Português',
                'preco' => 54.90,
                'quantidade_total' => 6,
                'quantidade_disponivel' => 4,
                'localizacao' => 'Estante G1'
            ],
            [
                'titulo' => 'O Poder do Hábito',
                'autor' => 'Charles Duhigg',
                'editora' => 'Objetiva',
                'isbn' => '9788539004119',
                'category_id' => 7,
                'descricao' => 'Como os hábitos funcionam e como mudá-los.',
                'paginas' => 408,
                'ano_publicacao' => 2012,
                'idioma' => 'Português',
                'preco' => 49.90,
                'quantidade_total' => 5,
                'quantidade_disponivel' => 3,
                'localizacao' => 'Estante G2'
            ],
            
            // Biografia
            [
                'titulo' => 'Steve Jobs',
                'autor' => 'Walter Isaacson',
                'editora' => 'Companhia das Letras',
                'isbn' => '9788535918878',
                'category_id' => 8,
                'descricao' => 'Biografia autorizada do cofundador da Apple.',
                'paginas' => 656,
                'ano_publicacao' => 2011,
                'idioma' => 'Português',
                'preco' => 59.90,
                'quantidade_total' => 3,
                'quantidade_disponivel' => 1,
                'localizacao' => 'Estante H1'
            ],
            
            // História
            [
                'titulo' => 'Uma História do Mundo em 12 Mapas',
                'autor' => 'Jerry Brotton',
                'editora' => 'Zahar',
                'isbn' => '9788537815717',
                'category_id' => 9,
                'descricao' => 'Como os mapas moldaram nossa visão do mundo.',
                'paginas' => 512,
                'ano_publicacao' => 2012,
                'idioma' => 'Português',
                'preco' => 64.90,
                'quantidade_total' => 2,
                'quantidade_disponivel' => 2,
                'localizacao' => 'Estante I1'
            ],
            
            // Infantil
            [
                'titulo' => 'O Pequeno Príncipe',
                'autor' => 'Antoine de Saint-Exupéry',
                'editora' => 'Agir',
                'isbn' => '9788522008731',
                'category_id' => 10,
                'descricao' => 'Fábula poética sobre amizade, amor e perda.',
                'paginas' => 96,
                'ano_publicacao' => 1943,
                'idioma' => 'Português',
                'preco' => 19.90,
                'quantidade_total' => 6,
                'quantidade_disponivel' => 4,
                'localizacao' => 'Estante J1'
            ],
            [
                'titulo' => 'O Menino Maluquinho',
                'autor' => 'Ziraldo',
                'editora' => 'Melhoramentos',
                'isbn' => '9788506055847',
                'category_id' => 10,
                'descricao' => 'Clássico da literatura infantil brasileira.',
                'paginas' => 80,
                'ano_publicacao' => 1980,
                'idioma' => 'Português',
                'preco' => 24.90,
                'quantidade_total' => 8,
                'quantidade_disponivel' => 6,
                'localizacao' => 'Estante J2'
            ],
            [
                'titulo' => 'Matilda',
                'autor' => 'Roald Dahl',
                'editora' => 'Martins Fontes',
                'isbn' => '9788533619821',
                'category_id' => 10,
                'descricao' => 'História de uma menina com poderes especiais.',
                'paginas' => 240,
                'ano_publicacao' => 1988,
                'idioma' => 'Português',
                'preco' => 29.90,
                'quantidade_total' => 5,
                'quantidade_disponivel' => 3,
                'localizacao' => 'Estante J3'
            ]
        ];

        foreach ($books as $book) {
            DB::table('books')->insert(array_merge($book, [
                'status' => 'disponivel',
                'total_emprestimos' => 0,
                'avaliacao_media' => 0.0,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
