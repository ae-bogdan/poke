<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PokemonController extends AbstractController
{
    const TOTAL_POKEMON = 905;

    /**
     * @Route("/pokemon/catch", name="catch_pokemon")
     */
    public function catch(): Response
    {
        $pokemonId = rand(1, self::TOTAL_POKEMON);
        $Client = new Client();
        try {
            $response = $Client->request('GET', 'https://pokeapi.co/api/v2/pokemon/' . $pokemonId);
        } catch (RequestException $e) {
            throw new Exception($e->getMessage());
        }
        $pokemon = json_decode($response->getBody(), true);
        $data = [
            'id' => $pokemon['id'],
            'name' => $pokemon['name'],
            'base_exp' => (int)$pokemon['base_experience'],
            'image' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . $pokemon['id'] . '.png',
            'abilities' => [],
            'types' => [],
        ];
        foreach ($pokemon['abilities'] as $ability) {
            $data['abilities'][] = $ability['ability']['name'];
        }
        foreach ($pokemon['types'] as $type) {
            $typeId = explode('/', $type['type']['url'])[6];
            $data['types'][$typeId] = $type['type']['name'];
        }
        return new JsonResponse($data);
    }
}
