<?php

namespace App\Entity;

use App\Repository\ServiceWorkTimeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceWorkTimeRepository::class)
 */
class ServiceWorkTime
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $day;

    /**
     * @ORM\Column(type="integer")
     */
    private $hour;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceInfo::class, inversedBy="workTime")
     */
    private $serviceInfo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hidden;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function setHour(int $hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getServiceInfo(): ?ServiceInfo
    {
        return $this->serviceInfo;
    }

    public function setServiceInfo(?ServiceInfo $serviceInfo): self
    {
        $this->serviceInfo = $serviceInfo;

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(?bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Отображение рабочего времени удобного для пользователей
     * 
     * @param mixed $worktime
     * 
     * @return [type]
     */
    public static function getPublicWorkTime($worktime)
    {
        $data = [];
        $result = [];

        //Сортиреум дни и часы
        usort($worktime, function ($a, $b) {
            return ($a['day'] - $b['day']);
        });

        //Сохраняем день - часы
        foreach ($worktime as $key => $value) {
            $data[$value['day']][$key] = $value['hour'];
        }

        // Сохраняем день = ключ
        foreach ($data as $day => $value) {
            switch ($day) {
                case 1:
                    $day = 'понедельник';
                    break;
                case 2:
                    $day = 'вторник';
                    break;
                case 3:
                    $day = 'среда';
                    break;
                case 4:
                    $day = 'четверг';
                    break;
                case 5:
                    $day = 'пятница';
                    break;
                case 6:
                    $day = 'суббота';
                    break;
                case 7:
                    $day = 'воскресенье';
                    break;
            }

            $hour = 0;
            $start = 0;
            $last = 0;
            while ($hour <= 24) {
                $last_hour = $hour - 1;
                $next_hour = $hour + 1;
                // Есть ли в массиве значение, и есть ли следующий час
                if (in_array($hour, $value) && !in_array($last_hour, $value) && in_array($next_hour, $value)) {
                    $start = $hour;
                }
                // Есть ли в массиве значение, и есть ли предыдущий час
                if (in_array($hour, $value) && in_array($last_hour, $value) && !in_array($next_hour, $value)) {
                    $last = $hour;
                }
                // Сохраняем диапазон времени
                if (!in_array($hour, $value) && in_array($last_hour, $value)) {
                    if ($start !== $last) {
                        $result = self::addTimeResult($result, $day, $start, $last + 1);
                    }
                    $start = 0;
                    $last = 0;
                }
                // Сохраняем если нет следующего и предыдущего часа
                if (in_array($hour, $value) && !in_array($last_hour, $value) && !in_array($next_hour, $value)) {
                    $result = self::addTimeResult($result, $day, $hour, $hour + 1);
                }
                $hour++;
            }
        }
        return $result;
    }

    /**
     * Конвертирует int время в график работы
     *
     * @param int $time
     * 
     * @return [type]
     */
    public static function timeToString(int $time)
    {
        if ($time >= 24) {
            $time = 0;
        }

        if ($time < 10) {
            $time = '0' . $time . '-00';
        } else {
            $time = $time . '-00';
        }

        return $time;
    }

    /**
     * @param mixed $result
     * @param mixed $day
     * @param mixed $start
     * @param mixed $last
     * 
     * @return [type]
     */
    protected static function addTimeResult($result, $day, $start, $last)
    {
        $string_time = ServiceWorkTime::timeToString($start);
        $next_time = ServiceWorkTime::timeToString($last);
        // Если указаны сутки 00:00
        $result[$day][] = ($string_time == $next_time) ? '24 часа' : 'c ' . $string_time . ' до ' . $next_time;

        return $result;
    }
}
