@extends('layouts.admin')

@section('title', 'Painel RFID')

@push('page-css')
@vite('resources/css/pages/rfid-panel.css')
@endpush

@section('content')
<div class="rfid-panel-container">
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-microchip"></i>
                    Painel RFID
                </h1>
                <p class="page-subtitle">Sistema de detecção e identificação de livros via RFID</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>

        <!-- Connection Panel -->
        <div class="connection-panel">
            <div class="connection-card">
                <div class="connection-header">
                    <h3 class="connection-title">
                        <i class="fas fa-wifi"></i>
                        Conexão Arduino
                    </h3>
                    <div class="connection-status" id="connectionStatus">
                        <span class="status-indicator disconnected"></span>
                        <span class="status-text">Desconectado</span>
                    </div>
                </div>
                
                <div class="connection-controls">
                    <div class="port-selection">
                        <label for="portSelect">Porta Serial:</label>
                        <select id="portSelect" class="form-control">
                            <option value="">Selecione uma porta...</option>
                        </select>
                        <button id="refreshPorts" class="btn btn-outline">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    
                    <div class="connection-buttons">
                        <button id="connectBtn" class="btn btn-primary">
                            <i class="fas fa-plug"></i>
                            Conectar
                        </button>
                        <button id="disconnectBtn" class="btn btn-danger" disabled>
                            <i class="fas fa-times"></i>
                            Desconectar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RFID Detection Panel -->
        <div class="detection-panel">
            <div class="detection-card">
                <div class="detection-header">
                    <h3 class="detection-title">
                        <i class="fas fa-search"></i>
                        Detecção RFID
                    </h3>
                    <div class="detection-status" id="detectionStatus">
                        <span class="status-indicator waiting"></span>
                        <span class="status-text">Aguardando...</span>
                    </div>
                </div>
                
                <div class="rfid-display">
                    <div class="rfid-scanner">
                        <div class="scanner-animation" id="scannerAnimation">
                            <div class="scanner-line"></div>
                        </div>
                        <div class="scanner-text">
                            <i class="fas fa-id-card"></i>
                            <p>Aproxime o cartão RFID do leitor</p>
                        </div>
                    </div>
                    
                    <div class="rfid-info" id="rfidInfo" style="display: none;">
                        <div class="rfid-code">
                            <label>Código RFID:</label>
                            <span id="rfidCode">-</span>
                        </div>
                        <div class="detection-time">
                            <label>Detectado em:</label>
                            <span id="detectionTime">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Information Panel -->
        <div class="book-info-panel" id="bookInfoPanel" style="display: none;">
            <div class="book-info-card">
                <div class="book-info-header">
                    <h3 class="book-info-title">
                        <i class="fas fa-book"></i>
                        Informações do Livro
                    </h3>
                    <button id="clearInfo" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Limpar
                    </button>
                </div>
                
                <div class="book-details" id="bookDetails">
                    <div class="book-cover-section">
                        <div class="book-cover" id="bookCover">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    
                    <div class="book-info-section">
                        <div class="book-main-info">
                            <h4 class="book-title" id="bookTitle">-</h4>
                            <p class="book-author" id="bookAuthor">-</p>
                            <p class="book-category" id="bookCategory">-</p>
                        </div>
                        
                        <div class="book-additional-info">
                            <div class="info-row">
                                <span class="info-label">ISBN:</span>
                                <span class="info-value" id="bookIsbn">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Editora:</span>
                                <span class="info-value" id="bookPublisher">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ano:</span>
                                <span class="info-value" id="bookYear">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                <span class="info-value status-badge" id="bookStatus">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Localização:</span>
                                <span class="info-value" id="bookLocation">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="book-actions">
                    <a href="#" id="viewBookBtn" class="btn btn-primary" target="_blank">
                        <i class="fas fa-eye"></i>
                        Visualizar
                    </a>
                    <a href="#" id="editBookBtn" class="btn btn-secondary" target="_blank">
                        <i class="fas fa-edit"></i>
                        Editar
                    </a>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="activity-log-panel">
            <div class="activity-log-card">
                <div class="activity-log-header">
                    <h3 class="activity-log-title">
                        <i class="fas fa-history"></i>
                        Log de Atividades
                    </h3>
                    <button id="clearLog" class="btn btn-outline">
                        <i class="fas fa-trash"></i>
                        Limpar
                    </button>
                </div>
                
                <div class="activity-log-content" id="activityLog">
                    <div class="log-entry">
                        <span class="log-time">{{ date('H:i:s') }}</span>
                        <span class="log-message">Sistema iniciado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script>
// RFID Panel JavaScript
class RFIDPanel {
    constructor() {
        this.port = null;
        this.reader = null;
        this.isConnected = false;
        this.isScanning = false;
        
        this.initializeElements();
        this.bindEvents();
        this.checkWebSerialSupport();
        this.addLogEntry('Sistema iniciado');
    }
    
    initializeElements() {
        this.elements = {
            portSelect: document.getElementById('portSelect'),
            refreshPorts: document.getElementById('refreshPorts'),
            connectBtn: document.getElementById('connectBtn'),
            disconnectBtn: document.getElementById('disconnectBtn'),
            connectionStatus: document.getElementById('connectionStatus'),
            detectionStatus: document.getElementById('detectionStatus'),
            scannerAnimation: document.getElementById('scannerAnimation'),
            rfidInfo: document.getElementById('rfidInfo'),
            rfidCode: document.getElementById('rfidCode'),
            detectionTime: document.getElementById('detectionTime'),
            bookInfoPanel: document.getElementById('bookInfoPanel'),
            bookDetails: document.getElementById('bookDetails'),
            clearInfo: document.getElementById('clearInfo'),
            clearLog: document.getElementById('clearLog'),
            activityLog: document.getElementById('activityLog'),
            viewBookBtn: document.getElementById('viewBookBtn'),
            editBookBtn: document.getElementById('editBookBtn')
        };
    }
    
    bindEvents() {
        this.elements.refreshPorts.addEventListener('click', () => this.refreshPorts());
        this.elements.connectBtn.addEventListener('click', () => this.connect());
        this.elements.disconnectBtn.addEventListener('click', () => this.disconnect());
        this.elements.clearInfo.addEventListener('click', () => this.clearBookInfo());
        this.elements.clearLog.addEventListener('click', () => this.clearActivityLog());
    }
    
    checkWebSerialSupport() {
        if ('serial' in navigator) {
            this.addLogEntry('Web Serial API suportada');
            this.refreshPorts();
        } else {
            this.addLogEntry('Web Serial API não suportada neste navegador', 'error');
            this.elements.connectBtn.disabled = true;
        }
    }
    
    async refreshPorts() {
        try {
            const ports = await navigator.serial.getPorts();
            this.elements.portSelect.innerHTML = '<option value="">Selecione uma porta...</option>';
            
            ports.forEach((port, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = `Porta ${index + 1}`;
                this.elements.portSelect.appendChild(option);
            });
            
            if (ports.length === 0) {
                const option = document.createElement('option');
                option.value = 'request';
                option.textContent = 'Solicitar nova porta...';
                this.elements.portSelect.appendChild(option);
            }
            
            this.addLogEntry(`${ports.length} porta(s) encontrada(s)`);
        } catch (error) {
            this.addLogEntry(`Erro ao listar portas: ${error.message}`, 'error');
        }
    }
    
    async connect() {
        try {
            const selectedValue = this.elements.portSelect.value;
            
            if (selectedValue === 'request') {
                this.port = await navigator.serial.requestPort();
                this.addLogEntry('Nova porta selecionada pelo usuário');
            } else if (selectedValue) {
                const ports = await navigator.serial.getPorts();
                this.port = ports[parseInt(selectedValue)];
            } else {
                this.addLogEntry('Selecione uma porta primeiro', 'error');
                return;
            }
            
            await this.port.open({ baudRate: 9600 });
            this.isConnected = true;
            
            this.updateConnectionStatus(true);
            this.elements.connectBtn.disabled = true;
            this.elements.disconnectBtn.disabled = false;
            this.elements.portSelect.disabled = true;
            
            this.addLogEntry('Conectado ao Arduino com sucesso');
            this.startReading();
            
        } catch (error) {
            this.addLogEntry(`Erro na conexão: ${error.message}`, 'error');
        }
    }
    
    async disconnect() {
        try {
            if (this.reader) {
                await this.reader.cancel();
            }
            
            if (this.port) {
                await this.port.close();
            }
            
            this.isConnected = false;
            this.updateConnectionStatus(false);
            this.updateDetectionStatus('waiting');
            
            this.elements.connectBtn.disabled = false;
            this.elements.disconnectBtn.disabled = true;
            this.elements.portSelect.disabled = false;
            
            this.addLogEntry('Desconectado do Arduino');
            
        } catch (error) {
            this.addLogEntry(`Erro na desconexão: ${error.message}`, 'error');
        }
    }
    
    async startReading() {
        if (!this.port || !this.isConnected) return;
        
        try {
            this.updateDetectionStatus('scanning');
            this.reader = this.port.readable.getReader();
            
            while (this.isConnected) {
                const { value, done } = await this.reader.read();
                
                if (done) break;
                
                const textDecoder = new TextDecoder();
                const data = textDecoder.decode(value).trim();
                
                if (data && data.length > 0) {
                    this.handleRFIDDetection(data);
                }
            }
            
        } catch (error) {
            if (error.name !== 'NetworkError') {
                this.addLogEntry(`Erro na leitura: ${error.message}`, 'error');
            }
        } finally {
            if (this.reader) {
                this.reader.releaseLock();
            }
        }
    }
    
    handleRFIDDetection(rfidCode) {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        
        this.elements.rfidCode.textContent = rfidCode;
        this.elements.detectionTime.textContent = timeString;
        this.elements.rfidInfo.style.display = 'block';
        
        this.updateDetectionStatus('detected');
        this.addLogEntry(`RFID detectado: ${rfidCode}`);
        
        // Buscar informações do livro
        this.searchBookByRFID(rfidCode);
    }
    
    async searchBookByRFID(rfidCode) {
        try {
            const response = await fetch(`/api/books/rfid/${rfidCode}`);
            
            if (response.ok) {
                const book = await response.json();
                this.displayBookInfo(book);
                this.addLogEntry(`Livro encontrado: ${book.titulo}`);
            } else {
                this.addLogEntry(`Livro não encontrado para RFID: ${rfidCode}`, 'warning');
                this.clearBookInfo();
            }
            
        } catch (error) {
            this.addLogEntry(`Erro ao buscar livro: ${error.message}`, 'error');
        }
    }
    
    displayBookInfo(book) {
        // Atualizar capa
        const bookCover = document.getElementById('bookCover');
        if (book.capa) {
            bookCover.innerHTML = `<img src="/storage/${book.capa}" alt="${book.titulo}">`;
        } else {
            bookCover.innerHTML = '<i class="fas fa-book"></i>';
        }
        
        // Atualizar informações
        document.getElementById('bookTitle').textContent = book.titulo || '-';
        document.getElementById('bookAuthor').textContent = book.autor || '-';
        document.getElementById('bookCategory').textContent = book.category?.nome || '-';
        document.getElementById('bookIsbn').textContent = book.isbn || '-';
        document.getElementById('bookPublisher').textContent = book.editora || '-';
        document.getElementById('bookYear').textContent = book.ano_publicacao || '-';
        document.getElementById('bookLocation').textContent = book.localizacao || '-';
        
        // Status
        const statusElement = document.getElementById('bookStatus');
        statusElement.textContent = book.ativo ? 'Ativo' : 'Inativo';
        statusElement.className = `info-value status-badge ${book.ativo ? 'status-active' : 'status-inactive'}`;
        
        // Botões de ação
        this.elements.viewBookBtn.href = `/admin/books/${book.id}`;
        this.elements.editBookBtn.href = `/admin/books/${book.id}/edit`;
        
        // Mostrar painel
        this.elements.bookInfoPanel.style.display = 'block';
    }
    
    clearBookInfo() {
        this.elements.bookInfoPanel.style.display = 'none';
        this.elements.rfidInfo.style.display = 'none';
        this.updateDetectionStatus('waiting');
    }
    
    updateConnectionStatus(connected) {
        const indicator = this.elements.connectionStatus.querySelector('.status-indicator');
        const text = this.elements.connectionStatus.querySelector('.status-text');
        
        if (connected) {
            indicator.className = 'status-indicator connected';
            text.textContent = 'Conectado';
        } else {
            indicator.className = 'status-indicator disconnected';
            text.textContent = 'Desconectado';
        }
    }
    
    updateDetectionStatus(status) {
        const indicator = this.elements.detectionStatus.querySelector('.status-indicator');
        const text = this.elements.detectionStatus.querySelector('.status-text');
        
        switch (status) {
            case 'waiting':
                indicator.className = 'status-indicator waiting';
                text.textContent = 'Aguardando...';
                this.elements.scannerAnimation.classList.remove('active');
                break;
            case 'scanning':
                indicator.className = 'status-indicator scanning';
                text.textContent = 'Escaneando...';
                this.elements.scannerAnimation.classList.add('active');
                break;
            case 'detected':
                indicator.className = 'status-indicator detected';
                text.textContent = 'RFID Detectado';
                this.elements.scannerAnimation.classList.remove('active');
                break;
        }
    }
    
    addLogEntry(message, type = 'info') {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        
        const logEntry = document.createElement('div');
        logEntry.className = `log-entry log-${type}`;
        logEntry.innerHTML = `
            <span class="log-time">${timeString}</span>
            <span class="log-message">${message}</span>
        `;
        
        this.elements.activityLog.appendChild(logEntry);
        this.elements.activityLog.scrollTop = this.elements.activityLog.scrollHeight;
        
        // Limitar a 100 entradas
        const entries = this.elements.activityLog.querySelectorAll('.log-entry');
        if (entries.length > 100) {
            entries[0].remove();
        }
    }
    
    clearActivityLog() {
        this.elements.activityLog.innerHTML = '';
        this.addLogEntry('Log limpo');
    }
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    new RFIDPanel();
});
</script>
@endpush