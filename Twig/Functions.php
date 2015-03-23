<?php
/**
 * User  : Nikita.Makarov
 * Date  : 12/3/14
 * Time  : 7:55 AM
 * E-Mail: nikita.makarov@effective-soft.com
 */

namespace Akuma\Bundle\MediaBundle\Twig;


use Akuma\Bundle\MediaBundle\Doctrine\MediaFile;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Functions extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'image_url' => new \Twig_Function_Method($this, 'getImageUrl', array('is_safe' => array('html'))),
        );
    }

    public function getImageUrl(MediaFile $file)
    {
        $imageClass = $this->container->getParameter('akuma_media.image_class');
        if ($file instanceof $imageClass) {
            return $file->getWebPath();
        }
        return '#';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'akuma_media.twig.functions';
    }

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