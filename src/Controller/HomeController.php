<?php

namespace App\Controller;

use App\Form\File\FileFormType;
use App\Entity\File\Data\FileData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FileServices\UploadFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="route_homepage")
     * @return Response
     */
    public function index(
        Request $request,
        UploadFileService $uploadService,
    ): Response {

        $fileData = new FileData();

        $form = $this->createForm(FileFormType::class, $fileData);
        $form->handleRequest($request);
        $uploadedFiles = $request->files->get('files') ?? [];

        if ($form->isSubmitted()) {

            if (!$form->isValid() || count($uploadedFiles)  === 0) {
                if (count($uploadedFiles) === 0) {
                    $form->get('files')->addError(new FormError('No file has been uploaded'));
                }
                return new Response(
                    $this->renderView('home/home.html.twig', [
                        'form' =>  $form->createView(),
                    ]),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            try {
                $files = $uploadService->UploadFile($uploadedFiles, $this->getUser());
                $view = 'file/upload/uploaded.file.download.html.twig';
                if (false !== strpos($request->headers->get('accept'), 'text/vnd.turbo-stream.html')) {
                    $view = 'file/upload/stream.file_download.html.twig';
                }

                return new Response(
                    $this->renderView($view, [
                        'files' => $files
                    ]),
                    Response::HTTP_OK,
                    [
                        'Content-Type' => 'text/vnd.turbo-stream.html'
                    ]
                );
            } catch (\Throwable $e) {
                $form->get('files')->addError(new FormError($e->getMessage()));
                return new Response(
                    $this->renderView('home/home.html.twig', [
                        'form' =>  $form->createView(),
                    ]),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        }
        return $this->render('home/home.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
