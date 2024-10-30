<?php
/**
 * Index
 *
 * @author Woosa Team
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


//init
Module_Authorization_Hook::init();
Module_Authorization_Hook_AJAX::init();
Module_Authorization_Hook_Assets::init();
Module_Authorization_Hook_Settings::init();