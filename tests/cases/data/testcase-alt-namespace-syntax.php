<?php

namespace PHPDoctor\Tests\woo\yay {

    class AltNamespaceSyntax {
        
        const ZERO = 0;
        const ONE = 1;
        
    }
    
}

namespace PHPDoctor\Tests\A {

    class Foo {}
    class Bar extends Foo{}

    class Bar_A extends Foo{}

}

namespace PHPDoctor\Tests\B {

    class Foo {}
    class Bar extends Foo{}

    class Bar_B extends Foo{}
}

namespace PHPDoctor\Tests\A\C{

    class Foo {}
    class Bar extends Foo{}

    class Bar_C extends Foo{}

}