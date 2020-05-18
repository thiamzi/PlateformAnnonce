<?php

namespace App\Form;

use App\Entity\Anonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\File;
use App\Form\CompetanceType;

class AnonceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre' , TextType::class)
            ->add('contenu' , TextareaType::class)
            //->add('publie' , CheckboxType::class , array('required' => false))
            ->add('image', FileType::class, [

              // unmapped means that this field is not associated to any entity property
              'mapped' => false,

              'required' => false,

              'constraints' => [
                  new File([
                      'maxSize' => '1024k',
                      'mimeTypes' => [
                          'image/png',
                          'image/jpg',
                          'image/jpeg',
                      ],
                      'mimeTypesMessage' => 'Please upload a valid image',
                  ])
              ],
          ])
          ->add('competance', CollectionType::class, array(
            'entry_type'   => CompetanceType::class,
            'entry_options' => ['label' => false],
            'allow_add'    => true,
            'allow_delete' => true
          ))

          ->add('categories', EntityType::class, [
            'class' => Categorie::class,
            'choice_label' => 'nom',
            'multiple' => false,
            'expanded' => true,
          ])
        ;

            // On ajoute une fonction qui va écouter un évènement
    $builder->addEventListener(
        FormEvents::PRE_SET_DATA,    // 1er argument : L'évènement qui nous intéresse : ici, PRE_SET_DATA
        function(FormEvent $event) { // 2e argument : La fonction à exécuter lorsque l'évènement est déclenché
          // On récupère notre objet Advert sous-jacent
          $anonce = $event->getData();
  
          // Cette condition est importante, on en reparle plus loin
          if (null === $anonce) {
            return; // On sort de la fonction sans rien faire lorsque $advert vaut null
          }
  
          // Si l'annonce n'est pas publiée, ou si elle n'existe pas encore en base (id est null)
          if (!$anonce->getPublie() || null === $anonce->getId()) {
            // Alors on ajoute le champ published
            $event->getForm()->add('publie', CheckboxType::class, array('required' => true));
          } else {
            // Sinon, on le supprime
            $event->getForm()->remove('publie');
          }
        }
    );
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Anonce::class,//On indique l'entite(objet) correspondant
        ]);
    }
}
