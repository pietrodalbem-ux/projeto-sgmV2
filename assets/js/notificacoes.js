async function carregarNotificacoes() {
    try {
        const res = await fetch('api/notificacoes.php?acao=listar');
        const data = await res.json();
        
        if (data.success) {
            const badge = document.getElementById('notif-badge');
            const container = document.getElementById('notif-items');
            
            // Atualiza badge
            if (data.unreadCount > 0) {
                badge.innerText = data.unreadCount;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
            
            // Atualiza lista
            if (data.notificacoes.length === 0) {
                container.innerHTML = '<li class="p-4 text-center text-muted small">Nenhuma notificação recente</li>';
                return;
            }
            
            container.innerHTML = data.notificacoes.map(n => `
                <li class="p-3 border-bottom position-relative ${n.lida == 0 ? 'bg-light' : ''}" style="transition: all 0.2s;">
                    <a href="${n.link || '#'}" class="text-decoration-none" onclick="marcarComoLida(${n.id_notificacao})">
                        <div class="d-flex align-items-start gap-2">
                            <div class="mt-1">
                                <i class="ph ph-chat-text text-primary"></i>
                            </div>
                            <div style="flex: 1;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1 small fw-bold text-main">${n.titulo}</h6>
                                   
                                </div>
                                <p class="mb-0 small text-muted" style="line-height: 1.2;">${n.mensagem}</p>
                                 <small class="text-muted " style="font-size: 0.65rem;">${new Date(n.data_criacao).toLocaleDateString()}</small>
                            </div>
                        </div>
                    </a>
                    <button class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 p-1 mt-1 me-1 " onclick="excluirNotificacao(event, ${n.id_notificacao})">
                        <i class="bi bi-trash-fill text-dark " style="font-size: 0.8rem; color: #000000;"></i>
                    </button>
                </li>
            `).join('');
        }
    } catch (e) {
        console.error('Erro ao carregar notificações:', e);
    }
}

async function marcarComoLida(id) {
    await fetch(`api/notificacoes.php?acao=marcar_lida&id=${id}`);
}

async function excluirNotificacao(event, id) {
    event.stopPropagation();
    event.preventDefault();
    if (confirm('Deseja excluir esta notificação?')) {
        const res = await fetch(`api/notificacoes.php?acao=excluir&id=${id}`);
        const data = await res.json();
        if (data.success) {
            carregarNotificacoes();
        }
    }
}

// Inicia e atualiza a cada 30 segundos
document.addEventListener('DOMContentLoaded', () => {
    carregarNotificacoes();
    setInterval(carregarNotificacoes, 30000);
});
