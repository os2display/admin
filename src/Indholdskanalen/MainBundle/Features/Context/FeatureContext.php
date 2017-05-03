<?php

namespace Indholdskanalen\MainBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behatch\Context\BaseContext;
use Behatch\HttpCall\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Indholdskanalen\MainBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends BaseContext implements Context, KernelAwareContext {
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
   * @var \Behatch\HttpCall\Request
   */
  private $request;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct(ManagerRegistry $doctrine, Request $request) {
    $this->doctrine = $doctrine;
    $this->request = $request;
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
    }
    else {
      parent::fillField($field, $value);
    }
  }

  /**
   * @Given the following users exist:
   */
  public function theFollowingUsersExist(TableNode $table) {
    foreach ($table->getHash() as $row) {
      $username = $row['username'];
      $email = !empty($row['email']) ? $row['email'] : uniqid($username) . '@' . uniqid('example') . '.com';
      $password = !empty($row['password']) ? $row['password'] : uniqid();
      $roles = !empty($row['roles']) ? preg_split('/\s*,\s*/', $row['roles'], -1, PREG_SPLIT_NO_EMPTY) : [];

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
   * @When I sign in with username :username and password :password
   */
  public function iSignInWithUsernameAndPassword($username, $password) {
    $user = $this->getUser($username);

    if ($user) {
      $encoder_service = $this->container->get('security.encoder_factory');
      $encoder = $encoder_service->getEncoder($user);
      if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
        $this->authenticate($user);
      }
    }
    else {
      $this->deauthenticate();
    }
  }

  /**
   * @When I attach the file :filename
   */
  public function iAttachTheFile($filename) {
    $path = tempnam('/tmp', 'attachment');
    file_put_contents($path, $path);
    $this->attachments[] = new UploadedFile($path, $filename);
  }

  private $attachments = [];

  /**
   * @When I attach files:
   */
  public function iAttachFiles(TableNode $table) {
    foreach ($table->getHash() as $row) {
      $filename = $row['filename'];
      $content = isset($row['content']) ? $row['content'] : NULL;
      if (!$content && isset($row['mimetype'])) {
        switch ($row['mimetype']) {
          case 'image/png':
            $content = base64_decode('R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs=');
            break;
        }
      }
      $path = tempnam('/tmp', 'attachment' . $filename);
      file_put_contents($path, $content);
      $this->attachments[] = new UploadedFile($path, $filename);
    }
  }

  /**
   * Sends a HTTP request with a body
   *
   * @Given I send a :method request to :url with attachments and body:
   */
  public function iSendARequestToWithAttachmentsAndBody($method, $url, PyStringNode $body) {
    return $this->request->send(
      $method,
      $this->locatePath($url),
      [],
      $this->attachments,
      $body !== NULL ? $body->getRaw() : NULL
    );
  }

  /**
   * Locates url, based on provided path.
   * Override to provide custom routing mechanism.
   *
   * @param string $path
   *
   * @return string
   */
  public function locatePath($path) {
    $startUrl = rtrim($this->getMinkParameter('base_url'), '/') . '/';

    return 0 !== strpos($path, 'http') ? $startUrl . ltrim($path, '/') : $path;
  }

  /**
   * Get a user by username.
   *
   * @param $username
   * @return User|null
   */
  private function getUser($username) {
    $repository = $this->manager->getRepository(User::class);
    return $repository->findOneBy(['username' => $username]);
  }

  /**
   * Add authentication header to request.
   */
  private function authenticate(User $user) {
    $firewall = 'main';

    $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
    $this->container->get('security.token_storage')->setToken($token);
    $session = $this->container->get('session');
    $session->set('_security_user', serialize($token));
    $session->save();

    $this->getSession()->setCookie($session->getName(), $session->getId());
  }

  private function deauthenticate() {
    $this->container->get('security.token_storage')->setToken(NULL);
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
