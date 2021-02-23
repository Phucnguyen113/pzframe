<?php

    class route{
        protected static $route=[];
        protected static $routeGroup=[];
        public static function addRoute($method,$url,$action){
            self::$route[]=func_get_args();
        }
        public static function get($url,$action){
            self::addRoute('get',...func_get_args());
        }
        public static function post($url,$action){
            self::addRoute('post',...func_get_args());
        }
        public static function any($url,$action){
            self::addRoute('get|post',...func_get_args());
        }
        public static function group($groupName,$closure){
            if(is_array($groupName)){
                if(isset($groupName['prefix'])){
                    self::addRoute('group',$groupName['prefix'],'group');
                    $closure();
                    self::addRoute('group',$groupName['prefix'],'group');
                    $routeGroup=self::GetRouteGroup($groupName['prefix']);
                    if($routeGroup){
                        $newRouteGroupAddedPrefix=self::addPrefixIntoRoute($groupName['prefix'],$routeGroup);
                        foreach ($newRouteGroupAddedPrefix as $key => $route) {
                            list($method,$url,$action)=$route;
                           self::addRoute($method,$url,$action);
                        }
                    }
                }
                return new static;
            }
        }

        public static function GetRouteGroup($groupName){
            $pointRouteGroup=self::findStartGroupName($groupName);
            if(!empty($pointRouteGroup)){
                $newRouteGroup=array_splice(self::$route,$pointRouteGroup[0][0],$pointRouteGroup[1]);
                return $newRouteGroup;
            }
            return false;
        }
        public static function addPrefixIntoRoute($prefix,$arrayRoute){
            foreach ($arrayRoute as $key => $value) {
                list($method,$url,$action)=$value;
                if($method=='group'){
                    unset($arrayRoute[$key]);
                    continue;
                }
                if(preg_match('/\/$/',$prefix)){
                    $url=$prefix.$url;
                }else{
                    $url=$prefix.'/'.$url;
                }
                $arrayRoute[$key]=[$method,$url,$action];
                
            }
           return $arrayRoute;
        }
        public static function findStartGroupName($groupName){
            $point=[];
            $countRoute=0;
            $check=false;
            foreach (self::$route as $key => $route) {
                list($method,$group,$action)=$route;
                if($method=='group' && $group=$groupName){
                    $point[]=$key;
                    $check=true;
                };
                if($check) $countRoute++;
                if(count($point)==2){
                    return [$point,$countRoute];
                }
            }
            return [];
        }
        public static function mapingRoute(Request $request){
            $routeRegistered=self::$route;
            foreach ($routeRegistered as $Routekey => $route) {
                list($method,$url,$action)=$route;
                if(strpos($request->method(),$method)===false){
                    continue;
                }
                if(strcmp($request->url(),$url)==0){
                    if(preg_match('/{/',$request->url())){
                        
                    }
                }
            }
        }
    }
?>