<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Symfony\Component\HttpKernel\Config;

namespace Novice\Config;

use Symfony\Component\Config\FileLocator as BaseFileLocator;
//use Symfony\Component\HttpKernel\KernelInterface;
use Novice\Application;

/**
 * FileLocator uses the KernelInterface to locate resources in bundles.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileLocator extends BaseFileLocator
{
    private $app;
    private $path;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     * @param null|string     $path   The path the global resource directory
     * @param array           $paths  An array of paths where to look for resources
     */
    public function __construct(Application $app, $path = null, array $paths = array())
    {
        $this->app = $app;
        if (null !== $path) {
            $this->path = $path;
            $paths[] = $path;
        }

        parent::__construct($paths);
    }

    /**
     * {@inheritdoc}
     */
    public function locate($file, $currentPath = null, $first = true)
    {
        if ('@' === $file[0]) {
            return $this->app->locateResource($file, $this->path, $first);
        }

        return parent::locate($file, $currentPath, $first);
    }
}
