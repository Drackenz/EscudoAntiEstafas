<?php

namespace Tests\Feature;

use Tests\TestCase;

class JuegoEducativoTest extends TestCase
{
    public function test_muestra_juego_con_diez_casos_y_opciones(): void
    {
        $this->get('/juego')
            ->assertOk()
            ->assertSee('¿Real o estafa?')
            ->assertSee('data-casos="10"', false)
            ->assertSee('Parece legítimo')
            ->assertSee('Parece una estafa')
            ->assertSee('Tigo')
            ->assertSee('Chivo Wallet')
            ->assertSee('todos los casos son ficticios');
    }
}
