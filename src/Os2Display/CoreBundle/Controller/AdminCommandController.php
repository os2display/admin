<?php
/**
 * @file
 * Contains the commands that can be run from the backend.
 */

namespace Os2Display\CoreBundle\Controller;

use Os2Display\CoreBundle\Command\PushContentCommand;
use Os2Display\CoreBundle\Command\SearchCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class AdminCommandController
 *
 * @Route("/api/command")
 *
 * @package Os2Display\CoreBundle\Controller
 */
class AdminCommandController extends Controller {
  /**
   * Load templates.
   *
   * @Route("/update_templates")
   * @Method("GET")
   */
  public function updateTemplates() {
    $this->container->get('os2display.template_service')->loadTemplates();

    // @TODO: Handle errors.

    return new Response();
  }

  /**
   * Reindex the search.
   *
   * @Route("/reindex")
   * @Method("GET")
   */
  public function reindex() {
    // Run the reindex command.
    $command = new SearchCommand();
    $command->setContainer($this->container);
    $input = new ArrayInput(array());
    $output = new NullOutput();
    $command->run($input, $output);

    // @TODO: Handle errors.

    return new Response();
  }


  /**
   * Force push.
   *
   * @Route("/forcepush")
   * @Method("GET")
   */
  public function forcePush() {
    // Run the reindex command.
    $command = new PushContentCommand();
    $command->setContainer($this->container);
    $input = new ArrayInput(array('--force' => true));
    $output = new NullOutput();
    $command->run($input, $output);

    // @TODO: Handle errors.

    return new Response();
  }
}
