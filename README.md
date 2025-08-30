# 📚 BiblioFlash - Sistema de Gerenciamento de Biblioteca

![BiblioFlash Logo](public/biblio-flash/logo-of.png)

## 🌟 Sobre o Projeto

O **BiblioFlash** é um sistema completo de gerenciamento de biblioteca desenvolvido em Laravel, que oferece uma experiência moderna e intuitiva para bibliotecários e usuários. O sistema integra tecnologia RFID para identificação automática de livros e possui um sistema de tótem para autoatendimento.

## ✨ Funcionalidades Principais

### 👥 Gestão de Usuários

### 📖 Gerenciamento de Livros

### 🔄 Sistema de Empréstimos

### 📋 Sistema de Reservas

### 🏷️ Tecnologia RFID

### 🖥️ Sistema de Tótem

### 📊 Dashboard Administrativo

### ⭐ Funcionalidades Extras

## 🛠️ Tecnologias Utilizadas

### Backend

### Frontend

### Integração Hardware

### Ferramentas de Desenvolvimento

## 📋 Pré-requisitos


## 🚀 Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/biblioflash.git
cd biblioflash/site
```

### 2. Instale as dependências PHP
```bash
composer install
```

### 3. Instale as dependências JavaScript
```bash
npm install
```

### 4. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o banco de dados
Edite o arquivo `.env` com suas configurações de banco de dados:
```env
DB_CONNECTION=sqlite
# Para SQLite, o arquivo será criado automaticamente

# Ou para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=biblioflash
# DB_USERNAME=root
# DB_PASSWORD=
```

### 6. Execute as migrações
```bash
php artisan migrate
```

### 7. Execute os seeders (opcional)
```bash
php artisan db:seed
```

### 8. Crie o usuário administrador
```bash
php artisan db:seed --class=AdminUserSeeder
```

### 9. Compile os assets
```bash
npm run build
```

### 10. Inicie o servidor
```bash
php artisan serve
```

O sistema estará disponível em `http://localhost:8000`

## 👤 Credenciais Padrão

**Administrador:**

## 📱 Como Usar

### Para Administradores
1. Acesse o sistema com as credenciais de admin
2. Use o **Dashboard Administrativo** para visão geral
3. Gerencie livros em **Gerenciar Livros**
4. Use o **Painel RFID** para identificação automática
5. Controle usuários, empréstimos e reservas

### Para Bibliotecários
1. Acesse com credenciais de bibliotecário
2. Gerencie empréstimos e devoluções
3. Atenda reservas de usuários
4. Use o sistema de tótem para autoatendimento

### Para Clientes
1. Registre-se no sistema
2. Navegue pelo catálogo de livros
3. Faça reservas de livros
4. Acompanhe seus empréstimos no perfil
5. Use o tótem para autoatendimento

### Sistema RFID
1. Acesse **Admin > Painel RFID**
2. Conecte o Arduino via porta serial
3. Aproxime livros com tags RFID
4. O sistema identificará automaticamente

### Sistema de Tótem
1. Acesse `/totem` no navegador
2. Faça login com CPF
3. Use para empréstimos ou devoluções
4. Siga as instruções na tela

## 🗂️ Estrutura do Projeto

```
biblioflash/site/
├── app/
│   ├── Http/Controllers/     # Controllers da aplicação
│   ├── Models/              # Modelos Eloquent
│   ├── Policies/            # Políticas de autorização
│   └── Providers/           # Service providers
├── database/
│   ├── migrations/          # Migrações do banco
│   └── seeders/            # Seeders para dados iniciais
├── resources/
│   ├── css/                # Estilos CSS
│   ├── js/                 # JavaScript
│   └── views/              # Templates Blade
├── routes/
│   ├── web.php             # Rotas web
│   └── api.php             # Rotas da API
└── public/                 # Arquivos públicos
```

## 🔧 Configuração RFID

### Hardware Necessário

### Conexões
```
Arduino -> RC522
GND     -> GND
3.3V    -> 3.3V
D9      -> RST
D10     -> SDA
D11     -> MOSI
D12     -> MISO
D13     -> SCK
```

### Código Arduino
O código para o Arduino está disponível na documentação do sistema.

## 🎨 Temas e Personalização

O sistema possui tema escuro moderno com:

## 📊 Modelos de Dados

### Principais Entidades

## � Segurança


## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=BookTest
```

## 📈 Performance


## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte e dúvidas:

## 🎯 Roadmap



**BiblioFlash** - Transformando a experiência de biblioteca com tecnologia moderna! 📚✨
=======
# biblioflash
biblioflash
>>>>>>> origin/main
# �📚 BiblioFlash - Sistema de Gerenciamento de Biblioteca

![BiblioFlash Logo](public/biblio-flash/logo-of.png)

## 🌟 Sobre o Projeto

O **BiblioFlash** é um sistema completo de gerenciamento de biblioteca desenvolvido em Laravel, que oferece uma experiência moderna e intuitiva para bibliotecários e usuários. O sistema integra tecnologia RFID para identificação automática de livros e possui um sistema de tótem para autoatendimento.

## ✨ Funcionalidades Principais

### 👥 Gestão de Usuários
- **Três tipos de usuário**: Admin, Bibliotecário e Cliente
- Sistema de autenticação seguro
- Perfis personalizados com estatísticas individuais
- Controle de permissões baseado em roles

### 📖 Gerenciamento de Livros
- Cadastro completo de livros com informações detalhadas
- Sistema de categorização
- Controle de estoque e disponibilidade
- Upload de capas de livros
- Sistema de avaliações e comentários
- Busca avançada por título, autor, ISBN ou categoria

### 🔄 Sistema de Empréstimos
- Empréstimos automatizados com controle de prazos
- Sistema de renovações
- Controle de multas por atraso
- Histórico completo de empréstimos
- Notificações de vencimento

### 📋 Sistema de Reservas
- Reserva de livros indisponíveis
- Fila de espera automática
- Notificações quando livros ficam disponíveis
- Controle de prazo para retirada

### 🏷️ Tecnologia RFID
- **Painel RFID** para identificação automática de livros
- Integração com Arduino para leitura de tags RFID
- Interface web para conexão com dispositivos seriais
- Sistema de detecção em tempo real
- Log de atividades RFID

### 🖥️ Sistema de Tótem
- **Autoatendimento** para empréstimos e devoluções
- Interface touch-friendly otimizada
- Autenticação por CPF
- Leitura RFID integrada
- Operação independente

### 📊 Dashboard Administrativo
- Estatísticas em tempo real
- Gráficos de empréstimos por período
- Top livros mais emprestados
- Controle de usuários ativos
- Relatórios de multas e atrasos

### ⭐ Funcionalidades Extras
- Sistema de favoritos
- Recomendações personalizadas
- Múltiplas bibliotecas
- Sistema de multas automatizado
- Log de atividades completo
- Configurações do sistema personalizáveis

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **PHP 8.2+** - Linguagem de programação
- **SQLite** - Banco de dados (configurável para MySQL/PostgreSQL)
- **Eloquent ORM** - Mapeamento objeto-relacional

### Frontend
- **Blade Templates** - Sistema de templates do Laravel
- **Bootstrap 5** - Framework CSS
- **Vite** - Build tool e bundler
- **TailwindCSS 4** - Framework CSS utilitário
- **Chart.js** - Gráficos e visualizações
- **Font Awesome** - Ícones

### Integração Hardware
- **Web Serial API** - Comunicação com Arduino
- **Arduino** - Microcontrolador para RFID
- **RFID RC522** - Leitor de tags RFID

### Ferramentas de Desenvolvimento
- **Composer** - Gerenciador de dependências PHP
- **NPM** - Gerenciador de pacotes JavaScript
- **Laravel Tinker** - REPL para Laravel
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Framework de testes

## 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js 18+ e NPM
- SQLite (ou MySQL/PostgreSQL)
- Servidor web (Apache/Nginx) ou Laravel Sail

## 🚀 Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/biblioflash.git
cd biblioflash/site
```

### 2. Instale as dependências PHP
```bash
composer install
```

### 3. Instale as dependências JavaScript
```bash
npm install
```

### 4. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o banco de dados
Edite o arquivo `.env` com suas configurações de banco de dados:
```env
DB_CONNECTION=sqlite
# Para SQLite, o arquivo será criado automaticamente

# Ou para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=biblioflash
# DB_USERNAME=root
# DB_PASSWORD=
```

### 6. Execute as migrações
```bash
php artisan migrate
```

### 7. Execute os seeders (opcional)
```bash
php artisan db:seed
```

### 8. Crie o usuário administrador
```bash
php artisan db:seed --class=AdminUserSeeder
```

### 9. Compile os assets
```bash
npm run build
```

### 10. Inicie o servidor
```bash
php artisan serve
```

O sistema estará disponível em `http://localhost:8000`

## 👤 Credenciais Padrão

**Administrador:**
- Email: `adm@adm.com`
- Senha: `adm`

## 📱 Como Usar

### Para Administradores
1. Acesse o sistema com as credenciais de admin
2. Use o **Dashboard Administrativo** para visão geral
3. Gerencie livros em **Gerenciar Livros**
4. Use o **Painel RFID** para identificação automática
5. Controle usuários, empréstimos e reservas

### Para Bibliotecários
1. Acesse com credenciais de bibliotecário
2. Gerencie empréstimos e devoluções
3. Atenda reservas de usuários
4. Use o sistema de tótem para autoatendimento

### Para Clientes
1. Registre-se no sistema
2. Navegue pelo catálogo de livros
3. Faça reservas de livros
4. Acompanhe seus empréstimos no perfil
5. Use o tótem para autoatendimento

### Sistema RFID
1. Acesse **Admin > Painel RFID**
2. Conecte o Arduino via porta serial
3. Aproxime livros com tags RFID
4. O sistema identificará automaticamente

### Sistema de Tótem
1. Acesse `/totem` no navegador
2. Faça login com CPF
3. Use para empréstimos ou devoluções
4. Siga as instruções na tela

## 🗂️ Estrutura do Projeto

```
biblioflash/site/
├── app/
│   ├── Http/Controllers/     # Controllers da aplicação
│   ├── Models/              # Modelos Eloquent
│   ├── Policies/            # Políticas de autorização
│   └── Providers/           # Service providers
├── database/
│   ├── migrations/          # Migrações do banco
│   └── seeders/            # Seeders para dados iniciais
├── resources/
│   ├── css/                # Estilos CSS
│   ├── js/                 # JavaScript
│   └── views/              # Templates Blade
├── routes/
│   ├── web.php             # Rotas web
│   └── api.php             # Rotas da API
└── public/                 # Arquivos públicos
```

## 🔧 Configuração RFID

### Hardware Necessário
- Arduino Uno/Nano
- Módulo RFID RC522
- Tags RFID (cartões ou etiquetas)
- Cabos jumper

### Conexões
```
Arduino -> RC522
GND     -> GND
3.3V    -> 3.3V
D9      -> RST
D10     -> SDA
D11     -> MOSI
D12     -> MISO
D13     -> SCK
```

### Código Arduino
O código para o Arduino está disponível na documentação do sistema.

## 🎨 Temas e Personalização

O sistema possui tema escuro moderno com:
- Gradientes e efeitos visuais
- Animações suaves
- Interface responsiva
- Componentes personalizados

## 📊 Modelos de Dados

### Principais Entidades
- **User** - Usuários do sistema
- **Book** - Livros do acervo
- **Category** - Categorias de livros
- **Loan** - Empréstimos
- **Reservation** - Reservas
- **Fine** - Multas
- **BookReview** - Avaliações
- **Library** - Bibliotecas
- **ActivityLog** - Log de atividades

## 🔒 Segurança

- Autenticação baseada em sessões
- Autorização com policies
- Proteção CSRF
- Validação de dados
- Sanitização de inputs
- Controle de acesso por roles

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=BookTest
```

## 📈 Performance

- Eager loading para relacionamentos
- Cache de consultas frequentes
- Otimização de assets com Vite
- Compressão de imagens
- Índices de banco otimizados

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte e dúvidas:
- Abra uma issue no GitHub
- Entre em contato através do sistema

## 🎯 Roadmap

- [ ] API REST completa
- [ ] Aplicativo mobile
- [ ] Integração com sistemas externos
- [ ] Relatórios avançados
- [ ] Sistema de notificações push
- [ ] Integração com e-books

---

**BiblioFlash** - Transformando a experiência de biblioteca com tecnologia moderna! 📚✨
