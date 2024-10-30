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
new Module_Settings;
Module_Settings_Hook_General::init();