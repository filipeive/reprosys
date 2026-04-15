#!/bin/bash

# Deploy script para o servidor de produção
SERVER="ubuntu@146.235.224.99"
KEY="/home/fdev-ms/.ssh/oracle-2025"
PROJECT_DIR="/var/www/html/reprosys"


# Deploy local
echo "Iniciando o deploy local"
git push origin main # isso depois de comitar todas as mudancas

# Deploy produção
echo "🚀 Iniciando deploy para produção..."

ssh -i $KEY $SERVER "cd $PROJECT_DIR && \
    echo '🔧 Corrigindo permissões do git...' && \
    sudo chown -R ubuntu:ubuntu .git && \
    echo '📥 Puxando últimas alterações...' && \
    git pull origin main && \
    echo '📦 Instalando dependências...' && \
    composer install --optimize-autoloader --no-dev && \
    echo '🧹 Limpando cache...' && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    echo '✅ Deploy concluído com sucesso!'"