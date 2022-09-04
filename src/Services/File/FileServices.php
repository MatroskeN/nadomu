<?php

namespace App\Services\File;

use App\Entity\Files;
use App\Entity\SupportMessage;
use App\Entity\User;
use App\Repository\FilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hshn\Base64EncodedFile\HttpFoundation\File\UploadedBase64EncodedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

class FileServices
{
    //тип изображения фото профиля
    const TYPE_AVATAR = 'photo_profile';
    //изображения в профиле
    const TYPE_ACCOUNT_PHOTO = 'photo_account';
    //изображение публичных документов
    const TYPE_PUBLIC_DOCS = 'public_docs';
    //документы для модерации
    const TYPE_MODERATE_DOCS = 'private_docs';
    //файл из обратной связи
    const TYPE_FEEDBACK_DOCS = 'feedback_docs';
    //файл сообщенией
    const TYPE_REQUEST_DOCS = 'request_docs';

    //максимальные размеры картинок в пикселях
    const MAX_SIZES_PIXEL = [
        self::TYPE_AVATAR => [300, 2000],
        self::TYPE_PUBLIC_DOCS => [300, 5000],
        self::TYPE_MODERATE_DOCS => [300, 5000],
        self::TYPE_ACCOUNT_PHOTO => [300, 5000],
        self::TYPE_FEEDBACK_DOCS => [300, 5000],
        self::TYPE_REQUEST_DOCS => [300, 5000]
    ];

    //максимальные размеры файла в байтах
    // текущие мин 1кб, макс 15мб
    const MAX_SIZES = [
        self::TYPE_PUBLIC_DOCS => [15360, 15728640],
        self::TYPE_MODERATE_DOCS => [15360, 15728640],
        self::TYPE_ACCOUNT_PHOTO => [15360, 15728640],
        self::TYPE_FEEDBACK_DOCS => [15360, 15728640],
        self::TYPE_REQUEST_DOCS => [15360, 15728640]
    ];

    private array $available_types = [self::TYPE_AVATAR, self::TYPE_PUBLIC_DOCS, self::TYPE_MODERATE_DOCS, self::TYPE_ACCOUNT_PHOTO, self::TYPE_FEEDBACK_DOCS, self::TYPE_REQUEST_DOCS];
    // Только загрузка фото
    private array $only_image_types = [self::TYPE_AVATAR, self::TYPE_PUBLIC_DOCS, self::TYPE_MODERATE_DOCS, self::TYPE_ACCOUNT_PHOTO];

    private EntityManagerInterface $em;
    private FilesRepository $filesRepository;

    public function __construct(EntityManagerInterface $em, FilesRepository $filesRepository)
    {
        $this->em = $em;
        $this->filesRepository = $filesRepository;
    }

    /**
     * Проверка валидности типа файла
     *
     * @param string $type
     * @return bool
     */
    public function isTypeValid(string $type)
    {
        return in_array($type, $this->available_types);
    }

    /**
     * Проверка можно ли загружать файлы
     *
     * @param string $type
     * @return bool
     */
    public function isTypeFile(string $type)
    {
        return in_array($type, $this->only_image_types);
    }

    /**
     * Возвоащаем массив без левых идентификаторов
     *
     * @param UserInterface $user
     * @param array $ids
     * @return array
     */
    public function filterBrokenIds(UserInterface $user, array $ids): array
    {
        //если пустой массив, то запрос не шлем
        if (empty($ids))
            return [];

        $result = $this->filesRepository->findByUserIdAndFileIds($user->getId(), $ids);

        return array_column($result, 'id');
    }

    /**
     * Информация по файлу. В случае если файл битый, то false
     *
     * @param UploadedBase64EncodedFile $encodedFile
     * @return array|false
     */
    public function getImageSize(UploadedBase64EncodedFile $encodedFile)
    {
        return getimagesize($encodedFile->getPathInfo() . DIRECTORY_SEPARATOR . $encodedFile->getFilename());
    }

    /**
     * Информация по файлу. В случае если файл битый, то false
     *
     * @param UploadedBase64EncodedFile $encodedFile
     * @return array|false
     */
    public function getFileSize(UploadedBase64EncodedFile $encodedFile)
    {
        return filesize($encodedFile->getPathInfo() . DIRECTORY_SEPARATOR . $encodedFile->getFilename());
    }

    /**
     * Создаем запись о файле в базе
     *
     * @param UserInterface $user
     * @param string $filepath
     * @param string $filetype
     * @return Files
     */
    public function createFile(UserInterface $user, string $filepath, string $filetype, int $is_image): Files
    {
        $file = new Files();
        $file->setUser($user)
            ->setIsDeleted(false)
            ->setCreateTime(time())
            ->setFiletype($filetype)
            ->setFilePath($filepath)
            ->setIsImage($is_image);

        $this->em->persist($file);
        $this->em->flush();

        return $file;
    }

    /**
     * Размер файла в кило/мега/гига/тера/пета байтах
     * @param int $filesize — размер файла в байтах
     *
     * @return string — возвращаем размер файла в Б, КБ, МБ, ГБ или ТБ
     */
    public function filesizeFormat($filesize)
    {
        $formats = array('Б', 'КБ', 'МБ', 'ГБ', 'ТБ'
        ); // варианты размера файла
        $format = 0; // формат размера по-умолчанию

        // прогоняем цикл
        while ($filesize > 1024 && count($formats) != ++$format) {
            $filesize = round($filesize / 1024, 2);
        }

        // если число большое, мы выходим из цикла с
        // форматом превышающим максимальное значение
        // поэтому нужно добавить последний возможный
        // размер файла в массив еще раз
        $formats[] = 'ТБ';

        return $filesize . $formats[$format];
    }

    /**
     * Удаляем файл
     *
     * @param Files $file
     * @return bool
     */
    public function deleteFile(Files $file)
    {
        $file_path = '.' . DIRECTORY_SEPARATOR . $file->getFilePath();

        if (file_exists($file_path)) {
            $is_ok = unlink($file_path);

            if ($is_ok) {
                $file->setIsDeleted(true);
                $file->setDeleteTime(time());

                $this->em->persist($file);
                $this->em->flush();

                return true;
            } else
                return false;
        } else
            return false;
    }

    /**
     * Выставляем принадлежность файла к какой либо сущности в зависимости от объекта
     *
     * @param Files $file
     * @param $entity
     */
    private function setEntityType(Files &$file, $entity)
    {
        if (is_a($entity, SupportMessage::class))
            $file->setSupportMessage($entity);
    }

}
