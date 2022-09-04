<?php

namespace App\Services\File;

use App\Entity\Files;
use App\Entity\SupportMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderServices
{
    //форматы фото
    const EXTENSIONS_IMAGES = ['png', 'jpg', 'jpeg', 'webp'];
    //форматы файлов
    const EXTENSIONS_FILES = ['pdf', 'doc', 'docx', 'xlsx', 'xls', 'pdf', 'txt', 'zip', 'csv'];
    private SluggerInterface $slugger;
    private ParameterBagInterface $params;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $params, FileServices $fileServices)
    {
        $this->slugger = $slugger;
        $this->params  = $params;
        $this->fileServices  = $fileServices;
    }

    /**
     * Загрузка файла
     *
     * @param UploadedFile $file
     * @param string $filetype
     * @return false|string
     * @throws \Exception
     */
    public function upload(UploadedFile $file, string $filetype)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

        if (!$this->fileServices->isTypeValid($filetype))
            throw new FileException('Неизвестная группа файла!');

        try {
            $file->move($this->getTargetDirectory($filetype), $fileName);
        } catch (FileException $e) {
            if ($this->params->get('kernel.environment') !== 'prod')
                throw new \Exception('Ошибка при загрузке файла: ' . $e->getMessage());

            return null;
        }

        return $this->getTargetDirectory($filetype) . $fileName;
    }

    /**
     * Проверяем на валидность разрешение
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isValid(UploadedFile $file)
    {
        return in_array(strtolower($file->getClientOriginalExtension()), array_merge(self::EXTENSIONS_IMAGES, self::EXTENSIONS_FILES));
    }

    /**
     * Проверем файл фото или файл
     * 
     * @param mixed $filetype
     * 
     * @return [type]
     */
    public function isImage(UploadedFile $file): bool
    {
        return in_array(strtolower($file->getClientOriginalExtension()), self::EXTENSIONS_IMAGES);
    }

    /**
     * Возвращаем путь
     * @param string $filetype
     * @return string
     */
    private function getTargetDirectory(string $filetype)
    {
        $path = trim($this->params->get('file.directory_upload'), " \t\n\r\0\x0B/\\.");

        return $path . '/' . $filetype . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
    }
}
