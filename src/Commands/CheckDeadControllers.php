<?php

namespace Imanghafoori\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use Imanghafoori\LaravelMicroscope\Checks\RoutelessActions;
use Imanghafoori\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use Imanghafoori\LaravelMicroscope\ForPsr4LoadedClasses;
use Imanghafoori\LaravelMicroscope\Traits\LogsErrors;

class CheckDeadControllers extends Command
{
    use LogsErrors;

    protected $signature = 'check:dead_controllers {--f|folder= Check directory}';

    protected $customMsg = 'No dead Controller Action was found!   \(^_^)/';

    protected $description = 'Checks that public controller methods have routes.';

    public function handle(ErrorPrinter $errorPrinter)
    {
        event('microscope.start.command');
        $this->info('Checking for route-less controllers...');

        $errorPrinter->printer = $this->output;

        $folder = ltrim($this->option('folder'), '=');

        // checks calls like this: route('admin.user')
        // in the psr-4 loaded classes.
        ForPsr4LoadedClasses::check([RoutelessActions::class], [], '', $folder);

        $this->finishCommand($errorPrinter);
        $errorPrinter->printTime();

        return $errorPrinter->hasErrors() ? 1 : 0;
    }
}
