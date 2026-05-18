<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$token = \Laravel\Sanctum\PersonalAccessToken::findToken('138|mDkP57raChcidgNI3YKj6aGMc87p4iw4v7PVRf5s4ceac792');
if ($token) {
    echo "TOKEN VALID - user_id: " . $token->tokenable_id . " | name: " . $token->name . "\n";
    $user = $token->tokenable;
    echo "USER email: " . $user->email . "\n";
} else {
    echo "TOKEN NOT FOUND\n";
}

echo "\n--- All tokens in DB ---\n";
$tokens = DB::table('personal_access_tokens')->get(['id', 'tokenable_id', 'name']);
foreach ($tokens as $t) {
    echo "ID: {$t->id} | user_id: {$t->tokenable_id} | name: {$t->name}\n";
}