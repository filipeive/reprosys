# 🖨️ ReproSys - Sistema de Reprografia e Serigrafia

**Versão:** 2.0.0  
**Desenvolvido por:** Eng. Filipe dos Santos

Um sistema web completo e moderno para gestão de reprografia, serigrafia e serviços relacionados. Permite gerenciar produtos, vendas, estoque, despesas, dívidas e gerar relatórios detalhados de fluxo de caixa.

---

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Principais Características](#principais-características)
- [Requisitos do Sistema](#requisitos-do-sistema)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Como Usar](#como-usar)
- [Estrutura do Banco de Dados](#estrutura-do-banco-de-dados)
- [Troubleshooting](#troubleshooting)
- [Suporte e Contato](#suporte-e-contato)

---

## 🎯 Visão Geral

**ReproSys** é um sistema empresarial desenvolvido com **Laravel 12** e **Tailwind CSS** que oferece uma solução completa para empresas de reprografia, serigrafia e serviços similares.

### Para Quem é?
- ✅ Empresas de reprografia
- ✅ Serviços de serigrafia
- ✅ Negócios de impressão e cópia
- ✅ Qualquer empresa que precise gerenciar produtos, vendas e despesas

---

## ⭐ Principais Características

### 1. **Gestão de Produtos e Serviços**
- Cadastro de produtos e serviços
- Controle de categorias
- Associação de preços
- Tipos: Produto ou Serviço

### 2. **Gerenciamento de Vendas**
- Criação de pedidos e vendas
- Cálculo automático de totais
- Integração com estoque
- Histórico completo de transações

### 3. **Controle de Estoque**
- Movimentação em tempo real
- Rastreamento de entradas e saídas
- Alertas de produtos com baixo estoque
- Relatórios de inventário

### 4. **Gestão de Despesas**
- Registro detalhado de despesas
- Categorização por tipo
- Associação a recibos
- Filtros e busca avançada

### 5. **Controle de Dívidas**
- Registrar dívidas de clientes
- Rastreamento de pagamentos
- Cálculo automático de juros
- Status: Ativa, Parcial, Paga, Vencida

### 6. **Relatórios e Análises**
- Fluxo de caixa detalhado
- Gráficos de vendas e despesas
- Análise de lucro/prejuízo
- Exportação de dados
- Insights financeiros

### 7. **Sistema de Usuários e Permissões**
- Autenticação segura
- Controle de acesso por perfil
- Gerenciamento de permissões
- Auditoria de ações

### 8. **Interface Moderna**
- Design responsivo e intuitivo
- Tema claro/escuro
- Barra lateral recolhível
- Notificações em tempo real

---

## 💻 Requisitos do Sistema

### Backend
- **PHP** >= 8.2
- **Composer** (gerenciador de dependências PHP)
- **Node.js** >= 16 (para assets frontend)
- **NPM** ou **Yarn**

### Banco de Dados
- **MySQL** >= 5.7 ou **MariaDB** >= 10.3
- **SQLite** (para desenvolvimento)

### Servidor Web
- **Apache** (com mod_rewrite) ou **Nginx**
- **SSL/TLS** (recomendado para produção)

### Sistema Operacional
- Linux/Ubuntu, Windows, macOS

---

## 🚀 Instalação

### 1. Clonar o Repositório

```bash
git clone https://github.com/filipeive/reprosys.git
cd reprosys
```

### 2. Instalar Dependências PHP

```bash
composer install
```

### 3. Instalar Dependências Node.js

```bash
npm install
```

### 4. Copiar Arquivo de Ambiente

```bash
cp .env.example .env
```

### 5. Gerar Chave de Aplicação

```bash
php artisan key:generate
```

### 6. Executar Migrações e Seeders

```bash
php artisan migrate --seed
```

### 7. Compilar Assets

```bash
npm run build
```

### 8. Iniciar o Servidor

```bash
php artisan serve
```

A aplicação estará disponível em: `http://localhost:8000`

---

## ⚙️ Configuração

### Arquivo `.env`

```env
APP_NAME=ReproSys
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_reprografia
DB_USERNAME=root
DB_PASSWORD=

# Autenticação
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
```

### Configurar Banco de Dados MySQL

```sql
CREATE DATABASE sistema_reprografia;
CREATE USER 'reprosys'@'localhost' IDENTIFIED BY 'senha_segura';
GRANT ALL PRIVILEGES ON sistema_reprografia.* TO 'reprosys'@'localhost';
FLUSH PRIVILEGES;
```

### Usar Arquivo SQL Fornecido

```bash
mysql -u root -p sistema_reprografia < sistema_reprografia.sql
```

---

## 📚 Como Usar

### Primeiro Acesso

1. **Login Padrão:**
   - Email: `admin@example.com`
   - Senha: `password`

2. **Acesso ao Sistema:**
   - Navegue para `http://localhost:8000`
   - Faça login com as credenciais acima

### Navegação Principal

A interface é dividida em seções principais na barra lateral:

- 🏠 **Dashboard** - Visão geral do negócio
- 📦 **Produtos** - Gestão de produtos e serviços
- 🛒 **Vendas** - Registrar e gerenciar vendas
- 💰 **Despesas** - Controlar gastos
- 💳 **Dívidas** - Gerenciar débitos de clientes
- 📊 **Relatórios** - Análises e fluxo de caixa
- 👥 **Usuários** - Gerenciar funcionários

### Fluxo Típico de Uso

#### 1. **Adicionar um Produto**
   - Clique em **Produtos → Novo Produto**
   - Preencha: Nome, Descrição, Tipo, Preço
   - Clique em **Salvar**

#### 2. **Registrar uma Venda**
   - Clique em **Vendas → Nova Venda**
   - Selecione os produtos desejados
   - Defina quantidades
   - O total é calculado automaticamente
   - Clique em **Concluir Venda**

#### 3. **Registrar uma Despesa**
   - Clique em **Despesas → Nova Despesa**
   - Preencha os dados (descrição, valor, data)
   - Opcionalmente, anexe recibo
   - Clique em **Salvar**

#### 4. **Gerar Relatório**
   - Clique em **Relatórios → Fluxo de Caixa**
   - Selecione o período desejado
   - Visualize gráficos e estatísticas
   - Exporte em PDF ou Excel se necessário

---

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

| Tabela | Descrição |
|--------|-----------|
| `users` | Usuários do sistema |
| `roles` | Perfis de acesso |
| `categories` | Categorias de produtos |
| `products` | Produtos e serviços |
| `orders` | Pedidos realizados |
| `order_items` | Itens dos pedidos |
| `sales` | Vendas concluídas |
| `sale_items` | Itens das vendas |
| `expenses` | Despesas registradas |
| `expense_categories` | Categorias de despesas |
| `debts` | Dívidas de clientes |
| `debt_payments` | Pagamentos de dívidas |
| `stock_movements` | Movimentação de estoque |

### Relacionamentos

```
Users ─┬─ Orders
       ├─ Sales
       ├─ Expenses
       └─ Debts

Products ─┬─ Order Items
          └─ Sale Items
```

---

## 🔧 Deployment

### Deploy em Servidor Linux

1. **Clone o repositório:**
```bash
cd /var/www
git clone https://github.com/filipeive/reprosys.git
```

2. **Configure permissões:**
```bash
chmod -R 755 reprosys
chmod -R 777 reprosys/storage
chmod -R 777 reprosys/bootstrap/cache
```

3. **Instale dependências:**
```bash
cd reprosys
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

4. **Configure .env para produção:**
```env
APP_ENV=production
APP_DEBUG=false
```

5. **Configure Nginx ou Apache** com SSL/TLS

---

## 🐛 Troubleshooting

### Erro: "Class not found"
```bash
php artisan config:clear
composer dump-autoload
```

### Erro: "SQLSTATE[HY000]"
- Verifique credenciais do banco de dados no `.env`
- Confirme que o banco de dados existe
- Reinicie o serviço MySQL

### Erro: "Permission denied" em storage
```bash
chmod -R 777 storage bootstrap/cache
```

### Página em branco
```bash
php artisan config:cache
php artisan cache:clear
php artisan view:clear
```

### Assets não carregam
```bash
npm run dev
# ou
npm run build
php artisan storage:link
```

---

## 🔐 Segurança

- ✅ Autenticação com hash bcrypt
- ✅ Proteção CSRF em todos os formulários
- ✅ Validação de entrada em servidor
- ✅ Sanitização de dados
- ✅ Controle de acesso baseado em funções
- ✅ Auditoria de operações

**Recomendações:**
- Altere a senha padrão no primeiro acesso
- Use senhas fortes
- Mantenha o sistema atualizado
- Faça backups regulares

---

## 📞 Suporte e Contato

- **Email:** filipedomingos198@gmail.com
- **Portal de Suporte:** https://api.whatsapp.com/send/?phone=258847240296&text&type=phone_number&app_absent=0
- **Issues GitHub:** [Reportar problema](https://github.com/filipeive/reprosys/issues)

---

## 📄 Licença

MIT License - Veja o arquivo LICENSE para detalhes.

---

## 👨‍💼 Desenvolvedor

**Eng. Filipe dos Santos**  
FDSMULTSERVICES+

---

**Última atualização:** Novembro de 2025
