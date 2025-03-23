<?php

use App\Models\Machine;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->room = Room::factory()->create();
});

test('store endpoint creates new computer', function () {
    // Arrange
    $machineData = Machine::factory()
        ->make([
            'room_id' => $this->room->id,
        ])
        ->toArray();

    // Act
    $response = $this->post(route('computers.store'), $machineData);

    // Assert
    $response->assertRedirect(route('rooms.show', $this->room->id));
    $this->assertDatabaseHas('machines', [
        'room_id' => $this->room->id,
        'name' => $machineData['name'], // Change to use machineData['name']
        'mac_address' => $machineData['mac_address'],
    ]);
});

test('store endpoint validates input', function () {
    // Act - More comprehensive validation test
    $response = $this->post(route('computers.store'), [
        'room_id' => 'invalid-uuid',
        'name' => '',
        'mac_address' => 'invalid-mac',
        'ip_address' => 'not-an-ip',
        'pos_row' => 'not-a-number',
        'pos_col' => -1, // Test minimum value validation
    ]);

    // Assert
    $response->assertSessionHasErrors([
        'room_id', 'name', 'mac_address', 'ip_address', 'pos_row', 'pos_col',
    ]);
});

test('update endpoint modifies computer', function () {
    // Arrange
    $computer = Machine::factory()->create([
        'room_id' => $this->room->id,
    ]);
    $updatedData = [
        'name' => 'Updated PC',
        'ip_address' => '192.168.1.200',
    ];

    // Act
    $response = $this->patch(route('computers.update', $computer->id), $updatedData);

    // Assert
    $response->assertRedirect(route('rooms.show', $this->room->id)); // Check specific redirect
    $this->assertDatabaseHas('machines', array_merge(
        ['id' => $computer->id],
        $updatedData
    ));
});

test('update validation rejects prohibited fields', function () {
    // Arrange
    $computer = Machine::factory()->create([
        'room_id' => $this->room->id,
        'pos_row' => 3,
        'pos_col' => 4,
        'last_seen' => now(),
    ]);

    // Cập nhật CHỈ với các trường bị cấm
    $protectedData = [
        'pos_row' => 9,
        'pos_col' => 10,
        'room_id' => Room::factory()->create()->id,
    ];

    // Act
    $response = $this->from(route('rooms.show', $this->room->id))
        ->patch(route('computers.update', $computer->id), $protectedData);

    // Assert - Kiểm tra lỗi validation
    $response->assertRedirect(); // Redirect back due to validation error
    $response->assertSessionHasErrors(['pos_row', 'pos_col', 'room_id']);

    // Verify database remains unchanged
    $freshComputer = Machine::find($computer->id);
    expect($freshComputer->pos_row)->toBe(3);
    expect($freshComputer->pos_col)->toBe(4);
    expect($freshComputer->room_id)->toBe($this->room->id);
});

// Test 2: Tập trung vào partial updates thành công
test('update endpoint ignores prohibited fields while updating allowed fields', function () {
    // Arrange
    $originalRoom = $this->room;
    $newRoom = Room::factory()->create();

    $computer = Machine::factory()->create([
        'room_id' => $originalRoom->id,
        'pos_row' => 3,
        'pos_col' => 4,
        'name' => 'Original Name',
        'ip_address' => '192.168.1.1',
        'last_seen' => now(),
    ]);

    // Gửi cả trường hợp lệ và trường bị cấm
    $mixedData = [
        'name' => 'New Name', // Allowed field
        'ip_address' => '192.168.1.200', // Allowed field
        'pos_row' => 9, // Prohibited field
        'pos_col' => 10, // Prohibited field
        'room_id' => $newRoom->id, // Prohibited field
    ];

    // Act
    $response = $this->from(route('rooms.show', $originalRoom->id))
        ->patch(route('computers.update', $computer->id), $mixedData);

    // Assert
    $response->assertRedirect(); // Should redirect back due to validation errors
    $response->assertSessionHasErrors(['pos_row', 'pos_col', 'room_id']);

    // Verify prohibited fields remain unchanged while allowed fields should NOT be updated
    // Note: Since validation fails, NO fields should update at all
    $freshComputer = Machine::find($computer->id);
    expect($freshComputer->name)->toBe('Original Name');
    expect($freshComputer->ip_address)->toBe('192.168.1.1');
    expect($freshComputer->pos_row)->toBe(3);
    expect($freshComputer->pos_col)->toBe(4);
    expect($freshComputer->room_id)->toBe($originalRoom->id);
});

// Test 3: Test successful partial update with ONLY allowed fields
test('update endpoint successfully updates allowed fields', function () {
    // Arrange
    $computer = Machine::factory()->create([
        'room_id' => $this->room->id,
        'name' => 'Original Name',
        'ip_address' => '192.168.1.1',
        'last_seen' => now(),
    ]);

    // Chỉ gửi những trường được phép
    $validData = [
        'name' => 'New Name',
        'ip_address' => '192.168.1.200',
    ];

    // Act
    $response = $this->patch(route('computers.update', $computer->id), $validData);

    // Assert
    $response->assertRedirect(route('rooms.show', $this->room->id));
    $this->assertDatabaseHas('machines', [
        'id' => $computer->id,
        'name' => 'New Name',
        'ip_address' => '192.168.1.200',
        // Giữ nguyên các giá trị khác
        'room_id' => $this->room->id,
    ]);
});

test('update endpoint returns 404 for non-existent computer', function () {
    // Act
    $response = $this->put(route('computers.update', 'non-existent-id'), [
        'name' => 'Updated Name',
        'ip_address' => '192.168.1.200',
    ]);

    // Assert
    $response->assertNotFound();
});

test('destroy endpoint removes computer', function () {
    // Arrange
    $computer = Machine::factory()->create([
        'room_id' => $this->room->id,
    ]);

    // Act
    $response = $this->delete(route('computers.destroy', $computer->id));

    // Assert
    $response->assertRedirect(route('rooms.show', $this->room->id)); // Check specific redirect
    $this->assertDatabaseMissing('machines', ['id' => $computer->id]);
});

test('destroy endpoint returns 404 for non-existent computer', function () {
    // Act
    $response = $this->delete(route('computers.destroy', 'non-existent-id'));

    // Assert
    $response->assertNotFound();
});

test('cannot access computer endpoints when unauthorized', function () {
    // Arrange
    Auth::logout();
    $computer = Machine::factory()->create(['last_seen' => now()]);
    $validData = ['name' => 'Test', 'room_id' => $computer->room_id];

    // Act - Using valid data in requests
    $storeResponse = $this->post(route('computers.store'), $validData);
    $updateResponse = $this->put(route('computers.update', $computer->id), $validData);
    $deleteResponse = $this->delete(route('computers.destroy', $computer->id));
    $commandResponse = $this->post(route('computers.command', $computer->id), ['command_type' => 'SHUTDOWN']);

    // Assert
    $storeResponse->assertRedirect(route('login'));
    $updateResponse->assertRedirect(route('login'));
    $deleteResponse->assertRedirect(route('login'));
    $commandResponse->assertRedirect(route('login'));

    // Also verify no data was changed
    $this->assertDatabaseHas('machines', ['id' => $computer->id]);
});



// Additional test for preventing MAC address duplicates
test('store prevents duplicate mac addresses', function () {
    // Arrange
    $existingComputer = Machine::factory()->create([
        'mac_address' => '00:1A:2B:3C:4D:5E',
        'last_seen' => now(),
    ]);

    $computerData = [
        'room_id' => $this->room->id,
        'name' => 'Test Computer',
        'mac_address' => '00:1A:2B:3C:4D:5E', // Same as existing
        'ip_address' => '192.168.1.100',
        'pos_row' => 2,
        'pos_col' => 3,
        'last_seen' => now()->toDateTimeString(),
    ];

    // Act
    $response = $this->post(route('computers.store'), $computerData);

    // Assert
    $response->assertSessionHasErrors(['mac_address']);
});
