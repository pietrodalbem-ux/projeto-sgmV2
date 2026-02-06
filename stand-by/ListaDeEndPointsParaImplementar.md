# **Documentação da API REST \- SGM**

Esta documentação lista os endpoints ajustados para desenvolvimento em PHP nativo, utilizando parâmetros de query (?id=) em vez de rotas dinâmicas, facilitando a captura de dados via $\_GET.

**Convenções:**

* **Base URL:** /api  
* **Formato de Dados:** JSON  
* **Autenticação:** Bearer Token (JWT) no Header Authorization.  
* **Regra de ID:** Nenhum ID é passado na URL (rota). IDs são passados como parâmetro GET (ex: ?id=1) ou no corpo do POST/PUT.

## **🔒 1\. Autenticação e Conta**

Arquivos sugeridos: login.php, recuperar\_senha.php, perfil.php.

| Método | Rota (Arquivo) | Parâmetros | Descrição |
| :---- | :---- | :---- | :---- |
| **POST** | /login | Body JSON | Autentica usuário (email/senha) e retorna Token. |
| **POST** | /recuperar-senha | Body JSON | Envia link de recuperação para o e-mail. |
| **POST** | /redefinir-senha | Body JSON | Define nova senha (token \+ nova senha). |
| **GET** | /perfil | Nenhum | Retorna dados do usuário logado (baseado no Token). |

## **🏢 2\. Localização e Cadastros**

Arquivos sugeridos: blocos.php, ambientes.php, tipos\_servico.php.

### **Blocos**

| Método | Rota | Parâmetros (GET/Body) | Descrição |
| :---- | :---- | :---- | :---- |
| **GET** | /blocos | (Opcional) ?id=1 | Se enviar ID, retorna um bloco. Se não, lista todos. |
| **POST** | /blocos | Body: { nome, descricao } | Cria um novo bloco. |
| **PUT** | /blocos | Body: { id, nome, descricao } | Edita um bloco. O ID vai no corpo. |
| **DELETE** | /blocos | Query: ?id=1 | Remove um bloco. |

### **Ambientes (Salas)**

| Método | Rota | Parâmetros (GET/Body) | Descrição |
| :---- | :---- | :---- | :---- |
| **GET** | /ambientes | ?id\_bloco=1 | Lista ambientes de um bloco específico. |
| **POST** | /ambientes | Body: { id\_bloco, nome } | Cria novo ambiente. |
| **PUT** | /ambientes | Body: { id, nome } | Edita ambiente. |
| **DELETE** | /ambientes | Query: ?id=1 | Remove ambiente. |

### **Tipos de Serviço**

| Método | Rota | Parâmetros (GET/Body) | Descrição |
| :---- | :---- | :---- | :---- |
| **GET** | /tipos-servico | Nenhum | Lista categorias (Elétrica, Hidráulica...). |
| **POST** | /tipos-servico | Body: { nome } | Cadastra novo tipo. |

## **📋 3\. Chamados (Ordens de Serviço)**

Arquivo sugerido: chamados.php.

### **Listagem e Criação**

| Método | Rota | Parâmetros (GET/Body) | Descrição |
| :---- | :---- | :---- | :---- |
| **GET** | /chamados | ?status=aberto ?meus\_pedidos=true ?minhas\_tarefas=true | Lista chamados com filtros. |
| **GET** | /chamados | ?id=1 | Retorna detalhes completos de UM chamado. |
| **POST** | /chamados | Body: { id\_bloco, id\_ambiente, id\_tipo, descricao } | Cria nova solicitação. |

### **Fluxo de Ações (Workflow)**

*Sugestão: Criar um endpoint específico para atualizações de status para não misturar lógica.*

| Método | Rota | Parâmetros (Body JSON) | Descrição |
| :---- | :---- | :---- | :---- |
| **POST** | /chamados/atribuir | { id\_chamado, id\_tecnico, prioridade, data\_prevista } | Gestor atribui técnico. |
| **POST** | /chamados/iniciar | { id\_chamado } | Técnico inicia execução. |
| **POST** | /chamados/finalizar | { id\_chamado, solucao, tempo\_gasto } | Técnico conclui trabalho. |
| **POST** | /chamados/aprovar | { id\_chamado } | Gestor fecha o chamado. |
| **POST** | /chamados/rejeitar | { id\_chamado, motivo } | Gestor devolve chamado. |
| **POST** | /chamados/cancelar | { id\_chamado } | Solicitante cancela. |

## **💬 4\. Comentários e Anexos**

Arquivos sugeridos: comentarios.php, anexos.php.

| Método | Rota | Parâmetros | Descrição |
| :---- | :---- | :---- | :---- |
| **GET** | /comentarios | ?id\_chamado=1 | Lista chat do chamado. |
| **POST** | /comentarios | Body: { id\_chamado, texto } | Adiciona comentário. |
| **POST** | /upload | Form-Data: arquivo, id\_chamado, tipo | Upload de fotos. |

## **👥 5\. Usuários**

Arquivo sugerido: usuarios.php.

| Método | Rota | Parâmetros | Descrição |
| :---- | :---- | :---- | :---- |
| **GET** | /usuarios | ?perfil=tecnico | Lista usuários (pode filtrar por perfil). |
| **POST** | /usuarios | Body: { nome, email, perfil... } | Cria usuário. |
| **PUT** | /usuarios | Body: { id, nome... } | Edita usuário. |
| **POST** | /usuarios/alterar-status | Body: { id, ativo: boolean } | Ativa/Desativa usuário. |

## **📊 6\. Dashboards (Gestor)**

Arquivo sugerido: dashboard.php.

| Método | Rota | Parâmetros | Descrição |
| :---- | :---- | :---- | :---- |
| **GET** | /dashboard/resumo | Nenhum | Retorna contadores (Abertos, Urgentes...). |
| **GET** | /dashboard/graficos | Nenhum | Retorna dados para gráficos. |

