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

    public function save(GitHubRepositoryRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GitHubRepositoryRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return GitHubRepositoryRecord[] Returns an array of GitHubRepositoryRecord objects
     */
    public function getProjectList(): array
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.stargazers_count', 'DESC')
            ->getQuery()
            ->getResult();
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
    

    public function getProjectListRecordCount() : int {
        
        $count = 0;
        
        try {
            
            $query = $this->getEntityManager()->createQuery(
                "SELECT count(g.id) AS cnt FROM App\Entity\GitHubRepositoryRecord g"
            );

            $results = $query->getResult(); //Returns DateTime object, if found
                        
            if ($results && isset($results[0]['cnt'])){
                $count = (int) $results[0]['cnt'];
            }
        } 
        catch (\Exception $ex) {
            //$this->error_msg = "Request Manager Error: " . $ex->getMessage();
            //log_message("error", $this->error_msg);
        }
        
        return $count;
    }
    
    
//    /**
//     * @return GitHubRepositoryRecord[] Returns an array of GitHubRepositoryRecord objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GitHubRepositoryRecord
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
