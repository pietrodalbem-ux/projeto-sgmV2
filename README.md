# SGM - Sistema de Gestão de Manutenção

O **SGM** é um sistema de gestão de manutenção corretiva desenvolvido para facilitar o controle de chamados técnicos dentro de uma infraestrutura organizada por **Blocos** e **Ambientes** (Salas). O foco principal é a eficiência operacional, eliminando a necessidade de descrições complexas de localização e centralizando a comunicação entre solicitantes, técnicos e gestores.

## 🚀 Funcionalidades Principal

O sistema é dividido em três perfis de acesso, conforme detalhado nos casos de uso:

### 👤 Solicitante
- **Abertura de Chamados:** Registro de problemas selecionando Bloco/Ambiente e anexando fotos.
- **Acompanhamento:** Visualização em tempo real do status da solicitação.
- **Interação:** Chat interno para comunicação com a equipe de manutenção.

### 🛠️ Técnico de Manutenção
- **Agenda de Tarefas:** Visualização de chamados atribuídos com priorização.
- **Registro de Execução:** Alteração de status (Em Execução/Concluído), registro de solução técnica e tempo gasto.

### 💼 Gestor de Manutenção
- **Triagem e Dispatch:** Análise de chamados, definição de prioridade, prazos e atribuição de técnicos.
- **Controle de Qualidade:** Revisão e fechamento definitivo de ordens de serviço.
- **Gestão de Parâmetros:** Cadastro de novos Blocos, Ambientes e Tipos de Serviço.
- **Dashboard:** Indicadores mensais e anuais de volume de chamados.

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.2 (Nativo/Puro)
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla/AJAX Fetch)
- **Framework CSS:** Bootstrap 5
- **Banco de Dados:** MySQL / MariaDB
- **Ambiente de Desenvolvimento:** XAMPP

## 📂 Estrutura do Projeto

```text
projeto-sgm/
├── api/            # Endpoints para processamento de dados (JSON)
├── assets/         # Recursos estáticos (CSS, JS, Imagens)
│   └── uploads/    # Anexos dos chamados
├── config/         # Configurações de conexão (database.php)
├── docs/           # Documentação SQL e dumps do banco
├── stand-by/       # Documentação de requisitos, telas e histórias de usuário
└── index.php       # Ponto de entrada do sistema
```

## 🗄️ Banco de Dados

O modelo de dados é composto pelas seguintes tabelas principais:
- `usuarios`: Gestão de perfis (Solicitante, Técnico, Gestor).
- `blocos` e `ambientes`: Hierarquia de localização.
- `chamados`: Tabela central com logs de status, prioridade e datas.
- `chamados_anexos` e `chamados_comentarios`: Histórico multimídia e chat.
- `notificacoes`: Alertas automáticos para gestores sobre novas interações.

Os arquivos de migração e dump podem ser encontrados em `/docs`.

## 🔧 Instalação e Configuração

1.  **Clone o repositório** para a pasta `htdocs` do seu XAMPP:
    ```bash
    git clone https://github.com/seu-usuario/projeto-sgm.git
    ```

2.  **Importe o Banco de Dados**:
    - Acesse o `phpMyAdmin`.
    - Crie um banco chamado `sgm_db`.
    - Importe o arquivo `docs/database.sql`.

3.  **Configuração de Conexão**:
    - Verifique as credenciais em `config/database.php`.

4.  **Acesso**:
    - Abra o navegador e acesse `http://localhost/2025/projeto-sgm`.

## 🔑 Credenciais de Teste (Padrão)

| Perfil | E-mail | Senha |
| :--- | :--- | :--- |
| **Gestor** | admin@sgm.com | 123456 (ou hash correspondente) |
| **Técnico** | tecnico@sgm.com | 123456 |
| **Solicitante** | usuario@sgm.com | 123456 |

---
*Este projeto foi desenvolvido como parte do escopo de Gestão de Manutenção para o ano de 2025.*