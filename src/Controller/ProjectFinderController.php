<?php

namespace App\Controller;

use App\Entity\GitHubRepositoryRecord;
use App\Entity\GitHubProjectsRequestManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

//Include custom classes for this project - the AutoLoader would be ideal here, but was not working as expected with the correct namespaces
use App\Classes\ReturnPayload;
use App\Classes\GitHubRepositoryRecordJS;
use App\Classes\GitHubRepositoryRecordDetailJS;
use App\Classes\GitHubApiCurlRequest;
//use App\Classes\AutoLoader;

use DateTime;

//Include basic PSR Logger 
use Psr\Log\LoggerInterface;

class ProjectFinderController extends AbstractController {
    
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {                
        $this->logger = $logger;
    }  
    
    public function index(Request $request): Response
    {

        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //Restrict to authenticated users only
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        return $this->render("project_finder.html.twig");
    }
        
    /**
     * Controller for retrieving projects from the database.
     * 
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws BadRequestHttpException
     */
    public function getProjectList(Request $request, ManagerRegistry $doctrine): Response 
    {

        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = new ReturnPayload();
        $data->data = new \stdClass();
        $data->data->project_data = null;
        $data->data->last_updated = null;
                
        $entityManager = $doctrine->getManager();
        
        $records = $entityManager->getRepository(GitHubRepositoryRecord::class)->getProjectList(); //Yields an iterator for better memory usage
        $update_time = $entityManager->getRepository(GitHubProjectsRequestManager::class)->getLastUpdateTime();
        
        if ($records){
            
            $data->data->project_data = [];
            
            foreach ($records as $record){
                
                $obj = new GitHubRepositoryRecordJS; //Use JS version of class for interfacing with JS data
                $obj->name = $record->getName();
                $obj->repository_id = $record->getRepositoryId();
                $obj->stargazers_count = $record->getStargazersCount();
                
                $data->data->project_data[] = $obj;
            }
        }
        
        //Fetch the last update time
        if ($update_time){
            $data->data->last_updated = $update_time;
        }
        
        return new JsonResponse($data);
    }
    
    /**
     * Controller for retrieving detail for a particular project based on repository ID
     * 
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws BadRequestHttpException
     */
    public function getProjectListDetail(Request $request, ManagerRegistry $doctrine): Response 
    {
        
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = new ReturnPayload();
        $data->data = new \stdClass();
        
        $repo_id = (int) $request->query->get("repo_id", 0);
        
        if (!$repo_id){
            $data->error = true;
            $data->error_msg = "Invalid repository";
            return new JsonResponse($data);
        }
        
        try {
            
            $entityManager = $doctrine->getManager();

            $record = $entityManager->getRepository(GitHubRepositoryRecord::class)->findOneByRepositoryId($repo_id);

            if ($record){

                $data->data->project_data = new GitHubRepositoryRecordDetailJS; //Use JS version of detail class for interfacing with JS data
                $data->data->project_data->name = utf8_decode(htmlentities($record->getName()));
                $data->data->project_data->html_url = htmlentities($record->getHtmlUrl());
                $data->data->project_data->description = utf8_decode(htmlentities($record->getDescription()));
                $data->data->project_data->repository_id = $record->getRepositoryId();
                $data->data->project_data->stargazers_count = $record->getStargazersCount();

                //Format DateTime objects
                if ($record->getCreatedAt()){
                    $data->data->project_data->created_at = $record->getCreatedAt()->format("n/j/Y g:i A");
                }

                if ($record->getPushedAt()){
                    $data->data->project_data->pushed_at = $record->getPushedAt()->format("n/j/Y g:i A");
                }
            }
        } 
        catch (Exception | Doctrine\ORM\NonUniqueResultException $ex) {
            $this->logger->error($ex->getMessage());
        }

        return new JsonResponse($data);
    }
    
    /**
     * Controller for calling GitHub API and updating database with fresh projects. 
     * 
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function loadGitHubProjects(Request $request, ManagerRegistry $doctrine): Response 
    {
        
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = new ReturnPayload();
        $data->data = new \stdClass();
        $data->data->project_data = null;
        $data->data->last_updated = null;

        $agent = $request->server->get("HTTP_USER_AGENT"); //Retreive the user agent for the cUrl request
                        
        $entityManager = $doctrine->getManager();
        
        $initial_count = $entityManager->getRepository(GitHubRepositoryRecord::class)->getProjectListRecordCount();
                
        /**
         * Note: according to GitHub documentation, search results are limited to up to 1,000 results for each search, 
         * with up to 100 results per page, and up to 30 requests per minute (if using basic authentication, OAuth, or 
         * client ID / secret), or up to 10 per minute for unauthenticated requests. For this reason, one could be creative 
         * in the way you submit the search requests by slicing the search into chunks. For example, one approach might be 
         * to search for PHP projects by year starting with the current year (created:2022) and continuing x number of years. 
         * Another approach might be to search by specific ranges of stars (stars:>=5000, stars:1000..2000, etc). Using either 
         * one of these approaches would yield more results than what you would otherwise get from the simple search we are 
         * using here. For this project, however, I am only searching for the PHP projects with the greatest number of 
         * stars because the requirements were to “retrieve the most-starred public PHP projects”.
         * 
         * For reference: 
         * https://docs.github.com/en/rest/search#about-the-search-api         * 
         */
        
        $curl = new GitHubApiCurlRequest();
        $curl->init_cURL();
        $curl->setUserAgent($agent);
        $curl->setPerPage(100); //Max block is 100 per request
        
        try {
                        
            //Prevent running multiple requests from multiple users being submitted. A queueing method might be more appropriate here.  
            if ($entityManager->getRepository(GitHubProjectsRequestManager::class)->isRequestProcessRunning()){
                $data->error = true;
                $data->error_msg = "Request is already running. Try again once complete.";
                return new JsonResponse($data);
            }
            
            //Mark the request as started
            $entityManager->getRepository(GitHubProjectsRequestManager::class)->updateRequestManager(true);
            
            $page = 1;
            $number_of_requests = 0;
            $iso_format = "Y-m-d\TH:i:sO"; //Default GitHub Date format
                        
            while (true){
                                
                $curl->setPageNumber($page);

                $items = $curl->submitGitSearchRequest();

                if ($curl->getErrorMsg()){
                    throw new \Exception($curl->getErrorMsg());
                }

                //Halt once the result set has been exhausted
                if (!$items){
                    break;
                }
                                
                foreach ($items as $item){
                                       
                    $record = $entityManager->getRepository(GitHubRepositoryRecord::class)->findOneByRepositoryId((int) $item->id);

                    //Update record, if found
                    if ($record){

                        $this->logger->info("Found existing records.." . print_r($record, true));
                        
                        $record->setName(trim($item->name));
                        $record->setHtmlUrl($item->html_url);
                        $record->setDescription(trim($item->description));
                        $record->setStargazersCount((int) $item->stargazers_count);
                        
                        $pushed_dt = DateTime::createFromFormat($iso_format, $item->pushed_at);
                        $record->setPushedAt($pushed_dt);
                        
                        $entityManager->persist($record);
                        continue;
                    }
                    
                    //Otherwise, extract the pertinent info from the items array and save new records to the db
                    $record = new GitHubRepositoryRecord;
                    $record->setRepositoryId((int) $item->id);
                    $record->setName(trim($item->name));
                    $record->setHtmlUrl($item->html_url);
                    $record->setDescription(trim($item->description));
                    $record->setStargazersCount((int) $item->stargazers_count);
                    
                    //GitHub datetimes are in the ISO 8601 format. Convert these to MySQL datetimes.        
                    $create_dt = DateTime::createFromFormat($iso_format, $item->created_at);
                    $pushed_dt = DateTime::createFromFormat($iso_format, $item->pushed_at);
                    
                    $record->setCreatedAt($create_dt);
                    $record->setPushedAt($pushed_dt);
                                        
                    //Prepare the repository record for an update
                    $entityManager->persist($record);
                }

                //Insert records into DB
                $entityManager->flush();
                
                $page++; //Increase pagination
                $number_of_requests++;
                
                //GitHub limits 30 requests per minute.
                if ($number_of_requests > 30){
                    break;
                }
            }
        }
        catch (\Exception $ex){
            $data->error_msg = "cURL Request Error: " . $ex->getMessage();
            $this->logger->error($data->error_msg);
        }
        finally {
            
            //Close the cURL connection
            $curl->close_cURL(); 
            
            //Mark the request manager as complete 
            $entityManager->getRepository(GitHubProjectsRequestManager::class)->updateRequestManager(false, $data->error_msg);
        }

        //After cURL request and update is complete, return an updated list of projects. 
        $records = $entityManager->getRepository(GitHubRepositoryRecord::class)->getProjectList();
        $update_time = $entityManager->getRepository(GitHubProjectsRequestManager::class)->getLastUpdateTime();
        
        if ($records){
            
            $data->data->project_data = [];
            
            foreach ($records as $record){
                
                $obj = new GitHubRepositoryRecordJS; //Use custom JS version of class for updating JS data
                $obj->name = $record->getName();
                $obj->repository_id = $record->getRepositoryId();
                $obj->stargazers_count = $record->getStargazersCount();
                
                $data->data->project_data[] = $obj;
            }
        }
        
        if ($update_time){
            $data->data->last_updated = $update_time;
        }

        $data->data->success_msg = "GitHub Projects updated successfully. ";

        if ($data->data->project_data){
            
            $updated_count = count($data->data->project_data);
            
            $diff = ($updated_count - $initial_count);
                        
            //If this is the initial upload, simply state the number of records found.
            if (!$initial_count){
                $data->data->success_msg .= "Uploaded " . $updated_count . " projects.";
            }
            //Otherwise, show how many more were added since the last pull. 
            else if ($diff > 0){
                $data->data->success_msg .= $diff . " additional projects added.";
            }
            else if ($diff == 0){
                $data->data->success_msg .= " No additional projects were found.";
            }
        }
        
        return new JsonResponse($data);
    }
}

