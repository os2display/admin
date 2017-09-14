<?php

namespace Indholdskanalen\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\Grouping;
use Indholdskanalen\MainBundle\Entity\UserGroup;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class MigrationDefaultUserGroupCommand
 *
 * @package Indholdskanalen\MainBundle\Command
 */
class CreateDefaultUserGroupCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('ik:create-default-user-group')
            ->setDescription('Create a default user group and add all users and content to the group');
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $groupName = $io->ask('Group name?', 'Fælles indhold');

        $confirm = $io->confirm('This will override existing data. Do you wish to continue?', false);

        if (!$confirm) {
            $output->writeln('Aborted!');
            return;
        }

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager('default');

        // Get the group "Fælles indhold".
        $group = $em->getRepository('IndholdskanalenMainBundle:Group')->findOneBy(['title' => $groupName]);

        // If it does not exist, create it.
        if (!$group) {
            $group = new Group();
            $group->setTitle($groupName);
            $em->persist($group);
        }

        // Remove users and content from the group.
        $contentGroups = $em->getRepository('IndholdskanalenMainBundle:Grouping')->findBy(['group' => $group->getId()]);
        foreach ($contentGroups as $contentGroup) {
            $em->remove($contentGroup);
        }
        $userGroups = $em->getRepository('IndholdskanalenMainBundle:UserGroup')->findBy(['group' => $group->getId()]);
        foreach ($userGroups as $userGroup) {
            $em->remove($userGroup);
        }

        // Add content to group.
        $types  = [
            'IndholdskanalenMainBundle:Channel',
            'IndholdskanalenMainBundle:Slide',
            'IndholdskanalenMainBundle:Screen',
            'ApplicationSonataMediaBundle:Media'
        ];
        foreach ($types as $type) {
            $entities = $em->getRepository($type)->findAll();
            foreach ($entities as $entity) {
                $grouping = new Grouping($group, $entity);
                $em->persist($grouping);
            }
        }

        // Add users to group
        $users = $em->getRepository('IndholdskanalenMainBundle:User')->findAll();
        foreach ($users as $user) {
            $grouping = new UserGroup();
            $grouping->setGroup($group);
            $grouping->setUser($user);
            $grouping->setRole('ROLE_GROUP_ROLE_USER');

            $em->persist($grouping);
        }

        $em->flush();

        $output->writeln('Created a default user group and added all users and content to the group');
    }
}
