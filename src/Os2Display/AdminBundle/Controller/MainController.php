<?php
/**
 * @file
 * Contains the main entry point to the admin.
 */

namespace Os2Display\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/")
 */
class MainController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        // Add paths to css files for activated templates.
        $templates = array();
        $slideTemplates = $this->container->get('indholdskanalen.template_service')
            ->getEnabledSlideTemplates();
        foreach ($slideTemplates as $template) {
            $templates[] = $template->getPathCss();
        }
        $screenTemplates = $this->container->get('indholdskanalen.template_service')
            ->getEnabledScreenTemplates();
        foreach ($screenTemplates as $template) {
            $templates[] = $template->getPathCss();
        }

        // Get current user.
        $user = $this->getUser();
        $user->buildRoleGroups();
        $user = $this->get('os2display.api_data')->setApiData($user);
        $user = $this->get('serializer')
            ->serialize($user, 'json', SerializationContext::create()
                ->setGroups(array('api'))
                ->enableMaxDepthChecks());

        // Get angular modules and apps from other bundles.
        $externalAssets = $this->container->hasParameter('external_assets') ?
            $this->container->getParameter('external_assets') : [];
        $externalModules = $this->container->hasParameter('external_modules') ?
            $this->container->getParameter('external_modules') : [];
        $externalApps = $this->container->hasParameter('external_apps') ?
            $this->container->getParameter('external_apps') : [];
        $externalBootstrap = $this->container->hasParameter('external_bootstrap') ?
            $this->container->getParameter('external_bootstrap') : [];

        $mergedAssets = array_merge($this->container->getParameter('assets'), $externalAssets);
        $mergedApps = array_merge($this->container->getParameter('apps'), $externalApps);
        $mergedBootstrap = array_merge($this->container->getParameter('bootstrap'), $externalBootstrap);
        $mergedModules = array_merge($this->container->getParameter('modules'), $externalModules);

        return $this->render(
            'Os2DisplayAdminBundle:Main:index.html.twig',
            [
                'assets' => $mergedAssets,
                'apps' => $mergedApps,
                'bootstrap' => $mergedBootstrap,
                'modules' => $mergedModules,
                'templates' => $templates,
                'user' => $user
            ]
        );
    }
}
