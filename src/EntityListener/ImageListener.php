<?php

namespace App\EntityListener;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class ImageListener
{
    /**
     * @var string
     */
    private string $imageDir;

    /**
     * @var string
     */
    private string $imageAbsoluteDir;

    /**
     * ImageListener constructor.
     * @param string $imageDir
     * @param string $imageAbsoluteDir
     */
    public function __construct(string $imageDir, string $imageAbsoluteDir)
    {
        $this->imageDir = $imageDir;
        $this->imageAbsoluteDir = $imageAbsoluteDir;
    }

    /**
     * @param Image $image
     */
    public function prePersist(Image $image): void
    {
        $this->upload($image);
    }

    /**
     * @param Image $image
     */
    public function preUpdate(Image $image): void
    {
        $this->upload($image);
    }

    /** @param Image $image
    */
   public function preRemove(Image $image): void
   {
        if (strpos($image->getSrc(),$this->imageDir) == 0) //On vérifie si src contient imageDir
        {
            $path=substr($image->getSrc(),strlen($this->imageDir)+1); //on extrait le filename
            @unlink(sprintf("%s/%s",$this->imageAbsoluteDir,$path)); //on génère le chemin absolue + on supprime le fichier avc unlink (@pour ne pas générer d'erreur)
        }
   }

    /**
     * @param Image $image
     */
    private function upload(Image $image): void
    {
       if ($image->getFile() instanceof UploadedFile) {
            $filename = sprintf("%s.%s",Uuid::v4(), $image->getFile()->getClientOriginalExtension());
            $image->getFile()->move($this->imageAbsoluteDir, $filename);
            $image->setSrc(sprintf("%s/%s",$this->imageDir, $filename));
        }
    }
}    
