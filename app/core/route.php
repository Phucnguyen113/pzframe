<?php

    class route{
        protected static $route=[];
        protected static $param=[];
        public static function addRoute($method,$url,$action){
            self::$route[]=func_get_args();
        }
        public static function get($url,$action){
            self::addRoute('get',...func_get_args());
            return new static;
        }
        public static function post($url,$action){
            self::addRoute('post',...func_get_args());
            return new static;
        }
        public static function any($url,$action){
            self::addRoute('get|post',...func_get_args());
            return new static;
        }
        public function name($name){
            $currentRoute=array_pop(self::$route);
            $currentRoute+=['name'=>$name];
            array_push(self::$route,$currentRoute);
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
            array_push(self::$route,['get|post','*',function(){
                die('page not found');
            }]);
            $routeRegistered=self::$route;
            $check=false;
            foreach ($routeRegistered as $Routekey => $route) {
                list($method,$url,$action)=$route; 
               
                if($url=='*'){
                    $check=true;
                    break;
                }
                if(strpos(strtolower($request->method()),$method)===false){ 
                    continue;
                }
               
                $arrayUrlRoute=explode('/',trim($url,'/'));
                $requestUrl=trim($request->url(),'/');
                $arrayUrlRequest=explode('/',$requestUrl);
                if(count($arrayUrlRoute)!==count($arrayUrlRequest)) continue;
                if(preg_match('/({|})+/',$url)){    
                    //Route have param for closure
                    foreach ($arrayUrlRoute as $urlKey => $urlValue) {
                        if(preg_match('/({|})+/',$urlValue)){
                            self::$param[]=$arrayUrlRequest[$urlKey];
                            $arrayUrlRoute[$urlKey]=$arrayUrlRequest[$urlKey];
                        }
                    }
                    $newUrlRoute=implode('/',$arrayUrlRoute);
                    if(strcmp($requestUrl,$newUrlRoute)===0){
                        $check=true;
                        break;
                    }
                }else{
                
                    if(strcmp($requestUrl,trim($url,'/'))===0){
                        $check=true;
                        break;
                    }
                }   
            }
            if($check){
                //middleware execute
                $middleware='';
                if(isset($route['middleware'])){
                    self::middlewareExecute($route['middleware'],$middleware);
                }
                if(is_callable($action)){
                    $info=new ReflectionFunction($action);
                    if($info->getNumberOfParameters()!== count(self::$param)){
                        foreach ($info->getParameters() as $keyParameters => $valueParameters) {
                            $objectRequestCheck=$valueParameters->getClass();
                            if(isset($objectRequestCheck->name) && $objectRequestCheck->name=='Request'){
                                $request=new Request;
                                if(isset(self::$param[$keyParameters])){
                                    $temp=self::$param[$keyParameters];
                                    self::$param[$keyParameters]=$request;
                                    array_push(self::$param,$temp);
                                }else{
                                    self::$param[$keyParameters]=$request;
                                }
                            }
                        }
                        
                    };
                    call_user_func_array($action,self::$param);
                }else if(is_string($action)){
                    self::callbackController($action);
                }

                if(is_object($middleware)){
                    $parentClass=new ReflectionClass($middleware);
                    $parentClassName=$parentClass->getParentClass();
                    if($parentClassName->name=='middleware'){
                        $middleware->after();
                    }
                }
            }else{
                self::$param=[];
            }
        }
        public static function callbackController($strAction){
            $arrayAction=explode('@',$strAction);
            if(count($arrayAction)!==2) return false;
            list($controller,$action)=$arrayAction;
            if(file_exists('../app/controller/'.$controller.'.php')){
                require_once '../app/controller/'.$controller.'.php';
                $class=new ReflectionClass($controller);
                $info=$class->getMethod($action);
                foreach ($info->getParameters() as $keyParameters => $valueParameters) {
                    $objectRequestCheck=$valueParameters->getClass();
                    if(isset($objectRequestCheck) && $objectRequestCheck->name=='Request'){
                        $request=new Request;
                        if(isset(self::$param[$keyParameters])){
                            $temp=self::$param[$keyParameters];
                            self::$param[$keyParameters]=$request;
                            array_push(self::$param,$temp);
                        }else{
                            self::$param[$keyParameters]=$request;
                        }
                    }
                }
                call_user_func_array([$controller,$action],self::$param);
            }else{
                die("controller $controller không tồn tại");
            }
           
        }
        public function middleware($name){
            $currentRoute=array_pop(self::$route);
            $currentRoute+=['middleware'=>$name];
            array_push(self::$route,$currentRoute);
        }
        public static function middlewareExecute($middlewareName,&$middleware){
           
            if(file_exists('../app/middleware/'.$middlewareName.'Middleware.php')){
                require_once '../app/middleware/'.$middlewareName.'Middleware.php';
                $middlewareName.="Middleware";
                $middleware= new $middlewareName;
                $middleware->before();
            }else{
                die('../app/middleware/'.$middlewareName.'.php');
            }
        }
    }
?>