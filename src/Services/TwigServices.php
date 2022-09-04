<?php

namespace App\Services;


use App\Entity\ServiceEducation;
use App\Entity\ServiceImages;
use App\Entity\ServiceInfo;
use App\Entity\ServicePrice;
use App\Entity\ServiceWorkTime;
use App\Entity\User;
use App\Form\Steps\StepOneType;
use App\Repository\CitiesRepository;
use App\Repository\FilesRepository;
use App\Repository\MetroStationsRepository;
use App\Repository\PromocodesRepository;
use App\Repository\RegionsRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use App\Services\File\FileServices;
use App\Services\User\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use Symfony\Component\Security\Core\User\UserInterface;

class TwigServices
{
    private CoreSecurity $security;
    private UserServices $userServices;
    private ParameterBagInterface $params;

    public function __construct(CoreSecurity $security, UserServices $userServices, ParameterBagInterface $params)
    {
        $this->security = $security;
        $this->userServices = $userServices;
        $this->params = $params;
    }

    /**
     * Возвращаем, авторизован пользователь или нет
     *
     * @return bool
     */
    public function isAuth(): bool
    {
        $user = $this->security->getUser();

        return !empty($user);
    }

    /**
     * Преобразовываем к общему виду информацию о юзере
     *
     * @param User $user
     * @return array
     */
    public function parseUserEntity(User $user): array
    {
        return $this->userServices->getInformation($user);
    }

    /**
     * Возвращаем информацию о пользователе
     *
     * @return array|null
     */
    public function getUser(): ?array
    {
        $user = $this->security->getUser();

        if (!$user)
            return null;

        return $this->userServices->getInformation($user);
    }

    /**
     * Форматируем номер телефона к единому виду
     *
     * @param string $phone
     * @return string
     */
    public function phone(string $phone): string
    {
        $phone = trim($phone);

        $res = preg_replace(
            array(
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',
            ),
            array(
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4',
                '+7 ($2) $3-$4',
            ),
            $phone
        );

        return $res;
    }

    /**
     * Склонение существительных после числительных.
     *
     * @param string $value Значение
     * @param array $words Массив вариантов, например: array('товар', 'товара', 'товаров')
     * @param bool $show Включает значение $value в результирующею строку
     *
     * @return [type]
     */
    public static function plural($value, $words, $show = true)
    {
        $num = $value % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        $out = ($show) ?  $value . ' ' : '';
        switch ($num) {
            case 1:
                $out .= $words[0];
                break;
            case 2:
            case 3:
            case 4:
                $out .= $words[1];
                break;
            default:
                $out .= $words[2];
                break;
        }

        return $out;
    }

    /**
     * Возвращаем версию деплоя
     *
     * @return array|bool|float|int|string|null
     */
    public function getVersionDeploy()
    {
        return $this->params->get('base.version_deploy');
    }

  /**
   * Возвращает информацию по массиву
   *
   * @param $array
   * @return string|true
   */
  public function print_r($array)
    {
      return print_r($array, true);
    }

    /**
       * Возвращает массив с числами
       *
       * @param int $digit
       * @return array
       */
    public function fillArrayByNum(int $digit): array
    {
        $result = [];

        for ($i = 0; $i < $digit; $i++)
            $result[] = $i;

        return $result;
    }
}
