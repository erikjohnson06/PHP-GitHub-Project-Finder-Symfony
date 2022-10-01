<?php

namespace App\Repository;

use App\Entity\GitHubProjectsRequestManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use DateTime;

/**
 * @extends ServiceEntityRepository<GitHubProjectsRequestManager>
 *
 * @method GitHubProjectsRequestManager|null find($id, $lockMode = null, $lockVersion = null)
 * @method GitHubProjectsRequestManager|null findOneBy(array $criteria, array $orderBy = null)
 * @method GitHubProjectsRequestManager[]    findAll()
 * @method GitHubProjectsRequestManager[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GitHubProjectsRequestManagerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GitHubProjectsRequestManager::class);
    }

    /**
     * @param GitHubProjectsRequestManager $entity
     * @param bool $flush
     * @return void
     */
    public function save(GitHubProjectsRequestManager $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param GitHubProjectsRequestManager $entity
     * @param bool $flush
     * @return void
     */
    public function remove(GitHubProjectsRequestManager $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Fetch the last time the request manager ended
     * 
     * @return string
     */
    public function getLastUpdateTime() : string {
        
        $data = "";
                    
        $query = $this->getEntityManager()->createQuery(
            "SELECT g.end_time  
            FROM App\Entity\GitHubProjectsRequestManager g
            WHERE g.end_time IS NOT NULL"
        );

        $results = $query->getResult();

        //Format DateTime object, if found
        if ($results && isset($results[0]['end_time']) && is_a($results[0]['end_time'], "DateTime")){
            $data = $results[0]['end_time']->format("n/j/Y g:i A");
        }
        
        return $data;
    }
    
    
    /**
     * Use the request manager to determine if another cURL request is currently underway. Return true if it is, false if not. 
     * 
     * @return boolean
     */
    public function isRequestProcessRunning(): bool {
                            
        $query = $this->getEntityManager()->createQuery(
            "SELECT g.is_running FROM App\Entity\GitHubProjectsRequestManager g"
        );

        $results = $query->getResult();

        //It's possible that the table is empty. If so, add the required entry into the table. 
        if (!$results || !isset($results[0])){

            $entity = new GitHubProjectsRequestManager;
            $entity->setIsRunning(0);

            $this->save($entity, true);
            return false;
        }

        //Otherwise, check whether the request manager is marked as running
        if ($results && isset($results[0]['is_running']) && $results[0]['is_running'] == 1){
            return true;
        }
        
        return false;
    }
    
    /**
     * Update the request manager table, marking start times, end times, and recording error messages
     * 
     * @param bool $starting_new_request
     * @param string $error_msg
     * @return bool
     */
    public function updateRequestManager(bool $starting_new_request = false, string $error_msg = "") : bool {
                            
        $query = $this->getEntityManager()->createQuery(
            "SELECT g FROM App\Entity\GitHubProjectsRequestManager g"
        );

        $results = $query->getResult();

        if ($results && isset($results[0])){
            $entity = $results[0];

            //Mark the request manager entity as "running"
            if ($starting_new_request){
                $entity->setIsRunning(1);
                $entity->setStartTime((new DateTime()));
            }
            //Otherwise, mark it as complete
            else {
                $entity->setIsRunning(0);
                $entity->setEndTime((new DateTime()));

                //Record any error messages upon completion
                if ($error_msg){
                    $entity->setErrorMsg($error_msg);
                }
            }

            $this->save($entity, true);
        }
        
        return false;
    }
}
