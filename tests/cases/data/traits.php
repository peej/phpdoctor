<?php

trait myTrait {
    function traitMethod() {}
}

trait myOtherTrait {
    function anotherTraitMethod() {}
}

class traitTestClass {
    use myTrait, myOtherTrait;
}