<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\UsuarioAdm;

class UsuarioAdmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $senhanormal = 1234;
        $senha = bcrypt($senhanormal);

        $user = User::create([

            'email' => 'unir@unir.com',
            'password' => $senha,
            'nivel_acesso' => 'adm',

        ]);

        $usuario_adm = UsuarioAdm::create([

            'user_id' => $user->id,
            'nome' => 'unir',
            'status' => 'ativo',

        ]);
    }
}
