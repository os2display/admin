<?php

namespace Os2Display\CoreBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behatch\Context\BaseContext;
use Behatch\HttpCall\HttpCallResultPool;
use Behatch\HttpCall\Request;
use Behatch\Json\Json;
use Behatch\Json\JsonInspector;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Os2Display\CoreBundle\Entity\User;
use Os2Display\CoreBundle\Entity\UserGroup;
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
   * @var \Behatch\Json\JsonInspector
   */
  private $inspector;

  /**
   * @var \Behatch\HttpCall\HttpCallResultPool
   */
  private $httpCallResultPool;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct(ManagerRegistry $doctrine, Request $request, HttpCallResultPool $httpCallResultPool, $evaluationMode = 'javascript') {
    $this->doctrine = $doctrine;
    $this->request = $request;
    $this->manager = $doctrine->getManager();
    $this->schemaTool = new SchemaTool($this->manager);
    $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();

    $this->inspector = new JsonInspector($evaluationMode);
    $this->httpCallResultPool = $httpCallResultPool;
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
      $groups = !empty($row['groups']) ? preg_split('/\s*,\s*/', $row['groups'], -1, PREG_SPLIT_NO_EMPTY) : NULL;

      $this->createUser($username, $email, $password, $roles, $groups);
    }
    $this->doctrine->getManager()->clear();
  }

  private function createUser($username, $email, $password, array $roles, array $groups = NULL) {
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

    // Only set groups on new users.
    if ($groups && $user->getId() === NULL) {
      $groupManager = $this->container->get('os2display.group_manager');

      foreach ($groups as $spec) {
        list($groupId, $role) = preg_split('/\s*:\s*/', $spec, 2, PREG_SPLIT_NO_EMPTY);
        $group = $groupManager->findGroupBy(['id' => $groupId]);
        $userGroup = new UserGroup();
        $userGroup->setUser($user);
        $userGroup->setGroup($group);
        $userGroup->setRole($role);
        $this->doctrine->getManager()->persist($userGroup);
      }
    }

    $userManager->updateUser($user);
  }

  /**
   * @Given the following groups exist:
   */
  public function theFollowingGroupsExist(TableNode $table) {
    foreach ($table->getHash() as $row) {
      $title = $row['title'];

      $this->createGroup(['title' => $title]);
    }
    $this->doctrine->getManager()->clear();
  }

  private function createGroup(array $data) {
    $manager = $this->container->get('os2display.group_manager');

    $group = $manager->findGroupBy(['title' => $data['title']]);
    if (!$group) {
      $group = $manager->createGroup($data);
    }

    $manager->updateGroup($group, $data);
  }

  /**
   * @When I authenticate as :username
   */
  public function iAuthenticateAs($username) {
    $this->iSignInWithUsernameAndPassword($username, NULL);
  }

  /**
   * @When I sign in with username :username and password :password
   */
  public function iSignInWithUsernameAndPassword($username, $password) {
    $user = $this->getUser($username);

    if ($user) {
      $encoder_service = $this->container->get('security.encoder_factory');
      $encoder = $encoder_service->getEncoder($user);
      if (!$password || $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
        $this->authenticate($user);
      }
    }
    else {
      $this->deauthenticate();
    }
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

  /**
   * @Then the JSON node :node should contain key :key
   */
  public function theJsonNodeShouldContainKey($node, $key) {
    $json = $this->getJson();
    $actual = $this->inspector->evaluate($json, $node);
    $this->assertTrue(array_key_exists($key, $actual), sprintf('The node "%s" should contain key "%s"', $node, $key));
  }

  /**
   * @Then the JSON node :node should not contain key :key
   */
  public function theJsonNodeShouldNotContainKey($node, $key) {
    $this->not(function () use ($node, $key) {
      return $this->theJsonNodeShouldContainKey($node, $key);
    }, sprintf('The node "%s" should not contain key "%s"', $node, $key));
  }

  /**
   * @Then the JSON node :node should contain value :value
   */
  public function theJsonNodeShouldContainValue($node, $value) {
    $json = $this->getJson();
    $actual = $this->inspector->evaluate($json, $node);
    $this->assertTrue(in_array($value, $actual), sprintf('The node "%s" should contain value "%s"', $node, $value));
  }

  /**
   * @Then the JSON node :node should not contain value :value
   */
  public function theJsonNodeShouldNotContainValue($node, $value) {
    $this->not(function () use ($node, $value) {
      return $this->theJsonNodeShouldContainKey($node, $value);
    }, sprintf('The node "%s" should not contain value "%s"', $node, $value));
  }

  /**
   * Checks that a list of elements contains a specific number of nodes matching a criterion.
   *
   * @Then the JSON node :node should contain :count element(s) with :propertyPath equal to :value
   */
  public function theJsonNodeShouldContainElementWithEqualTo($node, $count, $propertyPath, $value) {
    $json = $this->getJson();
    $items = $this->inspector->evaluate($json, $node);
    $this->assertTrue(is_array($items), sprintf('The node "%s" should be an array', $node));
    $matches = array_filter($items, function ($item) use ($propertyPath, $value) {
      $accessor = $this->container->get('property_accessor');
      return $accessor->isReadable($item, $propertyPath) && $accessor->getValue($item, $propertyPath) === $value;
    });
    $this->assertEquals($count, count($matches));
  }

  protected function getJson() {
    return new Json($this->httpCallResultPool->getResult()->getValue());
  }

  /**
   * @Then the DQL query :dql should return :count element(s)
   */
  public function theDqlQueryShouldReturnElements($dql, $count) {
    $query = $this->manager->createQuery($dql);
    $items = $query->getResult();

    $this->assertEquals($count, count($items));
  }

  /**
   * @Then the SQL query :sql should return :count element(s)
   */
  public function theSqlQueryShouldReturnElements($sql, $count) {
    $stmt = $this->manager->getConnection()->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll();

    $this->assertEquals($count, count($items));
  }

  /**
   * @Then print result of :sql
   */
  public function printResultOfSql($sql) {
    $stmt = $this->manager->getConnection()->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll();

    $rows = [];
    foreach ($items as $index => $item) {
      if ($index === 0) {
        $rows[$index+1] = array_keys($item);
      }
      $rows[$index+2] = array_values($item);
    }

    if ($rows) {
      $table = new TableNode($rows);
      echo $table->getTableAsString();
    } else {
      echo '(empty)';
    }
  }

}
