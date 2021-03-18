<?php

namespace App\Controller;

use App\Controller\File\UploadFileController;
use App\Entity\File\File;
use App\Form\File\FileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController 
{

    /**
     * @Route("/", name="route_homepage")
     * @return Response
     */
    public function index(Request $request): Response
    {
        $error = null;
        $file = new File();

        $form = $this->createForm(FileFormType::class, $file);
        $form->handleRequest($request);

        return $this->render('home/home.html.twig',[
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }
}


