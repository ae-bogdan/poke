<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\Team;
use App\Entity\Type;
use App\Form\Type\PokemonType;
use App\Form\Type\TypeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route("/team")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/create", name="create_team")
     */
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $teams = $this->handleNewPokemon($form, $em, $request);
            $teamId = $teams[count($teams)-1]->getId();
            return $this->redirectToRoute('edit_team', [
                'id' => $teamId,
            ]);
        }
        return $this->render('team/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_team")
     */
    public function edit(int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $team = $em->getRepository(Team::class)->find($id);

        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon, [
            'team_id' => $team->getId(),
            'team_name' => $team->getName(),
            'pokemon_required' => false,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $this->handleNewPokemon($form, $em, $request, $team->getId());
        }

        $pokemons = $team->getPokemon();
        return $this->render('team/edit.html.twig', [
            'form' => $form->createView(),
            'pokemons' => $pokemons,
        ]);
    }

    /**
     * @Route("/list", name="list_team")
     */
    public function list(ManagerRegistry $doctrine): Response
    {
        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);
        return $this->render('team/list.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/fetch", name="fetch_team")
     */
    public function getTeams(Request $request, ManagerRegistry $doctrine): Response
    {
        $typeIds = [];
        $data = $request->request->get('type');
        if (!empty($data['id'])) {
            $typeIds = $data['id'];
        }
        $em = $doctrine->getManager();
        $cache = new FilesystemAdapter();
        $teams = $cache->get('list' . md5(json_encode($typeIds)), function(ItemInterface $item) use ($typeIds, $em) {
            $item->expiresAfter(3600);
            return $em->getRepository(Team::class)->findAllDesc($typeIds);
        });
        return new JsonResponse($teams);
    }

    private function handleNewPokemon($form, $em, $request, $teamId = null) {
        $pokemon = $em->getRepository(Pokemon::class)->findOneBy(['pokemon_id' => $form->getData()->getPokemonId()]);
        if (empty($pokemon)) {
            $pokemon = $form->getData();
        } else {
            $pokemon->setName($form->getData()->getName());
            $pokemon->setExp($form->getData()->getExp());
        }
        $pokemonData = $request->request->get('pokemon');
        $types = $pokemonData['type'] ?? [];
        foreach ($types as $typeId) {
            $type = $em->getRepository(Type::class)->find($typeId);
            $type->addPokemon($pokemon);
        }
        $teamName = $request->request->get('pokemon')['team_name'];

        if (empty($teamId)) {
            $team = new Team();
        } else {
            $team = $em->getRepository(Team::class)->find($teamId);
        }
        $team->setName($teamName);

        if (empty($pokemon->getPokemonId())) {
            $em->persist($team);
        } else {
            $team->addPokemon($pokemon);
            $em->persist($pokemon);
        }
        $em->flush();

        // Delete the base result for the cache
        $cache = new FilesystemAdapter();
        $cache->clear();

        return $pokemon->getTeam();
    }
}
