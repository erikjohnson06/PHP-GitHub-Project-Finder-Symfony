<?php

namespace App\Repository;

use App\Entity\GitHubRepositoryRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GitHubRepositoryRecord>
 *
 * @method GitHubRepositoryRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method GitHubRepositoryRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method GitHubRepositoryRecord[]    findAll()
 * @method GitHubRepositoryRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GitHubRepositoryRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GitHubRepositoryRecord::class);
    }

    /**
     * @param GitHubRepositoryRecord $entity
     * @param bool $flush
     * @return void
     */
    public function save(GitHubRepositoryRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param GitHubRepositoryRecord $entity
     * @param bool $flush
     * @return void
     */
    public function remove(GitHubRepositoryRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Returns an (iterable) array of GitHubRepositoryRecord objects. Use iterable for better memory usage, rather than all entities in an array.
     * 
     * @return iterable|null
     */
    public function getProjectList(): ?iterable
    {
        
        $qb = $this->createQueryBuilder('g')
            ->orderBy('g.stargazers_count', 'DESC');
        
        if ($qb->getQuery()){
            return $qb->getQuery()->toIterable();
        }
        
        return null;
    }
    
    /**
     * Fetch the project detail based on a given repository Id
     * 
     * @param int $value
     * @return GitHubRepositoryRecord|null
     */
    public function findOneByRepositoryId(int $value): ?GitHubRepositoryRecord
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.repository_id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    /**
     * Return a count of GitHubRepositoryRecord entities in the database
     *
     * @return int
     */
    public function getProjectListRecordCount() : int {
        
        $count = 0;
        
        $query = $this->getEntityManager()->createQuery(
            "SELECT count(g.id) AS cnt FROM App\Entity\GitHubRepositoryRecord g"
        );

        $results = $query->getResult();

        if ($results && isset($results[0]['cnt'])){
            $count = (int) $results[0]['cnt'];
        }
        
        return $count;
    }
}
