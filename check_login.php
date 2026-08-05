<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;

echo "=== CEK USER MAHASISWA ===\n";
$user = User::where('mhsnobp', '2210050')->where('role', 'mahasiswa')->first();
if ($user) {
    echo "User FOUND!\n";
    echo "mhsnobp: " . $user->mhsnobp . "\n";
    echo "role: " . $user->role . "\n";
    echo "password hash: " . $user->password . "\n";
    $check1 = Hash::check('01112002', $user->password);
    $check2 = Hash::check('2210050', $user->password);
    echo "Check '01112002': " . ($check1 ? 'MATCH ✓' : 'NO MATCH') . "\n";
    echo "Check '2210050': " . ($check2 ? 'MATCH ✓' : 'NO MATCH') . "\n";
} else {
    echo "User NOT FOUND untuk mhsnobp=2210050\n";
}

echo "\n=== SAMPLE USER MAHASISWA (5 pertama) ===\n";
$all = User::where('role', 'mahasiswa')->take(5)->get(['id','mhsnobp','role','email']);
foreach ($all as $u) {
    echo "id={$u->id} | mhsnobp={$u->mhsnobp} | role={$u->role} | email={$u->email}\n";
}

echo "\n=== CEK MAHASISWA TABLE ===\n";
$mhs = Mahasiswa::find('2210050');
if ($mhs) {
    echo "Mahasiswa FOUND: " . $mhs->mhsnama . "\n";
    echo "Tgl Lahir: " . $mhs->mhstgllahir . "\n";
    echo "mhstgllahir: " . ($mhs->mhstgllahir ? $mhs->mhstgllahir->format('dmY') : 'null') . "\n";
} else {
    echo "Mahasiswa NOT FOUND di tabel mahasiswa\n";
}

echo "\n=== TOTAL USERS PER ROLE ===\n";
$counts = User::selectRaw('role, count(*) as total')->groupBy('role')->get();
foreach ($counts as $c) {
    echo "role={$c->role}: {$c->total} users\n";
}
