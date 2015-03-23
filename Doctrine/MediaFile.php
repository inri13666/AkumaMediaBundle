<?php
/**
 * User  : Nikita.Makarov
 * Date  : 2/17/15
 * Time  : 4:29 AM
 * E-Mail: nikita.makarov@effective-soft.com
 */

namespace Akuma\Bundle\MediaBundle\Doctrine;

use Akuma\Bundle\CoreBundle\Doctrine\Traits\EntityModifiedTrait;
use Akuma\Bundle\CoreBundle\Doctrine\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 *
 * @ORM\HasLifecycleCallbacks()
 */
abstract class MediaFile
{
    use EntityTrait;
    use EntityModifiedTrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $path;

    public function setPath($_)
    {
        $this->path = $_;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setName($_)
    {
        $this->name = $_;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setFile(File $file)
    {
        $this->file = $file;
        if ($this->file instanceof UploadedFile) {
            $this->setName($this->file->getClientOriginalName());
        } else {
            $this->setName($this->file->getFilename());
        }
        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "The maxmimum allowed file size is 5MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed."
     * )
     */
    protected  $file;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        switch (true) {
            case ($this->file instanceof UploadedFile):
                $this->setName($this->file->getClientOriginalName());
                $this->setPath(uniqid('image_') . '.' . $this->file->guessExtension());
                break;
            case ($this->file instanceof File):
                $this->setName($this->file->getFilename());
                $this->setPath(uniqid('image_') . '.' . $this->file->guessExtension());
                break;
            default:
                break;
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (!($this->file instanceof File)) {
            return;
        }
        $this->file->move($this->getUploadRootDir(), $this->getPath());
        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        //$this->file->move($this->getUploadRootDir(), $this->id.'.'.$this->file->guessExtension());

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getFullPath()) {
            unlink($file);
        }
    }


    abstract public function getWebPath();

    abstract public function getFullPath();

    abstract public function getUploadRootDir();
} 