## Placeholder Support

This is a hacky fork which implements 

    $greeting = function($title,$firstname,$secondname){
    	return $title.' '.$firstname.' '.$secondname;
    };
    $mrSmith = fp\curry($greeting,'mr',(new fp\Placeholder),'smith');
    var_dump($mrSmith('barry'));//string(14) "mr barry string"
    
Don't use this fork as I consider it an experiement based off http://ramdajs.com/0.16/docs/#__
If you need placeholder like support I suggest using the original repo and this method: 
https://github.com/camspiers/php-fp/issues/2#issuecomment-121775585

I have not run unit tests on this nor used it in production 
