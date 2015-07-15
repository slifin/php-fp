<?php

namespace fp;
class Placeholder{}
/**
 * Returns a function that can be invoked without all required arguments.
 *
 * Doing so will return a new function with the previous argument applied.
 *
 * @param callable $fn
 * @return \Closure
 */
function curry(callable $fn,...$start) {
    $rf = is_array($fn) ? new \ReflectionMethod(...$fn) : new \ReflectionFunction($fn);
    return _curry($fn,$start, $rf->getNumberOfRequiredParameters());
}


/**
 * Internal function used for the main currying functionality
 * @param callable $fn
 * @param $appliedArgs
 * @param $requiredParameters
 * @return callable
 */
function _curry(callable $fn, $appliedArgs, $requiredParameters) {
    return function (...$args) use ($fn, $appliedArgs, $requiredParameters) {

        $originalArgs = $appliedArgs;
        $newArgs = $args;

        array_push($appliedArgs, ...$args);

        // Get the number of arguments currently applied
        $appliedArgsCount = count(array_filter($appliedArgs, function($v){
            if ($v instanceof Placeholder){
                return false;
            }
            return true;
        }));


        // If we have the required number of arguments call the function
        if ($appliedArgsCount >= $requiredParameters) {
            foreach($appliedArgs as $k=>$v){
                if ($v instanceof Placeholder){
                    $appliedArgs[$k] = $newArgs[$k];
                    unset($appliedArgs[count($originalArgs)+$k]);
                }
            }
            return $fn(...$appliedArgs);
        // If we will have the required arguments on the next call, return an optimized function
        } elseif ($appliedArgsCount + 1 === $requiredParameters) {
            return bind($fn, ...$appliedArgs);
        // Return the standard full curry
        } else {
            return _curry($fn, $appliedArgs, $requiredParameters);
        }
    };
}

/**
 * Simple bind function
 * @param callable $fn
 * @param $appliedArgs
 * @return callable
 */
function bind(callable $fn, ...$appliedArgs) {
    return function (...$args) use ($fn, $appliedArgs) {
        return $fn(...$appliedArgs, ...$args);
    };
}

/**
 * Returns a new function which is a composition the supplied functions
 * @param callable[] ...$fns
 * @return \Closure
 */
function compose(...$fns) {
    /** @var callable $prev */
    $prev = array_shift($fns);

    foreach ($fns as $fn) {
        $prev = function (...$args) use ($fn, $prev) {
            return $prev($fn(...$args));
        };
    }

    return $prev;
}
