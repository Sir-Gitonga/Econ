<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Vendor;

class VendorManager
{
    protected $dbAdminHost;
    protected $dbAdminUser;
    protected $dbAdminPassword;
    protected $dbPort;

    public function __construct()
    {
        $this->dbAdminHost = env('DB_HOST', '127.0.0.1');
        $this->dbPort = env('DB_PORT', '3306');
        $this->dbAdminUser = env('DB_ADMIN_USER', env('DB_USERNAME'));
        $this->dbAdminPassword = env('DB_ADMIN_PASSWORD', env('DB_PASSWORD'));
    }

    /**
     * Create database and run vendor migrations
     *
     * @param  \App\Models\User $vendorUser
     * @param  string $subdomain
     * @param  array $options
     * @return \App\Models\Vendor
     */
    public function createVendorDb($vendorUser, $subdomain, $options = [])
    {
        // sanitize and build db name
        $slug = Str::slug(substr($subdomain, 0, 50));
        $dbName = 'vendor_' . $slug;

        // ensure uniqueness
        $i = 0; $candidate = $dbName;
        while ($this->databaseExists($candidate)) {
            $i++; $candidate = $dbName . '_' . $i;
        }
        $dbName = $candidate;

        // Optionally create a DB-specific user (else reuse admin user)
        // For simplicity we'll reuse admin user for connection but store placeholders.
        // If you want to create per-vendor DB user, add SQL to create user and grant privileges (requires admin rights).
        $dbUsername = $options['db_username'] ?? null;
        $dbPassword = $options['db_password'] ?? null;

        // Create database using admin connection
        $this->createDatabase($dbName);

        // Insert vendor record to main DB
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'name' => $vendorUser->business_name ?? $vendorUser->name,
            'subdomain' => $subdomain,
            'database' => $dbName,
            'db_username' => $dbUsername,
            'db_password' => $dbPassword,
            'status' => 'active',
        ]);

        // Create a runtime DB connection for the vendor
        $this->addVendorConnection($dbName, $dbUsername, $dbPassword);

        // Run vendor migrations (path: database/migrations/vendor)
        Artisan::call('migrate', [
            '--database' => 'vendor',
            '--path' => '/database/migrations/vendors',
            '--force' => true,
        ]);

        // Optionally seed default data:
        // Artisan::call('db:seed', ['--class' => 'VendorDatabaseSeeder', '--database' => 'vendor', '--force' => true]);

        return $vendor;
    }

    protected function addVendorConnection($dbName, $dbUsername = null, $dbPassword = null)
    {
        $connection = [
            'driver' => 'mysql',
            'host' => $this->dbAdminHost,
            'port' => $this->dbPort,
            'database' => $dbName,
            'username' => $dbUsername ?? env('DB_USERNAME'),
            'password' => $dbPassword ?? env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];

        Config::set('database.connections.vendor', $connection);

        // Purge and reconnect to ensure Laravel uses it
        DB::purge('vendor');
        DB::reconnect('vendor');
    }

    protected function createDatabase($dbName)
    {
        // Connect using admin credentials to create database
        $dsn = "mysql:host={$this->dbAdminHost};port={$this->dbPort}";
        $pdo = new \PDO($dsn, $this->dbAdminUser, $this->dbAdminPassword, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        // Execute create DB SQL
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    }

    protected function databaseExists($dbName)
    {
        try {
            $dsn = "mysql:host={$this->dbAdminHost};port={$this->dbPort};dbname={$dbName}";
            $pdo = new \PDO($dsn, $this->dbAdminUser, $this->dbAdminPassword, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
