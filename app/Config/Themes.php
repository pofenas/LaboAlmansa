<?php

namespace Config;

class Themes extends \Arifrh\Themes\Config\Themes
{
    public $theme     = 'labo';
    public $theme_path = 'themes';
    public $header    = 'header';
    public $template  = 'index';
    public $footer    = 'footer';
    public $css_path   = 'css';

    // CSS y JS a cargar siempre
    public $plugin_css = ['app'];
    public $plugin_js  = [];
}