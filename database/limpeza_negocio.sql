-- Script de Limpeza para Novo Início de Negócio
-- Este script remove todo o histórico financeiro e de transações, mantendo cadastros (Produtos, Categorias, Usuários)

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Limpar Histórico de Vendas
TRUNCATE TABLE sale_items;
TRUNCATE TABLE sales;

-- 2. Limpar Pedidos
TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;

-- 3. Limpar Movimentações de Stock (Histórico)
TRUNCATE TABLE stock_movements;

-- 4. Limpar Financeiro (Dívidas e Despesas)
TRUNCATE TABLE debt_payments;
TRUNCATE TABLE debt_items;
TRUNCATE TABLE debts;
TRUNCATE TABLE expenses;

-- 5. Limpar Logs e Notificações
TRUNCATE TABLE notifications;
TRUNCATE TABLE user_activities;
TRUNCATE TABLE temporary_passwords;

-- 6. Zerar Stocks Actuais de Produtos
-- Mantemos os produtos e categorias, mas o inventário começa do zero
UPDATE products SET stock_quantity = 0;

SET FOREIGN_KEY_CHECKS = 1;
