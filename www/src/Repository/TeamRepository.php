<?php

namespace App\Repository;

use App\Entity\Pokemon;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function add(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Team[] Returns an array of Team objects
     */
    public function findAllDesc(array $typeIds = []): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->select('t.id AS team_id, t.name AS team_name, p.pokemon_id AS poke_id, p.name AS poke_name, p.exp AS poke_exp, ty.id AS ty_id, ty.name AS ty_name')
            ->innerJoin('t.pokemon', 'p')
            ->innerJoin('p.type', 'ty')
            ->orderBy('t.created', 'DESC');
        if (!empty($typeIds)) {
            $qb->andWhere('ty.id IN (:typeIds)')
                ->setParameters([
                    'typeIds' => $typeIds,
                ]);
        }
        $results = $qb->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
        $data = [];
        foreach ($results as $result) {
            $data[$result['team_id']] = $result;
            $data[$result['team_id']]['sum_exp'] = 0;
        }
        foreach ($results as $result) {
            foreach ($data as $teamId => $datum) {
                if ($result['team_id'] === $teamId) {
                    $data[$teamId]['pokemon'][$result['poke_id']] = [
                        'pokemon_id' => $result['poke_id'],
                        'pokemon_exp' => $result['poke_exp'],
                        'pokemon_name' => $result['poke_name'],
                    ];
                }
            }
        }
        foreach ($results as $result) {
            foreach ($data as $teamId => $datum) {
                if ($result['team_id'] === $teamId) {
                    foreach($datum['pokemon'] as $pokeId => $pokemon) {
                        if ($result['poke_id'] === $pokeId) {
                            $data[$teamId]['pokemon'][$pokeId]['type'][$result['ty_id']] = [
                                'type_name' => $result['ty_name'],
                                'type_id' => $result['ty_id'],
                            ];
                        }
                    }
                }
                unset($data[$teamId]['poke_id']);
                unset($data[$teamId]['poke_name']);
                unset($data[$teamId]['poke_exp']);
                unset($data[$teamId]['ty_id']);
                unset($data[$teamId]['ty_name']);
            }
        }
        foreach ($data as $id => $datum) {
            foreach ($datum['pokemon'] as $pokemon) {
                $data[$id]['sum_exp'] += $pokemon['pokemon_exp'];
            }
        }
        return $data;
    }

}
