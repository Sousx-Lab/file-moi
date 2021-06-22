<?php

namespace App\Controller;

use App\Entity\File\Data\FileData;
use App\Form\File\FileFormType;
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
    public function index(): Response
    {
        $error = null;
        $fileData = new FileData();

        $form = $this->createForm(FileFormType::class, $fileData);
        
        return $this->render('home/home.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }
}
