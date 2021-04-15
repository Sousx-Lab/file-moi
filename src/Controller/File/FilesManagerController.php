<?php

namespace App\Controller\File;

use App\Entity\Auth\User;
use App\Entity\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FileServices\FileManagerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
     * Delete file to list files
     * @Route("/my_files/file/{id}", requirements={"id": "%routing.uuid%"}, name="route_file_delete", methods="DELETE")
     */
    public function delete(string $id, Request $request, FileManagerService $service): Response
    {
        /**@var User */
        $user = $this->getUser();
        
        if ($this->isCsrfTokenValid('deleteFile', $request->get('_token'))) {
            try {
                $service->unsetFile($user, $id);

                $this->addFlash('success', 'File has been removed');
                return $this->redirectToRoute('route_files_manager');
            } catch (\Throwable $e) {

                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('route_files_manager');
            }
        }

        throw new UnauthorizedHttpException('Invalid Csrf token', null, null, 401);
    }
}
