# **Lista de Telas \- Sistema de Gestão de Manutenção (SGM)**

Este documento mapeia as interfaces gráficas do sistema.

**Convenção:** Como o sistema será em PHP Puro, o "Path" refere-se ao nome sugerido para o arquivo físico na pasta do projeto.

## **🔒 1\. Módulo de Autenticação (Público)**

Arquivos acessíveis sem estar logado.

| ID | Nome da Tela | Arquivo / Path | Funcionalidades |
| :---- | :---- | :---- | :---- |
| **AUT-01** | **Login** | login.php | • Formulário de E-mail e Senha. • Botão "Entrar". |
| **AUT-02** | **Recuperação de Senha** | recuperar\_senha.php | • Campo de E-mail. • Botão "Enviar Link". |

## **👤 2\. Módulo do Solicitante**

Prefixo sugerido: solicitante\_

| ID | Nome da Tela | Arquivo / Path | Funcionalidades |
| :---- | :---- | :---- | :---- |
| **SOL-01** | **Minhas Solicitações** | solicitante\_dashboard.php | **(Tela Inicial)** • Tabela com chamados abertos pelo usuário. • Colunas: ID, Bloco, Sala, Status, Data. • Botão "Nova Solicitação". |
| **SOL-02** | **Nova Solicitação** | solicitante\_abrir\_chamado.php | • Select: Bloco. • Select: Ambiente (carregado via AJAX/JS ao escolher Bloco). • Select: Tipo de Serviço. • Textarea: Descrição. • Input File: Fotos. |
| **SOL-03** | **Detalhes / Chat** | solicitante\_visualizar.php | • Recebe ID via GET (?id=1). • Exibe dados do chamado (read-only). • Timeline de Status. • Área de Chat (Histórico \+ Campo de envio). |

## **🛠️ 3\. Módulo do Técnico**

Prefixo sugerido: tecnico\_

| ID | Nome da Tela | Arquivo / Path | Funcionalidades |
| :---- | :---- | :---- | :---- |
| **TEC-01** | **Minhas Tarefas** | tecnico\_minhas\_tarefas.php | **(Tela Inicial)** • Lista apenas chamados atribuídos ao técnico logado. • Ordenação: Prioridade (Urgente primeiro). • Link para "Atender". |
| **TEC-02** | **Execução de Serviço** | tecnico\_atendimento.php | • Recebe ID via GET (?id=1). • Botão "Iniciar" (muda status). • Formulário de Conclusão: Solução aplicada, Tempo gasto, Upload de foto final. • Botão "Concluir Chamado". |
| **TEC-03** | **Histórico Pessoal** | tecnico\_historico.php | • Lista de todos os chamados já finalizados por este técnico (para consulta). |

## **💼 4\. Módulo do Gestor**

Prefixo sugerido: gestor\_

| ID | Nome da Tela | Arquivo / Path | Funcionalidades |
| :---- | :---- | :---- | :---- |
| **GES-01** | **Dashboard** | gestor\_dashboard.php | **(Tela Inicial)** • Cards: Abertos, Pendentes, Urgentes. • Gráficos simples (se houver tempo). |
| **GES-02** | **Todos os Chamados** | gestor\_chamados.php | • Listagem geral. • Filtros: Status, Técnico, Prioridade. • Ações: "Visualizar/Gerenciar". |
| **GES-03** | **Gerenciar Chamado** | gestor\_detalhes.php | • Recebe ID via GET (?id=1). • **Ação de Triagem:** Definir Prioridade, Prazo e Selecionar Técnico. • **Ação de Fechamento:** Validar fotos e aprovar conclusão. |
| **GES-04** | **Gestão de Locais** | gestor\_locais.php | • Lista de Blocos e Ambientes. • Botão "Novo Bloco" / "Novo Ambiente". • Ícones para Editar/Excluir. |
| **GES-05** | **Gestão de Usuários** | gestor\_usuarios.php | • Lista de Usuários. • Formulário para cadastrar novo usuário (definindo se é Técnico, Gestor ou Solicitante). |
| **GES-06** | **Relatórios** | gestor\_relatorios.php | • Seleção de filtros (Data Início, Data Fim). • Botão "Gerar Tabela" ou "Imprimir". |

## **🧭 Estrutura de Pastas Sugerida**

Para organizar o projeto dos alunos:

/projeto-sgm  
│  
├── /assets           (CSS, JS, Imagens, Uploads)  
│   ├── /css  
│   ├── /js  
│   └── /uploads      (Fotos dos chamados)  
│  
├── /config           (Conexão com Banco de Dados)  
│   └── database.php  
│  
├── /api              (Scripts PHP que processam formulários \- Opcional se fizer tudo na mesma tela)  
│   ├── login\_action.php  
│   ├── salvar\_chamado.php  
│   └── ...  
│  
├── /components       (Pedaços de HTML repetidos)  
│   ├── header.php    (Menu do topo)  
│   ├── sidebar.php   (Menu lateral)  
│   └── footer.php  
│  
├── index.php         (Redireciona para login.php)  
├── login.php  
├── solicitante\_dashboard.php  
├── gestor\_dashboard.php  
└── ... (outros arquivos da lista acima)  
