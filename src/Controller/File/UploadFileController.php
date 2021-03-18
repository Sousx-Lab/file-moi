<?php

namespace App\Controller\File;

use App\Entity\Auth\User;
use App\Entity\File\File;
use App\Form\File\FileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class UploadFileController extends AbstractController
{
    /**
     * @Route("/upload", name="route_file_uplaod", methods="POST")
     * @return Response
     */
    public function upload(FormInterface $form, ?User $user, EntityManagerInterface $em): ?Response
    {
        $file = $form->getData();
        
        $user = $this->getUser();
        if ($file instanceof File) {
            if ($user) {
                $file->addUser($user);
            }
            $em->persist($file);
            $em->flush();
            $this->addFlash('success', 'File has been uploaded successfuly');
            return $this->redirectToRoute('route_file_download', ['id' => $file->getId()]);
        }
    }
}
