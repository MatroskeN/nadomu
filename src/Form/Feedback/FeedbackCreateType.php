<?php

namespace App\Form\Feedback;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use App\Services\SMSServices;
use App\Services\FormServices;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\FilesRepository;
use Symfony\Component\Security\Core\Security as CoreSecurity;

/**
 * Форма для проверки номеров телефона
 *
 * @package App\Form
 */
class FeedbackCreateType extends AbstractType
{
    private UserRepository $userRepository;
    private CoreSecurity $security;

    public function __construct(
        UserRepository $userRepository,
        FilesRepository $filesRepository,
        CoreSecurity $security,
        FormServices $formServices
    ) {
        $this->userRepository = $userRepository;
        $this->filesRepository = $filesRepository;
        $this->security = $security;
        $this->formServices = $formServices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chat_id', IntegerType::class)
            ->add('title', TextType::class, [
                'constraints' => [
                    new Callback([$this, 'validateTitle']),
                ]
            ])
            ->add('message', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите текст сообщения'
                    ]),
                    new Assert\Length([
                        'min' => 5,
                        'minMessage' => 'Текст не может быть меньше 5 символов',
                    ])
                ]
            ])
            ->add('files', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'constraints' => [
                    new Assert\Count([
                        'max' => 50,
                        'maxMessage' => 'Максимальное количество фото - 50',
                    ]),
                    new Callback([$this->formServices, 'validateFileId'], ['groups' => 'InitUser']),
                ]
            ]);
    }

    public function validateTitle($value, ExecutionContextInterface $context)
    {
        $form = $context->getObject()->getParent();
        $chat_id = $form->get('chat_id')->getData();
        if (empty($chat_id)) {
            $value = trim($value);
            if (empty($value)) {
                return $context
                    ->buildViolation('Нужно заполнить заголовок')
                    ->addViolation();
            }
            if (mb_strlen($value) < 5) {
                return $context
                    ->buildViolation('Заголовок не может быть меньше 5 символов')
                    ->addViolation();
            }
            if (mb_strlen($value) > 255) {
                return $context
                    ->buildViolation('Заголовок не может быть больше 255 символов')
                    ->addViolation();
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
