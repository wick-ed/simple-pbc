<?php

/**
 * Trait PbcTrait
 *
 * This trait provides basic design by contract functionality.
 * Therefor it has to rely on several things.
 * First of all, these attributes have to exist in the class using it:
 *
 * $visibility  Will contain the initial visibility of class methods
 *
 * $invariant   Contains the class invariant
 * Example:
 * private $invariant = array(
 *  '$this->test1 === "test1"'
 * );
 *
 * $preconditions   Contains the methods's preconditions
 * Example:
 * protected $preconditions = array(
 *  'methodA' => array(
 *  '$arg_0 === $arg_1',
 *  '$arg_1 === "asdf"'
 * ));
 *
 * $postconditions  Contains the methods's postconditions similar to the precondition syntax
 *
 * As you might have already figured out, we rely heavily on the __call() method.
 * For this to work all your methods have to be declared private and you might have to implement a workaround
 * if you want to use __call() yourself.
 *
 * When specifiying conditions you have to make sure to use argument names based on their argument number
 * and not their initial name (e.g. $arg_0 instead of the first aguments name). This is some behaviour of __call()'s
 * $args I could not work around.
 */
trait PbcTrait
{
    /**
     * @param $name
     * @param $args
     * @return mixed
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function __call($name, $args)
    {
        if (isset($this->visibility[$name])) {

            $calledClass = get_called_class();

            // Now check what kind of visibility we would have
            switch ($this->visibility[$name]) {

                case "protected" :

                    if (!is_subclass_of($calledClass, __CLASS__)) {

                        throw new Exception($name . ' is of protected visibility. You are not allowed to access it in this context');
                    }
                    break;

                case "public" :

                    break;

                default :

                    throw new Exception($name . ' is of private visibility. You are not allowed to access it in this context');

                    break;
            }
        }
        elseif (method_exists($this, $name) && in_array($name, get_class_methods($this))) {

            return $this->$name();

        } else {
            throw new \InvalidArgumentException;
        }

        extract($args, EXTR_PREFIX_ALL, 'arg');

        // Check the invariant up front
        foreach ($this->invariant as $invariant) {

            $string = 'return (' . $invariant . ')? true : false;';
            if (eval($string) === false) {

                throw new Exception('Invariant broken on entry of ' . $name);
            }
        }

        // Check all the preconditions
        foreach(@$this->preconditions[$name] as $precondition) {

            $string = 'return (' . $precondition . ')? true : false;';
            if (eval($string) === false) {

                throw new Exception('Precondition ' . $precondition . ' broken within ' . $name);
            }
        }

        // Save for use of old
        $this->old = clone $this;

        // Call the actual function
        $result = call_user_func_array(array($this, $name), $args);

        // Check all the postconditions
        foreach(@$this->postconditions[$name] as $postcondition) {

            $string = 'return (' . $postcondition . ')? true : false;';
            if (eval($string) === false) {

                throw new Exception('Postcondition ' . $postcondition . ' broken within ' . $name);
            }
        }

        // Check the invariant before leaving
        foreach ($this->invariant as $invariant) {

            $string = 'return (' . $invariant . ')? true : false;';
            if (eval($string) === false) {

                throw new Exception('Invariant broken on exit of ' . $name);
            }
        }
    }
}