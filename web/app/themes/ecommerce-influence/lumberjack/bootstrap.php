<?php

namespace Lumberjack;

use Lumberjack\Core\Site;
use Lumberjack\Config\ThemeSupport;
use Lumberjack\Config\CustomPostTypes;
use Lumberjack\Config\CustomTaxonomies;
use Lumberjack\Config\Menus;
use Lumberjack\Functions\Assets;
use Lumberjack\Config\ACF;

require_once('autoload.php');

/**
 * ------------------
 * Core
 * ------------------
 */

// Set up the default Timber context & extend Twig for the site
new Site;

/**
 * ------------------
 * Config
 * ------------------
 */

// Register support of certain theme features
ThemeSupport::register();

// Register any custom post types
CustomPostTypes::register();

// Register any custom taxonomies
CustomTaxonomies::register();

// Register WordPress menus
Menus::register();

// Register Advanced Custom Fields
ACF::register();

/**
 * ------------------
 * Functions
 * ------------------
 */

// Enqueue assets
Assets::load();
