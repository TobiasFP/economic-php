# 1. economic-php

## 1.1. What is it

A basic wrapper around the Economics api, with input & output validation/verification.

## 1.2. How to install/use

Simply add it via composer:

    composer require tobiasfp/economics-php

Then use:

    $econ = new Economics\Economics("demo", "demo");
    $econ->createCustomer("DKK", $this->econ->customerGroup(1), "test" $this->econ->paymentTerms(1), $this->econ->vatZone(1))

For better documentation on how to use it, see the economicsTest.php file. Everything has at least one test, so it should give you a good overview.

## 1.3. Why no docblocks?

I hate docblocks.!
If a programming language does not support something, it should be fixed in the language, not by addition of features outside the scope of the language.
In my opinion, PHP8 completely removed the need for docblocks. Also, docblocks makes PHP less readable.!
TypeHint everything instead.!

If you want to add docblocks to this project for your own use, I will allow it, since we are all different, however, I will not maintain them.!
