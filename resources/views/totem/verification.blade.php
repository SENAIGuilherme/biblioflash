@extends('layouts.totem')

@php
$totemLayout = true;
@endphp
@section('title', 'Verificação de Livro')
@section('content')

<div class="verification-card">
    <div class="totem-logo">
        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="Logo Biblioteca">
    </div>
    <h2>Verificação de Livro (Segurança)</h2>

    <div class="verification-row">
        <div class="left-panel">
            <video id="video" autoplay muted playsinline></video>
            <div id="buttons">
                <h3 id="stepTitle" class="step-title">Etapa 1: Apresente a capa do livro</h3>
                <button id="btnCapturarCapa">Capturar Capa</button>
                <h3 id="stepTitle2" class="step-title">Etapa 2: Apresente o verso do livro</h3>
                <button id="btnCapturarVerso">Capturar Verso</button>
                <h3 id="stepTitle3" class="step-title">Etapa 3: Folhear o livro por 10 segundos</h3>
                <button id="btnIniciarFolhear">Iniciar Folhear</button>
                <p id="contador"></p>
                <p id="status">Aguardando...</p>
            </div>
        </div>

        <div class="right-panel capture-box">
            <div>
                <div class="capture-label">📘 Capa Capturada:</div>
                <img id="imgCapa" alt="Capa capturada" />
            </div>
            <div>
                <div class="capture-label">📙 Verso Capturado:</div>
                <img id="imgVerso" alt="Verso capturado" />
            </div>
            <div>
                <div class="capture-label">📹 Folhear Gravado:</div>
                <video id="videoFolhear" controls autoplay muted></video>
            </div>
        </div>
    </div>

    <canvas id="canvas" width="400" height="300"></canvas>
</div>

<!-- Modal de verificação de reserva -->
<div id="reservationModal" class="reservation-modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeReservationModal()">&times;</span>
        <h2 class="modal-title">Verificação de Reserva</h2>
        <div id="reservationBody" class="modal-body"></div>
        <button id="btnConfirmLoan" onclick="confirmLoan()" class="confirm-btn">Finalizar Empréstimo</button>
        <div id="reservationMessage" class="error-message"></div>
    </div>
</div>

<script>
    // --- Lógica da webcam e verificação visual automática para múltiplos livros ---
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    const imgCapa = document.getElementById('imgCapa');
    const imgVerso = document.getElementById('imgVerso');
    const videoFolhear = document.getElementById('videoFolhear');

    const btnCapturarCapa = document.getElementById('btnCapturarCapa');
    const btnCapturarVerso = document.getElementById('btnCapturarVerso');
    const btnIniciarFolhear = document.getElementById('btnIniciarFolhear');

    const status = document.getElementById('status');
    const contadorEl = document.getElementById('contador');

    const stepTitle = document.getElementById('stepTitle');
    const stepTitle2 = document.getElementById('stepTitle2');
    const stepTitle3 = document.getElementById('stepTitle3');

    let mediaStream = null;
    let mediaRecorder = null;
    let recordedChunks = [];

    let folheando = false;
    let contador = 10;
    let timerFolhear = null;

    // --- Suporte a múltiplos livros ---
    let livros = [];
    let livroAtual = 0;
    let capturas = [];

    // Recupera lista de livros da query string
    function getLivrosFromQuery() {
        const params = new URLSearchParams(window.location.search);
        let livrosParam = params.get('livros');
        if (!livrosParam) return [];
        // Formato: id1,isbn1,titulo1;id2,isbn2,titulo2
        return livrosParam.split(';').map(str => {
            const [id, isbn, ...tituloArr] = str.split(',');
            return {
                id,
                isbn,
                titulo: tituloArr.join(',')
            };
        });
    }

    async function startWebcam() {
        status.textContent = 'Iniciando câmera...';
        status.className = '';

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            status.textContent = 'Este dispositivo ou navegador não suporta acesso à câmera.';
            status.className = 'red';
            return;
        }

        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: {
                        ideal: 640
                    },
                    height: {
                        ideal: 480
                    },
                    facingMode: 'environment'
                }
            });
            video.srcObject = mediaStream;

            // Inicia o fluxo automático após a webcam estar pronta
            setTimeout(() => startLivro(0), 1200);
        } catch (err) {
            status.textContent = 'Erro ao acessar webcam: ' + (err.message || err);
            status.className = 'red';
            console.error('Erro webcam:', err);
        }
    }

    function startLivro(idx) {
        livroAtual = idx;
        let livro = livros[livroAtual];

        stepTitle.textContent = `Etapa 1: Apresente a capa do livro (${livro.titulo || livro.isbn})`;
        stepTitle.style.display = 'block';
        btnCapturarCapa.style.display = 'none';
        stepTitle2.style.display = 'none';
        btnCapturarVerso.style.display = 'none';
        stepTitle3.style.display = 'none';
        btnIniciarFolhear.style.display = 'none';
        contadorEl.style.display = 'none';
        status.textContent = 'Aguardando...';
        status.className = '';

        imgCapa.src = '';
        imgVerso.src = '';
        videoFolhear.style.display = 'none';

        setTimeout(autoCaptureCapa, 1200);
    }

    function captureImage() {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/png');
    }

    // Função para detectar se há "livro" (imagem não escura)
    function isBookPresent(threshold = 40) {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        let total = 0;
        for (let i = 0; i < imageData.data.length; i += 4) {
            // média dos canais RGB
            total += (imageData.data[i] + imageData.data[i + 1] + imageData.data[i + 2]) / 3;
        }
        const avg = total / (canvas.width * canvas.height);
        return avg > threshold;
    }

    // Aguarda até detectar o livro na frente da câmera
    function waitForBook(instruction, callback) {
        status.textContent = 'Aguardando livro na frente da câmera...';
        stepTitle.textContent = instruction;
        stepTitle.style.display = 'block';

        let interval = setInterval(() => {
            if (isBookPresent()) {
                clearInterval(interval);
                status.textContent = 'Livro detectado!';
                status.className = 'green';
                setTimeout(callback, 800);
            }
        }, 400);
    }

    // Contagem regressiva visual
    function countdown(seconds, onTick, onEnd) {
        contadorEl.style.display = 'block';
        let s = seconds;
        contadorEl.textContent = `Captura em: ${s}`;

        let timer = setInterval(() => {
            s--;
            contadorEl.textContent = `Captura em: ${s}`;
            if (onTick) onTick(s);
            if (s <= 0) {
                clearInterval(timer);
                contadorEl.style.display = 'none';
                if (onEnd) onEnd();
            }
        }, 1000);
    }

    function autoCaptureCapa() {
        waitForBook('Etapa 1: Apresente a capa do livro', () => {
            status.textContent = 'Posicione a capa. Preparando captura...';
            status.className = '';

            countdown(5, null, () => {
                status.textContent = 'Capturando capa...';
                const imgData = captureImage();
                imgCapa.src = imgData;
                status.textContent = 'Capa capturada!';
                status.className = 'green';

                setTimeout(() => {
                    stepTitle2.style.display = 'block';
                    btnCapturarVerso.style.display = 'none';
                    status.textContent = 'Vire o livro para o verso.';
                    status.className = '';
                    setTimeout(autoCaptureVerso, 1200);
                }, 1200);
            });
        });
    }

    function autoCaptureVerso() {
        waitForBook('Etapa 2: Apresente o verso do livro', () => {
            status.textContent = 'Posicione o verso. Preparando captura...';
            status.className = '';

            countdown(5, null, () => {
                status.textContent = 'Capturando verso...';
                const imgData = captureImage();
                imgVerso.src = imgData;
                status.textContent = 'Verso capturado!';
                status.className = 'green';

                btnCapturarVerso.style.display = 'none';
                stepTitle2.style.display = 'none';

                setTimeout(() => {
                    stepTitle3.style.display = 'block';
                    btnIniciarFolhear.style.display = 'none';
                    status.textContent = 'Folheie o livro na frente da câmera.';
                    status.className = '';
                    setTimeout(autoStartFolhear, 1200);
                }, 1200);
            });
        });
    }

    function autoStartFolhear() {
        waitForBook('Etapa 3: Folheie o livro na frente da câmera', () => {
            status.textContent = 'Preparando gravação do folhear...';
            status.className = '';

            countdown(3, null, () => {
                if (folheando) return;

                folheando = true;
                contador = 10;
                contadorEl.style.display = 'block';
                contadorEl.textContent = `Tempo restante: ${contador} s`;
                status.textContent = 'Gravando vídeo do folhear...';
                status.className = '';
                btnIniciarFolhear.style.display = 'none';

                recordedChunks = [];
                mediaRecorder = new MediaRecorder(mediaStream, {
                    mimeType: 'video/webm'
                });

                mediaRecorder.ondataavailable = e => {
                    if (e.data.size > 0) recordedChunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    const blob = new Blob(recordedChunks, {
                        type: 'video/webm'
                    });
                    const url = URL.createObjectURL(blob);
                    videoFolhear.src = url;
                    videoFolhear.style.display = 'block';
                    videoFolhear.controls = true;
                    videoFolhear.autoplay = true;
                    videoFolhear.muted = false;
                    status.textContent = 'Gravação finalizada!';
                    status.className = 'green';
                    video.srcObject = mediaStream;

                    // Salva captura deste livro
                    capturas.push({
                        id: livros[livroAtual].id,
                        isbn: livros[livroAtual].isbn,
                        titulo: livros[livroAtual].titulo,
                        capa: imgCapa.src,
                        verso: imgVerso.src,
                        video: blob
                    });

                    // Próximo livro ou finalizar
                    if (livroAtual + 1 < livros.length) {
                        setTimeout(() => startLivro(livroAtual + 1), 1500);
                    } else {
                        finalizarVerificacao();
                    }
                };

                mediaRecorder.start();

                timerFolhear = setInterval(() => {
                    contador--;
                    contadorEl.textContent = `Tempo restante: ${contador} s`;

                    if (contador <= 0) {
                        clearInterval(timerFolhear);
                        contadorEl.style.display = 'none';
                        mediaRecorder.stop();
                        folheando = false;
                        stepTitle3.textContent = 'Processo concluído!';
                    }
                }, 1000);
            });
        });
    }

    // Finaliza verificação e processa empréstimo
    function finalizarVerificacao() {
        const params = new URLSearchParams(window.location.search);
        const cliente_id = params.get('cliente_id');
        const livrosArray = capturas.map(cap => parseInt(cap.id)).filter(id => !isNaN(id));

        if (!cliente_id || livrosArray.length === 0) {
            alert('Erro: cliente ou livros não encontrados. Faça login novamente.');
            window.location.href = '{{ route("totem.login") }}';
            return;
        }

        status.textContent = 'Finalizando empréstimo...';
        status.className = '';

        // Chama a API de empréstimo
        fetch('/api/totem/finalize-loan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    cliente_id,
                    livros: livrosArray
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("totem.loan-success") }}';
                } else {
                    let msg = 'Erro ao registrar empréstimo: ' + (data.message || data.msg || 'Erro desconhecido.');
                    if (data.error) msg += '\nDetalhe: ' + data.error;
                    if (data.nao_reservados) msg += '\nLivros não reservados: ' + JSON.stringify(data.nao_reservados);
                    if (data.errors) msg += '\nValidação: ' + JSON.stringify(data.errors);
                    alert(msg);
                    status.textContent = 'Erro ao finalizar empréstimo';
                    status.className = 'red';
                }
            })
            .catch(err => {
                console.error('Erro ao finalizar empréstimo:', err);
                alert('Erro ao finalizar empréstimo: ' + err);
                status.textContent = 'Erro de conexão';
                status.className = 'red';
            });
    }

    // Funções do modal de reserva
    function showReservationModal(resultados, livrosInfo) {
        const modal = document.getElementById('reservationModal');
        const body = document.getElementById('reservationBody');
        const btnFinalizar = document.getElementById('btnConfirmLoan');

        let todosReservados = true;
        let html = '<ul>';

        resultados.forEach(res => {
            const livro = livrosInfo.find(l => l.id == res.id);
            if (res.reservado) {
                html += `<li class="reserved"><b>✔</b> ${livro ? livro.titulo : 'Livro'} <span>(Reservado)</span></li>`;
            } else {
                html += `<li class="not-reserved"><b>✖</b> ${livro ? livro.titulo : 'Livro'} <span>(Não reservado para você)</span></li>`;
                todosReservados = false;
            }
        });

        html += '</ul>';
        body.innerHTML = html;

        if (todosReservados) {
            btnFinalizar.style.display = 'inline-block';
            btnFinalizar.disabled = false;
        } else {
            btnFinalizar.style.display = 'none';
            btnFinalizar.disabled = true;
        }

        modal.style.display = 'flex';
    }

    function closeReservationModal() {
        document.getElementById('reservationModal').style.display = 'none';
    }

    function confirmLoan() {
        closeReservationModal();
        finalizarVerificacao();
    }

    // Garante que o cliente_id está na URL
    function garantirClienteIdNaUrl() {
        const params = new URLSearchParams(window.location.search);
        let cliente_id = params.get('cliente_id');

        if (!cliente_id || isNaN(Number(cliente_id))) {
            // Tenta buscar o cliente logado via endpoint backend
            fetch('/api/cliente-logado', {
                    credentials: 'same-origin'
                })
                .then(resp => resp.json())
                .then(data => {
                    if (data && data.id) {
                        cliente_id = data.id;
                        params.set('cliente_id', cliente_id);
                        window.location.search = params.toString();
                    } else {
                        alert('ATENÇÃO: Não foi possível identificar o cliente logado. Faça login novamente.');
                        window.location.href = '{{ route("totem.login") }}';
                    }
                })
                .catch(() => {
                    alert('Erro ao tentar identificar o cliente logado.');
                    window.location.href = '{{ route("totem.login") }}';
                });
        }
    }

    // Inicialização
    livros = getLivrosFromQuery();
    if (!livros.length) {
        // fallback: 1 livro genérico
        livros = [{
            id: 1,
            isbn: '1',
            titulo: 'Livro'
        }];
    }

    capturas = [];
    garantirClienteIdNaUrl();
    startWebcam();

    console.log('URL params:', window.location.search);
    console.log('Livros carregados:', livros);
</script>
@endsection