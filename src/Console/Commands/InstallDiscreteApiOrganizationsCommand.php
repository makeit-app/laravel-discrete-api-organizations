<?php

namespace MakeIT\DiscreteApi\Organizations\Console\Commands;

use Illuminate\Console\Command;
use MakeIT\DiscreteApi\Base\Helpers\DiscreteApiHelpers;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Symfony\Component\VarExporter\VarExporter;

class InstallDiscreteApiOrganizationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'makeit:discreteapi:organizations:install';

    /**
     * Pachage configuration
     */
    protected array $_config;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation master. Small Quiz and actions based on answers.';

    /**
     * Execute the console command.
     *
     * @throws ExceptionInterface
     */
    public function handle(): void
    {
        $this->_config = require realpath(__DIR__.'/../../config.php');
        $this->newLine();
        $this->info('This is MakeIT\'s Discrete API (Organizations) Installer.');
        $this->newLine();
        $this->error(' ATTENTION please !                                                   ');
        $this->error(' We strongly recommend to deploy this package on to CLEAN Laravel 10! ');
        $this->newLine();
        if (is_file(base_path('config/discreteapiorganizations.php'))) {
            if (! $this->confirm(question: "Before begin, we need to force delete existing config file to avoid mistakes in the future configuration.\n")) {
                $this->error('Cant continue with existing config file:       ');
                $this->error('    config/config/discreteapiorganizations.php ');
                $this->newLine();

                return;
            }
        }
        $quiz['modify_source_code'] = $this->confirm(question: "Are you planning to modify the Source Code of this package?\n", default: true);
        $this->comment('INTEGRATION INSTRUCTIONS:');
        $this->newLine();
        foreach ($quiz as $k => $v) {
            switch ($k) {
                case 'modify_source_code':
                    $this->newLine();
                    if (is_bool($v)) {
                        if ($v) {
                            $this->generateDescendantss();
                            //
                            $this->info(
                                'You need to add a Middleare to the Kernel'
                            );
                            $this->newLine();
                            $this->warn('     \'api\' => .... // to the end of list');
                            $this->comment('        '.(
                                ($quiz['modify_source_code'])
                                    ? '\App\Http\Middleware\DiscreteApi\Organizations\PreloadUserOrganizationsData::class,'
                                    : '\MakeIT\DiscreteApi\Organizations\Http\Middleware\PreloadUserOrganizationsData::class,'
                            ));
                            $this->newLine(2);
                            //
                        }
                    }
                    $this->_config['route_namespace'] = 'app';
                    break;
            }
        }
        $this->newLine();
        $this->info('To automate the organizations You need to add trait to the User Model');
        $this->info('We do not know how You realize the User class and where is located, therefore You should to find and edit them manually...');
        $this->newLine();
        $this->comment('     class User extends Authenticatable implements MustVerifyEmail');
        $this->comment('     {');
        $this->comment('         //...to the end of use-list');
        $this->comment('        '.(
            ($quiz['modify_source_code'])
                ? 'use \App\Traits\DiscreteApi\Organizations\HasUserOrganizationSlots;'
                : 'use \MakeIT\DiscreteApi\Organizations\Traits\HasUserOrganizationSlots;'
        ));
        $this->comment('        '.(
            ($quiz['modify_source_code'])
                ? 'use \App\Traits\DiscreteApi\Organizations\HasOrganizations;'
                : 'use \MakeIT\DiscreteApi\Organizations\Traits\HasOrganizations;'
        ));
        $this->newLine(2);
        $this->info('To automate the organizations You need to add trait to the Profile Model');
        $this->info('We do not know how You realize the Profile class and where is located, therefore You should to find and edit them manually...');
        $this->newLine();
        $this->comment('     class Profile ....');
        $this->comment('     {');
        $this->comment('        //....');
        $this->comment('        '.(
            ($quiz['modify_source_code'])
               ? 'use \App\Traits\DiscreteApi\Organizations\HasOrganization;'
               : 'use \MakeIT\DiscreteApi\Organizations\Traits\HasOrganization;'
        ));
        $this->comment('        '.(
            ($quiz['modify_source_code'])
               ? 'use \App\Traits\DiscreteApi\Organizations\HasWorkspace;'
               : 'use \MakeIT\DiscreteApi\Organizations\Traits\HasWorkspace;'
        ));
        $this->writeNewConfig();
        $this->info('Done.');
        $this->newLine();
    }

    /**
     * Wrapper for generators
     */
    protected function generateDescendantss(): void
    {
        foreach ($this->getClasses() as $type => $generated_classes) {
            $this->generate($type, $generated_classes);
        }
    }

    /**
     * Returns an array of classes of the package compatible twith futute modifications as parent classes
     */
    protected function getClasses(): array
    {
        $return = [];
        $dirs = DiscreteApiHelpers::dirs(__DIR__.'/../../');
        $namespace = DiscreteApiHelpers::compute_namespace($this->_config);
        $namespaces = DiscreteApiHelpers::namespaces($namespace);
        foreach ($dirs as $type => $dir) {
            $return[$type] = DiscreteApiHelpers::scanDirs($type, $dir, $namespace, $namespaces, $this->_config, 'Organizations');
        }

        return $return;
    }

    /**
     * Generates source code files in to App namespace
     */
    public function generate(string $type, array $generated_classes): void
    {
        if (! empty($generated_classes['observers'])) {
            $this->_config['observersToRegister'] = [];
        }
        if (! empty($generated_classes['policies'])) {
            $this->_config['policiesToRegister'] = [];
        }
        $printer = new PsrPrinter();
        foreach ($generated_classes as $class) {
            if ($type == 'traits') {
                $this->_config = DiscreteApiHelpers::generateTrait($this, $class, $printer, $type, 'Organizations', $this->_config);
            } else {
                $this->_config = DiscreteApiHelpers::generate($this, $class, $printer, $type, 'Organizations', $this->_config);
            }
        }
    }

    /**
     * @throws ExceptionInterface
     */
    protected function writeNewConfig(): void
    {
        $content = VarExporter::export($this->_config);
        file_put_contents(config_path('discreteapiorganizations.php'), "<?php\n\nreturn ".$content.";\n");
    }
}
