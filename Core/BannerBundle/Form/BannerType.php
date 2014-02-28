<?php

namespace Core\BannerBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\MediaBundle\Form\MediaType;
use Core\MediaBundle\Form\ImageType;
use Core\MediaBundle\Form\VideoType;

class BannerType extends EditBannerType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        switch ($options['mediaType']) {
            case 'VideoType':
                $type = new VideoType();
                break;
            case 'ImageType':
            default:
                $type = new ImageType();
                break;
        }
        $builder
            ->add('media', $type, array('required' => false, 'only_file' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'mediaType' => 'ImageType',
        ));
    }
}
