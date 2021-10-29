<?php
    class Superhero
    {

        public function __construct($name)
        {
            $this->name = $name;
        }

        public function __call($method, $args)
        {
            $methods = get_class_methods($this);
            foreach($methods as $m)
            {
                if($method == $m)
                {
                    return $this->$method($args);
                }
            }
            
            $methods = scandir(__DIR__."/apis/auth");
            //die(print_r($methods));
            foreach($methods as $m)
            {
                if('.' == $m or '..' == $m)
                {
                    continue;
                }
                $basename = basename($m, '.php');
                echo "trying to call function...\n";
                if($method == $basename)
                {
                    include __DIR__."/apis/auth/$m";
                    $func = Closure::bind($get_power, $this, get_class());
                    die(var_dump($func));
                    if(is_callable($func))
                    {
                        return call_user_func_array($func,$args);
                    }
                    else
                    {
                        echo "something worng";
                    }
                }
            }
        }

        private function getName()
        {
            return $this->name;
        }
    }

$hero = new Superhero("Batman");
echo $hero->get_power();