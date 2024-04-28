<?php
/**
 * Plugin Name: Active Beginnings Portal by Comworks
 * Description: Active Beginnings Portal Plugin
 * Version: 0.1
 * Author: Comworks
 * License: GPL2
 */
 
/*  Copyright 2023  Comworks Media (email : info@comworks.com.au)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('PORTAL_URI', plugin_dir_path( __FILE__ ));
define('PORTAL_URL', plugin_dir_url( __FILE__ ));

require_once PORTAL_URI . 'admin/class-admin.php';
require_once PORTAL_URI . 'public/class-public.php';
