<h1>Langkah Langkah filament panel multi tenant dan shield</h1>
<img src='pos1.png'><br>
<ol>
    <li><pre>composer create-project laravel/laravel:^10.0 nama_proyek</pre></li>
    <li><pre>composer require filament/filament:"^3.2" -W</pre></li>
    <li><pre>php artisan filament:install --panels</pre></li>
    <li><pre>php artisan migrate</pre></li>
    <li><pre>php artisan make:filament-user</pre></li>
    <li><pre>composer require spatie/laravel-permission</pre></li>
    <li><pre>php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"</pre>
</li>
    <li>
        <pre>php artisan optimize:clear</pre><br>
        # or<br>
        <pre>php artisan config:clear</pre><br>
    </li>
    <li><pre>php artisan migrate</pre></li>
    <li>
        Tambahkan  pada model User<br>
        <pre>use HasRoles</pre>;
    </li>
    <li>
        Install Shield<br>
        <pre>composer require bezhansalleh/filament-shield</pre>
    </li>
   
    <li>
        <pre>php artisan vendor:publish --tag=filament-shield-config</pre>
    </li>

    <li>
        Tambahkan pada AdminPanelProvider.php<br>
        ->plugins([
            \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
        ]);
    </li>
    <li>
        Jalankan shieldnya agar hak aksenya berjalan<br>
        php artisan shield:install
    </li>
    <li>
        pada model yang berelasi ke team tambahkan belongsto<br>
        contoh : <br>
        <pre>
        public function team(): BelongsTo<br>
        {<br>
            return $this->belongsTo(\App\Models\Team::class);<br>
        }<br>
        </pre>

pada model team tambahkan hasmany atau belongstomany<br>

<pre>
public function members(): BelongsToMany<br>
    {<br>
        return $this->BelongsToMany(User::class);<br>
    }<br>
    public function dataalamat(): HasMany<br>
    {<br>
        return $this->HasMany(DataAlamat::class);<br>
    }<br>
    public function categories(): HasMany<br>
    {<br>
        return $this->HasMany(DataAlamat::class);<br>
    }<br>

    public function role(): HasMany<br>
    {<br>
        return $this->HasMany(Role::class);<br>
    }<br>
    </pre>

    </li>

<li>
    Buat resource baru<br>
    <pre>php artisan make:filament-resource Category --generate --simple</pre><br>
    --simple adalah opsional
</li>
<li>jalanakan ulang php artisan shield:install jika migrasi refresh</li>
<li>
    untuk role atau yang lainnya jika diperlukan tambahkan kode ini<br>
     public static ?string $tenantOwnershipRelationshipName = 'team';<br>
    protected static  ?string $tenantRelationshipName = 'role';<br>
</li>
<li>
tambahkan ini pada Spatie\Permission\Models\Role<br>
    public function team(): BelongsTo<br>
    {<br>
        return $this->belongsTo(\App\Models\Team::class);<br>
    }<br>
</li>
<li>
Tambahkan ini di .env untuk upload file<br>
<pre>FILESYSTEM_DRIVER=local</pre>
</li>
<li>
    Setting faker ke bahasa indonesia<br>
    Setting di env<br>
    <pre>FAKER_LOCALE=id_ID</pre>
    Setting di config/app.php<br>
    <pre>'faker_locale' => env('FAKER_LOCALE', 'en_US'),</pre>
</li>
<li>
    //https://stackoverflow.com/questions/70765417/laravel-filament-sum-and-count-repeater<br>
    //https://laraveldaily.com/post/filament-repeater-live-calculations-on-update<br>
</li>
<li>
https://forum.aapanel.com/d/18317-aapanel-linux-panel-6834-installation-tutorial<br>
https://www.rosehosting.com/blog/how-to-install-aapanel-on-debian-11/
</li>
<li>
Prerequisites
A server with Debian 11 as OS
User privileges: root or non-root user with sudo privileges
Step 1. Update the System
Update the system packages to the latest versions available. Execute the following command:

sudo apt-get update -y && sudo apt-get upgrade -y
Step 2. Download aaPanel script
To download and set the aaPanel script executable, use the following commands:

wget -O install.sh http://www.aapanel.com/script/install-ubuntu_6.0_en.sh

sudo chmod +x install.sh
Step 3. Install aaPanel
To install the aaPanel execute the following command:

sudo bash install.sh
You will be asked if you want to install the aaPanel into the /www directory on your server:

+----------------------------------------------------------------------
| aaPanel 6.x FOR CentOS/Ubuntu/Debian
+----------------------------------------------------------------------
| Copyright © 2015-2099 BT-SOFT(http://www.aapanel.com) All rights reserved.
+----------------------------------------------------------------------
| The WebPanel URL will be http://SERVER_IP:7800 when installed.
+----------------------------------------------------------------------

Do you want to install aaPanel to the /www directory now?(y/n): Y
Once you confirm, the installation will start and it will take up to 3 minutes. You will see the following output after successful installation:

# Congratulations! Installed successfully!

aaPanel Internet Address: https://YourServerIP:7800/64b21d3e
aaPanel Internal Address: https://YourServerIP:7800/64b21d3e
username: zjs6ojyz
password: 7cf519ed
Warning:
If you cannot access the panel,
release the following port (7800|888|80|443|20|21) in the security group
==================================================================
Time consumed: 2 Minute!

</li>
</ol>
