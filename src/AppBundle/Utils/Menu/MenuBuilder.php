<?php

namespace AppBundle\Utils\Menu;

use Knp\Menu\FactoryInterface;

class MenuBuilder
{
  private $factory;

  /**
   * @param FactoryInterface $factory
   *
   * Add any other dependency you need
   */
  public function __construct(FactoryInterface $factory)
  {
    $this->factory = $factory;
  }

  public function createGuestbookMenu(array $options)
  {
    $menu = $this->factory->createItem('root');

    $menu->addChild('Home', array('route' => 'guestbook_main'));
    $menu->addChild('New', array('route' => 'guestbook_new'));
    // ... add more children

    return $menu;
  }
}