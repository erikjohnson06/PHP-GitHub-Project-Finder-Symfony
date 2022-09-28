<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Classes\ReturnPayload;
use App\Entity\GitHubRepositoryRecord;
use App\Repository\GitHubRepositoryRecordRepository;

class ProjectFinderController extends AbstractController {
    
    public function index(): Response
    {
        
        $userFirstName = "...";
        $userNotifications = ["...", "..."];
        
        return $this->render("project_finder.html.twig", [
            "test1" => $userFirstName,
            "test2" => $userNotifications
            ]);
    }
    
    public function ajaxTest(Request $request): Response 
    {
        
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = new \stdClass();
        $data->data = new \stdClass;
        $data->data->project_data = null;
        
        $results = array($data);
        
        return new JsonResponse($results);
    }
    
    public function getProjectList(Request $request): Response 
    {
        
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = new ReturnPayload();
        $data->data = new \stdClass();
        
        $data->data->project_data = null; //$model->getProjectList();
        $data->data->last_updated = null;//$model->getLastUpdateTime();
        
        //if ($model->getErrorMsg()){
        //    $data->error = true;
        //    $data->error_msg = $model->getErrorMsg();            
        //}
        
        /*
        $request->request->get("id", 1);
        $request->query->get("id", 2);
        
        $data = new \stdClass();
        $data->data = new \stdClass;
        $data->data->project_data = null;
        
        $results = array($data);
        */
        
        return new JsonResponse($data);
    }
    
    public function getProjectListDetail(Request $request): Response 
    {
        
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = new ReturnPayload();
        $data->data = new \stdClass();
        
        return new JsonResponse($data);
    }
    
    public function loadGitHubProjects(Request $request): Response 
    {
        
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = new ReturnPayload();
        $data->data = new \stdClass();
        

        
        return new JsonResponse($data);
    }
}

