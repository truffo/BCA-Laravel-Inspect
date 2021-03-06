<?php

/**
 * Inspector Tools for Artisan
 *
 * PHP Version 5.3
 *
 * @category   Command
 * @package    Laravel
 * @subpackage Artisan
 * @author     Brodkin CyberArts <oss@brodkinca.com>
 * @copyright  2013 Brodkin CyberArts.
 * @license    MIT
 * @version    GIT: $Id$
 * @link       https://github.com/brodkinca/BCA-Laravel-Inspect
 */

namespace BCA\LaravelInspect\Commands;

use Symfony\Component\Console\Input\InputOption;

/**
 * Artisan Inspect:Fix Command
 *
 * @category   Command
 * @package    Laravel
 * @subpackage Artisan
 */
class InspectFixCommand extends Inspect
{
    /**
     * Name of CLI executable
     *
     * @var string
     */
    const CLI_TOOL = 'php-cs-fixer';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'inspect:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run PHP-CS-Fixer.';

    /**
     * Run the command. Executed immediately.
     *
     * @return void
     */
    public function fire()
    {
        parent::fire();

        if (!$this->isInstalledGlobally()) {
            $this->error(
                'Due to dependency conflicts with Laravel\'s CLI tools '.
                'PHP-CS-Fixer cannot be bundled with BCA\'s Laravel-Inspect '.
                'package.'."\n\n".
                'To continue, please install PHP-CS-Fixer in your system path '.
                'by issuing the following commands:'."\n\n".
                "\t".'sudo curl http://cs.sensiolabs.org/get/php-cs-fixer.phar -o'.
                ' /usr/local/bin/php-cs-fixer'."\n".
                "\t".'sudo chmod a+x /usr/local/bin/php-cs-fixer'."\n\n".
                'Once the tool has been installed you can run '.$this->name.' '.
                'again to activate the fixer.'
            );

            return false;
        }

        $this->info('Running php-cs-fixer...');

        if (!$this->option('dry-run') && !$this->option('force')) {
            if (!$this->confirm(
                'This will permanently modify your code to comply with PSR-1. '."\n".
                'Are you sure that you want to continue? (y/n)[y]'
            )) {
                return false;
            }
        }

        $command = self::CLI_TOOL.' ';
        $command.= 'fix ';
        $command.= base_path().'/'.$this->option('path');
        $command.= ' --level=psr1';
        if ($this->option('dry-run')) {
            $command.= ' --dry-run';
        }

        passthru($command);

        $this->info('Done.');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('dry-run', null, InputOption::VALUE_NONE, 'Only shows which files would have been modified.'),
            array('force', 'f', InputOption::VALUE_NONE, 'Do not confirm before editing files.'),
            array('path', null, InputOption::VALUE_OPTIONAL, 'Path containing the files to be fixed.', 'app')
        );
    }
}
