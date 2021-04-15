<?php

namespace App\Services\FileServices\Helper;

use App\Entity\File\File;
use Doctrine\Common\Annotations\Reader;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class FileMapping
{
    private ParameterBagInterface $prameter;

    private Reader $reader;

    private UploaderHelper $helper;

    public function __construct(ParameterBagInterface $parameter, Reader $reader, UploaderHelper $helper)
    {
        $this->prameter = $parameter;
        $this->reader = $reader;
        $this->helper = $helper;
    }

    /**
     * @param File $file
     * @return array
     */
    public function getMapping(File $file): array
    {
        $reflClass = new \ReflectionClass($file);
        $reflProp = $reflClass->getProperty('uploadedFile');
        
        /**Get annotation property in File object */
        $mappingAnnotation = $this->reader->getPropertyAnnotation($reflProp, UploadableField::class);

        $fileMapping = $this->getVichUploaderMappings($mappingAnnotation->getMapping());

        $mapping  = \array_merge(
            [
            'mapping' => $mappingAnnotation->getMapping(),
            "relative_path" => $this->getRelativePathName($file)
            ], 
            $fileMapping
        );
        return $mapping;
    }

    /**
     * @param string $mappingName
     * @return array
     */
    public function getVichUploaderMappings(string $mappingName): array
    {     
        return $this->prameter->get('vich_uploader.mappings')[$mappingName];
    }

    /**
     * @param File $file
     * @return string
     */
    public function getRelativePathName(File $file): string
    {
        return  $this->helper->asset($file);
    }
}
