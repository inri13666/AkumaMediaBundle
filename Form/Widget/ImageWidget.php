<?php
/**
 * User  : Nikita.Makarov
 * Date  : 2/17/15
 * Time  : 9:38 AM
 * E-Mail: nikita.makarov@effective-soft.com
 */

namespace Akuma\Bundle\MediaBundle\Form\Widget;

use Akuma\Bundle\MediaBundle\Form\Transformer\ImageWidgetViewTransformer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

class ImageWidget extends AbstractType implements ContainerAwareInterface
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'image_widget';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add("id", "hidden", array('label' => false, 'required' => false));
        $builder->add("id", "hidden", array('label' => false, 'required' => false));
        //$builder->addViewTransformer(new ImageWidgetViewTransformer($this->container->get('doctrine')->getManager(), $this->container->getParameter('akuma_media.image_class')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Darom\Bundle\CoreBundle\Entity\Image',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention' => 'file',
            'label' => false,
        ));
    }

    /**
     * @var Container
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}