<?php
namespace App\Controller\File;

use App\Entity\Auth\User;
use App\Entity\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddFileToListController extends AbstractController
{
    /**
     * @Route("/add/{id}", requirements={"id": "%routing.uuid%"}, name="route_file_add")
     * @return Response
     */
    public function addFile(File $file = null, Request $request, EntityManagerInterface $em): Response
    {
        if(null === $file){
            throw new NotFoundHttpException('File not found !');
        }
        if($this->isCsrfTokenValid("addUserFile", $request->get('_token')) )
        {
            /**@var User */
            $user = $this->getUser();
            $user->addFile($file);
            $em->flush();

            $this->addFlash("success", 'The file has been added to your list');
            return $this->redirectToRoute('route_file_download', ['id' => $file->getId()]);
        }
        return $this->render('file/download/file.download.html.twig',[
            'file' => $file
        ]);
    }

}