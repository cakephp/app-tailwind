<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Exception\StopException;

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
     * @return int|null The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $cwd = getcwd();
        if ($cwd === false) {
            throw new StopException('Cannot read CWD');
        }
        $pipeSpec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'w'],
        ];
        // Input is a 'server'.
        // Servers have a name, command to run, and environment vars.
        $io->verbose('Starting bin/cake/server');
        $cakeserver = proc_open(
            'bin/cake server',
            $pipeSpec,
            $cakePipes,
            $cwd,
            ['CAKE_DEVSERVER' => '1', 'PATH' => getenv('PATH')],
        );
        stream_set_blocking($cakePipes[1], false);
        stream_set_blocking($cakePipes[2], false);

        $io->verbose('Starting npm run dev');
        $npm = proc_open('npm run dev', $pipeSpec, $npmPipes, $cwd);
        stream_set_blocking($npmPipes[1], false);
        stream_set_blocking($npmPipes[2], false);

        // Prototype of internal data model
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
        $poll = true;
        while ($poll) {
            foreach ($servers as $server) {
                if (!is_resource($server['process'])) {
                    // Currently the devserver crashes as soon as any child dies.
                    // This may not be the ideal behavior, but we'll have to play with it for a while.
                    $io->err("{$server['name']} has died!");
                    $io->err((string)fgets($server['pipes'][2]));
                    $poll = false;
                    break;
                }
                $output = fgets($server['pipes'][1]);
                if ($output !== false && strlen($output)) {
                    $io->out($server['name'] . ' | ' . $output, 0);
                }
                $err = fgets($server['pipes'][2]);
                if ($err !== false && strlen($err)) {
                    $io->out($server['name'] . ' | ' . $err, 0);
                }
            }
            // Perhaps the polling interval should be configurable?
            usleep(100);
        }
        $io->verbose('Start shutdown');
        foreach ($servers as $server) {
            proc_close($server['process']);
        }
        $io->out('Shutdown complete');

        // We exit error as, normally this process gets killed with ctrl-c, and we
        // should only get here if a server died.
        return static::CODE_ERROR;
    }
}
