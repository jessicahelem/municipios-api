<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MunicipiosController extends Controller
{
    public function index($uf)
    {
        $provider = config('app.municipios_provider', 'brasilapi');
        $cacheKey = "municipios_{$uf}_{$provider}";

        try {
            // Usando cache para armazenar os dados de municípios
            $cacheData = Cache::get($cacheKey);

            // Se os dados não estiverem no cache, fazer a requisição à API
            if (!$cacheData) {
                $response = match ($provider) {
                    'brasilapi' => $this->fetchFromBrasilAPI($uf),
                    'ibge' => $this->fetchFromIBGE($uf),
                    default => throw new \Exception('Provider inválido')
                };

                // Verifica se a resposta é uma instância de JsonResponse e extrair os dados JSON corretamente
                if ($response instanceof \Illuminate\Http\Client\Response) {
                    $cacheData = $response->json();  // Extrai os dados JSON da resposta HTTP
                } else {
                    // Se já estiver no formato correto (array), podemos prosseguir
                    $cacheData = $response;
                }

                // Salvar no cache por 30 minutos
                Cache::put($cacheKey, $cacheData, now()->addMinutes(30));
            }

            // Definindo a quantidade de itens por página
            $itemsPerPage = 10;

            // Página atual (se não for passada, padrão para 1)
            $page = request()->get('page', 1);

            // Calculando a posição inicial dos itens para a página atual
            $start = ($page - 1) * $itemsPerPage;

            // Paginação: usando slice para pegar apenas os itens da página atual
            $paginatedResponse = collect($cacheData) // Garantindo que $cacheData seja uma coleção
            ->slice($start, $itemsPerPage)
                ->values(); // Reseta as chaves do array

            // Calculando o número total de páginas
            $totalPages = ceil(count($cacheData) / $itemsPerPage);

            return response()->json([
                'data' => $paginatedResponse, // Dados da página atual
                'current_page' => $page,      // Página atual
                'per_page' => $itemsPerPage,  // Itens por página
                'total' => count($cacheData), // Total de itens
                'total_pages' => $totalPages  // Total de páginas
            ], 200);
        }catch (\Exception $e) {
            // Tratamento de exceções
            return response()->json([
                'error' => 'Ocorreu um erro ao processar a solicitação.',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    private function fetchFromBrasilAPI($uf)
    {
        try {
            // Requisição à API BrasilAPI para obter os municípios
            $response = Http::get("https://brasilapi.com.br/api/ibge/municipios/v1/{$uf}");

            // Verifica se a resposta foi bem-sucedida
            if ($response->failed()) {
                throw new \Exception('Erro ao consultar a API BrasilAPI');
            }

            $municipios = $response->json(); // Converte para um array

            return array_map(fn ($item) => [
                'name' => $item['nome'],
                'ibge_code' => $item['id'],
            ], $municipios);

        }
        catch (\Exception $e) {

         throw new \Exception('Falha ao buscar dados na BrasilAPI: ' . $e->getMessage());

        }



    }

    private function fetchFromIBGE($uf)
    {
        try {


            // Requisição à API IBGE para obter os municípios
            $response = Http::get("https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$uf}/municipios");

            $municipios = $response->json(); // Converte para um array

            // Retornando um array de municípios com os dados necessários

            return array_map(fn($item) => [
                'name' => $item['nome'],
                'ibge_code' => $item['id']
            ], $municipios);
        }
        catch (\Exception $e) {

            throw new \Exception('Falha ao buscar dados na API IBGE: ' . $e->getMessage());
        }
    }





}
