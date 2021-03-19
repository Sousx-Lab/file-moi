<?php
namespace App\Controller\File;

use App\Entity\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DownloadFileController extends AbstractController
{
    /**
     * @Route("/{id}", requirements={"id": "%routing.uuid%"}, name="route_file_download")
     * @return Response
     */
    public function download(Request $request, File $file)
    {
        return $this->render('file/download/file.download.html.twig',[
            'file' => $file
        ]);
    }

}