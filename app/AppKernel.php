<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Debril\RssAtomBundle\DebrilRssAtomBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new JMS\JobQueueBundle\JMSJobQueueBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            new Os2Display\MediaBundle\Os2DisplayMediaBundle(),
            new Os2Display\CoreBundle\Os2DisplayCoreBundle(),
            new Os2Display\AdminBundle\Os2DisplayAdminBundle(),
            new Os2Display\DefaultTemplateBundle\Os2DisplayDefaultTemplateBundle(),

            new Itk\TemplateExtensionBundle\ItkTemplateExtensionBundle(),
            new Itk\ExchangeBundle\ItkExchangeBundle(),

            new Itk\HorizonTemplateBundle\ItkHorizonTemplateBundle(),
            new Itk\LokalcenterTemplateBundle\ItkLokalcenterTemplateBundle(),
            new Itk\AarhusTemplateBundle\ItkAarhusTemplateBundle(),
            new Itk\AarhusSecondTemplateBundle\ItkAarhusSecondTemplateBundle(),
            new Itk\AarhusDataBundle\ItkAarhusDataBundle(),
            new Itk\VimeoBundle\ItkVimeoBundle(),
            new Itk\CampaignBundle\ItkCampaignBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test', 'acceptance'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    public function __construct($environment, $debug)
    {
        // Force the timezone to be UTC.
        date_default_timezone_set('UTC');
        parent::__construct($environment, $debug);
    }
}
