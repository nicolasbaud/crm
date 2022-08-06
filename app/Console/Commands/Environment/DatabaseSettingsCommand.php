<?php
namespace App\Console\Commands\Environment;

use PDOException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\DatabaseManager;
use App\Traits\Commands\EnvironmentWriterTrait;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class DatabaseSettingsCommand extends Command
{
    use EnvironmentWriterTrait;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var \Illuminate\Contracts\Console\Kernel
     */
    protected $console;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $database;

    /**
     * @var string
     */
    protected $description = 'Configure database settings for the Panel.';

    /**
     * @var string
     */
    protected $signature = 'crm:environment:database
                            {--host= : The connection address for the MySQL server.}
                            {--port= : The connection port for the MySQL server.}
                            {--database= : The database to use.}
                            {--username= : Username to use when connecting.}
                            {--password= : Password to use for this database.}';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * DatabaseSettingsCommand constructor.
     */
    public function __construct(ConfigRepository $config, DatabaseManager $database, Kernel $console)
    {
        parent::__construct();

        $this->config = $config;
        $this->console = $console;
        $this->database = $database;
    }

    /**
     * Handle command execution.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->output->note('Il est fortement recommandé de ne pas utiliser "localhost" comme hôte de votre base de données, car nous avons constaté de fréquents problèmes de connexion de socket. Si vous souhaitez utiliser une connexion locale, vous devez utiliser "127.0.0.1".');
        $this->variables['DB_HOST'] = $this->option('host') ?? $this->ask(
            'Hôte de la base de données',
            $this->config->get('database.connections.mysql.host', '127.0.0.1')
        );

        $this->variables['DB_PORT'] = $this->option('port') ?? $this->ask(
            'Port de la base de données',
            $this->config->get('database.connections.mysql.port', 3306)
        );

        $this->variables['DB_DATABASE'] = $this->option('database') ?? $this->ask(
            'Nom de la base de données',
            $this->config->get('database.connections.mysql.database', 'crm')
        );

        $this->output->note('L\'utilisation du compte "root" pour les connexions MySQL n\'est pas seulement très mal vue, elle n\'est pas non plus autorisée par cette application. Vous devrez avoir créé un utilisateur MySQL pour ce logiciel.');
        $this->variables['DB_USERNAME'] = $this->option('username') ?? $this->ask(
            'Utilisateur de la base de données',
            $this->config->get('database.connections.mysql.username', 'crmuser')
        );

        $askForMySQLPassword = true;
        if (!empty($this->config->get('database.connections.mysql.password')) && $this->input->isInteractive()) {
            $this->variables['DB_PASSWORD'] = $this->config->get('database.connections.mysql.password');
            $askForMySQLPassword = $this->confirm('Il semble que vous ayez déjà défini un mot de passe de connexion MySQL, voulez-vous le changer ?');
        }

        if ($askForMySQLPassword) {
            $this->variables['DB_PASSWORD'] = $this->option('password') ?? $this->secret('Mot de passe de la base de données');
        }

        try {
            $this->config->set('database.connections.testing', [
                'driver' => 'mysql',
                'host' => $this->variables['DB_HOST'],
                'port' => $this->variables['DB_PORT'],
                'database' => $this->variables['DB_DATABASE'],
                'username' => $this->variables['DB_USERNAME'],
                'password' => $this->variables['DB_PASSWORD'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'strict' => true,
            ]);

            $this->database->connection('testing')->getPdo();
        } catch (PDOException $exception) {
            $this->output->error('Impossible de se connecter au serveur MySQL à l\'aide des informations d\'identification fournies. L\'erreur renvoyée est "'.$exception->getMessage().'".');
            $this->output->error('Vos informations de connexion n\'ont PAS été enregistrées. Vous devrez fournir des informations de connexion valides avant de poursuivre.');

            if ($this->confirm('Retournez-y et essayez à nouveau ?')) {
                $this->database->disconnect('testing');

                return $this->handle();
            }

            return 1;
        }

        $this->writeToEnvironment($this->variables);

        $this->info($this->console->output());

        return 0;
    }
}