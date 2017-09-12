<?php

namespace Os2Display\CoreBundle\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Os2Display\CoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Defines application features from the specific context.
 */
class ApiContext extends RestContext implements KernelAwareContext {
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
  public function __construct(ManagerRegistry $doctrine, Request $request) {
    parent::__construct($request);
    $this->doctrine = $doctrine;
    $this->manager = $doctrine->getManager();
    $this->schemaTool = new SchemaTool($this->manager);
    $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
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
   * @When I sign in with username :username and password :password
   */
  public function iSignInWithUsernameAndPassword($username, $password)
  {
    $user = $this->getUser($username);

    if ($user) {
      $encoder_service = $this->container->get('security.encoder_factory');
      $encoder = $encoder_service->getEncoder($user);
      if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
        $this->authenticate($user);
      }
    } else {
      $this->deauthenticate();
    }
  }

  /**
   * @When I attach the file :filename
   */
  public function iAttachTheFile($filename)
  {
    $path = tempnam('/tmp', 'attachment');
    file_put_contents($path, $path);
    $this->attachments[] = new UploadedFile($path, $filename);
  }

  /**
   * @When I send a :method request to :url with attachments:
   * @param $method
   * @param $url
   * @param \Behat\Gherkin\Node\TableNode $table
   */
  public function iSendARequestToWithAttachments($method, $url, TableNode $table)
  {
    $files = [];
    foreach ($table->getHash() as $row) {
      $filename = $row['filename'];
      $content = isset($row['content']) ? $row['content'] : $filename;
      $path = tempnam('/tmp', 'attachment' . $filename);
      file_put_contents($path, $content);
      $files[] = new UploadedFile($path, $filename);
    }
    $body = null;

    return $this->iSendARequestTo($method, $url, $body, $files);
  }

  /**
   * Get a user by username.
   *
   * @param $username
   * @return User|null
   */
  private function getUser($username)
  {
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
