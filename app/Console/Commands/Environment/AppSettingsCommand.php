<?php
namespace App\Console\Commands\Environment;

use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\Factory as ValidatorFactory;
use App\Traits\Commands\EnvironmentWriterTrait;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Str;

class AppSettingsCommand extends Command
{
    use EnvironmentWriterTrait;

    public const ALLOWED_CACHE_DRIVERS = [
        'redis' => 'Redis (recommended)',
        'memcached' => 'Memcached',
        'file' => 'Filesystem',
    ];

    public const ALLOWED_SESSION_DRIVERS = [
        'redis' => 'Redis (recommended)',
        'memcached' => 'Memcached',
        'database' => 'MySQL Database',
        'file' => 'Filesystem',
        'cookie' => 'Cookie',
    ];

    public const ALLOWED_QUEUE_DRIVERS = [
        'redis' => 'Redis (recommended)',
        'database' => 'MySQL Database',
        'sync' => 'Sync',
    ];

    /**
     * @var \Illuminate\Contracts\Console\Kernel
     */
    protected $command;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $description = 'Configure basic environment settings for the Panel.';

    /**
     * @var string
     */
    protected $signature = 'crm:environment:app
                            {--new-salt : Whether or not to generate a new salt for Hashids.}
                            {--name= : The Name that this Panel is running on.}
                            {--url= : The URL that this Panel is running on.}
                            {--timezone= : The timezone to use for Panel times.}
                            {--cache= : The cache driver backend to use.}
                            {--session= : The session driver backend to use.}
                            {--queue= : The queue driver backend to use.}
                            {--redis-host= : Redis host to use for connections.}
                            {--redis-pass= : Password used to connect to redis.}
                            {--redis-port= : Port to connect to redis over.}';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * AppSettingsCommand constructor.
     */
    public function __construct(ConfigRepository $config, Kernel $command, ValidatorFactory $validator)
    {
        parent::__construct();

        $this->config = $config;
        $this->command = $command;
        $this->validator = $validator;
    }

    /**
     * Handle command execution.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->output->note('Nom de l\'application');
        $this->variables['APP_NAME'] = $this->option('name') ?? $this->ask(
            'Nom de l\'application',
            $this->config->get('app.name')
        );

        $this->output->comment('L\'URL de l\'application doit commencer par https:// ou http:// selon que vous utilisez SSL ou non. Si vous n\'incluez pas le schéma, vos courriels et autres contenus seront liés à un mauvais emplacement.');
        $this->variables['APP_URL'] = $this->option('url') ?? $this->ask(
            'URL de l\'application',
            $this->config->get('app.url', 'http://example.org')
        );

        $this->output->comment('Le fuseau horaire doit correspondre à l\'un des fuseaux horaires supportés par PHP. Si vous n\'êtes pas sûr, veuillez vous référer à http://php.net/manual/en/timezones.php.');
        $this->variables['APP_TIMEZONE'] = $this->option('timezone') ?? $this->anticipate(
            'Fuseau horaire de l\'application',
            DateTimeZone::listIdentifiers(DateTimeZone::ALL),
            $this->config->get('app.timezone')
        );

        $selected = $this->config->get('cache.default', 'redis');
        $this->variables['CACHE_DRIVER'] = $this->option('cache') ?? $this->choice(
            'Driver de cache',
            self::ALLOWED_CACHE_DRIVERS,
            array_key_exists($selected, self::ALLOWED_CACHE_DRIVERS) ? $selected : null
        );

        $selected = $this->config->get('session.driver', 'redis');
        $this->variables['SESSION_DRIVER'] = $this->option('session') ?? $this->choice(
            'Driver de session',
            self::ALLOWED_SESSION_DRIVERS,
            array_key_exists($selected, self::ALLOWED_SESSION_DRIVERS) ? $selected : null
        );

        $selected = $this->config->get('queue.default', 'redis');
        $this->variables['QUEUE_CONNECTION'] = $this->option('queue') ?? $this->choice(
            'Driver de queue',
            self::ALLOWED_QUEUE_DRIVERS,
            array_key_exists($selected, self::ALLOWED_QUEUE_DRIVERS) ? $selected : null
        );

        $this->checkForRedis();
        $this->writeToEnvironment($this->variables);

        $this->info($this->command->output());
    }

    /**
     * Check if redis is selected, if so, request connection details and verify them.
     */
    private function checkForRedis()
    {
        $items = collect($this->variables)->filter(function ($item) {
            return $item === 'redis';
        });

        // Redis was not selected, no need to continue.
        if (count($items) === 0) {
            return;
        }

        $this->output->note('Vous avez sélectionné le pilote Redis pour une ou plusieurs options, veuillez fournir des informations de connexion valides ci-dessous. Dans la plupart des cas, vous pouvez utiliser les valeurs par défaut fournies, à moins que vous n\'ayez modifié votre configuration.');
        $this->variables['REDIS_HOST'] = $this->option('redis-host') ?? $this->ask(
            'Hôte Redis',
            $this->config->get('database.redis.default.host')
        );

        $askForRedisPassword = true;
        if (!empty($this->config->get('database.redis.default.password'))) {
            $this->variables['REDIS_PASSWORD'] = $this->config->get('database.redis.default.password');
            $askForRedisPassword = $this->confirm('Il semble qu\'un mot de passe soit déjà défini pour Redis, voulez-vous le changer ?');
        }

        if ($askForRedisPassword) {
            $this->output->comment('Par défaut, une instance de serveur Redis n\'a pas de mot de passe car elle fonctionne localement et est inaccessible au monde extérieur. Si c\'est le cas, appuyez simplement sur la touche Entrée sans saisir de valeur.');
            $this->variables['REDIS_PASSWORD'] = $this->option('redis-pass') ?? $this->output->askHidden(
                'Mot de passe Redis'
            );
        }

        if (empty($this->variables['REDIS_PASSWORD'])) {
            $this->variables['REDIS_PASSWORD'] = 'null';
        }

        $this->variables['REDIS_PORT'] = $this->option('redis-port') ?? $this->ask(
            'Port Redis',
            $this->config->get('database.redis.default.port')
        );
    }
}