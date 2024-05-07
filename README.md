<h1>Langkah Langkah</h1>
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

</ol>
