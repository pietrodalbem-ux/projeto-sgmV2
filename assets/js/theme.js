// Script de gerenciamento de tema (Claro/Escuro)
(function() {
    document.documentElement.setAttribute('data-theme', 'light');
    localStorage.setItem('sgm-theme', 'light');
})();

function toggleTheme() {
    // Função desativada conforme solicitação de remover o tema escuro
    console.log("Tema escuro removido pelo usuário.");
}


function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Adiciona evento ao botão de toggle se ele existir
    const themeBtn = document.getElementById('theme-toggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', toggleTheme);
    }

    // Fechar sidebar ao clicar fora em mobile
    document.addEventListener('click', (e) => {
        const sidebar = document.querySelector('.sidebar');
        const menuToggle = document.querySelector('.menu-toggle');
        if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
});

