<?php

namespace App\Http\Repositories;

use DB;
use Spatie\Permission\Models\Role;

class RoleRepository {

    private $role;

    public function __construct(Role $role) {
        $this->role = $role;
    }

    public function all(){
        return $this->role->paginate(5);
    }


}
