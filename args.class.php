<?php
    class Args
    {
        const argv = 'argv';
        
        public static function arrayFilter()
        {
            return function($arg)
            {
                if(is_array($arg) && !count($arg))
                    $arg = FALSE;

                return $arg;
            };
        }

        public static function get($name)
        {
            $ret = NULL;
            //get dynamica arguments
            $args = func_get_args();
            //shift off the $name argument
            array_shift($args);
            //throw an exception if they didn't pass anything
            //except the name
            if(!count($args))
                throw new InvalidArgumentsRequestException('You tried to get '.$name.' from Args::get but you didn\'t pass in any argument arrays (eg. $_POST, $_GET, or Args::argv');
            $filter = self::arrayFilter();
            //loop through the args
            foreach($args as $arg)
            {
                //if they passed in a filter
                if($arg instanceof Closure)
                    $filter = $arg;

                else
                {
                    if($arg == self::argv)
                        $arg = self::buildArgv();
                        
                    if($ret === NULL)
                        if(isset($arg[$name]))
                            $ret = $arg[$name];
                }
            }

            return $filter($ret);
        }

        private static function buildArgv()
        {
            global $argv;
            $ret = array();

            if(is_array($argv))
            {
                foreach($argv as $arg)
                {
                    if(strpos($arg,'=') !== FALSE)
                    {
                        $split = explode('=',$arg);
                        $name = $split[0];
                        $value = $split[1];
                        $ret[$name] = $value;
                    }
                }
            }
            
            return $ret;
        }
    }
    
    class InvalidArgumentsRequestException extends Exception{}
