<?php

namespace Tests\Feature\CRUD;

use PHPUnit\Framework\ExpectationFailedException;
use Tests\TestCase;

/**
 * Class CRUDTestCase
 *
 * @version 1.0.0
 * @date    2019-03-11
 * @author  Yure Nery <yurenery@gmail.com>
 */
class CRUDTestCase extends TestCase
{

    public function setUp() : void
    {
        parent::setUp();
    }

    /**
     * Return name of the class for testing.
     *
     * @return string
     */
    protected function getCRUDClassUnderTest()
    {
        return '';
    }

    /**
     * Use try catch is output mus be verbose.
     *
     * @param \Closure $callback
     * @param \Closure|null $catchBack
     */
    protected function tryCatchIfVerbose(\Closure $callback, $catchBack = null)
    {
        if($this->isVerboseOutput()){
            try{
                $callback();
            } catch(ExpectationFailedException $exception){
                if($catchBack instanceOf \Closure) {
                    $catchBack($exception);
                }
            }
        }else{
            $callback();
        }
    }

    /**
     * Throws crud exception.
     *
     * @param $message
     */
    protected function throwCrudException($message)
    {
        throw new ExpectationFailedException($message);
    }
}