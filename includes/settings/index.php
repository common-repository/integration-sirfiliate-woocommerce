<?php
/**
 * Index
 *
 * @author Team WSA
 */

namespace SirFiliate;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


//init
Settings_Hook_AJAX::init();
Settings_Hook_Assets::init();
Settings_Hook_General::init();
Settings_Hook_Overview::init();