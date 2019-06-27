<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AclBundle\AclBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Debril\RssAtomBundle\DebrilRssAtomBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new JMS\JobQueueBundle\JMSJobQueueBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),

            new Os2Display\MediaBundle\Os2DisplayMediaBundle(),
            new Os2Display\CoreBundle\Os2DisplayCoreBundle(),
            new Os2Display\AdminBundle\Os2DisplayAdminBundle(),
            new Os2Display\DefaultTemplateBundle\Os2DisplayDefaultTemplateBundle(),
            new Os2Display\CampaignBundle\Os2DisplayCampaignBundle(),
            new Os2Display\ScreenBundle\Os2DisplayScreenBundle(),

            new Reload\Os2DisplaySlideTools\Os2DisplaySlideToolsBundle(),
            new Kkos2\KkOs2DisplayIntegrationBundle\Kkos2DisplayIntegrationBundle(),
            new Os2Display\VimeoBundle\Os2DisplayVimeoBundle(),
            new Os2Display\YoutubeBundle\Os2DisplayYoutubeBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test', 'acceptance'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    protected function getEnvBase($environment) {
      if ($environment === 'dev') {
        return '/var/symfony';
      }
      else {
        return dirname(__DIR__).'/var';
      }
    }

    public function getCacheDir()
    {
        return $this->getEnvBase($this->getEnvironment()) . '/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return $this->getEnvBase($this->getEnvironment()) . '/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', true);

            $container->addObjectResource($this);
        });
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
