<?php
/**
 * Htpasswd CLI utility
 *
 * @package axy\htpasswd\cli
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/htpasswd-cli/master/LICENSE MIT
 * @link https://github.com/axypro/htpasswd-cli repository
 * @link https://packagist.org/packages/axy/htpasswd-cli composer package
 * @uses PHP5.4+
 */

namespace axy\htpasswd\cli;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
