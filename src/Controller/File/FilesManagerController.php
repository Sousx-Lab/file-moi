<?php
namespace App\Controller\File;

use App\Entity\Auth\User;
use App\Entity\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class FilesManagerController extends AbstractController
{
    /**
     * @Route("/my_files", name="route_files_manager")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {   
        /**@var User */
        $user = $this->getUser();
        
        return $this->render('file/filesManager/files.manager.html.twig', [
            "user" => $user,
            "files" => $user->getFiles()->getValues()
        ]);
    }

    /**
     * @Route("/my_files/file/{id}", requirements={"id": "%routing.uuid%"}, name="route_file_delete", methods="DELETE")
     */
    public function delete(File $file, Request $request): Response
    {
        if(null === $file)
        {
            $this->addFlash('error', 'File not exist');
            return $this->redirectToRoute('route_files_manager');
        }

        if($this->isCsrfTokenValid('deleteFile', $request->get('_token')) )
        {
            //Todo : add asynchrone service to delete file
            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush();
            $this->addFlash('success', 'File has been removed');
        }
        return $this->redirectToRoute('route_files_manager');
    }
}