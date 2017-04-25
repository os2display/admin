<?php

namespace Indholdskanalen\MainBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, KernelAwareContext {
  private $kernel;
  private $container;

  public function setKernel(KernelInterface $kernel) {
    $this->kernel = $kernel;
    $this->container = $this->kernel->getContainer();
  }

  /**
   * @var ManagerRegistry
   */
  private $doctrine;
  /**
   * @var \Doctrine\Common\Persistence\ObjectManager
   */
  private $manager;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct(ManagerRegistry $doctrine) {
    $this->doctrine = $doctrine;
    $this->manager = $doctrine->getManager();
    $this->schemaTool = new SchemaTool($this->manager);
    $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
  }

  /**
   * Checks, that a given element contains the specified text.
   * Example: Then I should see an "h1" element containing "My page"
   * Example: Then I see "My page" in "h1"
   *
   * @Then /^(?:|I )(?:should )?see (?:an? )?"(?P<selector>[^"]+)" (?:element)? containing "(?P<text>[^"]+)"$/
   * @Then /^(?:|I )see "(?P<text>[^"]*)" in (?:an?)? "(?P<selector>[^"]+)"(?: element)?$/
   */
  public function iShouldSeeAnElementContaining($selector, $text) {
    $this->assertSession()->elementTextContains('css', $selector, $text);
  }

  /**
	 * Wait for a number of seconds.
	 *
   * @When /^(?:|I )wait (?:for )?(?P<value>[0-9]+) seconds?$/
   */
  public function iWaitForSeconds($value) {
    $this->getSession()->wait(1000 * $value);
  }

  /**
	 * Click on an element.
	 *
   * @When /^(?:|I )click (?:a |the )?"(?P<selector>[^"]+)"(?: element)?$/
   */
  public function iClickTheElement($selector) {
    $session = $this->getSession();
    $page = $session->getPage();
    $element = $page->find('css', $selector);
    if ($element === NULL) {
      try {
        $element = $page->find('named', ['content', $selector]);
      }
      catch (\Exception $e) {
      }
    }
    if (NULL === $element) {
      throw new \InvalidArgumentException(sprintf('Could not find element matching selector "%s"', $selector));
    }

    $element->click();
  }

  /**
   * {@inheritdoc}
   */
  public function fillField($field, $value) {
    // See if we can find the field by css selector.
    $element = $this->getSession()->getPage()->find('css', $field);
    if ($element !== NULL) {
      $element->setValue($value);
    } else {
      parent::fillField($field, $value);
    }
  }

  /**
   * @Given the following users exist:
   */
  public function theFollowingUsersExist(TableNode $table) {
    foreach ($table->getHash() as $row) {
      $username = $row['username'];
      $email = $username . '@example.com';
      $password = isset($row['password']) ? $row['password'] : uniqid();
      $roles = isset($row['roles']) ? preg_split('/\s*,\s*/', $row['roles'], -1, PREG_SPLIT_NO_EMPTY) : [];

      $this->createUser($username, $email, $password, $roles);
    }
  }

  private function createUser($username, $email, $password, array $roles) {
    $userManager = $this->container->get('fos_user.user_manager');

    $user = $userManager->findUserBy(['username' => $username]);
    if (!$user) {
      $user = $userManager->createUser();
    }
    $user
      ->setEnabled(TRUE)
      ->setUsername($username)
      ->setPlainPassword($password)
      ->setEmail($email)
      ->setRoles($roles);

    $userManager->updateUser($user);
  }

  /**
   * @BeforeScenario @createSchema
   */
  public function createDatabase() {
    $this->schemaTool->createSchema($this->classes);
  }

  /**
   * @AfterScenario @dropSchema
   */
  public function dropDatabase() {
    $this->schemaTool->dropSchema($this->classes);
  }

}
