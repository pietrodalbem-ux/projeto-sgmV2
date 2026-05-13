<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>

<html lang="pt-br">
<?php include 'layout/head.php'; ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --text-primary: #1e293b;
    }



    body {
        margin: 0;
        padding: 0;
        font-family: 'Outfit', sans-serif;
        background: url('premium_mesh_gradient_bg_1778677115169.png') center/cover no-repeat fixed;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }



    body > * {
        position: relative;
        z-index: 1;
    }


    .login-container {
        width: 100%;
        max-width: 420px;
        padding: 20px;
        perspective: 1000px;
    }

    .login-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 32px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: cardAppear 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
    }

    @keyframes cardAppear {
        from { opacity: 0; transform: scale(0.9) translateY(30px) rotateX(-10deg); }
        to { opacity: 1; transform: scale(1) translateY(0) rotateX(0); }
    }

    .login-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .login-logo {
        width: 64px;
        height: 64px;
        background: var(--primary);
        color: white;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 24px;
        box-shadow: 0 10px 20px -5px rgba(var(--primary-rgb), 0.5);
        transform: rotate(-5deg);
        transition: all 0.5s ease;
    }

    .login-card:hover .login-logo {
        transform: rotate(0deg) scale(1.1);
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .login-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 8px;
        letter-spacing: -0.5px;
    }

    .login-header p {
        color: var(--text-muted);
        font-size: 15px;
        margin: 0;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        margin-bottom: 8px;
        display: block;
    }

    .input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    .input-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        transition: all 0.3s ease;
    }

    .form-control {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        padding: 14px 16px 14px 48px;
        border-radius: 14px;
        color: var(--text-primary);
        font-size: 15px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.15);
    }

    .form-control:focus + i {
        color: var(--primary);
        transform: translateY(-50%) scale(1.2);
    }

    .btn-submit {
        width: 100%;
        background: var(--primary);
        color: white;
        border: none;
        padding: 16px;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -10px rgba(var(--primary-rgb), 0.5);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .theme-switch {
        position: fixed;
        top: 24px;
        right: 24px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        color: var(--text-primary);
    }

    .theme-switch:hover {
        transform: rotate(180deg) scale(1.1);
        background: var(--primary);
        color: white;
    }

    .footer {
        text-align: center;
        margin-top: 24px;
        font-size: 13px;
        color: var(--text-muted);
    }


    #errorMessage {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 12px;
        border-radius: 10px;
        font-size: 14px;
        text-align: center;
        margin-bottom: 20px;
        display: none;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
</style>

<body>
    

    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="ph-fill ph-shield-check"></i>
            </div>
            
            <div class="login-header">
                <h1>Acesso SGM</h1>
                <p>Gestão de Manutenção Inteligente</p>
            </div>

            <div id="errorMessage"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">E-mail</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" class="form-control" placeholder="nome@empresa.com" required>
                        <i class="ph ph-envelope-simple"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <div class="input-wrapper">
                        <input type="password" id="senha" class="form-control" placeholder="••••••••" required>
                        <i class="ph ph-lock-simple"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span>Entrar no Sistema</span>
                    <i class="ph ph-arrow-right"></i>
                </button>
            </form>
        </div>
        
        <div class="footer">
            &copy; 2025 SGM | Sophisticated Asset Management
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            const errorDiv = document.getElementById('errorMessage');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-circle-notch ph-spin"></i> Autenticando...';
            errorDiv.style.display = 'none';

            const payload = {
                email: document.getElementById('email').value,
                senha: document.getElementById('senha').value
            };

            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    errorDiv.innerText = result.message;
                    errorDiv.style.display = 'block';
                    btn.disabled = false;
                    btn.innerHTML = '<span>Entrar no Sistema</span> <i class="ph ph-arrow-right"></i>';
                }
            } catch (error) {
                errorDiv.innerText = "Erro de conexão. Tente novamente.";
                errorDiv.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<span>Entrar no Sistema</span> <i class="ph ph-arrow-right"></i>';
            }
        });
    </script>
</body>
</html>

