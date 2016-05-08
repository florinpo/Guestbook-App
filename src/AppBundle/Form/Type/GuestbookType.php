<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class GuestbookType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class'      => 'AppBundle\Entity\Guestbook',
      'csrf_protection' => false,
      'csrf_field_name' => '_token',
      'csrf_token_id'   => 'guestbook_item',
    ));
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name', TextType::class)
      ->add('email', EmailType::class)
      ->add('website', UrlType::class)
      ->add('comment', TextareaType::class)
      ->add('file', FileType::class, array('label' => 'Thumbnail profile)'))
      ->add('captcha_code', EWZRecaptchaType::class)
      ->add('save', SubmitType::class)
      ->add('reset', ResetType::class);
  }
}