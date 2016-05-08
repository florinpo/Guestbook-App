<?php

namespace AppBundle;

use AppBundle\Entity\Guestbook;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
  public function boot()
  {
    Guestbook::setUploadDirectory($this->container->getParameter('gb_uploads_directory'));
  }
}
