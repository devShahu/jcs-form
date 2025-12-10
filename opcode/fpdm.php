<?php

/**
 * Entry point for legacy calls
 *
 * Devs not using composer autoload will have included this file directly.
 * Keeping it as a wrapper allows to retain compatibility with legacy projects
 * while allowing adjustments to the source to improve composer integration.
 */

define('FPDM_DIRECT', true);

require_once("fpdm_class.php");

require_once("FilterASCIIHex.php");
require_once("FilterASCII85.php");
require_once("FilterFlate.php");
require_once("FilterLZW.php");
require_once("FilterStandard.php");