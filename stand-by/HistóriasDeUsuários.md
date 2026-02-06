# **Histórias de Usuários \- SGM**

Este documento reflete o Backlog do Produto ajustado para o novo escopo: **Foco em Ambientes e Resolução de Chamados (sem gestão de ativos/custos).**

## **👤 Ator: Solicitante**

### **HU01: Abrir chamado por Ambiente**

**Como um** Solicitante,

**eu quero** selecionar especificamente o Bloco e a Sala onde estou,

**para que** a manutenção saiba exatamente onde deve ir sem eu precisar descrever o caminho.

* **Critérios de Aceitação:**  
  * O sistema deve apresentar listas suspensas (dropdowns) hierárquicas: Unidade \-\> Bloco \-\> Ambiente.  
  * Deve ser obrigatório descrever o problema.  
  * Deve permitir envio de até 3 fotos.  
  * Ao salvar, deve gerar um número de protocolo.

### **HU02: Acompanhar os meus pedidos**

**Como um** Solicitante,

**eu quero** ver uma lista simples com o status dos meus pedidos,

**para que** eu saiba se o técnico já foi designado ou se o serviço já acabou.

* **Critérios de Aceitação:**  
  * Listagem deve mostrar: ID, Local, Data e Status (ícone ou texto colorido).  
  * Status principais visíveis: Aberto, Agendado, Em Execução, Concluído.

### **HU03: Interagir no chamado**

**Como um** Solicitante,

**eu quero** adicionar comentários a um chamado aberto,

**para que** eu possa avisar se a sala estará trancada ou responder dúvidas do técnico.

* **Critérios de Aceitação:**  
  * Campo de texto livre para comentários.  
  * Registro cronológico (chat) dentro do detalhe do chamado.

## **🛠️ Ator: Técnico de Manutenção**

### **HU04: Visualizar a minha agenda**

**Como um** Técnico,

**eu quero** ver apenas os chamados atribuídos a mim, ordenados pelos mais urgentes,

**para que** eu possa planear o meu dia de trabalho com eficiência.

* **Critérios de Aceitação:**  
  * Filtro automático para exibir id\_tecnico \= usuario\_logado.  
  * Ordenação padrão: Prioridade (Urgente no topo) e Data Prevista.  
  * Visualização clara do Ambiente (Sala) no cartão da tarefa.

### **HU05: Reportar serviço realizado**

**Como um** Técnico,

**eu quero** registar o que fiz e quanto tempo levei de forma rápida,

**para que** eu possa partir para o próximo chamado sem burocracia.

* **Critérios de Aceitação:**  
  * Botão simples para mudar status (Iniciar / Concluir).  
  * Campo de texto "Solução Técnica" obrigatório no fechamento.  
  * Campo numérico/tempo para "Duração do Atendimento".  
  * Não deve exigir cadastro de peças ou custos (fora do escopo).

## **💼 Ator: Gestor de Manutenção**

### **HU06: Classificar e Priorizar**

**Como um** Gestor,

**eu quero** definir a prioridade e o prazo dos novos chamados,

**para que** a equipa foque no que é mais crítico para a operação da empresa.

* **Critérios de Aceitação:**  
  * Interface para editar o chamado inserindo: Prioridade (Baixa/Média/Alta/Urgente) e Data Limite.  
  * Capacidade de rejeitar chamados duplicados ou indevidos.

### **HU07: Distribuir tarefas (Dispatch)**

**Como um** Gestor,

**eu quero** selecionar qual técnico vai atender qual chamado,

**para que** eu possa equilibrar a carga de trabalho da equipa.

* **Critérios de Aceitação:**  
  * Dropdown com lista de técnicos ativos.  
  * Ao salvar, o chamado muda de "Aberto" para "Agendado" (ou "Atribuído").

### **HU08: Controle de Qualidade (Fechamento)**

**Como um** Gestor,

**eu quero** validar os chamados dados como "Concluídos" pelos técnicos,

**para que** eu possa garantir que o serviço foi descrito corretamente antes de arquivar.

* **Critérios de Aceitação:**  
  * Fluxo de aprovação final: O chamado só vai para status "Fechado" após clique do Gestor.  
  * Visualização das fotos de "Depois" enviadas pelo técnico.

### **HU09: Cadastrar Locais**

**Como um** Gestor,

**eu quero** gerenciar a lista de Salas e Blocos,

**para que** os solicitantes sempre tenham as opções corretas de locais no formulário.

* **Critérios de Aceitação:**  
  * Tela de cadastro simples (CRUD) para tabela de Ambientes.

### **HU10: Monitorizar Indicadores**

**Como um** Gestor,

**eu quero** ver um painel com o volume de chamados por Sala e por Status,

**para que** eu identifique gargalos ou locais com problemas recorrentes.

* **Critérios de Aceitação:**  
  * Gráfico de pizza: Chamados por Status.  
  * Lista: Top 5 ambientes com mais chamados no mês.