<?php
/**
 * This file is part of the teleport package.
 *
 * Copyright (c) MODX, LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Teleport\Transport;


class xPDOObjectVehicle extends \xPDOObjectVehicle
{
    public function get(& $transport, $options = array (), $element = null) {
        $object = null;
        $element = parent :: get($transport, $options, $element);
        if (isset ($element['class']) && isset ($element['object'])) {
            $vClass = $element['class'];
            if (!empty ($element['package'])) {
                $pkgPrefix = $element['package'];
                $pkgKeys = array_keys($transport->xpdo->packages);
                if ($pkgFound = in_array(strtolower($pkgPrefix), $pkgKeys)) {
                    $pkgPrefix = '';
                }
                elseif ($pos = strpos($pkgPrefix, '.')) {
                    $prefixParts = explode('.', $pkgPrefix);
                    $prefix = '';
                    foreach ($prefixParts as $prefixPart) {
                        $prefix .= $prefixPart;
                        $pkgPrefix = substr($pkgPrefix, $pos +1);
                        if ($pkgFound = in_array(strtolower($prefix), $pkgKeys))
                            break;
                        $prefix .= '.';
                        $pos = strpos($pkgPrefix, '.');
                    }
                    if (!$pkgFound)
                        $pkgPrefix = $element['package'];
                }
                $vClass = (!empty ($pkgPrefix) ? $pkgPrefix . '.' : '') . $vClass;
            }
            $object = $transport->xpdo->newObject($vClass);
            if (is_object($object) && $object instanceof \xPDOObject) {
                $options = array_merge($options, $element);
                $setKeys = false;
                if (isset ($options[\xPDOTransport::PRESERVE_KEYS])) {
                    $setKeys = (boolean) $options[\xPDOTransport::PRESERVE_KEYS];
                }
                $object->fromJSON($element['object'], '', $setKeys, true);
            }
        }
        return $object;
    }
}
