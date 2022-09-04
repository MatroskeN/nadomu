<?php

namespace App\Form;

use App\Entity\Files;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\User\UserServices;
use App\Services\FormServices;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\CallbackTransformer;
/**
 * Форма для редактирования профиля
 *
 * @package App\Form
 */
class UpdateUserDataType extends AbstractType
{
    private UserRepository $userRepository;
    private CoreSecurity $security;

    public function __construct(UserRepository $userRepository, CoreSecurity $security, FormServices $formServices)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->formServices = $formServices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patronymic_name', TextType::class, [
                'constraints' => [
                    new Callback([$this, 'validateFIO'], ['groups' => 'InitUser'])
                ],
            ])
            ->add('first_name', TextType::class, [
                'constraints' => [
                    new Callback([$this, 'validateFIO'], ['groups' => 'InitUser'])
                ],
            ])
            ->add('last_name', TextType::class, [
                'constraints' => [
                    new Callback([$this, 'validateFIO'], ['groups' => 'InitUser'])
                ],
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new Callback([$this, 'validatePhone'], ['groups' => 'InitUser'])
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Callback([$this, 'validateEmail'], ['groups' => 'InitUser'])
                ],
            ])
            ->add('birthday', DateType::class, array(
                'widget' => 'single_text',
                "required" => true,
                'html5' => false,
                'constraints' => [
                    new Callback([$this, 'validateBirthday'], ['groups' => 'InitUser'])
                ],
            ))
            ->add('notification')
            ->add('avatar', EntityType::class, [
                'class' => Files::class,
                'constraints' => [
                    new Callback([$this->formServices, 'validateFileId'], ['groups' => 'InitUser'])
                ],
            ]);
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     *
     * @return [type]
     */
    public function validatePhone($value, ExecutionContextInterface $context)
    {
        $user = $this->security->getUser();
        $form = $context->getObject()->getParent();
        $phone = $form->get('phone')->getData();

        if (empty($phone))
            return $context
                ->buildViolation('Введите корректный номер телефона')
                ->addViolation();

        if ($user->getPhone() != $phone)
            return $context
                ->buildViolation('Для смены номера воспользуйтесь восстановлением доступа на главной странице')
                ->addViolation();
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     *
     * @return [type]
     */
    public function validateEmail($value, ExecutionContextInterface $context)
    {
        $user = $this->security->getUser();
        $form = $context->getObject()->getParent();
        $email = $form->get('email')->getData();

        if (empty($email))
            return $context
                ->buildViolation('Введите e-mail')
                ->addViolation();

        if ($this->userRepository->checkUniqueEmail($user->getId(), $email))
            return $context
                ->buildViolation('Данный e-mail уже используется другим пользователем')
                ->addViolation();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $context
                ->buildViolation('Данный e-mail не может быть указан')
                ->addViolation();
        }
    }



    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     *
     * @return [type]
     */
    public function validateBirthday($value, ExecutionContextInterface $context)
    {
        $user = $this->security->getUser();
        $form = $context->getObject()->getParent();
        $birthday = $form->get('birthday')->getData();

        if (!$birthday) {
            return $context
                ->buildViolation('Дата рождения не указана!')
                ->addViolation();
        }

        // Validate year
        $year = (int)$birthday->format('Y');

        if (date("Y") - 100 > $year) {
            return $context
                ->buildViolation('Год указан неверно!')
                ->addViolation();
        }

        if (date_diff($birthday, date_create('now'))->y < 18) {
            return $context
                ->buildViolation('Вам меньше 18 лет!')
                ->addViolation();
        }
    }

    public function validateFIO($value, ExecutionContextInterface $context)
    {
        if (preg_match("/[^a-zа-яё ]/iu", $value)) {
            return $context
                ->buildViolation('В написании допустимы только буквы латинского и русского алфавита!')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'validation_groups' => ['InitUser'],
        ]);
    }
}
