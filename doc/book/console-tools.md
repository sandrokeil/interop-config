# Console Tools
Starting in 2.1.0, interop-config began shipping with console tools.

To get an overview of available commands run in your CLI `./vendor/bin/interop-config help`. This displays the following help message.

```bash
Usage:
  command [options] [arguments]

Options:
  -h, --help, help          Display this help message

Available commands:
  generate-config           Generates options for the provided class name
  display-config            Displays current options for the provided class name
```

## generate-config
The `generate-config` command is pretty handy. It has never been so easy to create the configuration for a class which
uses one of the `Interop\Config` interfaces. Depending on implemented interfaces, a wizard will ask you for the option values.
It is also possible to update your current configuration. The value in brackets is used, if input is blank.

```bash
Usage:
  generate-config  [options] [<configFile>] [<className>]

Options:
  -h, --help, help       Display this help message

Arguments:
  configFile             Path to a config file or php://stdout for which to generate options.
  className              Name of the class to reflect and for which to generate options.

Reads the provided configuration file (creating it if it does not exist), and injects it with options for the provided 
class name, writing the changes back to the file.
```

If your PHP config file is in the folder `config/global.php` and you have a class `My\AwesomeFactory` then you run

```bash
$ ./vendor/bin/interop-config generate-config config/global.php "My\AwesomeFactory"
```

## display-config
You can also see which options are set in the configuration file for a factory. If multiple configurations are supported
through the `Interop\Config\RequiresConfigId` you can enter a *config id* or leave it blank to display all configurations.

```bash
Usage:
  display-config  [options] [<configFile>] [<className>]
  
Options:
  -h, --help, help       Display this help message

Arguments:
  configFile             Path to a config file for which to display options. It must return an array / ArrayObject.
  className              Name of the class to reflect and for which to display options.

Reads the provided configuration file and displays options for the provided class name.
```

If your PHP config file is in the folder `config/global.php` and you have a class `My\AwesomeFactory` then you run

```bash
$ ./vendor/bin/interop-config display-config config/global.php "My\AwesomeFactory"
```
