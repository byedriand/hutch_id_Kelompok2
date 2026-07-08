<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_user_management_index_view_includes_add_user_button(): void
    {
        $view = $this->view('admin.users.index', ['users' => collect([])]);

        $view->assertSee('Tambah Pengguna');
    }
}
