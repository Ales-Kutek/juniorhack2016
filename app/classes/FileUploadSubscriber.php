<?php

//namespace Events;

use Gedmo\Uploadable\Event\UploadablePreFileProcessEventArgs;
use Gedmo\Uploadable\UploadableListener;
use Nette\Utils\DateTime;
use Gedmo\Uploadable\MimeType\MimeTypeGuesserInterface;
use Gedmo\Uploadable\Events;
use Gedmo\Uploadable\FileInfo\FileInfoInterface;
use Gedmo\Uploadable\FileInfo\FileInfoArray;
use Nette\FileNotFoundException;
use Nette\InvalidStateException;
use Nette\Http\FileUpload;

/**
 * Handle file uploads.
 *
 * EventSubscriber
 */
class FileUploadSubscriber extends UploadableListener
{

    private $fileName;
    
    public function setFileName($name) {
        $this->fileName = $name;
        
        return $this;
    }
    
    public function getFileName() {
        return $this->fileName;
    }
    
    /**
     * @param string $defaultPath
     * @param \Gedmo\Uploadable\MimeType\MimeTypeGuesserInterface $mimeTypeGuesser
     */
    public function __construct($defaultPath,
                                MimeTypeGuesserInterface $mimeTypeGuesser = NULL)
    {
        parent::__construct($mimeTypeGuesser);
        $this->setDefaultPath($defaultPath);
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return parent::getSubscribedEvents() + array(
            Events::uploadablePreFileProcess => 'preUpload'
        );
    }

    /**
     * Set file upload date and extension.
     *
     * @param UploadablePreFileProcessEventArgs $args
     */
    public function preUpload(UploadablePreFileProcessEventArgs $args)
    {
        $entity = $args->getEntity();
//		$entity->setUploadDate(new DateTime('now'));
//		$entity->setExtension((string)pathinfo($args->getFileInfo()->getName(), PATHINFO_EXTENSION));
    }

    /**
     * File info can be also directly Nette FileUpload object.
     *
     * @param mixed $entity
     * @param FileUpload|FileInfoInterface $file
     * @throws Nette\InvalidStateException
     */
    public function addEntityFileInfo($entity, $file)
    {
        if ($file instanceof FileUpload) {
            $file = $this->fileInfoFromFileUpload($file);
        } else if (!$file instanceof FileInfoInterface) {
            throw new Nette\InvalidStateException("Unexpected type of \$file. Expected \\Nette\\Http\\FileUpload or \\Gedmo\\Uploadable\\FileInfo\\FileInfoInterface.");
        }
        parent::addEntityFileInfo($entity, $file);
    }

    /**
     * FileUpload -> FileInfoArray
     *
     * @param \Nette\Http\FileUpload $file
     * @return \Gedmo\Uploadable\FileInfo\FileInfoArray
     */
    public function fileInfoFromFileUpload(FileUpload $file)
    {
        if ($this->fileName !== NULL) {
            $name = $this->fileName;
        } else {
            $name = $file->name;
        }
        
        return new FileInfoArray(array(
            'name' => $name,
            'tmp_name' => $file->temporaryFile,
            'size' => $file->size,
            'error' => $file->error,
            'type' => $file->contentType
        ));
    }

    /**
     * Set and validate default path
     * @param string $path
     * @throws FileNotFoundException
     * @throws InvalidStateException
     */
    public function setDefaultPath($path)
    {
        // Validate default path
        if (!file_exists($path) && !mkdir($path, 0777, TRUE)) {
            throw new FileNotFoundException("Default upload path: \"$path\" not found.");
        }

        if (!is_dir($path)) {
            throw new InvalidStateException("Default upload path: \"$path\" is not dir.");
        } else if (!is_writable($path)) {
            throw new InvalidStateException("Default upload path: \"$path\" is not writable.");
        }
        $path = self::normalizePath($path);
        parent::setDefaultPath($path);
    }

    protected static function normalizePath($path)
    {
        return realpath($path);
    }
}