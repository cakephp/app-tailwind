<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Devserver command.
 */
class DevserverCommand extends Command
{
    /**
     * The name of this command.
     *
     * @var string
     */
    protected string $name = 'devserver';

    /**
     * Get the default command name.
     *
     * @return string
     */
    public static function defaultName(): string
    {
        return 'devserver';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Run the `bin/cake server` and `npm run dev` together';
    }

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/5/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription(static::getDescription());
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $cwd = getcwd();
        $pipeSpec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'r'],
        ];
        $io->verbose('Starting bin/cake/server');
        $cakeserver = proc_open('bin/cake server', $pipeSpec, $cakePipes, $cwd, ['CAKE_DEVSERVER' => '1']);
        stream_set_blocking($cakePipes[1], false);
        stream_set_blocking($cakePipes[2], false);

        $io->verbose('Starting npm run dev');
        $npm = proc_open('npm run dev', $pipeSpec, $npmPipes, $cwd);
        stream_set_blocking($npmPipes[1], false);
        stream_set_blocking($npmPipes[2], false);

        $servers = [
            [
                'name' => 'cake',
                'process' => $cakeserver,
                'pipes' => $cakePipes,
            ],
            [
                'name' => 'npm',
                'process' => $npm,
                'pipes' => $npmPipes,
            ],
        ];
        while (is_resource($cakeserver) && is_resource($npm)) {
            foreach ($servers as $server) {
                $output = fgets($server['pipes'][1]);
                if ($output !== false && strlen($output)) {
                    $io->out($server['name'] . ' | ' . $output, 0);
                }
            }
            usleep(100);
        }
        $io->out('Shutdown complete');
    }
}
