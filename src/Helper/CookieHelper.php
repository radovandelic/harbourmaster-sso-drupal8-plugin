<?php

/**
 * Copyright © 2016 Valiton GmbH.
 *
 * This file is part of Harbourmaster Drupal Plugin.
 *
 * Harbourmaster Drupal Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Harbourmaster Drupal Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Harbourmaster Drupal Plugin.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\hms\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Drupal\Core\Config\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Copyright © 2016 Valiton GmbH.
 *
 * This file is part of Harbourmaster Drupal Plugin.
 *
 * Harbourmaster Drupal Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Harbourmaster Drupal Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Harbourmaster Drupal Plugin.  If not, see <http://www.gnu.org/licenses/>.
 */
class CookieHelper implements EventSubscriberInterface {

  /**
   * @var string
   */
  protected $ssoCookieName;

  /**
   * @var string
   */
  protected $ssoCookieDomain;

  /**
   * @var bool
   */
  protected $clearTokenTriggered = FALSE;

  public function __construct(Config $hmsSettings) {
    $this->ssoCookieName = $hmsSettings->get('sso_cookie_name');
    $this->ssoCookieDomain = $hmsSettings->get('sso_cookie_domain');
  }

    /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onResponse', 512];
    return $events;
  }

  /**
   * Kernel.response subscriber that removes the SSO cookie if clearing has
   * been triggered at some point during the event.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   */
  public function onResponse(FilterResponseEvent $event) {
    // TODO test this in conjunction with Drupal's own login
    if ($this->clearTokenTriggered && $event->getRequest()->cookies->has($this->ssoCookieName)) {

      $event->getResponse()->headers->clearCookie($this->ssoCookieName, '/', $this->ssoCookieDomain);
    }
  }

  public function hasValidSsoCookie($request) {
    return (NULL !== $this->getValidSsoCookie($request));
  }

  public function getValidSsoCookie(Request $request) {
    if (!$request->cookies->has($this->ssoCookieName)) {
      return NULL;
    }

    $cookie = $request->cookies->get($this->ssoCookieName);
    // ignore fallback cookies that begin with "err"
    return preg_match('/^err/', $cookie) ? NULL : $cookie;
  }

  public function triggerClearSsoCookie() {
    $this->clearTokenTriggered = TRUE;
  }

}