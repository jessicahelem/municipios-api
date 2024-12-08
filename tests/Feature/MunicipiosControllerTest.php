<?php

namespace Tests\Feature;


use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\MunicipiosController;
use Illuminate\Http\Client\Response;


class MunicipiosControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function testFetchMunicipiosBrasilAPI()
    {
        // Simular a resposta da API BrasilAPI
        $uf = 'RS';
        $municipios = [
            ['nome' => 'Porto Alegre', 'id' => 4314902],
            ['nome' => 'Caxias do Sul', 'id' => 4305108],
        ];

        // Mock da resposta HTTP
        Http::fake([
            "https://brasilapi.com.br/api/ibge/municipios/v1/{$uf}" => Http::response($municipios, 200),
        ]);

        // Instância do controlador
        $controller = new MunicipiosController();

        // Teste da função que faz a chamada à API
        $response = $controller->fetchFromBrasilAPI($uf);

        // Verificar se a resposta é a esperada
        $this->assertIsArray($response);
        $this->assertCount(2, $response); // Verifica que temos 2 municípios na resposta
        $this->assertEquals('Porto Alegre', $response[0]['name']);
        $this->assertEquals(4314902, $response[0]['ibge_code']);
    }


    /**
     * Testando se o cache está funcionando corretamente.
     *
     * @return void
     */
    public function testCacheUsage()
    {
        $uf = 'RS';
        $cacheKey = "municipios_{$uf}_brasilapi";

        // Simulando dados que estariam em cache
        $cacheData = [
            ['name' => 'Porto Alegre', 'ibge_code' => 4314902],
            ['name' => 'Caxias do Sul', 'ibge_code' => 4305108],
        ];

        // Definindo os dados no cache
        Cache::put($cacheKey, $cacheData);

        // Verificando se os dados estão sendo recuperados do cache
        $cachedData = Cache::get($cacheKey);

        $this->assertEquals($cacheData, $cachedData);
    }

    public function testFetchMunicipiosIntegration()
    {
        $response = $this->getJson('/municipios/RS'); // Requisição para o endpoint

        // Verificando se a resposta contém a chave 'data' com os itens esperados
        $response->assertStatus(200)  // Verifica se o status é 200 (OK)
        ->assertJsonStructure([ // Verifica a estrutura do JSON
            'data' => [
                '*' => ['name', 'ibge_code']
            ]
        ])->assertJsonCount(2, 'data'); // Verifica se há 2 itens em 'data'
    }




}
