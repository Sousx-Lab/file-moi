<?php

namespace App\Controller\File;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FileServices\UploadFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadFileController extends AbstractController
{
    /**
     * @Route("/upload", name="route_file_upload", methods="POST")
     * @return Response
     */
    public function upload(Request $request, UploadFileService $uploadService): Response
    {
        $error = null;
        $files = null;
        if ($request->getMethod() === Request::METHOD_POST) {
            $token = $request->request->get('_token');

            if ($this->isCsrfTokenValid('upload', $token)) {
                if ($uploadedFiles = $request->files->get('files')) {
                    try {
                        $files = $uploadService->UploadFile($uploadedFiles, $this->getUser());

                    } catch (\Throwable $e) {
                        $error = $e->getMessage();
                    }
                    
                    return $this->render('file/download/files.download.html.twig',[
                        'files' => $files,
                        'error' => $error
                    ]);
                }
            }
        }
    }
}
