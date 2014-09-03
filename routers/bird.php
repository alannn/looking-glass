<?php

/*
 * Looking Glass - An easy to deploy Looking Glass
 * Copyright (C) 2014 Guillaume Mazoyer <gmazoyer@gravitons.in>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
 */

require_once 'router.php';
require_once 'includes/utils.php';

final class Bird extends Router {
  protected function build_commands($command, $parameters) {
    $commands = array();

    $birdc = 'birdc';
    $birdc6 = 'birdc6';

    switch ($command) {
      case 'bgp':
        if (match_ipv4($parameters, false)) {
          $commands[] = $birdc.' \'show route for '.$parameters.'\'';
        } else if (match_ipv6($parameters, false)) {
          $commands[] = $birdc6.' \'show route for '.$parameters.'\'';
        } else {
          throw new Exception('The parameter is not an IPv4/IPv6 address.');
        }
        break;

      case 'as-path-regex':
        if (match_aspath_regex($parameters)) {
          $commands[] = $birdc.' \'show route where bgp_path ~ [= '.
            $parameters.' =]\'';
          $commands[] = $birdc6.' \'show route where bgp_path ~ [= '.
            $parameters.' =]\'';
        } else {
          throw new Exception('The parameter is not an AS-Path regular expression.');
        }
        break;

      case 'as':
        if (match_as($parameters)) {
          $commands[] = $birdc.' \'show route where bgp_path ~ [= '.
            $parameters.' =]\'';
          $commands[] = $birdc6.' \'show route where bgp_path ~ [= '.
            $parameters.' =]\'';
        } else {
          throw new Exception('The parameter is not an AS number.');
        }
        break;

      case 'ping':
        if (match_ipv4($parameters)) {
          $commands[] = 'ping -A -c 10 '.$parameters;
        } else if (match_ipv6($parameters)) {
          $commands[] = 'ping6 -A -c 10 '.$parameters;
        } else if (match_fqdn($parameters)) {
          $ip_address = fqdn_to_ip_address($parameters);

          if (!$ip_address) {
            throw new Exception('No A or AAAA record found for '.$parameters);
          }

          if (match_ipv4($ip_address)) {
            $commands[] = 'ping -A -c 10 '.$parameters;
          } else if (match_ipv6($ip_address)) {
            $commands[] = 'ping6 -A -c 10 '.$parameters;
          }
        } else {
          throw new Exception('The parameter is not an IPv4/IPv6 address.');
        }
        break;

      case 'traceroute':
        if (match_ipv4($parameters)) {
          $commands[] = 'traceroute -4 -A -q1 -N32 -w1 -m15 '.$parameters;
        } else if (match_ipv6($parameters)) {
          $commands[] = 'traceroute -6 -A -q1 -N32 -w1 -m15 '.$parameters;
        } else if (match_fqdn($parameters)) {
          $ip_address = fqdn_to_ip_address($parameters);

          if (!$ip_address) {
            throw new Exception('No A or AAAA record found for '.$parameters);
          }

          if (match_ipv4($ip_address)) {
            $commands[] = 'traceroute -4 -A -q1 -N32 -w1 -m15 '.$parameters;
          } else if (match_ipv6($ip_address)) {
            $commands[] = 'traceroute -6 -A -q1 -N32 -w1 -m15 '.$parameters;
          }
        } else {
          throw new Exception('The parameter is not an IPv4/IPv6 address.');
        }
        break;

      default:
        throw new Exception('Command not supported.');
    }

    return $commands;
  }
}

// End of bird.php
