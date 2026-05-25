# Pokémon Battle Simulator

## Descrição

Pokémon Battle Simulator é a entrega final do teste técnico para Ateliware (Vaga: Pessoa Desenvolvedora). O objetivo é simulando uma batalha Pokémon baseada no maior HP entre dois candidatos, consumindo dados em tempo real da PokéAPI.

## Decisões Técnicas e Arquitetura

### Por que Monorepo com Back-end isolado do Front-end

A divisão em monorepo com backend isolado do frontend foi escolhida por dois motivos principais:

- Permite evoluir a API e a camada de apresentação de forma independente, sem acoplamento excessivo.
- Facilita testes de integração e deploy, já que o backend centraliza a distribuição de dados externos e expõe apenas payloads já validados ao cliente. O frontend não conhece a PokéAPI, apenas os endpoints disponibilizados pelo backend.

### Arquitetura do Back-end Laravel 12

O Back-end foi implementado em Laravel 12, adotando uma organização clara em Services e DTOs:

- `app/Services/`: contém a lógica de orquestração, consumo da PokéAPI e cálculo da batalha.
- `app/DTOs/`: encapsula dados de domínio.

### Escolha do Front-end: HTML5 + Bootstrap 5 + jQuery

A camada de UI foi definida com HTML5, Bootstrap 5 e jQuery, porque:

- Remove dependências de frameworks pesados como React ou Vue.
- Minimiza tempo de carregamento e latência inicial no browser.
- Garante que o front-end seja uma camada de apresentação simples, delegando toda a lógica ao BFF.

O backend consome a PokéAPI, faz a normalização do payload, e entrega ao cliente o que ele precisa, evitando processamento desnecessário no browser.

## Stack Tecnológica

- PHP 8.4-fpm-alpine
- Laravel 12
- Nginx stable-alpine3.23
- Bootstrap 5
- jQuery
- Docker

## Estrutura de Pastas

Abaixo está o layout conceitual do monorepo, destacando a separação entre backend e frontend e as camadas customizadas do backend em `app/`.

```text
/
├─ backend/
│  ├─ app/
│  │  ├─ DTOs/
│  │  │  ├─ BattleResultDto.php
│  │  │  └─ PokemonDto.php
│  │  ├─ Services/
│  │  │  ├─ PokemonBattleService.php
│  │  │  └─ PokeApiClient.php
│  │  ├─ Http/
│  │  │  └─ Controllers/
│  │  │     └─ PokemonController.php
│  │  └─ Providers/
│  ├─ bootstrap/
│  ├─ config/
│  ├─ public/
│  └─ routes/
├─ frontend/
│  ├─ assets/
│  │  ├─ css/
│  │  ├─ img/
│  │  └─ js/
│  └─ index.html
├─ docker-compose.yml
└─ README.md
```

> O ponto-chave da arquitetura é que o backend concentra a regra de negócio e o frontend permanece leve, consumindo apenas o payload validado.

## Passo a Passo de Execução em Qualquer Máquina (Via Docker)

Siga este fluxo à prova de falhas para executar o projeto.

1. Clonar o repositório público e acessar a pasta raiz:

```bash
git clone https://github.com/esdrasfranca/teste-ateliware.git && cd <NOME_DA_PASTA>
```

2. Baixar as imagens e subir todos os containers:

```bash
docker compose up -d --build
```

3. Instalar as dependências do Composer dentro do container de aplicação (visto que a pasta vendor fica de fora do Git):

```bash
docker compose exec app composer install
```

4. Gerar a chave de segurança única do Laravel 12:

```bash
docker compose exec app php artisan key:generate
```

5. Conceder permissões totais de escrita para as pastas de logs e cache no ecossistema Linux Alpine:

```bash
docker compose exec app chmod -R 777 storage bootstrap/cache
```

## Endpoints do Ecossistema

Após o deploy dos containers, o avaliador pode acessar:

- Front-end: `http://localhost:3000`
- Back-end (API): `http://localhost:8000`

##  Endpoints API:

```
Para testar apenas a API, foi incluída uma collection do Postman com todos os endpoints configurados. O arquivo encontra-se na raiz do projeto backend com o nome de Ateliware.postman_collection.json
```

- [GET] http://localhost:8000/api/pokemon?name=[NOME_POKEMON]
```text
http://localhost:8000/api/pokemon?name=charizard
```
- [GET] http://localhost:8000/api/pokemons?page=0
- [GET] http://localhost:8000/api/battle?pokemon1=[NOME_POKEMON]&pokemon2=[NOME_POKEMON]
```text
http://localhost:8000/api/battle?pokemon1=pikachu&pokemon2=charizard
```

## Testes Automatizados

A aplicação conta com testes unitários cobrindo toda a regra de negócio:

- Validação dos DTOs
- Consumo e tratamento de respostas da PokéAPI
- Lógica de batalha baseada em maior Hit Point

Para rodar os testes dentro do container:

```bash
docker compose exec app php artisan test
```

---

Esse README foi elaborado para oferecer uma visão técnica completa, permitindo ao avaliador rodar o projeto com segurança e analisar a decisão arquitetural BFF em um contexto de monorepo.
