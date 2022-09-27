<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}

