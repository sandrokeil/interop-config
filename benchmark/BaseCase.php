<?php

namespace InteropBench\Config;

/**
 * @BeforeMethods({"classSetUp"})
 * @Revs(10000)
 * @Iterations(10)
 */
abstract class BaseCase
{
    public function classSetUp()
    {
    }
}
