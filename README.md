# Projeto de Listagem de Municípios

Este projeto consiste em uma aplicação web que permite visualizar a listagem de municípios brasileiros por estado, com paginação e integração com a API BrasilAPI. O sistema é composto por uma **API** em Laravel que fornece os dados de municípios e um **frontend** simples utilizando HTML, jQuery e Bootstrap para exibição e navegação.

## Estrutura do Projeto

- **Backend (API)**: Utiliza o framework Laravel para fornecer os dados dos municípios.
- **Frontend (SPA)**: Implementado em HTML, jQuery e Bootstrap para a exibição dinâmica de dados e interatividade.

## Funcionalidades

- **Carregar Estados**: A partir da seleção de um estado, a aplicação carrega os municípios daquele estado.
- **Listagem de Municípios**: A listagem dos municípios é exibida em uma tabela com informações sobre o nome e código IBGE.
- **Paginação**: A navegação é feita por paginação, permitindo a visualização de dados de forma paginada.
- **Total de Municípios**: Exibe o total de municípios disponíveis na página selecionada.

## Tecnologias Utilizadas

- **Backend**:
  - Laravel 
  - Brasil API para consultar dados de municípios e estados
  - IBGE para consultar dados de municípios
  - Cache do Laravel para armazenar os dados por 30 minutos, evitando chamadas repetidas à API

- **Frontend**:
  - HTML
  - jQuery
  - Bootstrap 

## Como Funciona

### API (Backend)

A API é responsável por fornecer os dados dos municípios com base no estado selecionado. Para isso, ela utiliza as APIs externas da **BrasilAPI** e **IBGE** para consultar as informações.

1. **Endpoint de Municípios**:
   - URL: `/municipios/{uf}`
   - Método: `GET`
   - Parâmetros:
     - `uf`: A sigla do estado (ex: `RS` para Rio Grande do Sul).
   - Resposta: Retorna uma lista paginada de municípios com os campos `name` (nome do município) e `ibge_code` (código IBGE do município).

2. **Exemplo de Resposta**:
   ```json
   {
       "data": [
           {
               "name": "Porto Alegre",
               "ibge_code": 4314902
           },
           {
               "name": "Caxias do Sul",
               "ibge_code": 4305108
           }
       ],
       "current_page": 1,
       "per_page": 10,
       "total": 100,
       "total_pages": 10
   }

3. **A aplicação está rodando no servidor AWS no seguinte endereço: http://ec2-54-210-233-243.compute-1.amazonaws.com:8080/municipios**
