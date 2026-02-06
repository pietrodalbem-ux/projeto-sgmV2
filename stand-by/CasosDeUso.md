# **Documentação de Casos de Uso \- SGM (Escopo Revisado)**

## **1\. Introdução**

Este documento detalha os requisitos funcionais para o Sistema de Gestão de Manutenção (SGM).

**Alteração de Escopo:** O sistema foi ajustado para focar na gestão eficiente de chamados corretivos baseados em **Ambientes** (Salas, Blocos). Não há gestão de múltiplas unidades, custos ou ativos patrimoniais.

## **2\. Atores do Sistema**

* **Solicitante:** Colaborador que abre pedidos de manutenção para um determinado ambiente.  
* **Gestor de Manutenção:** Responsável por triar, priorizar e atribuir os chamados, além de monitorar o fluxo.  
* **Técnico de Manutenção:** Executor das tarefas, responsável por reportar a solução e o tempo gasto.

## **3\. Lista de Casos de Uso**

| **ID** | **Título** | **Ator Principal** |

| **UC01** | Solicitar Manutenção | Solicitante |

| **UC02** | Consultar Status da Solicitação | Solicitante |

| **UC03** | Comunicar-se sobre a Solicitação | Todos |

| **UC04** | Visualizar Ordens de Serviço Atribuídas | Técnico |

| **UC05** | Registrar Execução do Trabalho | Técnico |

| **UC06** | Analisar e Priorizar Solicitação | Gestor |

| **UC07** | Atribuir Solicitação a Técnico | Gestor |

| **UC08** | Revisar e Fechar Ordem de Serviço | Gestor |

| **UC09** | Gerir Parâmetros (Blocos/Ambientes) | Gestor |

| **UC10** | Gerir Utilizadores e Perfis | Gestor |

| **UC11** | Gerar Relatórios Operacionais | Gestor |

## **4\. Especificação Detalhada**

### **UC01: Solicitar Manutenção**

* **Ator:** Solicitante  
* **Objetivo:** Registrar um problema ocorrido em um ambiente específico.  
* **Fluxo Principal:**  
  1. O Solicitante seleciona a opção "Nova Solicitação".  
  2. O sistema solicita a identificação do local: **Bloco** e **Ambiente** (ex: Bloco A \> Sala 101).  
  3. O Solicitante descreve o problema e define o Tipo de Serviço (ex: Elétrica, Hidráulica).  
  4. O Solicitante pode anexar até 3 fotos como evidência.  
  5. O sistema valida os dados, gera um ID de protocolo e define o status como "Aberta".

### **UC02: Consultar Status da Solicitação**

* **Ator:** Solicitante  
* **Fluxo Principal:**  
  1. O Solicitante acessa "Minhas Solicitações".  
  2. O sistema exibe uma lista com ID, Local (Bloco/Sala), Resumo e Status.  
  3. O Solicitante clica em um item para ver detalhes completos.

### **UC03: Comunicar-se sobre a Solicitação**

* **Atores:** Solicitante, Técnico, Gestor  
* **Fluxo Principal:**  
  1. No detalhe da OS, o ator insere um comentário.  
  2. O sistema registra a mensagem com data/hora e autor e notifica os envolvidos.

### **UC04: Visualizar Ordens de Serviço Atribuídas**

* **Ator:** Técnico  
* **Pré-condição:** O Gestor já deve ter atribuído a OS ao técnico.  
* **Fluxo Principal:**  
  1. O Técnico acessa sua "Fila de Trabalho".  
  2. O sistema lista as OS pendentes, ordenadas por **Prioridade**.  
  3. A lista exibe: ID, Bloco/Ambiente, Descrição do Problema e Prazo.

### **UC05: Registrar Execução do Trabalho**

* **Ator:** Técnico  
* **Fluxo Principal:**  
  1. O Técnico inicia o atendimento mudando status para "Em Execução".  
  2. Ao finalizar, o Técnico registra:  
     * **Descrição da Solução:** O que foi feito.  
     * **Tempo Gasto:** Duração do reparo.  
     * **Evidências:** Fotos do serviço concluído (opcional).  
  3. O Técnico altera o status para "Concluída".

### **UC06: Analisar e Priorizar Solicitação**

* **Ator:** Gestor  
* **Fluxo Principal:**  
  1. O Gestor visualiza a lista de "Novas Solicitações".  
  2. O Gestor define a **Prioridade** e a **Data Prevista**.  
  3. Se o pedido for inválido, o Gestor pode "Rejeitar" a solicitação.

### **UC07: Atribuir Solicitação a Técnico**

* **Ator:** Gestor  
* **Fluxo Principal:**  
  1. Em uma solicitação aprovada, o Gestor clica em "Atribuir".  
  2. O Gestor seleciona o Técnico responsável.  
  3. O sistema atualiza a OS vinculando o id\_tecnico.

### **UC08: Revisar e Fechar Ordem de Serviço**

* **Ator:** Gestor  
* **Fluxo Principal:**  
  1. O Gestor filtra por chamados com status "Concluída".  
  2. O Gestor verifica a solução e as fotos.  
  3. Se aprovado, muda o status para "Fechada".

### **UC09: Gerir Parâmetros do Sistema**

* **Ator:** Gestor  
* **Fluxo Principal:**  
  1. O Gestor mantém o cadastro de **Blocos** e **Ambientes** (Salas) e **Tipos de Serviço**.

### **UC10: Gerir Utilizadores e Perfis**

* **Ator:** Gestor (Admin)  
* **Fluxo Principal:**  
  1. Cadastro de novos usuários definindo seu perfil de acesso.

### **UC11: Gerar Relatórios Operacionais**

* **Ator:** Gestor  
* **Fluxo Principal:**  
  1. Visualização de métricas: Total de chamados por Status, Ambientes com mais problemas, Produtividade.