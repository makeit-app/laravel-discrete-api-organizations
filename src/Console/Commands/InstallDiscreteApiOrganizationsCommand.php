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
        $this->_config = require realpath(__DIR__ . '/../../config.php');
        $this->newLine();
        $this->info('This is MakeIT\'s Discrete API (Organizations) Installer.');
        $this->newLine();
        $this->error(' ATTENTION please !                                                   ');
        $this->error(' We strongly recommend to deploy this package on to CLEAN Laravel 10! ');
        $this->newLine();
        if (is_file(base_path('config/discreteapiorganizations.php'))) {
            if (!$this->confirm(question: "Before begin, we need to force delete existing config file to avoid mistakes in the future configuration.\n")) {
                $this->error('Cant continue with existing config file:       ');
                $this->error('    config/config/discreteapiorganizations.php ');
                $this->newLine();
                return;
            }
        }
        $quiz['modify_source_code'] = $this->confirm(question: "Are you planning to modify the Source Code of this package?\n", default: true);
        $quiz['middleware1'] = $this->confirm(question: "Install `PreloadUserData (Organizations)` Middleware?\n", default: true);
        $this->comment('INTEGRATION INSTRUCTIONS:');
        $this->newLine();
        foreach ($quiz as $k => $v) {
            switch ($k) {
                case 'modify_source_code':
                    $this->newLine();
                    if (is_bool($v)) {
                        if ($v) {
                            //$this->generateDescendantss();
                        }
                    }
                    $this->_config['route_namespace'] = 'app';
                    break;
                case 'middleware1':
                    if (is_bool($v)) {
                        if ($v) {
                            $this->info('You need to add Middleware to the api group');
                            $this->newLine();
                            $this->comment('     //...');
                            $this->comment('     ');
                            $this->comment('     protected $middlewareGroups = [');
                            $this->comment('        \'api\' => [');
                            $this->comment('            //.... to the end');
                            $this->comment('            ' . (
                                ($quiz['modify_source_code'])
                                    ? '\App\Http\Middleware\DiscreteApi\Organizations\PreloadUserOrganizationsData::class,'
                                    : '\MakeIT\DiscreteApi\Organizations\Http\Middleware\PreloadUserOrganizationsData::class,'
                            ));
                            $this->newLine();
                        }
                    }
                    break;
            }
        }
        $this->newLine();
        $this->info('To automate the organizations database creation You need to add trait to the User class');
        $this->info('We do not know how You realize the User class and where is located, therefore You should to find and edit them manually...');
        $this->newLine();
        $this->comment('     class User extends Authenticatable implements MustVerifyEmail');
        $this->comment('     {');
        $this->comment('         //...to the end of use-list');
        $this->comment('         '.(
            ($quiz['modify_source_code'])
                ? 'use \App\Traits\DiscreteApi\Organizations\HasOrganizationSlots;'
                : 'use \MakeIT\DiscreteApi\Organizations\Traits\HasOrganizationSlots;'
        ));

        $this->newLine();
        $this->info('To automate the organizations database creation You need to add created() method to the UserObserver class');
        $this->info('We do not know how You realize the UserObserver class and where is located, therefore You should to find and edit them manually...');
        $this->newLine();
        $this->comment('     public function created(Model $model): void');
        $this->comment('     {');
        $this->comment('         //...to the end');
        $this->comment('         $model->organization_slots()->create();');
        $this->comment('         // Creating a dependent organization with associated data...');
        $this->comment('         app(\MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCreateContract::class)->handle($model, [');
        $this->comment('             \'title\' => __(\'Personal Organization\'),');
        $this->comment('             \'description\' => __(\'This is Your personal Organization. This Organization is free for Your personal use. You can not delete it. You may change this description at any time.\')');
        $this->comment('         ]);');
        $this->newLine();
        $this->newLine(2);
        $this->info('Finally You need to add HasOrganizations Trait to the User Model');
        $this->newLine(2);
        $this->comment('     class Profile ....');
        $this->comment('     {');
        $this->comment('        //....');
        $this->comment('        '.(
            ($quiz['modify_source_code'])
               ? 'use \App\Traits\DiscreteApi\Organizations\HasOrganizations;'
               : 'use \MakeIT\DiscreteApi\Organizations\Traits\HasOrganizations;'
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
        $dirs = DiscreteApiHelpers::dirs(__DIR__ . '/../../');
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
        if (!empty($generated_classes['observers'])) {
            $this->_config['observersToRegister'] = [];
        }
        if (!empty($generated_classes['policies'])) {
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
        file_put_contents(config_path('discreteapiorganizations.php'), "<?php\n\nreturn " . $content . ";\n");
    }
}
