@extends('layouts.admin')
@section('title', 'Editar Livro')

@push('layout-css')
    @vite(['resources/css/pages/book-register.css'])
@endpush

@section('content')
<div class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">Editar Livro</h1>
            <p class="dashboard-subtitle">Atualize as informações do livro "{{ $book->titulo }}"</p>
        </div>

        <div class="form-cadastrar-livro">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo', $book->titulo) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="autor" class="form-label">Autor *</label>
                        <input type="text" class="form-control" id="autor" name="autor" value="{{ old('autor', $book->autor) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Categoria *</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="editora" class="form-label">Editora</label>
                        <input type="text" class="form-control" id="editora" name="editora" value="{{ old('editora', $book->editora) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ano_publicacao" class="form-label">Ano de Publicação</label>
                        <input type="number" class="form-control" id="ano_publicacao" name="ano_publicacao" value="{{ old('ano_publicacao', $book->ano_publicacao) }}" min="1000" max="{{ date('Y') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="paginas" class="form-label">Páginas</label>
                        <input type="number" class="form-control" id="paginas" name="paginas" value="{{ old('paginas', $book->paginas) }}" min="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="isbn" class="form-label">ISBN (leia a TAG RFID ou digite) *</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" autocomplete="off" placeholder="Aproxime a TAG RFID ou digite o ISBN" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantidade_total" class="form-label">Quantidade Total *</label>
                        <input type="number" class="form-control" id="quantidade_total" name="quantidade_total" value="{{ old('quantidade_total', $book->quantidade_total) }}" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" value="{{ old('localizacao', $book->localizacao) }}" placeholder="Ex: Estante A, Prateleira 3">
                    </div>
                </div>

                <div class="form-group">
                    <label for="sinopse" class="form-label">Sinopse</label>
                    <textarea class="form-control" id="sinopse" name="sinopse" rows="4" placeholder="Descrição do livro...">{{ old('sinopse', $book->sinopse) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="capa" class="form-label">Foto da Capa</label>
                    @if($book->capa)
                        <div class="current-cover">
                            <p class="current-cover-label">Capa atual:</p>
                            <img src="{{ asset('storage/' . $book->capa) }}" alt="{{ $book->titulo }}" class="current-cover-image">
                        </div>
                    @endif
                    <input type="file" class="form-control" id="capa" name="capa" accept="image/*">
                    <small class="form-text">Deixe em branco para manter a capa atual</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="form-check-group">
                        <label class="form-check">
                            <input type="radio" name="ativo" value="1" {{ old('ativo', $book->ativo) == 1 ? 'checked' : '' }}>
                            <span class="form-check-label">Ativo</span>
                        </label>
                        <label class="form-check">
                            <input type="radio" name="ativo" value="0" {{ old('ativo', $book->ativo) == 0 ? 'checked' : '' }}>
                            <span class="form-check-label">Inativo</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Salvar Alterações
                    </button>
                    
                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar à Lista
                    </a>
                    
                    <a href="{{ route('admin.books.show', $book) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i>
                        Visualizar
                    </a>
                </div>
            </form>
            
            <!-- RFID Controls -->
            <div class="rfid-controls">
                <button onclick="conectarArduino()" class="btn-rfid">
                    <i class="fas fa-plug"></i>
                    Conectar Arduino
                </button>
                <button onclick="gravarISBNNaTag()" class="btn-rfid btn-rfid-write">
                    <i class="fas fa-save"></i>
                    Gravar ISBN na TAG
                </button>
            </div>
            
            <div id="rfid-alert" class="rfid-alert"></div>
        </div>
    </div>
</div>

<script>
let portaSerial = null;
let leitor = null;
let uidAtual = "";

// Foco inicial no ISBN se vazio
const isbnInput = document.getElementById('isbn');
if(isbnInput && !isbnInput.value) isbnInput.focus();

// Conectar ao Arduino
async function conectarArduino() {
  try {
    portaSerial = await navigator.serial.requestPort();
    await portaSerial.open({ baudRate: 9600 });
    leitor = portaSerial.readable.getReader();
    lerRFID();
    mostrarAlerta('Conectado ao Arduino. Aproxime a TAG para ler o ISBN.', '#1e90ff');
  } catch (err) {
    mostrarAlerta('Erro ao conectar: ' + err, '#e74c3c');
  }
}

// Leitura da TAG RFID: preenche o campo ISBN
async function lerRFID() {
  while (portaSerial && leitor) {
    const { value, done } = await leitor.read();
    if (done) break;
    const texto = new TextDecoder().decode(value).trim();
    if (texto && texto !== uidAtual) {
      uidAtual = texto;
      if (isbnInput) {
        isbnInput.value = uidAtual;
        isbnInput.focus();
      }
      mostrarAlerta('ISBN lido da TAG: ' + uidAtual, '#00e676');
    }
  }
}

// Grava o valor do campo ISBN na TAG via Arduino
async function gravarISBNNaTag() {
  if (!portaSerial || !portaSerial.writable) {
    mostrarAlerta('Conecte ao Arduino antes de gravar.', '#e74c3c');
    return;
  }
  const valor = isbnInput.value.trim();
  if (!valor) {
    mostrarAlerta('Digite ou leia um ISBN para gravar.', '#e74c3c');
    return;
  }
  try {
    const writer = portaSerial.writable.getWriter();
    await writer.write(new TextEncoder().encode(valor + "\n"));
    writer.releaseLock();
    mostrarAlerta('ISBN enviado para gravação na TAG: ' + valor, '#00e676');
  } catch (err) {
    mostrarAlerta('Erro ao gravar na TAG: ' + err, '#e74c3c');
  }
}

// Feedback visual
function mostrarAlerta(msg, cor) {
  const alertDiv = document.getElementById('rfid-alert');
  alertDiv.innerText = msg;
  alertDiv.style.color = cor;
  alertDiv.style.display = 'block';
  setTimeout(() => { alertDiv.style.display = 'none'; }, 3500);
}
</script>
@endsection