<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {
  public function registerBundles() {
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
      new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
      new Debril\RssAtomBundle\DebrilRssAtomBundle(),
      new JMS\SerializerBundle\JMSSerializerBundle(),
      new Indholdskanalen\MainBundle\IndholdskanalenMainBundle(),
      new JMS\JobQueueBundle\JMSJobQueueBundle(),
      new JMS\DiExtraBundle\JMSDiExtraBundle($this),
      new JMS\AopBundle\JMSAopBundle(),
      new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
      new Itk\KobaIntegrationBundle\ItkKobaIntegrationBundle(),
      new Os2Display\DefaultTemplateBundle\Os2DisplayDefaultTemplateBundle(),
      new Os2Display\AdminBundle\Os2DisplayAdminBundle(),
            new Os2Display\TemplateContainerBundle\Os2DisplayTemplateContainerBundle(),
            new Os2Display\CoreBundle\Os2DisplayCoreBundle(),
            new Itk\AarhusTemplateBundle\ItkAarhusTemplateBundle(),
    );

    if (in_array($this->getEnvironment(), array('dev', 'test', 'acceptance'))) {
      $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
      $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
      $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
    }

    return $bundles;
  }

  public function registerContainerConfiguration(LoaderInterface $loader) {
    $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
  }
}
