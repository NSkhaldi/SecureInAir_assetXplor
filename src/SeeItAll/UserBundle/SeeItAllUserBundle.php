<?php

namespace SeeItAll\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SeeItAllUserBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}
