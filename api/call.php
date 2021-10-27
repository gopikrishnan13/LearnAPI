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
            
            $methods = scandir(__DIR__."/apis");
            foreach($methods as $m)
            {
                if('.' == $m or '..' == $m)
                {
                    continue;
                }
                $basename = basename($m, '.php');
                //echo "trying to call function...\n";
                if($method == $basename)
                {
                    include __DIR__."/apis/$m";
                    $func = Closure::bind(${$basename}, $this, get_class());
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