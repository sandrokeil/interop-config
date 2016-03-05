# Examples

This files contains examples for each interface. The factory class uses the `ConfigurationTrait` to retrieve options 
from a configuration and optional to perform a mandatory option check or merge default options. There is also an 
example for a independent config structure of the Zend Expressive TwigRendererFactory. 

## Use a vendor.package.id config structure

Let's assume we have the following module configuration:

```php
// interop config example
return [
    // vendor name
    'doctrine' => [
        // package name
        'connection' => [
            // container id
            'orm_default' => [
                // mandatory params
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                ],
            ], 
        ],
    ],
];
```

> Note that the configuration above is injected as `$config` in `options()`

### Retrieving options
Then you have easily access to the `orm_default` options in your method with `ConfigurationTrait`.

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfigId;

class MyDBALConnectionFactory implements RequiresConfigId
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'), 'orm_default');
        
        // check if mandatory options are available or use \Interop\Config\RequiresMandatoryOptions, see below 
        if (empty($options['driverClass'])) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Driver class was not set for configuration %s.%s.%s',
                    'doctrine', 
                    'connection', 
                    'orm_default'
                )
            );
        }

        if (empty($options['params'])) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Params was not set for configuration %s.%s.%s',
                    'doctrine', 
                    'connection', 
                    'orm_default'
                )
            );
        }

        $driverClass = $options['driverClass'];
        $params = $options['params'];

        // create your instance and set options

        return $instance;
    }
    
    /**
     * Is used to retrieve options from the configuration array ['doctrine' => ['connection' => [...]]].
     *
     * @return []
     */
    public function dimensions()
    {
        return ['doctrine', 'connection'];
    }
}
```

### Mandatory options check
You can also check for mandatory options automatically with `MandatoryOptionsInterface`. Now we want also check that
option `driverClass` and `params` are available. So we also implement in the example above the interface
`RequiresMandatoryOptions`. If one of these options is missing, an exception is raised.

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Config\RequiresConfigId;

class MyDBALConnectionFactory implements RequiresConfigId, RequiresMandatoryOptions
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'), 'orm_default');

        // mandatory options check is automatically done by RequiresMandatoryOptions

        $driverClass = $options['driverClass'];
        $params = $options['params'];

        // create your instance and set options

        return $instance;
    }

    /**
     * Returns a list of mandatory options which must be available
     *
     * @return string[] List with mandatory options
     */
    public function mandatoryOptions()
    {
        return [
            'driverClass',
            'params',
        ];
    }
    
    /**
     * Is used to retrieve options from the configuration array ['doctrine' => ['connection' => [...]]].
     *
     * @return []
     */
    public function dimensions()
    {
        return ['doctrine', 'connection'];
    }
}
```

### Default options
Use the `ProvidesDefaultOptions` interface if you have default options. These options are merged with the provided options in
`\Interop\Config\RequiresConfig::options()`. Let's look at this example from 
[DoctrineORMModule](https://github.com/doctrine/DoctrineORMModule/blob/master/docs/configuration.md#how-to-use-two-connections). 
All the options under the key *orm_crawler* are optional, but it's not visible in the factory.

```php
return [
    'doctrine' => [
        'configuration' => [
            'orm_crawler' => [
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
                'hydration_cache'   => 'array',
            ],
        ],
    ],
];
```

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfigId;

class ConfigurationFactory implements RequiresConfigId, ProvidesDefaultOptions
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.configuration.orm_crawler
        $options = $this->options($container->get('config'), 'orm_crawler');

        # these keys are always available now 
        $options['metadata_cache'];
        $options['query_cache'];
        $options['result_cache'];
        $options['hydration_cache'];

        // create your instance and set options

        return $instance;
    }
    
    /**
     * Returns a list of default options, which are merged in \Interop\Config\RequiresConfig::options
     *
     * @return string[] List with default options and values
     */
    public function defaultOptions()
    {
        return [
            'metadata_cache' => 'array',
            'query_cache' => 'array',
            'result_cache' => 'array',
            'hydration_cache' => 'array',
        ];
    }
    
    /**
     * Is used to retrieve options from the configuration array 
     * ['doctrine' => ['configuration' => []]].
     *
     * @return []
     */
    public function dimensions()
    {
        return ['doctrine', 'configuration'];
    }
}
```

## Use arbitrary configuration structure
Whatever configuration structure you use, `interop-config` can handle it. You can use a three-dimensional array with
`vendor.package.id` like the examples above or you don't care of it and organize your configuration by behavior or
nature (db, cache, ... or sale, admin).
  
The following example demonstrates how to replace the [Zend Expressive TwigRendererFactory](https://github.com/zendframework/zend-expressive-twigrenderer/blob/e1dd1744bf9ba5ec364fc1320566699d04f407c4/src/TwigRendererFactory.php).
The factory uses optionally the following config structure:

```php
return [
    'debug' => true,
    'templates' => [
        'cache_dir' => 'path to cached templates',
        'assets_url' => 'base URL for assets',
        'assets_version' => 'base version for assets',
        'extension' => 'file extension used by templates; defaults to html.twig',
        'paths' => [
            // namespace / path pairs
            //
            // Numeric namespaces imply the default/main namespace. Paths may be
            // strings or arrays of string paths to associate with the namespace.
        ],
    ],
    'twig' => [
        'cache_dir' => 'path to cached templates',
        'assets_url' => 'base URL for assets',
        'assets_version' => 'base version for assets',
        'extensions' => [
            // extension service names or instances
        ],
    ],
];
```

You can see that the factory uses different keys (debug, templates, twig) of the config array on the same level. This
configuration is maybe used by other factories too like the `debug` setting. `interop-config` reduces the checks in the 
factory and gives the user the possibility to find out the config structure. More than that, it is possible to create the
configuration file from the factory.

```php
namespace Zend\Expressive\Twig;

use Interop\Container\ContainerInterface;
use Twig_Environment as TwigEnvironment;
use Twig_Extension_Debug as TwigExtensionDebug;
use Twig_ExtensionInterface;
use Twig_Loader_Filesystem as TwigLoader;
use Zend\Expressive\Router\RouterInterface;

// interop-config
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfig;
use Interop\Config\ProvidesDefaultOptions;

class TwigRendererFactory implements RequiresConfig, ProvidesDefaultOptions
{
    use ConfigurationTrait;

    /**
     * Uses root config to retrieve several options
     *
     * @return array
     */
    public function dimensions()
    {
        return [];
    }

    /**
     * This is the whole config structure with default settings for this factory
     */
    public function defaultOptions()
    {
        return [
            'debug' => false,
            'templates' => [
                'extension' => 'html.twig',
                'paths' => [],
            ],
            'twig' => [
                'cache_dir' => false,
                'assets_url' => '',
                'assets_version' => '',
                'extensions' => [],
            ],
        ];
    }

    /**
     * @param ContainerInterface $container
     * @return TwigRenderer
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];

        // no OptionNotFoundException is thrown from ConfigurationTrait, because there are no config dimensions
        $config = $this->options($config);

        $debug = (bool) $config['debug'];

        // Create the engine instance
        $loader      = new TwigLoader();
        $environment = new TwigEnvironment($loader, [
            'cache'            => $debug ? false : $config['twig']['cache_dir'],
            'debug'            => $debug,
            'strict_variables' => $debug,
            'auto_reload'      => $debug
        ]);
        // Add extensions
        if ($container->has(RouterInterface::class)) {
            $environment->addExtension(new TwigExtension(
                $container->get(RouterInterface::class),
                $config['twig']['assets_url'],
                $config['twig']['assets_version']
            ));
        }
        if ($debug) {
            $environment->addExtension(new TwigExtensionDebug());
        }
        // Add user defined extensions
        $this->injectExtensions($environment, $container, $config['twig']['extensions']);
        // Inject environment
        $twig = new TwigRenderer($environment, $config['templates']['extension']);
        // Add template paths
        foreach ($config['templates']['paths'] as $namespace => $paths) {
            $namespace = is_numeric($namespace) ? null : $namespace;
            foreach ((array) $paths as $path) {
                $twig->addPath($path, $namespace);
            }
        }
        return $twig;
    }
    /**
     * Inject extensions into the TwigEnvironment instance.
     *
     * @param TwigEnvironment $environment
     * @param ContainerInterface $container
     * @param array $extensions
     * @throws Exception\InvalidExtensionException
     */
    private function injectExtensions(TwigEnvironment $environment, ContainerInterface $container, array $extensions)
    {
        foreach ($extensions as $extension) {
            // Load the extension from the container
            if (is_string($extension) && $container->has($extension)) {
                $extension = $container->get($extension);
            }
            if (! $extension instanceof Twig_ExtensionInterface) {
                throw new Exception\InvalidExtensionException(sprintf(
                    'Twig extension must be an instance of Twig_ExtensionInterface; "%s" given,',
                    is_object($extension) ? get_class($extension) : gettype($extension)
                ));
            }
            if ($environment->hasExtension($extension->getName())) {
                continue;
            }
            $environment->addExtension($extension);
        }
    }
    // The mergeConfig function is not needed anymore
    // private function mergeConfig($config)
}
```
