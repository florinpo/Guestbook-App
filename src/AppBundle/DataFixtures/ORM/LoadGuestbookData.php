<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\DataFixtures\ORM\LoadAppData;
use AppBundle\Entity\Guestbook as Guestbook;

class LoadGuestbookData extends LoadAppData implements OrderedFixtureInterface
{
  /**
   * Main load function.
   *
   * @param Doctrine\Common\Persistence\ObjectManager $manager
   */
  function load(ObjectManager $manager)
  {
    $posts = $this->getModelFixtures();
    // Now iterate thought all fixtures
    foreach ($posts['Guestbook'] as $reference => $columns)
    {
      $post = new Guestbook();
      $post->setName($columns['name']);
      $post->setEmail($columns['email']);
      $post->setWebsite($columns['website']);
      $post->setComment($columns['comment']);
      $post->setCreatedAt(new \DateTime());
      $post->setUpdatedAt(new \DateTime());

      $manager->persist($post);
      $manager->flush();
      // Add a reference to be able to use this object in others entities loaders
      $this->addReference('Guestbook_'. $reference, $post);
    }
  }

  /**
   * The main fixtures file for this loader.
   */
  public function getModelFile()
  {
    return 'guestbook';
  }

  /**
   * The order in which these fixtures will be loaded.
   */
  public function getOrder()
  {
    return 1;
  }
}