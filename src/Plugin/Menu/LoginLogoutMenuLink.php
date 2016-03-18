<?php

/**
 * Copyright © 2016 Valiton GmbH.
 *
 * This file is part of msg-web.
 *
 * msg-web is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * msg-web is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with msg-web.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\hms\Plugin\Menu;

use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Plugin\Menu\LoginLogoutMenuLink as DrupalLoginLogoutMenuLink;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\hms\User\Manager as HmsUserManager;

class LoginLogoutMenuLink extends DrupalLoginLogoutMenuLink {

  /**
   * @var \Drupal\hms\User\Manager
   */
  protected $hmsUserManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, StaticMenuLinkOverridesInterface $static_override, AccountInterface $current_user, HmsUserManager $hmsUserManager) {
    $this->hmsUserManager = $hmsUserManager;
    parent::__construct($configuration, $plugin_id, $plugin_definition, $static_override, $current_user);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu_link.static.overrides'),
      $container->get('current_user'),
      $container->get('hms.user_manager')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getRouteName() {
    if ($this->currentUser->isAuthenticated()) {
      $userKey = $this->hmsUserManager->findHmsUserKeyForUid($this->currentUser->id());
      return (NULL === $userKey) ? 'user.logout' : 'hms.logout';
    }
    else {
      return 'hms.login_page';
    }
  }

}