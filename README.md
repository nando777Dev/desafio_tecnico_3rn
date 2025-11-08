🧾 Sistema de Propostas de Crédito 

Projeto feito para desafio da 3RN 
Análise de propostas de crédito, desenvolvido com Laravel + Vue 3 + Vite, utilizando Docker para orquestração dos serviços e um design modular e limpo.
Tempo gasto 9 horas e 16 minutos

🚀 Tecnologias Utilizadas
Backend

PHP 8.3+

Laravel 11

PostgreSQL 15

Docker e Docker Compose

Repository Pattern

Form Requests para validação

Resources para formatação de resposta

Enum e Status controlados

Seeders e Migrations automáticas

Frontend

Vue 3 (Composition API)

Vite

Axios

TailwindCSS 3

Composables reutilizáveis (useApi, useMask) para reutilizáveis

Componentização (StatusBadge, Modal, Paginação, etc.)

UX aprimorada com modais e confirmações

🧩 Arquitetura

A aplicação segue uma estrutura limpa e modular:

propostas/
├── backend/ (Laravel)
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   ├── Resources/
│   │   ├── Models/
│   │   ├── Repositories/
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   ├── routes/
│   │   └── api.php
│   └── dockerfile
│
└── frontend/ (Vue 3)
    ├── src/
    │   ├── api/
    │   ├── components/
    │   ├── composables/
    │   ├── views/
    │   ├── router/
    │   └── assets/
    └── vite.config.js

⚙️ Requisitos

Docker e Docker Compose instalados

Portas 8000 (backend) e 5173 (frontend) livres

🧱 Configuração e Execução
1️⃣ Clone o repositório
git clone https://github.com/seuusuario/propostas.git
cd propostas

2️⃣ Suba os containers
docker compose up -d

Isso irá levantar os containers:

laravel_app (PHP + Laravel)

postgres_db (Banco de dados)

frontend_app (Vue 3 + Vite)

3️⃣ Configure o backend (Laravel)

Acesse o container do Laravel:

docker exec -it laravel_app bash


Crie o arquivo .env:

cp .env.example .env


Gere a chave da aplicação:

php artisan key:generate

php artisan l5-swagger:generate

Api disponivel em http://localhost:8000/api/documentation

Rode as migrations e seeders:

php artisan migrate --seed

A api vai estar disponivel no 

4️⃣ Configure o frontend

Acesse o container do frontend:

docker exec -it frontend_app bash


Instale as dependências:

npm install


Rode o servidor:

npm run dev


O frontend ficará disponível em:
👉 http://localhost:5173

🧰 Rotas Principais (API)
Método	Rota	Descrição
GET	/api/propostas	Listar propostas
GET	/api/propostas/{id}	Detalhar proposta
POST	/api/propostas/create	Criar nova proposta
PATCH	/api/propostas/{id}	Atualizar proposta
PATCH	/api/propostas/{id}/status	Atualizar status
DELETE	/api/propostas/{id}	Excluir proposta
🧮 Funcionalidades

 Cadastro de propostas com cálculo automático de parcelas e margem

 Máscaras de CPF e valores monetários

 Filtros dinâmicos (status, nome, CPF)

 Controle de status (rascunho, em_analise, aprovada, reprovada, cancelada)

 Edição restrita a status específicos

 Modais de confirmação e feedback visual

 Paginação automática e responsiva

 Validações backend + frontend

🧑‍💻 Desenvolvido por

Fernando Henrique
Desenvolvedor Full Stack (Laravel + Vue.js)
📧 [fernandohjesus777@gmail.com]


📄 Licença

Este projeto é distribuído sob a licença MIT — sinta-se livre para usar, estudar e contribuir.
