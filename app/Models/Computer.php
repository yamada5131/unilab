<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $room_id
 * @property array|null $hardware_specifications
 * @property string $mac_address
 * @property string $operating_system
 * @property array|null $installed_software
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Room $room
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Software> $software
 * @property-read int|null $software_count
 * @property-read \App\Models\ComputerStatus|null $status
 * @method static \Database\Factories\ComputerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereHardwareSpecifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereInstalledSoftware($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereMacAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereOperatingSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Computer whereStatus($value)
 * @mixin \Eloquent
 */
class Computer extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['name', 'room_id', 'hardware_specifications'];

    protected function hardwareSpecifications(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value, true),
            set: fn (array $value) => json_encode($value),
        );
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
