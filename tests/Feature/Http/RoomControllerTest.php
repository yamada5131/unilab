<?php

use App\Models\Machine;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('rooms index page displays all rooms', function () {
    // Arrange
    Room::factory()->count(3)->create();

    // Act
    $response = $this->get(route('rooms.index'));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Room/Index')
        ->has('rooms.data', 3)
        ->has('rooms.data.0', fn (Assert $page) => $page
            ->hasAll(['id', 'name', 'grid_rows', 'grid_cols'])
        )
    );
});

test('store endpoint creates new room', function () {
    // Arrange
    $roomData = [
        'name' => 'Test Room',
        'grid_rows' => 5,
        'grid_cols' => 6,
    ];

    // Act
    $response = $this->post(route('rooms.store'), $roomData);

    // Assert
    $response->assertRedirect(route('rooms.index'));
    $this->assertDatabaseHas('rooms', $roomData);
});

test('store endpoint validates input', function () {
    // Act
    $response = $this->post(route('rooms.store'), [
        'name' => '',
        'grid_rows' => 'not-a-number',
        'grid_cols' => -1,
    ]);

    // Assert
    $response->assertSessionHasErrors(['name', 'grid_rows', 'grid_cols']);
});

test('show endpoint displays room with machines', function () {
    // Arrange
    $room = Room::factory()->create();
    $machines = Machine::factory()->count(3)->create(['room_id' => $room->id]);

    // Act
    $response = $this->get(route('rooms.show', $room->id));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Room/Show')
        ->has('room.data', fn (Assert $page) => $page
            ->where('id', $room->id)
            ->hasAll(['name', 'grid_rows', 'grid_cols'])
            ->has('machines', 3)
        )
    );
});

test('update endpoint modifies room', function () {
    // Arrange
    $room = Room::factory()->create();
    $updatedData = [
        'name' => 'Updated Name',
        'grid_rows' => 10,
        'grid_cols' => 12,
    ];

    // Act
    $response = $this->patch(route('rooms.update', $room->id), $updatedData);

    // Assert
    $response->assertRedirect(route('rooms.index'));
    $this->assertDatabaseHas('rooms', array_merge(['id' => $room->id], $updatedData));
});

test('update endpoint supports partial updates', function () {
    $room = Room::factory()->create(['name' => 'Old Name', 'grid_rows' => 5, 'grid_cols' => 5]);

    // Chỉ cập nhật tên
    $response = $this->patch(route('rooms.update', $room->id), ['name' => 'New Name']);

    $this->assertDatabaseHas('rooms', [
        'id' => $room->id,
        'name' => 'New Name',
        'grid_rows' => 5, // Giữ nguyên giá trị cũ
        'grid_cols' => 5, // Giữ nguyên giá trị cũ
    ]);
});

test('destroy endpoint removes room', function () {
    // Arrange
    $room = Room::factory()->create();

    // Act
    $response = $this->delete(route('rooms.destroy', $room->id));

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
});

test('unauthorized users cannot access rooms', function () {
    // Arrange
    Auth::logout();

    // Act
    $response = $this->get(route('rooms.index'));

    // Assert
    $response->assertRedirect(route('login'));
});
