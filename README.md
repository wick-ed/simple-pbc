What is simple-pbc?
==========

simple-pbc is a very basic trait based design by contract solution for PHP (pbc => php by contract).
Which is intended for simple testing and to get a hang of the concepts.

Why another implementation?
==========
I already have another design by contract solution for PHP, which you can find within my GitHub as php-by-contract.
So why another solution?
Reason is this two solutions are hardly comparable.
simple-pbc is, as the name suggests, very simple in what it can do (no contract inheritance, etc.) and is also harder to
use (no annotation support yet) and requires PHP 5.4 which is by no means widely used by now.
php-by-contract otherwise is very easy to use and provides advanced design by contract features, but it is a full blown
solution with several composer dependencies and a not as transparent implementation.
So to simply put it: php-by-contract for serious projects, simple-pbc to test the concept and toy around with it.

Mostly simple-pbc was developed as a sideproduct of php-by-contract, as I was trying to find the best way to implement
such a solution. I found a AOP-like approach to work the best but still wanted to let you guys see what else is possible.

I hope you have fun with both, but php-by-contract will be the main project.