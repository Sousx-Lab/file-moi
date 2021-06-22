<?php

namespace App\Controller\File;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FileServices\UploadFileService;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Services\FileServices\FileSizeFormatterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UploadFileController extends AbstractController
{
    /**
     * @Route("/upload", name="route_file_upload", methods="POST")
     * @return Response
     */
    public function upload(Request $request, UploadFileService $uploadService, 
        FileSizeFormatterService $formatter,
        TranslatorInterface $translator
        ): Response
    {

        $error = null;
        $token = $request->request->get('_token');
        $uploadedFiles = $request->files->get('files');

        $maxSize = $formatter->format($this->getParameter('app.max_file_size'));

        if(null === $uploadedFiles)
        {  
           
           $this->addFlash('danger', 
                $translator->trans("The file size exceeds the allowed limit of %maxSize%",['%maxSize%' => $maxSize]));
           return $this->redirectToRoute("route_homepage", [], 303);
        }

        if (false === $this->isCsrfTokenValid('upload', $token)) {
            throw new UnauthorizedHttpException('Invalid Csrf token', null, null, 401);
            
        }

        $uploadedFiles = $request->files->get('files');
        if (empty($uploadedFiles)) {
            throw new UnprocessableEntityHttpException('No file has been uploaded', null, 422);
        
        }

        try {
            $files = $uploadService->UploadFile($uploadedFiles, $this->getUser());
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return $this->render('file/upload/uploaded.file.download.html.twig', [
            'files' => $files,
            'error' => $error
        ]);
    }
}
