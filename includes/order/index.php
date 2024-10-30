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
Order_Hook::init();
Order_Hook_AJAX::init();
Order_Hook_Assets::init();