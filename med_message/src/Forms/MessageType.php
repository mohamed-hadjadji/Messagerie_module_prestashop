<?php

namespace MedMessage\Forms;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        

            ->add('title', TextType::class, array(
                "attr" => array(
                    "placeholder" => "Titre",
                ),
                "label" => "Objet",
                
            ))

            ->add('message', TextareaType::class, array(
                "attr" => array(
                    "placeholder" => "Message",
                    'class' => 'tinymce'
                ),
                
            ))

            ->add('files', FileType::class, [
                'label' => 'Pièce Jointe (PDF file)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '8192k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Svp, télécharger un document PDF valide',
                    ])
                ],
            ])

            ->add('Envoyer', SubmitType::class);
    }
}
