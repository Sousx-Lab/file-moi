<?php
namespace App\Controller\File;

use App\Entity\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadFileController extends AbstractController
{
    /**
     * @Route("/{id}", requirements={"id": "%routing.uuid%"}, name="route_file_download")
     * @return Response
     */
    public function download(File $file = null): Response
    {
        if(null === $file){
            throw new NotFoundHttpException('File not found !');
        }
        return $this->render('file/download/file.download.html.twig',[
            'file' => $file
        ]);
    }

}