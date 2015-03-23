<?php

namespace Akuma\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AkumaMediaExtension extends Extension  implements PrependExtensionInterface
{
    protected function createConfigEntries(array $config, ContainerBuilder $container, $parent = null)
    {
        $isScalar = true;
        foreach ($config as $key => $value) {
            if (!is_numeric($key)) {
                $isScalar = false;
                break;
            }
        }
        if ($isScalar) {
            if (!is_null($parent)) {
                $container->setParameter($parent, $config);
            }
        } else {
            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    //$keys = array_keys($value);
                    $this->createConfigEntries($value, $container, $parent ? $parent . '.' . $key : $key);
                } else {
                    $container->setParameter($parent ? $parent . '.' . $key : $key, $value);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->createConfigEntries($config, $container, 'akuma_media');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['TwigBundle'])) {
            $this->appendTwigBundle($container);
        }
    }

    protected function appendTwigBundle(ContainerBuilder $container)
    {
        $config = array(
            'form' => array(
                'resources' => array(
                    'AkumaMediaBundle:Form:fields.html.twig',
                )
            )
        );

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['TwigBundle'])) {
            foreach ($container->getExtensions() as $name => $extension) {
                switch ($name) {
                    case 'twig':
                        $container->prependExtensionConfig($name, $config);
                        break;
                }
            }
        }
    }
}
