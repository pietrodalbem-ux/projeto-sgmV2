document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const msg = document.getElementById('mensagem');

    try {
        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, senha: senha })
        });

        // Debug: Veja o que o PHP está retornando no console do navegador (F12)
        const textoRetorno = await response.text();
        console.log("Resposta do Servidor:", textoRetorno);
        
        const result = JSON.parse(textoRetorno);

        if (result.success) {
            // Se o login funcionar, manda para o dashboard que criamos
            window.location.href = 'dashboard.php';
        } else {
            msg.innerText = result.message;
        }
    } catch (error) {
        console.error("Erro na requisição:", error);
        msg.innerText = "Erro ao conectar com o servidor.";
    }
});