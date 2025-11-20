<?php

it('builds safe SQL with IN and NULL', function () {
    $ref = new ReflectionClass(\Core\Database::class);

    $db = $ref->newInstanceWithoutConstructor();


    $sql = $db->table('users')
        ->select('id', 'email')
        ->whereIn('id', [1, 2, 3])
        ->whereNull('deleted_at')
        ->toSql();

    expect($sql)->toContain('SELECT id, email FROM users');
    expect($sql)->toContain('id IN (:id_0, :id_1, :id_2)');
    expect($sql)->toContain('deleted_at IS NULL');
});
