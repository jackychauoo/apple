#!/bin/bash

# 更新系統套件
sudo apt-get update
sudo apt-get upgrade -y

# 安裝必要的依賴
sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common

# 添加 Docker 的 GPG 密鑰和源
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# 安裝 Docker
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io

# 啟用 Docker 並設定開機自動啟動
sudo systemctl enable docker
sudo systemctl start docker

# 安裝 Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep -oP '"tag_name": "\K(.*)(?=")')/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# 確認安裝版本
docker --version
docker-compose --version
