<?php

namespace Payconiq\Support\Laravel;

use Illuminate\Support\Facades\Facade;

class PayconiqFacade extends Facade {

    protected static function getFacadeAccessor() { return 'payconiq'; }


}

?>
