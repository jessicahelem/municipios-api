<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1>Listagem de municípios</h1>

    <!-- Exibe o total de municípios -->
    <p id="totalMunicipios"></p>

    <!-- Select para os estados -->
    <select id="ufSelect" class="form-select mb-4" onchange="loadMunicipios(1)">
        <option value="">Selecione um Estado</option>
        <!-- Os estados serão carregados aqui  -->
    </select>

    <!-- Tabela de municípios -->
    <div id="municipios" class="row"></div>

    <!-- Paginação -->
    <nav id="pagination" class="mt-4" aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
        </ul>
    </nav>
</div>

<script>
    // Função para carregar os estados usando a BrasilAPI
    function loadEstados() {
        $.ajax({
            url: 'https://brasilapi.com.br/api/ibge/uf/v1',  // Endpoint da BrasilAPI para estados
            method: 'GET',
            success: function(response) {
                const ufSelect = $('#ufSelect');
                ufSelect.empty(); // Limpar opções existentes
                ufSelect.append('<option value="">Selecione um Estado</option>');

                // Preencher o select com os estados
                response.forEach(state => {
                    ufSelect.append(`<option value="${state.sigla}">${state.nome}</option>`);
                });
            },
            error: function(error) {
                console.error('Erro ao carregar estados:', error);
                alert('Erro ao carregar estados.');
            }
        });
    }

    // Função para carregar os municípios com base no estado selecionado
    function loadMunicipios(page = 1) {
        const uf = $('#ufSelect').val(); // Pega o estado selecionado

        if (!uf) {
            alert('Por favor, selecione um estado.');
            return;
        }

        $.ajax({
            url: `/municipios/${uf}?page=${page}`,
            method: 'GET',
            success: function(response) {
                const municipiosContainer = $('#municipios');
                const paginationContainer = $('#pagination');
                const totalMunicipios = $('#totalMunicipios');

                municipiosContainer.empty();
                paginationContainer.empty();
                totalMunicipios.empty();

                if (response.data.length > 0) {
                    // Exibir total de municípios
                    totalMunicipios.text(`Total de Municípios: ${response.total}`);

                    // Exibir os municípios
                    response.data.forEach(municipio => {
                        const municipioElement = `
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">${municipio.name}</h5>
                                        <p class="card-text">IBGE Code: ${municipio.ibge_code}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        municipiosContainer.append(municipioElement);
                    });
                } else {
                    municipiosContainer.append('<p class="text-center">Nenhum município encontrado.</p>');
                }

                // Exibir paginação
                if (response.total_pages > 1) {
                    const pagination = $('<ul class="pagination justify-content-center"></ul>');

                    // Botão de "Página anterior"
                    if (page > 1) {
                        pagination.append(`
                            <li class="page-item">
                                <button class="page-link" onclick="loadMunicipios(${page - 1})">Anterior</button>
                            </li>
                        `);
                    } else {
                        pagination.append(`
                            <li class="page-item disabled">
                                <button class="page-link">Anterior</button>
                            </li>
                        `);
                    }

                    // Botões de páginas
                    for (let i = 1; i <= response.total_pages; i++) {
                        const activeClass = (i === page) ? 'active' : '';
                        pagination.append(`
                            <li class="page-item ${activeClass}">
                                <button class="page-link" onclick="loadMunicipios(${i})">${i}</button>
                            </li>
                        `);
                    }

                    // Botão de "Próxima página"
                    if (page < response.total_pages) {
                        pagination.append(`
                            <li class="page-item">
                                <button class="page-link" onclick="loadMunicipios(${page + 1})">Próxima</button>
                            </li>
                        `);
                    } else {
                        pagination.append(`
                            <li class="page-item disabled">
                                <button class="page-link">Próxima</button>
                            </li>
                        `);
                    }

                    // Adiciona a paginação
                    paginationContainer.append(pagination);
                }
            },
            error: function(error) {
                console.error('Erro ao carregar municípios:', error);
                alert('Ocorreu um erro ao carregar os dados.');
            }
        });
    }


    $(document).ready(function() {
        loadEstados();
    });
</script>
</body>
</html>