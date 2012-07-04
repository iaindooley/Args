<?php
    class TestArgs implements RocketSled\Runnable
    {
        public function run()
        {
            $mysql_filter = function($arg)
            {
                $ret = NULL;
                $chain = Args::arrayFilter();
                
                if($ret = $chain($arg))
                    //NOTE: you would actually use mysql_real_escape_string()
                    //here, but we don't want to require a db for this test
                    $ret = addslashes($arg);

                return $ret;
            };
            
            $_POST = array('name' => 'some value',
                           'empty_array' => array(),
                           'an_array' => array('ohai' => 'guy'),
                           );
            $_GET = array('another_name' => 'some value',
                          'escaped' => 'something\'s rotten');

            echo 'name: '.Args::get('name',$_POST,$_GET,Args::argv).PHP_EOL;
            echo 'name from argv: '.Args::get('name',Args::argv).PHP_EOL;
            echo 'another_name: '.Args::get('another_name',$_POST,$_GET).PHP_EOL;
            echo 'escaped: '.Args::get('escaped',$_POST,$_GET,Args::argv,$mysql_filter).PHP_EOL;
            echo 'empty array default filter: '.Args::get('empty_array',$_POST).PHP_EOL;
            echo 'empty array custom filter: '.PHP_EOL;
            print_r(Args::get('empty_array',$_POST,function($arg){return $arg;})).PHP_EOL;
            echo 'full array default filter: '.PHP_EOL;
            print_r(Args::get('an_array',$_POST)).PHP_EOL;
            echo 'full array custom filter: '.PHP_EOL;
            print_r(Args::get('an_array',$_POST,function($arg){return $arg;})).PHP_EOL;
            
            try
            {
                Args::get('ohai');
                echo '****FAILED**** why didn\'t fetching with only a name throw InvalidArgumentsRequestException?'.PHP_EOL;
            }
            
            catch(InvalidArgumentsRequestException $exc)
            {
                echo '****PASSED**** calling Args::get with only a name threw InvalidArgumentsRequestException'.PHP_EOL;
            }
        }
    }
