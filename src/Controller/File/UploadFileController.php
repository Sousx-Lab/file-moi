<?php

namespace App\Controller\File;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FileServices\UploadFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UploadFileController extends AbstractController
{
    /**
     * @Route("/upload", name="route_file_upload", methods="POST")
     * @return Response
     */
    public function upload(Request $request, UploadFileService $uploadService): Response
    {

        $error = null;
        $token = $request->request->get('_token');

        if (false === $this->isCsrfTokenValid('upload', $token)) {
            throw new BadRequestHttpException('Invalid Csrf token', null, 400);
            
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
