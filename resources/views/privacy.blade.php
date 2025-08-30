@extends('layouts.public')

@section('title', 'Política de Privacidade - BiblioFlash')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="h2 mb-4 text-center text-primary">Política de Privacidade</h1>
                    
                    <div class="content-section">
                        <h3 class="h4 mb-3">1. Informações que Coletamos</h3>
                        <p>O BiblioFlash coleta as seguintes informações:</p>
                        <ul>
                            <li>Dados pessoais fornecidos durante o cadastro (nome, email, CPF, telefone, endereço)</li>
                            <li>Informações de uso da plataforma (histórico de empréstimos, reservas, avaliações)</li>
                            <li>Dados de navegação e cookies para melhorar a experiência do usuário</li>
                        </ul>
                    </div>

                    <div class="content-section mt-4">
                        <h3 class="h4 mb-3">2. Como Utilizamos suas Informações</h3>
                        <p>Utilizamos suas informações para:</p>
                        <ul>
                            <li>Gerenciar sua conta e fornecer nossos serviços</li>
                            <li>Processar empréstimos e reservas de livros</li>
                            <li>Enviar notificações sobre prazos e multas</li>
                            <li>Melhorar nossos serviços e recomendar conteúdo relevante</li>
                            <li>Cumprir obrigações legais e regulamentares</li>
                        </ul>
                    </div>

                    <div class="content-section mt-4">
                        <h3 class="h4 mb-3">3. Compartilhamento de Informações</h3>
                        <p>Não vendemos, alugamos ou compartilhamos suas informações pessoais com terceiros, exceto:</p>
                        <ul>
                            <li>Quando necessário para cumprir obrigações legais</li>
                            <li>Com prestadores de serviços que nos auxiliam na operação da plataforma</li>
                            <li>Em caso de fusão, aquisição ou venda de ativos da empresa</li>
                        </ul>
                    </div>

                    <div class="content-section mt-4">
                        <h3 class="h4 mb-3">4. Segurança dos Dados</h3>
                        <p>Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações contra acesso não autorizado, alteração, divulgação ou destruição.</p>
                    </div>

                    <div class="content-section mt-4">
                        <h3 class="h4 mb-3">5. Seus Direitos</h3>
                        <p>Você tem o direito de:</p>
                        <ul>
                            <li>Acessar e atualizar suas informações pessoais</li>
                            <li>Solicitar a exclusão de seus dados</li>
                            <li>Retirar o consentimento para o processamento de dados</li>
                            <li>Solicitar a portabilidade de seus dados</li>
                        </ul>
                    </div>

                    <div class="content-section mt-4">
                        <h3 class="h4 mb-3">6. Cookies</h3>
                        <p>Utilizamos cookies para melhorar sua experiência de navegação. Você pode configurar seu navegador para recusar cookies, mas isso pode afetar a funcionalidade do site.</p>
                    </div>

                    <div class="content-section mt-4">
                        <h3 class="h4 mb-3">7. Alterações nesta Política</h3>
                        <p>Podemos atualizar esta política periodicamente. Notificaremos sobre mudanças significativas através do email cadastrado ou por meio de avisos no site.</p>
                    </div>

                    <div class="content-section mt-4">
                        <h3 class="h4 mb-3">8. Contato</h3>
                        <p>Para questões sobre esta política de privacidade, entre em contato conosco através da página de <a href="{{ route('contact') }}" class="text-primary">contato</a>.</p>
                    </div>

                    <div class="text-muted mt-5">
                        <small>Última atualização: {{ date('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-css')
@vite('resources/css/pages/privacy.css')
@endpush