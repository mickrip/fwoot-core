<?php
namespace Fw;

class View
{
    static $session;
    static $userobj = false;

    static function twig($template_file, $inject = '')
    {
        self::output(
            \Fw\Twig::factory($template_file, $inject)
                ->render()
        );
    }

    static function output($o)
    {

        if (is_array($o)) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/json');
            echo json_encode($o);
        } else {
            echo $o;
        }
    }
}
